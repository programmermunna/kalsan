<?php

namespace App\Http\Controllers;

use App\Models\AddTransactionLine;
use App\Models\ChartOfAccount;
use App\Models\CreditNote;
use App\Models\CustomerCreditNotes;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductService;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerCreditNotesController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage credit note'))
        {
            $customcreditNotes = CustomerCreditNotes::whereHas('invoices', function ($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })->with(['invoices'])->get();

            return view('customerCreditNote.index', compact('customcreditNotes'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create credit note'))
        {
            $invoices = Invoice::where('status', '!=' , 0)->where('created_by', \Auth::user()->creatorId())->get()->pluck('invoice_id', 'id');

            $statues = CustomerCreditNotes::$statues;
            return view('customerCreditNote.create', compact('invoices','statues'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create credit note'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'invoice' => 'required|numeric',
                                   'amount' => 'required|numeric|gt:0',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoice_id = $request->invoice;
            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            $creditAmount = floatval($request->amount);

            if($invoiceDue){

                $invoicePaid = $invoiceDue->getTotal() - $invoiceDue->getDue() - $invoiceDue->invoiceTotalCreditNote();

                $customerCreditNotes = CustomerCreditNotes::where('invoice',$invoice_id)->get()->sum('amount');

                if($creditAmount > $invoicePaid || ($customerCreditNotes + $creditAmount)  > $invoicePaid)
                {
                    return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoicePaid-$customerCreditNotes) . ' credit limit of this invoice.');
                }
                    $credit                  = new CustomerCreditNotes();
                    $credit->credit_id       = $this->creditNoteNumber();
                    $credit->invoice         = $invoice_id;
                    $credit->invoice_product = $request->invoice_product;
                    $credit->date            = $request->date;
                    $credit->amount          = $creditAmount;
                    $credit->status          = 1;
                    $credit->description     = $request->description;
                    $credit->save();

                    $invoiceProduct = InvoiceProduct::find($credit->invoice_product);
                    $product        = ProductService::find($invoiceProduct->product_id);

                    $data = [
                        'account_id'         => !empty($product->sale_chartaccount_id) ? $product->sale_chartaccount_id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $credit->amount,
                        'reference'          => 'Credit Note',
                        'reference_id'       => $credit->id,
                        'reference_sub_id'   => $credit->invoice,
                        'date'               => $credit->date,
                    ];
                    Utility::addTransactionLines($data);

                    $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                        'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $credit->amount,
                        'reference'          => 'Credit Note',
                        'reference_id'       => $credit->id,
                        'reference_sub_id'   => $credit->invoice,
                        'date'               => $credit->date,
                    ];
                    Utility::addTransactionLines($data);

                return redirect()->back()->with('success', __('Credit Note successfully created.'));
            }else{
                return redirect()->back()->with('error', __('The invoice field is required.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('edit credit note'))
        {
            $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get()->pluck('invoice_id', 'id');
            $creditNote = CustomerCreditNotes::find($creditNote_id);
            $statues = CustomerCreditNotes :: $statues;
            return view('customerCreditNote.edit', compact('creditNote','statues' , 'invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('edit credit note'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric|gt:0',
                                   'date' => 'required|date_format:Y-m-d',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoiceDue    = Invoice::where('id', $invoice_id)->first();

            $credit        = CustomerCreditNotes::find($creditNote_id);

            $creditAmount  = floatval($request->amount);

            $invoicePaid   = $invoiceDue->getTotal() - $invoiceDue->getDue() - $invoiceDue->invoiceTotalCreditNote();

            $existingCredits = CustomerCreditNotes::where('invoice', $invoice_id)->where('id', '!=', $creditNote_id)->get()->sum('amount');

            if (($existingCredits + $creditAmount) > $invoicePaid) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoicePaid - $existingCredits) . ' credit to this invoice.');
            }

            $credit->invoice_product = $request->invoice_product;
            $credit->date            = $request->date;
            $credit->amount          = $creditAmount;
            $credit->description     = $request->description;
            $credit->save();

            $invoiceProduct = InvoiceProduct::find($credit->invoice_product);
            $product        = ProductService::find($invoiceProduct->product_id);

            $data = [
                'account_id'         => !empty($product->sale_chartaccount_id) ? $product->sale_chartaccount_id : 0,
                'transaction_type'   => 'debit',
                'transaction_amount' => $credit->amount,
                'reference'          => 'Credit Note',
                'reference_id'       => $credit->id,
                'reference_sub_id'   => $credit->invoice,
                'date'               => $credit->date,
            ];
            Utility::addTransactionLines($data , 'edit' , 'notes');

            $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
            $data    = [
                'account_id'         => !empty($account) ? $account->id : 0,
                'transaction_type'   => 'credit',
                'transaction_amount' => $credit->amount,
                'reference'          => 'Credit Note',
                'reference_id'       => $credit->id,
                'reference_sub_id'   => $credit->invoice,
                'date'               => $credit->date,
            ];
            Utility::addTransactionLines($data , 'edit');

            return redirect()->back()->with('success', __('The credit note details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('delete credit note'))
        {
            $creditNote = CustomerCreditNotes::find($creditNote_id);

            if($creditNote->status == 0)
            {
                AddTransactionLine::where('reference_id',$creditNote->id)->where('reference_sub_id',$creditNote->invoice)->where('reference', 'Credit Note')->delete();

                $creditNote->delete();

                return redirect()->back()->with('success', __('The credit note has been deleted.'));
            }
            else
            {
                $usedCreditNote = CreditNote::where('credit_note', $creditNote->id)
                ->pluck('invoice')
                ->unique();
                $invoice = Invoice::whereIn('id' , $usedCreditNote)->get()->pluck('invoice_id')->toarray();
                $formattedInvoices = array_map(function ($invoiceId) {
                    return \Auth::user()->invoiceNumberFormat($invoiceId);
                }, $invoice);
                $invoiceId = implode(' , ' ,($formattedInvoices));

                return redirect()->back()->with('error', __('This credit note is already used in invoice ') .$invoiceId. __(', so it can not deleted.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getItems(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        if(!empty($invoice)) {
            $items = InvoiceProduct::select('invoice_products.*' , 'product_services.name as product_name')->join('product_services' ,  'product_services.id' , 'invoice_products.product_id')->where('invoice_id' , $request->invoice_id)->where('product_services.created_by',\Auth::user()->creatorId())->get();

            $getDue = $invoice->getTotal() - $invoice->getDue();

            return response()->json(['status' => true ,'items' => $items , 'getDue' => $getDue]);
        }
        return response()->json(['status' => false]);
    }

    public function getItemPrice(Request $request)
    {
        $invoiceProduct = InvoiceProduct::find($request->item_id);
        $totalPrice     = 0;
        if($invoiceProduct != null)
        {
            $product        = ProductService::find($invoiceProduct->product_id);
            $taxRate        = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
            $lineAmount     = (float) ($invoiceProduct->price ?? 0);
            $totalTax       = ($taxRate / 100) * $lineAmount;
            $totalPrice     = $lineAmount + $totalTax;
        }

        return response()->json($totalPrice);
    }

    function creditNoteNumber()
    {
        $latest = CustomerCreditNotes::whereHas('invoices', function ($query) {
                    $query->where('created_by', \Auth::user()->creatorId());
                     })->with(['invoices'])->latest()->first();
        if ($latest == null) {
            return 1;
        } else {
            return $latest->credit_id + 1;
        }
    }
}
