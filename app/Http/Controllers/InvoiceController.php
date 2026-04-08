<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Models\AddTransactionLine;
use App\Models\BankAccount;
use App\Models\Vender;
use App\Models\ChartOfAccount;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\CustomerCreditNotes;
use App\Models\CustomField;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceBankTransfer;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\Products;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\StockReport;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Traits\updateNotesStatus;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    use updateNotesStatus;

    public function index(Request $request)
    {

        if(\Auth::user()->type == 'client' || \Auth::user()->can('manage invoice'))
        {


            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('Select Customer', '');

           $users = User::where('created_by', '=', Auth::user()->creatorId())
                ->get()
                ->pluck('name', 'id');

            $users->prepend('Select User', '');


            $status = Invoice::$statues;

               $query = Invoice::query();

        // If user is not Admin, Cashier, or Accountant, filter by their own invoices
            if (!in_array(\Auth::user()->type, ['company', 'Cashier', 'Accountant'])) {
            $query->where('created_by', '=', \Auth::id());
            }

            if(!empty($request->customer))
            {
                $query->where('customer_id', '=', $request->customer);
            }
            if(count(explode('to', $request->issue_date)) > 1)
            {
                $date_range = explode(' to ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            }
            elseif(!empty($request->issue_date))
            {
                $date_range = [$request->issue_date , $request->issue_date];
                $query->whereBetween('issue_date', $date_range);
            }

            if(!empty($request->user)) {
                $query->where('created_by', '=', $request->user);
            }

            if ($request->status != null)
            {
                $query->where('status', '=', $request->status);
            }
            $invoices = $query->orderBy('id', 'desc')->paginate(10);

            return view('invoice.index', compact('invoices', 'customer', 'status', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($customerId = 0)
    {

        if(\Auth::user()->type == 'client' || \Auth::user()->can('create invoice'))
        {
            $customFields   = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
            $invoice_number = \Auth::user()->invoiceNumberFormat($this->invoiceNumber());
            $customers      = Customer::where('created_by', \Auth::user()->creatorId())
                ->get()
                ->mapWithKeys(function ($customer) {
                    $displayName = $customer->name . ' - ' . ($customer->contact ?? '');
                    return [$customer->id => $displayName];
                });
            $customers->prepend('Select Customer', '');
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type','Income')->get()->pluck('name', 'id');
            $category->prepend('Select ...', '');
            $topup = Vender::where('created_by', \Auth::user()->creatorId())->where('tax_number','Airline')->orWhere('tax_number', 'Other')->get()->pluck('name', 'id');
            $topup->prepend('Select Company', 'Select Company');

            // For invoice-level origin/destination we want to store/display text (route name),
            // so use the product "name" both as key and value.
            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $product_services->prepend('Select Origin', '');

            $destination = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
            $destination->prepend('Select Destination', '');

            return view('invoice.create', compact('customers', 'invoice_number', 'product_services','destination', 'category', 'topup', 'customFields', 'customerId'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function customer(Request $request)
    {
        $customer = Customer::where('id', '=', $request->id)->first();

        return view('invoice.customer_detail', compact('customer'));
    }

    public function customerCommission(Request $request)
    {
        $customer = Customer::where('id', '=', $request->id)->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'commission' => $customer->commission ?? 0,
            ]);
        }

        return response()->json([
            'success' => false,
            'commission' => 0,
        ]);
    }

    public function vender(Request $request)
    {
        $vender = Vender::where('id', '=', $request->id)->first();

        if ($vender) {
            return response()->json([
                'success' => true,
                'commission' => $vender->commission ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'commission' => 0
        ]);
    }

    public function product(Request $request)
    {
        if (empty($request->product_id)) {
            return json_encode(['error' => 'Product ID is required']);
        }

        $data['product']     = $product = ProductService::where('id',$request->product_id)->with('unit')->first();

        if (!$product) {
            return json_encode(['error' => 'Product not found']);
        }

        $data['unit']        = (!empty($product->unit)) ? $product->unit->name : '';
        $data['taxRate']     = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes']       = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice           = $product->sale_price ?? 0;
        $quantity            = 1;
        $taxPrice            = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function store(Request $request)
    {

        // return $request;
        if(\Auth::user()->type == 'sales' || \Auth::user()->can('create invoice'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'customer_id' => 'required',
                                   'issue_date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $status = Invoice::$statues;

            // Get branch_id from employee table for logged in user
            $branchId = 0;
            $user = \Auth::user();

            // Try to get employee record for current user
            $employee = Employee::where('user_id', $user->id)->first();

            if ($employee) {
                // If employee exists, get branch_id (0 is also a valid value)
                $branchId = $employee->branch_id !== null ? (int)$employee->branch_id : 0;
            }

            $invoice                 = new Invoice();
            $invoice->invoice_id     = $this->invoiceNumber();
            $invoice->customer_id    = $request->customer_id;
            $invoice->status         = 0;
            $invoice->issue_date     = $request->issue_date;
            $invoice->due_date     = $request->due_date;
            //$invoice->created_by     = $user->creatorId();
            $invoice->created_by     = \Auth::id();
            $invoice->branch_id      = $branchId;
            $invoice->save();
            CustomField::saveData($invoice, $request->customField);
            $products = $request->items;

            for($i = 0; $i < count($products); $i++)
            {
                $invoiceProduct              = new InvoiceProduct();
                $invoiceProduct->invoice_id  = $invoice->id;
                // Product no longer used; store as 0 to satisfy NOT NULL columns
                $invoiceProduct->product_id  = !empty($products[$i]['product_id']) ? $products[$i]['product_id'] : 1;
                $invoiceProduct->type        = $products[$i]['type'] ?? null;
                $invoiceProduct->grade        = $products[$i]['grade'] ?? null;
                $financials = $this->prepareInvoiceProductFinancials($products[$i]);
                $invoiceProduct->quantity = $financials['quantity'];
                $invoiceProduct->net = $financials['net'];
                $invoiceProduct->fare = $financials['fare'];
                $invoiceProduct->tax = $financials['tax'];
                $invoiceProduct->refund = $financials['refund'];
                $invoiceProduct->commission = $financials['commission'];
                $invoiceProduct->discount = $financials['discount'];
                $invoiceProduct->price = $financials['price'];
                $invoiceProduct->cost =  $invoiceProduct->fare + $invoiceProduct->tax + $invoiceProduct->commission;


                $invoiceProduct->description = $products[$i]['description'] ?? '';
                if (!empty($invoiceProduct->product_id)) {
                    $invoiceProduct->destination = optional(
                        ProductService::find($invoiceProduct->product_id)
                    )->name;
                }
                $invoiceProduct->save();

                //inventory management (Quantity) - only if product_id exists
                if (!empty($invoiceProduct->product_id)) {
                    Utility::total_quantity('minus', $invoiceProduct->quantity, $invoiceProduct->product_id);
                }

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $customer = Customer::find($request->customer_id);
                $invoiceNotificationArr = [
                    'invoice_number' => \Auth::user()->invoiceNumberFormat($invoice->invoice_id),
                    'user_name' => \Auth::user()->name,
                    'invoice_issue_date' => $invoice->issue_date,
                    'invoice_due_date' => $invoice->due_date,
                    'customer_name' => $customer->name,
                ];
                //Slack Notification
                if(isset($setting['invoice_notification']) && $setting['invoice_notification'] ==1)
                {
                    Utility::send_slack_msg('new_invoice', $invoiceNotificationArr);
                }
                //Telegram Notification
                if(isset($setting['telegram_invoice_notification']) && $setting['telegram_invoice_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_invoice', $invoiceNotificationArr);
                }
                //Twilio Notification
                if(isset($setting['twilio_invoice_notification']) && $setting['twilio_invoice_notification'] ==1)
                {
                    Utility::send_twilio_msg($customer->contact,'new_invoice', $invoiceNotificationArr);
                }
            }


            //Product Stock Report - only for items with product_id
            $type='invoice';
            $type_id = $invoice->id;
            StockReport::where('type','=','invoice')->where('type_id' ,'=', $invoice->id)->delete();
            foreach($invoice->items as $item) {
                if (!empty($item->product_id)) {
                    $description = $item->quantity.'  '.__(' quantity sold in invoice').' '. \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
                    Utility::addProductStock($item->product_id, $item->quantity, $type, $description, $type_id);
                }
            }


            //webhook
            $module ='New Invoice';
            $webhook =  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($invoice);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                if($status == true)
                {
                    return redirect()->route('invoice.index', $invoice->id)->with('success', __('Invoice successfully created.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            // Check which button was clicked
            $submitButton = $request->input('submit_button');

            if ($submitButton === 'create_new') {
                // Redirect to create page for new invoice
                return redirect()->route('invoice.create', 0)->with('success', __('Invoice successfully created. You can create another one.'));
            } else {
                // Redirect to index page (default behavior)
                return redirect()->route('invoice.index')->with('success', __('Invoice successfully created.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($ids)
    {
        if(\Auth::user()->type == 'client' || \Auth::user()->can('edit invoice'))
        {
            try{
                $id      = Crypt::decrypt($ids);
            } catch (\Exception $e){
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
            $invoice = Invoice::find($id);

            if ($invoice->status != 3 && $invoice->status != 4) {
                $invoice_number = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

                $customers      = Customer::where('created_by', \Auth::user()->creatorId())
                    ->get()
                    ->mapWithKeys(function ($customer) {
                        $displayName = $customer->name . ' - ' . ($customer->contact ?? '');
                        return [$customer->id => $displayName];
                    });
                $customers->prepend('Select Customer', '');

                $category       = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                    ->where('type', 'Income')
                    ->get()
                    ->pluck('name', 'id');
                $category->prepend('Select ...', '');

                $topup = Vender::where('created_by', \Auth::user()->creatorId())
                    ->where('tax_number','Airline')->orWhere('tax_number', 'Other')
                    ->get()
                    ->pluck('name', 'id');
                $topup->prepend('Select Company', 'Select Company');

                // For invoice-level origin/destination we want to store/display text (route name),
                // so use the product "name" both as key and value (same as create()).
               $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
               $product_services->prepend('Select Origin', '');

               $destination = ProductService::where('created_by', \Auth::user()->creatorId())->where('type','Destination')->get()->pluck('name', 'name');
               $destination->prepend('Select Destination', '');

                $invoice->customField = CustomField::getData($invoice, 'invoice');
                $customFields         = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

                return view('invoice.edit', compact('customers', 'product_services', 'destination', 'invoice', 'invoice_number', 'category', 'topup', 'customFields'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {

        if (\Auth::user()->type == 'client' || \Auth::user()->can('edit invoice')) {
            if ($invoice->created_by = \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'customer_id' => 'required',
                        'vender_id' => 'required',
                        'issue_date' => 'required',
                        'due_date' => 'required',
                        'category_id' => 'required',
                        'vender_id' => 'required',
                        'origin' => 'required',
                        'destination' => 'required',
                        'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoice.index')->with('error', $messages->first());
                }

                $invoice->customer_id    = $request->customer_id;
                $invoice->issue_date     = $request->issue_date;
                $invoice->due_date       = $request->due_date;
                $invoice->ref_number     = $request->ref_number;
                $invoice->category_id    = $request->category_id;
                $invoice->vender_id       = $request->vender_id;
                $invoice->origin         = $request->origin;
                $invoice->destination    = $request->destination;

                $due = $invoice->getDue();
                if ($due <= 0) {
                    $invoice->status = 4;
                } else {
                    $invoice->status = 2;
                }

                $invoice->save();

                Utility::starting_number( $invoice->invoice_id + 1, 'invoice');
                CustomField::saveData($invoice, $request->customField);
                $products = $request->items;
                $updatePrice = 0;

                for ($i = 0; $i < count($products); $i++) {
                    $invoiceProduct = InvoiceProduct::find($products[$i]['id']);

                    if ($invoiceProduct == null)
                    {
                        $invoiceProduct             = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;

                        // Only update inventory if product_id exists
                        if (!empty($products[$i]['product_id'])) {
                            Utility::total_quantity('minus', $products[$i]['quantity'] ?? 0, $products[$i]['product_id']);
                        }
                    }
                    else{
                        // Only update inventory if product_id exists
                        if (!empty($invoiceProduct->product_id)) {
                            Utility::total_quantity('plus', $invoiceProduct->quantity, $invoiceProduct->product_id);
                        }
                    }

                    // Product no longer used; store as 0 to satisfy NOT NULL columns
                    $invoiceProduct->product_id = !empty($products[$i]['product_id']) ? $products[$i]['product_id'] : 1;
                    $invoiceProduct->type        = $products[$i]['type'] ?? null;
                    $invoiceProduct->ticket_number = $products[$i]['ticket_number'] ?? null;
                    $invoiceProduct->passanger_name = $products[$i]['passanger_name'] ?? null;
                    $financials = $this->prepareInvoiceProductFinancials($products[$i]);
                    $invoiceProduct->quantity = $financials['quantity'];
                    $invoiceProduct->fare = $financials['fare'];
                    $invoiceProduct->tax = $financials['tax'];
                    $invoiceProduct->refund = $financials['refund'];
                    $invoiceProduct->commission = $financials['commission'];
                    $invoiceProduct->cust_commission = $financials['cust_commission'];
                    $invoiceProduct->discount = $financials['discount'];
                    $invoiceProduct->price = $financials['price'];
                    $invoiceProduct->cost =  $invoiceProduct->fare + $invoiceProduct->tax - $invoiceProduct->commission;
                    $invoiceProduct->description = $products[$i]['description'] ?? '';
                    $invoiceProduct->save();

                    // Only update inventory if product_id exists
                    if (!empty($invoiceProduct->product_id) && isset($products[$i]['id']) && $products[$i]['id'] > 0) {
                        Utility::total_quantity('minus', $products[$i]['quantity'] ?? 0, $invoiceProduct->product_id);
                    }
                }

                //Product Stock Report - only for items with product_id
                $type='invoice';
                $type_id = $invoice->id;
                StockReport::where('type','=','invoice')->where('type_id' ,'=', $invoice->id)->delete();
                foreach($invoice->items as $item) {
                    if (!empty($item->product_id)) {
                        $description = $item->quantity.'  '.__(' quantity sold in invoice').' '. \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
                        Utility::addProductStock($item->product_id, $item->quantity, $type, $description, $type_id);
                    }
                }

                $invoice_products = InvoiceProduct::where('invoice_id', $invoice->id)->get();
                foreach ($invoice_products as $invoice_product) {
                    // Only process transactions if product_id exists
                    if (empty($invoice_product->product_id)) {
                        continue;
                    }

                    $product = ProductService::find($invoice_product->product_id);
                    if (!$product) {
                        continue;
                    }

                    $totalTaxPrice = 0;
                    if($invoice_product->tax != null && $invoice_product->tax != ''){
                        $taxValue = trim($invoice_product->tax);
                        // Check if tax contains commas (tax IDs) or is numeric (tax amount)
                        if (strpos($taxValue, ',') !== false) {
                            // Tax contains tax IDs (comma-separated) - process them for product invoices
                            $taxes = \App\Models\Utility::tax($taxValue);
                            if (is_array($taxes)) {
                                foreach ($taxes as $tax) {
                                    // Check if tax object exists and has rate property before accessing it
                                    if ($tax !== null && is_object($tax) && isset($tax->rate) && $tax->rate !== null) {
                                        $taxPrice = \App\Models\Utility::taxRate($tax->rate, $invoice_product->price, 1, 0);
                                        $totalTaxPrice += $taxPrice;
                                    }
                                }
                            }
                        } else {
                            // Tax is a numeric value (tax amount for ticket invoices)
                            // Price already includes tax, so don't add it again
                            $totalTaxPrice = 0;
                        }
                    }

                    // For ticket invoices: price already includes tax (fare + tax + cust_commission - discount)
                    // For product invoices: add calculated tax to price

                     //$purchaseProducts = PurchaseProduct::where('purchase_id', $purchase->id)->get();
                    DB::table('add_transaction_lines')
                    ->where('reference_id', $invoice->id) // or $purchase->id depending on your structure
                    ->delete();

                    $itemAmount = $invoice_product->price + $totalTaxPrice;

                    $price = InvoiceProduct::where('invoice_id', $invoice->id)->value('price');

                    if($invoice->status != 0) {


                        $data = [
                            'account_id'         => $product->sale_chartaccount_id,
                            'transaction_type'   => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Invoice',
                            'reference_id'       => $invoice->id,
                            'customer_id'        => '0',
                            'vender_id'          => '0',
                            'bank_id'            => '0',
                            'reference_sub_id'   => $product->id,
                            'date'               => $invoice->issue_date,
                        ];

                        Utility::addTransactionLines($data , 'edit' , 'notes');

                        Utility::updateUserBalance('customer', $invoice->customer_id, $price, 'debit');

                        Utility::updateUserBalance('customer', $invoice->customer_id, $itemAmount, 'credit');

                        $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                        $data    = [
                            'account_id'         => !empty($account) ? $account->id : 0,
                            'transaction_type'   => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Invoice',
                            'reference_id'       => $invoice->id,
                            'customer_id'        => $invoice->customer_id,
                            'vender_id'          => '0',
                            'bank_id'            => '0',
                            'reference_sub_id'   => $product->id,
                            'date'               => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data , 'edit');


                         $vender = Vender::where('id', $invoice->vender_id)->value('tax_number');
                if($vender == 'Airline') {

                        $itemAmount = $invoice_product->fare + $invoice_product->tax - $invoice_product->commission;


                        $data = [
                            'account_id'         => $product->expense_chartaccount_id,
                            'transaction_type'   => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference'          => 'Invoice',
                            'reference_id'       => $invoice->id,
                            'vender_id'          => '0',
                            'customer_id'        => '0',
                            'bank_id'         => '0',
                            'reference_sub_id'    => $product->id,
                            'date'                => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data , 'edit');

                        $account = ChartOfAccount::where('name', 'Top Up')->where('created_by', \Auth::user()->creatorId())->first();


                        $data = [
                            'account_id' => !empty($account) ? $account->id : 0,
                            'transaction_type' => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice',
                            'reference_id' => $invoice->id,
                            'vender_id'       => $invoice->vender_id,
                            'customer_id'         => '0',
                            'bank_id'         => '0',
                            'reference_sub_id' => $product->id,
                            'date' => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data , 'edit');
                    }
                    else {
                          $itemAmount = $invoice_product->fare + $invoice_product->tax - $invoice_product->commission;
                         Utility::updateUserBalance('vender', $invoice->vender_id, $itemAmount, 'debit');
                         Utility::updateUserBalance('vender', $invoice->vender_id, $itemAmount, 'credit');


                        $data = [
                            'account_id' => $product->expense_chartaccount_id,
                            'transaction_type' => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice',
                            'reference_id' => $invoice->id,
                            'vender_id'       => '0',
                            'bank_id'         => '0',
                            'customer_id'         => '0',
                            'reference_sub_id' => $product->id,
                            'date' => $invoice->issue_date,
                        ];
                         Utility::addTransactionLines($data , 'edit');
                        $account = ChartOfAccount::where('name', 'Accounts Payable')->where('created_by', \Auth::user()->creatorId())->first();

                        $itemAmount = $invoice_product->fare + $invoice_product->tax - $invoice_product->commission;

                        $data = [
                            'account_id' => !empty($account) ? $account->id : 0,
                            'transaction_type' => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice',
                            'reference_id' => $invoice->id,
                            'vender_id'       => $invoice->vender_id,
                            'customer_id'         => '0',
                            'bank_id'         => '0',
                            'reference_sub_id' => $product->id,
                            'date' => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data , 'edit');

                    }

                        Utility::updateUserBalance('customer', $invoice->customer_id, $itemAmount, 'debit');
                        Utility::updateUserBalance('customer', $invoice->customer_id, $itemAmount, 'credit');

                    }
                }

                return redirect()->route('invoice.index')->with('success', __('Invoice successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function invoiceNumber()
{
    $latest = Invoice::latest('invoice_id')->first();
    if(!$latest)
    {
        return 1;
    }

    return $latest->invoice_id + 1;
}

    public function show($ids)
    {

        if(\Auth::user()->type == 'client' || \Auth::user()->can('show invoice'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
            $id      = Crypt::decrypt($ids);
            $invoice = Invoice::with(['creditNote','payments.bankAccount','items.product.unit'])->find($id);

            if(!empty($invoice->created_by) == \Auth::user()->creatorId())
            {
                $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

                $customer             = $invoice->customer;
                $iteams               = $invoice->items;
                $user                 = \Auth::user();
                $invoice->customField = CustomField::getData($invoice, 'invoice');
                $customFields         = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
                return view('invoice.view', compact('invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'user'));
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

    public function destroy(Invoice $invoice,Request $request)
    {
        if(\Auth::user()->type == 'client' || \Auth::user()->can('delete invoice'))
        {
            if($invoice->created_by = \Auth::user()->creatorId())

            {
                $customerCreditNote = CustomerCreditNotes::where('invoice',$invoice->id)->first();

                if($customerCreditNote) {
                    return redirect()->back()->with('error', __('The credit note has been created for this invoice, so it cannot be deleted.'));
                }



                foreach($invoice->payments as $invoices)
                {
                    Utility::bankAccountBalance($invoices->account_id, $invoices->amount, 'debit');
                    Utility::updateUserBalance('customer', $invoice->customer_id, $invoices->amount, 'credit');

                    $invoicepayment = InvoicePayment::find($invoices->id);

                    AddTransactionLine::where('reference_id', $invoice->id)->where('reference_sub_id', $invoicepayment->id)->where('reference', 'Invoice Payment')->delete();

                    $invoicepayment->delete();
                }
                AddTransactionLine::where('reference_id',$invoice->id)->where('reference','Invoice')->delete();


                $creditNoteApply = CreditNote::where('invoice', $invoice->id)->get();
                foreach($creditNoteApply as $creditNote)
                {
                    $this->updateCreditNoteStatus($creditNote , 'delete');
                    $creditNote->delete();
                }
                InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();
                $invoice->delete();
                Utility::updateUserBalance('customer', $invoice->customer_id, $invoice->amount, type: 'debit');
                return redirect()->route('invoice.index')->with('success', __('Invoice successfully deleted.'));
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
    public function productDestroy(Request $request)
    {

        if (\Auth::user()->type == 'client' || \Auth::user()->can('delete invoice product')) {
            $invoiceProduct = InvoiceProduct::find($request->id);

            if($invoiceProduct)
            {
                $invoice = Invoice::find($invoiceProduct->invoice_id);
                $productService = ProductService::find($invoiceProduct->product_id);
                // If there is a linked product, clean up transaction lines for that sub reference
                if ($productService) {
                    AddTransactionLine::where('reference_id', $invoice->id)
                        ->where('reference_sub_id', $productService->id)
                        ->where('reference','Invoice')
                        ->delete();
                }

                InvoiceProduct::where('id', '=', $request->id)->delete();
                return response()->json(['status' => true, 'message' => __('Invoice product successfully deleted.')]);
            } else {
                return response()->json(['status' => false, 'message' => __('Invoice product not found!')]);
            }
        } else {
            return response()->json(['status' => false, 'message' => __('Permission denied.')]);
        }
    }

    public function customerInvoice(Request $request)
    {
        if(\Auth::user()->can('manage customer invoice'))
        {

            $status = Invoice::$statues;

            $query = Invoice::where('customer_id', '=', \Auth::user()->id)->where('status', '!=', '0')->where('created_by', \Auth::user()->creatorId());

            if(!empty($request->issue_date))
            {
                $date_range = explode(' - ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            }

            if(!empty($request->status))
            {
                $query->where('status', '=', $request->status);
            }
            $invoices = $query->get();

            return view('invoice.index', compact('invoices', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function customerInvoiceShow($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        $user    = User::where('id', $invoice->created_by)->first();
        if($invoice->created_by == $user->creatorId())
        {
            $customer = $invoice->customer;
            $iteams   = $invoice->items;
            if($user->type == 'super admin')
            {
                return view('invoice.view', compact('invoice', 'customer', 'iteams', 'user'));
            }
            elseif($user->type == 'company')
            {
                return view('invoice.customer_invoice', compact('invoice', 'customer', 'iteams', 'user'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function sent($id)
    {
    if (\Auth::user()->type == 'client' || \Auth::user()->can('send invoice')) {
            // Send Email
            $setings = Utility::settings();

        if ($setings['customer_invoice_sent'] == 1) {

                //$invoice = Invoice::where('id','customer_id', 'vender_id' $id)->first();
                $invoice = Invoice::select('id','customer_id', 'vender_id','issue_date','category_id')->where('id', $id)->first();
                $invoice->send_date = date('Y-m-d');
                $invoice->status = ($invoice->category_id == 9) ? 5 : 2;  // Set status based on category
                $invoice->save();

                if (empty($invoice->issue_date)) {
                   $invoice->issue_date = date('Y-m-d');
                 }

                $customer = Customer::where('id', $invoice->customer_id)->first();
                $invoice->name = !empty($customer) ? $customer->name : '';
                $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

                $invoiceId = Crypt::encrypt($invoice->id);
                $invoice->url = route('invoice.pdf', $invoiceId);

                $invoice_products = InvoiceProduct::where('invoice_id', $invoice->id)->get();
                foreach ($invoice_products as $invoice_product) {
                    // Only process transactions if product_id exists
                    if (empty($invoice_product->product_id)) {
                        continue;
                    }

                    $product = ProductService::find($invoice_product->product_id);
                    if (!$product) {
                        continue;
                    }

                    $totalTaxPrice = 0;
                    if ($invoice_product->tax != null && $invoice_product->tax != '') {
                        $taxValue = trim($invoice_product->tax);
                        // Check if tax contains commas (tax IDs) or is numeric (tax amount)
                        if (strpos($taxValue, ',') !== false) {
                            // Tax contains tax IDs (comma-separated) - process them for product invoices
                            $taxes = \App\Models\Utility::tax($taxValue);
                            if (is_array($taxes)) {
                                foreach ($taxes as $tax) {
                                    // Check if tax object exists and has rate property before accessing it
                                    if ($tax !== null && is_object($tax) && isset($tax->rate) && $tax->rate !== null) {
                                        $taxPrice = \App\Models\Utility::taxRate($tax->rate, $invoice_product->price, 1, 0);
                                        $totalTaxPrice += $taxPrice;
                                    }
                                }
                            }
                        } else {
                            // Tax is a numeric value (tax amount for ticket invoices)
                            // Price already includes tax, so don't add it again
                            $totalTaxPrice = 0;
                        }
                    }

                    // For ticket invoices: price already includes tax (fare + tax + cust_commission - discount)
                    // For product invoices: add calculated tax to price
                    // $invoice1 = Invoice::select('customer_id', 'vender_id')->where('id', $id)->first();



                        $itemAmount = $invoice_product->price + $totalTaxPrice;
                        Utility::updateUserBalance('customer', $invoice->customer_id, $itemAmount, 'credit');

                        $data = [
                            'account_id' => $product->sale_chartaccount_id,
                            'bank_id' => '0',
                            'transaction_type' => 'credit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice-Sales',
                            'reference_id' => $invoice->id,
                            'reference_id' => $invoice->id,
                            'reference_sub_id' => $product->id,
                            'customer_id' => $invoice->customer_id,
                            'vender_id' => '0',
                            'date' => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data);



                        $account = ChartOfAccount::where('name', 'Accounts Receivable')->where('created_by', \Auth::user()->creatorId())->first();
                        $data = [
                            'account_id' => !empty($account) ? $account->id : 0,
                            'bank_id' => '0',
                            'transaction_type' => 'debit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice-AR',
                            'reference_id' => $invoice->id,
                            'reference_sub_id' => $product->id,
                            'customer_id' => $invoice->customer_id,
                            'vender_id' => '0',
                            'date' => $invoice->issue_date,
                        ];
                        Utility::addTransactionLines($data);



                }

                $customerArr = [

                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'invoice_name' => $customer->name,
                    'invoice_number' => $invoice->invoice,
                    'invoice_url' => $invoice->url,

                ];
                $resp = Utility::sendEmailTemplate('customer_invoice_sent', [$customer->id => $customer->email], $customerArr);

                return redirect()->back()->with('success', __('Invoice successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            } else {
                return redirect()->back()->with('error', 'Invoice not found');
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function resent($id)
    {
        if(\Auth::user()->type == 'client' || \Auth::user()->can('send invoice'))
        {
            $invoice = Invoice::where('id', $id)->first();

            $customer         = Customer::where('id', $invoice->customer_id)->first();
            $invoice->name    = !empty($customer) ? $customer->name : '';
            $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

            $invoiceId    = Crypt::encrypt($invoice->id);
            $invoice->url = route('invoice.pdf', $invoiceId);
            $customerArr = [

                'customer_name'=> $customer->name,
                'customer_email' => $customer->email,
                'invoice_name' => $customer->name,
                'invoice_number' => $invoice->invoice,
                'invoice_url' => $invoice->url,

            ];
            $resp = Utility::sendEmailTemplate('customer_invoice_sent', [$customer->id => $customer->email], $customerArr);

            return redirect()->back()->with('success', __('Invoice successfully sent.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment($invoice_id)
    {
        if(\Auth::user()->type == 'client' || \Auth::user()->can('create payment invoice'))
        {
            $invoice = Invoice::where('id', $invoice_id)->first();

            $customers  = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('invoice.payment', compact('customers', 'categories', 'accounts', 'invoice'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {

        if(\Auth::user()->type == 'Cashier' || \Auth::user()->can('create payment invoice'))
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

            $invoice = Invoice::find($invoice_id);
            if ($invoice->getDue() < $request->amount) {
                return redirect()->back()->with('error', __('Invoice payment amount should not greater than subtotal.'));
            }

            $bankAccount = BankAccount::find($request->account_id);
            if($bankAccount->chart_account_id == 0)
            {
                return redirect()->back()->with('error', __('This bank account is not connect with chart of account, so please connect first.'));
            }

            $invoicePayment                 = new InvoicePayment();
            $invoicePayment->invoice_id     = $invoice_id;
            $invoicePayment->date           = $request->date;
            $invoicePayment->amount         = $request->amount;
            $invoicePayment->account_id     = $request->account_id;
            $invoicePayment->payment_method = 0;
            $invoicePayment->reference      = $request->reference;
            $invoicePayment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $request->add_receipt->storeAs('uploads/payment', $fileName);
                $invoicePayment->add_receipt = $fileName;
            }



            $invoicePayment->save();


            $invoice = Invoice::where('id', $invoice_id)->first();
            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();
            if($invoice->status == 0)
            {
                $invoice->send_date = date('Y-m-d');
                $invoice->sent_by = \Auth::id();
                $invoice->save();
            }

            if($due <= 0)
            {
                $invoice->status = 4;
                $invoice->sent_by = \Auth::id();
                $invoice->save();
            }
            else
            {
                $invoice->status = 3;
                $invoice->sent_by = \Auth::id();
                $invoice->save();
            }
            $invoicePayment->user_id    = $invoice->customer_id;
            $invoicePayment->user_type  = 'Customer';
            $invoicePayment->type       = 'Partial';
            $invoicePayment->created_by = \Auth::user()->id;
            $invoicePayment->payment_id = $invoicePayment->id;
            $invoicePayment->category   = 'Invoice';
            $invoicePayment->account    = $request->account_id;

            Transaction::addTransaction($invoicePayment);
            $customer = Customer::where('id', $invoice->customer_id)->first();


            $payment            = new InvoicePayment();
            $payment->name      = $customer['name'];
            $payment->date      = \Auth::user()->dateFormat($request->date);
            $payment->amount    = \Auth::user()->priceFormat($request->amount);
            $payment->invoice   = 'invoice ' . \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $payment->dueAmount = \Auth::user()->priceFormat($invoice->getDue());

            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            $accountId = BankAccount::find($request->account_id);
            $data = [
                'account_id'         => $accountId->chart_account_id,
                'bank_id'            => '1',
                'transaction_type'   => 'debit',
                'transaction_amount' => $request->amount,
                'reference'          => 'Invoice-Payment-Bank',
                'reference_id'       => $invoice->id,
                'customer_id'        => $invoice->customer_id,
                'vender_id'          => '0',
                'reference_sub_id'   => $invoicePayment->id,
                'date'               => $invoicePayment->date,
            ];
            Utility::addTransactionLines($data);

            $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
            $data    = [
                'account_id'         => !empty($account) ? $account->id : 2,
                'bank_id'            => '0',
                'transaction_type'   => 'credit',
                'transaction_amount' => $request->amount,
                'reference'          => 'Invoice-Payment-AR',
                'reference_id'       => $invoice->id,
                'customer_id'        => $invoice->customer_id,
                'vender_id'          => '0',
                'reference_sub_id'   => $invoicePayment->id,
                'date'               => $invoicePayment->date,
            ];
            Utility::addTransactionLines($data);

            // Send Email
            $setings = Utility::settings();
            if($setings['new_invoice_payment'] == 1)
            {
                $customer = Customer::where('id', $invoice->customer_id)->first();
                $invoice_payment_method = InvoicePayment::where('invoice_id', $invoice->id)->orderBy('id', 'desc')->first();
                $invoicePaymentArr = [
                    'invoice_payment_name'   => $customer->name,
                    'invoice_payment_amount'   => $payment->amount,
                    'invoice_payment_date'  =>$payment->date,
                    'payment_dueAmount'  => $payment->dueAmount,
                    'invoice_number' => \Auth::user()->priceFormat($invoice->getDue()),
                    'invoice_payment_method' => $invoice_payment_method->payment_type,
                ];

                $resp = Utility::sendEmailTemplate('new_invoice_payment', [$customer->id => $customer->email], $invoicePaymentArr);
            }

            //webhook
            $module ='New Invoice Payment';
            $webhook =  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($invoice);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                if($status == true)
                {
                    return redirect()->back()->with('success', __('Payment successfully added.') .((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : '').(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '') );

                }
                else
                {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }
            return redirect()->back()->with('success', __('Payment successfully added.') .((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : '').(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '') );

        }
    }

    public function paymentDestroy(Request $request, $invoice_id, $payment_id)
    {

        if(\Auth::user()->type == 'client' || \Auth::user()->can('delete payment invoice'))
        {
            $payment = InvoicePayment::find($payment_id);
            $invoice = Invoice::where('id', $invoice_id)->first();

            InvoicePayment::where('id', '=', $payment_id)->delete();

            InvoiceBankTransfer::where('id', '=', $payment_id)->delete();

            AddTransactionLine::where('reference_id',$invoice->id)->where('reference_sub_id',$payment_id)->delete();

            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();

            if($due > 0 && $total != $due)
            {
                $invoice->status = 3;

            }
            else
            {
                $invoice->status = 2;
            }

            $invoice->save();
            $type = 'Partial';
            $user = 'Customer';
            Transaction::destroyTransaction($payment_id, $type, $user);

            if ($payment) {
                Utility::updateUserBalance('customer', $invoice->customer_id, $payment->amount, 'credit');
                Utility::bankAccountBalance($payment->account_id, $payment->amount, 'debit');
            }

            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function paymentReminder($invoice_id)
    {
        $invoice            = Invoice::find($invoice_id);
        $customer           = Customer::where('id', $invoice->customer_id)->first();
        $invoice->dueAmount = \Auth:: user()->priceFormat($invoice->getDue());
        $invoice->name      = $customer['name'];
        $invoice->date      = \Auth::user()->dateFormat($invoice->send_date);
        $invoice->invoice   = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);


        // Send Email
        $setings = Utility::settings();
        if($setings['new_payment_reminder'] == 1)
        {
            $invoice            = Invoice::find($invoice_id);
            $customer           = Customer::where('id', $invoice->customer_id)->first();
            $invoice->dueAmount = \Auth:: user()->priceFormat($invoice->getDue());
            $invoice->name      = $customer['name'];
            $invoice->date      = \Auth::user()->dateFormat($invoice->send_date);
            $invoice->invoice   = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

            $reminderArr = [

                'payment_reminder_name'=> $invoice->name,
                'invoice_payment_number' =>$invoice->invoice,
                'invoice_payment_dueAmount'=>$invoice->dueAmount,
                'payment_reminder_date' => $invoice->date,

            ];


            $resp = Utility::sendEmailTemplate('new_payment_reminder', [$customer->id => $customer->email], $reminderArr);

        }
        //For Notification
        $setting  = Utility::settings(\Auth::user()->creatorId());
        $customer = Customer::find($invoice->customer_id);
        $reminderNotificationArr = [
            'invoice_number' => \Auth::user()->invoiceNumberFormat($invoice->invoice_id),
            'customer_name' => $customer->name,
            'user_name' => \Auth::user()->name,
        ];

        //Twilio Notification
        if(isset($setting['twilio_reminder_notification']) && $setting['twilio_reminder_notification'] ==1)
        {
            Utility::send_twilio_msg($customer->contact,'invoice_payment_reminder', $reminderNotificationArr);
        }

        return redirect()->back()->with('success', __('Payment reminder successfully send.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
    }

    public function customerInvoiceSend($invoice_id)
    {
        return view('customer.invoice_send', compact('invoice_id'));
    }

    public function shippingDisplay(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if($request->is_display == 'true')
        {
            $invoice->shipping_display = 1;
        }
        else
        {
            $invoice->shipping_display = 0;
        }
        $invoice->save();

        return redirect()->back()->with('success', __('Shipping address status successfully changed.'));
    }

    public function duplicate($invoice_id)
    {
        if(\Auth::user()->type == 'client' || \Auth::user()->can('duplicate invoice'))
        {
            $invoice                            = Invoice::where('id', $invoice_id)->first();
            $duplicateInvoice                   = new Invoice();
            $duplicateInvoice->invoice_id       = $this->invoiceNumber();
            $duplicateInvoice->customer_id      = $invoice['customer_id'];
            $duplicateInvoice->issue_date       = date('Y-m-d');
            $duplicateInvoice->due_date         = $invoice['due_date'];
            $duplicateInvoice->send_date        = null;
            $duplicateInvoice->category_id      = $invoice['category_id'];
            $duplicateInvoice->ref_number       = $invoice['ref_number'];
            $duplicateInvoice->status           = 0;
            $duplicateInvoice->shipping_display = $invoice['shipping_display'];
            $duplicateInvoice->vender_id         = $invoice['vender_id'];
            $duplicateInvoice->created_by       = $invoice['created_by'];
            $duplicateInvoice->save();

            if($duplicateInvoice)
            {
                $invoiceProduct = InvoiceProduct::where('invoice_id', $invoice_id)->get();
                foreach($invoiceProduct as $product)
                {
                    $duplicateProduct                     = new InvoiceProduct();
                    $duplicateProduct->invoice_id         = $duplicateInvoice->id;
                    $duplicateProduct->product_id         = $product->product_id;
                    $duplicateProduct->type               = $product->type;
                    $duplicateProduct->ticket_number      = $product->ticket_number;
                    $duplicateProduct->passanger_name     = $product->passanger_name;
                    $duplicateProduct->quantity           = $product->quantity;
                    $duplicateProduct->fare               = $product->fare;
                    $duplicateProduct->commission         = $product->commission;
                    $duplicateProduct->cust_commission    = $product->cust_commission;
                    $duplicateProduct->discount           = $product->discount;
                    $duplicateProduct->destination        = $product->destination;
                    $duplicateProduct->description        = $product->description;
                    $duplicateProduct->price              = $product->price;
                    $duplicateProduct->save();
                }
            }

            return redirect()->back()->with('success', __('Invoice duplicate successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewInvoice($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        $invoice  = new Invoice();

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

        $items = [];
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

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->itemData   = $items;
        $invoice->status = 0;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;
        //$invoice->created_by     = $objUser->creatorId();
        $invoice->created_by = \Auth::id();

        $invoice->customField   = [];
        $customFields           = [];

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);


        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = Utility::get_file('invoice_logo/') . $invoice_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }
        return view('invoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'customer', 'font_color', 'customFields'));
    }

    public function invoice($invoice_id)
    {
        $settings = Utility::settings();

        try{
            $invoiceId = Crypt::decrypt($invoice_id);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }

        $invoice   = Invoice::where('id', $invoiceId)->first();

        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $invoice->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $customer      = $invoice->customer;
        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        foreach($invoice->items as $product)
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

            $taxes = Utility::tax($product->tax);
            if (empty($taxes) || (count($taxes) == 1 && is_null($taxes[0]))) {
                $taxes = [];
            }

            $itemTaxes = [];
            if(!empty($item->tax))
            {
                foreach($taxes as $tax)
                {
                    $taxPrice      = Utility::taxRate($tax->rate, $item->price, 1, 0);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate ?? 0 . '%';
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

        $invoice->itemData      = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;
        $invoice->customField   = CustomField::getData($invoice, 'invoice');
        $customFields           = [];
        if(!empty(\Auth::user()))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
        }


        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($invoice->created_by);
        $invoice_logo = $settings_data['invoice_logo'];
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = Utility::get_file('invoice_logo/') . $invoice_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }


        if($invoice)
        {
            $color      = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('invoice.templates.' . $settings['invoice_template'], compact('invoice', 'color', 'settings', 'customer', 'img', 'font_color', 'customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function saveTemplateSettings(Request $request)
    {

        $post = $request->all();
        unset($post['_token']);

        if(isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color'])))
        {
            $post['invoice_color'] = "ffffff";
        }

        if($request->invoice_logo)
        {
            $dir = 'invoice_logo/';
            $invoice_logo = \Auth::user()->id . '_invoice_logo.png';
            $validation =[
                'mimes:'.'png',
                'max:'.'20480',
            ];
            $path = Utility::upload_file($request,'invoice_logo',$invoice_logo,$dir,$validation);

            if($path['flag']==0)
            {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['invoice_logo'] = $invoice_logo;
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

        return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
    }

    public function items(Request $request)
    {
        $items = InvoiceProduct::where('invoice_id', $request->invoice_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function invoiceLink($invoiceId)
    {
        try {
            $id       = Crypt::decrypt($invoiceId);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Invoice Not Found.'));
        }

        $id             = Crypt::decrypt($invoiceId);
        $invoice = Invoice::with(['creditNote','payments.bankAccount','items.product.unit'])->find($id);

        if(!empty($invoice)){

            $user_id        = $invoice->created_by;
            $user           = User::find($user_id);
            $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

        $customer = $invoice->customer;

        $iteams   = $invoice->items;

        $invoice->customField = CustomField::getData($invoice, 'invoice');

        $customFields         = CustomField::where('module', '=', 'invoice')->get();

        $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);

        $settings = Utility::settingsById($invoice->created_by);

        $khaltiAccount = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','khalti')->first();
        $paystackAccount = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paystack')->first();
        $flutterwaveAccount = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','flutterwave')->first();
        $razorpayAccount = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','razorpay')->first();

        return view('invoice.customer_invoice', compact('settings', 'invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'user', 'company_payment_setting' , 'khaltiAccount' , 'paystackAccount' , 'flutterwaveAccount' , 'razorpayAccount'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function export()
    {
        $name = 'invoice_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new InvoiceExport(), $name . '.xlsx');

        return $data;
    }

    protected function prepareInvoiceProductFinancials(array $product): array
    {
        // Quantity and product price are no longer used for ticket invoices
        $quantity = 1.0;
        $productPrice = 0.0;
        $net = (float) ($product['net'] ?? 0);
        $fareAmount = (float) ($product['fare'] ?? $product['fare_amount'] ?? 0);
        $tax = (float) ($product['tax'] ?? 0);
        $refund = (float) ($product['refund'] ?? 0);
        $commission = (float) ($product['commission'] ?? 0);
        $custCommission = (float) ($product['cust_commission'] ?? 0);
        $discount = (float) ($product['discount'] ?? 0);

        $rowSubTotal = $fareAmount + $tax + $commission + $refund;
        $rowTotal = $rowSubTotal - $discount;

        return [
            'quantity' => $quantity,
            'net' => $net,
            'fare' => $fareAmount,
            'tax' => $tax,
            'refund' => $refund,
            'commission' => $commission,
            'cust_commission' => $custCommission,
            'discount' => $discount,
            'price' => $rowTotal,
        ];
    }

}

