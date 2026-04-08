{{Form::open(array('url'=>'overtime','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">

    {{ Form::hidden('employee_id',$employee->id, array()) }}

    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Overtime Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('number_of_days', __('Number of days'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('number_of_days',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Number of days'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('hours', __('Hours'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('hours',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Hours'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('rate', __('Rate'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('rate',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Rate'))) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}

