<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Models\InvoicePayment;
use App\Models\Customer;
use Auth;

class NepalstePaymnetController extends Controller
{

    public function invoicePayWithnepalste(Request $request)
    {

        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','nepalste')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Nepalste.'));
        }

        $user = User::where('id', $invoice->created_by)->first();
        $get_amount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($invoice) {
                $payment_setting = Utility::getCompanyPaymentSetting($user->id);
                $api_key = $payment_setting['nepalste_public_key'];
                $settings = Utility::settingsById($user->id);
                $currency = isset($settings['site_currency']) ? $settings['site_currency'] : '';

                    $response = ['user' => $user, 'get_amount' => $get_amount, 'invoice' => $invoice];

                $parameters = [
                    'identifier' => 'DFU80XZIKS',
                    'currency' => $currency,
                    'amount' => $get_amount,
                    'details' => 'Invoice',
                    'ipn_url' => route('invoice.nepalste.status',$response),
                    'cancel_url' => route('invoice.nepalste.cancel'),
                    'success_url' => route('invoice.nepalste.status',$response),
                    'public_key' => $api_key,
                    'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme' => 'dark',
                    'customer_name' => 'John Doe',
                    'customer_email' => 'john@mail.com',
                ];

                 //live end point
                 if($payment_setting['nepalste_mode'] == 'live'){
                    $url = "https://nepalste.com.np/payment/initiate";
                }else{
                    $url = "https://nepalste.com.np/sandbox/payment/initiate";
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result, true);

                if(isset($result['success'])){
                    return redirect($result['url']);
                }else{
                    return redirect()->back()->with('error',__($result['message'] ?? 'Something went wrong.'));
                }
        }
    }

    public function invoiceGetNepalsteStatus(Request $request)
    {

        $invoice = Invoice::find($request->invoice);
        $user = User::where('id', $invoice->created_by)->first();
        $settings= Utility::settingsById($invoice->created_by);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $get_amount = $request->get_amount;
            $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','nepalste')->first();
            $invoice_payment                 = new InvoicePayment();
            $invoice_payment->invoice_id     = $invoice->id;
            $invoice_payment->date           = Date('Y-m-d');
            $invoice_payment->amount         = $get_amount;
            $invoice_payment->account_id     = $account->id;
            $invoice_payment->payment_method = 0;
            $invoice_payment->order_id       = $orderID;
            $invoice_payment->payment_type   = 'Nepalste';
            $invoice_payment->receipt        = '';
            $invoice_payment->reference      = '';
            $invoice_payment->description    = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
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

            Utility::addOnlinePaymentData($invoice_payment , $invoice , 'nepalste');
            
            //for customer balance update
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            //for bank balance update
            Utility::bankAccountBalance($account->id, $request->amount, 'credit');

            //For Notification
            $setting  = Utility::settingsById($invoice->created_by);
            $customer = Customer::find($invoice->customer_id);
            $notificationArr = [
                    'payment_price' => $request->amount,
                    'invoice_payment_type' => 'Aamarpay',
                    'customer_name' => $customer->name,
                ];
            //Slack Notification
            if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1)
            {
                Utility::send_slack_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
            }
            //Telegram Notification
            if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] == 1)
            {
                Utility::send_telegram_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
            }
            //Twilio Notification
            if(isset($settings['twilio_payment_notification']) && $settings['twilio_payment_notification'] ==1)
            {
                Utility::send_twilio_msg($customer->contact,'new_invoice_payment', $notificationArr,$invoice->created_by);
            }
            //webhook
            $module ='New Invoice Payment';
            $webhook=  Utility::webhookSetting($module,$invoice->created_by);
            if($webhook)
            {
                $parameter = json_encode($invoice_payment);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                if($status == true)
                {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Payment successfully, Webhook call failed.'));
                }
            }

            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));

    }

    public function invoiceGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error',__('Transaction has failed'));
    }
}
