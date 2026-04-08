{{Form::model($customer, array('route' => array('customer.update', $customer->id), 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate')) }}
<div class="modal-body">

    <h5 class="sub-title">{{__('Basic Information')}}</h5>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('name', __('Customer Name'), array('class' => 'form-label')) }}<x-required></x-required>
                {{Form::text('name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Full Name')))}}

            </div>
        </div>
        {{-- <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                <x-Mobile label="{{__('Mobile')}}" name="contact" required placeholder="{{__('Enter Contact')}}"></x-Mobile>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('email', __('Email'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::text('email', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter email')))}}

            </div>
        </div>--}}
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('contact', __('Mobile'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::number('contact', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Mobile')))}}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('email', __('Email'), ['class' => 'form-label'])}}
                {{Form::email('email', null, array('class' => 'form-control', 'placeholder' => __('Enter email')))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('billing_address', __('Address'), array('class' => 'form-label')) }}
                {{Form::textarea('billing_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address')))}}

            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('type', __('Customer Type'), ['class' => 'form-label'])}}
                {{Form::select('type', ['Person' => 'Person', 'Travel Agency' => 'Travel Agency', 'Organization' => 'Organization',], old('type', $customer->type ?? ''), ['class' => 'form-control', 'placeholder' => __('Select Customer Type'), 'style' => 'width: 235px;']) }}

            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('balance', __('Balance'), ['class' => 'form-label'])}}
                {{Form::number('balance', null, array('class' => 'form-control', 'placeholder' => __('Enter Balance')))}}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
          <div class="form-group">
            {{Form::label('commission', __('Customer Commission'), ['class' => 'form-label'])}}
            {{Form::number('commission', null, array('class' => 'form-control', 'placeholder' => __('Enter customer commission')))}}
           </div>
        </div>

        @if($customer->type == 'Travel Agency' || old('type') == 'Travel Agency')
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-5 mb-3 form-group">
                    <label for="password_switch">{{ __('Login is enable') }}</label>
                    <div class="form-check form-switch custom-switch-v1 float-end">
                        <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch" {{ $customer->is_enable_login == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="password_switch"></label>
                    </div>
                </div>
                <div class="col-md-12 ps_div {{ $customer->is_enable_login == 1 ? '' : 'd-none' }}">
                    <div class="form-group">
                        {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter New Password (leave blank to keep current)'), 'minlength' => '6']) }}
                        @error('password')
                            <small class="invalid-password" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!$customFields->isEmpty())
                    @include('customFields.formBuilder')
        @endif
    </div>

    {{-- <h5 class="sub-title">{{__('Billing Address')}}</h5>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_name', __('Name'), array('class' => '', 'class' => 'form-label')) }}
                {{Form::text('billing_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Name')))}}

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                <x-Mobile label="{{__('Phone')}}" name="billing_phone" placeholder="{{__('Enter Phone')}}"></x-Mobile>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('billing_address', __('Address'), array('class' => 'form-label')) }}
                {{Form::textarea('billing_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address')))}}

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_city', __('City'), array('class' => 'form-label')) }}
                {{Form::text('billing_city', null, array('class' => 'form-control', 'placeholder' => __('Enter City')))}}

            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_state', __('State'), array('class' => 'form-label')) }}
                {{Form::text('billing_state', null, array('class' => 'form-control', 'placeholder' => __('Enter State')))}}

            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_country', __('Country'), array('class' => 'form-label')) }}
                {{Form::text('billing_country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country')))}}

            </div>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_zip', __('Zip Code'), array('class' => 'form-label')) }}
                {{Form::text('billing_zip', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code')))}}

            </div>
        </div>

    </div> --}}

    @if(App\Models\Utility::getValByName('shipping_display') == 'on')
        <div class="col-md-12 text-end">
            <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
        </div>
        <h5 class="sub-title">{{__('Shipping Address')}}</h5>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_name', __('Name'), array('class' => 'form-label')) }}
                    {{Form::text('shipping_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Name')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <x-Mobile label="{{__('Phone')}}" name="shipping_phone" placeholder="{{__('Enter Phone')}}"></x-Mobile>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('shipping_address', __('Address'), array('class' => 'form-label')) }}
                    <label class="form-label" for="example2cols1Input"></label>
                    <div class="input-group">
                        {{Form::textarea('shipping_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address')))}}
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_city', __('City'), array('class' => 'form-label')) }}
                    {{Form::text('shipping_city', null, array('class' => 'form-control', 'placeholder' => __('Enter City')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_state', __('State'), array('class' => 'form-label')) }}
                    {{Form::text('shipping_state', null, array('class' => 'form-control', 'placeholder' => __('Enter State')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_country', __('Country'), array('class' => 'form-label')) }}
                    {{Form::text('shipping_country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country')))}}

                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_zip', __('Zip Code'), array('class' => 'form-label')) }}
                    {{Form::text('shipping_zip', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code')))}}
                </div>
            </div>

        </div>
    @endif

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}

<script>
$(document).ready(function() {
    // Password switch toggle
    $(document).on('change', '#password_switch', function() {
        if ($(this).is(':checked')) {
            $('.ps_div').removeClass('d-none');
        } else {
            $('.ps_div').addClass('d-none');
            $('#password').val(null);
        }
    });
});
</script>
