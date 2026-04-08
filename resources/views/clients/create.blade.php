{{ Form::open(array('url' => 'clients' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('customer_id', $travelAgencies, null, array('class' => 'form-control select', 'placeholder' => __('Select Customer'), 'required' => 'required', 'id' => 'customer_id')) }}
            {{ Form::hidden('name', null, array('id' => 'name')) }}
        </div>
        <div class="form-group">
            {{ Form::label('email', __('E-Mail Address'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::email('email', null, array('class' => 'form-control','placeholder'=>__('Enter Client Email'),'required'=>'required')) }}
        </div>
        <div class="form-group">
            {{ Form::label('branch_id', __('Branch'),['class'=>'form-label']) }}
            {{ Form::select('branch_id', $branches, null, array('class' => 'form-control select', 'placeholder' => __('Select Branch'))) }}
        </div>
        <div class="col-md-5 mb-3 form-group">
            <label for="password_switch">{{ __('Login is enable') }}</label>
            <div class="form-check form-switch custom-switch-v1 float-end">
                <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
                <label class="form-check-label" for="password_switch"></label>
            </div>
        </div>
        <div class="col-md-12 ps_div d-none">
            <div class="form-group">
                {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Client Password'), 'minlength' => '6']) }}
                @error('password')
                    <small class="invalid-password" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
            </div>
        </div>

        @if(!$customFields->isEmpty())
            @include('custom_fields.formBuilder')
        @endif

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

@push('script-page')
<script>
    $(document).ready(function() {
        $('#customer_id').on('change', function() {
            var selectedText = $(this).find('option:selected').text();
            $('#name').val(selectedText);
        });
    });
</script>
@endpush


