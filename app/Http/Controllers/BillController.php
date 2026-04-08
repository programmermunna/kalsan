<?php

namespace App\Http\Controllers;

use App\Exports\BillExport;
use App\Models\AddTransactionLine;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\BillProduct;
use App\Models\ChartOfAccount;
use App\Models\CustomerDebitNotes;
use App\Models\CustomField;
use App\Models\DebitNote;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\StockReport;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Traits\updateNotesStatus;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BillController extends Controller
{

    use updateNotesStatus;

    public function index(Request $request)
    {
        if(\Auth::user()->can('manage bill'))
        {

            $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

            $status = Bill::$statues;

            $query = Bill::where('type', '=', 'Bill')->where('created_by', '=', \Auth::user()->creatorId());
            if(!empty($request->vender))
            {
                $query->where('vender_id', '=', $request->vender);
            }

            if(count(explode('to', $request->bill_date)) > 1)
            {
                $date_range = explode(' to ', $request->bill_date);
                $query->whereBetween('bill_date', $date_range);
            }
            elseif(!empty($request->bill_date))
            {
                $date_range = [$request->date , $request->bill_date];
                $query->whereBetween('bill_date', $date_range);
            }

            if ($request->status != null)
            {
                $query->where('status', '=', $request->status);
            }
            $bills = $query->with(['category'])->paginate(10);

            return view('bill.index', compact('bills', 'vender', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($vendorId = 0)
    {

        if(\Auth::user()->can('create bill'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'bill')->get();
            $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                ->whereNotIn('type', ['product & service', 'income',])
                ->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');

            $bill_number = \Auth::user()->billNumberFormat($this->billNumber());
            $venders     = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $venders->prepend('Select Vender', '');

            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Expense')->get()->pluck('name', 'id');
            $product_services->prepend('Select Expense', '');

            return view('bill.create', compact('venders', 'bill_number', 'product_services', 'category', 'customFields', 'vendorId'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {

        if(\Auth::user()->can('create bill'))
        {

            $validator = \Validator::make(
                $request->all(), [
                    'vender_id' => 'required',
                    'bill_date' => 'required',
                    'due_date' => 'required',
                    // 'category_id' => 'required'
                ]
            );
            if ($validator->fails()) {
                $messages3 = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages3->first());
            }

            $bill            = new Bill();
            $bill->bill_id   = $this->billNumber();
            $bill->vender_id = $request->vender_id;;
            $bill->bill_date      = $request->bill_date;
            $bill->status         = 0;
            $bill->type         =  'Bill';
            $bill->user_type         =  'vendor';
            $bill->due_date       = $request->due_date;
            // $bill->category_id    = !empty($request->category_id) ? $request->category_id :0;
            $bill->order_number   = !empty($request->order_number) ? $request->order_number : 0;
            $bill->created_by     = \Auth::user()->creatorId();
            $bill->save();

            CustomField::saveData($bill, $request->customField);
            $products = $request->items;

            $total_amount=0;

            for($i = 0; $i < count($products); $i++)
            {
                if(!empty($products[$i]['item']))
                {
                    $billProduct              = new BillProduct();
                    $billProduct->bill_id     = $bill->id;
                    $billProduct->product_id  = $products[$i]['item'];
                    $billProduct->quantity    = $products[$i]['quantity'];
                    // $billProduct->tax         = $products[$i]['tax'];
                    $billProduct->discount    = $products[$i]['discount'];
                    $billProduct->price       = $products[$i]['price'];
                    $billProduct->description = $products[$i]['description'];
                    $billProduct->save();
                }

                //inventory management (Quantity)
                if(!empty($billProduct))
                {
                    Utility::total_quantity('plus',$billProduct->quantity,$billProduct->product_id);
                }

                //Product Stock Report
                if(!empty($products[$i]['item']))
                {
                    $type='bill';
                    $type_id = $bill->id;
                    $description=$products[$i]['quantity'].'  '.__('quantity purchase in bill').' '. \Auth::user()->billNumberFormat($bill->bill_id);
                    Utility::addProductStock( $products[$i]['item'],$products[$i]['quantity'],$type,$description,$type_id);
                    //$total_amount += ((float)$billProduct->quantity * (float)$billProduct->price);
                    $total_amount += ($billProduct->quantity * $billProduct->price);

                }

            }

            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $vendor = Vender::find($request->vender_id);
            $billNotificationArr = [
                'bill_number' => \Auth::user()->billNumberFormat($bill->bill_id),
                'user_name' => \Auth::user()->name,
                'bill_date' => $bill->bill_date,
                'bill_due_date' => $bill->due_date,
                'vendor_name' => $vendor->name,
            ];
            //Slack Notification
            if(isset($setting['bill_notification']) && $setting['bill_notification'] ==1)
            {
                Utility::send_slack_msg('new_bill', $billNotificationArr);
            }
            //Telegram Notification
            if(isset($setting['telegram_bill_notification']) && $setting['telegram_bill_notification'] ==1)
            {
                Utility::send_telegram_msg('new_bill', $billNotificationArr);
            }
            //Twilio Notification
            if(isset($setting['twilio_bill_notification']) && $setting['twilio_bill_notification'] ==1)
            {
                Utility::send_twilio_msg($vendor->contact,'new_bill', $billNotificationArr);
            }


            //webhook
            $module ='New Bill';
            $webhook =  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($bill);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                if($status == true)
                {
                    return redirect()->route('bill.index', $bill->id)->with('success', __('Bill successfully created.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }


            return redirect()->route('bill.index', $bill->id)->with('success', __('Bill successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($ids)
    {

        if(\Auth::user()->can('show bill'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Bill Not Found.'));
            }

            $id   = Crypt::decrypt($ids);
            $bill = Bill::with('debitNote' ,'payments.bankAccount','items.product.unit')->find($id);

            if(!empty($bill) && $bill->created_by == \Auth::user()->creatorId())
            {
                $billPayment = BillPayment::where('bill_id', $bill->id)->first();
                $vendor      = $bill->vender;

                $item      = $bill->items;
                $accounts  = $bill->accounts;
                $items     = [];
                if(!empty($item) && count($item) > 0)
                {
                    foreach ($item as $k=>$val)
                    {
                        $items[]=$val;
                    }
                }

                $bill->customField = CustomField::getData($bill, 'bill');
                $customFields      = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'bill')->get();

                return view('bill.view', compact('bill', 'vendor', 'items', 'billPayment', 'customFields'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($ids)
    {

        if(\Auth::user()->can('edit bill'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Bill Not Found.'));
            }

            $id       = Crypt::decrypt($ids);
            $bill     = Bill::find($id);

            if(!empty($bill))
            {
                if ($bill->status != 3 && $bill->status != 4) {
                    $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                        ->whereNotIn('type', ['product & service', 'income',])
                        ->get()->pluck('name', 'id');
                    $category->prepend('Select Category', '');
                    $bill_number      = \Auth::user()->billNumberFormat($bill->bill_id);
                    $venders          = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                    $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Expense')->get()->pluck('name', 'id');
                    $product_services->prepend('Select Expense', '');
                    $bill->customField = CustomField::getData($bill, 'bill');
                    $customFields      = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'bill')->get();

                    //for item and account show in repeater
                    $item      = $bill->items;
                    $items     = [];
                    if(!empty($item) && count($item) > 0)
                    {
                        foreach ($item as $k=>$val)
                        {
                            $items[]=$val;
                        }
                    }

                    return view('bill.edit', compact('venders', 'product_services', 'bill', 'bill_number', 'category',
                        'customFields','items'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            }
            else{
                return redirect()->back()->with('error', __('Bill Not Found.'));
            }

        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Bill $bill)
    {

        if (\Auth::user()->can('edit bill')) {

            if ($bill->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'vender_id' => 'required',
                        'bill_date' => 'required',
                        'due_date' => 'required',
                        // 'category_id' => 'required'
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('bill.index')->with('error', $messages->first());
                }
                $bill->vender_id      = $request->vender_id;
                $bill->bill_date      = $request->bill_date;
                $bill->due_date       = $request->due_date;
                $bill->user_type         =  'vendor';
                $bill->order_number   = $request->order_number;
                // $bill->category_id    = $request->category_id;
                $bill->save();
                CustomField::saveData($bill, $request->customField);
                $products = $request->items;
                $total_amount=0;

                for ($i = 0; $i < count($products); $i++)
                {
                    $billProduct = BillProduct::find($products[$i]['id']);
                    if ($billProduct == null)
                    {
                        $billProduct             = new BillProduct();
                        $billProduct->bill_id    = $bill->id;

                        if(isset($products[$i]['items']) ) {
                            Utility::total_quantity('plus', $products[$i]['quantity'], $products[$i]['items']);
                        }
                    }
                    else{

                        Utility::total_quantity('minus',$billProduct->quantity,$billProduct->product_id);
                    }

                    if (isset($products[$i]['items'])) {
                        $billProduct->product_id = $products[$i]['items'];
                        $billProduct->quantity    = $products[$i]['quantity'];
                        // $billProduct->tax         = $products[$i]['tax'];
                        $billProduct->discount    = $products[$i]['discount'];
                        $billProduct->price       = $products[$i]['price'];
                        $billProduct->description = $products[$i]['description'];
                        $billProduct->save();
                    }

                    if ($products[$i]['id']>0) {
                        Utility::total_quantity('plus',$products[$i]['quantity'],$billProduct->product_id);
                    }

                    //Product Stock Report
                    $type='bill';
                    $type_id = $bill->id;
                    StockReport::where('type','=','bill')->where('type_id','=',$bill->id)->delete();
                    $description=$products[$i]['quantity'].'  '.__(' quantity purchase in bill').' '. \Auth::user()->billNumberFormat($bill->bill_id);

                    if(isset($products[$i]['items']) ){
                        Utility::addProductStock( $products[$i]['items'],$products[$i]['quantity'],$type,$description,$type_id);
                    }

                    $total_amount += ($billProduct->quantity * $billProduct->price);

                }

                $bill_products = BillProduct::where('bill_id', $bill->id)->get();
                foreach ($bill_products as $bill_product) {
                    $product = ProductService::find($bill_product->product_id);
                    $totalTaxPrice = 0;
                    if($bill_product->tax != null){

                        $taxes = \App\Models\Utility::tax($bill_product->tax);
                        foreach ($taxes as $tax) {
                            $taxPrice = \App\Models\Utility::taxRate($tax->rate, $bill_product->price, $bill_product->quantity, $bill_product->discount);
                            $totalTaxPrice += $taxPrice;
                        }
                    }
                    $itemAmount = ($bill_product->price * $bill_product->quantity) - ($bill_product->discount) + $totalTaxPrice;

                    if($bill->status != 0) {
                        $data = [
                            'account_id'         => $product->expense_chartaccount_id,
                            'transaction_type'   => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Bill',
                            'reference_id'       => $bill->id,
                            'reference_sub_id'   => $product->id,
                            'date'               => $bill->bill_date,
                        ];
                        Utility::addTransactionLines($data , 'edit' , 'notes');

                        $account = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
                        $data    = [
                            'account_id'         => !empty($account) ? $account->id : 0,
                            'transaction_type'   => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Bill',
                            'reference_id'       => $bill->id,
                            'reference_sub_id'   => $product->id,
                            'date'               => $bill->bill_date,
                        ];
                        Utility::addTransactionLines($data , 'edit');
                    }
                }

                return redirect()->route('bill.index')->with('success', __('Bill successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Bill $bill)
    {
        if(\Auth::user()->can('delete bill'))
        {
            if($bill->created_by == \Auth::user()->creatorId())
            {
                $customerDebitNote = CustomerDebitNotes::where('bill',$bill->id)->first();

                if($customerDebitNote) {
                    return redirect()->back()->with('error', __('The debit note has been created for this bill, so it cannot be deleted.'));
                }

                $billpayments = $bill->payments;

                foreach($billpayments as $key => $value)
                {
                    Utility::bankAccountBalance($value->account_id, $value->amount, 'credit');
                    Utility::updateUserBalance('vendor', $bill->vender_id, $value->amount, 'debit');

                    $transaction= Transaction::where('payment_id',$value->id)->first();
                    $transaction->delete();

                    $billpayment = BillPayment::where('id', $value->id)->first();

                    AddTransactionLine::where('reference_id', $bill->id)->where('reference_sub_id', $billpayment->id)->where('reference', 'Bill Payment')->delete();
                    $billpayment->delete();
                }

                AddTransactionLine::where('reference_id',$bill->id)->where('reference','Bill')->delete();

                BillProduct::where('bill_id', '=', $bill->id)->delete();

                $debitNoteApply = DebitNote::where('bill', $bill->id)->get();
                foreach($debitNoteApply as $debitNote)
                {
                    $this->updateDebitNoteStatus($debitNote , 'delete');
                    $debitNote->delete();
                }

                $bill->delete();

                return redirect()->route('bill.index')->with('success', __('Bill successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    function billNumber()
    {
        $latest = Bill::where('created_by', '=', \Auth::user()->creatorId())->latest('bill_id')->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->bill_id + 1;
    }

    public function product(Request $request)
    {
        $data['product']     = $product = ProductService::find($request->product_id);
        $data['unit']        = !empty($product->unit) ? $product->unit->name : '';
        $data['taxRate']     = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes']       = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice           = $product->purchase_price;
        $quantity            = 1;
        $taxPrice            = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function productDestroy(Request $request)
    {

        if(\Auth::user()->can('delete bill product'))
        {
            $billProduct=BillProduct::find($request->id);
            $bill=Bill::find($billProduct->bill_id);

            $productService = ProductService::find($billProduct->product_id);

            AddTransactionLine::where('reference_id', $bill->id)->where('reference_sub_id', $productService->id)->where('reference', 'Bill')->delete();

            BillProduct::where('id', '=', $request->id)->delete();

            return response()->json(['status' => true, 'message' => __('Bill product successfully deleted.')]);

        }
        else
        {
            return response()->json(['status' => false, 'message' => __('Permission denied.')]);
        }
    }


    public function sent($id)
    {
        if(\Auth::user()->can('send bill'))
        {
            $bill            = Bill::where('id', $id)->first();
            $bill->send_date = date('Y-m-d');
            $bill->status    = 2;
            $bill->save();

            $vender = Vender::where('id', $bill->vender_id)->first();

            $bill->name = !empty($vender) ? $vender->name : '';
            $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);

            $billId    = Crypt::encrypt($bill->id);
            $bill->url = route('bill.pdf', $billId);

            $bill_products = BillProduct::where('bill_id', $bill->id)->get();
            foreach ($bill_products as $bill_product) {
                $product = ProductService::find($bill_product->product_id);
                $totalTaxPrice = 0;
                // if($bill_product->tax != null)
                // {
                //     $taxes = \App\Models\Utility::tax($bill_product->tax);
                //     foreach ($taxes as $tax) {
                //         $taxPrice = \App\Models\Utility::taxRate($tax->rate, $bill_product->price, $bill_product->quantity, $bill_product->discount);
                //         $totalTaxPrice += $taxPrice;
                //     }
                // }

                $itemAmount = ($bill_product->price * $bill_product->quantity) - ($bill_product->discount) + $totalTaxPrice;

                $data = [
                    'account_id'         => $product->expense_chartaccount_id,
                    'bank_id'            => '0',
                    'transaction_type'   => 'debit',
                    'transaction_amount' => $itemAmount,
                    'reference'          => 'Bill',
                    'reference_id'       => $bill->id,
                    'reference_sub_id'   => $product->id,
                    'vender_id'          => '0',
                    'customer_id'        => '0',
                    'date'               => $bill->bill_date,
                ];
                Utility::addTransactionLines($data);

                $account = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
                $data    = [
                    'account_id'         => !empty($account) ? $account->id : 0,
                    'bank_id'            => '0',
                    'transaction_type'   => 'credit',
                    'transaction_amount' => $itemAmount,
                    'reference'          => 'Bill-AP',
                    'reference_id'       => $bill->id,
                    'reference_sub_id'   => $product->id,
                    'vender_id'          => $bill->vender_id,
                     'customer_id'        => '0',
                    'date'               => $bill->bill_date,
                ];
                Utility::addTransactionLines($data);
            }

            $vendorArr = [
                'vender_bill_name' => $bill->name,
                'vender_bill_number' =>$bill->bill,
                'vender_bill_url' => $bill->url,

            ];
            $resp = Utility::sendEmailTemplate('vender_bill_sent', [$vender->id => $vender->email], $vendorArr);


            return redirect()->back()->with('success', __('Bill successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function resent($id)
    {

//        if(\Auth::user()->can('send bill'))
//        {

        // Send Email
        $setings = Utility::settings();

        if($setings['bill_resent'] == 1)
        {
            $bill = Bill::where('id', $id)->first();
            $vender = Vender::where('id', $bill->vender_id)->first();
            $bill->name = !empty($vender) ? $vender->name : '';
            $bill->status    = 2;
            $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);
            $billId    = Crypt::encrypt($bill->id);
            $bill->url = route('bill.pdf', $billId);
            $billResendArr = [
                'vender_name'   => $vender->name,
                'vender_email'  => $vender->email,
                'bill_name'  => $bill->name,
                'bill_number'   => $bill->bill,
                'bill_url' =>$bill->url,
            ];

            $resp = Utility::sendEmailTemplate('bill_resent', [$vender->id => $vender->email], $billResendArr);


        }

        return redirect()->back()->with('success', __('Bill successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
//        }
//        else
//        {
//            return redirect()->back()->with('error', __('Permission denied.'));
//        }

    }

    public function payment($bill_id)
    {
        if(\Auth::user()->can('create payment bill'))
        {
            $bill    = Bill::where('id', $bill_id)->first();
            $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('bill.payment', compact('venders', 'categories', 'accounts', 'bill'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));

        }
    }

    public function createPayment(Request $request, $bill_id)
    {
        if(\Auth::user()->can('create payment bill'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'amount' => 'required',
                    'account_id' => 'required',

                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $bill = Bill::find($bill_id);
            if ($bill->getDue() < $request->amount) {
                return redirect()->back()->with('error', __('Bill payment amount should not greater than subtotal.'));
            }

            $bankAccount = BankAccount::find($request->account_id);
            if($bankAccount->chart_account_id == 0)
            {
                return redirect()->back()->with('error', __('This bank account is not connect with chart of account, so please connect first.'));
            }

            $billPayment                 = new BillPayment();
            $billPayment->bill_id        = $bill_id;
            $billPayment->date           = $request->date;
            $billPayment->amount         = $request->amount;
            $billPayment->account_id     = $request->account_id;
            $billPayment->payment_method = 0;
            $billPayment->reference      = $request->reference;
            $billPayment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $request->add_receipt->storeAs('uploads/payment', $fileName);
                $billPayment->add_receipt = $fileName;
            }

            $billPayment->save();

            $bill  = Bill::where('id', $bill_id)->first();
            $due   = $bill->getDue();
            $total = $bill->getTotal();

            if($bill->status == 0)
            {
                $bill->send_date = date('Y-m-d');
                $bill->save();
            }

            if($due <= 0)
            {
                $bill->status = 4;
                $bill->save();
            }
            else
            {
                $bill->status = 3;
                $bill->save();
            }
            $billPayment->user_id    = $bill->vender_id;
            $billPayment->user_type  = 'Vender';
            $billPayment->type       = 'Partial';
            $billPayment->created_by = \Auth::user()->id;
            $billPayment->payment_id = $billPayment->id;
            $billPayment->category   = 'Bill';
            $billPayment->account    = $request->account_id;
            Transaction::addTransaction($billPayment);

            $vender = Vender::where('id', $bill->vender_id)->first();

            $payment         = new BillPayment();
            $payment->name   = $vender['name'];
            $payment->method = '-';
            $payment->date   = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->bill   = 'bill ' . \Auth::user()->billNumberFormat($billPayment->bill_id);

            Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');
            Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

            $accountId = BankAccount::find($billPayment->account_id);

            $data = [
                'account_id'         => $accountId->chart_account_id,
                'bank_id'            => '1',
                'transaction_type'   => 'credit',
                'transaction_amount' => $billPayment->amount,
                'reference'          => 'Bill Payment',
                'reference_id'       => $bill->id,
                'reference_sub_id'   => $billPayment->id,
                'customer_id'        => '0',
                'vender_id'          => $bill->vender_id,
                'date'               => $billPayment->date,
            ];
            Utility::addTransactionLines($data);

            $account = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
            $data    = [
                'account_id'         => !empty($account) ? $account->id : 0,
                'bank_id'            => '0',
                'transaction_type'   => 'debit',
                'transaction_amount' => $request->amount,
                'reference'          => 'Bill Payment',
                'reference_id'       => $bill->id,
                'reference_sub_id'   => $billPayment->id,
                'customer_id'            => '0',
                'vender_id'            => '0',
                'date'               => $billPayment->date,
            ];
            Utility::addTransactionLines($data);

            // Send Email
            $setings = Utility::settings();
            if($setings['new_bill_payment'] == 1)
            {

                $vender = Vender::where('id', $bill->vender_id)->first();
                $billPaymentArr = [
                    'vender_name'   => $vender->name,
                    'vender_email'  => $vender->email,
                    'payment_name'  =>$payment->name,
                    'payment_amount'=>$payment->amount,
                    'payment_bill'  =>$payment->bill,
                    'payment_date'  =>$payment->date,
                    'payment_method'=>$payment->method,
                    'company_name'=>$payment->method,

                ];


                $resp = Utility::sendEmailTemplate('new_bill_payment', [$vender->id => $vender->email], $billPaymentArr);

                return redirect()->back()->with('success', __('Payment successfully added.') .((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : '').(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '') );

            }

            return redirect()->back()->with('success', __('Payment successfully added.'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));


        }

    }

    public function paymentDestroy(Request $request, $bill_id, $payment_id)
    {

        if(\Auth::user()->can('delete payment bill'))
        {
            $payment = BillPayment::find($payment_id);
            BillPayment::where('id', '=', $payment_id)->delete();

            $bill = Bill::where('id', $bill_id)->first();

            $due   = $bill->getDue();
            $total = $bill->getTotal();

            if($due > 0 && $total != $due)
            {
                $bill->status = 3;

            }
            else
            {
                $bill->status = 2;
            }

            Utility::updateUserBalance('vendor', $bill->vender_id, $payment->amount, 'debit');
            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            AddTransactionLine::where('reference_id',$bill->id)->where('reference_sub_id',$payment->id)->where('reference','Bill Payment')->delete();


            $bill->save();
            $type = 'Partial';
            $user = 'Vender';
            Transaction::destroyTransaction($payment_id, $type, $user);

            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function venderBill(Request $request)
    {
        if(\Auth::user()->can('manage vender bill'))
        {

            $status = Bill::$statues;

            $query = Bill::where('vender_id', '=', \Auth::user()->vender_id)->where('status', '!=', '0')->where('created_by', \Auth::user()->creatorId());

            if(!empty($request->vender))
            {
                $query->where('id', '=', $request->vender);
            }
            if(!empty($request->bill_date))
            {
                $date_range = explode(' - ', $request->bill_date);
                $query->whereBetween('bill_date', $date_range);
            }

            if(!empty($request->status))
            {
                $query->where('status', '=', $request->status);
            }
            $bills = $query->get();


            return view('bill.index', compact('bills', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function venderBillShow($id)
    {
        if(\Auth::user()->can('show bill'))
        {
            try{
                $bill_id = Crypt::decrypt($id);
            } catch (\Exception $e){
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
            $bill    = Bill::where('id', $bill_id)->first();

            if($bill->created_by == \Auth::user()->creatorId())
            {
                $vendor = $bill->vender;
                $iteams = $bill->items;

                return view('bill.view', compact('bill', 'vendor', 'iteams'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vender(Request $request)
    {
        $vender = Vender::where('id', '=', $request->id)->first();

        return view('bill.vender_detail', compact('vender'));
    }


    public function venderBillSend($bill_id)
    {
        return view('vender.bill_send', compact('bill_id'));
    }

    public function venderBillSendMail(Request $request, $bill_id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'email' => 'required|email',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $email = $request->email;
        $bill  = Bill::where('id', $bill_id)->first();

        $vender     = Vender::where('id', $bill->vender_id)->first();
        $bill->name = !empty($vender) ? $vender->name : '';
        $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);

        $billId    = Crypt::encrypt($bill->id);
        $bill->url = route('bill.pdf', $billId);

        try
        {
//            Mail::to($email)->send(new VenderBillSend($bill));
        }
        catch(\Exception $e)
        {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Bill successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));

    }

    public function shippingDisplay(Request $request, $id)
    {
        $bill = Bill::find($id);

        if($request->is_display == 'true')
        {
            $bill->shipping_display = 1;
        }
        else
        {
            $bill->shipping_display = 0;
        }
        $bill->save();

        return redirect()->back()->with('success', __('Shipping address status successfully changed.'));
    }

    public function duplicate($bill_id)
    {
        if(\Auth::user()->can('duplicate bill'))
        {
            $bill = Bill::where('id', $bill_id)->first();

            $duplicateBill                   = new Bill();
            $duplicateBill->bill_id          = $this->bill  ;
            $duplicateBill->vender_id        = $bill['vender_id'];
            $duplicateBill->bill_date        = date('Y-m-d');
            $duplicateBill->due_date         = $bill['due_date'];
            $duplicateBill->send_date        = null;
            // $duplicateBill->category_id      = $bill['category_id'];
            $duplicateBill->order_number     = $bill['order_number'];
            $duplicateBill->status           = 0;
            $duplicateBill->type             = 'Bill';
            $duplicateBill->shipping_display = $bill['shipping_display'];
            $duplicateBill->created_by       = $bill['created_by'];
            $duplicateBill->save();

            if($duplicateBill)
            {
                $billProduct = BillProduct::where('bill_id', $bill_id)->get();
                foreach($billProduct as $product)
                {
                    $duplicateProduct             = new BillProduct();
                    $duplicateProduct->bill_id    = $duplicateBill->id;
                    $duplicateProduct->product_id = $product->product_id;
                    $duplicateProduct->quantity   = $product->quantity;
                    $duplicateProduct->tax        = $product->tax;
                    $duplicateProduct->discount   = $product->discount;
                    $duplicateProduct->price      = $product->price;
                    $duplicateProduct->save();
                }
            }


            return redirect()->back()->with('success', __('Bill duplicate successfully.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewBill($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        $bill     = new Bill();

        $vendor                   = new \stdClass();
        $vendor->email            = '<Email>';
        $vendor->shipping_name    = '<Vendor Name>';
        $vendor->shipping_country = '<Country>';
        $vendor->shipping_state   = '<State>';
        $vendor->shipping_city    = '<City>';
        $vendor->shipping_phone   = '<Vendor Phone Number>';
        $vendor->shipping_zip     = '<Zip>';
        $vendor->shipping_address = '<Address>';
        $vendor->billing_name     = '<Vendor Name>';
        $vendor->billing_country  = '<Country>';
        $vendor->billing_state    = '<State>';
        $vendor->billing_city     = '<City>';
        $vendor->billing_phone    = '<Vendor Phone Number>';
        $vendor->billing_zip      = '<Zip>';
        $vendor->billing_address  = '<Address>';

        $totalTaxPrice = 0;
        $taxesData     = [];
        $items         = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;
            $item->unit     = 1;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach($taxes as $k => $tax)
            {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTax['tax_price'] = 10;
                $itemTaxes[]      = $itemTax;
                if(array_key_exists('Tax ' . $k, $taxesData))
                {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                }
                else
                {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $bill->bill_id    = 1;
        $bill->issue_date = date('Y-m-d H:i:s');
        $bill->due_date   = date('Y-m-d H:i:s');
        $bill->itemData   = $items;

        $bill->totalTaxPrice = 60;
        $bill->totalQuantity = 3;
        $bill->totalRate     = 300;
        $bill->totalDiscount = 10;
        $bill->taxesData     = $taxesData;
        $bill->created_by     = $objUser->creatorId();

        $bill->customField   = [];
        $customFields        = [];

        $preview      = 1;
        $color        = '#' . $color;
        $font_color   = Utility::getFontColor($color);

        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $bill_logo = Utility::getValByName('bill_logo');
        if(isset($bill_logo) && !empty($bill_logo))
        {
            $img = Utility::get_file('bill_logo/') . $bill_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }

        return view('bill.templates.' . $template, compact('bill', 'preview', 'color', 'img', 'settings', 'vendor', 'font_color', 'customFields'));
    }

    public function bill($bill_id)
    {
        $settings = Utility::settings();
        try {
            $billId       = Crypt::decrypt($bill_id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Bill Not Found.'));
        }
        $billId   = Crypt::decrypt($bill_id);

        $bill  = Bill::where('id', $billId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $bill->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $vendor = $bill->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach($bill->items as $product)
        {

            $item              = new \stdClass();
            $item->name        = !empty($product->product) ? $product->product->name : '';
            $item->quantity    = $product->quantity;
            $item->unit        = !empty($product->product) ? $product->product->unit_id : '';
            $item->tax         = $product->tax;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = Utility::tax($product->tax);
            $itemTaxes = [];
            if(!empty($item->tax))
            {
                foreach($taxes as $tax)
                {
                    $taxPrice      = Utility::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate . '%';
                    $itemTax['price'] = Utility::priceFormat($settings, $taxPrice);
                    $itemTax['tax_price'] =$taxPrice;
                    $itemTaxes[]      = $itemTax;


                    if(array_key_exists($tax->name, $taxesData))
                    {
                        $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                    }
                    else
                    {
                        $taxesData[$tax->name] = $taxPrice;
                    }

                }

                $item->itemTax = $itemTaxes;
            }
            else
            {
                $item->itemTax = [];
            }
            $items[] = $item;
        }

        $bill->itemData      = $items;
        $bill->totalTaxPrice = $totalTaxPrice;
        $bill->totalQuantity = $totalQuantity;
        $bill->totalRate     = $totalRate;
        $bill->totalDiscount = $totalDiscount;
        $bill->taxesData     = $taxesData;
        $bill->customField   = CustomField::getData($bill, 'bill');
        $customFields        = [];
        if(!empty(\Auth::user()))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'bill')->get();
        }

        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($bill->created_by);
        $bill_logo = $settings_data['bill_logo'];
        if(isset($bill_logo) && !empty($bill_logo))
        {
            $img = Utility::get_file('bill_logo/') . $bill_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }


        if($bill)
        {
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('bill.templates.' . $settings['bill_template'], compact('bill', 'color', 'settings', 'vendor', 'img', 'font_color', 'customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function saveBillTemplateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['bill_template']) && (!isset($post['bill_color']) || empty($post['bill_color'])))
        {
            $post['bill_color'] = "ffffff";
        }


        if($request->bill_logo)
        {
            $dir = 'bill_logo/';
            $bill_logo = \Auth::user()->id . '_bill_logo.png';
            $validation =[
                'mimes:'.'png',
                'max:'.'20480',
            ];
            $path = Utility::upload_file($request,'bill_logo',$bill_logo,$dir,$validation);
            if($path['flag']==0)
            {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['bill_logo'] = $bill_logo;
        }

        foreach($post as $key => $data)
        {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                             $data,
                                                                                                                                             $key,
                                                                                                                                             \Auth::user()->creatorId(),
                                                                                                                                         ]
            );
        }

        return redirect()->back()->with('success', __('Bill Setting updated successfully'));
    }

    public function items(Request $request)
    {
        $items = BillProduct::where('bill_id', $request->bill_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function invoiceLink($billId)
    {
        try {
            $id       = Crypt::decrypt($billId);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Bill Not Found.'));
        }

        $id             = Crypt::decrypt($billId);
        $bill           =Bill::find($id);

        $user_id        = $bill->created_by;
        $user           = User::find($user_id);

        $billPayment = BillPayment::where('bill_id', $bill->id)->first();
        $vendor = $bill->vender;
        $iteams   = $bill->items;

        $bill->customField = CustomField::getData($bill, 'bill');
         $customFields         = CustomField::where('module', '=', 'bill')->get();



         return view('bill.customer_bill', compact('bill', 'vendor', 'iteams', 'customFields','billPayment','user'));
    }

    public function export()
    {
        $name = 'bill_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new BillExport(), $name . '.xlsx');

        return $data;
    }

}

