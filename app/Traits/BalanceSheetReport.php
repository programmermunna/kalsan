<?php

namespace App\Traits;

use App\Models\AddTransactionLine;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountParent;
use Illuminate\Support\Facades\DB;

trait BalanceSheetReport
{
    public function buildSubTypeArray($type, $subTypes, $start, $end)
    {
        $subTypeArray = [];
        foreach ($subTypes as $subType) {
            $parentAccounts          = ChartOfAccountParent::where('type', $type->id)->get();
            $totalParentAccountArray = $this->buildParentAccountArray($type, $subType, $parentAccounts, $start, $end);
            $accounts                = $this->getAccountsForSubType($type, $subType, $totalParentAccountArray, $start, $end);
            $totalAccountArray       = $this->buildTotalAccountArray($accounts, $totalParentAccountArray, $subType);
            if ($totalAccountArray != []) {
                $subTypeData['subType'] = ($totalAccountArray != []) ? $subType->name : '';
                $subTypeData['account'] = $totalAccountArray;
                $subTypeArray[]         = ($subTypeData['account'] != [] && $subTypeData['subType'] != []) ? $subTypeData : [];
            }
        }
        return $subTypeArray;
    }

    public function buildParentAccountArray($type, $subType, $parentAccounts, $start, $end)
    {
        $totalParentAccountArray = [];
        if ($parentAccounts->isNotEmpty()) {
            foreach ($parentAccounts as $parentAccount) {
                $parentAccs = $this->getParentAccs($type, $subType, $parentAccount, $start, $end);
                $accounts   = $this->getChildAccounts($type, $subType, $parentAccount, $start, $end);

                if ($parentAccs == [] && $accounts != []) {
                    $parentAccs = $this->getZeroParentAccs($type, $subType, $parentAccount);
                }
                if ($parentAccs != [] && $accounts == []) {
                    $parentAccs = [];
                }

                $parentAccountArray = $this->buildAccountArray($parentAccs, 'parent');
                $parentAccountArray = array_merge($parentAccountArray, $this->buildAccountArray($accounts, 'subAccount'));

                if (!empty($parentAccountArray)) {
                    $parentAccountArrayTotal   = $this->buildParentAccountTotal($parentAccountArray, $parentAccount->account, $parentAccount->name);
                    $totalArray                = array_merge($parentAccountArray, $parentAccountArrayTotal);
                    $totalParentAccountArray[] = $totalArray;
                }
            }
        }
        return $totalParentAccountArray;
    }

    public function getAccountsForSubType($type, $subType, $totalParentAccountArray, $start, $end)
    {
        if ($totalParentAccountArray != []) {
            $accounts = AddTransactionLine::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', DB::raw('sum(debit) as totalDebit'), DB::raw('sum(credit) as totalCredit'));
                        $accounts->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id');
                        $accounts->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');
                        $accounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.name', 'chart_of_account_parents.name');
                        $accounts->where('chart_of_accounts.type', $type->id);
                        $accounts->where('chart_of_accounts.sub_type', $subType->id);
                        $accounts->where('chart_of_account_parents.account');
                        $accounts->where('chart_of_accounts.parent', '=', 'chart_of_account_parents.id');
                        $accounts->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
            if ($start && $end) {
                $accounts->where('add_transaction_lines.date', '>=', $start);
                $accounts->where('add_transaction_lines.date', '<=', $end);
            }
            $accounts->groupBy('account_id');
            return $accounts->get()->toArray();
        } else {
            $accounts = AddTransactionLine::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', DB::raw('sum(debit) as totalDebit'), DB::raw('sum(credit) as totalCredit'));
                        $accounts->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id');
                        $accounts->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');
                        $accounts->where('chart_of_accounts.type', $type->id);
                        $accounts->where('chart_of_accounts.sub_type', $subType->id);
                        $accounts->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
                        if ($start && $end) {
                            $accounts->where('add_transaction_lines.date', '>=', $start);
                            $accounts->where('add_transaction_lines.date', '<=', $end);
                        }
                        $accounts->groupBy('account_id');
            return $accounts->get()->toArray();
        }
    }

    public function buildTotalAccountArray($accounts, $totalParentAccountArray, $subType)
    {
        $totalBalance = 0;
        $creditTotal  = 0;
        $debitTotal   = 0;
        $totalAmount  = 0;
        $accountArray = [];
        foreach ($accounts as $account) {
            $Balance       = $account['totalCredit'] - $account['totalDebit'];
            $totalBalance += $Balance;
            if ($Balance != 0) {
                $data['account_id']    = $account['id'];
                $data['account_code']  = $account['code'];
                $data['account_name']  = $account['name'];
                $data['account']       = '';
                $data['totalCredit']   = 0;
                $data['totalDebit']    = 0;
                $data['netAmount']     = $Balance;
                $accountArray[][]      = $data;
                $creditTotal          += $data['totalCredit'];
                $debitTotal           += $data['totalDebit'];
                $totalAmount          += $data['netAmount'];
            }
        }
        $totalAccountArray = [];
        if ($accountArray != []) {
            $dataTotal['account_id']   = '';
            $dataTotal['account_code'] = '';
            $dataTotal['account']      = '';
            $dataTotal['account_name'] = 'Total ' . $subType->name;
            $dataTotal['totalCredit']  = $creditTotal;
            $dataTotal['totalDebit']   = $debitTotal;
            if (isset($totalParentAccountArray) && $totalParentAccountArray != []) {
                $netAmount = 0;
                foreach ($totalParentAccountArray as $innerArray) {
                    $lastElement  = end($innerArray);
                    $netAmount   += $lastElement['netAmount'];
                }
                $dataTotal['netAmount'] = $netAmount + $totalAmount;
            } else {
                $dataTotal['netAmount'] = $totalAmount;
            }
            $accountArrayTotal[][] = $dataTotal;
            $totalAccountArray     = array_merge($totalParentAccountArray, $accountArray, $accountArrayTotal);
        } elseif ($totalParentAccountArray != []) {
            $dataTotal['account_id']   = '';
            $dataTotal['account_code'] = '';
            $dataTotal['account']      = '';
            $dataTotal['account_name'] = 'Total ' . $subType->name;
            $dataTotal['totalCredit']  = $creditTotal;
            $dataTotal['totalDebit']   = $debitTotal;
            $netAmount                 = 0;
            foreach ($totalParentAccountArray as $innerArray) {
                $lastElement  = end($innerArray);
                $netAmount   += $lastElement['netAmount'];
            }
            $dataTotal['netAmount'] = $netAmount;
            $accountArrayTotal[][]  = $dataTotal;
            $totalAccountArray      = array_merge($totalParentAccountArray, $accountArrayTotal);
        }
        return $totalAccountArray;
    }

