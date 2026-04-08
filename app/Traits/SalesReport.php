<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;

trait SalesReport
{
 public function getInvoiceItems($start, $end, $userId = null, $venderId = null, $customerId = null)
{
    // Subquery for paid amounts (per invoice)
    $paidAmounts = DB::table('invoice_payments')
        ->select('invoice_id')
        ->selectRaw('COALESCE(SUM(amount), 0) as paid')
        ->groupBy('invoice_id');

    // Subquery for credit notes (per invoice)
    $creditNotes = DB::table('credit_notes')
        ->select('invoice')
        ->selectRaw('COALESCE(SUM(amount), 0) as credit_note_amount')
        ->groupBy('invoice');

    // Subquery for invoice product totals (aggregated by invoice)
    $invoiceProductTotals = DB::table('invoice_products')
        ->select('invoice_id')
        ->selectRaw('GROUP_CONCAT(ticket_number SEPARATOR ", ") as ticket_numbers')
        ->selectRaw('GROUP_CONCAT(DISTINCT destination SEPARATOR ", ") as destinations')
        ->selectRaw('SUM(fare) as total_fare')
        ->selectRaw('SUM(tax) as total_tax')
        ->selectRaw('SUM(refund) as total_refund')
        ->selectRaw('SUM(commission) as total_commission')
        ->selectRaw('SUM(cust_commission) as total_cust_commission')
        ->selectRaw('SUM(discount) as total_discount')
        ->selectRaw('SUM(price) as total_price')
        ->groupBy('invoice_id');

    $invoiceItems = DB::table('invoices')
        ->select(
            'invoices.id as invoice_id',
            'invoices.origin',
            'invoices.destination as invoice_destination',
            'invoices.issue_date',
            'invoices.pnr',
            'invoices.status',
            'customers.id as customer_id',
            'customers.name as customer_name',
            'users.id as user_id',
            'users.name as user_name',
            'venders.id as vender_id',
            'venders.name as vender_name',
            'invoice_totals.ticket_numbers',
            'invoice_totals.destinations',
            DB::raw('COALESCE(invoice_totals.total_fare, 0) as invoice_total_fare'),
            DB::raw('COALESCE(invoice_totals.total_tax, 0) as invoice_total_tax'),
            DB::raw('COALESCE(invoice_totals.total_refund, 0) as invoice_total_refund'),
            DB::raw('COALESCE(invoice_totals.total_commission, 0) as invoice_total_commission'),
            DB::raw('COALESCE(invoice_totals.total_cust_commission, 0) as invoice_total_cust_commission'),
            DB::raw('COALESCE(invoice_totals.total_discount, 0) as invoice_total_discount'),
            DB::raw('COALESCE(invoice_totals.total_price, 0) as invoice_total'),
            DB::raw('COALESCE(paid_amounts.paid, 0) as paid'),
            DB::raw('COALESCE(credit_notes.credit_note_amount, 0) as credit_note_amount')
        )
        ->leftJoin('customers', 'customers.id', '=', 'invoices.customer_id')
        ->leftJoin('users', 'invoices.created_by', '=', 'users.id')
        ->leftJoin('venders', 'invoices.vender_id', '=', 'venders.id')
        ->leftJoinSub($invoiceProductTotals, 'invoice_totals', function($join) {
            $join->on('invoice_totals.invoice_id', '=', 'invoices.id');
        })
        ->leftJoinSub($paidAmounts, 'paid_amounts', function($join) {
            $join->on('paid_amounts.invoice_id', '=', 'invoices.id');
        })
        ->leftJoinSub($creditNotes, 'credit_notes', function($join) {
            $join->on('credit_notes.invoice', '=', 'invoices.id');
        })
        //->where('invoices.created_by', \Auth::user()->creatorId())
        ->where('invoices.status', '!=', 'draft');
        //->whereNotNull('invoice_totals.invoice_id'); // Only show invoices with products

    // Apply date filter
    if ($start && $end) {
        $invoiceItems->whereBetween('invoices.issue_date', [$start, $end]);
    }

    // Apply user filter
    if ($userId) {
        $invoiceItems->where('invoices.created_by', $userId);
    }

    // Apply vender filter - FIXED: use $venderId parameter, not undefined $vendorId
    if ($venderId) {
        $invoiceItems->where('invoices.vender_id', $venderId);
    }

    // Apply customer filter
    if ($customerId) {
        $invoiceItems->where('invoices.customer_id', $customerId);
    }

    $invoiceItems->orderBy('invoices.issue_date', 'desc');

    // Get results as a collection of objects
    $results = $invoiceItems->get();

    // Convert to array of arrays for consistent handling
    $resultsArray = [];

    foreach ($results as $item) {
        // Convert stdClass to array
        $itemArray = (array) $item;

        // Calculate balance for this invoice
        $totalAmount = $itemArray['invoice_total'] ?? 0;
        $paid = $itemArray['paid'] ?? 0;
        $creditNote = $itemArray['credit_note_amount'] ?? 0;

        // Balance = total_amount - paid - credit_notes
        $itemArray['balance'] = max($totalAmount - $paid - $creditNote, 0);

        // Format ticket numbers for display
        $itemArray['ticket_number_display'] = $itemArray['ticket_numbers'] ?? '-';

        // Format origin-destination
        $origin = $itemArray['origin'] ?? '';
        $destination = $itemArray['invoice_destination'] ?? '';

        if (!empty($origin) && !empty($destination)) {
            $itemArray['route_display'] = $origin . ' - ' . $destination;
        } elseif (!empty($origin)) {
            $itemArray['route_display'] = $origin;
        } elseif (!empty($destination)) {
            $itemArray['route_display'] = $destination;
        } else {
            $itemArray['route_display'] = '-';
        }

        $resultsArray[] = $itemArray;
    }

    return $resultsArray;
}
   public function getInvoiceCustomers($start, $end, $userId = null, $venderId = null, $customerId = null)
{
    $invoices = Invoice::select(
        'customers.name',
        DB::raw('count(DISTINCT invoices.id) as invoice_count'),
        DB::raw('sum(invoice_products.price) as price'),
        DB::raw('sum(invoice_products.fare) as total_fare'),
        DB::raw('sum(invoice_products.tax) as total_tax'),
        DB::raw('sum(invoice_products.refund) as total_refund'),
        DB::raw('sum(invoice_products.commission) as total_commission'),
        DB::raw('sum(invoice_products.cust_commission) as total_cust_commission'),
        DB::raw('sum(invoice_products.discount) as total_discount')
    )
    ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
    ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
    ->where('invoices.created_by', \Auth::user()->creatorId())
    ->where('invoices.status', '!=', 'draft');

    // Apply date filter
    if ($start && $end) {
        $invoices->whereBetween('invoices.issue_date', [$start, $end]);
    }

    // Apply user filter
    if ($userId) {
        $invoices->where('invoices.created_by', $userId);
    }

    // Apply vender filter
    if ($venderId) {
        $invoices->where('invoices.vender_id', $venderId);
    }

    // Apply customer filter
    if ($customerId) {
        $invoices->where('invoices.customer_id', $customerId);
    }

    $invoices = $invoices->groupBy('customers.id', 'customers.name')
                ->get()
                ->toArray();

    return $invoices;
}

