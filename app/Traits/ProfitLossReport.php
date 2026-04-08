<?php

namespace App\Traits;

use App\Models\AddTransactionLine;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountParent;
use Illuminate\Support\Facades\DB;

trait ProfitLossReport
{
      /**
     * Process profit loss report data
     */
    private function processProfitLossData($types, $start, $end)
    {
        $subTypeArray  = [];
        $totalAccounts = [];

        foreach ($types as $type) {
            $totalParentAccountArray = $this->getProfitLossParentAccountsData($type, $start, $end);
            $accounts                = $this->getProfitLossOtherAccountsData($type, $start, $end, $totalParentAccountArray);
            $accountData       = $this->processAccountData($accounts);
            $totalAccountArray = $this->buildProfitLossTotalAccountArray($accountData, $totalParentAccountArray, $type);
            
            if (!empty($totalAccountArray)) {
                $subTypeArray[] = $this->buildSubTypeData($type, $totalAccountArray);
            }
            $totalAccounts = $subTypeArray;
        }
        return $totalAccounts;
    }

      /**
     * Process account data
     */
    private function processAccountData($accounts)
    {
        $totalBalance = 0;
        $creditTotal  = 0;
        $debitTotal   = 0;
        $totalAmount  = 0;
        $accountArray = [];

        foreach ($accounts as $account) {
            $balance       = $account['totalCredit'] - $account['totalDebit'];
            $totalBalance += $balance;

            if ($balance != 0) {
                $data = [
                    'account_id'   => $account['id'],
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account'      => '',
                    'totalCredit'  => 0,
                    'totalDebit'   => 0,
                    'netAmount'    => $balance,
                ];
                $accountArray[][]  = $data;
                $creditTotal      += $data['totalCredit'];
                $debitTotal       += $data['totalDebit'];
                $totalAmount      += $data['netAmount'];
            }
        }

        return [
            'accountArray' => $accountArray,
            'creditTotal'  => $creditTotal,
            'debitTotal'   => $debitTotal,
            'totalAmount'  => $totalAmount
        ];
    }

      /**
     * Build total account array for profit loss
     */
    private function buildProfitLossTotalAccountArray($accountData, $totalParentAccountArray, $type)
    {
        $totalAccountArray = [];
            $dataTotal = [
                'account_id'   => '',
                'account_code' => '',
                'account'      => '',
                'account_name' => 'Total ' . $type->name,
                'totalCredit'  => $accountData['creditTotal'],
                'totalDebit'   => $accountData['debitTotal'],
                'netAmount'    => $accountData['totalAmount'],
            ];
            $accountData['accountArray'][] = [$dataTotal];
            $totalAccountArray             = array_merge($totalParentAccountArray, $accountData['accountArray']);
        if (!empty($totalParentAccountArray)) {
            // $dataTotal = [
            //     'account_id'   => '',
            //     'account_code' => '',
            //     'account'      => '',
            //     'account_name' => 'Total ' . $type->name,
            //     'totalCredit'  => $accountData['creditTotal'],
            //     'totalDebit'   => $accountData['debitTotal'],
            //     'netAmount'    => 0,
            // ];

            foreach ($totalParentAccountArray as $innerArray) {
                $lastElement             = end($innerArray);
                $dataTotal['netAmount'] += $lastElement['netAmount'];
            }
            $accountArrayTotal[][] = $dataTotal;
            $totalAccountArray     = array_merge($totalAccountArray, $accountArrayTotal);
        }
        return $totalAccountArray;
    }

      /**
     * Build sub type data
     */
    private function buildSubTypeData($type, $totalAccountArray)
    {
        return [
            'Type'    => $type->name,
            'account' => $totalAccountArray
        ];
    }

