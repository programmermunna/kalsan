<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountType;
use App\Models\CustomField;
use App\Exports\visaExport;
use App\Models\barcodeProduct;
use App\Models\ChartOfAccountSubType;
use App\Models\visa;
use App\Models\Customer;
use App\Models\ProductServiceCategory;
use App\Models\Country;
use App\Models\ProductServiceUnit;
use App\Models\Tax;
use App\Models\User;
use App\Models\Utility;
use App\Models\Vender;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;



class VisaController extends Controller
{
    public function index(Request $request)
    {

        if(\Auth::user()->can('manage visa') || \Auth::user()->can('view visa'))
        {
            $country = Country::where('created_by', '=', \Auth::user()->creatorId()) ->get()
    ->pluck('name', 'id'); $country->prepend('Select Country', '');


             $customers = Customer::where('created_by', auth()->id())->orderBy('name') ->pluck('name', 'id');
             $customers->prepend('Select Customer', '');

            $query = Visa::select('visa.*', 'customers.name as customer_name', 'venders.name as vender_name')
                      ->leftJoin('customers', 'visa.customer_id', '=', 'customers.id')
                      ->leftJoin('venders', 'visa.vender_id', '=', 'venders.id')
                      ->where('visa.created_by', auth()->id());

            // Apply filters
            if ($request->filled('issue_date')) {
                $query->whereDate('visa.issue_date', $request->issue_date);
            }

            if ($request->filled('country')) {
                $countryName = Country::where('id', $request->country)->where('created_by', \Auth::user()->creatorId())->value('name');
                if ($countryName) {
                    $query->where('visa.country', $countryName);
                }
            }

            if ($request->filled('customer')) {
                $query->where('visa.customer_id', $request->customer);
            }

            if ($request->filled('visa_status')) {
                $query->where('visa.visa_status', $request->visa_status);
            }

            $visas = $query->orderBy('visa.created_at', 'desc')->get();

            return view('visa.index', compact('visas','country','customers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function create()
    {
        if(\Auth::user()->can('create visa') || \Auth::user()->can('add visa'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'product')->get();

            $country = Country::where('created_by', '=', \Auth::user()->creatorId()) ->get()
             ->pluck('name', 'id'); $country->prepend('Select Country', '');

             $customers = Customer::where('created_by', auth()->id())->orderBy('name') ->pluck('name', 'id');
             $customers->prepend('Select Customer', '');

            $venders = Vender::where('created_by', auth()->id())->orderBy('name') ->pluck('name', 'id');
            $venders->prepend('Select Vender', '');


            return view('visa.create', compact('country', 'customers', 'venders', 'customFields'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {

        if(\Auth::user()->can('create visa') || \Auth::user()->can('add visa'))
        {

            $rules = [
                'customer_id' => 'required',
                'vender_id' => 'required',
                'name' => 'required',
                'country' => 'required',
                'visa_type' => 'required',
                'visa_fee' => 'required|numeric',
                'commission' => 'required|numeric',
                'issue_date' => 'required|date',
                'visa_status' => 'required',

            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('visa.index')->with('error', $messages->first());
            }

            $country = Country::find($request->country);


            $visa                      = new visa();
            $visa->name                = $request->name;
            $visa->gender              = $request->gender ?? null;
            $visa->passport_no         = $request->passport_no ?? null;
            $visa->customer_id         = $request->customer_id;
            $visa->vender_id           = $request->vender_id;
            $visa->visa_type           = $request->visa_type;
            $visa->country             = $country->name;
            $visa->visa_fee            = $request->visa_fee;
            $visa->commission          = $request->commission;
            $visa->description         = $request->description ?? '';
            $visa->visa_status         = $request->visa_status;
            $visa->total_amount        = $request->visa_fee + $request->commission;
            $visa->payment_status      = 'unpaid';
            $visa->issue_date           = $request->issue_date;

            $visa->created_by       = \Auth::user()->creatorId();
            $visa->save();
            CustomField::saveData($visa, $request->customField);

            $account = ChartOfAccount::where('name','Sales Visa Income')->where('created_by' , \Auth::user()->creatorId())->first();


             $data = [
                        'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $visa->total_amount,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);



            $account2 = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account2) ? $account2->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $visa->total_amount,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => $visa->customer_id,
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);

            $account3 = ChartOfAccount::where('name','Cost of Visa Fee')->where('created_by' , \Auth::user()->creatorId())->first();


             $data = [
                        'account_id'         => !empty($account3) ? $account3->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $visa->visa_fee,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);



            $account4 = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account4) ? $account4->id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $visa->visa_fee,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => $visa->vender_id,
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);

            return redirect()->route('visa.index')->with('success', __('Visa successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show()
    {
        return redirect()->route('visa.index');
    }


    public function edit($id)
    {
        $visa = visa::find($id);

        if(\Auth::user()->can('edit visa'))
        {
            //if($visa->created_by == \Auth::user()->creatorId())
            //{
              $visa->customField = CustomField::getData($visa, 'visa');
                $customFields    = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'visa')->get();

                $country = Country::where('created_by', '=', \Auth::user()->creatorId()) ->get()->pluck('name', 'id');
                $country->prepend('Select Country', '');

                // Find country ID from country name
                $countryId = Country::where('name', $visa->country)
                    ->where('created_by', \Auth::user()->creatorId())
                    ->value('id');
                $visa->country_id = $countryId;

                $customers = Customer::where('created_by', auth()->id())->orderBy('name') ->pluck('name', 'id');
                $customers->prepend('Select Customer', '');

                $venders = Vender::where('created_by', auth()->id())->orderBy('name') ->pluck('name', 'id');
                $venders->prepend('Select Vender', '');


                return view('visa.edit', compact('visa', 'customFields', 'venders','country','customers'));
            //}
            // else
            // {
            //     return response()->json(['error' => __('Permission denied.')], 401);
            // }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {

        if(\Auth::user()->can('edit visa'))
        {
            $visa = visa::find($id);
            if($visa->created_by == \Auth::user()->creatorId())
            {
                $rules = [
                'customer_id' => 'required',
                'vender_id' => 'required',
                'name' => 'required',
                'country' => 'required',
                'visa_type' => 'required',
                'visa_fee' => 'required|numeric',
                'commission' => 'required|numeric',
                'issue_date' => 'required|date',
                'visa_status' => 'required',

                ];

                $validator = \Validator::make($request->all(), $rules);

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('visa.index')->with('error', $messages->first());
                }

                $country = Country::find($request->country);
                if(!$country) {
                    return redirect()->route('visa.index')->with('error', __('Country not found.'));
                }

                 $visa->name                = $request->name;
                 $visa->gender              = $request->gender ?? null;
                 $visa->passport_no         = $request->passport_no ?? null;
                 $visa->customer_id         = $request->customer_id;
                 $visa->vender_id           = $request->vender_id;
                 $visa->visa_type           = $request->visa_type;
                 $visa->country             = $country->name;
                 $visa->visa_fee            = $request->visa_fee;
                 $visa->commission          = $request->commission;
                 $visa->description         = $request->description ?? '';
                 $visa->visa_status         = $request->visa_status;
                 $visa->total_amount        = $request->visa_fee + $request->commission;
                 $visa->payment_status      = $visa->payment_status ?? 'unpaid';
                 $visa->issue_date          = $request->issue_date;
                $visa->save();
                CustomField::saveData($visa, $request->customField);


                DB::table('add_transaction_lines')
                    ->where('reference_id', $visa->id) // or $purchase->id depending on your structure
                    ->delete();

                 $account = ChartOfAccount::where('name','Sales Visa Income')->where('created_by' , \Auth::user()->creatorId())->first();


             $data = [
                        'account_id'         => !empty($account) ? $account->id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $visa->total_amount,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);



            $account2 = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account2) ? $account2->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $visa->total_amount,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => $visa->customer_id,
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);

            $account3 = ChartOfAccount::where('name','Cost of Visa Fee')->where('created_by' , \Auth::user()->creatorId())->first();


             $data = [
                        'account_id'         => !empty($account3) ? $account3->id : 0,
                        'transaction_type'   => 'debit',
                        'transaction_amount' => $visa->visa_fee,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => '0',
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);



            $account4 = ChartOfAccount::where('name','Accounts Payable')->where('created_by' , \Auth::user()->creatorId())->first();
                    $data    = [
                       'account_id'         => !empty($account4) ? $account4->id : 0,
                        'transaction_type'   => 'credit',
                        'transaction_amount' => $visa->visa_fee,
                        'reference'          => 'Visa',
                        'reference_id'       => $visa->id,
                        'reference_sub_id'   => $visa->id,
                        'customer_id'        => '0',
                        'vender_id'          => $visa->vender_id,
                        'date'               => $visa->issue_date,
                    ];
                    Utility::addTransactionLines($data);


                return redirect()->route('visa.index')->with('success', __('Visa successfully updated.'));
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


    public function destroy($id)
    {
        if(\Auth::user()->can('delete visa'))
        {
            $visa = visa::find($id);
            if($visa->created_by == \Auth::user()->creatorId())
            {
                $visa->delete();

                return redirect()->route('visa.index')->with('success', __('Product successfully deleted.'));
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

    public function export()
    {
        $name = 'product_service_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new visaExport(), $name . '.xlsx');

        return $data;
    }

    public function importFile()
    {
        return view('visa.import');
    }

    public function fileImport(Request $request)
    {
        session_start();

        $error = '';

        $html = '';

        if ($request->hasFile('file') && $request->file->getClientOriginalName() != '') {
            $file_array = explode(".", $request->file->getClientOriginalName());

            $extension = end($file_array);
            if ($extension == 'csv') {
                $file_data = fopen($request->file->getRealPath(), 'r');

                $file_header = fgetcsv($file_data);
                $html .= '<table class="table table-bordered"><tr>';

                for ($count = 0; $count < count($file_header); $count++) {
                    $html .= '
                            <th>
                                <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                    <option value="">Set Count Data</option>
                                    <option value="name">Name</option>
                                    <option value="sku">SKU</option>
                                    <option value="sale_price">Sale Price</option>
                                    <option value="purchase_price">Purchase Price</option>
                                    <option value="quantity">Quantity</option>
                                    <option value="description">Description</option>
                                </select>
                            </th>
                            ';
                }
                $html .= '
                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="type">Type</option>
                                    </select>
                            </th>

                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="sale_chartaccount_id">Income Account</option>
                                    </select>
                            </th>

                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="expense_chartaccount_id">Expense Account</option>
                                    </select>
                            </th>

                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="tax_id">Tax</option>
                                    </select>
                            </th>

                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="category_id">Category</option>
                                    </select>
                            </th>

                            <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="unit_id">Unit</option>
                                    </select>
                            </th>
                            ';
                $html .= '</tr>';
                $limit = 0;
                $temp_data = [];
                while (($row = fgetcsv($file_data)) !== false) {
                    $limit++;

                    $html .= '<tr>';

                    for ($count = 0; $count < count($row); $count++) {
                        $html .= '<td>' . $row[$count] . '</td>';
                    }

                    $html .= '<td>
                                <select name="type" class="form-control type" id="type" required>
                                    <option value="product">Product</option>
                                    <option value="service">Service</option>
                                </select>
                            </td>';


                    $html .= '<td>
                        <select name="sale_chartaccount_id" class="form-control sale_chartaccount_id" id="sale_chartaccount_id" required>
                            <option value="">' . __('Select Chart of Account') . '</option>';

                    $incomeTypes = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())
                        ->whereIn('name', ['Assets', 'Liabilities', 'Income'])
                        ->get();
                    $incomeChartAccounts = [];
                    foreach ($incomeTypes as $type) {
                        $accountTypes = ChartOfAccountSubType::where('type', $type->id)
                            ->where('created_by', '=', \Auth::user()->creatorId())
                            ->whereNotIn('name', ['Accounts Receivable' , 'Accounts Payable'])
                            ->get();

                        $temp = [];

                        foreach ($accountTypes as $accountType) {
                            $chartOfAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '=', 0)
                                ->where('created_by', '=', \Auth::user()->creatorId())
                                ->get();

                            $incomeSubAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '!=', 0)
                            ->where('created_by', '=', \Auth::user()->creatorId())
                            ->get();
                            $tempData = [
                                'account_name'      => $accountType->name,
                                'chart_of_accounts' => [],
                                'subAccounts'       => [],
                            ];
                            foreach ($chartOfAccounts as $chartOfAccount) {
                                $tempData['chart_of_accounts'][] = [
                                    'id'             => $chartOfAccount->id,
                                    'account_number' => $chartOfAccount->account_number,
                                    'account_name'   => $chartOfAccount->name,
                                ];
                            }

                            foreach ($incomeSubAccounts as $chartOfAccount) {
                                $tempData['subAccounts'][] = [
                                    'id'             => $chartOfAccount->id,
                                    'account_number' => $chartOfAccount->account_number,
                                    'account_name'   => $chartOfAccount->name,
                                    'parent'         => $chartOfAccount->parent,
                                    'parent_account' => !empty($chartOfAccount->parentAccount) ? $chartOfAccount->parentAccount->account : 0,
                                ];
                            }
                            $temp[$accountType->id] = $tempData;
                        }

                        $incomeChartAccounts[$type->name] = $temp;
                    }
                    // Invoice Dropdown
                    foreach ($incomeChartAccounts as $typeName => $subtypes) {
                        $html .= '<optgroup label="' . $typeName . '">';

                        foreach ($subtypes as $subtypeId => $subtypeData) {
                            $html .= '<option disabled style="color: #000; font-weight: bold;">' . $subtypeData['account_name'] . '</option>';

                            foreach ($subtypeData['chart_of_accounts'] as $chartOfAccount) {
                                $html .= '<option value="' . $chartOfAccount['id'] . '">&nbsp;&nbsp;&nbsp;' . $chartOfAccount['account_name'] . '</option>';

                                foreach ($subtypeData['subAccounts'] as $subAccount) {
                                    if ($chartOfAccount['id'] == $subAccount['parent_account']) {
                                        $html .= '<option value="' . $subAccount['id'] . '" class="ms-5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ' . $subAccount['account_name'] . '</option>';
                                    }
                                }
                            }
                        }

                        $html .= '</optgroup>';
                    }

                    $html .= '</select>
                        </td>';


                    $expenseTypes = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())
                        ->whereIn('name', ['Assets', 'Liabilities', 'Expenses', 'Costs of Goods Sold'])
                        ->get();
                    $expenseChartAccounts = [];
                    foreach ($expenseTypes as $type) {
                        $accountTypes = ChartOfAccountSubType::where('type', $type->id)
                            ->where('created_by', '=', \Auth::user()->creatorId())
                            ->whereNotIn('name', ['Accounts Receivable' , 'Accounts Payable'])
                            ->get();

                        $temp = [];

                        foreach ($accountTypes as $accountType) {
                            $chartOfAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '=', 0)
                                ->where('created_by', '=', \Auth::user()->creatorId())
                                ->get();

                            $expenseSubAccounts = ChartOfAccount::where('sub_type', $accountType->id)->where('parent', '!=', 0)
                            ->where('created_by', '=', \Auth::user()->creatorId())
                            ->get();

                            $tempData = [
                                'account_name'      => $accountType->name,
                                'chart_of_accounts' => [],
                                'subAccounts'       => [],
                            ];
                            foreach ($chartOfAccounts as $chartOfAccount) {
                                $tempData['chart_of_accounts'][] = [
                                    'id'             => $chartOfAccount->id,
                                    'account_number' => $chartOfAccount->account_number,
                                    'account_name'   => $chartOfAccount->name,
                                ];
                            }

                            foreach ($expenseSubAccounts as $chartOfAccount) {
                                $tempData['subAccounts'][] = [
                                    'id'             => $chartOfAccount->id,
                                    'account_number' => $chartOfAccount->account_number,
                                    'account_name'   => $chartOfAccount->name,
                                    'parent'         => $chartOfAccount->parent,
                                    'parent_account' => !empty($chartOfAccount->parentAccount) ? $chartOfAccount->parentAccount->account : 0,
                                ];
                            }
                            $temp[$accountType->id] = $tempData;
                        }

                        $expenseChartAccounts[$type->name] = $temp;
                    }
                    // Expense Dropdown
                    $html .= '<td>
                        <select name="expense_chartaccount_id" class="form-control expense_chartaccount_id" id="expense_chartaccount_id" required>
                            <option value="">' . __('Select Chart of Account') . '</option>';

                    foreach ($expenseChartAccounts as $typeName => $subtypes) {
                        $html .= '<optgroup label="' . $typeName . '">';

                        foreach ($subtypes as $subtypeId => $subtypeData) {
                            $html .= '<option disabled style="color: #000; font-weight: bold;">' . $subtypeData['account_name'] . '</option>';

                            foreach ($subtypeData['chart_of_accounts'] as $chartOfAccount) {
                                $html .= '<option value="' . $chartOfAccount['id'] . '">&nbsp;&nbsp;&nbsp;' . $chartOfAccount['account_name'] . '</option>';

                                foreach ($subtypeData['subAccounts'] as $subAccount) {
                                    if ($chartOfAccount['id'] == $subAccount['parent_account']) {
                                        $html .= '<option value="' . $subAccount['id'] . '" class="ms-5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ' . $subAccount['account_name'] . '</option>';
                                    }
                                }
                            }
                        }

                        $html .= '</optgroup>';
                    }

                    $html .= '</select>
                        </td>';


                    $html .= '<td>
                                <select name="tax_id" class="form-control tax_id" id="tax_id" required>;';
                    $taxes   = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                    foreach ($taxes as $key => $tax) {
                        $html .= ' <option value="' . $key . '">' . $tax . '</option>';
                    }
                    $html .= '  </select>
                            </td>';

                    $html .= '<td>
                                <select name="category_id" class="form-control category_id" id="category_id" required>;';
                    $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
                    foreach ($categories as $key => $category) {
                        $html .= ' <option value="' . $key . '">' . $category . '</option>';
                    }
                    $html .= '  </select>
                            </td>';

                    $html .= '<td>
                                <select name="unit_id" class="form-control unit_id" id="unit_id" required>;';
                    $units  = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                    foreach ($units as $key => $unit) {
                        $html .= ' <option value="' . $key . '">' . $unit . '</option>';
                    }
                    $html .= '  </select>
                            </td>';

                    $html .= '</tr>';

                    $temp_data[] = $row;

                }
                $_SESSION['file_data'] = $temp_data;
            } else {
                $error = 'Only <b>.csv</b> file allowed';
            }
        } else {

            $error = 'Please Select CSV File';
        }
        $output = array(
            'error' => $error,
            'output' => $html,
        );

        return json_encode($output);


    }

    public function fileImportModal()
    {
        return view('visa.import_modal');
    }

    public function visaImportdata(Request $request)
    {
        session_start();
        $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
        $flag = 0;
        $html .= '<table class="table table-bordered"><tr>';
        try {
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);
        } catch (\Throwable $th) {
            $html = '<h3 class="text-danger text-center">Something went wrong, Please try again</h3></br>';
            return response()->json([
                'html' => true,
                'response' => $html,
            ]);
        }

        foreach ($file_data as $key => $row) {

            try {
                $sale_chartaccount = ChartOfAccount::where('created_by', \Auth::user()->creatorId())->Where('id', $request->sale_chartaccount_id[$key])->first();
                $expense_chartaccount = ChartOfAccount::where('created_by', \Auth::user()->creatorId())->Where('id', $request->expense_chartaccount_id[$key])->first();
                $tax = Tax::where('created_by', \Auth::user()->creatorId())->Where('id', $request->tax_id[$key])->first();
                $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->Where('id', $request->category_id[$key])->first();
                $unit = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->Where('id', $request->unit_id[$key])->first();

                if (!$sale_chartaccount || !$expense_chartaccount || !$category || !$unit ) {
                    throw new \Exception();
                }

                $visa = new visa();
                $visa->name = $row[$request['name']];
                $visa->sku = $row[$request['sku']];
                $visa->sale_price = $row[$request['sale_price']];
                $visa->purchase_price = $row[$request['purchase_price']];
                $visa->quantity = (isset($request->type[$key]) && $request->type[$key] == 'product') ? $row[$request['quantity']] : 0;
                $visa->description = $row[$request['description']];
                $visa->type = $request->type[$key];
                $visa->sale_chartaccount_id = optional($sale_chartaccount)->id;
                $visa->expense_chartaccount_id = optional($expense_chartaccount)->id;
                $visa->tax_id = optional($tax)->id;
                $visa->category_id = optional($category)->id;
                $visa->unit_id = optional($unit)->id;
                $visa->created_by = \Auth::user()->creatorId();
                $visa->save();

            } catch (\Exception $e) {
                $flag = 1;
                $html .= '<tr>';

                $html .= '<td>' . (isset($row[$request['name']]) ? $row[$request['name']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['sku']]) ? $row[$request['sku']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['sale_price']]) ? $row[$request['sale_price']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['purchase_price']]) ? $row[$request['purchase_price']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['quantity']]) ? $row[$request['quantity']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['description']]) ? $row[$request['description']] : '-') . '</td>';
                $html .= '<td>' . (isset($request->type[$key]) ? $request->type[$key] : '-') . '</td>';
                $html .= '<td>' . (isset($request->sale_chartaccount_id[$key]) ? $request->sale_chartaccount_id[$key] : '-') . '</td>';
                $html .= '<td>' . (isset($request->expense_chartaccount_id[$key]) ? $request->expense_chartaccount_id[$key] : '-') . '</td>';
                $html .= '<td>' . (isset($request->tax_id[$key]) ? $request->tax_id[$key] : '-') . '</td>';
                $html .= '<td>' . (isset($request->category_id[$key]) ? $request->category_id[$key] : '-') . '</td>';
                $html .= '<td>' . (isset($request->unit_id[$key]) ? $request->unit_id[$key] : '-') . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '
                </table>
                <br />
                ';

        if ($flag == 1) {

            return response()->json([
                'html' => true,
                'response' => $html,
            ]);
        } else {
            return response()->json([
                'html' => false,
                'response' => 'Data Imported Successfully',
            ]);
        }
    }

    public function warehouseDetail($id)
    {
        $products = WarehouseProduct::with(['warehouse'])->where('product_id', '=', $id)->where('created_by', '=', \Auth::user()->creatorId())->get();
        return view('visa.detail', compact('products'));
    }

    public function searchProducts(Request $request)
    {

        $lastsegment = $request->session_key;

        if (Auth::user()->can('manage pos') && $request->ajax() && isset($lastsegment) && !empty($lastsegment)) {

            $output = "";
            if($request->war_id == '0'){
                $ids = WarehouseProduct::where('warehouse_id',1)->get()->pluck('product_id')->toArray();

                if ($request->cat_id !== '' && $request->search == '') {
                    if($request->cat_id == '0'){
                        $products = visa::getallproducts()->whereIn('visa.id',$ids)->with(['unit'])->get();

                    }else{
                        $products = visa::getallproducts()->where('category_id', $request->cat_id)->whereIn('visa.id',$ids)->with(['unit'])->get();

                    }

                } else {
                    if($request->cat_id == '0'){
                        $products = visa::getallproducts()->where('visa.'.$request->type, 'LIKE', "%{$request->search}%")->with(['unit'])->get();
                    }else{
                        $products = visa::getallproducts()->where('visa.'.$request->type, 'LIKE', "%{$request->search}%")->orWhere('category_id', $request->cat_id)->with(['unit'])->get();
                    }
                }

            }else{
                $ids = WarehouseProduct::where('warehouse_id',$request->war_id)->get()->pluck('product_id')->toArray();

                if($request->cat_id == '0'){
                    $products = visa::getallproducts()->whereIn('visa.id',$ids)->with(['unit'])->get();

                }else{
                    $products = visa::getallproducts()->whereIn('visa.id',$ids)->where('category_id', $request->cat_id)->with(['unit'])->get();

                }

            }


            if (count($products)>0)
            {
                foreach ($products as $key => $product) {
                    $quantity = $product->warehouseProduct($product->id, $request->war_id != 0 ? $request->war_id : 1);

                    $unit = (!empty($product) && !empty($product->unit)) ? $product->unit->name : '';
                        if (!empty($product->pro_image)) {
                            $image_url = ('uploads/pro_image') . '/' . $product->pro_image;
                        } else {
                            $image_url = ('uploads/pro_image') . '/default.png';
                        }
                        if ($request->session_key == 'purchases') {
                            $productprice = $product->purchase_price != 0 ? $product->purchase_price : 0;
                        } else if ($request->session_key == 'pos') {
                            $productprice = $product->sale_price != 0 ? $product->sale_price : 0;
                        } else {
                            $productprice = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;
                        }
                        $output .= '

                                    <div class="col-xl-3 col-lg-4 col-md-3 col-sm-4 col-6">
                                        <div class="tab-pane fade show active toacart w-100" data-url="' . url('add-to-cart/' . $product->id . '/' . $lastsegment) . '">
                                            <div class="position-relative card">
                                                <img alt="Image placeholder" src="' . asset(Storage::url($image_url)) . '" class="card-image avatar shadow hover-shadow-lg" style=" height: 6rem; width: 100%;">
                                                <div class="p-0 custom-card-body card-body d-flex ">
                                                    <div class="card-body mt-2 p-0 text-left card-bottom-content">
                                                    <h5 class="mb-2 text-dark product-title-name">' . $product->name . '</h5>
                                                    <h6 class="mb-2 text-dark product-title-name small">' . $product->sku . '</h6>
                                                        <small class="badge badge-primary mb-0">' . Auth::user()->priceFormat($productprice) . '</small>

                                                        <small class="top-badge badge badge-danger mb-0">' . $quantity . ' ' . $unit . '</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            ';

                }
                return Response($output);


            } else {
                $output='<div class="card card-body col-12 text-center">
                    <h5>'.__("No Product Available").'</h5>
                    </div>';
                return Response($output);
            }
        }
    }

    public function addToCart(Request $request, $id,$session_key)
    {

        if (Auth::user()->can('manage visa') && $request->ajax()) {
            $product = visa::find($id);
            $productquantity = 0;

            if ($product) {
                $productquantity = $product->getTotalProductQuantity();
            }

            if (!$product || ($session_key == 'pos' && $productquantity == 0)) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            $productname = $product->name;

            if ($session_key == 'purchases') {

                $productprice = $product->purchase_price != 0 ? $product->purchase_price : 0;
            } else if ($session_key == 'pos') {

                $productprice = $product->sale_price != 0 ? $product->sale_price : 0;
            } else {

                $productprice = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;
            }

            $originalquantity = (int)$productquantity;



            $taxes=Utility::tax($product->tax_id);

            $totalTaxRate=Utility::totalTaxRate($product->tax_id);

            $product_tax='';
            $product_tax_id=[];
            foreach($taxes as $tax){
                $product_tax.= !empty($tax)?"<span class='badge badge-primary'>". $tax->name.' ('.$tax->rate.'%)'."</span><br>":'';
                $product_tax_id[]=!empty($tax) ?$tax->id :0;
            }

            if(empty($product_tax)){
                $product_tax="-";
            }
            $producttax = $totalTaxRate;


            $tax = ($productprice * $producttax) / 100;

            $subtotal        = $productprice + $tax;
            $cart            = session()->get($session_key);
            $image_url = (!empty($product->pro_image) && Storage::exists($product->pro_image)) ? $product->pro_image : 'uploads/pro_image/'. $product->pro_image;


            $model_delete_id = 'delete-form-' . $id;

            $carthtml = '';

            $carthtml .= '<tr data-product-id="' . $id . '" id="product-id-' . $id . '">
                            <td class="cart-images">
                                <img alt="Image placeholder" src="' . asset(Storage::url($image_url)) . '" class="card-image avatar shadow hover-shadow-lg">
                            </td>

                            <td class="name">' . $productname . '</td>

                            <td class="">
                                   <span class="quantity buttons_added">
                                         <input type="button" value="-" class="minus">
                                         <input type="number" step="1" min="1" max="" name="quantity" title="' . __('Quantity') . '" class="input-number" size="4" data-url="' . url('update-cart/') . '" data-id="' . $id . '">
                                         <input type="button" value="+" class="plus">
                                   </span>
                            </td>


                            <td class="tax">' . $product_tax . '</td>

                            <td class="price">' . Auth::user()->priceFormat($productprice) . '</td>

                            <td class="subtotal">' . Auth::user()->priceFormat($subtotal) . '</td>

                            <td class="mt-3">
                                <div class="action-btn">
                                 <a href="#" class="btn btn-sm bg-danger bs-pass-para-pos" data-confirm="' . __("Are You Sure?") . '" data-text="' . __("This action can not be undone. Do you want to continue?") . '" data-confirm-yes=' . $model_delete_id . ' title="' . __('Delete') . '" data-id="' . $id . '" title="' . __('Delete') . '"   >
                                   <span class=""><i class="ti ti-trash text-white"></i></span>
                                 </a>
                                 <form method="post" action="' . url('remove-from-cart') . '"  accept-charset="UTF-8" id="' . $model_delete_id . '">
                                      <input name="_method" type="hidden" value="DELETE">
                                      <input name="_token" type="hidden" value="' . csrf_token() . '">
                                      <input type="hidden" name="session_key" value="' . $session_key . '">
                                      <input type="hidden" name="id" value="' . $id . '">
                                 </form>
                                </div>
                            </td>
                        </td>';



            // if cart is empty then this the first product
            if (!$cart) {
                $cart = [
                    $id => [
                        "name" => $productname,
                        "quantity" => 1,
                        "price" => $productprice,
                        "id" => $id,
                        "tax" => $producttax,
                        "subtotal" => $subtotal,
                        "originalquantity" => $originalquantity,
                        "product_tax"=>$product_tax,
                        "product_tax_id"=>!empty($product_tax_id)?implode(',',$product_tax_id):0,
                    ],
                ];


                if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                    return response()->json(
                        [
                            'code' => 404,
                            'status' => 'Error',
                            'error' => __('This product is out of stock!'),
                        ],
                        404
                    );
                }

                session()->put($session_key, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __(' added to cart successfully!'),
                        'product' => $cart[$id],
                        'carthtml' => $carthtml,
                    ]
                );
            }

            // if cart not empty then check if this product exist then increment quantity
            if (isset($cart[$id])) {

                $cart[$id]['quantity']++;
                $cart[$id]['id'] = $id;

                $subtotal = $cart[$id]["price"] * $cart[$id]["quantity"];
                $tax      = ($subtotal * $cart[$id]["tax"]) / 100;

                $cart[$id]["subtotal"]         = $subtotal + $tax;
                $cart[$id]["originalquantity"] = $originalquantity;

                if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                    return response()->json(
                        [
                            'code' => 404,
                            'status' => 'Error',
                            'error' => __('This product is out of stock!'),
                        ],
                        404
                    );
                }

                session()->put($session_key, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __(' added to cart successfully!'),
                        'product' => $cart[$id],
                        'carttotal' => $cart,
                    ]
                );
            }

            // if item not exist in cart then add to cart with quantity = 1
            $cart[$id] = [
                "name" => $productname,
                "quantity" => 1,
                "price" => $productprice,
                "tax" => $producttax,
                "subtotal" => $subtotal,
                "id" => $id,
                "originalquantity" => $originalquantity,
                "product_tax"=>$product_tax,
            ];

            if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            session()->put($session_key, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => $productname . __(' added to cart successfully!'),
                    'product' => $cart[$id],
                    'carthtml' => $carthtml,
                    'carttotal' => $cart,
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('This Product is not found!'),
                ],
                404
            );
        }
    }

    public function updateCart(Request $request)
    {


        $id          = $request->id;
        $quantity    = $request->quantity;
        $discount    = $request->discount;
        $session_key = $request->session_key;


        if (Auth::user()->can('manage visa') && $request->ajax() && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
            $cart = session()->get($session_key);



            if (isset($cart[$id]) && $quantity == 0) {
                unset($cart[$id]);
            }

            if ($quantity) {

                $cart[$id]["quantity"] = $quantity;

                $producttax            = isset($cart[$id]["tax"]) ? $cart[$id]["tax"] :0;
                $productprice          = isset($cart[$id]["price"]) ? $cart[$id]["price"] :0;

                $subtotal = $productprice * $quantity;
                $tax      = ($subtotal * $producttax) / 100;

                $cart[$id]["subtotal"] = $subtotal + $tax;

            }

            if (isset($cart[$id]) && ($cart[$id]["originalquantity"]) < $cart[$id]['quantity'] && $session_key == 'pos') {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            $subtotal = array_sum(array_column($cart, 'subtotal'));
            $discount = $request->discount;
            $total = $subtotal - $discount;
            $totalDiscount = Auth::user()->priceFormat($total);
            $discount = $totalDiscount;


            session()->put($session_key, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'success' => __('Cart updated successfully!'),
                    'product' => $cart,
                    'discount' => $discount,
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('This Product is not found!'),
                ],
                404
            );
        }
    }

    public function emptyCart(Request $request)
    {
        $session_key = $request->session_key;

        if (Auth::user()->can('manage visa') && isset($session_key) && !empty($session_key))
        {
            $cart = session()->get($session_key);
            if (isset($cart) && count($cart) > 0)
            {
                session()->forget($session_key);
            }

            return redirect()->back()->with('error', __('Cart is empty!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Cart cannot be empty!.'));

        }
    }

    public function warehouseemptyCart(Request $request)
    {
        $session_key = $request->session_key;

        $cart = session()->get($session_key);
        if (isset($cart) && count($cart) > 0)
        {
            session()->forget($session_key);
        }

        return response()->json();

    }

    public function removeFromCart(Request $request)
    {
        $id          = $request->id;
        $session_key = $request->session_key;
        if (Auth::user()->can('manage visa') && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
            $cart = session()->get($session_key);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put($session_key, $cart);
            }

            return redirect()->back()->with('success', __('Product removed from cart!'));
        } else {
            return redirect()->back()->with('error', __('This Product is not found!'));
        }
    }



}
