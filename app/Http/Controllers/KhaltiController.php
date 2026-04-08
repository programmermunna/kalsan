<?php

namespace App\Http\Controllers;

use App\Khalti\Khalti;
use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;

class KhaltiController extends Controller
{
    public function planPayWithKhalti(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();

        config(
            [
                'khalti.public_key' => isset($payment_setting['khalti_public_key']) ? $payment_setting['khalti_public_key'] : '',
                'khalti.sck' => isset($payment_setting['khalti_secret_key']) ? $payment_setting['khalti_secret_key'] : '',
            ]
        );
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = \Auth::user();
        $coupon_id = '';
        if ($plan) {
            try {
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
                $secret = !empty($payment_setting['khalti_secret_key']) ? $payment_setting['khalti_secret_key'] : '';
                $amount = $price;
                return $amount;
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetKhaltiStatus(Request $request)
    {
        $admin_settings = Utility::getAdminPaymentSetting();
        $currency = isset($admin_settings['currency']) ? $admin_settings['currency'] : 'USD';
        $plan_id = decrypt($request->plan_id);
        $plan = Plan::find($plan_id);
        $user = \Auth::user();

        $payload = $request->payload;
        $secret = !empty($admin_settings['khalti_secret_key']) ? $admin_settings['khalti_secret_key'] : '';
        $token = $payload['token'];
        $amount = $payload['amount'];
        $khalti = new Khalti();
        $response = $khalti->verifyPayment($secret, $token, $amount);

        Utility::referralTransaction($plan);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        try {
            if ($response['status_code'] == '200') {
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $amount;
                $order->price_currency = $currency;
                $order->txn_id = time();
                $order->payment_type = __('Khalti');
                $order->payment_status = 'success';
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $user = User::find($user->id);

                $assignPlan = $user->assignPlan($plan->id);

                if ($assignPlan['is_success']) {
                    return $response;
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }
        } catch (\Exception $e) {
            return response()->json('failed');
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $invoice_id = decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','khalti')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Khalti.'));
        }

        $user = User::find($invoice->created_by);
        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $payload = $request->payload;
        $secret = isset($company_payment_setting['khalti_secret_key']) ? $company_payment_setting['khalti_secret_key'] : '';
        $token = $payload['token'];
        $amount = $payload['amount'];
        $khalti = new Khalti();
        $response = $khalti->verifyPayment($secret, $token, $amount);
        $settings = Utility::settingsById($invoice->created_by);
        if ($invoice) {
            if ($response['status_code'] == '200') {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice->id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->account_id         = $account->id;
                    $invoice_payment->payment_method         = 0;
                    $invoice_payment->order_id      = $orderID;
                    $invoice_payment->payment_type   = 'Khalti';
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

                    Utility::addOnlinePaymentData($invoice_payment , $invoice , 'khalti');
                    
                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Khalti',
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
                    return $response;
                } catch (\Exception $e) {
                    return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', $e->getMessage());
                }
            } else {
                return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
        }
    }
}
