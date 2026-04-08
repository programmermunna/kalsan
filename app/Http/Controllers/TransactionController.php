<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Models\BankAccount;
use App\Models\ProductServiceCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\AddTransactionLine;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{

   public function index(Request $request)
{
    if(\Auth::user()->can('manage transaction'))
    {
        $filter['account']  = __('All');
        $filter['category'] = __('All');

        $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('bank_name', 'id');
        $account->prepend('Select Account', '');

        // Build the accounts query without executing it yet
        $accountsQuery = AddTransactionLine::select(
                'chart_of_accounts.name',
                DB::raw('SUM(add_transaction_lines.credit) as cr'),
                DB::raw('SUM(add_transaction_lines.debit) as dr'),
                DB::raw('SUM(add_transaction_lines.debit) - SUM(add_transaction_lines.credit) as balance')
            )
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'add_transaction_lines.account_id')
            ->where('add_transaction_lines.bank_id', 1)
            ->groupBy('add_transaction_lines.account_id', 'chart_of_accounts.name');

        $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())
            ->whereIn('type', [1, 2])
            ->get()->pluck('name', 'name');

        $category->prepend('Invoice', 'Invoice');
        $category->prepend('Bill', 'Bill');
        $category->prepend('Select Category', '');

        // Build the transactions query without executing it yet
        $transactionsQuery = Transaction::orderBy('id', 'desc');

        if(!empty($request->start_month) && !empty($request->end_month))
        {
            $start = strtotime($request->start_month);
            $end   = strtotime($request->end_month);
        }
        else
        {
            $start = strtotime(date('Y-m'));
            $end   = strtotime(date('Y-m', strtotime("-5 month")));
        }

        $currentdate = $start;

        while($currentdate <= $end)
        {
            $data['month'] = date('m', $currentdate);
            $data['year']  = date('Y', $currentdate);

            $transactionsQuery->orWhere(function ($query) use ($data) {
                $query->whereMonth('date', $data['month'])
                      ->whereYear('date', $data['year'])
                      ->where('transactions.created_by', '=', \Auth::user()->creatorId());
            });

            // Remove the accountsQuery filtering here since it doesn't have a date field
            // If you need to filter accounts by date, you'll need to join with transactions

            $currentdate = strtotime('+1 month', $currentdate);
        }

        $filter['startDateRange'] = date('M-Y', $start);
        $filter['endDateRange']   = date('M-Y', $end);

        // Apply filters to transactionsQuery
        if(!empty($request->account))
        {
            $transactionsQuery->where('account', $request->account);

            if($request->account == 'strip-paypal')
            {
                // You can't filter accountsQuery by account=0 directly
                $filter['account'] = __('Stripe / Paypal');
            }
            else
            {
                $bankAccount = BankAccount::find($request->account);
                $filter['account'] = !empty($bankAccount) ? $bankAccount->holder_name . ' - ' . $bankAccount->bank_name : '';
                if($bankAccount && $bankAccount->holder_name == 'Cash')
                {
                    $filter['account'] = 'Cash';
                }
            }
        }

        if(!empty($request->category))
        {
            $transactionsQuery->where('category', $request->category);
            $filter['category'] = $request->category;
        }

        // Apply created_by filter to both queries
        $transactionsQuery->where('created_by', '=', \Auth::user()->creatorId());

        // Execute both queries
        $transactions = $transactionsQuery->with(['bankAccount'])->get();
         $accounts = $accountsQuery->get();

        return view('transaction.index', compact('transactions', 'account', 'category', 'filter', 'accounts','accountsQuery'));
    }
    else
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }
}
    //for export in transaction report
    public function export()
    {
        $name = 'transaction_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new TransactionExport(), $name . '.xlsx');

        return $data;
    }


}
