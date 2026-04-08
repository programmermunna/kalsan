{{Form::open(array('url'=>'attendanceemployee','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
<div class="card-body p-0">
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('employee_id',__('Employee'))}}
            {{Form::select('employee_id',$employees,null,array('class'=>'form-control select2'))}}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('date',__('Date'))}}
            {{Form::text('date',null,array('class'=>'form-control datepicker'))}}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('clock_in',__('Clock In'))}}
            {{Form::time('clock_in',null,array('class'=>'form-control '))}}

        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('clock_out',__('Clock Out'))}}
            {{Form::time('clock_out',null,array('class'=>'form-control '))}}
        </div>
    </div>
</div>
<div class="modal-footer pr-0">
    <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>
{{Form::close()}}
