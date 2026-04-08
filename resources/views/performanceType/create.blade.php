
{{ Form::open(array('url' => 'performanceType'  , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">

    <div class="form-group">
        {{ Form::label('name', __('Name'),['class'=>'form-label'])}}<x-required></x-required>
        {{ Form::text('name', '', array('class' => 'form-control','required'=>'required' , 'placeholder' => 'Enter Performance Type Name')) }}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}

