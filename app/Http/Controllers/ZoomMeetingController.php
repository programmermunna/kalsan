<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use App\Models\Utility;
use App\Models\ZoomMeeting;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Http\Request;

class ZoomMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ZoomMeetingTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL = "https://api.zoom.us/v2/";

    public function index()
    {
        if (\Auth::user()->can('manage zoom meeting')) {
            if (\Auth::user()->isClient()) {
                $meetings = ZoomMeeting::where('client_id', \Auth::user()->id)->with(['projectName'])->get();
            } else {
                $meetings = ZoomMeeting::where('created_by', \Auth::user()->creatorId())->with(['projectName'])->get();
            }
        return view('zoom-meeting.index', compact('meetings'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied'));
        }

        // $this->statusUpdate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create zoom meeting')) {
            $projects = Project::where('created_by', \Auth::user()->creatorId())->get()->pluck('project_name', 'id');
            $projects->prepend('Select Project', '0');
            $users = User::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $settings = Utility::settings();

            return view('zoom-meeting.create', compact('projects', 'users', 'settings'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create zoom meeting')) {
            $settings = Utility::settings();

            if (isset($settings['zoom_account_id']) && $settings['zoom_account_id'] != "" && isset($settings['zoom_client_id']) && $settings['zoom_client_id'] != "" && isset($settings['zoom_client_secret']) && $settings['zoom_client_secret'] != '') {
                $validator = \Validator::make($request->all(), [
                    'title' => 'required',
                    'start_date' => 'required',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('zoom-meeting.index')->with('error', $messages->first());
                }
                $data['title'] = $request->title;
                $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
                $data['duration'] = (int) $request->duration;
                $data['password'] = $request->password;
                $data['host_video'] = 0;
                $data['participant_video'] = 0;

                $meeting_create = $this->createmitting($data);

                \Log::info('Meeting');
                \Log::info((array) $meeting_create);
                if (isset($meeting_create['success']) && $meeting_create['success'] == true) {
                    $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
                    $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
                    $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
                    $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';

                    $client = Project::where('id', $request->project_id)->first();

                    $zoommeeting = new Zoommeeting();
                    $zoommeeting->title = $request->title;
                    $zoommeeting->project_id = !empty($request->project_id) ? $request->project_id : 0;
                    $zoommeeting->user_id = implode(',', $request->user_id);
                    $zoommeeting->start_date = date('y:m:d H:i:s', strtotime($request->start_date));
                    $zoommeeting->duration = $request->duration;
                    $zoommeeting->start_url = $start_url;
                    $zoommeeting->client_id = !empty($request->client_id) ? $request->client_id : 0;
                    $zoommeeting->join_url = $join_url;
                    $zoommeeting->status = $status;
                    $zoommeeting->created_by = \Auth::user()->creatorId();
                    $zoommeeting->save();

                    // For Google Calendar
                    if ($request->get('synchronize_type') == 'google_calender') {
                        $type = 'zoom_meeting';
                        $request1 = new ZoomMeeting();
                        $request1->title = $request->title;
                        $request1->start_date = $request->start_date;
                        $request1->end_date = $request->start_date;
                        Utility::addCalendarData($request1, $type);

                    }

                    return redirect()->route('zoom-meeting.index')->with('success', __('Zoom Meeting successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please set Api key.'));

            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ZoomMeeting $zoomMeeting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ZoomMeeting $zoomMeeting)
    {
            if ($zoomMeeting->created_by == \Auth::user()->creatorId()) {

                return view('zoom-meeting.show', compact('zoomMeeting'));
            } else {
                return redirect()->back()->with('error', 'permission Denied');
            }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ZoomMeeting $zoomMeeting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ZoomMeeting $zoomMeeting
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ZoomMeeting $zoomMeeting
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ZoomMeeting $zoomMeeting)
    {
        if (\Auth::user()->can('delete zoom meeting')) {
            if ($zoomMeeting->created_by == \Auth::user()->creatorId()) {
                $zoomMeeting->delete();

                return redirect()->route('zoom-meeting.index')->with('success', __('Meeting successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function projectwiseuser($id)
    {
        if($id == 0){
            $users   = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')
            ->get()->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                ];
            });
        } else {
            $projects = ProjectUser::select('user_id')->where('project_id', $id)->get();
            $users = [];
            foreach ($projects as $key => $value) {
                $user = User::select('id', 'name')->where('id', $value->user_id)->first();
                if (!empty($user)) {
                    $users1['id'] = !empty($user) ? $user->id : 0;
                    $users1['name'] = !empty($user->name) ? $user->name : '';
                    $users[] = $users1;
                }
            }
        }

        return \Response::json($users);
    }

    public function statusUpdate()
    {
        $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->pluck('meeting_id');
        foreach ($meetings as $meeting) {
            $data = $this->get($meeting);

            if (isset($data['data']) && !empty($data['data'])) {
                $meeting = ZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
            }
        }

    }

    public function calender(Request $request)
    {
        $user = \Auth::user();
        $transdate = date('Y-m-d', time());
        if ($user->type == 'company' || $user->type == 'HR' || $user->type == 'accountant') {
            $zoomMeetings = ZoomMeeting::where('created_by', '=', \Auth::user()->creatorId())->get();
        } else {
            $zoomMeetings = [];
        }
        $calandar = [];

        foreach ($zoomMeetings as $zoomMeeting) {

            $arr['id'] = $zoomMeeting['id'];
            $arr['title'] = $zoomMeeting['title'];
            $arr['start'] = $zoomMeeting['start_date'];
            $arr['end'] = $zoomMeeting['end_date'];
            $arr['className'] = 'event-primary';
            $arr['url'] = route('zoom-meeting.show', $zoomMeeting['id']);
            $calandar[] = $arr;

        }

        return view('zoom-meeting.calender', compact('calandar', 'transdate', 'zoomMeetings'));
    }

    //for Google Calendar
    public function get_zoom_meeting_data(Request $request)
    {

        if ($request->get('calender_type') == 'goggle_calender') {
            $type = 'zoom_meeting';
            $arrayJson = Utility::getCalendarData($type);
        } else {
            $data = ZoomMeeting::where('created_by', \Auth::user()->creatorId())->get();
            $arrayJson = [];
            foreach ($data as $val) {
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->title,
                    "start" => $val->start_date,
                    "className" => 'event-primary',
                    "textColor" => '#51459d',
                    'url' => route('zoom-meeting.show', $val->id),
                ];
            }
        }

        return $arrayJson;
    }

}
