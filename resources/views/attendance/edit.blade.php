
{{Form::model($attendanceEmployee,array('route' => array('attendanceemployee.update', $attendanceEmployee->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">

    <div class="row">
        <div class="form-group col-lg-6  ">
            {{Form::label('employee_id',__('Employee'), ['class' => 'form-label'])}}
            {{Form::select('employee_id',$employees,null,array('class'=>'form-control select'))}}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 ">
            {{Form::label('date',__('Date'), ['class' => 'form-label'])}}
            {{Form::date('date',null,array('class'=>'form-control '))}}
        </div>
    </div>
    <div class="row">

        <div class="form-group col-lg-6 ">
            {{Form::label('clock_in',__('Clock In'), ['class' => 'form-label'])}}
            {{Form::time('clock_in',null,array('class'=>'form-control '))}}
        </div>

        <div class="form-group col-lg-6 ">
            {{Form::label('clock_out',__('Clock Out'), ['class' => 'form-label'])}}
            {{Form::time('clock_out',null,array('class'=>'form-control '))}}
        </div>

    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{ Form::close() }}



