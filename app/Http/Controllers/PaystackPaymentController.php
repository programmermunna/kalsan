<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PaystackPaymentController extends Controller
{
    //
    public    $secret_key;
    public    $public_key;
    public    $is_enabled;
    protected $invoiceData;


    public function paymentConfig()
    {

            $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);



        $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
        $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
    }



    public function customerPayWithPaystack(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paystack')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Paystack.'));
        }

        $user      = User::find($invoice->created_by);
        $settings=Utility::settingsById($invoice->created_by);


        if($invoice)
        {
            $price = $request->amount;
            if($price > 0)
            {
                $res_data['email']       = $user->email;
                $res_data['total_price'] = $price;
                $res_data['currency']    =isset($settings['site_currency'])?$settings['site_currency']:'USD';
                $res_data['flag']        = 1;

                return $res_data;

            }
            else
            {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }

        }
        else
        {
            return redirect()->back()->with('error', __('Invoice is deleted.'));

        }

    }

    public function getInvoicePaymentStatus(Request $request, $pay_id, $invoice_id)
    {


        $invoiceID         = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice           = Invoice::find($invoiceID);
        $this->invoiceData = $invoice;
        $payment           = $this->paymentConfig();
        $orderID           = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings          = Utility::settingsById($invoice->created_by);


        $result = array();

        if($invoice)
        {
            try
            {

                //The parameter after verify/ is the transaction reference to be verified
                $url = "https://api.paystack.co/transaction/verify/$pay_id";
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, [
                           'Authorization: Bearer ' . $this->secret_key,
                       ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if($responce)
                {
                    $result = json_decode($responce, true);
                }

                if(isset($result['status']) && $result['status'] == true)
                {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paystack')->first();
                    $payments = InvoicePayment::create(
                        [
                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'account_id' => $account->id,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Paystack'),
                            'receipt' => '',
                            'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                        ]
                    );

                    $invoice = Invoice::find($invoice->id);


                    if($invoice->getDue() <= 0)
                    {
                        Invoice::change_status($invoice->id, 4);
                    }
                    else
                    {
                        Invoice::change_status($invoice->id, 3);
                    }

                    Utility::addOnlinePaymentData($payments , $invoice , 'paystack');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Paystack',
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
                        $parameter = json_encode($invoicePayment);
                        $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                        if($status == true)
                        {
                            return redirect()->back()->with('success', __('Payment successfully added!'));
                        }
                        else
                        {
                            return redirect()->back()->with('error', __('Payment successfully, Webhook call failed.'));
                        }
                    }



                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));

                }
                else
                {

                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction Unsuccesfull'));
                }
            }
            catch(\Exception $e)
            {


                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice is deleted.'));
        }
    }
}
