<?php

namespace App\Http\Controllers;

use App\Models\TimeTracker;
use App\Models\TrackPhoto;
use App\Models\Utility;
use App\Models\Projects;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TimeTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->type == 'company') {
            $treckers = TimeTracker::where('created_by',\Auth::user()->creatorId())->get();
        } else {
            $treckers = TimeTracker::where('user_id',\Auth::user()->id)->where('created_by',\Auth::user()->creatorId())->get();
        }

       return view('time_trackers.index',compact('treckers'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeTracker  $timeTracker
     * @return \Illuminate\Http\Response
     */
    public function show(TimeTracker $timeTracker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeTracker  $timeTracker
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeTracker $timeTracker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeTracker  $timeTracker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimeTracker $timeTracker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeTracker  $timeTracker
     * @return \Illuminate\Http\Response
     */
    public function destroy($timetracker_id)
    {
        $timetrecker = TimeTracker::find($timetracker_id);
        $timetrecker->delete();

        return redirect()->back()->with('success', __('TimeTracker successfully deleted.'));

    }

    public function getTrackerImages(Request $request){

        $tracker = TimeTracker::where('id', $request->id)->where('created_by',\Auth::user()->creatorId())->first();

        if (!$tracker) {
            return redirect()->back()->with('error', __('Permission denied!'));
        }

        if (\Auth::user()->type == 'company') {
            $images = TrackPhoto::where('track_id',$request->id)->where('created_by',\Auth::user()->creatorId())->get();
        } else {
            $images = TrackPhoto::where('track_id',$request->id)->where('user_id',\Auth::user()->id)->where('created_by',\Auth::user()->creatorId())->get();
        }

        return view('time_trackers.images',compact('images','tracker'));
    }

    public function removeTrackerImages(Request $request){



        $images = TrackPhoto::find($request->id);
        if($images){
            $url= $images->img_path;
            if($images->delete()){
                \Storage::delete($url);
                return Utility::success_res(__('Tracker Photo remove successfully.'));
            }else{
                return Utility::error_res(__('opps something wren wrong.'));
            }
        }else{
            return Utility::error_res(__('opps something wren wrong.'));
        }

    }

    public function removeTracker(Request $request)
    {


        $track = TimeTracker::find($request->input('id'));
        if($track)
        {
            $track->delete();

            return Utility::success_res(__('Track remove successfully.'));
        }
        else
        {
            return Utility::error_res(__('Track not found.'));
        }
    }
}
