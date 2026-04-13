<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Transaction;
use App\Models\Utility;
use Auth;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{

    public function dashboard()
    {
        $data['invoiceChartData'] = \Auth::user()->invoiceChartData();

        return view('customer.dashboard', $data);
    }

    public function index()
    {
        if(\Auth::user()->can('manage customer'))
        {
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->paginate(10);

            // Calculate balance for each customer using the provided query
            $balances = DB::table('add_transaction_lines as t')
                ->join('customers as c', 't.customer_id', '=', 'c.id')
                ->where('c.created_by', '=', \Auth::user()->creatorId())
                ->where('t.customer_id', '!=', 0)
                ->whereNotNull('t.customer_id')
                ->select('t.customer_id', 'c.name', DB::raw('SUM(t.credit - t.debit) as balance'))
                ->groupBy('t.customer_id')
                ->get()
                ->keyBy('customer_id');

            // Attach balance to each customer
            foreach ($customers as $customer) {
                $customer->balance = isset($balances[$customer->id])
                    ? $balances[$customer->id]->balance
                    : 0;
            }

            return view('customer.index', compact('customers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create customer'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

            return view('customer.create', compact('customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create customer'))
        {

            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
               // 'email' => 'required|email|unique:customers',
                'cust_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'cust_document' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            ];


            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('customer.index')->with('error', $messages->first());
            }

            $objCustomer    = \Auth::user();
            $creator        = User::find($objCustomer->creatorId());
            $total_customer = $objCustomer->countCustomers();


                $default_language          = DB::table('settings')->select('value')->where('name', 'default_language')->first();

                $customer                  = new Customer();
                $customer->customer_id     = $this->customerNumber();
                $customer->name            = $request->name;
                $customer->mother_name     = $request->mother_name;
                $customer->contact         = $request->contact;
                $customer->email           = $request->email;
                $customer->type            = $request->type;
                $customer->reg_date        = $request->reg_date;
                $customer->dob             = $request->dob;
                $customer->pob             = $request->pob;
                $customer->body             = $request->body;
                $customer->eye             = $request->eye;
                $customer->gender          = $request->gender;
                $customer->serial_no       = $request->serial_no;
                $customer->cust_image      = $request->cust_image;
                $customer->cust_document   = $request->cust_document;
                $customer->billing_address = $request->billing_address;
                $customer->billing_country = $request->billing_country;
                $customer->billing_state   = $request->billing_state;
                $customer->billing_city    = $request->billing_city;
                $customer->created_by      = \Auth::user()->creatorId();

                // Handle customer image upload
                if($request->hasFile('cust_image') && $request->file('cust_image')->isValid()) {
                    $fileName = time() . '_' . $request->cust_image->getClientOriginalName();
                    $dir = 'uploads/cust_image';
                    $upload_result = Utility::upload_file($request, 'cust_image', $fileName, $dir, []);

                    if($upload_result['flag'] == 1) {
                        $customer->cust_image = $fileName;
                    } else {
                        return redirect()->back()->with('error', $upload_result['msg'])->withInput();
                    }
                }

                // Handle customer document upload
                if($request->hasFile('cust_document') && $request->file('cust_document')->isValid()) {
                    $fileName = time() . '_' . $request->cust_document->getClientOriginalName();
                    $dir = 'uploads/cust_document';
                    $upload_result = Utility::upload_file($request, 'cust_document', $fileName, $dir, []);

                    if($upload_result['flag'] == 1) {
                        $customer->cust_document = $fileName;
                    } else {
                        return redirect()->back()->with('error', $upload_result['msg'])->withInput();
                    }
                }

                // $customer->shipping_name    = $request->shipping_name;
                // $customer->shipping_country = $request->shipping_country;
                // $customer->shipping_state   = $request->shipping_state;
                // $customer->shipping_city    = $request->shipping_city;
                // $customer->shipping_phone   = $request->shipping_phone;
                // $customer->shipping_zip     = $request->shipping_zip;
                // $customer->shipping_address = $request->shipping_address;

                 /* if(!empty($request->cust_image))
                  {

                     if($customer->cust_image)
                        {
                          $path = storage_path('uploads/cust_image' . $customer->cust_image);
                          if(file_exists($path))
                           {
                             \File::delete($path);
                           }
                        }
                    $fileName = $request->cust_image->getClientOriginalName();
                    $customer->cust_image = $fileName;
                    $dir        = 'uploads/cust_image';
                    $path = Utility::upload_file($request,'cust_image',$fileName,$dir,[]);
                    $request->cust_image  = '';
                    //$customer->save();
                 } */
                   /*  if(!empty($request->cust_image))
{
    // Check if it's actually an uploaded file
    if($request->hasFile('cust_image') && $request->file('cust_image')->isValid()) {
        if($customer->cust_image)
        {
            $path = storage_path('uploads/cust_image/' . $customer->cust_image);
            if(file_exists($path))
            {
                \File::delete($path);
            }
        }

        $fileName = $request->cust_image->getClientOriginalName();
        $customer->cust_image = $fileName;
        $dir = 'uploads/cust_image';
        $path = Utility::upload_file($request, 'cust_image', $fileName, $dir, []);

        // Note: You're already saving the customer later, so remove this save
        // $customer->save();
    }
    // If it's a string (existing filename), don't process it as a file upload
}
                 if(!empty($request->cust_document))
                  {

                     if($customer->cust_document)
                        {
                          $path = storage_path('uploads/cust_document' . $customer->cust_document);
                          if(file_exists($path))
                           {
                             \File::delete($path);
                           }
                        }
                    $fileName = $request->cust_document->getClientOriginalName();
                    $customer->cust_document = $fileName;
                    $dir        = 'uploads/cust_document';
                    $path = Utility::upload_file($request,'cust_document',$fileName,$dir,[]);
                    $request->cust_document  = '';
                    //$customer->save();
                 } */

                $customer->lang = !empty($default_language) ? $default_language->value : '';
                $customer->balance          = $request->balance ?? 0;

                // Handle password and login enable for Travel Agency type
                $enableLogin = 0;
                if($request->type == 'Travel Agency' && !empty($request->password_switch) && $request->password_switch == 'on')
                {
                    $enableLogin = 1;
                    $validator = \Validator::make(
                        $request->all(), ['password' => 'required|min:6']
                    );

                    if($validator->fails())
                    {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                    $customer->password = \Hash::make($request->password);
                }
                $customer->is_enable_login = $enableLogin;

                $customer->save();
                CustomField::saveData($customer, $request->customField);

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $customerNotificationArr = [
                    'user_name' => \Auth::user()->name,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                ];

                //Twilio Notification
                if(isset($setting['twilio_customer_notification']) && $setting['twilio_customer_notification'] ==1)
                {
                    Utility::send_twilio_msg($request->contact,'new_customer', $customerNotificationArr);
                }

            return redirect()->route('customer.index')->with('success', __('Customer successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        try{
            $id       = \Crypt::decrypt($ids);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        $customer = Customer::find($id);
        $customer->customField = CustomField::getShowData($customer, 'customer');

        return view('customer.show', compact('customer'));
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit customer'))
        {
            $customer              = Customer::find($id);
            $customer->customField = CustomField::getData($customer, 'customer');

            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

            return view('customer.edit', compact('customer', 'customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


   public function update(Request $request, Customer $customer)
    {

        if(\Auth::user()->can('edit customer'))
        {

            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            ];


            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('customer.index')->with('error', $messages->first());
            }

                $customer->name            = $request->name;
                $customer->mother_name     = $request->mother_name;
                $customer->contact         = $request->contact;
                $customer->email           = $request->email;
                $customer->type            = $request->type;
                $customer->reg_date        = $request->reg_date;
                $customer->dob             = $request->dob;
                $customer->pob             = $request->pob;
                $customer->body            = $request->body;
                $customer->eye             = $request->eye;
                $customer->gender          = $request->gender;
                $customer->serial_no       = $request->serial_no;
                $customer->cust_image      = $request->cust_image;
                $customer->cust_document   = $request->cust_document;
                $customer->billing_address = $request->billing_address;
                $customer->billing_country = $request->billing_country;
                $customer->billing_state   = $request->billing_state;
                $customer->billing_city    = $request->billing_city;
                $customer->created_by      = \Auth::user()->creatorId();


                if($request->hasFile('cust_image') && $request->file('cust_image')->isValid()) {
                    $fileName = time() . '_' . $request->cust_image->getClientOriginalName();
                    $dir = 'uploads/cust_image';
                    $upload_result = Utility::upload_file($request, 'cust_image', $fileName, $dir, []);

                    if($upload_result['flag'] == 1) {
                        $customer->cust_image = $fileName;
                    } else {
                        return redirect()->back()->with('error', $upload_result['msg'])->withInput();
                    }
                }

                // Handle customer document upload
                if($request->hasFile('cust_document') && $request->file('cust_document')->isValid()) {
                    $fileName = time() . '_' . $request->cust_document->getClientOriginalName();
                    $dir = 'uploads/cust_document';
                    $upload_result = Utility::upload_file($request, 'cust_document', $fileName, $dir, []);

                    if($upload_result['flag'] == 1) {
                        $customer->cust_document = $fileName;
                    } else {
                        return redirect()->back()->with('error', $upload_result['msg'])->withInput();
                    }
                }

            // Handle password and login enable for Travel Agency type
            if($customer->type == 'Travel Agency' || $request->type == 'Travel Agency')
            {
                $enableLogin = $customer->is_enable_login ?? 0;
                if(!empty($request->password_switch) && $request->password_switch == 'on')
                {
                    $enableLogin = 1;
                    if(!empty($request->password))
                    {
                        $validator = \Validator::make(
                            $request->all(), ['password' => 'required|min:6']
                        );

                        if($validator->fails())
                        {
                            return redirect()->back()->with('error', $validator->errors()->first());
                        }
                        $customer->password = \Hash::make($request->password);
                    }
                }
                else
                {
                    $enableLogin = 0;
                }
                $customer->is_enable_login = $enableLogin;
            }

            $customer->save();

            CustomField::saveData($customer, $request->customField);

            return redirect()->route('customer.index')->with('success', __('Customer successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function destroy(Customer $customer)
    {
        if(\Auth::user()->can('delete customer'))
        {
            if($customer->created_by == \Auth::user()->creatorId())
            {
                $customer->delete();

                return redirect()->route('customer.index')->with('success', __('Customer successfully deleted.'));
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

    function customerNumber()
    {
        $latest = Customer::where('created_by', '=', \Auth::user()->creatorId())->latest('customer_id')->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->customer_id + 1;
    }

    public function customerLogout(Request $request)
    {
        \Auth::guard('customer')->logout();

        $request->session()->invalidate();

        return redirect()->route('customer.login');
    }

    public function payment(Request $request)
    {

        if(\Auth::user()->can('manage customer payment'))
        {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer')->where('type', 'Payment');
            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();

            return view('customer.payment', compact('payments', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {
        if(\Auth::user()->can('manage customer payment'))
        {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer');

            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();

            return view('customer.transaction', compact('transactions', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail              = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'customer');
        $customFields            = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

        return view('customer.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);

        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'contact' => 'required',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                    ]
        );

        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            if(File::exists($image_path))
            {
                File::delete($image_path);
            }

            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function editBilling(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'billing_name' => 'required',
                        'billing_country' => 'required',
                        'billing_state' => 'required',
                        'billing_city' => 'required',
                        'billing_phone' => 'required',
                        'billing_zip' => 'required',
                        'billing_address' => 'required',
                    ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'shipping_name' => 'required',
                        'shipping_country' => 'required',
                        'shipping_state' => 'required',
                        'shipping_city' => 'required',
                        'shipping_phone' => 'required',
                        'shipping_zip' => 'required',
                        'shipping_address' => 'required',
                    ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }


    public function changeLanquage($lang)
    {

        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));

    }


    public function export()
    {
        $name = 'customer_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new CustomerExport(), $name . '.xlsx');

        return $data;
    }

    public function importFile()
    {
        return view('customer.import');
    }


    public function customerImportdata(Request $request)
    {
        session_start();
        $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
        $flag = 0;
        $html .= '<table class="table table-bordered"><tr>';
        try {
            $request = $request->data;
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

            $customerByEmail = Customer::where('email', $row[$request['email']])->first();

            if(empty($customerByEmail)){
                try {
                    $customerData = new Customer();
                    $customerData->customer_id      = $this->customerNumber();
                    $customerData->name             = $row[$request['name']];
                    $customerData->email            = $row[$request['email']];
                    $customerData->contact          = $row[$request['contact']];
                    $customerData->is_active        = 1;
                    $customerData->billing_name     = $row[$request['billing_name']];
                    $customerData->billing_country  = $row[$request['billing_country']];
                    $customerData->billing_state    = $row[$request['billing_state']];
                    $customerData->billing_city     = $row[$request['billing_city']];
                    $customerData->billing_phone    = $row[$request['billing_phone']];
                    $customerData->billing_zip      = $row[$request['billing_zip']];
                    $customerData->billing_address  = $row[$request['billing_address']];
                    $customerData->shipping_name    = $row[$request['shipping_name']];
                    $customerData->shipping_country = $row[$request['shipping_country']];
                    $customerData->shipping_state   = $row[$request['shipping_state']];
                    $customerData->shipping_city    = $row[$request['shipping_city']];
                    $customerData->shipping_phone   = $row[$request['shipping_phone']];
                    $customerData->shipping_zip     = $row[$request['shipping_zip']];
                    $customerData->shipping_address = $row[$request['shipping_address']];
                    $customerData->balance          = $row[$request['balance']];
                    $customerData->created_by       = \Auth::user()->creatorId();
                    $customerData->save();

                } catch (\Exception $e) {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . (isset($row[$request['name']]) ? $row[$request['name']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['email']]) ? $row[$request['email']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['contact']]) ? $row[$request['contact']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_name']]) ? $row[$request['billing_name']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_country']]) ? $row[$request['billing_country']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_state']]) ? $row[$request['billing_state']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_city']]) ? $row[$request['billing_city']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_phone']]) ? $row[$request['billing_phone']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_zip']]) ? $row[$request['billing_zip']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['billing_address']]) ? $row[$request['billing_address']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_name']]) ? $row[$request['shipping_name']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_country']]) ? $row[$request['shipping_country']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_state']]) ? $row[$request['shipping_state']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_city']]) ? $row[$request['shipping_city']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_phone']]) ? $row[$request['shipping_phone']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_zip']]) ? $row[$request['shipping_zip']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['shipping_address']]) ? $row[$request['shipping_address']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['balance']]) ? $row[$request['balance']] : '-') . '</td>';

                    $html .= '</tr>';
                }
            } else {
                $flag = 1;
                $html .= '<tr>';

                $html .= '<td>' . (isset($row[$request['name']]) ? $row[$request['name']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['email']]) ? $row[$request['email']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['contact']]) ? $row[$request['contact']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_name']]) ? $row[$request['billing_name']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_country']]) ? $row[$request['billing_country']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_state']]) ? $row[$request['billing_state']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_city']]) ? $row[$request['billing_city']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_phone']]) ? $row[$request['billing_phone']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_zip']]) ? $row[$request['billing_zip']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['billing_address']]) ? $row[$request['billing_address']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_name']]) ? $row[$request['shipping_name']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_country']]) ? $row[$request['shipping_country']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_state']]) ? $row[$request['shipping_state']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_city']]) ? $row[$request['shipping_city']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_phone']]) ? $row[$request['shipping_phone']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_zip']]) ? $row[$request['shipping_zip']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['shipping_address']]) ? $row[$request['shipping_address']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['balance']]) ? $row[$request['balance']] : '-') . '</td>';

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
}
