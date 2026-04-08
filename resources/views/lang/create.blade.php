{{ Form::open(array('route' => array('store.language'))) }}
<div class="modal-body">

    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('code', __('Language Code'),['class'=>'form-label']) }}
            {{ Form::text('code', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>'Enter Langugae Code')) }}
            @error('code')
            <span class="invalid-code" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('full_name', __('Language Name'),['class'=>'form-label']) }}
            {{ Form::text('full_name', '', array('class' => 'form-control','required'=>'required','placeholder' => 'Enter Lanuguage Name')) }}
            @error('full_name')
            <span class="invalid-code" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>


<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
