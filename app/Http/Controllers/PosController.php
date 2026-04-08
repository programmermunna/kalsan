<?php

namespace App\Http\Controllers;

use App\Mail\SelledInvoice;
use App\Models\Customer;
use App\Models\Pos;
use App\Models\PosPayment;
use App\Models\PosProduct;
use App\Models\ProductService;
use App\Models\StockReport;
use App\Models\User;
use App\Models\Utility;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = 0)
    {
        
        if (Auth::user()->can('manage pos')) {
            // session()->forget('pos');
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'name');
            $customers->prepend('Walk-in-customer', '');
            $warehouses = warehouse::select('*', \DB::raw("CONCAT(name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user = Auth::user();
            $details = [
                'pos_id' => $user->posNumberFormat($this->invoicePosNumber()),
                'customer' => $customers != null ? $customers->toArray() : [],
                'user' => $user != null ? $user->toArray() : [],
                'date' => date('Y-m-d'),
                'pay' => 'show',
            ];

            if ($id != 0) {

                $quotation = Quotation::find($id);

                $customerId = $quotation->customer_id;
                $customerId = Customer::find($customerId);
                $customer = $customerId->name;

                $warehouseId = $quotation->warehouse_id;

                $quotationProduct = QuotationProduct::where('quotation_id', $id)->get();

                foreach ($quotationProduct as $value) {
                    $products = Quotation::quotationProduct($value);
                }
            } else {
                $customer = '';
                $warehouseId = '';
            }

            return view('pos.index', compact('customers', 'warehouses', 'details', 'customer', 'warehouseId', 'id'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $sess = session()->get('pos');

        if (Auth::user()->can('manage pos') && isset($sess) && !empty($sess) && count($sess) > 0) {
            $user = Auth::user();

            $settings = Utility::settings();


            $customer = Customer::where('name', '=', $request->vc_name)->where('created_by', $user->creatorId())->first();
            $warehouse = warehouse::where('id', '=', $request->warehouse_name)->where('created_by', $user->creatorId())->first();

            $details = [
                'pos_id' => $user->posNumberFormat($this->invoicePosNumber()),
                'customer' => $customer != null ? $customer->toArray() : [],
                'warehouse' => $warehouse != null ? $warehouse->toArray() : [],
                'user' => $user != null ? $user->toArray() : [],
                'date' => date('Y-m-d'),
                'pay' => 'show',
            ];

            if (!empty($details['customer']))
            {
                $warehousedetails = '<h7 class="text-dark">' . ucfirst($details['warehouse']['name'])  . '</p></h7>';
                $details['customer']['billing_state'] = $details['customer']['billing_state'] != '' ? ", " . $details['customer']['billing_state'] : '';
                $details['customer']['shipping_state'] = $details['customer']['shipping_state'] != '' ? ", " . $details['customer']['shipping_state'] : '';

                $customerdetails = '<h6 class="text-dark">' . ucfirst($details['customer']['name']) . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_phone'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_city'] . $details['customer']['billing_state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_zip'] . '</p></h6>';

                $shippdetails = '<h6 class="text-dark"><b>' . ucfirst($details['customer']['name']) . '</b>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_phone'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_city'] . $details['customer']['shipping_state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_zip'] . '</p></h6>';

            }
            else {
                $customerdetails = '<h2 class="h6"><b>' . __('Walk-in Customer') . '</b><h2>';
                $warehousedetails = '<h7 class="text-dark">' . ucfirst($details['warehouse']['name'])  . '</p></h7>';
                $shippdetails = '-';

            }


            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails = '<h6 class="text-dark"><b>' . ucfirst($details['user']['name']) . ' </b> <h2  class="font-weight-normal">' . '<p class="m-0 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_country'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_zipcode'] . '</p></h2>';

            $details['customer']['details'] = $customerdetails;
            $details['warehouse']['details'] = $warehousedetails;
//
            $details['customer']['shippdetails'] = $shippdetails;

            $details['user']['details'] = $userdetails;

            $mainsubtotal = 0;
            $sales        = [];

            foreach ($sess as $key => $value) {


                $subtotal = $value['price'] * $value['quantity'];
                $tax      = ($subtotal * $value['tax']) / 100;
                $sales['data'][$key]['name']       = $value['name'];
                $sales['data'][$key]['quantity']   = $value['quantity'];
                $sales['data'][$key]['price']      = Auth::user()->priceFormat($value['price']);
                $sales['data'][$key]['tax']        = $value['tax'] . '%';
                $sales['data'][$key]['product_tax']        = $value['product_tax'];
                $sales['data'][$key]['tax_amount'] = Auth::user()->priceFormat($tax);
                $sales['data'][$key]['subtotal']   = Auth::user()->priceFormat($value['subtotal']);
                $mainsubtotal                      += $value['subtotal'];
            }

            $discount=!empty($request->discount)?$request->discount:0;
            $sales['discount'] = Auth::user()->priceFormat($discount);
            $total= $mainsubtotal-$discount;
            $sales['sub_total'] = Auth::user()->priceFormat($mainsubtotal);
            $sales['total'] = Auth::user()->priceFormat($total);


            return view('pos.show', compact('sales', 'details'));
        } else {
            return response()->json(
                [
                    'error' => __('Add some products to cart!'),
                ],
                '404'
            );
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
        $discount=$request->discount;

        if (Auth::user()->can('manage pos')) {

            if($request->quotation_id != 0)
            {
                $quotation = Quotation::where('id', $request->quotation_id)->first();
                $quotation->is_converted = 1;
                $quotation->save();
            }

            $user_id = Auth::user()->creatorId();
            $customer_id      = Customer::customer_id($request->vc_name);
            $warehouse_id      = warehouse::warehouse_id($request->warehouse_name);
            $pos_id       = $this->invoicePosNumber();
            $sales            = session()->get('pos');

            if (isset($sales) && !empty($sales) && count($sales) > 0) {
                $result = DB::table('pos')->where('pos_id', $pos_id)->where('created_by', $user_id)->get();
                if (count($result) > 0) {
                    return response()->json(
                        [
                            'code' => 200,
                            'success' => __('Payment is already completed!'),
                        ]
                    );
                } else {
                    $pos = new Pos();
                    $pos->pos_id       = $pos_id;
                    $pos->customer_id      = $customer_id;
                    $pos->warehouse_id      = $request->warehouse_name;
                    $pos->created_by       = $user_id;
                    $pos->save();

                    if($request->quotation_id != 0)
                    {
                                        $quotation->converted_pos_id = $pos->id;
                                        $quotation->save();
                                    }

                    foreach ($sales as $key => $value) {
                        $product_id = $value['id'];

                        $product = ProductService::whereId($product_id)->where('created_by', $user_id)->first();

                        $original_quantity = ($product == null) ? 0 : (int)$product->quantity;

                        $product_quantity = $original_quantity - $value['quantity'];


                        if ($product != null && !empty($product)) {
                            ProductService::where('id', $product_id)->update(['quantity' => $product_quantity]);
                        }

                        $tax_id = ProductService::tax_id($product_id);


                        $positems = new PosProduct();
                        $positems->pos_id    = $pos->id;
                        $positems->product_id = $product_id;
                        $positems->price      = $value['price'];
                        $positems->quantity   = $value['quantity'];
                        $positems->tax       = $tax_id;
                        $positems->discount        = $discount;
                        $positems->save();

                        Utility::warehouse_quantity('minus',$positems->quantity,$positems->product_id,$request->warehouse_name);

                        //Product Stock Report
                        $type='pos';
                        $type_id = $pos->id;
                        StockReport::where('type','=','pos')->where('type_id' ,'=', $pos->id)->delete();
                        $description=$positems->quantity.'  '.__(' quantity sold in pos').' '. \Auth::user()->posNumberFormat($pos->pos_id);
                        Utility::addProductStock( $positems->product_id,$positems->quantity,$type,$description,$type_id);

                    }

                    $posPayment                 = new PosPayment();
                    $posPayment->pos_id          =$pos->id;
                    $posPayment->date           = $request->date;

                    $mainsubtotal = 0;
                    $sales        = [];

                    $sess = session()->get('pos');

                    foreach ($sess as $key => $value) {
                        $subtotal = $value['price'] * $value['quantity'];
                        $tax      = ($subtotal * $value['tax']) / 100;
                        $sales['data'][$key]['price']      = Auth::user()->priceFormat($value['price']);
                        $sales['data'][$key]['tax']        = $value['tax'] . '%';
                        $sales['data'][$key]['tax_amount'] = Auth::user()->priceFormat($tax);
                        $sales['data'][$key]['subtotal']   = Auth::user()->priceFormat($value['subtotal']);
                        $mainsubtotal                      += $value['subtotal'];
                    }
                    $amount = $mainsubtotal;
                    $posPayment->amount         = $amount;
                    $total= $mainsubtotal - $discount;
                    $posPayment->discount         = $discount;
                    $posPayment->discount_amount       = $total;
                    $posPayment->save();

                    session()->forget('pos');

                    return response()->json(
                        [
                            'code' => 200,
                            'success' => __('Payment completed successfully!'),
                        ]
                    );
                }
            } else {
                return response()->json(
                    [
                        'code' => 404,
                        'success' => __('Items not found!'),
                    ]
                );
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($ids)
    {

        if(\Auth::user()->can('manage pos'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Pos Not Found.'));
            }
            $id   = Crypt::decrypt($ids);

            $pos = Pos::find($id);

            if($pos->created_by == \Auth::user()->creatorId())
            {
                $posPayment = PosPayment::where('pos_id', $pos->id)->first();

                $customer             = $pos->customer;
                $iteams               = $pos->items;

                return view('pos.view', compact('pos', 'customer','iteams','posPayment'));
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

    function invoicePosNumber()
    {
        if (Auth::user()->can('manage pos')) {
            $latest = Pos::where('created_by', '=', \Auth::user()->creatorId())->latest('pos_id')->first();


            return $latest ? $latest->pos_id + 1 : 1;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function report()
    {
        if(\Auth::user()->can('manage pos'))
        {

            $posPayments = Pos::where('created_by', '=', \Auth::user()->creatorId())->with(['customer','warehouse'])->get();
            return view('pos.report',compact('posPayments'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    function barcode()
    {
        if(\Auth::user()->can('manage pos'))
        {
            $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
            $barcode  = [
                'barcodeType' => Auth::user()->barcodeType() ,
                'barcodeFormat' => Auth::user()->barcodeFormat(),
            ];

            return view('pos.barcode',compact('productServices','barcode'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    public function setting()
    {
        if(\Auth::user()->can('manage pos'))
        {
            $settings                = Utility::settings();

            return view('pos.setting',compact('settings'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

    public function BarcodesettingStore(Request $request)
    {
        $request->validate(
                [

                    'barcode_type' => 'required',
                    'barcode_format' => 'required',
                ]
            );

        $post['barcode_type'] = $request->barcode_type;
        $post['barcode_format'] = $request->barcode_format;

        foreach($post as $key => $data)
        {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', $arr
            );
        }
        return redirect()->back()->with('success', 'Barcode setting successfully updated.');

    }

    public function printBarcode()
    {
        if(\Auth::user()->can('manage pos'))
        {
            $warehouses = warehouse::select('*', \DB::raw("CONCAT(name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');


            return view('pos.print',compact('warehouses'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    public function getproduct(Request $request)
    {
        if($request->warehouse_id == 0)
        {
            $productServices = WarehouseProduct::where('product_id', '=', $request->warehouse_id)->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        else
        {
            $productServicesId = WarehouseProduct::where('created_by', '=', \Auth::user()->creatorId())->where('warehouse_id', $request->warehouse_id)->get()->pluck('product_id')->toArray();
            $productServices = ProductService::whereIn('id', $productServicesId )->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($productServices);
    }

    public function receipt(Request $request)
    {
        if(!empty($request->product_id))
        {
            $productServices = ProductService::whereIn('id',$request->product_id)->get();
            $quantity  = $request->quantity;
            $barcode  = [
                'barcodeType' => Auth::user()->barcodeType() == '' ? 'code128' : Auth::user()->barcodeType(),
                'barcodeFormat' => Auth::user()->barcodeFormat() == '' ? 'css' : Auth::user()->barcodeFormat(),
            ];
        }
        else
        {
            return redirect()->back()->with('error', 'Product is required.');

        }

        return view('pos.receipt',compact('productServices','barcode','quantity'));

    }

    public function cartdiscount(Request $request)
    {

        if($request->discount){
            $sess = session()->get('pos');
            $subtotal = !empty($sess)?array_sum(array_column($sess, 'subtotal')):0;
            $discount = $request->discount;
            $total = $subtotal - $discount;
            $total = \Auth::user()->priceFormat($total);

        }else{
            $sess = session()->get('pos');
            $subtotal = !empty($sess)?array_sum(array_column($sess, 'subtotal')):0;
            $discount = 0;
            $total = $subtotal - $discount;
            $total = \Auth::user()->priceFormat($total);
        }

        // session()->put('pos-discount', $discount);

        return response()->json(['total' => $total], '200');

    }

    public function pos($pos_id)
    {
        $settings = Utility::settings();
        try{
            $posId   = Crypt::decrypt($pos_id);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        $pos  = Pos::where('id', $posId)->first();

        $posPayment = PosPayment::where('pos_id', $pos->id)->first();



        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $pos->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $customer = $pos->customer;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach($pos->items as $product)
        {
            $item              = new \stdClass();
            $item->name        = !empty($product->product) ? $product->product->name : '';
            $item->quantity    = $product->quantity;
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
                    $taxPrice      = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate . '%';
                    $itemTax['price'] = Utility::priceFormat($settings, $taxPrice);
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

        $pos->itemData      = $items;
        $pos->totalTaxPrice = $totalTaxPrice;
        $pos->totalQuantity = $totalQuantity;
        $pos->totalRate     = $totalRate;
        $pos->totalDiscount = $totalDiscount;
        $pos->taxesData     = $taxesData;


        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $pos_logo = Utility::getValByName('pos_logo');
        if(isset($pos_logo) && !empty($pos_logo))
        {
            $img = Utility::get_file('pos_logo/') . $pos_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }



        if($pos)
        {
            $color      = '#' . $settings['pos_color'];
            $font_color = Utility::getFontColor($color);

            return view('pos.templates.' . $settings['pos_template'], compact('pos','posPayment', 'color', 'settings', 'customer', 'img', 'font_color'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function previewPos($template, $color)
    {

        $objUser  = \Auth::user();
        $settings = Utility::settings();

        $pos     = new Pos();
        $posPayment     = new posPayment();
        $posPayment->amount=360;
        $posPayment->discount=100;


        $customer                   = new \stdClass();
        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Customer Name>';
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

        $pos->pos_id    = 1;

        $pos->issue_date = date('Y-m-d H:i:s');
        $pos->itemData   = $items;

        $pos->totalTaxPrice = 60;
        $pos->totalQuantity = 3;
        $pos->totalRate     = 300;
        $pos->totalDiscount = 10;
        $pos->taxesData     = $taxesData;
        $pos->created_by     = $objUser->creatorId();

        $preview      = 1;
        $color        = '#' . $color;
        $font_color   = Utility::getFontColor($color);

        $logo         = asset(Storage::url('uploads/logo/'));

        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($pos->created_by);
        $pos_logo = $settings_data['pos_logo'];

        if(isset($pos_logo) && !empty($pos_logo))
        {
            $img = Utility::get_file('pos_logo/') . $pos_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }


        return view('pos.templates.' . $template, compact('pos', 'preview', 'color', 'img', 'settings', 'customer', 'font_color','posPayment'));
    }



    public function savePosTemplateSettings(Request $request)
    {

        $post = $request->all();
        unset($post['_token']);

        if(isset($post['pos_template']) && (!isset($post['pos_color']) || empty($post['pos_color'])))
        {
            $post['pos_color'] = "ffffff";
        }


        if($request->pos_logo)
        {
            $dir = 'pos_logo/';
            $pos_logo = \Auth::user()->id . '_pos_logo.png';
            $validation =[
                'mimes:'.'png',
                'max:'.'20480',
            ];
            $path = Utility::upload_file($request,'pos_logo',$pos_logo,$dir,$validation);
            if($path['flag']==0)
            {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['pos_logo'] = $pos_logo;
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

        return redirect()->back()->with('success', __('POS Setting updated successfully'));
    }

    //for thermal print
    public function printView(Request $request)
    {

        $sess = session()->get('pos');

        $user = Auth::user();
        $settings = Utility::settings();

        $customer = Customer::where('name', '=', $request->vc_name)->where('created_by', $user->creatorId())->first();
        $warehouse = warehouse::where('id', '=', $request->warehouse_name)->where('created_by', $user->creatorId())->first();

        $details = [
            'pos_id' => $user->posNumberFormat($this->invoicePosNumber()),
            'customer' => $customer != null ? $customer->toArray() : [],
            'warehouse' => $warehouse != null ? $warehouse->toArray() : [],
            'user' => $user != null ? $user->toArray() : [],
            'date' => date('Y-m-d'),
            'pay' => 'show',
        ];



        if (!empty($details['customer']))
        {
            $warehousedetails = '<h7 class="text-dark">' . ucfirst($details['warehouse']['name'])  . '</p></h7>';
            $details['customer']['billing_state'] = $details['customer']['billing_state'] != '' ? ", " . $details['customer']['billing_state'] : '';
            $details['customer']['shipping_state'] = $details['customer']['shipping_state'] != '' ? ", " . $details['customer']['shipping_state'] : '';
            $customerdetails = '<h6 class="text-dark">' . ucfirst($details['customer']['name']) . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_phone'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_city'] . $details['customer']['billing_state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['billing_zip'] . '</p></h6>';
            $shippdetails = '<h6 class="text-dark"><b>' . ucfirst($details['customer']['name']) . '</b>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_phone'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_city'] . $details['customer']['shipping_state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['shipping_zip'] . '</p></h6>';

        }
        else {
            $customerdetails = '<h2 class="h6"><b>' . __('Walk-in Customer') . '</b><h2>';
            $warehousedetails = '<h7 class="text-dark">' . ucfirst($details['warehouse']['name'])  . '</p></h7>';
            $shippdetails = '-';

        }


        $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
        $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

        $userdetails = '<h6 class="text-dark"><b>' . ucfirst($details['user']['name']) . ' </b> <h2  class="font-weight-normal">' . '<p class="m-0 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_country'] . '</p>' . '<p class="m-0 font-weight-normal">' . $settings['company_zipcode'] . '</p></h2>';

        $details['customer']['details'] = $customerdetails;
        $details['warehouse']['details'] = $warehousedetails;
//
        $details['customer']['shippdetails'] = $shippdetails;

        $details['user']['details'] = $userdetails;

        $mainsubtotal = 0;
        $sales        = [];

        foreach ($sess as $key => $value) {

            $subtotal = $value['price'] * $value['quantity'];
            $tax      = ($subtotal * $value['tax']) / 100;
            $sales['data'][$key]['name']       = $value['name'];
            $sales['data'][$key]['quantity']   = $value['quantity'];
            $sales['data'][$key]['price']      = Auth::user()->priceFormat($value['price']);
            $sales['data'][$key]['tax']        = $value['tax'] . '%';
            $sales['data'][$key]['product_tax']        = $value['product_tax'];
            $sales['data'][$key]['tax_amount'] = Auth::user()->priceFormat($tax);
            $sales['data'][$key]['subtotal']   = Auth::user()->priceFormat($value['subtotal']);
            $mainsubtotal                      += $value['subtotal'];
        }

        $discount=!empty($request->discount)?$request->discount:0;
        $sales['discount'] = Auth::user()->priceFormat($discount);
        $total= $mainsubtotal-$discount;
        $sales['sub_total'] = Auth::user()->priceFormat($mainsubtotal);
        $sales['total'] = Auth::user()->priceFormat($total);

        //for barcode

        $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
        $barcode  = [
            'barcodeType' => Auth::user()->barcodeType() ,
            'barcodeFormat' => Auth::user()->barcodeFormat(),
        ];

        return view('pos.printview', compact('details', 'sales', 'customer','productServices','barcode'));

    }



}
