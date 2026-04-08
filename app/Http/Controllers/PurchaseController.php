<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\CustomField;
use App\Models\ProductService;
use App\Models\ChartOfAccount;
use App\Models\ProductServiceCategory;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\PurchaseProduct;
use App\Models\PurchasePayment;
use App\Models\StockReport;
use App\Models\Transaction;
use App\Models\Vender;
use App\Models\User;
use App\Models\Utility;
use App\Models\WarehouseProduct;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\Crypt;
use App\Models\warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customer->prepend('Select Customer', '');

        $query = Purchase::where('created_by', '=', \Auth::user()->creatorId());

        if(!empty($request->customer))
            {
                $query->where('customer_id', '=', $request->customer);
            }

        $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $vender->prepend('Select Vendor', '');
        $status = Purchase::$statues;
        $purchases = $query->with(['vender','category','customer'])->paginate(10);


        return view('purchase.index', compact('purchases', 'status','vender', 'customer'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($vendorId = 0)
    {
        if(\Auth::user()->can('create purchase'))
        {


            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'purchase')->get();
            $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');



            $purchase_number = \Auth::user()->purchaseNumberFormat($this->purchaseNumber());
            $venders     = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $venders->prepend('Select Vender', '');

            $customers      = Customer::where('created_by', \Auth::user()->creatorId())
                ->get()
                ->mapWithKeys(function ($customer) {
                    $displayName = $customer->name . ' - ' . ($customer->contact ?? '');
                    return [$customer->id => $displayName];
                });
            $customers->prepend('Select Customer', '');



            // $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $customers->prepend('Select Customer', 'Select Customer');

            $warehouse     = warehouse::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $warehouse->prepend('Select Warehouse', '');

            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Cargo')->get()->pluck('name', 'id');

            $product_services->prepend('Select Cargo Type', 'Select Cargo Type');

            $topup = Vender::where('created_by', \Auth::user()->creatorId())->where('tax_number','Airline')->orWhere('tax_number', 'Other')->get()->pluck('name', 'id');
            $topup->prepend('Select Company', 'Select Company');

            $Origin = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $Origin->prepend('Select Origin', '');

            $destination = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $destination->prepend('Select Destination', '');

            return view('purchase.create', compact('venders', 'purchase_number','destination', 'product_services', 'category','customers', 'Origin','customFields','vendorId','warehouse','topup', 'customers'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {

        if(\Auth::user()->can('create purchase'))
        {
            // Simple validation like InvoiceController
            $validator = \Validator::make(
                $request->all(), [
                    'vender_id' => 'required',
                    'customer_id' => 'required',
                    // 'warehouse_id' => 'required',
                    'origin' => 'required',
                    'destination' => 'required',
                    'purchase_date' => 'required',
                    // 'category_id' => 'required',
                    'items' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            // Get items - repeater sends them as array
            $products = $request->items;

            // Ensure items is an array and not empty
            if(empty($products) || !is_array($products)) {
                return redirect()->back()->with('error', __('Please add at least one item.'))->withInput();
            }

            // Filter out any empty items (safety check)
            $products = array_filter($products, function($item) {
                return is_array($item) &&
                       isset($item['item']) && !empty($item['item']) &&
                       isset($item['quantity']) && !empty($item['quantity']) &&
                       isset($item['price']) && !empty($item['price']);
                       isset($item['other_frieght']) && !empty($item['other_frieght']);
                       isset($item['agent_fee']) && !empty($item['agent_fee']);
            });

            if(empty($products) || count($products) == 0) {
                return redirect()->back()->with('error', __('Please add at least one item with product, quantity, and price.'))->withInput();
            }

            // Re-index array to ensure sequential keys
            $products = array_values($products);
            $request->merge(['items' => $products]);
            $purchase                 = new Purchase();
            $purchase->purchase_id    = $this->purchaseNumber();
            $purchase->vender_id      = $request->vender_id;
            $purchase->customer_id      = $request->customer_id;
            $purchase->origin         = $request->origin;
            $purchase->destination    = $request->destination;
            // $purchase->warehouse_id      = $request->warehouse_id;
            $purchase->purchase_date  = $request->purchase_date;
            $purchase->purchase_number   = !empty($request->purchase_number) ? $request->purchase_number : 0;
            $purchase->status         =  0;
            // $purchase->category_id    = $request->category_id;
            $purchase->created_by     = \Auth::user()->creatorId();
            $purchase->save();

            $products = $request->items;

            for($i = 0; $i < count($products); $i++)
            {
                $purchaseProduct  = new PurchaseProduct();
                $purchaseProduct->purchase_id     = $purchase->id;
                $purchaseProduct->product_id  = $products[$i]['item'];
                $purchaseProduct->quantity    = $products[$i]['quantity'];
                // $purchaseProduct->tax         = $products[$i]['tax'];
                $purchaseProduct->discount    = $products[$i]['discount'];
                $purchaseProduct->price       = $products[$i]['price'];
                $purchaseProduct->other_frieght       = $products[$i]['other_frieght'];
                $purchaseProduct->agent_fee       = $products[$i]['agent_fee'];
                $purchaseProduct->description = $products[$i]['description'];

                   // Calculate subtotal based on your formula

                $purchaseProduct->subtotal = ($products[$i]['quantity'] * $products[$i]['price'])
                                    + $products[$i]['other_frieght']
                                    + $products[$i]['agent_fee'];


                $purchaseProduct->save();

                //inventory management (Quantity)
                Utility::total_quantity('plus',$purchaseProduct->quantity,$purchaseProduct->product_id);

                //Product Stock Report
                $type='purchase';
                $type_id = $purchase->id;
                $description=$products[$i]['quantity'].'  '.__(' quantity add in purchase').' '. \Auth::user()->purchaseNumberFormat($purchase->purchase_id);
                Utility::addProductStock( $products[$i]['item'],$products[$i]['quantity'],$type,$description,$type_id);

                //Warehouse Stock Report
                // if(isset($products[$i]['item']))
                // {
                //     Utility::addWarehouseStock( $products[$i]['item'],$products[$i]['quantity'],$request->warehouse_id);
                // }

            }

            return redirect()->route('purchase.index', $purchase->id)->with('success', __('Purchase successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show($ids)
    {

        if(\Auth::user()->can('show purchase'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Purchase Not Found.'));
            }
            $id   = Crypt::decrypt($ids);
            $purchase = Purchase::find($id);

            if(!empty($purchase) && $purchase->created_by == \Auth::user()->creatorId())
            {

                $purchasePayment = PurchasePayment::where('purchase_id', $purchase->id)->first();
                $vendor      = $purchase->vender;
                $customer    = $purchase->customer;
                $iteams      = $purchase->items;



                return view('purchase.view', compact('purchase', 'vendor', 'customer', 'iteams', 'purchasePayment'));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit($idsd)
    {
        if(\Auth::user()->can('edit purchase'))
        {

            try{
                $idwww   = Crypt::decrypt($idsd);
            } catch (\Exception $e){
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
            $purchase     = Purchase::find($idwww);

            if ($purchase->status != 3 && $purchase->status != 4) {

                $customers   = Customer::where('created_by', \Auth::user()->creatorId())
                ->get() ->mapWithKeys(function ($customer) {
                    $displayName = $customer->name . ' - ' . ($customer->contact ?? '');
                    return [$customer->id => $displayName];
                });
               $customers->prepend('Select Customer', '');

              $purchase_number = \Auth::user()->purchaseNumberFormat($this->purchaseNumber());
            $venders     = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $venders->prepend('Select Vender', '');

            $customers      = Customer::where('created_by', \Auth::user()->creatorId())
                ->get()
                ->mapWithKeys(function ($customer) {
                    $displayName = $customer->name . ' - ' . ($customer->contact ?? '');
                    return [$customer->id => $displayName];
                });
            $customers->prepend('Select Customer', '');



            // $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $customers->prepend('Select Customer', 'Select Customer');

            $warehouse     = warehouse::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $warehouse->prepend('Select Warehouse', '');

            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Cargo')->get()->pluck('name', 'id');

            $product_services->prepend('Select Cargo Type', 'Select Cargo Type');

            $topup = Vender::where('created_by', \Auth::user()->creatorId())->where('tax_number','Airline')->orWhere('tax_number', 'Other')->get()->pluck('name', 'id');
            $topup->prepend('Select Company', 'Select Company');

            $origin = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $origin->prepend('Select Origin', '');

            $destination = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $destination->prepend('Select Destination', '');


                return view('purchase.edit', compact('venders', 'product_services', 'purchase', 'warehouse','purchase_number','customers','origin','destination','topup'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {


        if(\Auth::user()->can('edit purchase'))
        {

            if($purchase->created_by == \Auth::user()->creatorId())
            {
                // Simple validation like InvoiceController
                $validator = \Validator::make(
                    $request->all(), [
                        'vender_id' => 'required',
                        'customer_id' => 'required',
                        'purchase_date' => 'required',
                        'items' => 'required',
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first())->withInput();
                }

                // Get items - repeater sends them as array
                $products = $request->items;

                // Ensure items is an array and not empty
                if(empty($products) || !is_array($products)) {
                    return redirect()->back()->with('error', __('Please add at least one item.'))->withInput();
                }

                // Filter out any empty items (safety check)
                $products = array_filter($products, function($item) {
                    return is_array($item) &&
                           isset($item['item']) && !empty($item['item']) &&
                           isset($item['quantity']) && !empty($item['quantity']) &&
                           isset($item['price']) && !empty($item['price']);
                           isset($item['other_frieght']) && !empty($item['other_frieght']);
                           isset($item['agent_fee']) && !empty($item['agent_fee']);
                });

                if(empty($products) || count($products) == 0) {
                    return redirect()->back()->with('error', __('Please add at least one item with product, quantity, and price.'))->withInput();
                }

                // Re-index array to ensure sequential keys
                $products = array_values($products);

                $purchase->vender_id      = $request->vender_id;
                $purchase->customer_id      = $request->customer_id;
                $purchase->origin         = $request->origin;
                $purchase->destination    = $request->destination;
                $purchase->purchase_date      = $request->purchase_date;
                $purchase->save();

                // Use filtered products

                for($i = 0; $i < count($products); $i++)
                {
                    // Check if this is an existing item (has id) or new item
                    $purchaseProduct = null;
                    if(isset($products[$i]['id']) && !empty($products[$i]['id'])) {
                        $purchaseProduct = PurchaseProduct::find($products[$i]['id']);
                    }

                    if($purchaseProduct == null)
                    {
                        $purchaseProduct             = new PurchaseProduct();
                        $purchaseProduct->purchase_id    = $purchase->id;

                        Utility::total_quantity('plus',$products[$i]['quantity'],$products[$i]['item']);
                        $old_qty=0;
                    }
                    else{
                        $old_qty = $purchaseProduct->quantity;
                        Utility::total_quantity('minus',$purchaseProduct->quantity,$purchaseProduct->product_id);
                    }

                    if(isset($products[$i]['item']))
                    {
                        $purchaseProduct->product_id = $products[$i]['item'];
                    }

                    $purchaseProduct->quantity    = $products[$i]['quantity'];
                    $purchaseProduct->tax         = isset($products[$i]['tax']) ? $products[$i]['tax'] : '';
                    $purchaseProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $purchaseProduct->price       = $products[$i]['price'];
                    $purchaseProduct->other_frieght = isset($products[$i]['other_frieght']) ? $products[$i]['other_frieght'] : 0;
                    $purchaseProduct->agent_fee = isset($products[$i]['agent_fee']) ? $products[$i]['agent_fee'] : 0;
                    $purchaseProduct->description = isset($products[$i]['description']) ? $products[$i]['description'] : '';


                    // Calculate subtotal based on your formula

                    $purchaseProduct->subtotal = ($products[$i]['quantity'] * $products[$i]['price'])
                                    + (isset($products[$i]['other_frieght']) ? $products[$i]['other_frieght'] : 0)
                                    + (isset($products[$i]['agent_fee']) ? $products[$i]['agent_fee'] : 0);


                    $purchaseProduct->save();

                    // Update quantity for existing items
                    if(isset($products[$i]['id']) && !empty($products[$i]['id']) && $products[$i]['id'] > 0) {
                        Utility::total_quantity('plus',$products[$i]['quantity'],$purchaseProduct->product_id);
                    }

                    //Product Stock Report - delete old reports first, then add new ones
                    if($i == 0) {
                        // Only delete once, on first iteration
                        StockReport::where('type','=','purchase')->where('type_id','=',$purchase->id)->delete();
                    }

                    if(isset($products[$i]['item']) && !empty($products[$i]['item'])) {
                        $type='purchase';
                        $type_id = $purchase->id;
                        $description=$products[$i]['quantity'].'  '.__(' quantity add in purchase').' '. \Auth::user()->purchaseNumberFormat($purchase->purchase_id);
                        Utility::addProductStock( $products[$i]['item'],$products[$i]['quantity'],$type,$description,$type_id);
                    }

                    //Warehouse Stock Report
                    $new_qty = $purchaseProduct->quantity;
                    $total_qty= $new_qty - $old_qty;
                    // if(isset($products[$i]['item'])){

                    //     Utility::addWarehouseStock($products[$i]['item'],$total_qty,$request->warehouse_id);
                    // }

                }

                return redirect()->route('purchase.index')->with('success', __('Purchase successfully updated.'));
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        if(\Auth::user()->can('delete purchase'))
        {
            if($purchase->created_by == \Auth::user()->creatorId())
            {
                $purchase_products = PurchaseProduct::where('purchase_id',$purchase->id)->get();

                $purchasepayments = $purchase->payments;
                foreach($purchasepayments as $key => $value)
                {
                    $purchasepayment = PurchasePayment::find($value->id)->first();
                    $purchasepayment->delete();
                }

                foreach($purchase_products as $purchase_product)
                {
                    $warehouse_qty = WarehouseProduct::where('warehouse_id',$purchase->warehouse_id)->where('product_id',$purchase_product->product_id)->first();

                    $warehouse_transfers = WarehouseTransfer::where('product_id',$purchase_product->product_id)->where('from_warehouse',$purchase->warehouse_id)->get();
                    foreach ($warehouse_transfers as $warehouse_transfer)
                    {
                        $temp = WarehouseProduct::where('warehouse_id',$warehouse_transfer->to_warehouse)->first();
                        if($temp)
                        {
                            $temp->quantity = $temp->quantity - $warehouse_transfer->quantity;
                            if($temp->quantity > 0)
                            {
                                $temp->save();
                            }
                            else
                            {
                                $temp->delete();
                            }

                        }
                    }
                    if(!empty($warehouse_qty))
                    {
                        $warehouse_qty->quantity = $warehouse_qty->quantity - $purchase_product->quantity;
                        if( $warehouse_qty->quantity > 0)
                        {
                            $warehouse_qty->save();
                        }
                        else
                        {
                            $warehouse_qty->delete();
                        }
                    }
                    $product_qty = ProductService::where('id',$purchase_product->product_id)->first();

                    if(!empty($product_qty))
                    {
                        $product_qty->quantity = $product_qty->quantity - $purchase_product->quantity;
                        $product_qty->save();
                    }
                    $purchase_product->delete();

                }

                $purchase->delete();
                PurchaseProduct::where('purchase_id', '=', $purchase->id)->delete();


                return redirect()->route('purchase.index')->with('success', __('Purchase successfully deleted.'));
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


    function purchaseNumber()
    {
        $latest = Purchase::where('created_by', '=', \Auth::user()->creatorId())->latest('purchase_id')->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->purchase_id + 1;
    }
    public function sent($id)
    {
        if(\Auth::user()->can('send purchase'))
        {
            $setings = Utility::settings();

            if ($setings['customer_purchase_sent'] == 1) {
            $purchase            = Purchase::where('id', $id)->first();
            $purchase->send_date = date('Y-m-d');
            $purchase->status    = 2;
            $purchase->save();

            $customer = Customer::where('id', $purchase->customer_id)->first();
            $purchase->name = !empty($customer) ? $customer->name : '';
            $purchase->purchase = \Auth::user()->purchaseNumberFormat($purchase->purchase_id);

            $vender = Vender::where('id', $purchase->vender_id)->first();

            $purchase->name = !empty($vender) ? $vender->name : '';
            $purchase->purchase = \Auth::user()->purchaseNumberFormat($purchase->purchase_id);

            $purchaseId    = Crypt::encrypt($purchase->id);
            $purchase->url = route('purchase.pdf', $purchaseId);

            $purchase_product = PurchaseProduct::where('purchase_id', $purchase->id)->get();
                foreach ($purchase_product as $purchase_product) {
                    // Only process transactions if product_id exists
                    if (empty($purchase_product->product_id)) {
                        continue;
                    }

            $product = ProductService::find($purchase_product->product_id);
                     if (!$product) {
                        continue;
                     }
            $totalTaxPrice = 0;
                    if($purchase_product->tax != null && $purchase_product->tax != '')
                    {
                        $taxValue = trim($purchase_product->tax);
                        // Check if tax contains commas (tax IDs) or is numeric (tax amount)
                        if (strpos($taxValue, ',') !== false) {
                            // Tax contains tax IDs (comma-separated) - process them for product purchases
                            $taxes = \App\Models\Utility::tax($taxValue);
                            if (is_array($taxes)) {
                                foreach ($taxes as $tax) {
                                    // Check if tax object exists and has rate property before accessing it
                                    if ($tax !== null && is_object($tax) && isset($tax->rate) && $tax->rate !== null) {
                                        $taxPrice = \App\Models\Utility::taxRate($tax->rate, $purchase_product->price, 1, 0);
                                        $totalTaxPrice += $taxPrice;
                                    }
                                }
                            }
                        } else {
                            // Tax is a numeric value (tax amount for ticket purchases)
                            // Price already includes tax, so don't add it again
                            $totalTaxPrice = 0;
                        }
                    }

                    // For ticket purchases: price already includes tax (fare + tax + cust_commission - discount)
                    // For product purchases: add calculated tax to price
                    $itemAmount1 = $purchase_product->quantity * $purchase_product->price;


                    $itemAmount = $itemAmount1 + $purchase_product->other_frieght + $purchase_product->agent_fee - $purchase_product->discount;


                    Utility::updateUserBalance('customer', $purchase->customer_id, $itemAmount, 'credit');

                    $data = [
                        'account_id'         => $product->sale_chartaccount_id,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $itemAmount,
                        'reference'          => 'Cargo',
                        'reference_id'       => $purchase->id,
                        'reference_sub_id'   => $product->id,
                        'vender_id'          => '0',
                        'customer_id'        => '0',
                        'date'               => $purchase->purchase_date,
                    ];
                    Utility::addTransactionLines($data);



                    $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $itemAmount,
                        'reference'          => 'Cargo',
                        'reference_id'       => $purchase->id,
                        'reference_sub_id'   => $product->id,
                        'vender_id'          => '0',
                        'customer_id'        => $purchase->customer_id,
                        'date'               => $purchase->purchase_date,
                    ];
                    Utility::addTransactionLines($data);

                    $itemAmount1 = $purchase_product->quantity * $purchase_product->price;

                    $itemAmount = $itemAmount1 + $purchase_product->other_frieght  - $purchase_product->discount;

                    Utility::updateUserBalance('vender', $purchase->vender_id, $itemAmount, 'credit');



                        $data = [
                            'account_id' => $product->expense_chartaccount_id,
                            'transaction_type'   => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Cargo',
                            'reference_id'       => $purchase->id,
                            'reference_sub_id'   => $product->id,
                            'vender_id'          => '0',
                            'customer_id'        => '0',
                            'date' => $purchase->purchase_date,
                        ];
                        Utility::addTransactionLines($data);

                        $account = ChartOfAccount::where('name', 'Accounts Payable')->where('created_by', \Auth::user()->creatorId())->first();



                        $data = [
                            'account_id'         => !empty($account) ? $account->id : 0,
                            'transaction_type'   => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Cargo',
                            'reference_id'       => $purchase->id,
                            'reference_sub_id'   => $product->id,
                            'vender_id'          => $purchase->vender_id,
                            'customer_id'        => '0',
                            'date' => $purchase->purchase_date,
                        ];
                        Utility::addTransactionLines($data);
                }

            $customerArr = [

                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'purchase_name' => $customer->name,
                    'purchase_number' => $purchase->purchase,
                    'purchase_url' => $purchase->url,

                ];
                $resp = Utility::sendEmailTemplate('customer_purchase_sent', [$customer->id => $customer->email], $customerArr);


            return redirect()->back()->with('success', __('Purchase successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
          } else {
                return redirect()->back()->with('error', 'Purchase not found');
            }


        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function resent($id)
    {

        if(\Auth::user()->can('send purchase'))
        {
            $purchase = Purchase::where('id', $id)->first();

            $vender = Vender::where('id', $purchase->vender_id)->first();

            $purchase->name = !empty($vender) ? $vender->name : '';
            $purchase->purchase = \Auth::user()->purchaseNumberFormat($purchase->purchase_id);

            $purchaseId    = Crypt::encrypt($purchase->id);
            $purchase->url = route('purchase.pdf', $purchaseId);

        return redirect()->back()->with('success', __('Bill successfully sent.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function purchase($purchase_id)
    {

        $settings = Utility::settings();
        try{
            $purchaseId   = Crypt::decrypt($purchase_id);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }

        $purchase  = Purchase::where('id', $purchaseId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $purchase->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach($purchase->items as $product)
        {

            $item              = new \stdClass();
            $item->name        = !empty($product->product) ? $product->product->name : '';
            $item->quantity    = $product->quantity;
            $item->tax         = $product->tax;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;
            $item->other_freight = $product->other_freight;
            $item->agent_fee = $product->agent_fee;

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

        $purchase->itemData      = $items;
        $purchase->totalTaxPrice = $totalTaxPrice;
        $purchase->totalQuantity = $totalQuantity;
        $purchase->totalRate     = $totalRate;
        $purchase->totalDiscount = $totalDiscount;
        $purchase->taxesData     = $taxesData;


        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));


        if($purchase)
        {
            $color      = '#' . $settings['purchase_color'];
            $font_color = Utility::getFontColor($color);

            return view('purchase.templates.' . $settings['purchase_template'], compact('purchase', 'color', 'settings', 'vendor', 'img', 'font_color'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function previewPurchase($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        $purchase     = new Purchase();

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


        $customer                   = new \stdClass();
        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Name>';
        $customer->billing_country  = '<Country>';
        $customer->billing_state    = '<State>';
        $customer->billing_city     = '<City>';
        $customer->billing_phone    = '<Customer Phone Number>';
        $customer->billing_zip      = '<Zip>';
        $customer->billing_address  = '<Address>';

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

        $purchase->purchase_id    = 1;
        $purchase->issue_date = date('Y-m-d H:i:s');
        $purchase->itemData   = $items;

        $purchase->totalTaxPrice = 60;
        $purchase->totalQuantity = 3;
        $purchase->totalRate     = 300;
        $purchase->totalDiscount = 10;
        $purchase->taxesData     = $taxesData;
        $purchase->created_by     = $objUser->creatorId();

        $preview      = 1;
        $color        = '#' . $color;
        $font_color   = Utility::getFontColor($color);

        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($purchase->created_by);
        $purchase_logo = $settings_data['purchase_logo'];

        if(isset($purchase_logo) && !empty($purchase_logo))
        {
            $img = Utility::get_file('purchase_logo/') . $purchase_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }

        return view('purchase.templates.' . $template, compact('purchase', 'preview', 'color', 'img', 'settings', 'vendor', 'font_color'));
    }

    public function savePurchaseTemplateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['purchase_template']) && (!isset($post['purchase_color']) || empty($post['purchase_color'])))
        {
            $post['purchase_color'] = "ffffff";
        }


        if($request->purchase_logo)
        {
            $dir = 'purchase_logo/';
            $purchase_logo = \Auth::user()->id . '_purchase_logo.png';
            $validation =[
                'mimes:'.'png',
                'max:'.'20480',
            ];
            $path = Utility::upload_file($request,'purchase_logo',$purchase_logo,$dir,$validation);
            if($path['flag']==0)
            {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['purchase_logo'] = $purchase_logo;
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

        return redirect()->back()->with('success', __('Purchase Setting updated successfully'));
    }

    public function items(Request $request)
    {

        $items = PurchaseProduct::where('purchase_id', $request->purchase_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function purchaseLink($purchaseId)
    {
        try {
            $id       = Crypt::decrypt($purchaseId);
            $purchase = Purchase::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Purchase Not Found.'));
        }

        if(!empty($purchase))
        {
            $user_id        = $purchase->created_by;
            $user           = User::find($user_id);
            $purchasePayment = PurchasePayment::where('purchase_id', $purchase->id)->first();
            $vendor = $purchase->vender;
            $iteams   = $purchase->items;

            return view('purchase.customer_bill', compact('purchase', 'vendor', 'iteams','purchasePayment','user'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function payment($purchase_id)
    {
        if(\Auth::user()->can('create payment purchase'))
        {
            $purchase    = Purchase::where('id', $purchase_id)->first();
            $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('purchase.payment', compact('venders', 'categories', 'accounts', 'purchase'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));

        }
    }

    public function createPayment(Request $request, $purchase_id)
    {
        if(\Auth::user()->can('create payment purchase'))
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

            $purchasePayment                 = new PurchasePayment();
            $purchasePayment->purchase_id        = $purchase_id;
            $purchasePayment->date           = $request->date;
            $purchasePayment->amount         = $request->amount;
            $purchasePayment->account_id     = $request->account_id;
            $purchasePayment->payment_method = 0;
            $purchasePayment->reference      = $request->reference;
            $purchasePayment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $request->add_receipt->storeAs('uploads/payment', $fileName);
                $purchasePayment->add_receipt = $fileName;
            }
            $purchasePayment->save();

            $purchase  = Purchase::where('id', $purchase_id)->first();
            $due   = $purchase->getDue();
            $total = $purchase->getTotal();

            if($purchase->status == 0)
            {
                $purchase->send_date = date('Y-m-d');
                $purchase->save();
            }

            if($due <= 0)
            {
                $purchase->status = 4;
                $purchase->save();
            }
            else
            {
                $purchase->status = 3;
                $purchase->save();
            }
            $purchasePayment->user_id    = $purchase->vender_id;
            $purchasePayment->user_type  = 'Vender';
            $purchasePayment->type       = 'Partial';
            $purchasePayment->created_by = \Auth::user()->id;
            $purchasePayment->payment_id = $purchasePayment->id;
            $purchasePayment->category   = 'Bill';
            $purchasePayment->account    = $request->account_id;
            Transaction::addTransaction($purchasePayment);

            $vender = Vender::where('id', $purchase->vender_id)->first();

            $payment         = new PurchasePayment();
            $payment->name   = $vender['name'];
            $payment->method = '-';
            $payment->date   = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->bill   = 'bill ' . \Auth::user()->purchaseNumberFormat($purchasePayment->purchase_id);

            // Utility::updateUserBalance('vendor', $purchase->vender_id, $request->amount, 'debit');

            // Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

            // Send Email
            $setings = Utility::settings();
            if($setings['new_bill_payment'] == 1)
            {

                $vender = Vender::where('id', $purchase->vender_id)->first();
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

                $customer = Customer::where('id', $purchase->customer_id)->first();

                $bank = BankAccount::where('id', $purchasePayment->account_id)->first();
                $chartAccountId = $bank->chart_account_id;

                $data = [
                        'account_id'         => $chartAccountId,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $purchasePayment->amount,
                        'reference'          => 'Cargo Payment',
                        'reference_id'       => $purchasePayment->payment_id,
                        'reference_sub_id'   => $purchasePayment->payment_id,
                        'vender_id'          => '0',
                        'customer_id'        => '0',
                        'date'               => $purchasePayment->date,
                    ];
                    Utility::addTransactionLines($data);



                    $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $purchasePayment->amount,
                        'reference'          => 'Cargo Payment',
                        'reference_id'       => $purchasePayment->payment_id,
                        'reference_sub_id'   => $purchasePayment->payment_id,
                        'vender_id'          => '0',
                        'customer_id'        => $customer->customer_id,
                        'date'               => $purchasePayment->date,
                    ];
                    Utility::addTransactionLines($data);

                  //  $itemAmount1 = $purchase_product->quantity * $purchase_product->price;

                  //  $itemAmount = $itemAmount1 + $purchase_product->other_frieght  - $purchase_product->discount;

                  // Utility::updateUserBalance('vender', $purchase->vender_id, $itemAmount, 'credit');


                return redirect()->back()->with('success', __('Payment successfully added.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

            return redirect()->back()->with('success', __('Payment successfully added.'));
        }

    }

    public function paymentDestroy(Request $request, $purchase_id, $payment_id)
    {

        if(\Auth::user()->can('delete payment purchase'))
        {
            $payment = PurchasePayment::find($payment_id);
            PurchasePayment::where('id', '=', $payment_id)->delete();

            $purchase = Purchase::where('id', $purchase_id)->first();

            $due   = $purchase->getDue();
            $total = $purchase->getTotal();

            if($due > 0 && $total != $due)
            {
                $purchase->status = 3;

            }
            else
            {
                $purchase->status = 2;
            }

            // Utility::updateUserBalance('vendor', $purchase->vender_id, $payment->amount, 'credit');
            // Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            $purchase->save();
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

    public function vender(Request $request)
    {
        $vender = Vender::where('id', '=', $request->id)->first();

        return view('purchase.vender_detail', compact('vender'));
    }
    public function product(Request $request)
    {


        $data['product']     = $product = ProductService::find($request->product_id);
        $data['unit']        = !empty($product->unit) ? $product->unit->name : '';
        $data['taxRate']     = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes']       = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice           = $product->purchase_price ?? 0;
        $quantity            = 1;
        $taxPrice            = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function productDestroy(Request $request)
    {

        if(\Auth::user()->can('delete purchase'))
        {

            $res = PurchaseProduct::where('id', '=', $request->id)->first();

            $purchase = Purchase::where('created_by', '=', \Auth::user()->creatorId())->first();
            $warehouse_id= $purchase->warehouse_id;

            $ware_pro =WarehouseProduct::where('warehouse_id',$warehouse_id)->where('product_id',$res->product_id)->first();

            $qty = $ware_pro->quantity;

            if($res->quantity == $qty || $res->quantity > $qty)
            {
                $ware_pro->delete();
            }
            elseif($res->quantity < $qty)
            {
                $ware_pro->quantity =  $qty - $res->quantity;
                $ware_pro->save();

            }
            PurchaseProduct::where('id', '=', $request->id)->delete();


            return response()->json(['status' => true, 'message' => __('Purchase product successfully deleted.')]);

        }
        else
        {
            return response()->json(['status' => false, 'message' => __('Permission denied.')]);
        }
    }








}
