{{ Form::open(array('route' => 'screenshots_store', 'method'=>'post', 'enctype' => "multipart/form-data", 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('Heading', __('Heading'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('screenshots_heading',null, ['class' => 'form-control ', 'placeholder' => __('Enter Heading'), 'required' => 'required']) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('screenshots', __('Screenshots'), ['class' => 'form-label']) }}<x-required></x-required>
                    <input type="file" name="screenshots" class="form-control file-validate" required="required">
                    <p id="" class="file-error text-danger"></p>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
    </div>
{{ Form::close() }}