   public function getInvoiceItemsTotals($start, $end, $userId = null, $venderId = null, $customerId = null)
{
    // Base query for invoice products with proper joins
    $baseQuery = DB::table('invoice_products')
        ->leftJoin('invoices', 'invoice_products.invoice_id', '=', 'invoices.id')
        ->where('invoices.created_by', \Auth::user()->creatorId())
        ->where('invoices.status', '!=', 'draft');

    // Apply filters
    if ($start && $end) {
        $baseQuery->whereBetween('invoices.issue_date', [$start, $end]);
    }
    if ($userId) {
        $baseQuery->where('invoices.created_by', $userId);
    }
    if ($venderId) {
        $baseQuery->where('invoices.vender_id', $venderId);
    }
    if ($customerId) {
        $baseQuery->where('invoices.customer_id', $customerId);
    }

    // Get totals from invoice_products
    $productTotals = (clone $baseQuery)
        ->selectRaw('COALESCE(SUM(invoice_products.fare), 0) as total_fare')
        ->selectRaw('COALESCE(SUM(invoice_products.tax), 0) as total_tax')
        ->selectRaw('COALESCE(SUM(invoice_products.refund), 0) as total_refund')
        ->selectRaw('COALESCE(SUM(invoice_products.commission), 0) as total_commission')
        ->selectRaw('COALESCE(SUM(invoice_products.cust_commission), 0) as total_cust_commission')
        ->selectRaw('COALESCE(SUM(invoice_products.discount), 0) as total_discount')
        ->selectRaw('COALESCE(SUM(invoice_products.price), 0) as total_amount1')
        ->first();

    // Get total paid amounts
    $paidQuery = DB::table('invoice_payments')
        ->leftJoin('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')
        ->where('invoices.created_by', \Auth::user()->creatorId())
        ->where('invoices.status', '!=', 'draft');

    if ($start && $end) {
        $paidQuery->whereBetween('invoices.issue_date', [$start, $end]);
    }
    if ($userId) {
        $paidQuery->where('invoices.created_by', $userId);
    }
    if ($venderId) {
        $paidQuery->where('invoices.vender_id', $venderId);
    }
    if ($customerId) {
        $paidQuery->where('invoices.customer_id', $customerId);
    }

    $totalPaidAmount = $paidQuery->sum('invoice_payments.amount') ?? 0;

    // Get total credit notes
    $creditQuery = DB::table('credit_notes')
        ->leftJoin('invoices', 'credit_notes.invoice', '=', 'invoices.id')
        ->where('invoices.created_by', \Auth::user()->creatorId())
        ->where('invoices.status', '!=', 'draft');

    if ($start && $end) {
        $creditQuery->whereBetween('invoices.issue_date', [$start, $end]);
    }
    if ($userId) {
        $creditQuery->where('invoices.created_by', $userId);
    }
    if ($venderId) {
        $creditQuery->where('invoices.vender_id', $venderId);
    }
    if ($customerId) {
        $creditQuery->where('invoices.customer_id', $customerId);
    }

    $totalCreditNoteAmount = $creditQuery->sum('credit_notes.amount') ?? 0;

    // Calculate balance
    $totalBalance = max(($productTotals->total_amount ?? 0) - $totalPaidAmount - $totalCreditNoteAmount, 0);

    return [
        'total_fare' => $productTotals->total_fare ?? 0,
        'total_tax' => $productTotals->total_tax ?? 0,
        'total_refund' => $productTotals->total_refund ?? 0,
        'total_commission' => $productTotals->total_commission ?? 0,
        'total_cust_commission' => $productTotals->total_cust_commission ?? 0,
        'total_discount' => $productTotals->total_discount ?? 0,
        'total_amount' => $productTotals->total_amount2 ?? 0,
        'total_paid' => $totalPaidAmount,
        'total_balance' => $totalBalance
    ];
}
}
