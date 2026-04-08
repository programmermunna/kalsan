<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    protected $invoiceData;

    public function paymentConfig()
    {

        $payment_setting = Utility::getCompanyPaymentSetting(!empty($this->invoiceData)?$this->invoiceData->created_by:0);



        if($payment_setting['paypal_mode'] == 'live'){
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }else{
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }
    }

    public function customerPayWithPaypal(Request $request, $invoice_id)
    {

        $invoice                 = Invoice::find($invoice_id);
        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paypal')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Paypal.'));
        }

        $this->invoiceData       = $invoice;


        $this->paymentConfig();
        $settings                = Utility::settingsById('created_by', '=', $invoice->created_by);
        $get_amount = $request->amount;
        $request->validate(['amount' => 'required|numeric|min:0']);


        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));



        if($invoice)
        {
            if($get_amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else
            {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $name = Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                $settings=Utility::settingsById($invoice->created_by);

                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('customer.get.payment.status',[$invoice->id,$get_amount]),
                        "cancel_url" =>  route('customer.get.payment.status',[$invoice->id,$get_amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => isset($settings['site_currency'])?$settings['site_currency']:'USD',
                                "value" => $get_amount
                            ]
                        ]
                    ]
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('invoice.link.copy', \Crypt::encrypt($invoice->id))
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('invoice.link.copy', \Crypt::encrypt($invoice->id))
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }

                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerGetPaymentStatus(Request $request, $invoice_id,$amount)
    {

        $invoice                 = Invoice::find($invoice_id);
        $this->invoiceData       = $invoice;
        $settings=Utility::settingsById($invoice->created_by);

        $payment_id = Session::get('paypal_payment_id');

        Session::forget('paypal_payment_id');

        if(empty($request->PayerID || empty($request->token)))
        {
            return redirect()->back()->with('error', __('Payment failed'));
        }


        try
        {
            $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paypal')->first();
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            $payments = InvoicePayment::create(
                [

                    'invoice_id' => $invoice->id,
                    'date' => date('Y-m-d'),
                    'amount' => $amount,
                    'account_id' => $account->id,
                    'payment_method' => 0,
                    'order_id' => $order_id,
                    'currency' => Utility::getValByName('site_currency'),
                    'txn_id' => $payment_id,
                    'payment_type' => __('PAYPAL'),
                    'receipt' => '',
                    'reference' => '',
                    'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                ]
            );

            if($invoice->getDue() <= 0)
            {
                $invoice->status = 4;
                $invoice->save();
            }
            elseif(($invoice->getDue() - $payments->amount) == 0)
            {
                $invoice->status = 4;
                $invoice->save();
            }
            else
            {
                $invoice->status = 3;
                $invoice->save();
            }

            Utility::addOnlinePaymentData($payments , $invoice , 'paypal');
            
            $invoicePayment              = new \App\Models\Transaction();
            $invoicePayment->user_id     = $invoice->customer_id;
            $invoicePayment->user_type   = 'Customer';
            $invoicePayment->type        = 'PAYPAL';
            $invoicePayment->created_by  = $invoice->created_by;
            $invoicePayment->payment_id  = $invoicePayment->id;
            $invoicePayment->category    = 'Invoice';
            $invoicePayment->amount      = $amount;
            $invoicePayment->date        = date('Y-m-d');
            $invoicePayment->payment_id  = $payments->id;
            $invoicePayment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
            $invoicePayment->account     = 0;

            //for customer balance update
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            //for bank balance update
            Utility::bankAccountBalance($account->id, $request->amount, 'credit');

            //For Notification
            $setting  = Utility::settingsById($invoice->created_by);
            $customer = Customer::find($invoice->customer_id);
            $notificationArr = [
                'payment_price' => $amount,
                'invoice_payment_type' =>$invoicePayment->type,
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
            return redirect()->back()->with('success', __('Payment successfully added'));

        }
        catch(\Exception $e)
        {

            return redirect()->back()->with('error', __('Transaction has been failed.'));
        }

    }



}