    public function buildParentAccountTotal($parentAccountArray, $accountId, $accountName)
    {
        $parentcreditTotal = 0;
        $parenntdebitTotal = 0;
        $parenttotalAmount = 0;
        foreach ($parentAccountArray as $data) {
            $parentcreditTotal += $data['totalCredit'];
            $parenntdebitTotal += $data['totalDebit'];
            $parenttotalAmount += $data['netAmount'];
        }

        $dataTotal = [
            'account_id'   => $accountId,
            'account_code' => '',
            'account'      => 'parentTotal',
            'account_name' => 'Total ' . $accountName,
            'totalCredit'  => $parentcreditTotal,
            'totalDebit'   => $parenntdebitTotal,
            'netAmount'    => $parenttotalAmount,
        ];
        
        return [$dataTotal];
    }

    public function getOtherAccounts($mainTypeIds, $start, $end)
    {
        return AddTransactionLine::select(
                        'chart_of_accounts.id',
                        'chart_of_accounts.code',
                        'chart_of_accounts.name',
            DB::raw('SUM(debit) as totalDebit'),
            DB::raw('SUM(credit) as totalCredit')
                    )
                    ->leftJoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id')
                    ->whereNotIn('chart_of_accounts.type', $mainTypeIds)
                    ->where('add_transaction_lines.created_by', \Auth::user()->creatorId())
                    ->when($start && $end, function ($query) use ($start, $end) {
                        $query->whereBetween('add_transaction_lines.date', [$start, $end]);
                    })
                    ->groupBy('account_id')
                    ->get();
    }

    public function buildAccountArray($accounts, $accountType)
    {
        $accountArray = [];
        $creditTotal  = 0;
        $debitTotal   = 0;
        $totalAmount  = 0;
        foreach ($accounts as $account) {
            $balance = $account['totalCredit'] - $account['totalDebit'];
            if ($balance != 0 || $accountType === 'parent') {
                $data = [
                    'account_id'   => $account['id'],
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account'      => $accountType,
                    'totalCredit'  => 0,
                    'totalDebit'   => 0,
                    'netAmount'    => $balance,
                ];
                $accountArray[]  = $data;
                $creditTotal    += $data['totalCredit'];
                $debitTotal     += $data['totalDebit'];
                $totalAmount    += $data['netAmount'];
            }
        }
        return $accountArray;
    }

    public function getZeroParentAccs($type, $subType, $parentAccount)
    {
        $parentAccs = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', DB::raw('0 as totalDebit'), DB::raw('0 as totalCredit'));
                                $parentAccs->leftjoin('chart_of_account_parents', 'chart_of_accounts.id', 'chart_of_account_parents.account');
                                $parentAccs->where('chart_of_accounts.type', $type->id);
                                $parentAccs->where('chart_of_accounts.sub_type', $subType->id);
                                $parentAccs->where('chart_of_accounts.name', $parentAccount->name);
                                $parentAccs->where('chart_of_accounts.created_by', \Auth::user()->creatorId());
        return $parentAccs->get()->toArray();
    }

    public function getChildAccounts($type, $subType, $parentAccount, $start, $end)
    {
        $accounts = AddTransactionLine::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', DB::raw('sum(debit) as totalDebit'), DB::raw('sum(credit) as totalCredit'));
                            $accounts->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id');
                            $accounts->where('chart_of_accounts.type', $type->id);
                            $accounts->where('chart_of_accounts.sub_type', $subType->id);
                            $accounts->where('chart_of_accounts.parent', $parentAccount->id);
                            $accounts->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
                            if ($start && $end) {
                                $accounts->where('add_transaction_lines.date', '>=', $start);
                                $accounts->where('add_transaction_lines.date', '<=', $end);
                            }
                            $accounts->groupBy('account_id');
        return $accounts->get()->toArray();
    }

    public function getParentAccs($type, $subType, $parentAccount, $start, $end)
    {
        $parentAccs = AddTransactionLine::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', DB::raw('sum(debit) as totalDebit'), DB::raw('sum(credit) as totalCredit'));
                            $parentAccs->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id');
                            $parentAccs->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');
                            $parentAccs->where('chart_of_accounts.type', $type->id);
                            $parentAccs->where('chart_of_accounts.sub_type', $subType->id);
                            $parentAccs->where('chart_of_accounts.name', $parentAccount->name);
                            $parentAccs->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
                            if ($start && $end) {
                                $parentAccs->where('add_transaction_lines.date', '>=', $start);
                                $parentAccs->where('add_transaction_lines.date', '<=', $end);
                            }
                            $parentAccs->groupBy('account_id');
        return $parentAccs->get()->toArray();
    }
}