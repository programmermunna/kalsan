
{{ Form::model($lead, array('route' => array('leads.discussion.store', $lead->id), 'method' => 'POST' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('comment', __('Message'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::textarea('comment', null, array('class' => 'form-control' , 'required' => 'required', 'placeholder' => __('Enter Message'))) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

