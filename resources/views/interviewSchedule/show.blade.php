<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{__('Schedule Detail')}}</h5>
            <div class="row mb-0 align-items-center">
                <p class="col-sm-5 h6 text-sm">{{__('Job')}}</p>
                <p class="col-sm-7 text-sm">{{!empty($interviewSchedule->applications) ? !empty($interviewSchedule->applications->jobs) ? $interviewSchedule->applications->jobs->title : '-' : '-'}}</p>
                <p class="col-sm-5 h6 text-sm">{{__('Interview On')}}</p>
                <p class="col-sm-7 text-sm"> {{  \Auth::user()->dateFormat($interviewSchedule->date).' '. \Auth::user()->timeFormat($interviewSchedule->time) }}</p>
                <p class="col-sm-5 h6 text-sm">{{__('Assign Employee')}}</p>
                <p class="col-sm-7 text-sm">{{!empty($interviewSchedule->users)?$interviewSchedule->users->name:'-'}}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{__('Candidate Detail')}}</h5>
            <div class="row mb-0 align-items-center">
                <p class="col-sm-5 h6 text-sm">{{__('Name')}}</p>
                <p class="col-sm-7 text-sm">{{($interviewSchedule->applications)?$interviewSchedule->applications->name:'-'}}</p>
                <p class="col-sm-5 h6 text-sm">{{__('Email')}}</p>
                <p class="col-sm-7 text-sm"> {{($interviewSchedule->applications)?$interviewSchedule->applications->email:'-'}}</p>
                <p class="col-sm-5 h6 text-sm">{{__('Phone')}}</p>
                <p class="col-sm-7 text-sm">{{($interviewSchedule->applications)?$interviewSchedule->applications->phone:'-'}}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{__('Candidate Status')}}</h5>
            @foreach($stages as $stage)
                <div class="form-check-control custom-radio">
                    <input type="radio" id="stage_{{$stage->id}}" name="stage" data-scheduleid="{{$interviewSchedule->candidate}}" value="{{$stage->id}}" class="form-check-input stages" {{!empty($interviewSchedule->applications)?!empty($interviewSchedule->applications->stage==$stage->id)?'checked':'':''}}>
                    <label class="form-check-label" for="stage_{{$stage->id}}">{{$stage->title}}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-footer">
        <a href="#" data-url="{{route('job.on.board.create', $interviewSchedule->candidate)}}"  data-ajax-popup="true"  class="btn btn-primary" >  {{__('Add to Job OnBoard')}}</a>
    </div>

</div>
