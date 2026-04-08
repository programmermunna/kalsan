<?php

namespace App\Http\Controllers;

use App\Models\AddTransactionLine;
use App\Models\BankAccount;
use App\Models\BillPayment;
use App\Models\ChartOfAccount;
use App\Models\Payment;
use App\Models\ProductServiceCategory;
use App\Models\Transaction;
use App\Models\Utility;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    public function index(Request $request)
    {
        if(\Auth::user()->can('manage payment'))
        {
            $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

             $expenses = ChartOfAccount::where('created_by', '=', \Auth::user()->creatorId())
            ->where('type', 6)  // type 6 = Expenses
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');



            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('bank_name', 'id');
            $account->prepend('Select Account', '');

            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'expense')->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');


            $query = Payment::where('created_by', '=', \Auth::user()->creatorId());

            if(count(explode('to', $request->date)) > 1)
            {
                $date_range = explode(' to ', $request->date);
                $query->whereBetween('date', $date_range);
            }
            elseif(!empty($request->date))
            {
                $date_range = [$request->date , $request->date];
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->vender))
            {
                $query->where('vender_id', '=', $request->vender);
            }
            if(!empty($request->account))
            {
                $query->where('account_id', '=', $request->account);
            }

            if(!empty($request->category))
            {
                $query->where('category_id', '=', $request->category);
            }


            $payments = $query->with(['category','vender','bankAccount'])->get();


            return view('payment.index', compact('payments', 'account', 'category', 'vender','expenses'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


   public function create()
{
    if(\Auth::user()->can('create payment'))
    {
        $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $venders->prepend('Select Vendor', 0);
        $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'expense')->get()->pluck('name', 'id');
        $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name) AS name"))->where('created_by', \Auth::user()->creatorId())->where('topup', 0) ->get()->pluck('name', 'id');

         $expenses = ChartOfAccount::where('created_by', '=', \Auth::user()->creatorId())
        ->where('type', 6)  // type 6 = Expenses
        ->orderBy('name')
        ->get()
        ->pluck('name', 'id');

        return view('payment.create', compact('venders', 'categories', 'accounts', 'expenses'));
    }
    else
    {
        return response()->json(['error' => __('Permission denied.')], 401);
    }
}
    public function store(Request $request)
    {


        if(\Auth::user()->can('create payment'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'amount' => 'required',
                                   'account_id' => 'required',
                                   'expense_id' => 'required',
                                //    'category_id' => 'required',
                                  // 'vender_id' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $bankAccount = BankAccount::find($request->account_id);
            if($bankAccount->chart_account_id == 0)
            {
                return redirect()->back()->with('error', __('This bank account is not connect with chart of account, so please connect first.'));
            }

             if ($request->type == 'expenses') {
                $vender_id = '0';
            } else {
                $vender_id = $request->vender_id;
            }

            $payment                 = new Payment();
            $payment->date           = $request->date;
            $payment->amount         = $request->amount;
            $payment->account_id     = $request->account_id;
            $payment->vender_id      = $vender_id;
            // $payment->category_id    = $request->category_id;
            $payment->chart_account_id = $request->expense_id;
            $payment->payment_method = 0;
            $payment->reference      = $request->reference;
            // if(!empty($request->add_receipt))
            // {

            //     $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            //     $payment->add_receipt = $fileName;
            //     $dir        = 'uploads/payment';
            //     $path = Utility::upload_file($request,'add_receipt',$fileName,$dir,[]);
            //     if($path['flag']==0){
            //         return redirect()->back()->with('error', __($path['msg']));
            //     }
            // }


            $payment->description    = $request->description;
            $payment->created_by     = \Auth::user()->creatorId();
            $payment->save();



            $account     = BankAccount::find($payment->account_id);
            $get_account = ChartOfAccount::find($account->chart_account_id);
            $account1 = ChartOfAccount::find($payment->chart_account_id);

             if ($request->type == 'expenses') {
                $payment->vender_id = '0';
                $payment->reference = 'Payment Expense';
                $account_id = !empty($account1) ? $account1->id : 0;
            } else {
                $payment->vender_id = $request->vender_id;
                $payment->reference = 'Payment Vender';
                $account_id = '10';
            }

            $data        = [
                'account_id'         => !empty($get_account)? $get_account->id : 0,
                'bank_id'            => '1',
                'transaction_type'   => 'credit',
                'transaction_amount' => $payment->amount,
                'reference'          => $payment->reference,
                'reference_id'       => $payment->id,
                'reference_sub_id'   => '0',
                'customer_id'        => '0',
                'vender_id'          => '0',
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data);


            $data    = [
                'account_id'         => $account_id,
                'bank_id'            => '0',
                'transaction_type'   => 'debit',
                'transaction_amount' => $payment->amount,
                'reference'          => $payment->reference,
                'reference_id'       => $payment->id,
                'reference_sub_id'   => 0,
                'customer_id'        => '0',
                'vender_id'          => $payment->vender_id,
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data);

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
            $payment->payment_id = $payment->id;
            $payment->type       = 'Payment';
            // $payment->category   = $category->name;
            $payment->user_id    = $payment->vender_id;
            $payment->user_type  = 'Vender';
            $payment->account    = $request->account_id;

            Transaction::addTransaction($payment);

            $vender          = Vender::where('id', $request->vender_id)->first();
            $payment         = new BillPayment();
            $payment->name   = !empty($vender) ? $vender['name'] : '' ;
            $payment->method = '-';
            $payment->date   = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->bill   = '';

            if(!empty($vender))
            {
                Utility::updateUserBalance('vendor', $vender->id, $request->amount, 'credit');
            }
            Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');


            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $vender = Vender::find($request->vender_id);
            $paymentNotificationArr = [
                'payment_amount' => \Auth::user()->priceFormat($request->amount),
                'vendor_name' =>  $vender != null  ? $vender->name : '',
                'payment_type' =>  'Payment',
            ];
            //Twilio Notification
            if(isset($setting['twilio_payment_notification']) && $setting['twilio_payment_notification'] ==1)
            {
                Utility::send_twilio_msg($$vender->contact,'bill_payment', $paymentNotificationArr);
            }

              return redirect()->route('payment.index')->with('success', __('Payment successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Payment $payment)
    {

        if(\Auth::user()->can('edit payment'))
        {
            $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $venders->prepend('Select Vendor', 0);
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->where('type', '=', 'expense')->pluck('name', 'id');

            $accounts = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $expenses = ChartOfAccount::where('created_by', '=', \Auth::user()->creatorId())
            ->where('type', 6)  // type 6 = Expenses
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');
            return view('payment.edit', compact('venders', 'categories', 'accounts', 'payment', 'expenses'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, Payment $payment)
    {
        if(\Auth::user()->can('edit payment'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'amount' => 'required',
                                   'account_id' => 'required',
                                   'expense_id' => 'required',
                                //    'category_id' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $vender = Vender::where('id', $request->vender_id)->first();
            if(!empty($vender))
            {
                Utility::updateUserBalance('vendor', $vender->id, $payment->amount, 'debit');
            }
            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            $payment->date           = $request->date;
            $payment->amount         = $request->amount;
            $payment->account_id     = $request->account_id;
            //$payment->vender_id      = $request->vender_id;
            $payment->chart_account_id     = $request->expense_id;
            // $payment->category_id    = $request->category_id;
            $payment->payment_method = 0;
            $payment->reference      = $request->reference;

            if(!empty($request->add_receipt))
            {

                if($payment->add_receipt)
                {
                    $path = storage_path('uploads/payment' . $payment->add_receipt);
                    if(file_exists($path))
                    {
                        \File::delete($path);
                    }
                }
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $payment->add_receipt = $fileName;
                $dir        = 'uploads/payment';
                $path = Utility::upload_file($request,'add_receipt',$fileName,$dir,[]);
                if($path['flag']==0){
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            $payment->description    = $request->description;
            $payment->save();

            $account     = BankAccount::find($payment->account_id);
            $get_account = ChartOfAccount::find($account->chart_account_id);
            $data        = [
                'account_id'         => !empty($get_account)? $get_account->id : 0,
                'transaction_type'   => 'credit',
                'transaction_amount' => $payment->amount,
                'reference'          => 'Payment',
                'reference_id'       => $payment->id,
                'reference_sub_id'   => 0,
                'customer_id'        => '0',
                'vender_id'          => '0',
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data , 'edit' , 'notes');

            $account = ChartOfAccount::find($payment->chart_account_id);
            $data    = [
                'account_id'         => !empty($account) ? $account->id : 0,
                'transaction_type'   => 'debit',
                'transaction_amount' => $payment->amount,
                'reference'          => 'Payment',
                'reference_id'       => $payment->id,
                'reference_sub_id'   => 0,
                'customer_id'        => '0',
                'vender_id'          => $request->vender_id,
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data , 'edit');

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
           // $payment->category   = $category->name;
            $payment->payment_id = $payment->id;
            $payment->type       = 'Payment';
            $payment->account    = $request->account_id;
            Transaction::editTransaction($payment);

            if(!empty($vender))
            {
                Utility::updateUserBalance('vendor', $vender->id, $request->amount, 'credit');
            }
            Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

            return redirect()->route('payment.index')->with('success', __('Payment successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Payment $payment)
    {
        if(\Auth::user()->can('delete payment'))
        {
            if($payment->created_by == \Auth::user()->creatorId())
            {
                $payment->delete();
                $type = 'Payment Vender';
                $user = 'Payment Expense';
                AddTransactionLine::where('reference_id',$payment->id)->delete();

                Transaction::destroyTransaction($payment->id, $type, $user);

                if($payment->vender_id != 0)
                {
                    Utility::updateUserBalance('vendor', $payment->vender_id, $payment->amount, 'debit');
                }
                Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

                return redirect()->route('payment.index')->with('success', __('Payment successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
