<?php

namespace App\Traits;

use App\Models\Bill;
use App\Models\DebitNote;
use App\Models\Vender;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait PayablesReport
{
    private function getVendor()
    {
        return  Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
    }

private function getPayableVendors($start = null, $end = null)
{
    // Use the new query for transaction lines
    $transactionResults = DB::table('add_transaction_lines as ad')
        ->select('venders.name',
            DB::raw('COALESCE(SUM(ad.credit), 0) as cr'),
            DB::raw('COALESCE(SUM(ad.debit), 0) as dr'))
        ->join('venders', 'ad.vender_id', '=', 'venders.id')
        ->where('venders.created_by', \Auth::user()->creatorId());

    if ($start && $end) {
        // Assuming there's a date field in add_transaction_lines
        $transactionResults->whereBetween('ad.date', [$start, $end]);
    }

    $transactionResults->groupBy('ad.vender_id', 'venders.name');
    $results = $transactionResults->get();

    // Transform to the expected structure
    $payableVendors = [];
    foreach ($results as $index => $result) {
        $payableVendors[] = [
            'name' => $result->name,
            'cr' => floatval($result->cr),
            'dr' => floatval($result->dr),
            'balance' => floatval($result->cr - $result->dr),
        ];
    }

    return $payableVendors;
}

    private function getPayableSummaries($start = null, $end = null)
    {
        $billSummaries = $this->getBillSummaries($start, $end);
        $debitSummaries = $this->getDebitSummaries($start, $end);

        return array_merge($debitSummaries, $billSummaries);
    }

    private function getBillSummaries($start = null, $end = null)
    {
        $payableSummariesBill = Bill::select('venders.name')
        ->selectRaw('(bills.bill_id) as bill')
        ->selectRaw('sum((bill_products.price * bill_products.quantity) - bill_products.discount) as price')
        ->selectRaw('sum((bill_payments.amount)) as pay_price')
        ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
           LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
            WHERE bill_products.bill_id = bills.id) as total_tax')
        ->selectRaw('bills.bill_date as bill_date')
        ->selectRaw('bills.status as status')
        ->leftJoin('venders', 'venders.id', 'bills.vender_id')
        ->leftJoin('bill_payments', 'bill_payments.bill_id', 'bills.id')
        ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id');
        $payableSummariesBill->where('bills.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $payableSummariesBill->whereBetween('bills.bill_date', [$start, $end]);
        }
        $payableSummariesBill->groupBy('bills.id');
        $payableSummariesBill = $payableSummariesBill->get()->toArray();

        return $payableSummariesBill;
    }

    private function getDebitSummaries($start = null, $end = null)
    {
        $payableSummariesDebit = DebitNote::select('venders.name')
            ->selectRaw('null as bill')
            ->selectRaw('debit_notes.amount as price')
            ->selectRaw('0 as pay_price')
            ->selectRaw('0 as total_tax')
            ->selectRaw('debit_notes.date as bill_date')
            ->selectRaw('5 as status')
            ->leftJoin('venders', 'venders.id', 'debit_notes.vendor')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
            ->leftJoin('bills', 'bills.id', 'debit_notes.bill');

        if ($start && $end) {
            $payableSummariesDebit->whereBetween('debit_notes.date', [$start, $end]);
        }
        $payableSummariesDebit->where('bills.created_by', \Auth::user()->creatorId());
        $payableSummariesDebit->groupBy('debit_notes.id');
        $payableSummariesDebit = $payableSummariesDebit->get()->toArray();

        return $payableSummariesDebit;
    }

    private function getPayableDetails($start = null, $end = null)
    {
        $billDetails = $this->getBillDetails($start, $end);
        $debitDetails = $this->getDebitDetails($start, $end);

        return array_merge($debitDetails, $billDetails);
    }

    private function getBillDetails($start = null, $end = null)
    {
        $payableDetailsBill = Bill::select('venders.name')
        ->selectRaw('(bills.bill_id) as bill')
        ->selectRaw('sum(bill_products.price) as price')
        ->selectRaw('(bill_products.quantity) as quantity')
        ->selectRaw('(product_services.name) as product_name')
        ->selectRaw('bills.bill_date as bill_date')
        ->selectRaw('bills.status as status')
        ->leftJoin('venders', 'venders.id', 'bills.vender_id')
        ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
        ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id');
        $payableDetailsBill->where('bills.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $payableDetailsBill->whereBetween('bills.bill_date', [$start, $end]);
        }
        $payableDetailsBill->groupBy('bills.bill_id', 'product_services.name');
        $payableDetailsBill = $payableDetailsBill->get()->toArray();

        return $payableDetailsBill;
    }

    private function getDebitDetails($start = null, $end = null)
    {
        $payableDetailsDebit = DebitNote::select('venders.name')
        ->selectRaw('null as bill')
        ->selectRaw('(debit_notes.id) as bills')
        ->selectRaw('(debit_notes.amount) as price')
        ->selectRaw('(product_services.name) as product_name')
        ->selectRaw('debit_notes.date as bill_date')
        ->selectRaw('5 as status')
        ->leftJoin('venders', 'venders.id', 'debit_notes.vendor')
        ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
        ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id')
        ->leftJoin('bills', 'bills.id', 'debit_notes.bill');
        $payableDetailsDebit->where('bills.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $payableDetailsDebit->whereBetween('debit_notes.date', [$start, $end]);
        }
        $payableDetailsDebit->groupBy('debit_notes.id', 'product_services.name');
        $payableDetailsDebit = $payableDetailsDebit->get()->toArray();

        return $payableDetailsDebit;
    }

    protected function getBills($start = null, $end = null)
    {
        $bills = Bill::select([
            'venders.name as name',
            'bills.due_date',
            'bills.status',
            'bills.id as bill_id',

            DB::raw('(SELECT SUM(price * quantity - discount)
                      FROM bill_products
                      WHERE bill_products.bill_id = bills.id) as price'),

            DB::raw('(SELECT SUM(amount)
                      FROM bill_payments
                      WHERE bill_payments.bill_id = bills.id) as pay_price'),

            DB::raw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100))
                      FROM bill_products
                      LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
                      WHERE bill_products.bill_id = bills.id) as total_tax'),

            DB::raw('(SELECT SUM(amount)
                      FROM debit_notes
                      WHERE debit_notes.bill = bills.id) as debit_price'),
        ])
        ->leftJoin('venders', 'venders.id', '=', 'bills.vender_id')
        ->where('bills.created_by', \Auth::user()->creatorId());

        if ($start && $end) {
            $bills->whereBetween('bills.bill_date', [$start, $end]);
        }

        $bills = $bills->get();
        return $bills;
    }

    private function getPayableAgingSummaries($start = null, $end = null)
    {
        $agingSummaries = [];
        $bills = $this->getBills($start, $end);

        $today = date("Y-m-d");
        foreach ($bills as $bill) {

            $name    = $bill->name;
            $price   = floatval(($bill->price + $bill->total_tax) - ($bill->pay_price + $bill->debit_price));
            $dueDate = $bill->due_date;

            if (!isset($agingSummaries[$name])) {
                $agingSummaries[$name] = [
                    'current'              => 0.0,
                    "1_15_days"            => 0.0,
                    "16_30_days"           => 0.0,
                    "31_45_days"           => 0.0,
                    "greater_than_45_days" => 0.0,
                    "total_due"            => 0.0,
                ];
            }

            $daysDifference = date_diff(date_create($dueDate), date_create($today));
            $daysDifference = $daysDifference->format("%R%a");

            if ($daysDifference <= 0) {
                $agingSummaries[$name]["current"] += $price;
            } elseif ($daysDifference >= 1 && $daysDifference <= 15) {
                $agingSummaries[$name]["1_15_days"] += $price;
            } elseif ($daysDifference >= 16 && $daysDifference <= 30) {
                $agingSummaries[$name]["16_30_days"] += $price;
            } elseif ($daysDifference >= 31 && $daysDifference <= 45) {
                $agingSummaries[$name]["31_45_days"] += $price;
            } elseif ($daysDifference > 45) {
                $agingSummaries[$name]["greater_than_45_days"] += $price;
            }

            $agingSummaries[$name]["total_due"] += $price;

        }

        return $agingSummaries;
    }

    protected function getPayableAgingDetails($start = null, $end = null)
    {
        $bills = $this->getBills($start, $end);
        $agingDetails = [
            'currents' => [],
            'days1to15' => [],
            'days16to30' => [],
            'days31to45' => [],
            'moreThan45' => []
        ];

        foreach ($bills as $bill) {
            $totalPrice     = $bill->price + $bill->total_tax;
            $balanceDue     = floatval(($bill->price + $bill->total_tax) - ($bill->pay_price + $bill->debit_price));
            $dueDate = Carbon::parse($bill->due_date);
            $today = Carbon::now();
            $daysDifference = $dueDate->diffInDays($today);

            $item = [
                'bill_id' => $bill->bill_id,
                'due_date' => $bill->due_date,
                'total_price' => $totalPrice,
                'balance_due' => $balanceDue,
                'age' => intval(str_replace(['+', '-'], '', $daysDifference)),
                'status' => $bill->status,
                'name'=>$bill->name
            ];

            if ($daysDifference <= 0) {
                $agingDetails['currents'][] = $item;
            } elseif ($daysDifference <= 15) {
                $agingDetails['days1to15'][] = $item;
            } elseif ($daysDifference <= 30) {
                $agingDetails['days16to30'][] = $item;
            } elseif ($daysDifference <= 45) {
                $agingDetails['days31to45'][] = $item;
            } else {
                $agingDetails['moreThan45'][] = $item;
            }
        }

        return $agingDetails;
    }
}
