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
use App\Models\Utility;
use Easebuzz\Easebuzz;
use Illuminate\Http\Request;

class EasebuzzController extends Controller
{
    public function planPayWithEasebuzz(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';

        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = \Auth::user();
        $coupon_id = '';
        if ($plan) {

            $price = $plan->price;
            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if ($usedCoupun >= $coupons->limit) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $price     = $price - $discount_value;
                    $coupon_id = $coupons->id;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if ($assignPlan['is_success'] == true && !empty($plan)) {

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
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
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => $currency,
                            'txn_id' => '',
                            'payment_type' => 'Razorpay',
                            'payment_status' => 'success',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg']  = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                } else {
                    return Utility::error_res(__('Plan fail to upgrade.'));
                }
            }
            $easebuzz_merchant_key = isset($payment_setting['easebuzz_merchant_key']) ? $payment_setting['easebuzz_merchant_key'] : '';
            $easebuzz_salt_key = isset($payment_setting['easebuzz_salt_key']) ? $payment_setting['easebuzz_salt_key'] : '';
            $easebuzz_enviroment_name = isset($payment_setting['easebuzz_enviroment_name']) ? $payment_setting['easebuzz_enviroment_name'] : '';
            $call_back = route('plan.get.easebuzz.notify', ['plan_id' => $plan->id, 'coupon_code' => $request->coupon_code, 'amount' => $price]) . '?_token=' . csrf_token();
            $returnURL = route('plan.easebuzz.return') . '?_token=' . csrf_token();
            $transaction_id = strtoupper(str_replace('.', '', uniqid('', true)));
            $easebuzzObj = new Easebuzz($easebuzz_merchant_key, $easebuzz_salt_key, $easebuzz_enviroment_name);

            $price = number_format((float)$price, 2, '.', '');
            $postData = array(
                "txnid" => $transaction_id,
                "amount" => $price,
                "firstname" => $authuser->name,
                "email" => $authuser->email,
                "phone" => '1111111111',
                "productinfo" => $plan->name,
                "surl" => $call_back,
                "furl" => $returnURL,
                "udf1" => "aaaa",
                "udf2" => "aaaa",
                "udf3" => "aaaa",
                "udf4" => "aaaa",
                "udf5" => "aaaa",
                "address1" => "aaaa",
                "address2" => "aaaa",
                "city" => "aaaa",
                "state" => "aaaa",
                "country" => "aaaa",
                "zipcode" => "123123"
            );
            $easebuzzObj->initiatePaymentAPI($postData);
            if ($easebuzzObj) {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        } else {
            return Utility::error_res(__('Plan is deleted.'));
        }
    }

    public function notify_url(Request $request)
    {
        $plan  = Plan::find($request->plan_id);
        $user = \Auth::user();
        $orderID = time();
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        if ($plan) {
            $price                  = $request->amount;
            if ($plan && $request->error == 'Transaction is successful.') {
                if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                    try {
                        $user->cancel_subscription($user->id);
                    } catch (\Exception $exception) {
                        \Log::debug($exception->getMessage());
                    }
                }
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $price;
                $order->price_currency = $currency;
                $order->txn_id = time();
                $order->payment_type = __('Easebuzz');
                $order->payment_status = 'success';
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $user = User::find($user->id);

                $assignPlan = $user->assignPlan($plan->id);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
            }
        }
    }

    public function return_url(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has been failed'));
    }

    public function invoicePayWitheasebuzz(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','easebuzz')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Easebuzz.'));
        }

        $user = User::find($invoice->created_by);

        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $get_amount = $request->amount;
        try {
            if ($invoice) {
                $easebuzz_merchant_key = isset($company_payment_setting['easebuzz_merchant_key']) ? $company_payment_setting['easebuzz_merchant_key'] : '';
                $easebuzz_salt_key = isset($company_payment_setting['easebuzz_salt_key']) ? $company_payment_setting['easebuzz_salt_key'] : '';
                $easebuzz_enviroment_name = isset($company_payment_setting['easebuzz_enviroment_name']) ? $company_payment_setting['easebuzz_enviroment_name'] : '';
                $call_back = route('invoice.get.easebuzz.notify', ['invoice_id' => $invoice->id, 'amount' => $get_amount]) . '?_token=' . csrf_token();
                $returnURL = route('invoice.easebuzz.return') . '?_token=' . csrf_token();
                $transaction_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $easebuzzObj = new Easebuzz($easebuzz_merchant_key, $easebuzz_salt_key, $easebuzz_enviroment_name);

                $price = number_format((float)$get_amount, 2, '.', '');
                $postData = array(
                    "txnid" => $transaction_id,
                    "amount" => $price,
                    "firstname" => $user->name,
                    "email" => $user->email,
                    "phone" => '1111111111',
                    "productinfo" => 'aaa',
                    "surl" => $call_back,
                    "furl" => $returnURL,
                    "udf1" => "aaaa",
                    "udf2" => "aaaa",
                    "udf3" => "aaaa",
                    "udf4" => "aaaa",
                    "udf5" => "aaaa",
                    "address1" => "aaaa",
                    "address2" => "aaaa",
                    "city" => "aaaa",
                    "state" => "aaaa",
                    "country" => "aaaa",
                    "zipcode" => "123123"
                );
                $easebuzzObj->initiatePaymentAPI($postData);
                if ($easebuzzObj) {
                    return redirect()->back()->with('error', __('Something went wrong.'));
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }
    public function invoiceReturnUrl(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);
        $user = User::find($invoice->created_by);
        $settings = Utility::settingsById($invoice->created_by);
        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        if ($invoice) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try {
                $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','easebuzz')->first();
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->invoice_id     = $request->invoice_id;
                $invoice_payment->date           = Date('Y-m-d');
                $invoice_payment->amount         = $request->amount;
                $invoice_payment->account_id         = $account->id;
                $invoice_payment->payment_method         = 0;
                $invoice_payment->order_id      = $orderID;
                $invoice_payment->payment_type   = 'Easebuzz';
                $invoice_payment->receipt     = '';
                $invoice_payment->reference     = '';
                $invoice_payment->description     = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
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

                Utility::addOnlinePaymentData($invoice_payment , $invoice , 'easebuzz');

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
                return redirect()->route('invoice.link.copy', \Crypt::encrypt($request->invoice_id))->with('success', __('Invoice paid Successfully!'));
            } catch (\Exception $e) {
                return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', $e->getMessage());
            }
        } else {
            return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
        }
    }

    public function invoiceNotifyUrl(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has been failed'));
    }
}
