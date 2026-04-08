    {{Form::model($interviewSchedule,array('route' => array('interview-schedule.update', $interviewSchedule->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">

    <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('candidate',__('Interview To'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('candidate', $candidates,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create candidate here.') }} <a href="{{ route('job-application.index') }}"><b>{{ __('Create candidate') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('employee',__('Interviewer'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('employee', $employees,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create interviewer here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create interviewer') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('date',__('Interview Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control' ,'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('time',__('Interview Time'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::time('time',null,array('class'=>'form-control timepicker' ,'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('comment',__('Comment'),['class'=>'form-label'])}}
            {{Form::textarea('comment',null,array('class'=>'form-control', 'placeholder'=>__('Comment')))}}
        </div>

    </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
    {{Form::close()}}

