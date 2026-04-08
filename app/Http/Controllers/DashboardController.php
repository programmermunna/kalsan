<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\AddTransactionLine;
use App\Models\Announcement;
use App\Models\AttendanceEmployee;
use App\Models\BalanceSheet;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\Contract;
use App\Models\Deal;
use App\Models\DealTask;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\LandingPageSection;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\Payees;
use App\Models\Payer;
use App\Models\Payment;
use App\Models\Pos;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Purchase;
use App\Models\Revenue;
use App\Models\Stage;
use App\Models\Tax;
use App\Models\Ticket;
use App\Models\Timesheet;
use App\Models\TimeTracker;
use App\Models\Trainer;
use App\Models\Training;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function account_dashboard_index()
    {

        // Check if it's a Customer login
        $isCustomerLogin = session('is_customer_login', false);

        if($isCustomerLogin) {
            // Customer (Travel Agency) can access dashboard
            $customer = \App\Models\Customer::find(session('customer_id'));
            if($customer && $customer->type == 'Travel Agency') {
                // For Customer, use creator's data
                $creatorId = $customer->created_by;
                $data['latestIncome']  = Revenue::with(['customer'])->where('created_by', '=', $creatorId)->orderBy('id', 'desc')->limit(5)->get();
                $data['latestExpense'] = Payment::with(['vender'])->where('created_by', '=', $creatorId)->orderBy('id', 'desc')->limit(5)->get();

                $incomeCategory = ProductServiceCategory::where('created_by', '=', $creatorId)->where('type', '=', 'income')->get();
                $inColor        = array();
                $inCategory     = array();
                $inAmount       = array();
                for($i = 0; $i < count($incomeCategory); $i++)
                {
                    $inColor[]    = '#' . $incomeCategory[$i]->color;
                    $inCategory[] = $incomeCategory[$i]->name;
                    $inAmount[]   = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                }

                $data['incomeCategoryColor'] = $inColor;
                $data['incomeCategory']      = $inCategory;
                $data['incomeCatAmount']     = $inAmount;

                $expenseCategory = ProductServiceCategory::where('created_by', '=', $creatorId)->where('type', '=', 'expense')->get();
                $exColor         = array();
                $exCategory      = array();
                $exAmount        = array();
                for($i = 0; $i < count($expenseCategory); $i++)
                {
                    $exColor[]    = '#' . $expenseCategory[$i]->color;
                    $exCategory[] = $expenseCategory[$i]->name;
                    $exAmount[]   = $expenseCategory[$i]->expenseCategoryAmount();
                }

                $data['expenseCategoryColor'] = $exColor;
                $data['expenseCategory']      = $exCategory;
                $data['expenseCatAmount']     = $exAmount;

                // For Customer, we'll use a simple chart data
                $data['incExpBarChartData']  = [];
                $data['incExpLineChartData'] = [];

                $data['currentYear']  = date('Y');
                $data['currentMonth'] = date('M');

                $constant['taxes']         = Tax::where('created_by', $creatorId)->count();
                $constant['category']      = ProductServiceCategory::where('created_by', $creatorId)->count();
                $constant['units']         = ProductServiceUnit::where('created_by', $creatorId)->count();
                $constant['bankAccount']   = BankAccount::where('created_by', $creatorId)->count();
                $data['constant']          = $constant;
                $data['bankAccountDetail'] = AddTransactionLine::join('bank_accounts', 'add_transaction_lines.account_id', '=', 'bank_accounts.chart_account_id')
                    ->where('bank_accounts.created_by', '=', $creatorId)
                    ->select('bank_accounts.bank_name', 'bank_accounts.holder_name', DB::raw('sum(add_transaction_lines.debit - add_transaction_lines.credit) as balance'))
                    ->groupBy('add_transaction_lines.account_id', 'bank_accounts.bank_name', 'bank_accounts.holder_name')
                    ->get();
                $data['recentInvoice'] = Invoice::join('customers', 'invoices.customer_id', '=', 'customers.id')
                    ->where('invoices.created_by', '=', $creatorId)
                    ->where('customers.id', '=', $customer->id)
                    ->orderBy('invoices.id', 'desc')
                    ->limit(5)
                    ->select('invoices.*', 'customers.name as customer_name')
                    ->get();
                $data['weeklyInvoice']     = [];
                $data['monthlyInvoice']    = [];
                $data['recentBill'] = [];
                $data['weeklyBill']        = [];
                $data['monthlyBill']       = [];
                $data['goals']             = [];

                return view('dashboard.account-dashboard', $data);
            }
        }

        if(Auth::check())
        {
           if(Auth::user()->type == 'client')
            {
                // Client users can now access the main dashboard
                // return redirect()->route('client.dashboard.view');
            }

            // Client users can now access the main dashboard
            if(Auth::user()->type == 'client' || \Auth::user()->can('show account dashboard'))
            {
                $data['latestIncome']  = Revenue::with(['customer'])->where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['latestExpense'] = Payment::with(['vender'])->where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();


                    $incomeCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get();
                    $inColor        = array();
                    $inCategory     = array();
                    $inAmount       = array();
                    for($i = 0; $i < count($incomeCategory); $i++)
                    {
                        $inColor[]    = '#' . $incomeCategory[$i]->color;
                        $inCategory[] = $incomeCategory[$i]->name;
                        $inAmount[]   = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                    }


                    $data['incomeCategoryColor'] = $inColor;
                    $data['incomeCategory']      = $inCategory;
                    $data['incomeCatAmount']     = $inAmount;


                    $expenseCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'expense')->get();
                    $exColor         = array();
                    $exCategory      = array();
                    $exAmount        = array();
                    for($i = 0; $i < count($expenseCategory); $i++)
                    {
                        $exColor[]    = '#' . $expenseCategory[$i]->color;
                        $exCategory[] = $expenseCategory[$i]->name;
                        $exAmount[]   = $expenseCategory[$i]->expenseCategoryAmount();
                    }

                    $data['expenseCategoryColor'] = $exColor;
                    $data['expenseCategory']      = $exCategory;
                    $data['expenseCatAmount']     = $exAmount;

                    $data['incExpBarChartData']  = \Auth::user()->getincExpBarChartData();
                    $data['incExpLineChartData'] = \Auth::user()->getIncExpLineChartDate();

                    $data['currentYear']  = date('Y');
                    $data['currentMonth'] = date('M');

                    $constant['taxes']         = Tax::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['category']      = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['units']         = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['bankAccount']   = BankAccount::where('created_by', \Auth::user()->creatorId())->count();
                    $data['constant']          = $constant;
                    $data['bankAccountDetail'] = AddTransactionLine::join('bank_accounts', 'add_transaction_lines.account_id', '=', 'bank_accounts.chart_account_id')
                        ->where('bank_accounts.created_by', '=', \Auth::user()->creatorId())
                        ->select('bank_accounts.bank_name', 'bank_accounts.holder_name', DB::raw('sum(add_transaction_lines.debit - add_transaction_lines.credit) as balance'))
                        ->groupBy('add_transaction_lines.account_id', 'bank_accounts.bank_name', 'bank_accounts.holder_name')
                        ->get();
                    $data['recentInvoice'] = Invoice::join('customers', 'invoices.customer_id', '=', 'customers.id')
                        ->where('invoices.created_by', '=', \Auth::user()->creatorId())
                        ->orderBy('invoices.id', 'desc')
                        ->limit(5)
                        ->select('invoices.*', 'customers.name as customer_name')
                        ->get();
                    $data['weeklyInvoice']     = \Auth::user()->weeklyInvoice();
                    $data['monthlyInvoice']    = \Auth::user()->monthlyInvoice();
                    $data['recentBill'] = Bill::join('venders', 'bills.vender_id', '=', 'venders.id')
                    ->where('bills.created_by', '=', \Auth::user()->creatorId())
                    ->orderBy('bills.id', 'desc')
                    ->limit(5)
                    ->select('bills.*', 'venders.name as vender_name')
                    ->get();
                    $data['weeklyBill']        = \Auth::user()->weeklyBill();
                    $data['monthlyBill']       = \Auth::user()->monthlyBill();
                    $data['goals']             = Goal::where('created_by', '=', \Auth::user()->creatorId())->where('is_display', 1)->get();

                    return view('dashboard.account-dashboard', $data);


                }
                else {
                    return $this->sales_dashboard_index();
                }


            }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                $setting = Utility::settings();

                if ($setting['display_landing_page'] == 'on' && \Schema::hasTable('landing_page_settings'))
                {
                    $lang = Utility::getValByName('default_language');
                    \App::setLocale($lang ?? 'en');
                    return view('landingpage::layouts.landingpage', compact('setting'));
                }
                else
                {
                    return redirect('login');
                }

            }
        }
    }


    public function project_dashboard_index()
    {
        $user = Auth::user();
        if(\Auth::user()->can('show project dashboard'))
        {
            if($user->type == 'admin')
            {
                return view('admin.dashboard');
            }
            else
            {
                $home_data = [];

                $user_projects   = $user->projects()->pluck('project_id')->toArray();
                $project_tasks   = ProjectTask::whereIn('project_id', $user_projects)->get();
                $project_expense = Expense::whereIn('project_id', $user_projects)->get();
                $seven_days      = Utility::getLastSevenDays();

                // Total Projects
                $complete_project           = $user->projects()->where('status', 'LIKE', 'complete')->count();
                $home_data['total_project'] = [
                    'total' => count($user_projects),
                    'percentage' => Utility::getPercentage($complete_project, count($user_projects)),
                ];

                // Total Tasks
                if(Auth::user()->type == 'company')
                {
                    $complete_task = ProjectTask::where('is_complete', '=', 1)->whereIn('project_id', $user_projects)->count();
                }
                else{
                    $complete_task = ProjectTask::where('is_complete', '=', 1)->whereRaw("find_in_set('" . $user->id . "',assign_to)")->whereIn('project_id', $user_projects)->count();
                }
                $home_data['total_task'] = [
                    'total' => $project_tasks->count(),
                    'percentage' => Utility::getPercentage($complete_task, $project_tasks->count()),
                ];

                // Total Expense
                $total_expense        = 0;
                $total_project_amount = 0;
                foreach($user->projects as $pr)
                {
                    $total_project_amount += $pr->budget;
                }
                foreach($project_expense as $expense)
                {
                    $total_expense += $expense->amount;
                }
                $home_data['total_expense'] = [
                    'total' => $project_expense->count(),
                    'percentage' => Utility::getPercentage($total_expense, $total_project_amount),
                ];

                // Total Users
                $home_data['total_user'] = Auth::user()->contacts->count();

                // Tasks Overview Chart & Timesheet Log Chart
                $task_overview    = [];
                $timesheet_logged = [];
                foreach($seven_days as $date => $day)
                {
                    // Task
                    $task_overview[$day] = ProjectTask::where('is_complete', '=', 1)->where('marked_at', 'LIKE', $date)->whereIn('project_id', $user_projects)->count();

                    // Timesheet
                    $time                   = Timesheet::whereIn('project_id', $user_projects)->where('date', 'LIKE', $date)->pluck('time')->toArray();
                    $timesheet_logged[$day] = str_replace(':', '.', Utility::calculateTimesheetHours($time));
                }

                $home_data['task_overview']    = $task_overview;
                $home_data['timesheet_logged'] = $timesheet_logged;

                // Project Status
                $total_project  = count($user_projects);
                $project_status = [];
                foreach(Project::$project_status as $k => $v)
                {
                    $project_status[$k]['total']      = $user->projects->where('status', 'LIKE', $k)->count();
                    $project_status[$k]['percentage'] = Utility::getPercentage($project_status[$k]['total'], $total_project);
                }
                $home_data['project_status'] = $project_status;

                // Top Due Project
                $home_data['due_project'] = $user->projects()->orderBy('end_date', 'DESC')->limit(5)->get();

                // Top Due Tasks
                $home_data['due_tasks'] = ProjectTask::where('is_complete', '=', 0)->whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->limit(5)->with(['project'])->get();

                $home_data['last_tasks'] = ProjectTask::whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->limit(5)->get();

                return view('dashboard.project-dashboard', compact('home_data'));
            }
        }
        else
        {
            return $this->hrm_dashboard_index();

        }
    }

    public function hrm_dashboard_index()
    {
        if(Auth::check())
        {
            if(\Auth::user()->can('show hrm dashboard'))
            {
                $user = Auth::user();
                if($user->type != 'client' && $user->type != 'company')
                {
                    $emp = Employee::where('user_id', '=', $user->id)->first();

                    $announcements = Announcement::where('announcements.end_date', '>=', date('Y-m-d'))->orderBy('announcements.id', 'desc')->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')->where('announcement_employees.employee_id', '=', $emp->id)->orWhere(function ($q) use ($emp) {
                        $q->where('announcements.department_id', '["0"]')
                        ->where('announcements.employee_id', '["0"]')
                        ->where('announcement_employees.employee_id', $emp->id);
                    })->get();

                    $employees = Employee::get();
                    $meetings  = Meeting::orderBy('meetings.id', 'desc')->take(5)->leftjoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')->where('meeting_employees.employee_id', '=', $emp->id)->orWhere(
                        function ($q){
                            $q->where('meetings.department_id', '["0"]')->where('meetings.employee_id', '["0"]');
                        }
                    )->groupBy('meetings.id')->get();
                    $events    = Event::leftjoin('event_employees', 'events.id', '=', 'event_employees.event_id')->where('event_employees.employee_id', '=', $emp->id)->orWhere(
                        function ($q){
                            $q->where('events.department_id', '["0"]')->where('events.employee_id', '["0"]');
                        }
                    )->get();

                    $arrEvents = [];
                    foreach($events as $event)
                    {

                        $arr['id']              = $event['id'];
                        $arr['title']           = $event['title'];
                        $arr['start']           = $event['start_date'];
                        $arr['end']             = $event['end_date'];
                        $arr['backgroundColor'] = $event['color'];
                        $arr['borderColor']     = "#fff";
                        $arr['textColor']       = "white";
                        $arrEvents[]            = $arr;
                    }

                    $date               = date("Y-m-d");
                    $time               = date("H:i:s");
                    $employeeAttendance = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)->where('date', '=', $date)->first();

                    $officeTime['startTime'] = Utility::getValByName('company_start_time');
                    $officeTime['endTime']   = Utility::getValByName('company_end_time');

                    return view('dashboard.dashboard', compact('arrEvents', 'announcements', 'employees', 'meetings', 'employeeAttendance', 'officeTime'));
                }

                else
                {
                    $events    = Event::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $arrEvents = [];

                    foreach($events as $event)
                    {
                        $arr['id']    = $event['id'];
                        $arr['title'] = $event['title'];
                        $arr['start'] = $event['start_date'];
                        $arr['end']   = $event['end_date'];

                        $arr['backgroundColor'] = $event['color'];
                        $arr['borderColor']     = "#fff";
                        $arr['textColor']       = "white";
                        $arr['url']             = route('event.edit', $event['id']);

                        $arrEvents[] = $arr;
                    }


                    $announcements = Announcement::where('end_date', '>=', date('Y-m-d'))->orderBy('announcements.id', 'desc')->take(5)->where('created_by', '=', \Auth::user()->creatorId())->get();



                    $user      = User::where('type', '!=', 'client')->where('type', '!=', 'company')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $countUser = count($user);

                    $countTrainer    = Trainer::where('created_by', '=', \Auth::user()->creatorId())->count();
                    $onGoingTraining = Training::where('status', '=', 1)->where('created_by', '=', \Auth::user()->creatorId())->count();
                    $doneTraining    = Training::where('status', '=', 2)->where('created_by', '=', \Auth::user()->creatorId())->count();

                    $currentDate = date('Y-m-d');

                    $employees   = User::where('type', '=', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $countClient = count($employees);
                    $notClockIn  = AttendanceEmployee::where('date', '=', $currentDate)->get()->pluck('employee_id');

                    $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())->whereNotIn('id', $notClockIn)->get();
                    $activeJob   = Job::where('status', 'active')->where('created_by', '=', \Auth::user()->creatorId())->count();
                    $inActiveJOb = Job::where('status', 'in_active')->where('created_by', '=', \Auth::user()->creatorId())->count();


                    $meetings = Meeting::where('created_by', '=', \Auth::user()->creatorId())->limit(5)->get();

                    return view('dashboard.dashboard', compact('arrEvents', 'onGoingTraining', 'activeJob', 'inActiveJOb', 'doneTraining', 'announcements', 'employees', 'meetings', 'countTrainer', 'countClient', 'countUser', 'notClockIns'));
                }
            }
            else
            {
                return $this->crm_dashboard_index();
            }
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                $settings = Utility::settings();
                if($settings['display_landing_page'] == 'on')
                {


                    return view('layouts.landing');
                }
                else
                {
                    return redirect('login');
                }

            }
        }
    }

    public function crm_dashboard_index()
    {
        $user = Auth::user();
        if(\Auth::user()->can('show crm dashboard'))
        {
            if($user->type == 'admin')
            {
                return view('admin.dashboard');
            }
            else
            {
                $crm_data = [];

                $leads = Lead::where('created_by', \Auth::user()->creatorId())->get();
                $deals = Deal::where('created_by', \Auth::user()->creatorId())->get();

                //count data
                $crm_data['total_leads']= $total_leads     = count($leads);
                $crm_data['total_deals']= $total_deals     = count($deals);
                $crm_data['total_contracts']       = Contract::where('created_by', \Auth::user()->creatorId())->count();

                //lead status
                $total_leads  = count($leads);
                $lead_status = [];
                $status = LeadStage::select('lead_stages.*', 'pipelines.name as pipeline')
                    ->join('pipelines', 'pipelines.id', '=', 'lead_stages.pipeline_id')
                    ->where('pipelines.created_by', '=', \Auth::user()->creatorId())
                    ->where('lead_stages.created_by', '=', \Auth::user()->creatorId())
                    ->orderBy('lead_stages.pipeline_id')->get();

                foreach($status as $k=>$v)
                {
                    $lead_status[$k]['lead_stage'] = $v->name;
                    $lead_status[$k]['lead_total']      = count($v->lead());
                    $lead_status[$k]['lead_percentage'] = Utility::getCrmPercentage($lead_status[$k]['lead_total'], $total_leads);

                }

                $crm_data['lead_status'] = $lead_status;

                //deal status
                $total_deals  = count($deals);
                $deal_status = [];
                $dealstatuss = Stage::select('stages.*', 'pipelines.name as pipeline')
                    ->join('pipelines', 'pipelines.id', '=', 'stages.pipeline_id')
                    ->where('pipelines.created_by', '=', \Auth::user()->creatorId())
                    ->where('stages.created_by', '=', \Auth::user()->creatorId())
                    ->orderBy('stages.pipeline_id')->get();
                foreach($dealstatuss as $k => $v)
                {
                    $deal_status[$k]['deal_stage'] = $v->name;
                    $deal_status[$k]['deal_total']      = count($v->deals());
                    $deal_status[$k]['deal_percentage'] = Utility::getCrmPercentage($deal_status[$k]['deal_total'], $total_deals);
                }
                $crm_data['deal_status'] = $deal_status;

                $crm_data['latestContract']  = Contract::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->with(['clients','projects','types'])->get();


                return view('dashboard.crm-dashboard', compact('crm_data'));
            }
        }
        else
        {
            return $this->pos_dashboard_index();
        }
    }

    public function pos_dashboard_index()
    {
        $user = Auth::user();
        if(\Auth::user()->can('show pos dashboard'))
        {
            if($user->type == 'admin')
            {
                return view('admin.dashboard');
            }
            else
            {
                $pos_data=[];
                $pos_data['monthlyPosAmount'] = Pos::totalPosAmount(true);
                $pos_data['totalPosAmount'] = Pos::totalPosAmount();
                $pos_data['monthlyPurchaseAmount'] = Purchase::totalPurchaseAmount(true);
                $pos_data['totalPurchaseAmount'] = Purchase::totalPurchaseAmount();

                $purchasesArray = Purchase::getPurchaseReportChart();
                $posesArray = Pos::getPosReportChart();

                return view('dashboard.pos-dashboard',compact('pos_data','purchasesArray','posesArray'));
            }
        }
        else
        {
            return $this->account_dashboard_index();
        }
    }



    // Load Dashboard user's using ajax
    public function filterView(Request $request)
    {
        $usr   = Auth::user();
        $users = User::where('id', '!=', $usr->id);

        if($request->ajax())
        {
            if(!empty($request->keyword))
            {
                $users->where('name', 'LIKE', $request->keyword . '%')->orWhereRaw('FIND_IN_SET("' . $request->keyword . '",skills)');
            }

            $users      = $users->get();
            $returnHTML = view('dashboard.view', compact('users'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }

    public function clientView()
    {

        if(Auth::check())
        {

            if(Auth::user()->type == 'client')
            {
                $transdate = date('Y-m-d', time());
                $currentYear  = date('Y');

                $calenderTasks = [];
                $chartData     = [];
                $arrCount      = [];
                $arrErr        = [];
                $m             = date("m");
                $de            = date("d");
                $y             = date("Y");
                $format        = 'Y-m-d';
                $user          = \Auth::user();
                if(\Auth::user()->can('View Task'))
                {
                    $company_setting = Utility::settings();
                }
                $arrTemp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arrTemp['date'][]    = __(date('D', strtotime($date)));
                    $arrTemp['invoice'][] = 10;
                    $arrTemp['payment'][] = 20;
                }

                $chartData = $arrTemp;

                foreach($user->clientDeals as $deal)
                {
                    foreach($deal->tasks as $task)
                    {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route(
                                'deals.tasks.show', [
                                                      $deal->id,
                                                      $task->id,
                                                  ]
                            ),
                            'className' => ($task->status) ? 'bg-success border-success' : 'bg-warning border-warning',
                        ];
                    }

                    $calenderTasks[] = [
                        'title' => $deal->name,
                        'start' => $deal->created_at->format('Y-m-d'),
                        'url' => route('deals.show', [$deal->id]),
                        'className' => 'deal bg-primary border-primary',
                    ];
                }
                $client_deal = $user->clientDeals->pluck('id');

                $arrCount['deal'] = !empty($user->clientDeals)? $user->clientDeals->count() : 0;
                if(!empty($client_deal->first()))
                {
                    $arrCount['task'] = DealTask::whereIn('deal_id', [$client_deal->first()])->count();
                }
                else
                {
                    $arrCount['task'] = 0;
                }


                $project['projects']             = Project::where('client_id', '=', Auth::user()->id)->where('created_by', \Auth::user()->creatorId())->where('end_date', '>', date('Y-m-d'))->limit(5)->orderBy('end_date')->get();
                $project['projects_count']       = count($project['projects']);
                $user_projects                   = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $tasks                           = ProjectTask::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->get();
                $project['projects_tasks_count'] = count($tasks);
                $project['project_budget']       = Project::where('client_id', Auth::user()->id)->sum('budget');

                $project_last_stages      = Auth::user()->last_projectstage();
                $project_last_stage       = (!empty($project_last_stages) ? $project_last_stages->id : 0);
                $project['total_project'] = Auth::user()->user_project();
                $total_project_task       = Auth::user()->created_total_project_task();
                $allProject               = Project::where('client_id', \Auth::user()->id)->where('created_by', \Auth::user()->creatorId())->get();
                $allProjectCount          = count($allProject);

                $bugs                               = Bug::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->get();
                $project['projects_bugs_count']     = count($bugs);
                $bug_last_stage                     = BugStatus::orderBy('order', 'DESC')->first();
                $completed_bugs                     = Bug::whereIn('project_id', $user_projects)->where('status', $bug_last_stage->id)->where('created_by', \Auth::user()->creatorId())->get();
                $allBugCount                        = count($bugs);
                $completedBugCount                  = count($completed_bugs);
                $project['project_bug_percentage']  = ($allBugCount != 0) ? intval(($completedBugCount / $allBugCount) * 100) : 0;
                $complete_task                      = Auth::user()->project_complete_task($project_last_stage);
                $completed_project                  = Project::where('client_id', \Auth::user()->id)->where('status', 'complete')->where('created_by', \Auth::user()->creatorId())->get();
                $completed_project_count            = count($completed_project);
                $project['project_percentage']      = ($allProjectCount != 0) ? intval(($completed_project_count / $allProjectCount) * 100) : 0;
                $project['project_task_percentage'] = ($total_project_task != 0) ? intval(($complete_task / $total_project_task) * 100) : 0;
                $invoice                            = [];
                $top_due_invoice                    = [];
                $invoice['total_invoice']           = 5;
                $complete_invoice                   = 0;
                $total_due_amount                   = 0;
                $top_due_invoice                    = array();
                $pay_amount                         = 0;

                if(Auth::user()->type == 'client')
                {
                    if(!empty($project['project_budget']))
                    {
                        $project['client_project_budget_due_per'] = intval(($pay_amount / $project['project_budget']) * 100);
                    }
                    else
                    {
                        $project['client_project_budget_due_per'] = 0;
                    }

                }

                $top_tasks       = Auth::user()->created_top_due_task();
                $users['staff']  = User::where('created_by', '=', Auth::user()->creatorId())->count();
                $users['user']   = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '!=', 'client')->count();
                $users['client'] = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'client')->count();
                $project_status  = array_values(Project::$project_status);
                $projectData     = \App\Models\Project::getProjectStatus();

                $taskData        = \App\Models\TaskStage::getChartData();

                return view('dashboard.clientView', compact('calenderTasks', 'arrErr', 'arrCount', 'chartData', 'project', 'invoice', 'top_tasks', 'top_due_invoice', 'users', 'project_status', 'projectData', 'taskData','transdate','currentYear'));
            }
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 14; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    public function stopTracker(Request $request)
    {
        if(Auth::user()->isClient())
        {
            return Utility::error_res(__('Permission denied.'));
        }
        $validatorArray = [
            'name' => 'required|max:120',
            'project_id' => 'required|integer',
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        );
        if($validator->fails())
        {
            return Utility::error_res($validator->errors()->first());
        }
        $tracker = TimeTracker::where('created_by', '=', Auth::user()->id)->where('is_active', '=', 1)->first();
        if($tracker)
        {
            $tracker->end_time   = $request->has('end_time') ? $request->input('end_time') : date("Y-m-d H:i:s");
            $tracker->is_active  = 0;
            $tracker->total_time = Utility::diffance_to_time($tracker->start_time, $tracker->end_time);
            $tracker->save();

            return Utility::success_res(__('Add Time successfully.'));
        }

        return Utility::error_res('Tracker not found.');
    }

}
