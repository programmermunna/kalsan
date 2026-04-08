{{Form::open(array('url' => 'vender', 'method' => 'post', 'class' => 'needs-validation', 'novalidate'))}}
<div class="modal-body">

    <h5 class="sub-title mb-3">{{__('Basic Information')}}</h5>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('name', __('Company Name'), array('class' => 'form-label')) }}<x-required></x-required>
                {{Form::text('name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Company Name')))}}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                <x-Mobile label="{{__('Contact')}}" name="contact" placeholder="{{__('Enter Contact')}}" required></x-Mobile>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('email', __('Email'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::email('email', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter email')))}}
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
                {{Form::label('tax_number', __('Company Type'), ['class' => 'form-label'])}}
                {{Form::select('tax_number', ['Vendor' => 'Vender', 'Other' => 'Other', 'Airline' => 'Airline'], old('title', $user->title ?? ''), ['class' => 'form-control', 'placeholder' => __('Select Company Type'), 'style' => 'width: 235px;']) }}            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('commission', __('Commission %'), ['class' => 'form-label'])}}
                {{Form::number('commission', null, array('class' => 'form-control', 'placeholder' => __('Enter Commission')))}}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('balance', __('Balance'), ['class' => 'form-label'])}}
                {{Form::number('balance', null, array('class' => 'form-control', 'placeholder' => __('Enter Balance')))}}
            </div>
        </div>

        @if(!$customFields->isEmpty())
                    @include('customFields.formBuilder')
        @endif
    </div>
    {{-- <h5 class="sub-title mb-3">{{__('Billing Address')}}</h5>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_name', __('Name'), array('class' => 'form-label')) }}
                {{Form::text('billing_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Name')))}}

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                <x-Mobile label="{{__('Phone')}}" name="billing_phone" placeholder="{{__('Enter Phone')}}"></x-Mobile>
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
                {{Form::text('billing_state', null, array('class' => 'form-control', 'placeholder' => _('Enter State')))}}
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

    </div>--}}

    @if(App\Models\Utility::getValByName('shipping_display') == 'on')
        <div class="col-md-12 text-end mb-3">
            <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
        </div>
        <h5 class="sub-title mb-3">{{__('Shipping Address')}}</h5>
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
                    {{Form::textarea('shipping_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address')))}}
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
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}
