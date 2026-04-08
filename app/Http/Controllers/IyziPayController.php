<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Exception;

class IyziPayController extends Controller
{

    public function invoicepaywithiyzipay(Request $request)
    {
        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','iyzipay')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Iyzipay.'));
        }

        $authuser      = User::find($invoice->created_by);
        $companyPaymentSettings = Utility::getCompanyPaymentSetting($authuser->id);
        $iyzipay_mode = $companyPaymentSettings['iyzipay_mode'];
        $iyzipay_public_key = $companyPaymentSettings['iyzipay_public_key'];
        $iyzipay_secret_key = $companyPaymentSettings['iyzipay_secret_key'];
        $settings  = DB::table('settings')->where('created_by', '=',$invoice->created_by)->get()->pluck('value', 'name');
        $get_amount = $request->amount;
        $currency= Utility::getValByName('site_currency');

        if ($invoice)
        {
            if ($get_amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            }
            $res_data['total_price'] = $get_amount;
            try {
                $setBaseUrl = ($iyzipay_mode == 'sandbox') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
                $options = new \Iyzipay\Options();
                $options->setApiKey($iyzipay_public_key);
                $options->setSecretKey($iyzipay_secret_key);
                $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
                $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
                $address = ($authuser->address) ? $authuser->address : 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
                // create a new payment request
                $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
                $request->setLocale('en');
                $request->setPrice($res_data['total_price']);
                $request->setPaidPrice($res_data['total_price']);
                $request->setCurrency($currency);
                $request->setCallbackUrl(route('iyzipay.invoicepayment.callback',[$invoice->id,$get_amount]));
                $request->setEnabledInstallments(array(1));
                $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
                $buyer = new \Iyzipay\Model\Buyer();
                $buyer->setId($authuser->id);
                $buyer->setName(explode(' ', $authuser->name)[0]);
                $buyer->setSurname(explode(' ', $authuser->name)[0]);
                $buyer->setGsmNumber("+" . $authuser->dial_code . $authuser->phone);
                $buyer->setEmail($authuser->email);
                $buyer->setIdentityNumber(rand(0, 999999));
                $buyer->setLastLoginDate("2023-03-05 12:43:35");
                $buyer->setRegistrationDate("2023-04-21 15:12:09");
                $buyer->setRegistrationAddress($address);
                $buyer->setIp($ipAddress['ip']);
                $buyer->setCity($ipAddress['city']);
                $buyer->setCountry($ipAddress['country']);
                $buyer->setZipCode($ipAddress['postal']);
                $request->setBuyer($buyer);
                $shippingAddress = new \Iyzipay\Model\Address();
                $shippingAddress->setContactName($authuser->name);
                $shippingAddress->setCity($ipAddress['city']);
                $shippingAddress->setCountry($ipAddress['country']);
                $shippingAddress->setAddress($address);
                $shippingAddress->setZipCode($ipAddress['postal']);
                $request->setShippingAddress($shippingAddress);
                $billingAddress = new \Iyzipay\Model\Address();
                $billingAddress->setContactName($authuser->name);
                $billingAddress->setCity($ipAddress['city']);
                $billingAddress->setCountry($ipAddress['country']);
                $billingAddress->setAddress($address);
                $billingAddress->setZipCode($ipAddress['postal']);
                $request->setBillingAddress($billingAddress);
                $basketItems = array();
                $firstBasketItem = new \Iyzipay\Model\BasketItem();
                $firstBasketItem->setId("BI101");
                $firstBasketItem->setName("Binocular");
                $firstBasketItem->setCategory1("Collectibles");
                $firstBasketItem->setCategory2("Accessories");
                $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $firstBasketItem->setPrice($res_data['total_price']);
                $basketItems[0] = $firstBasketItem;
                $request->setBasketItems($basketItems);

                $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
                return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
            } catch (\Exception $e) {
                return redirect()->route('customer.invoice.show')->with('errors', $e->getMessage());
            }
        }
        else{
            return redirect()
                ->route('customer.invoice.show', \Crypt::encrypt($invoice->id))
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }

        return redirect()->back()->with('error', __('Unknown error occurred'));
    }

    public function getInvoiceiyzipayCallback(Request $request, $invoice_id, $amount)
    {

        $invoice = Invoice::find($invoice_id);
        $settings  = Utility::settingsById($invoice->created_by);
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        try
        {
                $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','iyzipay')->first();
                $payments = InvoicePayment::create(
                    [

                        'invoice_id' => $invoice->id,
                        'date' => date('Y-m-d'),
                        'amount' => $amount,
                        'account_id' => $account->id,
                        'payment_method' => 0,
                        'order_id' => $orderID,
                        'payment_type' => __('Iyzipay'),
                        'receipt' => '',
                        'reference' => '',
                        'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                    ]
                );

                $invoicePayment              = new Transaction();
                $invoicePayment->user_id     = $invoice->customer_id;
                $invoicePayment->user_type   = 'Customer';
                $invoicePayment->type        = 'Iyzipay';
                $invoicePayment->created_by  = \Auth::check() ? \Auth::user()->id : $invoice->customer_id;
                $invoicePayment->payment_id  = $invoicePayment->id;
                $invoicePayment->category    = 'Invoice';
                $invoicePayment->amount      = $amount;
                $invoicePayment->date        = date('Y-m-d');
                $invoicePayment->created_by  = \Auth::check() ? \Auth::user()->creatorId() : $invoice->created_by;
                $invoicePayment->payment_id  = $payments->id;
                $invoicePayment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                $invoicePayment->account     = 0;

                Utility::addOnlinePaymentData($payments , $invoice , 'iyzipay');

                \App\Models\Transaction::addTransaction($invoicePayment);

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


                if (\Auth::check()) {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else
                {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }


        }
        catch (\Exception $e)
        {
            if (\Auth::check())
            {
                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
            } else
            {
                return redirect()->back()->with('success', __('Transaction has been completed.'));
            }
        }
    }


}
