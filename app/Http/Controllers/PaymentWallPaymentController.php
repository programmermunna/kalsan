<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Utility;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class PaymentWallPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;

    public function paymentwall(Request $request){
        $data = $request->all();

        $admin_payment_setting = Utility::getAdminPaymentSetting();

        return view('plan.paymentwall',compact('data','admin_payment_setting'));
    }
    public function paymentConfig($user)
    {
        if(Auth::check()){
            $user = Auth::user();
        }
        if($user->type == 'company')
        {
            $payment_setting = Utility::getAdminPaymentSetting();
        }
        else
        {
            $payment_setting = Utility::getCompanyPaymentSetting();
        }

        $this->secret_key = isset($payment_setting['paymentwall_private_key ']) ? $payment_setting['paymentwall_private_key  '] : '';
        $this->public_key = isset($payment_setting['paymentwall_public_key']) ? $payment_setting['paymentwall_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled']) ? $payment_setting['is_paymentwall_enabled'] : 'off';

        return $this;
    }



    public function invoicepaymentwall(Request $request){
        $data = $request->all();
        $company_payment_setting = Utility::getCompanyPayment();

        return view('invoice.paymentwall',compact('data','company_payment_setting'));
    }

    public function invoiceerror(Request $request,$flag,$invoice_id)
    {

        if($flag == 1)
        {
            return redirect()->route('invoice.show',encrypt($invoice_id))->with('error', __('Payment successfully added. '));
        }
        else
        {
            return redirect()->route("invoice.show",encrypt($invoice_id))->with('error', __('Transaction has been failed! '));
        }
    }


    public function invoicePayWithPaymentwall(Request $request,$invoiceID)
    {

        $invoiceID = \Crypt::decrypt($invoiceID);

        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paymentwall')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with PaymentWall.'));
        }

        if(\Auth::check())
        {
            $user=\Auth::user();
        }
        else
        {
            $user= User::where('id',$invoice->created_by)->first();
        }

        if($invoice)
        {
            $price = $request->amount;

            if($price < 0)
            {
                $res_data['email']       = $user->email;
                $res_data['total_price'] = $request->amount;
                $res_data['currency']    = $this->currancy;
                $res_data['flag']        = 1;
                $res_data['invoice_id']  = $invoice->id;

                // return $res_data;

            }

            else
            {
                $authuser = Auth::user();
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => 'sdrsefrszdef'
                ));
                $parameters = $request->all();
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );
                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber();
                        $invoice_payment->invoice_id     = $invoice->id;
                        $invoice_payment->amount         = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->account_id     = $account->id;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = 'Paystack';
                        $invoice_payment->notes          = '';
                        $invoice_payment->client_id      = $user->id;
                        $invoice_payment->save();

                        if(($invoice->getDue() - $invoice_payment->amount) == 0)
                        {
                            Invoice::change_status($invoice->id, 3);
                        }
                        else
                        {
                            Invoice::change_status($invoice->id, 2);
                        }

                        Utility::addOnlinePaymentData($invoice_payment , $invoice , 'paymentwall');

                        //for customer balance update
                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                        //for bank balance update
                        Utility::bankAccountBalance($account->id, $request->amount, 'credit');
                        
                        $assignPlan = $authuser->assignPlan($invoice->id);
                        if($assignPlan['is_success'])
                        {
                            $res['msg'] = __("Invoice successfully .");
                            $res['flag'] = 1;
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        // decide on risk charge
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                            $res['invoice']=$invoiceID;
                            $res['flag'] = 2;
                            return $res;
                }
                echo $response;

            }
        }

    }
}
