<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;

class AuthorizeNetController extends Controller
{

    public function invoicePayWithAuthorizeNet(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','authorizenet')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with AuthorizeNet.'));
        }

        $user = User::find($invoice->created_by);

        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $currency = isset($company_payment_setting['site_currency']) ? $company_payment_setting['site_currency'] : 'USD';
        $settings = Utility::settingsById($invoice->created_by);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $get_amount = $request->amount;
        $payment_id = $invoice->id;

        try {
            if ($invoice) {
                $data = [
                    'invoiceID'     =>  $invoice_id,
                    'user_id'       =>  $user->id,
                    'get_amount'    =>  $get_amount,
                    'authuser'      =>  $user,
                ];

                $data  =    json_encode($data);

                try {
                    return view('invoice.request', compact('invoice', 'get_amount','user','data'));
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $input          = $request->all();
        $data           = json_decode($input['data'] , true);

        $invoice_id     =   $data['invoiceID'];
        $amount         =   $data['get_amount'];
        $invoice = Invoice::find($invoice_id);
        $user = User::find($invoice->created_by);

        $settings= Utility::settingsById($invoice->created_by);
        if ($invoice)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try
            {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','authorizenet')->first();
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->account_id         = $account->id;
                    $invoice_payment->payment_method         = 0;
                    $invoice_payment->order_id      =$orderID;
                    $invoice_payment->payment_type   = 'AuthorizeNet';
                    $invoice_payment->receipt     = '';
                    $invoice_payment->reference     = '';
                    $invoice_payment->description     = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
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

                    Utility::addOnlinePaymentData($invoice_payment , $invoice , 'authorizenet');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
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
            catch (\Exception $e)
            {
                return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('success',$e->getMessage());
            }
        } else {
            return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('success', __('Invoice not found.'));
        }

    }
}
