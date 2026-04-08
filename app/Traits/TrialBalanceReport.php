<?php

namespace App\Traits;

use App\Models\AddTransactionLine;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountParent;
use App\Models\ChartOfAccountType;
use Illuminate\Support\Facades\DB;

trait TrialBalanceReport
{
      /**
     * Get parent account transactions
     */
    private function getParentAccountTransactions($type, $parentAccount, $start, $end)
    {
        $parentAccs = AddTransactionLine::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            DB::raw('sum(debit) as totalDebit'),
            DB::raw('sum(credit) as totalCredit')
        )
        ->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id')
        ->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id')
        ->where('chart_of_accounts.type', $type->id)
        ->where('chart_of_accounts.name', $parentAccount->name)
        ->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
        if ($start && $end) {
            $parentAccs->where('add_transaction_lines.date', '>=', $start)
            ->where('add_transaction_lines.date', '<=', $end);
        }

        return $parentAccs->groupBy('account_id')->get()->toArray();
    }

      /**
     * Get sub account transactions
     */
    private function getSubAccountTransactions($type, $parentAccount, $start, $end)
    {
        $accounts = AddTransactionLine::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            DB::raw('sum(debit) as totalDebit'),
            DB::raw('sum(credit) as totalCredit')
        )
        ->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id')
        ->where('chart_of_accounts.type', $type->id)
        ->where('chart_of_accounts.parent', $parentAccount->id)
        ->where('add_transaction_lines.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $accounts->where('add_transaction_lines.date', '>=', $start)
                    ->where('add_transaction_lines.date', '<=', $end);
        }

        return $accounts->groupBy('account_id')->get()->toArray();
    }

      /**
     * Get empty parent account
     */
    private function getEmptyParentAccount($type, $parentAccount)
    {
        return ChartOfAccount::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            DB::raw('0 as totalDebit'),
            DB::raw('0 as totalCredit')
        )
        ->leftjoin('chart_of_account_parents', 'chart_of_accounts.id', 'chart_of_account_parents.account')
        ->where('chart_of_accounts.type', $type->id)
        ->where('chart_of_accounts.name', $parentAccount->name)
        ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
        ->get()
        ->toArray();
    }

      /**
     * Process account transactions
     */
    private function processAccountTransactions($accounts, $type, $start, $end)
    {
        $query = AddTransactionLine::select(
            'chart_of_accounts.id as account_id',
            'chart_of_accounts.code as account_code',
            'chart_of_accounts.name as account_name',
            DB::raw('sum(debit) as totalDebit'),
            DB::raw('sum(credit) as totalCredit')
        )
        ->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id')
        ->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');

        if (!empty($accounts)) {
            $query->leftjoin('chart_of_account_parents', 'chart_of_accounts.name', 'chart_of_account_parents.name')
                  ->where('chart_of_account_parents.account')
                  ->where('chart_of_accounts.parent', '=', 'chart_of_account_parents.id');
        }

        $query->where('chart_of_accounts.type', $type->id)
              ->where('add_transaction_lines.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $query->where('add_transaction_lines.date', '>=', $start)
                  ->where('add_transaction_lines.date', '<=', $end);
        }

        return $query->groupBy('account_id')->get()->toArray();
    }

      /**
     * Calculate account balance
     */
    private function calculateAccountBalance($credit, $debit)
    {
        return $credit - $debit;
    }

      /**
     * Format account data
     */
    private function formatAccountData($account, $type = '')
    {
        return [
            'account_id'   => $account['id'] ?? $account['account_id'],
            'account_code' => $account['code'] ?? $account['account_code'],
            'account_name' => $account['name'] ?? $account['account_name'],
            'account'      => $type,
            'totalCredit'  => $account['totalCredit'] ?? 0,
            'totalDebit'   => $account['totalDebit'] ?? 0,
        ];
    }

      /**
     * Get account types for trial balance
     */
    private function getAccountTypes()
    {
        return ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->get();
    }

      /**
     * Process account types for trial balance
     */
    private function processAccountTypes($types, $start, $end)
    {
        $totalAccounts = [];
        $totalAccount  = [];

        foreach ($types as $type) {
            $parentAccounts = ChartOfAccountParent::where('type', $type->id)
                ->where('created_by', \Auth::user()->creatorId())
                ->get();
                $totalParentAccountArray = [];
                
                foreach ($parentAccounts as $parentAccount) {
                $parentAccs = $this->getParentAccountTransactions($type, $parentAccount, $start, $end);
                $accounts   = $this->getSubAccountTransactions($type, $parentAccount, $start, $end);

                if (empty($parentAccs) && !empty($accounts)) {
                    $parentAccs = $this->getEmptyParentAccount($type, $parentAccount);
                } elseif (!empty($parentAccs) && empty($accounts)) {
                    $parentAccs = [];
                }

                $parentAccountArray = [];
                $parentcreditTotal  = 0;
                $parenntdebitTotal  = 0;
                foreach ($parentAccs as $account) {
                    $data                  = $this->formatAccountData($account, 'parent');
                    $parentAccountArray[]  = $data;
                    $parentcreditTotal    += $data['totalCredit'];
                    $parenntdebitTotal    += $data['totalDebit'];
                }

                foreach ($accounts as $account) {
                    $balance = $this->calculateAccountBalance($account['totalCredit'], $account['totalDebit']);
                    if ($balance != 0) {
                        $data                  = $this->formatAccountData($account, 'subAccount');
                        $parentAccountArray[]  = $data;
                        $parentcreditTotal    += $data['totalCredit'];
                        $parenntdebitTotal    += $data['totalDebit'];
                    }
                }

                if (!empty($parentAccountArray)) {
                    $dataTotal = [
                        'account_id'   => $parentAccount->account,
                        'account_code' => '',
                        'account'      => 'parentTotal',
                        'account_name' => 'Total ' . $parentAccount->name,
                        'totalCredit'  => $parentcreditTotal,
                        'totalDebit'   => $parenntdebitTotal,
                    ];
                    $parentAccountArray[]      = $dataTotal;
                    $totalParentAccountArray[] = $parentAccountArray;
                }
            }
            $accounts = $this->processAccountTransactions($totalParentAccountArray, $type, $start, $end);
            $name = $type->name;
            if (isset($totalAccount[$name])) {
                $totalAccount[$name]["totalCredit"] += $accounts["totalCredit"];
                $totalAccount[$name]["totalDebit"]  += $accounts["totalDebit"];
            } else {
                $totalAccount[$name] = $accounts;
            }

            if (!empty($totalParentAccountArray)) {
                $totalAccount[$name] = array_merge_recursive($totalAccount[$name], $totalParentAccountArray[0]);
            }
        }
        foreach ($totalAccount as $category => $entries) {
            foreach ($entries as $entry) {
                $name = $entry['account_name'];
                if (!isset($totalAccounts[$category][$name])) {
                    $totalAccounts[$category][$name] = [
                        'account_id'   => $entry['account_id'],
                        'account_code' => $entry['account_code'],
                        'account_name' => $name,
                        'account'      => isset($entry['account']) ? $entry['account'] : '',
                        'totalDebit'   => 0,
                        'totalCredit'  => 0,
                    ];
                }

                $diff = $entry['totalCredit'] - $entry['totalDebit'];
                if ($diff < 0) {
                    $totalAccounts[$category][$name]['totalDebit']  += -$diff;
                    $totalAccounts[$category][$name]['totalCredit']  = 0;
                } else {
                    $totalAccounts[$category][$name]['totalDebit']   = 0;
                    $totalAccounts[$category][$name]['totalCredit'] += $diff;
                }
            }
        }
        return $totalAccounts;
    }
} 