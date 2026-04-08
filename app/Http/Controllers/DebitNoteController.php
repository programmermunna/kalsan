<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\CustomerDebitNotes;
use App\Models\DebitNote;
use App\Models\Vender;
use App\Traits\updateNotesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DebitNoteController extends Controller
{
    use updateNotesStatus;

    public function create($bill_id)
    {
        if(\Auth::user()->can('create debit note'))
        {
            $bill  = Bill::where('id', $bill_id)->first();
            $vender    = Vender::where('id', $bill->vender_id)->first();

            $debitNotes = CustomerDebitNotes::whereHas('bills', function ($query) use ($bill) {
                $query->where('vender_id', $bill->vender_id)
                    ->where('created_by', \Auth::user()->creatorId());
            })
            ->where('status', '!=', '2')
            ->with(['bills'])
            ->get()
            ->pluck('debit_id', 'id');

            return view('debitNote.create', compact('vender', 'bill_id' , 'debitNotes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request, $bill_id)
    {

        if(\Auth::user()->can('create debit note'))
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
            $billDue = Bill::where('id', $bill_id)->first();

            if($request->amount > $billDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' .\Auth::user()->priceFormat($billDue->getDue()) . ' debit limit of this bill.');
            }
            $bill               = Bill::where('id', $bill_id)->first();

            $debit              = new DebitNote();
            $debit->bill        = $bill_id;
            $debit->debit_note  = $request->debit_note;
            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->description = isset($request->description) ? $request->description : '--';
            $debit->save();

            if($billDue->getDue() <= 0)
            {
                $billDue->status = 4;
                $billDue->save();
            } else {
                $billDue->status = 3;
                $billDue->save();
            }
            
            $this->updateDebitNoteStatus($debit);


            return redirect()->back()->with('success', __('The debit note has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('edit debit note'))
        {
            $debitNote = DebitNote::find($debitNote_id);

            return view('debitNote.edit', compact('debitNote'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $bill_id, $debitNote_id)
    {

        if(\Auth::user()->can('edit debit note'))
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
            $billDue = Bill::where('id', $bill_id)->first();

            $debit = DebitNote::find($debitNote_id);
            if($request->amount > $billDue->getDue() + $debit->amount)
            {
                return redirect()->back()->with('error', 'Maximum ' .\Auth::user()->priceFormat($billDue->getDue() + $debit->amount ) . ' debit limit of this bill.');
            }

            if(($billDue->getDue() + $debit->amount ) - $request->amount <= 0)
            {
                $billDue->status = 4;
                $billDue->save();
            } else {
                $billDue->status = 3;
                $billDue->save();
            }

            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->description = $request->description;
            $debit->save();

            $this->updateDebitNoteStatus($debit);


            return redirect()->back()->with('success', __('The debit note details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('delete debit note'))
        {
            $debitNote = DebitNote::find($debitNote_id);
            if($debitNote)
            {
                $bill = Bill::find($debitNote->bill);
                $billDue = $bill->getDue() + $debitNote->amount;
                $total   = $bill->getTotal();

                if ( $billDue > 0 && $billDue != $total) {
                    $bill->status = 3;
                } elseif($billDue == $total) {
                    $bill->status = 2;
                }
                $bill->save();

                $this->updateDebitNoteStatus($debitNote , 'delete');
                $debitNote->delete();

                return redirect()->back()->with('success', __('The debit note has been deleted.'));
            }
            return redirect()->back()->with('error', __('Debit note not found!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getPrice(Request $request)
    {
        $debitNote = CustomerDebitNotes::find($request->debit_note);
        $price      = !empty($debitNote) ? ($debitNote->amount + $request->amount) - $debitNote->usedDebitNote() : 0;

        return response()->json($price);
    }
}
