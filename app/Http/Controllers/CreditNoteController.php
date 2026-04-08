<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Utility;
use App\Models\CustomerCreditNotes;
use App\Traits\updateNotesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditNoteController extends Controller
{

    use updateNotesStatus;

    public function create($invoice_id)
    {
        if(\Auth::user()->can('create credit note'))
        {
            $invoiceDue  = Invoice::where('id', $invoice_id)->first();
            $customer    = Customer::where('id', $invoiceDue->customer_id)->first();
            $creditNotes = CustomerCreditNotes::whereHas('invoices', function ($query) use ($invoiceDue) {
                $query->where('customer_id', $invoiceDue->customer_id)
                      ->where('created_by', \Auth::user()->creatorId());
            })
            ->where('status', '!=', '2')
            ->with(['custom_customer', 'invoices'])
            ->get()
            ->pluck('credit_id', 'id');
            
            return view('creditNote.create', compact('customer', 'invoice_id' , 'creditNotes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request, $invoice_id)
    {
        if(\Auth::user()->can('create credit note'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric|gt:0',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            if($request->amount > $invoiceDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' .\Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            }

            $credit              = new CreditNote();
            $credit->invoice     = $invoice_id;
            $credit->credit_note = $request->credit_note;
            $credit->customer    = 0;
            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->description = isset($request->description) ? $request->description : '--';
            $credit->save();

            if($invoiceDue->getDue() <= 0)
            {
                $invoiceDue->status = 4;
                $invoiceDue->save();
            } else {
                $invoiceDue->status = 3;
                $invoiceDue->save();
            }
            
            $this->updateCreditNoteStatus($credit);

            return redirect()->back()->with('success', __('The credit note has been created successfully.'));
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
            $creditNote = CreditNote::find($creditNote_id);

            return view('creditNote.edit', compact('creditNote'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $invoice_id, $creditNote_id)
    {

        if(\Auth::user()->can('edit credit note'))
        {

            $validator = Validator::make(
                $request->all(), [
                    'amount' => 'required|numeric',
                    'date' => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            $credit = CreditNote::find($creditNote_id);
            if($request->amount > $invoiceDue->getDue() + $credit->amount)
            {
                return redirect()->back()->with('error', 'Maximum ' .\Auth::user()->priceFormat($invoiceDue->getDue() + $credit->amount ) . ' credit limit of this invoice.');
            }

            if(($invoiceDue->getDue() + $credit->amount ) - $request->amount <= 0)
            {
                $invoiceDue->status = 4;
                $invoiceDue->save();
            } else {
                $invoiceDue->status = 3;
                $invoiceDue->save();
            }

            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->description = $request->description;
            $credit->save();

            $this->updateCreditNoteStatus($credit);

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

            $creditNote = CreditNote::find($creditNote_id);
            if($creditNote)
            {
                $invoice = Invoice::find($creditNote->invoice);
                $invoiceDue = $invoice->getDue() + $creditNote->amount;
                $total   = $invoice->getTotal();

                if ( $invoiceDue > 0 && $invoiceDue != $total) {
                    $invoice->status = 3;
                } elseif($invoiceDue == $total) {
                    $invoice->status = 2;
                }
                $invoice->save();

                $this->updateCreditNoteStatus($creditNote , 'delete');
                $creditNote->delete();

                return redirect()->back()->with('success', __('The credit note has been deleted.'));
            }
            return redirect()->back()->with('error', __('Credit note not found!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getPrice(Request $request)
    {
        $creditNote = CustomerCreditNotes::find($request->credit_note);
        $price      = !empty($creditNote) ? ($creditNote->amount + $request->amount) - $creditNote->usedCreditNote() : 0;

        return response()->json($price);
    }

}
