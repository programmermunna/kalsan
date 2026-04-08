<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BillPayment;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountSubType;
use App\Models\ChartOfAccountType;
use App\Models\CustomField;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Utility;
use App\Models\Transaction;
use App\Models\TransactionLines;
use App\Models\AddTransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('create bank account'))
        {
            $accounts = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->with(['chartAccount'])->get();

            // Calculate balance for each account using the provided query
            $balances = DB::table('add_transaction_lines as t')
                ->join('bank_accounts as b', 't.account_id', '=', 'b.chart_account_id')
                ->where('b.created_by', '=', \Auth::user()->creatorId())
                ->select('t.account_id', 'b.bank_name', DB::raw('SUM(t.credit - t.debit) as balance'))
                ->groupBy('t.account_id')
                ->get()
                ->keyBy('account_id');

            // Attach balance to each account
            foreach ($accounts as $account) {
                $account->current_balance = isset($balances[$account->chart_account_id]) 
                    ? $balances[$account->chart_account_id]->balance 
                    : 0;
            }

            return view('bankAccount.index', compact('accounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create bank account'))
        {
            $accountTypes = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())->get();

            $chartAccounts = [];

            foreach ($accountTypes as $type) {
                $accountTypes = ChartOfAccountSubType::where('type', $type->id)
                    ->where('created_by', '=', \Auth::user()->creatorId())
                    ->whereNotIn('name', ['Accounts Receivable' , 'Accounts Payable'])
                    ->get();

                $temp = [];

                foreach ($accountTypes as $accountType) {
                    $chartOfAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '=', 0)
                        ->where('created_by', '=', \Auth::user()->creatorId())
                        ->get();

                    $incomeSubAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '!=', 0)
                    ->where('created_by', '=', \Auth::user()->creatorId())
                    ->get();

                    $tempData = [
                        'account_name' => $accountType->name,
                        'chart_of_accounts' => [],
                        'subAccounts' => [],
                    ];
                    foreach ($chartOfAccounts as $chartOfAccount) {
                        $tempData['chart_of_accounts'][] = [
                            'id'             => $chartOfAccount->id,
                            'account_number' => $chartOfAccount->account_number,
                            'account_name'   => $chartOfAccount->name,
                        ];
                    }

                    foreach ($incomeSubAccounts as $chartOfAccount) {
                        $tempData['subAccounts'][] = [
                            'id'             => $chartOfAccount->id,
                            'account_number' => $chartOfAccount->account_number,
                            'account_name'   => $chartOfAccount->name,
                            'parent'         => $chartOfAccount->parent,
                            'parent_account' => !empty($chartOfAccount->parentAccount) ? $chartOfAccount->parentAccount->account : 0,
                        ];
                    }
                    $temp[$accountType->id] = $tempData;
                }

                $chartAccounts[$type->name] = $temp;
            }

            $payments = BankAccount::payments();
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'account')->get();

            return view('bankAccount.create', compact('customFields','chartAccounts' , 'payments'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create bank account'))
        {

            $rules = [
                'holder_name' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'payment_name' => ''
            ];

            if ($request->contact_number != null) {
                $rules['contact_number'] = ['regex:/^([0-9\s\-\+\(\)]*)$/'];
            }

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('bank-account.index')->with('error', $messages->first());
            }

            // if (BankAccount::where('payment_name', $request->payment_name)->where('created_by' , \Auth::user()->creatorId())->exists()) {
            //     return redirect()->route('bank-account.index')->with('error', __('This payment name already exists.'));
            // }

            $account                   = new BankAccount();
            $account->chart_account_id = $request->chart_account_id;
            $account->payment_name     = $request->payment_name;
            $account->holder_name      = $request->holder_name;
            $account->bank_name        = $request->bank_name;
            $account->account_number   = $request->account_number;
            $account->opening_balance  = $request->opening_balance ? $request->opening_balance : 0;
            $account->contact_number   = $request->contact_number ? $request->contact_number : '-';
            $account->bank_address     = $request->bank_address ? $request->bank_address : '-';
            $account->created_by       = \Auth::user()->creatorId();
            $account->save();
            CustomField::saveData($account, $request->customField);

            return redirect()->route('bank-account.index')->with('success', __('Account successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show()
    {
        return redirect()->route('bank-account.index');
    }


    public function edit(BankAccount $bankAccount)
    {
        if(\Auth::user()->can('edit bank account'))
        {
            if($bankAccount->created_by == \Auth::user()->creatorId())
            {
                $accountTypes = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())->get();

                $chartAccounts = [];

                foreach ($accountTypes as $type) {
                    $accountTypes = ChartOfAccountSubType::where('type', $type->id)
                        ->where('created_by', '=', \Auth::user()->creatorId())
                        ->whereNotIn('name', ['Accounts Receivable' , 'Accounts Payable'])
                        ->get();

                    $temp = [];

                    foreach ($accountTypes as $accountType) {
                        $chartOfAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '=', 0)
                            ->where('created_by', '=', \Auth::user()->creatorId())
                            ->get();

                        $incomeSubAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '!=', 0)
                        ->where('created_by', '=', \Auth::user()->creatorId())
                        ->get();

                        $tempData = [
                            'account_name'      => $accountType->name,
                            'chart_of_accounts' => [],
                            'subAccounts'       => [],
                        ];
                        foreach ($chartOfAccounts as $chartOfAccount) {
                            $tempData['chart_of_accounts'][] = [
                                'id'             => $chartOfAccount->id,
                                'account_number' => $chartOfAccount->account_number,
                                'account_name'   => $chartOfAccount->name,
                            ];
                        }

                        foreach ($incomeSubAccounts as $chartOfAccount) {
                            $tempData['subAccounts'][] = [
                                'id'             => $chartOfAccount->id,
                                'account_number' => $chartOfAccount->account_number,
                                'account_name'   => $chartOfAccount->name,
                                'parent'         => $chartOfAccount->parent,
                                'parent_account' => !empty($chartOfAccount->parentAccount) ? $chartOfAccount->parentAccount->account : 0,
                            ];
                        }
                        $temp[$accountType->id] = $tempData;
                    }

                    $chartAccounts[$type->name] = $temp;
                }

                $bankAccount->customField = CustomField::getData($bankAccount, 'account');
                $customFields             = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'account')->get();
                $payments = BankAccount::payments();

                return view('bankAccount.edit', compact('bankAccount', 'customFields','chartAccounts' , 'payments'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, BankAccount $bankAccount)
    {
        if(\Auth::user()->can('create bank account'))
        {

            $rules = [
                'holder_name' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'payment_name' => ''
            ];

            if ($request->contact_number != null) {
                $rules['contact_number'] = ['regex:/^([0-9\s\-\+\(\)]*)$/'];
            }

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('bank-account.index')->with('error', $messages->first());
            }

            // if (BankAccount::where('id', '!=', $bankAccount->id)->where('payment_name', $request->payment_name)->where('created_by' , \Auth::user()->creatorId())->exists()) {
            //     return redirect()->route('bank-account.index')->with('error', __('This payment name already exists.'));
            // }

            $bankAccount->chart_account_id = $request->chart_account_id;
            $bankAccount->payment_name     = $request->payment_name;
            $bankAccount->holder_name      = $request->holder_name;
            $bankAccount->bank_name        = $request->bank_name;
            $bankAccount->account_number   = $request->account_number;
            $bankAccount->opening_balance  = $request->opening_balance ? $request->opening_balance : 0;
            $bankAccount->contact_number   = $request->contact_number ? $request->contact_number : '-';
            $bankAccount->bank_address     = $request->bank_address ? $request->bank_address : '-';
            $bankAccount->created_by       = \Auth::user()->creatorId();
            $bankAccount->save();
            CustomField::saveData($bankAccount, $request->customField);

            return redirect()->route('bank-account.index')->with('success', __('Account successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(BankAccount $bankAccount)
    {
        if(\Auth::user()->can('delete bank account'))
        {
            if($bankAccount->created_by == \Auth::user()->creatorId())
            {
                $revenue        = Revenue::where('account_id', $bankAccount->id)->first();
                $invoicePayment = InvoicePayment::where('account_id', $bankAccount->id)->first();
                $transaction    = Transaction::where('account', $bankAccount->id)->first();
                $payment        = Payment::where('account_id', $bankAccount->id)->first();
                $billPayment    = BillPayment::first();

            TransactionLines::where('reference_id', $bankAccount->id)->where('reference', 'Bank Account')->delete();

                if(!empty($revenue) && !empty($invoicePayment) && !empty($transaction) && !empty($payment) && !empty($billPayment))
                {
                    return redirect()->route('bank-account.index')->with('error', __('Please delete related record of this account.'));
                }
                else
                {
                    $bankAccount->delete();

                    return redirect()->route('bank-account.index')->with('success', __('Account successfully deleted.'));
                }

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
