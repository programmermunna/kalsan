<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class CashfreeController extends Controller
{

    public function invoicepaywithcashfree(Request $request)
    {

        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','cashfree')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Cashfree.'));
        }

        try {
            $user      = User::find($invoice->created_by);
            $settings= Utility::settingsById($invoice->created_by);
            $companyPaymentSettings = Utility::getCompanyPaymentSetting($user->id);

            config(
                [
                    'services.cashfree.key' => isset($companyPaymentSettings['cashfree_api_key']) ? $companyPaymentSettings['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($companyPaymentSettings['cashfree_secret_key']) ? $companyPaymentSettings['cashfree_secret_key'] : '',
                ]
            );
            $url = config('services.cashfree.url');
            if (\Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->created_by)->first();
            }
            $get_amount = $request->amount;
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($invoice && $get_amount != 0) {
                if ($get_amount > $invoice->getDue())
                {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }
                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: 2022-01-01",
                    "x-client-id: " . config('services.cashfree.key'),
                    "x-client-secret: " . config('services.cashfree.secret')
                );


                $data = json_encode([
                    'order_id' => $orderID,
                    'order_amount' => $get_amount,
                    "order_currency" => 'INR',
                    "order_name" => Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                    "customer_details" => [
                        "customer_id" => 'customer_' . $user->id,
                        "customer_name" => $user->name,
                        "customer_email" => $user->email,
                        "customer_phone" => '1234567890',
                    ],
                    "order_meta" => [
                        "return_url" => route('invoice.cashfreePayment.success') . '?order_id={order_id}&invoice_id=' . $invoice->invoice_id . '&amount=' . $get_amount
                    ]
                ]);




                try {

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    $resp = curl_exec($curl);


                    curl_close($curl);
                    return redirect()->to(json_decode($resp)->payment_link);
                } catch (\Throwable $th) {

                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {



        $invoice    = Invoice::find($request->invoice_id);
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $user      = User::find($invoice->created_by);
        $settings= Utility::settingsById($invoice->created_by);
        $companyPaymentSettings = Utility::getCompanyPaymentSetting($user->id);

        config(
            [
                'services.cashfree.key' => isset($companyPaymentSettings['cashfree_api_key']) ? $companyPaymentSettings['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($companyPaymentSettings['cashfree_secret_key']) ? $companyPaymentSettings['cashfree_secret_key'] : '',
            ]
        );

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
            'headers' => [
                'accept' => 'application/json',
                'x-api-version' => '2022-09-01',
                "x-client-id" => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret')
            ],
        ]);
        $respons = json_decode($response->getBody());
        if ($respons->order_id && $respons->cf_payment_id != NULL) {

            $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    'x-client-id' => config('services.cashfree.key'),
                    'x-client-secret' => config('services.cashfree.secret'),
                ],
            ]);
            $info = json_decode($response->getBody());
            try {

                if ($info->payment_status == "SUCCESS") {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','cashfree')->first();
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $request->invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $request->has('amount') ? $request->amount : 0;
                    $invoice_payment->account_id         = $account->id;
                    $invoice_payment->payment_method         = 0;
                    $invoice_payment->order_id      =$orderID;
                    $invoice_payment->payment_type   = 'Cashfree';
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

                    Utility::addOnlinePaymentData($invoice_payment , $invoice , 'cashfree');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');
                    
                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Cashfree',
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

                    $request->session()->forget('invoice_data');

                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));

                } else {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction fail'));

                }
            } catch (\Exception $e) {

                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', $e->getMessage());

            }
        } else {
            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', 'Payment Failed.');
        }
    }

}
