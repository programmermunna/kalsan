<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Xendit\Xendit;
use Illuminate\Support\Str;
use App\Models\Customer;


class XenditPaymentController extends Controller
{

    public function invoicePayWithXendit(Request $request)
    {
        $data = request()->all();

        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }

        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($fixedData['invoice_id']);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','xendit')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Xendit.'));
        }

        $user = User::where('id', $invoice->created_by)->first();
        $get_amount = $fixedData['amount'];
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($invoice) {
                $payment_setting = Utility::getCompanyPaymentSetting($user->id);
                $xendit_token = $payment_setting['xendit_token'];
                $xendit_api = $payment_setting['xendit_api'];
                $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'USD';
                $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'invoice' => $invoice, 'currency' => $currency];
                Xendit::setApiKey($xendit_api);
                $params = [
                    'external_id' => $orderID,
                    'payer_email' => $user->email,
                    'description' => 'Payment for order ' . $orderID,
                    'amount' => $get_amount,
                    'callback_url' =>  route('invoice.xendit.status'),
                    'success_redirect_url' => route('invoice.xendit.status', $response),
                ];

                $Xenditinvoice = \App\Xendit\Invoice::create($params);
                Session::put('invoicepay', $Xenditinvoice);
                return redirect($Xenditinvoice['invoice_url']);
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvociePaymentStatus(Request $request)
    {

        $data = request()->all();
        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }
        $session = Session::get('invoicepay');
        $invoice = Invoice::find($fixedData['invoice']);
        $user = User::where('id', $invoice->created_by)->first();
        $settings= Utility::settingsById($invoice->created_by);

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);
        $getInvoice = \App\Xendit\Invoice::retrieve($session['id']);
        $get_amount = $fixedData['get_amount'];

        if ($getInvoice['status'] == 'PAID') {
            $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','xendit')->first();
            $invoice_payment                 = new InvoicePayment();
            $invoice_payment->invoice_id     = $invoice->id;
            $invoice_payment->date           = Date('Y-m-d');
            $invoice_payment->amount         = $get_amount;
            $invoice_payment->account_id     = $account->id;
            $invoice_payment->payment_method = 0;
            $invoice_payment->order_id       = $request->orderId;
            $invoice_payment->payment_type   = 'Xendit';
            $invoice_payment->receipt        = '';
            $invoice_payment->reference      = '';
            $invoice_payment->description    = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
            $invoice_payment->save();

            if(($invoice->getDue() - $invoice_payment->amount) == 0)
            {
                Invoice::change_status($invoice->id, 3);
            }
            else
            {
                Invoice::change_status($invoice->id, 2);
            }

            Utility::addOnlinePaymentData($invoice_payment , $invoice , 'xendit');
            
            //for customer balance update
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            //for bank balance update
            Utility::bankAccountBalance($account->id, $request->amount, 'credit');

            //For Notification
            $setting  = Utility::settingsById($invoice->created_by);
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
        else
        {
            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction fail'));
        }
    }
}
