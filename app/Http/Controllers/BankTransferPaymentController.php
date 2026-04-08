<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoiceBankTransfer;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BankTransferPaymentController extends Controller
{
    protected $invoiceData;


    public function customerPayWithBank(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                'payment_receipt' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        try{
            $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','bank transfer')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Bank Transfer.'));
        }

        $user      = User::find($invoice->created_by);
        $settings=Utility::settingsById($invoice->created_by);
        if($invoice)
        {

            if(!empty($request->payment_receipt))
            {
                $fileName = time() . "_" . $request->payment_receipt->getClientOriginalName();
                $dir        = 'uploads/order';
                $path = Utility::upload_file($request,'payment_receipt',$fileName,$dir,[]);
            }
            $orderID           = strtoupper(str_replace('.', '', uniqid('', true)));

            InvoiceBankTransfer::create(
                [
                    'invoice_id' =>$invoice->id,
                    'order_id' => $orderID,
                    'amount' => $request->amount,
                    'status' => 'Pending',
                    'date' => date('Y-m-d'),
                    'receipt' => $fileName,
                    'created_by' => $user->id,
                ]
            );

            return redirect()->back()->with('success', __('Invoice payment request send successfully.'));

        }
        else
        {
            return redirect()->back()->with('success', __('Invoice payment request send successfully.'));



        }


    }

    public function invoiceAction($id)
    {

        $invoiceBankTransfer     = InvoiceBankTransfer::find($id);
        $invoice = Invoice::find($invoiceBankTransfer->invoice_id);
        $user_id        = $invoiceBankTransfer->created_by;

        $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);


        return view('invoice.action', compact('invoiceBankTransfer','company_payment_setting','invoice'));
    }

    public function invoiceChangeStatus(Request $request , $invoice_id)
    {


        $invoiceBankTransfer = InvoiceBankTransfer::find($request->order_id);
        $invoice = Invoice::find($invoiceBankTransfer->invoice_id);

        $settings = Utility::settingsById($invoiceBankTransfer->created_by);

        if($request->status == 'Approval')
        {
            $bankAccount = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','bank transfer')->first();
            if(!$bankAccount)
            {
                return redirect()->back()->with('error', __('Bank account not connected with Bank Transfer.'));
            }
            
            $invoiceBankTransfer->status           = 'Approved';
            $payments = InvoicePayment::create(
                [

                    'invoice_id' => $invoiceBankTransfer->invoice_id,
                    'date' => date('Y-m-d'),
                    'amount' => $invoiceBankTransfer->amount,
                    'account_id' => $bankAccount->id,
                    'payment_method' => 1,
                    'order_id' => $invoiceBankTransfer->order_id,
                    'payment_type' => __('Bank Transfer'),
                    'receipt' => $invoiceBankTransfer->receipt,
                    'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                ]
            );

            Utility::addOnlinePaymentData($payments , $invoice , 'bank transfer');

            Utility::updateUserBalance('customer', $invoice->customer_id, $invoiceBankTransfer->amount, 'debit');
            Utility::bankAccountBalance($bankAccount->id, $invoiceBankTransfer->amount, 'credit');
            
            $invoiceBankTransfer->delete();
        }
        else
        {
            $invoiceBankTransfer->status           = 'Rejected';
        }
        $invoiceBankTransfer->save();

        return redirect()->back()->with('success', __('Invoice payment request status updated successfully.'));
    }



}
