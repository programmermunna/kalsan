<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InvoicePayment;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class PayFastController extends Controller
{

    public function paymentConfig()
    {
        if(\Auth::check())
        {
            $payment_setting = Utility::getAdminPaymentSetting();
        }
        else
        {
            $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);
        }
        $this->payfast_merchant_id = isset($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '';
        $this->payfast_merchant_key = isset($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '';
        $this->payfast_signature = isset($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
        $this->payfast_mode = isset($payment_setting['payfast_mode']) ? $payment_setting['payfast_mode'] : 'off';
        $this->is_enabled = isset($payment_setting['is_payfast_enabled']) ? $payment_setting['is_payfast_enabled'] : 'off';

        return $this;
    }

    public function planPayWithPayfast(Request $request)
    {
        $payment_setting = $this->paymentConfig();
        $planID = Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        if ($plan)
            {
                $plan_amount = $plan->price;
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $user = Auth::user();
                if ($request->coupon_code != null)
                {
                    $coupons = Coupon::where('code', $request->coupon_code)->first();
                    if (!empty($coupons))
                    {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $order_id;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                        $plan_amount = $request->coupon_amount;
                    }
                }
                if( $plan_amount<1){
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $user = Auth::user();
                    $order                 = new Order();
                    $order->order_id       = $orderID;
                    $order->name           = $user->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->plan_name      = $plan->name;
                    $order->plan_id        = $plan->id;
                    $order->price          = $plan_amount;
                    $order->price_currency = 'USD';
                    $order->txn_id         = '';
                    $order->payment_type   = __('PayFast');
                    $order->payment_status = 'success';
                    $order->receipt        = '';
                    $order->user_id        = $user->id;
                    $order->save();
                    $assignPlan = $user->assignPlan($plan->id);
                    if($assignPlan['is_success'])
                    {
                        return response()->json(['success' => __('Plan activated Successfully.')]);
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }

                }

                $success = Crypt::encrypt([
                    'plan' => $plan->toArray(),
                    'order_id' => $order_id,
                    'plan_amount' => $plan_amount
                ]);

                $data = array(
                    // Merchant details
                    'merchant_id' => !empty($payment_setting->payfast_merchant_id) ? $payment_setting->payfast_merchant_id : '',
                    'merchant_key' => !empty($payment_setting->payfast_merchant_key) ? $payment_setting->payfast_merchant_key : '',
                    'return_url' => route('payfast.payment.success',$success),
                    'cancel_url' => route('plans.index'),
                    'notify_url' => route('plans.index'),
                    'name_first' => $user->name,
                    'name_last' => '',
                    'email_address' => $user->email,
                    'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
                    'amount' => number_format(sprintf('%.2f', $plan_amount), 2, '.', ''),
                    'item_name' => $plan->name,
                );

                $passphrase = !empty($payment_setting->payfast_signature) ? $payment_setting->payfast_signature : '';


                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }


                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);

            }

    }

    public function generateSignature($data, $passPhrase = null)
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function getPaymentStatus($success)
    {
        try{
            $user = Auth::user();
            $data = Crypt::decrypt($success);

            $order = new Order();
            $order->order_id = $data['order_id'];
            $order->name = $user->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $data['plan']['name'];
            $order->plan_id = $data['plan']['id'];
            $order->price = $data['plan_amount'];
            $order->price_currency = 'USD';
            $order->txn_id = $data['order_id'];
            $order->payment_type = __('PayFast');
            $order->payment_status = 'success';
            $order->txn_id = '';
            $order->receipt = '';
            $order->user_id = $user->id;
            $order->save();
            $assignPlan = $user->assignPlan($data['plan']['id']);

            if ($assignPlan['is_success'])
            {
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
            } else
            {
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
        }catch(Exception $e)
        {
            return redirect()->route('plans.index')->with('error', __($e));
        }
    }

    public function invoicePayWithPayFast(Request $request)
    {
        $invoiceID = Crypt::decrypt($request->invoice_id);
        $invoice                 = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','payfast')->first();
        if(!$account)
        {
            return response()->json([
                'success' => false,
                'inputs' => __('Bank account not connected with PayFast.'),
            ]);
        }

        $user      = User::find($invoice->created_by);
        $settings=Utility::settingsById($invoice->created_by);
        $payment_setting   = $this->paymentConfig();
        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
        $get_amount = $request->amount;
        $success = Crypt::encrypt([
            'invoice' => $invoice->id,
            'order_id' => $order_id,
            'invoice_amount' => $get_amount
        ]);
        $data = array(
            'merchant_id' => !empty($payment_setting->payfast_merchant_id) ? $payment_setting->payfast_merchant_id : '',
            'merchant_key' => !empty($payment_setting->payfast_merchant_key) ? $payment_setting->payfast_merchant_key : '',
            'return_url' => route('invoice.payfast.status', $success),
            'name_first' => $user->name,
            'name_last' => '',
            'email_address' => $user->email,
            'm_payment_id' => $order_id, // Unique payment ID to pass through to notify_url
            'amount' => number_format(sprintf('%.2f', $get_amount), 2, '.', ''),
            'item_name' => $user->name,
        );
        $passphrase = !empty($payment_setting->payfast_signature) ? $payment_setting->payfast_signature : '';
        $signature = $this->generateSignature($data, $passphrase);
        $data['signature'] = $signature;
        $htmlForm = '';
        foreach ($data as $name => $value) {
            $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
        }
        return response()->json([
            'success' => true,
            'inputs' => $htmlForm,
        ]);

    }

    public function invoicepayfaststatus(Request $request, $success)
    {
        $data = Crypt::decrypt($success);
        $invoice                 = Invoice::find($data['invoice']);
        $settings  = Utility::settingsById($invoice->created_by);
        if (empty($request->PayerID || empty($request->token)))
        {
            return redirect()->back()->with('error', __('Payment failed'));
        }
        try {
            $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','payfast')->first();
            $payments = InvoicePayment::create(
                    [
                        'invoice_id' => $invoice->id,
                        'date' => date('Y-m-d'),
                        'amount' => $data['invoice_amount'],
                        'account_id' => $account->id,
                        'payment_method' => 1,
                        'order_id' =>  $data['order_id'],
                        'currency' => Utility::getValByName('site_currency'),
                        'txn_id' =>  $data['order_id'],
                        'payment_type' => __('Payfast'),
                        'receipt' => '',
                        'reference' => '',
                        'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                    ]
                );
                if ($invoice->getDue() <= 0) {
                    $invoice->status = 4;
                    $invoice->save();
                } elseif (($invoice->getDue() - $payments->amount) == 0) {
                    $invoice->status = 4;
                    $invoice->save();
                } elseif ($invoice->getDue() > 0) {
                    $invoice->status = 3;
                    $invoice->save();
                }
                else {
                    $invoice->status = 2;
                    $invoice->save();
                }

            Utility::addOnlinePaymentData($payments , $invoice , 'payfast');
            
            //for customer balance update
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            //for bank balance update
            Utility::bankAccountBalance($account->id, $request->amount, 'credit');

            //For Notification
            $setting  = Utility::settingsById($invoice->created_by);
            $customer = Customer::find($invoice->customer_id);
            $notificationArr = [
                'payment_price' => $request->amount,
                'invoice_payment_type' => 'Payfast',
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

                if (Auth::check())
                {
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));
                } else {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }
              }
            catch (\Exception $e)
            {
                if (Auth::check())
                {
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed! '));
                } else
                {
                    return redirect()->back()->with('success', __('Transaction has been complted.'));
                }
            }
    }

}
