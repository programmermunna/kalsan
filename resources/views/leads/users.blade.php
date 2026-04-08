{{ Form::model($lead, array('route' => array('leads.users.update', $lead->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('users', __('User'),['class'=>'form-label']) }}
            {{ Form::select('users[]', $users,false, array('class' => 'form-control select2','id'=>'choices-multiple3','multiple'=>'', 'placeholder' => __('Select Users'))) }}
            <div class="text-xs mt-1">
                {{ __('Create user here.') }} <a href="{{ route('users.index') }}"><b>{{ __('Create user') }}</b></a>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Add')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

