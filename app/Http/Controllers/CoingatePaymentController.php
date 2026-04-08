<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\Utility;
// use CoinGate\CoinGate;
use App\Coingate\Coingate;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CoingatePaymentController extends Controller
{
    //


    public $mode;
    public $coingate_auth_token;
    public $is_enabled;
    protected $invoiceData;

    public function paymentConfig()
    {

            $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);


        $this->coingate_auth_token = isset($payment_setting['coingate_auth_token']) ? $payment_setting['coingate_auth_token'] : '';
        $this->mode                = isset($payment_setting['coingate_mode']) ? $payment_setting['coingate_mode'] : 'off';
        $this->is_enabled          = isset($payment_setting['is_coingate_enabled']) ? $payment_setting['is_coingate_enabled'] : 'off';

        return $this;
    }



    public function customerPayWithCoingate(Request $request)
    {

        $invoiceID = Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','coingate')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with CoinGate.'));
        }

        $this->invoiceData = $invoice;
        $user      = User::find($invoice->created_by);

        $payment   = $this->paymentConfig();

        $settings  = Utility::settingsById($invoice->created_by);


        if($invoice)
        {
            $price = $request->amount;
            if($price > 0)
            {
                CoinGate::config(
                    array(
                        'environment' => $this->mode,
                        'auth_token' => $this->coingate_auth_token,
                        'curlopt_ssl_verifypeer' => FALSE,
                    )
                );
                $post_params = array(
                    'order_id' => time(),
                    'price_amount' => $price,
                    'price_currency' => Utility::getValByName('site_currency'),
                    'receive_currency' => Utility::getValByName('site_currency'),
                    'callback_url' => route(
                        'customer.coingate', [
                                               Crypt::encrypt($invoice->id),
                                           $price,
                                       ]
                    ),
                    'cancel_url' => route('invoice.link.copy', [Crypt::encrypt($invoice->id)]),
                    'success_url' => route(
                        'customer.coingate', [
                                               Crypt::encrypt($invoice->id),
                                           $price,
                                       ]
                    ),
                    'title' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                );

                $order = CoinGate::coingatePayment($post_params, 'POST');

                if($order['status_code'] === 200)
                {
                    $response = $order['response'];
                    return redirect($response['payment_url']);
                }
                else
                {
                    return redirect()->back()->with('error', __('opps something wren wrong.'));
                }

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
            return redirect()->route('invoice.index')->with('error', __('Invoice is deleted.'));

        }


    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoiceID = Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);
        $this->invoiceData = $invoice;

        $orderID   = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings  = Utility::settingsById($invoice->created_by);
        $payment   = $this->paymentConfig();

        $result    = array();
        if($invoice)
        {
            $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','coingate')->first();
            $payments = InvoicePayment::create(
                [
                    'invoice_id' => $invoice->id,
                    'date' => date('Y-m-d'),
                    'amount' => $request->amount,
                    'account_id' => $account->id,
                    'payment_method' => 1,
                    'order_id' => $orderID,
                    'payment_type' => __('Coingate'),
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

            Utility::addOnlinePaymentData($payments , $invoice , 'coingate');
            
            //for customer balance update
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            //for bank balance update
            Utility::bankAccountBalance($account->id, $request->amount, 'credit');


            //For Notification
            $setting  = Utility::settingsById($invoice->created_by);
            $customer = Customer::find($invoice->customer_id);
            $notificationArr = [
                'payment_price' => $request->amount,
                'invoice_payment_type' => 'Coingate',
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
            return redirect()->back()->with('error', __('Invoice is deleted.'));
        }
    }


}
