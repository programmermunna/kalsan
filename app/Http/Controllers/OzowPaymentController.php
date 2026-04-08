<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class OzowPaymentController extends Controller
{
    function generate_request_hash_check($inputString)
    {
        $stringToHash = strtolower($inputString);
        // echo "Before Hashcheck: " . $stringToHash . "\n";
        return $this->get_sha512_hash($stringToHash);
    }

    function get_sha512_hash($stringToHash)
    {
        return hash('sha512', $stringToHash);
    }


    public function invoicePayWithOzow(Request $request)
    {
        $invoice_id = Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','ozow')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Ozow.'));
        }

        $user = User::find($invoice->created_by);

        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $amount = $request->amount;

        try {
            if ($invoice) {
                if ($amount > $invoice->getDue()) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                } else {

                    $siteCode       = isset($company_payment_setting['company_ozow_site_key']) ? $company_payment_setting['company_ozow_site_key'] : '';
                    $privateKey     = isset($company_payment_setting['company_ozow_private_key']) ? $company_payment_setting['company_ozow_private_key'] : '';
                    $apiKey         = isset($company_payment_setting['company_ozow_api_key']) ? $company_payment_setting['company_ozow_api_key'] : '';
                    $isTest         = isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == 'sandbox'  ? 'true' : 'false';

                    $countryCode    = "ZA";
                    $currencyCode   = $company_payment_setting['currency'] ?? 'ZAR';

                    $bankReference  = time().'FKU';
                    $transactionReference = time();

                    $cancelUrl      = route('invoice.ozow.status', [$invoice_id]);
                    $errorUrl       = route('invoice.ozow.status', [$invoice_id]);
                    $successUrl     = route('invoice.ozow.status', [$invoice_id]);
                    $notifyUrl      = route('invoice.ozow.status', [$invoice_id]);

                    // Calculate the hash with the exact same data being sent
                    $inputString    = $siteCode . $countryCode . $currencyCode . $amount . $transactionReference . $bankReference . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey;
                    $hashCheck      = $this->generate_request_hash_check($inputString);

                    $data = [
                        "countryCode"           => $countryCode,
                        "amount"                => $amount,
                        "transactionReference"  => $transactionReference,
                        "bankReference"         => $bankReference,
                        "cancelUrl"             => $cancelUrl,
                        "currencyCode"          => $currencyCode,
                        "errorUrl"              => $errorUrl,
                        "isTest"                => $isTest, // boolean value here is okay
                        "notifyUrl"             => $notifyUrl,
                        "siteCode"              => $siteCode,
                        "successUrl"            => $successUrl,
                        "hashCheck"             => $hashCheck,
                    ];

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL             => 'https://api.ozow.com/postpaymentrequest',
                        CURLOPT_RETURNTRANSFER  => true,
                        CURLOPT_ENCODING        => '',
                        CURLOPT_MAXREDIRS       => 10,
                        CURLOPT_TIMEOUT         => 0,
                        CURLOPT_FOLLOWLOCATION  => true,
                        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST   => 'POST',
                        CURLOPT_POSTFIELDS      => json_encode($data),
                        CURLOPT_HTTPHEADER      => array(
                                'Accept: application/json',
                                'ApiKey: '.$apiKey,
                                'Content-Type: application/json'
                            ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $json_attendance = json_decode($response, true);

                    if (isset($json_attendance['url']) && $json_attendance['url'] != null) {
                        return redirect()->away($json_attendance['url']);
                    } else {
                        return redirect()->back()->with('error',__('Something went wrong.'));
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $user = User::find($invoice->created_by);

        $settings= Utility::settingsById($invoice->created_by);
        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        if ($invoice)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try
            {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','ozow')->first();
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $request->Amount;
                    $invoice_payment->account_id         = $account->id;
                    $invoice_payment->payment_method         = 0;
                    $invoice_payment->order_id      =$orderID;
                    $invoice_payment->payment_type   = 'Ozow';
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

                    Utility::addOnlinePaymentData($invoice_payment , $invoice , 'ozow');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                            'payment_price' => $invoice_payment->amount,
                            'invoice_payment_type' => 'Aamarpay',
                            'customer_name' => $customer->name ?? '-',
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
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
            }
            catch (\Exception $e)
            {
                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice_id))->with('error',$e->getMessage());
            }
        } else {
            return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
        }

    }
}
