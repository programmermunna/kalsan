<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
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
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;

class SkrillPaymentController extends Controller
{
    public    $email;
    public    $is_enabled;
    protected $invoiceData;

    public function paymentConfig()
    {


            $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);



        $this->email      = isset($payment_setting['skrill_email']) ? $payment_setting['skrill_email'] : '';
        $this->is_enabled = isset($payment_setting['is_skrill_enabled']) ? $payment_setting['is_skrill_enabled'] : 'off';

        return $this;
    }





    public function customerPayWithSkrill(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','skrill')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Skrill.'));
        }

        $user      = User::find($invoice->created_by);
        $this->invoiceData = $invoice;
        $payment = $this->paymentConfig();

        if($invoice)
        {
            $price = $request->amount;
            if($price > 0)
            {

                $tran_id             = md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id');
                $skill               = new SkrillRequest();
                $skill->pay_to_email = $this->email;
                $skill->return_url   = route(
                    'customer.skrill', [
                                         $request->invoice_id,
                                         $price,
                                         'tansaction_id=' . MD5($tran_id),
                                     ]
                );
                $skill->cancel_url   = route(
                    'customer.skrill', [
                                         $request->invoice_id,
                                         $price,
                                     ]
                );

                // create object instance of SkrillRequest
                $skill->transaction_id  = MD5($tran_id); // generate transaction id
                $skill->amount          = $price;
                $skill->currency        = Utility::getValByName('site_currency');
                $skill->language        = 'EN';
                $skill->prepare_only    = '1';
                $skill->merchant_fields = 'site_name, customer_email';
                $skill->site_name       = $user->name;
                $skill->customer_email  = $user->email;

                // create object instance of SkrillClient
                $client = new SkrillClient($skill);
                $sid    = $client->generateSID(); //return SESSION ID

                // handle error
                $jsonSID = json_decode($sid);
                if($jsonSID != null && $jsonSID->code == "BAD_REQUEST")
                {
                    return redirect()->back()->with('error', $jsonSID->message);
                }


                // do the payment
                $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
                if($tran_id)
                {
                    $data = [
                        'amount' => $price,
                        'trans_id' => MD5($request['transaction_id']),
                        'currency' => Utility::getValByName('site_currency'),
                    ];
                    session()->put('skrill_data', $data);
                }


                return redirect($redirectUrl);

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

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);
        $this->invoiceData = $invoice;

        $payment  = $this->paymentConfig();
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings = Utility::settingsById($invoice->created_by);


        $result    = array();

        if($invoice)
        {
            try
            {

                if(session()->has('skrill_data') && isset($request->tansaction_id) && !empty($request->tansaction_id))
                {
                    $get_data = session()->get('skrill_data');
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','skrill')->first();
                    $payments = InvoicePayment::create(
                        [
                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'account_id' => $account->id,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Skrill'),
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

                    Utility::addOnlinePaymentData($payments , $invoice , 'skrill');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Skrill',
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
                        $parameter = json_encode($invoice);
                        $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                        if($status == true)
                        {
                            return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));
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
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed! '));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice not found!'));
            }
        }
    }
}
