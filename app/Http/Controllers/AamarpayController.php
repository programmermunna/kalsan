<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class AamarpayController extends Controller
{


    function redirect_to_merchant($url)
    {

        $token = csrf_token();
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <script type="text/javascript">
                function closethisasap() { document.forms["redirectpost"].submit(); }
            </script>
        </head>

        <body onLoad="closethisasap();">

        <form name="redirectpost" method="post" action="<?php echo 'https://sandbox.aamarpay.com/' . $url; ?>">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
        </body>

        </html>
        <?php
        exit;
    }


    public function invoicepaywithaamarpay(Request $request)
    {

        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','aamarpay')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Aamarpay.'));
        }

        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $settings= Utility::settingsById($invoice->created_by);

        if ($company_payment_setting['aamarpay_mode'] == "sandbox") {
            $url = "https://sandbox.aamarpay.com/request.php";
        } else {
            $url =  "https://secure.aamarpay.com/request.php";
        }


        try {

            $get_amount = $request->amount;

            if ($invoice && $get_amount != 0) {

                if ($get_amount > $invoice->getDue())
                {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }
            }
            try {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $fields = array(
                    'store_id' => $company_payment_setting['aamarpay_store_id'],
                    //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                    'amount' => $get_amount,
                    //transaction amount
                    'payment_type' => '',
                    //no need to change
                    'currency' => $settings['site_currency'],
                    //currenct will be USD/BDT
                    'tran_id' => $orderID,
                    //transaction id must be unique from your end
                    'cus_name' => $user['name'],
                    //customer name
                    'cus_email' => $user['email'],
                    //customer email address
                    'cus_add1' => '',
                    //customer address
                    'cus_add2' => '',
                    //customer address
                    'cus_city' => '',
                    //customer city
                    'cus_state' => '',
                    //state
                    'cus_postcode' => '',
                    //postcode or zipcode
                    'cus_country' => '',
                    //country
                    'cus_phone' => '1234567890',
                    //customer phone number
                    'success_url' => route('invoice.pay.aamarpay.success',Crypt::encrypt(['response'=>'success','invoice_id' => $invoice_id, 'amount' => $get_amount, 'order_id' => $orderID]) ),
                    //your success route
                    'fail_url' => route('invoice.pay.aamarpay.success',Crypt::encrypt(['response'=>'success','invoice_id' => $invoice_id, 'amount' => $get_amount, 'order_id' => $orderID]) ),
                    //your fail route
                    'cancel_url' => route('invoice.pay.aamarpay.success', Crypt::encrypt(['response'=>'success','invoice_id' => $invoice_id, 'amount' => $get_amount, 'order_id' => $orderID]) ),
                    //your cancel url
                    'signature_key' => $company_payment_setting['aamarpay_signature_key'],
                    'desc' => $company_payment_setting['aamarpay_description'],
                ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key


                $fields_string = http_build_query($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                curl_close($ch);
                // $this->redirect_to_merchant($url_forward);
                if ($company_payment_setting['aamarpay_mode'] == "sandbox") {
                    header('Location: https://sandbox.aamarpay.com/' . $url_forward);
                } else {
                    header('Location: https://secure.aamarpay.com/' . $url_forward);
                }
                exit;
            } catch (\Exception $e) {



                return redirect()->back()->with('error', $e);
            }

        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus($data)
    {
        $data = Crypt::decrypt($data);
        $getAmount = $data['amount'];
        $invoice    = Invoice::find($data['invoice_id']);

        $user      = User::find($invoice->created_by);
        $settings= Utility::settingsById($invoice->created_by);

        try {

            $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($data['response'] == "success")
            {
                $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','aamarpay')->first();
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->invoice_id     = $invoice->id;
                $invoice_payment->date           = Date('Y-m-d');
                $invoice_payment->amount         = !empty($getAmount) ?$getAmount:0;
                $invoice_payment->account_id         = $account->id;
                $invoice_payment->payment_method         = 0;
                $invoice_payment->order_id      =$orderID;
                $invoice_payment->payment_type   = 'Aamarpay';
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

                Utility::addOnlinePaymentData($invoice_payment , $invoice , 'aamarpay');
                
                //for customer balance update
                Utility::updateUserBalance('customer', $invoice->customer_id, $getAmount, 'debit');
                //for bank balance update
                Utility::bankAccountBalance($account->id, $getAmount, 'credit');

                //For Notification
                $setting  = Utility::settingsById($invoice->created_by);
                $customer = Customer::find($invoice->customer_id);
                $notificationArr = [
                    'payment_price' => $getAmount,
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
            elseif ($data['response'] == "cancel")
            {

                return redirect()->back()->with('error', __('Your payment is cancel'));
            }
            else
            {


                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction fail'));
            }
        }
        catch (\Throwable $th)
        {

            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', $th->getMessage());

        }
    }

}
