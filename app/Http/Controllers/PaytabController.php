<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\DB;
// use Paytabscom\Laravel_paytabs\Facades\paypage;
use App\PayTab\paypage;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Exception;

class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled;

    public function invoicePayWithpaytab(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paytab')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with PayTab.'));
        }

        $user      = User::find($invoice->created_by);
        $settings= Utility::settingsById($invoice->created_by);
        $companyPaymentSettings = Utility::getCompanyPaymentSetting($user->id);

        config([
            'paytabs.profile_id' => isset($companyPaymentSettings['paytab_profile_id']) ? $companyPaymentSettings['paytab_profile_id'] : '',
            'paytabs.server_key' => isset($companyPaymentSettings['paytab_server_key']) ? $companyPaymentSettings['paytab_server_key'] : '',
            'paytabs.region' => isset($companyPaymentSettings['paytab_region']) ? $companyPaymentSettings['paytab_region'] : '',
            'paytabs.currency' => 'INR',
        ]);


        if (\Auth::check()) {
            $user = Auth::user();
        } else
        {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $get_amount = $request->amount;

        if ($invoice && $get_amount != 0)
        {
            if ($get_amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else{
                $paypage = new paypage();
                $pay = $paypage->sendPaymentCode('all')
                    ->sendTransaction('sale')
                    ->sendCart(1, $get_amount, 'invoice payment')
                    ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                    ->sendURLs(
                        route('invoice.paytab.success', ['success' => 1,'data' => $request->all(), $invoice->id, 'amount' => $get_amount]),
                        route('invoice.paytab.success', ['success' => 0,'data' => $request->all(), $invoice->id, 'amount' => $get_amount])
                    )
                    ->sendLanguage('en')
                    ->sendFramed($on = false)
                    ->create_pay_page();


                return $pay;

            }
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id)
    {
        if (!empty($invoice_id))
        {
            $invoice    = Invoice::find($invoice_id);
            $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
            $settings  = Utility::settingsById($invoice->created_by);
            if ($invoice)
            {
                try
                {

                    if($request->success == "1")
                    {
                        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paytab')->first();
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->date           = Date('Y-m-d');
                        $invoice_payment->amount         = $request->has('amount') ? $request->amount : 0;
                        $invoice_payment->account_id         = $account->id;
                        $invoice_payment->payment_method         = 0;
                        $invoice_payment->order_id      =$orderID;
                        $invoice_payment->payment_type   = 'Paytab';
                        $invoice_payment->receipt     = '';
                        $invoice_payment->reference     = '';
                        $invoice_payment->description     = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                        $invoice_payment->save();
                        if(($invoice->getDue() - $invoice_payment->amount) == 0)
                        {
                            Invoice::change_status($invoice->id, 3);
                        } else
                        {
                            Invoice::change_status($invoice->id, 2);
                        }
                        $invoice->save();

                        Utility::addOnlinePaymentData($invoice_payment , $invoice , 'paytab');
                        
                        //for customer balance update
                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                        //for bank balance update
                        Utility::bankAccountBalance($account->id, $request->amount, 'credit');


                        //For Notification
                        $setting  = Utility::settingsById($invoice->created_by);

                        $customer = Customer::find($invoice->customer_id);
                        $notificationArr = [
                            'payment_price' => $request->amount,
                            'invoice_payment_type' => 'Paytab',
                            'customer_name' => $customer->name,
                        ];
                        //Slack Notification
                        if(isset($setting['payment_notification']) && $setting['payment_notification'] ==1)
                        {
                            Utility::send_slack_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //Telegram Notification
                        if(isset($setting['telegram_payment_notification']) && $setting['telegram_payment_notification'] == 1)
                        {
                            Utility::send_telegram_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //Twilio Notification
                        if(isset($setting['twilio_payment_notification']) && $setting['twilio_payment_notification'] ==1)
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
                        if (Auth::user())
                        {

                            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                        } else {


                            $id = \Crypt::encrypt($invoice_id);
                            return redirect()->route('invoice.link.copy', $id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                        }
                    }else
                    {
                        if (Auth::user())
                        {
                            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction fail!'));
                        } else {
                            $id = \Crypt::encrypt($invoice_id);
                            return redirect()->route('invoice.link.copy', $id)->with('error', __('Transaction fail!'));
                        }
                    }
                } catch (\Exception $e)
                {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __($e->getMessage()));
                }
            }
            else
            {
                if (Auth::user())
                {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Invoice not found'));
                }
                else{
                    $id = \Crypt::encrypt($invoice_id);
                    return redirect()->route('invoice.link.copy',$id)->with('error', __('Transaction fail!'));
                }
            }
        } else
        {
            return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
        }
    }




}