      /**
     * Get parent accounts data for profit loss report
     */
    private function getProfitLossParentAccountsData($type, $start, $end)
    {
        $parentAccounts = ChartOfAccountParent::where('type', $type->id)
            ->where('created_by', \Auth::user()->creatorId())
            ->get();

        $totalParentAccountArray = [];
        
        if ($parentAccounts->isNotEmpty()) {
            foreach ($parentAccounts as $parentAccount) {
                $parentAccountData = $this->processProfitLossParentAccount($parentAccount, $type, $start, $end);
                if (!empty($parentAccountData)) {
                    $totalParentAccountArray[] = $parentAccountData;
                }
            }
        }
        return $totalParentAccountArray;
    }

      /**
     * Process individual parent account data for profit loss
     */
    private function processProfitLossParentAccount($parentAccount, $type, $start, $end)
    {
        $parentAccs = $this->getProfitLossParentAccountTransactions($parentAccount, $type, $start, $end);
        $accounts   = $this->getProfitLossSubAccountTransactions($parentAccount, $type, $start, $end);

        if (empty($parentAccs) && !empty($accounts)) {
            $parentAccs = $this->getProfitLossEmptyParentAccountData($parentAccount, $type);
        }

        if (!empty($parentAccs) && empty($accounts)) {
            return [];
        }

        $parentAccountArray      = [];
        $parentAccountArrayTotal = [];
        $parenttotalBalance      = 0;
        $parentcreditTotal       = 0;
        $parenntdebitTotal       = 0;
        $parenttotalAmount       = 0;

          // Process parent accounts
        foreach ($parentAccs as $account) {
            $balance             = $account['totalCredit'] - $account['totalDebit'];
            $parenttotalBalance += $balance;

            $data = [
                'account_id'   => $account['id'],
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'account'      => 'parent',
                'totalCredit'  => 0,
                'totalDebit'   => 0,
                'netAmount'    => $balance,
            ];

            $parentAccountArray[]  = $data;
            $parentcreditTotal    += $data['totalCredit'];
            $parenntdebitTotal    += $data['totalDebit'];
            $parenttotalAmount    += $data['netAmount'];
        }

          // Process sub accounts
        foreach ($accounts as $account) {
            $balance             = $account['totalCredit'] - $account['totalDebit'];
            $parenttotalBalance += $balance;

            if ($balance != 0) {
                $data = [
                    'account_id'   => $account['id'],
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account'      => 'subAccount',
                    'totalCredit'  => 0,
                    'totalDebit'   => 0,
                    'netAmount'    => $balance,
                ];

                $parentAccountArray[]  = $data;
                $parentcreditTotal    += $data['totalCredit'];
                $parenntdebitTotal    += $data['totalDebit'];
                $parenttotalAmount    += $data['netAmount'];
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
                'netAmount'    => $parenttotalAmount,
            ];

            $parentAccountArrayTotal[] = $dataTotal;
            return array_merge($parentAccountArray, $parentAccountArrayTotal);
        }

        return [];
    }

      /**
     * Get parent account transactions for profit loss
     */
    private function getProfitLossParentAccountTransactions($parentAccount, $type, $start, $end)
    {
        $query = AddTransactionLine::select(
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
            $query->where('add_transaction_lines.date', '>=', $start)
                  ->where('add_transaction_lines.date', '<=', $end);
        }

        return $query->groupBy('account_id')->get()->toArray();
    }

      /**
     * Get sub account transactions for profit loss
     */
    private function getProfitLossSubAccountTransactions($parentAccount, $type, $start, $end)
    {
        $query = AddTransactionLine::select(
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
            $query->where('add_transaction_lines.date', '>=', $start)
                  ->where('add_transaction_lines.date', '<=', $end);
        }

        return $query->groupBy('account_id')->get()->toArray();
    }

      /**
     * Get empty parent account data for profit loss
     */
    private function getProfitLossEmptyParentAccountData($parentAccount, $type)
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
     * Get other accounts data for profit loss
     */
    private function getProfitLossOtherAccountsData($type, $start, $end, $totalParentAccountArray)
    {
        $query = AddTransactionLine::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            DB::raw('sum(debit) as totalDebit'),
            DB::raw('sum(credit) as totalCredit')
        )
        ->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id')
        ->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');

        if (!empty($totalParentAccountArray)) {
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
} 