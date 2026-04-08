<?php

namespace App\Http\Controllers;

use App\Models\AddTransactionLine;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\ChartOfAccount;
use App\Models\CustomerDebitNotes;
use App\Models\DebitNote;
use App\Models\ProductService;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerDebitNotesController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage debit note'))
        {
            $customDebitNotes = CustomerDebitNotes::whereHas('bills', function ($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })->with(['bills'])->get();

            return view('customerDebitNote.index', compact('customDebitNotes'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create debit note'))
        {
            $bills   = Bill::where('status', '!=' , 0)->where('type' , 'Bill')->where('created_by', \Auth::user()->creatorId())->get()->pluck('bill_id', 'id');
            
            $statues = CustomerDebitNotes::$statues;
            return view('customerDebitNote.create', compact('bills','statues'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create debit note'))
        {
            $validator = validator::make(
                $request->all(), [
                                   'bill'   => 'required|numeric',
                                   'amount' => 'required|numeric|gt:0',
                                   'date'   => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $bill_id = $request->bill;
            $billDue = Bill::where('id', $bill_id)->first();
            $debitAmount = floatval($request->amount);

            if($billDue){

                $billPaid = $billDue->getTotal() - $billDue->getDue() - $billDue->billTotalDebitNote();

                $customerDebitNotes = CustomerDebitNotes::where('bill',$bill_id)->get()->sum('amount');

                if($debitAmount > $billPaid || ($customerDebitNotes + $debitAmount)  > $billPaid)
                {
                    return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billPaid-$customerDebitNotes) . ' debit limit of this bill.');
                }
                    $debit               = new CustomerDebitNotes();
                    $debit->debit_id     = $this->debitNoteNumber();
                    $debit->bill         = $bill_id;
                    $debit->bill_product = $request->bill_product;
                    $debit->date         = $request->date;
                    $debit->amount       = $debitAmount;
                    $debit->status       = 0;
                    $debit->description  = $request->description;
                    $debit->save();

                    $billProduct = BillProduct::find($debit->bill_product);
                    $product        = ProductService::find($billProduct->product_id);
                    
                    $data = [
                        'account_id'         => !empty($product->expense_chartaccount_id) ? $product->expense_chartaccount_id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $debit->amount,
                        'reference'          => 'Debit Note',
                        'reference_id'       => $debit->id,
                        'reference_sub_id'   => $debit->bill,
                        'date'               => $debit->date,
                    ];
                    Utility::addTransactionLines($data);
        
                    $account = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                        'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $debit->amount,
                        'reference'          => 'Debit Note',
                        'reference_id'       => $debit->id,
                        'reference_sub_id'   => $debit->bill,
                        'date'               => $debit->date,
                    ];
                    Utility::addTransactionLines($data);

                return redirect()->back()->with('success', __('Debit Note successfully created.'));
            }else{
                return redirect()->back()->with('error', __('The bill field is required.'));
            }
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
            $bills     = Bill::where('created_by', \Auth::user()->creatorId())->where('type' , 'Bill')->get()->pluck('bill_id', 'id');
            $debitNote = CustomerDebitNotes::find($debitNote_id);
            $statues   = CustomerDebitNotes::$statues;
            return view('customerDebitNote.edit', compact('debitNote','statues' , 'bills'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('edit debit note'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric|gt:0',
                                   'date'   => 'required|date_format:Y-m-d',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $billDue = Bill::where('id', $bill_id)->first();

            $debit = CustomerDebitNotes::find($debitNote_id);

            $debitAmount = floatval($request->amount);

            $billPaid = $billDue->getTotal() - $billDue->getDue() - $billDue->billTotalDebitNote();

            $existingDebits = CustomerDebitNotes::where('bill', $bill_id)->where('id', '!=', $debitNote_id)->get()->sum('amount');

            if (($existingDebits + $debitAmount) > $billPaid) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billPaid - $existingDebits) . ' debit to this bill.');
            }

            $debit->bill_product = $request->bill_product;
            $debit->date         = $request->date;
            $debit->amount       = $debitAmount;
            $debit->description  = $request->description;
            $debit->save();

            $billProduct = BillProduct::find($debit->bill_product);
            $product        = ProductService::find($billProduct->product_id);

            $data = [
                'account_id'         => !empty($product->expense_chartaccount_id) ? $product->expense_chartaccount_id : 0,
                'transaction_type'   => 'credit',
                'transaction_amount' => $debit->amount,
                'reference'          => 'Debit Note',
                'reference_id'       => $debit->id,
                'reference_sub_id'   => $debit->bill,
                'date'               => $debit->date,
            ];
            Utility::addTransactionLines($data , 'edit' , 'notes');

            $account = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
            $data    = [
                'account_id'         => !empty($account) ? $account->id : 0,
                'transaction_type'   => 'debit',
                'transaction_amount' => $debit->amount,
                'reference'          => 'Debit Note',
                'reference_id'       => $debit->id,
                'reference_sub_id'   => $debit->bill,
                'date'               => $debit->date,
            ];
            Utility::addTransactionLines($data , 'edit');

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
            $debitNote = CustomerDebitNotes::find($debitNote_id);

            if($debitNote->status == 0)
            {
                AddTransactionLine::where('reference_id',$debitNote->id)->where('reference_sub_id',$debitNote->bill)->where('reference', 'Debit Note')->delete();

                $debitNote->delete();

                return redirect()->back()->with('success', __('The debit note has been deleted.'));
            }
            else
            {
                $usedDebitNote = DebitNote::where('debit_note', $debitNote->id)
                ->pluck('bill')
                ->unique();
                $bill           = Bill::whereIn('id' , $usedDebitNote)->get()->pluck('bill_id')->toarray();
                $formattedBills = array_map(function ($billId) {
                    return \Auth::user()->billNumberFormat($billId);
                }, $bill);
                $billId = implode(' , ' ,($formattedBills));
                
                return redirect()->back()->with('error', __('This debit note is already used in bill ') .$billId. __(', so it can not deleted.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getItems(Request $request)
    {
        $bill = Bill::find($request->bill_id);

        if(!empty($bill)) {
            $items  = BillProduct::select('bill_products.*' , 'product_services.name as product_name')->join('product_services' ,  'product_services.id' , 'bill_products.product_id')->where('bill_id' , $request->bill_id)->where('product_services.created_by',\Auth::user()->creatorId())->get();
                
            $getDue = $bill->getTotal() - $bill->getDue();
    
            return response()->json(['status' => true ,'items' => $items , 'getDue' => $getDue]);
        }
        return response()->json(['status' => false]);
    }

    public function getItemPrice(Request $request)
    {
        $billProduct = BillProduct::find($request->item_id);
        $totalPrice     = 0;
        if($billProduct != null)
        {
            $product        = ProductService::find($billProduct->product_id);
            $taxRate        = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
            $totalTax       = ($taxRate / 100) * (($billProduct->price * $billProduct->quantity) - $billProduct->discount);
            $totalPrice     = (($billProduct->price * $billProduct->quantity) + $totalTax) - $billProduct->discount;
        }
        
        return response()->json($totalPrice);
    }

    function debitNoteNumber()
    {
        $latest = CustomerDebitNotes::whereHas('bills', function ($query) {
                    $query->where('created_by', \Auth::user()->creatorId());
                     })->with(['bills'])->latest()->first();
        if ($latest == null) {
            return 1;
        } else {
            return $latest->debit_id + 1;
        }
    }
}