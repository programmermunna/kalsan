<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Mail\LeaveActionSend;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaveController extends Controller
{
    public function index()
    {

        if(\Auth::user()->can('manage leave'))
        {
            if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
            {
                $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())->with(['leaveType','employees'])->get();

            }
            else
            {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $leaves   = Leave::where('employee_id', '=', $employee->id)->with(['leaveType','employees'])->get();
            }
            return view('leave.index', compact('leaves'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create leave'))
        {
            $employee_id = null;
            if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }
            else
            {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                $employee = \Auth::user()->employee;
                $employee_id = isset($employee) ? $employee->id : null;
            }
            $leavetypes      = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leave.create', compact('employees', 'leavetypes', 'employee_id'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create leave'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'leave_type_id' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'leave_reason' => 'required',
                    'remark' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }


            $employee = Employee::where('user_id', '=', Auth::user()->id)->first();
            $leave_type = LeaveType::find($request->leave_type_id);
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;
            if ($leave_type->days >= $total_leave_days)
            {
                $leave    = new Leave();
                if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
                {
                    $leave->employee_id = $request->employee_id;
                }
                else
                {
                    $leave->employee_id = $employee->id;
                }
                $leave->leave_type_id    = $request->leave_type_id;
                $leave->applied_on       = date('Y-m-d');
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = $request->remark;
                $leave->status           = 'Pending';
                $leave->created_by       = \Auth::user()->creatorId();

                $leave->save();

                if(\Auth::user()->type !='company' || \Auth::user()->type !='HR')
                {
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    $employee = Employee::find($leave->employee_id);
                    $user = User::find($leave->created_by);
                    if(isset($setting['new_leave']) && $setting['new_leave'] ==1)
                    {
                            $leaveArr = [
                                'user_name'=>$user->name,
                                'start_date' => $leave->start_date,
                                'end_date' => $leave->end_date,
                                'leave_reason' => $leave->leave_reason,
                                'employee_name' => $employee->name,
                            ];
                            $resp = Utility::sendEmailTemplate('new_leave', [$user->id => $user->email], $leaveArr);
                    }
                }

                return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Leave type ' . $leave_type->name . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Leave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {

                if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
                {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }
                else
                {
                    $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                }

                $employee = $leave->employees;
                $employee_id = isset($employee) ? $employee->id : null;
                $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');

                return view('leave.edit', compact('leave', 'employees', 'leavetypes', 'employee_id'));
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

    public function update(Request $request, $leave)
    {

        $leave = Leave::find($leave);
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'leave_type_id' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'leave_reason' => 'required',
                        'remark' => 'required',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leave_type = LeaveType::find($request->leave_type_id);

                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date);
                $endDate->add(new \DateInterval('P1D'));
                $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;
                if ($leave_type->days >= $total_leave_days)
                {

                    $leave->employee_id      = $request->employee_id;
                    $leave->leave_type_id    = $request->leave_type_id;
                    $leave->start_date       = $request->start_date;
                    $leave->end_date         = $request->end_date;
                    $leave->total_leave_days = $total_leave_days;
                    $leave->leave_reason     = $request->leave_reason;
                    $leave->remark           = $request->remark;

                    $leave->save();

                    return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Leave type ' . $leave_type->name . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
                }
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

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('delete leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
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

    public function action($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {

        $leave = Leave::find($request->leave_id);

        $leave->status = $request->status;
        if($leave->status == 'Approval')
        {
            $startDate               = new \DateTime($leave->start_date);
            $endDate                 = new \DateTime($leave->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days        = $startDate->diff($endDate)->days;
            $leave->total_leave_days = $total_leave_days;
            $leave->status           = 'Approved';
        }

        $leave->save();

        //Send Email
        $setings = Utility::settings();
        if(!empty($employee->id))
        {
            if($setings['leave_action_sent'] == 1)
            {

                $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
                $leave->name  = !empty($employee->name) ? $employee->name : '';
                $leave->email = !empty($employee->email) ? $employee->email : '';

                $actionArr = [

                    'leave_name'=> !empty($employee->name) ? $employee->name : '',
                    'leave_status' => $leave->status,
                    'leave_reason' =>  $leave->leave_reason,
                    'leave_start_date' => $leave->start_date,
                    'leave_end_date' => $leave->end_date,
                    'total_leave_days' => $leave->total_leave_days,

                ];
                $resp = Utility::sendEmailTemplate('leave_action_sent', [$employee->id => $employee->email], $actionArr);


                return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {


        $leave_counts=[];
        $leave_types = LeaveType::where('created_by',\Auth::user()->creatorId())->get();
        foreach ($leave_types as  $type) {
            $year = date('Y');
            $counts = Leave::select(\DB::raw('
                COALESCE(SUM(
                    CASE
                        WHEN YEAR(start_date) = ? AND YEAR(end_date) = ? THEN total_leave_days
                        WHEN YEAR(start_date) = ? THEN DATEDIFF(LAST_DAY(start_date), start_date) + 1
                        WHEN YEAR(end_date) = ? THEN DATEDIFF(end_date, DATE_FORMAT(end_date, "%Y-01-01")) + 1
                        ELSE 0
                    END
                ), 0) AS total_leave
            '))
            ->where('leave_type_id', $type->id)
            ->where('employee_id', $request->employee_id)
            ->where('status', '!=', 'Reject')
            ->where(function ($query) use ($year) {
                $query->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year);
            })
            ->addBinding([$year, $year, $year, $year], 'select')
            ->first();

            $leave_count['total_leave']=!empty($counts)?$counts['total_leave']:0;
            $leave_count['title']=$type->title;
            $leave_count['days']=$type->days;
            $leave_count['id']=$type->id;
            $leave_counts[]=$leave_count;
        }


        return $leave_counts;

    }

}
