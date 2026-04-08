<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Imports\EmployeesImport;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Document;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\ExperienceCertificate;
use App\Models\JoiningLetter;
use App\Models\NOC;
use App\Models\PaySlip;
use App\Models\Termination;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

//use Faker\Provider\File;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage employee')) {
            if (Auth::user()->type == 'Employee') {
                $employees = Employee::where('user_id', '=', Auth::user()->id)->with(['branch', 'department', 'designation'])->paginate(10);
            } else {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->with(['branch', 'department', 'designation'])->paginate(10);
            }

            return view('employee.index', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create employee')) {
            $company_settings = Utility::settings();
            $documents = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employees = User::where('created_by', \Auth::user()->creatorId())->get();
            $employeesId = \Auth::user()->employeeIdFormat($this->employeeNumber());

            return view('employee.create', compact('employees', 'employeesId', 'departments', 'designations', 'documents', 'branches', 'company_settings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create employee')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'dob' => 'required',
                    'phone' => 'required',
                    'address' => 'required',
                    'email' => 'required|unique:users',
                    'password' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'designation_id' => 'required',
                    // 'biometric_emp_id' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $objUser = User::find(\Auth::user()->creatorId());
            $total_employee = $objUser->countEmployees();

            $user = User::create(
                [
                    'name' => $request['name'],
                    'email' => $request['email'],
                    // 'gender'=>$request['gender'],
                    'password' => Hash::make($request['password']),
                    'type' => 'employee',
                    'lang' => 'en',
                    'created_by' => \Auth::user()->creatorId(),
                ]
            );
            $user->save();
            $user->assignRole('Employee');

            if (!empty($request->document) && !is_null($request->document)) {
                $document_implode = implode(',', array_keys($request->document));
            } else {
                $document_implode = null;
            }

            $employee = Employee::create(
                [
                    'user_id' => $user->id,
                    'name' => $request['name'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'employee_id' => $this->employeeNumber(),
                    // 'biometric_emp_id' => !empty($request['biometric_emp_id']) ? $request['biometric_emp_id'] : '',
                    // 'biometric_emp_id' => '-',
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => $request['company_doj'],
                    'documents' => $document_implode,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_identifier_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['tax_payer_id'],
                    'created_by' => \Auth::user()->creatorId(),
                ]
            );

            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {

                    $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('document')[$key]->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = storage_path('uploads/document/');
                    $image_path = $dir . $filenameWithExt;

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $path = $request->file('document')[$key]->storeAs('uploads/document/', $fileNameToStore);
                    $employee_document = EmployeeDocument::create(
                        [
                            'employee_id' => $employee['employee_id'],
                            'document_id' => $key,
                            'document_value' => $fileNameToStore,
                            'created_by' => \Auth::user()->creatorId(),
                        ]
                    );
                    $employee_document->save();

                }

            }

            $setings = Utility::settings();

            if ($setings['new_user'] == 1) {
                $userArr = [
                    'email' => $user->email,
                    'password' => $user->password,
                ];

                $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $userArr);
                return redirect()->route('employee.index')->with('success', __('Employee successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }
            return redirect()->route('employee.index')->with('success', __('Employee  successfully created.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        try{
            $id = Crypt::decrypt($id);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        if (\Auth::user()->can('edit employee')) {
            $documents = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branches->prepend('Select Branch', '');
            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee = Employee::find($id);
            $employeesId = \Auth::user()->employeeIdFormat(!empty($employee->employee_id) ? $employee->employee_id : '');
            $departmentData = Department::where('created_by', \Auth::user()->creatorId())->where('branch_id', $employee->branch_id)->get()->pluck('name', 'id');

            return view('employee.edit', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents', 'departmentData'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('edit employee')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'dob' => 'required',
                    'gender' => 'required',
                    'phone' => 'required|numeric',
                    'address' => 'required',
//                                   'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $employee = Employee::findOrFail($id);

            if ($request->document) {
                foreach ($request->document as $key => $document) {
                    if (!empty($document)) {
                        $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('document')[$key]->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                        $dir = 'uploads/document/';
                        $image_path = $dir . $filenameWithExt;

                        if (File::exists($image_path)) {
                            File::delete($image_path);
                        }
                        $path = \Utility::upload_coustom_file($request, 'document', $fileNameToStore, $dir, $key, []);

                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->back()->with('error', __($path['msg']));
                        }

                        $employee_document = EmployeeDocument::where('employee_id', $employee->employee_id)->where('document_id', $key)->first();
                        if (!empty($employee_document)) {

                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->save();
                        } else {

                            $employee_document = new EmployeeDocument();
                            $employee_document->employee_id = $employee->employee_id;
                            $employee_document->document_id = $key;

                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->created_by = \Auth::user()->creatorId();

                            $employee_document->save();
                        }

                    }
                }
            }

            $employee = Employee::findOrFail($id);
            $input = $request->all();
            $employee->fill($input)->save();
            $employee = Employee::find($id);
            $user = User::where('id', $employee->user_id)->first();
            if (!empty($user)) {
                $user->name = $employee->name;
                $user->email = $employee->email;
                $user->save();
            }

            if ($request->salary) {
                return redirect()->route('setsalary.index')->with('success', 'Employee successfully updated.');
            }

            if (\Auth::user()->type != 'employee') {
                return redirect()->route('employee.index')->with('success', 'Employee successfully updated.');
            } else {
                return redirect()->route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id))->with('success', 'Employee successfully updated.');
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {

        if (Auth::user()->can('delete employee')) {
            $employee = Employee::findOrFail($id);
            $user = User::where('id', '=', $employee->user_id)->first();
            $emp_documents = EmployeeDocument::where('employee_id', $employee->employee_id)->get();
            $pay_slips     = PaySlip::where('employee_id', $employee->id)->get();
            $employee->delete();
            $user->delete();
            $dir = storage_path('uploads/document/');

            foreach ($emp_documents as $emp_document) {
                $emp_document->delete();
                if (!empty($emp_document->document_value)) {
                    unlink($dir . $emp_document->document_value);
                }
            }

            foreach ($pay_slips as $pay_slip) {
                if (!empty($pay_slip)) {
                    $pay_slip->delete();
                }
            }

            return redirect()->route('employee.index')->with('success', 'Employee successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function show($id)
    {
        if (\Auth::user()->can('view employee')) {
            try {
                $empId = Crypt::decrypt($id);
                $employee     = Employee::findOrFail($empId);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Employee Not Found.'));
            }
            $documents = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branches->prepend('Select Branch', '');
            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');


            $employeesId = \Auth::user()->employeeIdFormat(!empty($employee->employee_id) ? $employee->employee_id : '');

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function json(Request $request)
    {
        $designations = Designation::where('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();

        return response()->json($designations);
    }

    public function employeeNumber()
    {
        $latest = Employee::where('created_by', '=', \Auth::user()->creatorId())->latest('employee_id')->first();
        if (!$latest) {
            return 1;
        }

        return $latest->employee_id + 1;
    }

    public function profile(Request $request)
    {
        if (\Auth::user()->can('manage employee profile')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId());
            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
            }
            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
            }
            if (!empty($request->designation)) {
                $employees->where('designation_id', $request->designation);
            }
            $employees = $employees->get();

            $brances = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $brances->prepend('All', '');

            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments->prepend('All', '');

            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations->prepend('All', '');

            return view('employee.profile', compact('employees', 'departments', 'designations', 'brances'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profileShow($id)
    {
        if (\Auth::user()->can('show employee profile')) {
            $empId = Crypt::decrypt($id);
            $documents = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee = Employee::find($empId);
            $employeesId = \Auth::user()->employeeIdFormat($employee->employee_id);

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function lastLogin()
    {
        $users = User::where('created_by', \Auth::user()->creatorId())->get();

        return view('employee.lastLogin', compact('users'));
    }

    public function employeeJson(Request $request)
    {
        $employees = Employee::where('branch_id', $request->branch)->get()->pluck('name', 'id')->toArray();

        return response()->json($employees);
    }

    public function getdepartment(Request $request)
    {
        $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();

        return response()->json($departments);
    }

    public function getEmployee(Request $request)
    {
        $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();

        return response()->json($employees);
    }

    public function joiningletterPdf($id)
    {
        $users = \Auth::user();

        $currantLang = $users->currentLanguage();
        $joiningletter = JoiningLetter::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);
        $obj = [
            'date' => \Auth::user()->dateFormat($date),
            'app_name' => $settings['company_name'] ?? env('APP_NAME'),
            'employee_name' => $employees->name,
            'address' => !empty($employees->address) ? $employees->address : '',
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'start_date' => !empty($employees->company_doj) ? $employees->company_doj : '',
            'branch' => !empty($employees->Branch->name) ? $employees->Branch->name : '',
            'start_time' => !empty($settings['company_start_time']) ? $settings['company_start_time'] : '',
            'end_time' => !empty($settings['company_end_time']) ? $settings['company_end_time'] : '',
            'total_hours' => $result,
        ];

        $joiningletter->content = JoiningLetter::replaceVariable($joiningletter->content, $obj);
        return view('employee.template.joiningletterpdf', compact('joiningletter', 'employees'));

    }
    public function joiningletterDoc($id)
    {
        $users = \Auth::user();

        $currantLang = $users->currentLanguage();
        $joiningletter = JoiningLetter::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);

        $obj = [
            'date' => \Auth::user()->dateFormat($date),

            'app_name' => env('APP_NAME'),
            'employee_name' => $employees->name,
            'address' => !empty($employees->address) ? $employees->address : '',
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'start_date' => !empty($employees->company_doj) ? $employees->company_doj : '',
            'branch' => !empty($employees->Branch->name) ? $employees->Branch->name : '',
            'start_time' => !empty($settings['company_start_time']) ? $settings['company_start_time'] : '',
            'end_time' => !empty($settings['company_end_time']) ? $settings['company_end_time'] : '',
            'total_hours' => $result,
            //

        ];
        $joiningletter->content = JoiningLetter::replaceVariable($joiningletter->content, $obj);
        return view('employee.template.joiningletterdocx', compact('joiningletter', 'employees'));

    }
    public function ExpCertificatePdf($id)
    {
        $currantLang = \Cookie::get('LANGUAGE');
        if (!isset($currantLang)) {
            $currantLang = 'en';
        }
        $termination = Termination::where('employee_id', $id)->first();
        $experience_certificate = ExperienceCertificate::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);
        $date1 = date_create($employees->company_doj);
        $date2 = date_create($employees->termination_date);
        $diff = date_diff($date1, $date2);
        $duration = $diff->format("%a days");

        if (!empty($termination->termination_date)) {

            $obj = [
                'date' => \Auth::user()->dateFormat($date),
                'app_name' => env('APP_NAME'),
                'employee_name' => $employees->name,
                'payroll' => !empty($employees->salaryType->name) ? $employees->salaryType->name : '',
                'duration' => $duration,
                'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',

            ];
        } else {
            return redirect()->back()->with('error', __('Termination date is required.'));
        }

        $experience_certificate->content = ExperienceCertificate::replaceVariable($experience_certificate->content, $obj);
        return view('employee.template.ExpCertificatepdf', compact('experience_certificate', 'employees'));

    }
    public function ExpCertificateDoc($id)
    {
        $currantLang = \Cookie::get('LANGUAGE');
        if (!isset($currantLang)) {
            $currantLang = 'en';
        }
        $termination = Termination::where('employee_id', $id)->first();
        $experience_certificate = ExperienceCertificate::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);
        $date1 = date_create($employees->company_doj);
        $date2 = date_create($employees->termination_date);
        $diff = date_diff($date1, $date2);
        $duration = $diff->format("%a days");
        if (!empty($termination->termination_date)) {
            $obj = [
                'date' => \Auth::user()->dateFormat($date),
                'app_name' => env('APP_NAME'),
                'employee_name' => $employees->name,
                'payroll' => !empty($employees->salaryType->name) ? $employees->salaryType->name : '',
                'duration' => $duration,
                'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',

            ];
        } else {
            return redirect()->back()->with('error', __('Termination date is required.'));
        }

        $experience_certificate->content = ExperienceCertificate::replaceVariable($experience_certificate->content, $obj);
        return view('employee.template.ExpCertificatedocx', compact('experience_certificate', 'employees'));

    }
    public function NocPdf($id)
    {
        $users = \Auth::user();

        $currantLang = $users->currentLanguage();
        $noc_certificate = NOC::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);

        $obj = [
            'date' => \Auth::user()->dateFormat($date),
            'employee_name' => $employees->name,
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'app_name' => env('APP_NAME'),
        ];

        $noc_certificate->content = NOC::replaceVariable($noc_certificate->content, $obj);
        return view('employee.template.Nocpdf', compact('noc_certificate', 'employees'));

    }
    public function NocDoc($id)
    {
        $users = \Auth::user();

        $currantLang = $users->currentLanguage();
        $noc_certificate = NOC::where(['lang' => $currantLang, 'created_by' => \Auth::user()->creatorId()])->first();
        $date = date('Y-m-d');
        $employees = Employee::find($id);
        $settings = Utility::settings();
        $secs = strtotime($settings['company_start_time']) - strtotime("00:00");
        $result = date("H:i", strtotime($settings['company_end_time']) - $secs);

        $obj = [
            'date' => \Auth::user()->dateFormat($date),
            'employee_name' => $employees->name,
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'app_name' => env('APP_NAME'),
        ];

        $noc_certificate->content = NOC::replaceVariable($noc_certificate->content, $obj);
        return view('employee.template.Nocdocx', compact('noc_certificate', 'employees'));

    }

    //Export
    public function export()
    {
        $name = 'employee_' . date('Y-m-d i:h:s');

        // buffer active then clean it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = Excel::download(new EmployeeExport(), $name . '.xlsx');

        return $data;
    }

    //import
    public function importFile()
    {
        return view('employee.import');
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
                                <option value="dob">DOB</option>
                                <option value="gender">Gender</option>
                                <option value="phone">Phone</option>
                                <option value="address">Address</option>
                                <option value="email">Email</option>
                                <option value="password">Password</option>
                                <option value="company_doj">Company Doj</option>
                                <option value="account_holder_name">Account Holder Name</option>
                                <option value="account_number">Account Number</option>
                                <option value="bank_name">Bank Name</option>
                                <option value="bank_identifier_code">Bank Identifier Code</option>
                                <option value="branch_location">Branch Location</option>
                                <option value="tax_payer_id">Tax Payer Id</option>
                                </select>
                            </th>
                            ';
                }
                $html .= '
                            <th>
                                    <select name="set_column_data branch_name" class="form-control set_column_data branch-name" data-column_number="' . $count . '">
                                        <option value="branch">Branch</option>
                                    </select>
                            </th>
                            ';
                $html .= '
                            <th>
                                    <select name="set_column_data department_name" class="form-control set_column_data department-name" data-column_number="' . $count . '">
                                        <option value="department">Department</option>
                                    </select>
                            </th>
                            ';
                $html .= '
                            <th>
                                    <select name="set_column_data designation_name" class="form-control set_column_data designation-name" data-column_number="' . $count . '">
                                        <option value="designation">Designation</option>
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
                                <select name="branch_name" class="form-control branch-name-value" id="branch_name" required>;';
                    $branchs = Branch::where('created_by', \Auth::user()->id)->pluck('name', 'id');
                    foreach ($branchs as $key => $branch) {
                        $html .= ' <option value="' . $key . '">' . $branch . '</option>';
                    }
                    $html .= '  </select>
                            </td>';

                    $html .= '<td>
                                <select name="department_name" class="form-control department-name-value" id="department_name" required>;';
                    $departments = Department::where('created_by', \Auth::user()->id)->pluck('name', 'id');
                    foreach ($departments as $key => $department) {
                        $html .= ' <option value="' . $key . '">' . $department . '</option>';
                    }
                    $html .= '  </select>
                            </td>';

                    $html .= '<td>
                                <select name="designation_name" class="form-control designation-name-value" id="designation_name" required>;';
                    $designations = Designation::where('created_by', \Auth::user()->id)->pluck('name', 'id');
                    foreach ($designations as $key => $designation) {
                        $html .= ' <option value="' . $key . '">' . $designation . '</option>';
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
        return view('employee.import_modal');
    }

    public function employeeImportdata(Request $request)
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

        $user = Auth::user();
        foreach ($file_data as $key => $row) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->Where('email', 'like', $row[$request->email])->get();
            $branch = Branch::find($request->branch[$key]);
            $department = Department::find($request->department[$key]);
            $designation = Designation::find($request->designation[$key]);

            if ($employees->isEmpty()) {

                try {
                    $user = User::create(
                        [
                            'name' => $row[$request->name],
                            'email' => $row[$request->email],
                            'password' => Hash::make($row[$request->password]),
                            'email_verified_at' => date('Y-m-d h:i:s'),
                            'type' => 'Employee',
                            'lang' => 'en',
                            'created_by' => \Auth::user()->creatorId(),
                        ]
                    );
                    $user->assignRole('Employee');

                    Employee::create([
                        'name' => $row[$request->name],
                        'user_id' => $user->id,
                        'dob' => $row[$request->dob],
                        'gender' => $row[$request->gender],
                        'phone' => $row[$request->phone],
                        'address' => $row[$request->address],
                        'email' => $row[$request->email],
                        'password' => Hash::make($row[$request->password]),
                        'employee_id' => $this->employeeNumber(),
                        'company_doj' => $row[$request->company_doj],
                        'account_holder_name' => $row[$request->account_holder_name],
                        'account_number' => $row[$request->account_number],
                        'bank_name' => $row[$request->bank_name],
                        'bank_identifier_code' => $row[$request->bank_identifier_code],
                        'branch_location' => $row[$request->branch_location],
                        'tax_payer_id' => $row[$request->tax_payer_id],
                        'branch_id' => !empty($branch) ? $branch->id : 0,
                        'department_id' => !empty($department) ? $department->id : 0,
                        'designation_id' => !empty($designation) ? $designation->id : 0,
                        'created_by' => \Auth::user()->creatorId(),
                    ]);
                } catch (\Exception $e) {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . (isset($row[$request->name]) ? $row[$request->name] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->dob]) ? $row[$request->dob] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->gender]) ? $row[$request->gender] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->phone]) ? $row[$request->phone] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->address]) ? $row[$request->address] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->email]) ? $row[$request->email] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->password]) ? $row[$request->password] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->company_doj]) ? $row[$request->company_doj] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->account_holder_name]) ? $row[$request->account_holder_name] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->account_number]) ? $row[$request->account_number] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->bank_name]) ? $row[$request->bank_name] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->bank_identifier_code]) ? $row[$request->bank_identifier_code] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request->account_holder_name]) ? $row[$request->account_holder_name] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$branch->id]) ? $row[$branch->id] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$department->id]) ? $row[$department->id] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$designation->id]) ? $row[$designation->id] : '-') . '</td>';

                    $html .= '</tr>';
                }
            } else {
                $flag = 1;
                $html .= '<tr>';

                $html .= '<td>' . (isset($row[$request->name]) ? $row[$request->name] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->dob]) ? $row[$request->dob] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->gender]) ? $row[$request->gender] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->phone]) ? $row[$request->phone] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->address]) ? $row[$request->address] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->email]) ? $row[$request->email] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->password]) ? $row[$request->password] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->company_doj]) ? $row[$request->company_doj] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->account_holder_name]) ? $row[$request->account_holder_name] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->account_number]) ? $row[$request->account_number] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->bank_name]) ? $row[$request->bank_name] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->bank_identifier_code]) ? $row[$request->bank_identifier_code] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request->account_holder_name]) ? $row[$request->account_holder_name] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$branch->id]) ? $row[$branch->id] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$department->id]) ? $row[$department->id] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$designation->id]) ? $row[$designation->id] : '-') . '</td>';

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
