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

class CinetPayController extends Controller
{
    public function invoicePayWithCinetPay(Request $request)
    {
        $invoice_id = decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','cinetpay')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Cinetpay.'));
        }

        $getAmount = $request->amount;

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($user->type != 'company') {
            $user = User::where('id', $user->created_by)->first();
        }

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $cinetpay_api_key = !empty($payment_setting['cinetpay_api_key']) ? $payment_setting['cinetpay_api_key'] : '';
        $cinetpay_site_id = !empty($payment_setting['cinetpay_site_id']) ? $payment_setting['cinetpay_site_id'] : '';
        $currency_code = 'USD';
        $get_amount = round($request->amount);

        try {

            if (
                $currency_code != 'XOF' &&
                $currency_code != 'CDF' &&
                $currency_code != 'USD' &&
                $currency_code != 'KMF' &&
                $currency_code != 'GNF'
            ) {
                return redirect()->route('invoice.link.copy', encrypt($invoice_id))->with('error', __('Availabe currencies: XOF, CDF, USD, KMF, GNF'));
            }
            $call_back = route('invoice.cinetpay.return') . '?_token=' . csrf_token();
            $returnURL = route('invoice.cinetpay.notify') . '?_token=' . csrf_token();
            $cinetpay_data =  [
                "amount" => $get_amount,
                "currency" => $currency_code,
                "apikey" => $cinetpay_api_key,
                "site_id" => $cinetpay_site_id,
                "transaction_id" => $orderID,
                "description" => "Plan purchase",
                "return_url" => $call_back,
                "notify_url" => $returnURL,
                "metadata" => "user001",
                'customer_name' => isset($user->name) ? $user->name : 'Test',
                'customer_surname' => isset($user->name) ? $user->name : 'Test',
                'customer_email' => isset($user->email) ? $user->email : 'test@gmail.com',
                'customer_phone_number' => isset($user->mobile_number) ? $user->mobile_number : '1234567890',
                'customer_address' => isset($user->address) ? $user->address  : 'A-101, alok area, USA',
                'customer_city' => 'texas',
                'customer_country' => 'BF',
                'customer_state' => 'USA',
                'customer_zip_code' => isset($user->zipcode) ? $user->zipcode : '432876',
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    "content-type:application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);


            //On recupère la réponse de CinetPay
            $response_body = json_decode($response, true);

            if ($response_body['code'] == '201') {
                $cinetpaySession = [

                    'amount' => $get_amount,
                    'invoice_id' => $invoice_id,

                ];

                $request->session()->put('cinetpaySession', $cinetpaySession);

                $payment_link = $response_body["data"]["payment_url"]; // Retrieving the payment URL
                return redirect($payment_link);
            } else {
                return back()->with('error', ('Permission Denied'));
            }
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            return redirect()->route('invoice.link.copy', $invoice->id)->with('error', __($e->getMessage()));
        }
    }

    public function invoiceCinetPayReturn(Request $request)
    {
        try {

            $cinetpaySession = $request->session()->get('cinetpaySession');
            $request->session()->forget('cinetpaySession');

            $orderID   = strtoupper(str_replace('.', '', uniqid('', true)));
            $invoice = Invoice::find($request->invoice_id);
            $settings = Utility::settingsById($invoice->created_by);

            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->created_by)->first();
            }
            if ($user->type != 'company') {
                $user = User::where('id', $user->created_by)->first();
            }

            $response = json_decode($request->json, true);
            $get_amount = $request->get_amount;

            if ($invoice) {
                try {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','cinetpay')->first();
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice->id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $get_amount;
                    $invoice_payment->account_id     = $account->id;
                    $invoice_payment->payment_method = 0;
                    $invoice_payment->order_id       = $orderID;
                    $invoice_payment->payment_type   = 'CinetPay';
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

                    Utility::addOnlinePaymentData($invoice_payment , $invoice , 'cinetpay');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'CinetPay',
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
                    return redirect()->route('invoice.link.copy')->with('error', __($e->getMessage()));
                }
            } else {
                if (Auth::check()) {
                    return redirect()->route('invoice.link.copy', $request->invoice_id)->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('invoice.link.copy', encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('invoice.link.copy')->with('error', __($e->getMessage()));
        }
    }

    public function invoiceCinetPayNotify(Request $request)
    {
        /* 1- Recovery of parameters posted on the URL by CinetPay
         * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#les-etapes-pour-configurer-lurl-de-notification
         * */
        if (isset($request->cpm_trans_id)) {
            // Using your transaction identifier, check that the order has not yet been processed
            $VerifyStatusCmd = "1"; // status value to retrieve from your database
            if ($VerifyStatusCmd == '00') {
                //The order has already been processed
                // Scarred you script
                die();
            }
            $payment_setting = Utility::getAdminPaymentSetting();

            /* 2- Otherwise, we check the status of the transaction in the event of a payment attempt on CinetPay
            * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#2-verifier-letat-de-la-transaction */
            $cinetpay_check = [
                "apikey" => $payment_setting['cinetpay_api_key'],
                "site_id" => $payment_setting['cinetpay_site_id'],
                "transaction_id" => $request->cpm_trans_id
            ];

            $response = $this->getPayStatus($cinetpay_check); // call query function to retrieve status

            //We get the response from CinetPay
            $response_body = json_decode($response, true);
            if ($response_body['code'] == '00') {
                /* correct, on délivre le service
                 * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#3-delivrer-un-service*/
                echo 'Congratulations, your payment has been successfully completed';
            } else {
                // transaction a échoué
                echo 'Failure, code:' . $response_body['code'] . ' Description' . $response_body['description'] . ' Message: ' . $response_body['message'];
            }
            // Update the transaction in your database
            /*  $order->update(); */
        } else {
            print("cpm_trans_id non found");
        }
    }
}
