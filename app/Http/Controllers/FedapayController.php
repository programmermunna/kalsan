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

class FedapayController extends Controller
{

    public function invoicePayWithFedapay(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','fedapay')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Fedapay.'));
        }

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($user->type != 'company') {
            $user = User::where('id', $user->created_by)->first();
        }

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $fedapay_secret = $payment_setting['fedapay_secret'];
        $fedapay_mode = $payment_setting['fedapay_mode'];
        $currency_code = !empty($payment_setting['currency_code']) ? $payment_setting['currency_code'] : 'XOF';
        $price = $request->amount;

        $get_amount = round($price);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {

                    \FedaPay\FedaPay::setApiKey($fedapay_secret);
                    \FedaPay\FedaPay::setEnvironment($fedapay_mode);

                    $transaction = \FedaPay\Transaction::create([
                        "description" => "Fedapay Payment",
                        "amount" => $get_amount,
                        "currency" => ["iso" => $currency_code],
                        "callback_url" => route(
                            'invoice.fedapay.status',
                            [
                                'invoice_id' => $invoice_id,
                                'amount' => $get_amount,
                            ]
                        ),
                        "cancel_url" =>  route(
                            'invoice.fedapay.status',
                            [
                                'invoice_id' => $invoice_id,
                                'amount' => $get_amount,
                            ]
                        )
                    ]);
                    $token = $transaction->generateToken();

                    return redirect($token->url);
                } catch (\Exception $e) {
                    return redirect()->route('invoice.show', $invoice_id)->with('error', $e->getMessage() ?? 'Something went wrong.');
                }
                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function getInvociePaymentStatus(Request $request)
    {
        $orderID   = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($request->invoice_id);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($user->type != 'owner') {
            $user = User::where('id', $user->created_by)->first();
        }
        $response = json_decode($request->json, true);
        $get_amount = $request->amount;
        $settings= Utility::settingsById($invoice->created_by);

        if ($invoice) {
            try {
                if ($request->status == 'approved') {

                    try {
                        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','fedapay')->first();
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->invoice_id     = $invoice->id;
                        $invoice_payment->date           = Date('Y-m-d');
                        $invoice_payment->amount         = $get_amount;
                        $invoice_payment->account_id     = $account->id;
                        $invoice_payment->payment_method = 0;
                        $invoice_payment->order_id       = $orderID;
                        $invoice_payment->payment_type   = 'FedaPay';
                        $invoice_payment->receipt        = '';
                        $invoice_payment->reference      = '';
                        $invoice_payment->description    = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                        $invoice_payment->save();

                        if ($invoice->getDue() <= 0) {
                            $invoice->status = 4;
                            $invoice->save();
                        } elseif (($invoice->getDue() - $invoice_payment->amount) == 0) {
                            $invoice->status = 4;
                            $invoice->save();
                        } else {
                            $invoice->status = 3;
                            $invoice->save();
                        }

                        Utility::addOnlinePaymentData($invoice_payment , $invoice , 'fedapay');
                        
                        //for customer balance update
                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                        //for bank balance update
                        Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                        //For Notification
                        $setting  = Utility::settingsById($invoice->created_by);
                        $customer = Customer::find($invoice->customer_id);
                        $notificationArr = [
                            'payment_price' => $request->amount,
                            'invoice_payment_type' => 'FedaPay',
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
                        $webhook =  Utility::webhookSetting($module, $invoice->created_by);
                        if ($webhook) {
                            $parameter = json_encode($invoice_payment);
                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                            if ($status == true) {
                                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                            } else {
                                return redirect()->back()->with('error', __('Payment successfully, Webhook call failed.'));
                            }
                        }

                        return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                    } catch (\Exception $e) {
                        return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __($e->getMessage()));
                    }
                } else {
                    return redirect()->back()->with('error', ('Permission denied'));
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('invoice.link.copy', $request->invoice_id)->with('error', $e->getMessage());
                } else {
                    return redirect()->route('invoice.link.copy', encrypt($request->invoice_id))->with('success', $e->getMessage());
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('invoice.link.copy', $request->invoice_id)->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('invoice.link.copy', encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
            }
        }
    }
}
