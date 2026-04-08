<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementProController extends Controller
{
    public function invoicePayWithPaiementPro(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paiementpro')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Paiement Pro.'));
        }

        $getAmount = $request->amount;
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $merchant_id = isset($company_payment_setting['paiementpro_merchant_id']) ? $company_payment_setting['paiementpro_merchant_id'] : '';
        $currency_code = !empty($company_payment_setting['currency_code']) ? $company_payment_setting['currency_code'] : 'USD';
        $get_amount = round($request->amount);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($invoice) {
                $merchant_id = isset($company_payment_setting['paiementpro_merchant_id']) ? $company_payment_setting['paiementpro_merchant_id'] : '';
                $data = array(
                    'merchantId' => $merchant_id,
                    'amount' =>  $getAmount,
                    'description' => "Api PHP",
                    'channel' => $request->channel,
                    'countryCurrencyCode' => 'USD',
                    'referenceNumber' => "REF-" . time(),
                    'customerEmail' => $user->email,
                    'customerFirstName' => $user->name,
                    'customerLastname' =>  $user->name,
                    'customerPhoneNumber' => $request->mobile_number,
                    'notificationURL' => route('invoice.paiementpro.status',$invoice_id),
                    'returnURL' => route('invoice.paiementpro.status',$invoice_id),
                    'returnContext' => json_encode([
                        'coupon_code' => $request->coupon_code,
                    ]),
                );
                $data = json_encode($data);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);

                curl_close($ch);
                $response = json_decode($response);
                if (isset($response->success) && $response->success == true) {
                    // redirect to approve href
                    return redirect($response->url);

                }

            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }
    public function getInvociePaymentStatus(Request $request, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $settings = Utility::settingsById($invoice->created_by);
        $response = json_decode($request->json, true);

        if ($invoice) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try
            {
                $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paiementpro')->first();
                $invoice_payment = new InvoicePayment();
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->date = Date('Y-m-d');
                $invoice_payment->amount = $request->amount;
                $invoice_payment->account_id = $account->id;
                $invoice_payment->payment_method = 0;
                $invoice_payment->order_id = $orderID;
                $invoice_payment->payment_type = 'Payment Pro';
                $invoice_payment->receipt = '';
                $invoice_payment->reference = '';
                $invoice_payment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                $invoice_payment->save();


                if($invoice->getDue() <= 0)
                {
                    $invoice->status = 4;
                    $invoice->save();
                }
                elseif(($invoice->getDue() - $invoice_payment->amount) == 0)
                {
                    $invoice->status = 4;
                    $invoice->save();
                }
                else
                {
                    $invoice->status = 3;
                    $invoice->save();
                }

                Utility::addOnlinePaymentData($invoice_payment , $invoice , 'paiementpro');
                
                //for customer balance update
                Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                //for bank balance update
                Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                //For Notification
                $setting = Utility::settingsById($invoice->created_by);
                $customer = Customer::find($invoice->customer_id);
                $notificationArr = [
                    'payment_price' => $request->amount,
                    'invoice_payment_type' => 'Aamarpay',
                    'customer_name' => $customer->name,
                ];
                //Slack Notification
                if (isset($settings['payment_notification']) && $settings['payment_notification'] == 1) {
                    Utility::send_slack_msg('new_invoice_payment', $notificationArr, $invoice->created_by);
                }
                //Telegram Notification
                if (isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] == 1) {
                    Utility::send_telegram_msg('new_invoice_payment', $notificationArr, $invoice->created_by);
                }
                //Twilio Notification
                if (isset($settings['twilio_payment_notification']) && $settings['twilio_payment_notification'] == 1) {
                    Utility::send_twilio_msg($customer->contact, 'new_invoice_payment', $notificationArr, $invoice->created_by);
                }
                //webhook
                $module = 'New Invoice Payment';
                $webhook = Utility::webhookSetting($module, $invoice->created_by);
                if ($webhook) {
                    $parameter = json_encode($invoice_payment);
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                    if ($status == true) {
                        return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                    } else {
                        return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Payment successfully, Webhook call failed.'));
                    }
                }

                return redirect()->route('invoice.link.copy', \Crypt::encrypt($request->invoice_id))->with('success', __('Invoice paid Successfully!'));

            } catch (\Exception $e) {
                return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', $e->getMessage());
            }
        } else {
            return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
        }

    }
}
