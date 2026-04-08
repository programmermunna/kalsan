<?php

namespace App\Http\Controllers;

use App\Exports\VenderExport;
use App\Imports\VenderImport;
use App\Models\CustomField;
use App\Models\Transaction;
use App\Models\Utility;
use App\Models\Vender;
use Auth;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class VenderController extends Controller
{

    public function dashboard()
    {
        $data['billChartData'] = \Auth::user()->billChartData();

        return view('vender.dashboard', $data);
    }

    public function index()
    {
        if (\Auth::user()->can('manage vender')) {
            // Use the provided query: SELECT v.name, sum(credit) as Total Amount, sum(debit) as Paid Amount, sum(credit-debit) as balance
            $vendorFinancials = DB::table('add_transaction_lines as t')
                ->join('venders as v', 't.vender_id', '=', 'v.id')
                ->where('v.created_by', '=', \Auth::user()->creatorId())
                ->where('t.vender_id', '!=', 0)
                ->whereNotNull('t.vender_id')
                ->select(
                    'v.id',
                    'v.name',
                    'v.contact',
                    'v.email',
                    'v.tax_number',
                    'v.commission',
                    'v.vender_id',
                    'v.is_active',
                    DB::raw('SUM(t.credit) as total_amount'),
                    DB::raw('SUM(t.debit) as paid_amount'),
                    DB::raw('SUM(t.credit - t.debit) as balance')
                )
                ->groupBy('t.vender_id', 'v.id', 'v.name', 'v.contact', 'v.email', 'v.tax_number', 'v.commission', 'v.vender_id', 'v.is_active')
                ->get();

            // Get all vendors (including those without transactions)
            $allVenders = Vender::where('created_by', \Auth::user()->creatorId())->get();
            
            // Create a map of vendors with financial data by vender_id
            $vendorMap = $vendorFinancials->keyBy('id');
            
            // Merge financial data with all vendors
            $venders = $allVenders->map(function ($vender) use ($vendorMap) {
                $financial = $vendorMap->get($vender->id);
                if ($financial) {
                    $vender->total_amount = $financial->total_amount ?? 0;
                    $vender->paid_amount = $financial->paid_amount ?? 0;
                    $vender->balance = $financial->balance ?? 0;
                } else {
                    $vender->total_amount = 0;
                    $vender->paid_amount = 0;
                    $vender->balance = 0;
                }
                return $vender;
            });

            return view('vender.index', compact('venders'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create vender')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'vendor')->get();

            return view('vender.create', compact('customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create vender')) {
            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'email' => 'required|email|unique:venders',
            ];
            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('vender.index')->with('error', $messages->first());
            }
            $objVendor    = \Auth::user();
            $creator      = User::find($objVendor->creatorId());
            $total_vendor = $objVendor->countVenders();

            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();

            $vender                   = new Vender();
            $vender->vender_id        = $this->venderNumber();
            $vender->name             = $request->name;
            $vender->contact          = $request->contact;
            $vender->email            = $request->email;
            $vender->tax_number      = $request->tax_number;
            $vender->created_by       = \Auth::user()->creatorId();
            $vender->billing_name     = $request->billing_name;
            $vender->billing_country  = $request->billing_country;
            $vender->billing_state    = $request->billing_state;
            $vender->billing_city     = $request->billing_city;
            $vender->billing_phone    = $request->billing_phone;
            $vender->billing_zip      = $request->billing_zip;
            $vender->billing_address  = $request->billing_address;
            $vender->shipping_name    = $request->shipping_name;
            $vender->shipping_country = $request->shipping_country;
            $vender->shipping_state   = $request->shipping_state;
            $vender->shipping_city    = $request->shipping_city;
            $vender->shipping_phone   = $request->shipping_phone;
            $vender->shipping_zip     = $request->shipping_zip;
            $vender->shipping_address = $request->shipping_address;
            $vender->lang             = !empty($default_language) ? $default_language->value : '';
            $vender->commission          = $request->commission ?? 0;
            $vender->balance          = $request->balance ?? 0;
            $vender->save();
            CustomField::saveData($vender, $request->customField);


            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $vendorNotificationArr = [
                'user_name' => \Auth::user()->name,
                'vendor_name' => $vender->name,
                'vendor_email' => $vender->email,
            ];

            //Twilio Notification
            if (isset($setting['twilio_vender_notification']) && $setting['twilio_vender_notification'] == 1) {
                Utility::send_twilio_msg($request->contact, 'new_vendor', $vendorNotificationArr);
            }

            return redirect()->route('vender.index')->with('success', __('Vendor successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        try {
            $id       = Crypt::decrypt($ids);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Vendor Not Found.'));
        }
        $id     = \Crypt::decrypt($ids);
        $vendor = Vender::find($id);
        $vendor->customField = CustomField::getShowData($vendor, 'vendor');

        return view('vender.show', compact('vendor'));
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit vender')) {
            $vender              = Vender::find($id);
            $vender->customField = CustomField::getData($vender, 'vendor');

            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'vendor')->get();

            return view('vender.edit', compact('vender', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Vender $vender)
    {
        if (\Auth::user()->can('edit vender')) {

            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            ];


            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('vender.index')->with('error', $messages->first());
            }

            $vender->name             = $request->name;
            $vender->contact          = $request->contact;
            $vender->tax_number      = $request->tax_number;
            $vender->created_by       = \Auth::user()->creatorId();
            $vender->billing_name     = $request->billing_name;
            $vender->billing_country  = $request->billing_country;
            $vender->billing_state    = $request->billing_state;
            $vender->billing_city     = $request->billing_city;
            $vender->billing_phone    = $request->billing_phone;
            $vender->billing_zip      = $request->billing_zip;
            $vender->billing_address  = $request->billing_address;
            $vender->shipping_name    = $request->shipping_name;
            $vender->shipping_country = $request->shipping_country;
            $vender->shipping_state   = $request->shipping_state;
            $vender->shipping_city    = $request->shipping_city;
            $vender->shipping_phone   = $request->shipping_phone;
            $vender->shipping_zip     = $request->shipping_zip;
            $vender->shipping_address = $request->shipping_address;
            $vender->commission          = $request->commission ?? 0;
            $vender->balance          = $request->balance ?? 0;
            $vender->save();
            CustomField::saveData($vender, $request->customField);

            return redirect()->route('vender.index')->with('success', __('Vendor successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Vender $vender)
    {
        if (\Auth::user()->can('delete vender')) {
            if ($vender->created_by == \Auth::user()->creatorId()) {
                $vender->delete();

                return redirect()->route('vender.index')->with('success', __('Vendor successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function venderNumber()
    {
        $latest = Vender::where('created_by', '=', \Auth::user()->creatorId())->latest('vender_id')->first();
        if (!$latest) {
            return 1;
        }

        return $latest->vender_id + 1;
    }

    public function venderLogout(Request $request)
    {
        \Auth::guard('vender')->logout();

        $request->session()->invalidate();

        return redirect()->route('vender.login');
    }

    public function payment(Request $request)
    {

        if (\Auth::user()->can('manage vender payment')) {
            $category = [
                'Bill' => 'Bill',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('created_by', \Auth::user()->creatorId())->where('user_type', 'Vender')->where('type', 'Payment');
            if (!empty($request->date)) {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();


            return view('vender.payment', compact('payments', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {

        if (\Auth::user()->can('manage vender transaction')) {


            $category = [
                'Bill' => 'Bill',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Vender');

            if (!empty($request->date)) {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();


            return view('vender.transaction', compact('transactions', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail              = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'vendor');
        $customFields            = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'vendor')->get();

        return view('vender.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {

        $userDetail = \Auth::user();
        $user       = Vender::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'contact' => 'required',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );
        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function editBilling(Request $request)
    {

        $userDetail = \Auth::user();
        $user       = Vender::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
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
            'success',
            'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Vender::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
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
            'success',
            'Profile successfully updated.'
        );
    }

    public function changeLanquage($lang)
    {
        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language successfully change.'));
    }

    public function export()
    {
        $name = 'vendor_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new VenderExport(), $name . '.xlsx');

        return $data;
    }

    public function importFile()
    {
        return view('vender.import');
    }


    public function venderImportdata(Request $request)
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

            $vendorByEmail = Vender::where('email', $row[$request['email']])->first();

            if(empty($vendorByEmail)){
                try {
                    $vendorData            = new Vender();
                    $vendorData->vender_id = $this->venderNumber();
                    $vendorData->name             = $row[$request['name']];
                    $vendorData->email            = $row[$request['email']];
                    $vendorData->contact          = $row[$request['contact']];
                    $vendorData->tax_number       = $row[$request['tax_number']];
                    // $vendorData->billing_name     = $row[$request['billing_name']];
                    // $vendorData->billing_country  = $row[$request['billing_country']];
                    // $vendorData->billing_state    = $row[$request['billing_state']];
                    // $vendorData->billing_city     = $row[$request['billing_city']];
                    // $vendorData->billing_phone    = $row[$request['billing_phone']];
                    // $vendorData->billing_zip      = $row[$request['billing_zip']];
                    $vendorData->billing_address  = $row[$request['billing_address']];
                    // $vendorData->shipping_name    = $row[$request['shipping_name']];
                    // $vendorData->shipping_country = $row[$request['shipping_country']];
                    // $vendorData->shipping_state   = $row[$request['shipping_state']];
                    // $vendorData->shipping_city    = $row[$request['shipping_city']];
                    // $vendorData->shipping_phone   = $row[$request['shipping_phone']];
                    // $vendorData->shipping_zip     = $row[$request['shipping_zip']];
                    // $vendorData->shipping_address = $row[$request['shipping_address']];
                    $vendorData->balance          = $row[$request['balance']];
                    $vendorData->commission          = $row[$request['commission']];
                    $vendorData->created_by       = \Auth::user()->creatorId();
                    $vendorData->save();

                } catch (\Exception $e) {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . (isset($row[$request['vender_id']]) ? $row[$request['vender_id']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['name']]) ? $row[$request['name']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['email']]) ? $row[$request['email']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['contact']]) ? $row[$request['contact']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['tax_number']]) ? $row[$request['tax_number']] : '-') . '</td>';
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
                    $html .= '<td>' . (isset($row[$request['commission']]) ? $row[$request['commission']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['balance']]) ? $row[$request['balance']] : '-') . '</td>';

                    $html .= '</tr>';
                }
            } else {
                $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . (isset($row[$request['vender_id']]) ? $row[$request['vender_id']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['name']]) ? $row[$request['name']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['email']]) ? $row[$request['email']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['contact']]) ? $row[$request['contact']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['tax_number']]) ? $row[$request['tax_number']] : '-') . '</td>';
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
                    $html .= '<td>' . (isset($row[$request['commission']]) ? $row[$request['commission']] : '-') . '</td>';
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
