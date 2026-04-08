<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Event as LocalEvent;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use App\Models\EventEmployee;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage event'))
        {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get();
            $events    = LocalEvent::where('created_by', '=', \Auth::user()->creatorId())->get();

            $transdate = date('Y-m-d', time());
            $today_date = date('m');
            $current_month_event = LocalEvent::select('id','start_date','end_date', 'title', 'created_at','color')
                ->whereRaw('MONTH(start_date)=' . $today_date)->whereRaw('MONTH(end_date)=' . $today_date)->get();

            $arrEvents = [];
            foreach($events as $event)
            {
                $arr['id']        = $event['id'];
                $arr['title']     = $event['title'];
                $arr['start']     = $event['start_date'];
                $arr['end']       = $event['end_date'];
                $arr['className'] = $event['color'];$arr['url']       = route('event.edit', $event['id']);
                $arrEvents[]      = $arr;
            }
            $arrEvents = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrEvents)));

            return view('event.index', compact('arrEvents', 'employees', 'transdate','events','current_month_event'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create event'))
        {
            $employees   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch      = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get();
            $settings = Utility::settings();

            return view('event.create', compact('employees', 'branch', 'departments','settings'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create event'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'branch_id' => 'required',
                                   'department_id' => 'required',
                                   'employee_id' => 'required',
                                   'title' => 'required',
                                   'start_date' => 'required',
                                   'end_date' => 'required',
                                   'color' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $event                = new Event();
            $event->branch_id     = $request->branch_id;
            $event->department_id = json_encode($request->department_id);
            $event->employee_id   = json_encode($request->employee_id);
            $event->title         = $request->title;
            $event->start_date    = $request->start_date;
            $event->end_date      = $request->end_date;
            $event->color         = $request->color;
            $event->description   = $request->description;
            $event->created_by    = \Auth::user()->creatorId();
            $event->save();

            if(in_array('0', $request->employee_id))
            {

                $departmentEmployee = Employee::whereIn('department_id', [$request->department_id])->get()->pluck('id');
                $departmentEmployee = $departmentEmployee;
            }
            else
            {
                $departmentEmployee = $request->employee_id;
            }
            foreach($departmentEmployee as $employee)
            {
                $eventEmployee              = new EventEmployee();
                $eventEmployee->event_id    = $event->id;
                $eventEmployee->employee_id = $employee;
                $eventEmployee->created_by  = Auth::user()->creatorId();
                $eventEmployee->save();
            }
            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());

            if($request->branch_id == 0)
            {
                $branch = Branch::all()->pluck('name');

                $result = '';
                $separator = ',';
                foreach ($branch as $value) {
                    if (is_array($value)) {
                        $result .= arrayToString($value, $separator) . $separator;
                    } else {
                        $result .= $value . $separator;
                    }
                }

                $result = rtrim($result, $separator);
            }
            else
            {
                $branch = Branch::find($request->branch_id);
                $result = $branch->name;
            }
            $eventNotificationArr = [
                'event_title' => $request->title,
                'branch_name' => $result,
                'event_start_date' =>$request->start_date,
                'event_end_date' =>$request->end_date,
            ];
            //Slack Notification
            if(isset($setting['event_notification']) && $setting['event_notification'] ==1)
            {
                Utility::send_slack_msg('new_event', $eventNotificationArr);
            }
            //Telegram Notification
            if (isset($setting['telegram_event_notification']) && $setting['telegram_event_notification'] == 1)
            {
                Utility::send_telegram_msg('new_event', $eventNotificationArr);
            }

            //For Google Calendar
            if($request->get('synchronize_type')  == 'google_calender')
            {
                $type ='event';
                Utility::addCalendarData($request , $type);

            }
            //webhook
            $module ='New Event';
            $webhook =  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($event);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            }


            return redirect()->route('event.index')->with('success', __('Event  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Event $event)
    {
        return redirect()->route('event.index');
    }

    public function edit($event)
    {

            $event = LocalEvent::find($event);
            if($event->created_by == Auth::user()->creatorId())
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('event.edit', compact('event', 'employees'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }

    }

    public function update(Request $request, Event $event)
    {

        if(\Auth::user()->can('edit event'))
        {
            if($event->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'title' => 'required',
                                       'start_date' => 'required',
                                       'end_date' => 'required',
                                       'color' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $event->title       = $request->title;
                $event->start_date  = $request->start_date;
                $event->end_date    = $request->end_date;
                $event->color       = $request->color;
                $event->description = $request->description;
                $event->save();

                return redirect()->route('event.index')->with('success', __('Event successfully updated.'));
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

    public function destroy(Event $event)
    {

        return redirect()->back()->with('error', __('This operation is not perform due to demo mode.'));

        if(\Auth::user()->can('delete event'))
        {
            if($event->created_by == \Auth::user()->creatorId())
            {
                $event->delete();

                return redirect()->route('event.index')->with('success', __('Event successfully deleted.'));
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

    public function getdepartment(Request $request)
    {

        if($request->branch_id == 0)
        {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        else
        {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($departments);
    }

    public function getemployee(Request $request)
    {
        $employees=[];
        if(in_array('0',$request->department_id))
        {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();

        }
        else if(!empty($request->department_id))
        {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->whereIn('department_id',$request->department_id)->get()->pluck('name', 'id')->toArray();
        }
        return response()->json($employees);
    }

    public function get_event_data(Request $request)
    {
        $arrayJson = [];
        if($request->get('calender_type') == 'goggle_calender')
        {
            $type ='event';
            $arrayJson =  Utility::getCalendarData($type);
        }
        else
        {
            $data =LocalEvent::where('created_by', '=', \Auth::user()->creatorId())->get();
            foreach($data as $val)
            {
                $end_date=date_create($val->end_date);
                date_add($end_date,date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id"=> $val->id,
                    "title" => $val->title,
                    "start" => $val->start_date,
                    "end" => date_format($end_date,"Y-m-d H:i:s"),
                    "className" => $val->color,
                    'url'      => route('event.edit', $val->id),
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }

}
