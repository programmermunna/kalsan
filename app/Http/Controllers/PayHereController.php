<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lahirulhr\PayHere\PayHere;

class PayHereController extends Controller
{
    public function planPayWithPayHere(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $payhere_merchant_id = !empty($payment_setting['payhere_merchant_id']) ? $payment_setting['payhere_merchant_id'] : '';
        $payhere_merchant_secret = !empty($payment_setting['payhere_merchant_secret']) ? $payment_setting['payhere_merchant_secret'] : '';
        $payhere_app_id = !empty($payment_setting['payhere_app_id']) ? $payment_setting['payhere_app_id'] : '';
        $payhere_app_secret = !empty($payment_setting['payhere_app_secret']) ? $payment_setting['payhere_app_secret'] : '';
        $payhere_mode = !empty($payment_setting['payhere_mode']) ? $payment_setting['payhere_mode'] : 'sandbox';
        $currency_code = isset($payment_setting['currency_code']) ? $payment_setting['currency_code'] : 'LKR';
        // $currency_code = 'LKR';
        $user = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($plan) {

            $get_amount = $plan->price;
            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;

                    $get_amount = $plan->price - $discount_value;

                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    if ($get_amount <= 0) {
                        $authuser = Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();
                        $assignPlan = $authuser->assignPlan($plan->id);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {

                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            $userCoupon = new UserCoupon();

                            $userCoupon->user = $authuser->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();
                            Order::create(
                                [
                                    'order_id' => $orderID,
                                    'name' => null,
                                    'email' => null,
                                    'card_number' => null,
                                    'card_exp_month' => null,
                                    'card_exp_year' => null,
                                    'plan_name' => $plan->name,
                                    'plan_id' => $plan->id,
                                    'price' => $get_amount == null ? 0 : $get_amount,
                                    'price_currency' => $currency_code,
                                    'txn_id' => '',
                                    'payment_type' => 'Nepalste',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id);
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }


            try{
                $config = [
                    'payhere.api_endpoint' => $payhere_mode === 'sandbox'
                        ? 'https://sandbox.payhere.lk/'
                        : 'https://www.payhere.lk/',
                ];

                $config['payhere.merchant_id'] = $payhere_merchant_id ?? '';
                $config['payhere.merchant_secret'] = $payhere_merchant_secret ?? '';
                $config['payhere.app_secret'] = $payhere_app_secret ?? '';
                $config['payhere.app_id'] = $payhere_app_id ?? '';
                config($config);

                $hash = strtoupper(
                    md5(
                        $payhere_merchant_id .
                            $orderID .
                            number_format($get_amount, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5($payhere_merchant_secret))
                    )
                );

                $data = [
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => $user->mobile_no ?? '',
                    'address' => 'Main Rd',
                    'city' => 'Anuradhapura',
                    'country' => 'Sri lanka',
                    'order_id' => $orderID,
                    'items' => $plan->name ?? 'Add-on',
                    'currency' => $currency_code,
                    'amount' => $get_amount,
                    'hash' => $hash,
                ];

                return PayHere::checkOut()
                    ->data($data)
                    ->successUrl(route('plan.get.payhere.status', [
                        $plan->id,
                        'amount' => $get_amount,
                        'coupon_code' => !empty($request->coupon_code) ? $request->coupon_code : '',
                        'coupon_id' => !empty($coupons->id) ? $coupons->id : '',
                    ]))
                    ->failUrl(route('plan.get.payhere.status', [
                        $plan->id,
                        'amount' => $get_amount,
                        'coupon_code' => !empty($request->coupon_code) ? $request->coupon_code : '',
                        'coupon_id' => !empty($coupons->id) ? $coupons->id : '',
                    ]))
                    ->renderView();
            } catch (\Exception $e) {
                \Log::debug($e->getMessage());
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        }
    }

    public function planGetPayHereStatus(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency_code = isset($payment_setting['currency_code']) ? $payment_setting['currency_code'] : 'NPR';

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $getAmount = $request->get_amount;
        $authuser = Auth::user();
        $plan = Plan::find($request->plan);

        Utility::referralTransaction($plan);

            $order = new Order();
            $order->order_id = $orderID;
            $order->name = $authuser->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $plan->name;
            $order->plan_id = $plan->id;
            $order->price = $getAmount;
            $order->price_currency = $currency_code;
            $order->txn_id = $orderID;
            $order->payment_type = __('PayHere');
            $order->payment_status = 'success';
            $order->txn_id = '';
            $order->receipt = '';
            $order->user_id = $authuser->id;
            $order->save();
            $assignPlan = $authuser->assignPlan($plan->id);

            $coupons = Coupon::find($request->coupon_id);
            if (!empty($request->coupon_id)) {
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $authuser->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }

            if ($assignPlan['is_success'])
            {
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
            } else
            {
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
    }

    public function invoicePayWithPayHere(Request $request)
    {
        $invoice_id = decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','payhere')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with PayHere.'));
        }

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($user->type != 'company') {
            $user = User::where('id', $user->created_by)->first();
        }

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $payhere_merchant_id = !empty($payment_setting['payhere_merchant_id']) ? $payment_setting['payhere_merchant_id'] : '';
        $payhere_merchant_secret = !empty($payment_setting['payhere_merchant_secret']) ? $payment_setting['payhere_merchant_secret'] : '';
        $payhere_app_id = !empty($payment_setting['payhere_app_id']) ? $payment_setting['payhere_app_id'] : '';
        $payhere_app_secret = !empty($payment_setting['payhere_app_secret']) ? $payment_setting['payhere_app_secret'] : '';
        $payhere_mode = !empty($payment_setting['payhere_mode']) ? $payment_setting['payhere_mode'] : 'sandbox';
        $currency_code = isset($payment_setting['currency_code']) ? $payment_setting['currency_code'] : 'LKR';
        $get_amount = round($request->amount);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try{
            $config = [
                'payhere.api_endpoint' => $payhere_mode === 'sandbox'
                    ? 'https://sandbox.payhere.lk/'
                    : 'https://www.payhere.lk/',
            ];

            $config['payhere.merchant_id'] = $payhere_merchant_id ?? '';
            $config['payhere.merchant_secret'] = $payhere_merchant_secret ?? '';
            $config['payhere.app_secret'] = $payhere_app_secret ?? '';
            $config['payhere.app_id'] = $payhere_app_id ?? '';
            config($config);

            $hash = strtoupper(
                md5(
                    $payhere_merchant_id .
                        $orderID .
                        number_format($get_amount, 2, '.', '') .
                        $currency_code .
                        strtoupper(md5($payhere_merchant_secret))
                )
            );
            $data = [
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone' => $user->mobile_no ?? '',
                'address' => 'Main Rd',
                'city' => 'Anuradhapura',
                'country' => 'Sri lanka',
                'order_id' => $orderID,
                'items' => 'Add-on',
                'currency' => $currency_code,
                'amount' => $get_amount,
                'hash' => $hash,
            ];

            return PayHere::checkOut()
                ->data($data)
                ->successUrl(route('invoice.payhere.status', [
                    $request->invoice_id,
                    'amount' => $get_amount,
                ]))
                ->failUrl(route('invoice.payhere.status', [
                    $request->invoice_id,
                    'amount' => $get_amount,
                ]))
                ->renderView();
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function getInvociePaymentStatus(Request $request)
    {
        $orderID   = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($request->invoice);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($user->type != 'company') {
            $user = User::where('id', $user->created_by)->first();
        }
        $get_amount = $request->amount;
        $settings= Utility::settingsById($invoice->created_by);

        if ($invoice) {
            try {
                $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','payhere')->first();
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->invoice_id     = $invoice->id;
                $invoice_payment->date           = Date('Y-m-d');
                $invoice_payment->amount         = $get_amount;
                $invoice_payment->account_id     = $account->id;
                $invoice_payment->payment_method = 0;
                $invoice_payment->order_id       = $orderID;
                $invoice_payment->payment_type   = 'PayHere';
                $invoice_payment->receipt        = '';
                $invoice_payment->reference      = '';
                $invoice_payment->description    = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
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

                Utility::addOnlinePaymentData($invoice_payment , $invoice , 'payhere');
                
                //for customer balance update
                Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                //for bank balance update
                Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                //For Notification
                $setting  = Utility::settingsById($invoice->created_by);
                $customer = Customer::find($invoice->customer_id);
                $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'PayHere',
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
            } catch (\Exception $e) {
                return redirect()->route('invoice.link.copy',$request->invoice)->with('error', __($e->getMessage()));
            }
        }
    }
}
