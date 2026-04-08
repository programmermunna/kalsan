<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountType;
use App\Models\CustomField;
use App\Exports\ProductServiceExport;
use App\Imports\ProductServiceImport;
use App\Models\ChartOfAccountSubType;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
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



class ProductServiceController extends Controller
{
    public function index(Request $request)
    {

        if(\Auth::user()->can('manage product & service'))
        {
            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())
    ->whereIn('type', ['product', 'service'])
    ->get()
    ->pluck('name', 'id'); $category->prepend('Select Category', '');

            if(!empty($request->category))
            {

                $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->where('category_id', $request->category)->with(['taxes','unit','category'])->get();
            }
            else
            {
                $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->with(['taxes','unit','category'])->get();
            }

            return view('productservice.index', compact('productServices', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create product & service'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'product')->get();
            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())
    ->whereIn('type', ['product', 'service'])
    ->get()
    ->pluck('name', 'id');
            $unit         = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tax          = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

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

            return view('productservice.create', compact('category', 'unit', 'tax', 'customFields', 'incomeChartAccounts', 'expenseChartAccounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {

        if(\Auth::user()->can('create product & service'))
        {

            $rules = [
                'name' => ['required', Rule::unique('product_services')->where(function ($query) {
                    return $query->where('created_by', \Auth::user()->id);
                })
                ],
                // 'sale_price' => 'required|numeric',
                // 'purchase_price' => 'required|numeric',
                // 'category_id' => 'required',
                // 'unit_id' => 'required',
                // 'type' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('productservice.index')->with('error', $messages->first());
            }

            $productService                      = new ProductService();
            $productService->name                = $request->name;
            $productService->type                = $request->type;
            // $productService->description         = $request->description;
            // $productService->sku                 = $request->sku;
            // $productService->sale_price          = $request->sale_price;
            // $productService->purchase_price      = $request->purchase_price;
            // $productService->tax_id              = !empty($request->tax_id) ? implode(',', $request->tax_id) : '';
            // $productService->unit_id             = $request->unit_id;
            // if(!empty($request->quantity))
            // {
            //     $productService->quantity        = $request->quantity;
            // }
            // else{
            //     $productService->quantity   = 0;
            // }
            // $productService->type                       = $request->type;
             //  $productService->sale_chartaccount_id       = $request->sale_chartaccount_id;
             //  $productService->expense_chartaccount_id    = $request->expense_chartaccount_id;

               $productService->sale_chartaccount_id = $request->filled('sale_chartaccount_id') ? $request->sale_chartaccount_id : null;
               $productService->expense_chartaccount_id = $request->filled('expense_chartaccount_id') ? $request->expense_chartaccount_id : null;
            // $productService->category_id                = $request->category_id;

            // if(!empty($request->pro_image))
            // {

            //     if($productService->pro_image)
            //     {
            //         $path = storage_path('uploads/pro_image' . $productService->pro_image);
            //         if(file_exists($path))
            //         {
            //             \File::delete($path);
            //         }
            //     }
            //     $fileName = $request->pro_image->getClientOriginalName();
            //     $productService->pro_image = $fileName;
            //     $dir        = 'uploads/pro_image';
            //     $path = Utility::upload_file($request,'pro_image',$fileName,$dir,[]);
            //     $request->pro_image  = '';
            //     $productService->save();
            // }

            $productService->created_by       = \Auth::user()->creatorId();
            $productService->save();
            CustomField::saveData($productService, $request->customField);

            return redirect()->route('productservice.index')->with('success', __('Destination successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show()
    {
        return redirect()->route('productservice.index');
    }


    public function edit($id)
    {
        $productService = ProductService::find($id);

        if(\Auth::user()->can('edit product & service'))
        {
            if($productService->created_by == \Auth::user()->creatorId())
            {
                $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId()) ->whereIn('type', ['product', 'income'])->get()->pluck('name', 'id');
                $unit     = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $tax      = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                $productService->customField = CustomField::getData($productService, 'product');
                $customFields                = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'product')->get();
                $productService->tax_id      = explode(',', $productService->tax_id);


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
                            'account_name' => $accountType->name,
                            'chart_of_accounts' => [],
                            'subAccounts' => [],
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

                return view('productservice.edit', compact('category', 'unit', 'tax', 'productService', 'customFields', 'incomeChartAccounts', 'expenseChartAccounts'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {

        if(\Auth::user()->can('edit product & service'))
        {
            $productService = ProductService::find($id);
            if($productService->created_by == \Auth::user()->creatorId())
            {
                $rules = [
                    'name' => 'required',
                    // 'sku' => 'required', Rule::unique('product_services')->ignore($productService->id),
                    // 'sale_price' => 'required|numeric',
                    // 'purchase_price' => 'required|numeric',
                    // 'category_id' => 'required',
                    // 'unit_id' => 'required',
                    'type' => 'required',

                ];

                $validator = \Validator::make($request->all(), $rules);

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('productservice.index')->with('error', $messages->first());
                }

                $productService->name           = $request->name;
                $productService->description    = $request->description;
                $productService->sku            = $request->sku;
                $productService->sale_price     = $request->sale_price;
                $productService->purchase_price = $request->purchase_price;
                $productService->tax_id         = !empty($request->tax_id) ? implode(',', $request->tax_id) : '';
                $productService->unit_id        = $request->unit_id;

                if(!empty($request->quantity))
                {
                    $productService->quantity   = $request->quantity;
                }
                else{
                    $productService->quantity   = 0;
                }
                $productService->type                       = $request->type;
                $productService->sale_chartaccount_id       = $request->sale_chartaccount_id;
                $productService->expense_chartaccount_id    = $request->expense_chartaccount_id;
                $productService->category_id                = $request->category_id;

                if(!empty($request->pro_image))
                {

                    if($productService->pro_image)
                    {
                        $path = storage_path('uploads/pro_image' . $productService->pro_image);
                        if(file_exists($path))
                        {
                            \File::delete($path);
                        }
                    }
                    $fileName = $request->pro_image->getClientOriginalName();
                    $productService->pro_image = $fileName;
                    $dir        = 'uploads/pro_image';
                    $request->pro_image  = '';
                }



                $productService->created_by     = \Auth::user()->creatorId();
                $productService->save();
                CustomField::saveData($productService, $request->customField);

                return redirect()->route('productservice.index')->with('success', __('Product successfully updated.'));
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
        if(\Auth::user()->can('delete product & service'))
        {
            $productService = ProductService::find($id);
            if($productService->created_by == \Auth::user()->creatorId())
            {
                $productService->delete();

                return redirect()->route('productservice.index')->with('success', __('Product successfully deleted.'));
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

        $data = Excel::download(new ProductServiceExport(), $name . '.xlsx');

        return $data;
    }

    public function importFile()
    {
        return view('productservice.import');
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
        return view('productservice.import_modal');
    }

    public function productserviceImportdata(Request $request)
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

                $productService = new ProductService();
                $productService->name = $row[$request['name']];
                $productService->sku = $row[$request['sku']];
                $productService->sale_price = $row[$request['sale_price']];
                $productService->purchase_price = $row[$request['purchase_price']];
                $productService->quantity = (isset($request->type[$key]) && $request->type[$key] == 'product') ? $row[$request['quantity']] : 0;
                $productService->description = $row[$request['description']];
                $productService->type = $request->type[$key];
                $productService->sale_chartaccount_id = optional($sale_chartaccount)->id;
                $productService->expense_chartaccount_id = optional($expense_chartaccount)->id;
                $productService->tax_id = optional($tax)->id;
                $productService->category_id = optional($category)->id;
                $productService->unit_id = optional($unit)->id;
                $productService->created_by = \Auth::user()->creatorId();
                $productService->save();

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
        return view('productservice.detail', compact('products'));
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
                        $products = ProductService::getallproducts()->whereIn('product_services.id',$ids)->with(['unit'])->get();

                    }else{
                        $products = ProductService::getallproducts()->where('category_id', $request->cat_id)->whereIn('product_services.id',$ids)->with(['unit'])->get();

                    }

                } else {
                    if($request->cat_id == '0'){
                        $products = ProductService::getallproducts()->where('product_services.'.$request->type, 'LIKE', "%{$request->search}%")->with(['unit'])->get();
                    }else{
                        $products = ProductService::getallproducts()->where('product_services.'.$request->type, 'LIKE', "%{$request->search}%")->orWhere('category_id', $request->cat_id)->with(['unit'])->get();
                    }
                }

            }else{
                $ids = WarehouseProduct::where('warehouse_id',$request->war_id)->get()->pluck('product_id')->toArray();

                if($request->cat_id == '0'){
                    $products = ProductService::getallproducts()->whereIn('product_services.id',$ids)->with(['unit'])->get();

                }else{
                    $products = ProductService::getallproducts()->whereIn('product_services.id',$ids)->where('category_id', $request->cat_id)->with(['unit'])->get();

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

        if (Auth::user()->can('manage product & service') && $request->ajax()) {
            $product = ProductService::find($id);
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


        if (Auth::user()->can('manage product & service') && $request->ajax() && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
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

        if (Auth::user()->can('manage product & service') && isset($session_key) && !empty($session_key))
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
        if (Auth::user()->can('manage product & service') && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
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
