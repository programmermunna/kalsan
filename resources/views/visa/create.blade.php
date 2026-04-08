

{{ Form::open(array('url' => 'visa', 'enctype' => "multipart/form-data", 'class' => 'needs-validation', 'novalidate')) }}
 {{-- start for ai module--}}
{{-- @php
$settings = \App\Models\Utility::settings();
    @endphp
     @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate', ['productservice']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif --}}
    {{-- end for ai module--}}
<div class="modal-body">
    <div class="row">

         <div class="form-group col-md-6">
            {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'required']) }}
        </div>

        <div class="col-md-6">
                <div class="form-group">
                     {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}<x-required></x-required>
                            <div class="form-icon-user">
                             {{Form::date('issue_date', null, array('class' => 'form-control', 'required' => 'required'))}}

                            </div>
                    </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Applicant Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', '', array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name'))) }}
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('gender', __('Gender'), ['class' => 'form-label'])}}
                {{Form::select('gender', ['' => __('Select Gender'), 'Male' => 'Male', 'Female' => 'Female',], old('gender'), ['class' => 'form-control', 'placeholder' => __('Select Gender'), 'style' => 'width: 235px;']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('passport_no', __('Passport No'), ['class' => 'form-label']) }}
                {{ Form::text('passport_no', '', array('class' => 'form-control', 'step' => '0.01', 'placeholder' => __('Enter Passport #'))) }}
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('visa_type', __('Visa Type'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::select('visa_type', ['Hajj' => 'Hajj', 'Student' => 'Student', 'Business' => 'Business', 'Medical' => 'Medical', 'Work' => 'Work', 'Tourism' => 'Tourism'], old('visa_type'), ['class' => 'form-control', 'required','placeholder' => __('Select Visa Type'), 'style' => 'width: 235px;']) }}
            </div>
        </div>

         <div class="form-group col-md-6">
            {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('country', $country, null, ['class' => 'form-control', 'required']) }}

        </div>



        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('visa_fee', __('Visa Fee'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('visa_fee', '', array('class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter Visa Fee'))) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('commission', __('Visa Commission'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('commission', '', array('class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter Visa Commission'))) }}
            </div>
        </div>

          <div class="form-group col-md-6">
            {{ Form::label('vender_id', __('Vender'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('vender_id', $venders, null, ['class' => 'form-control', 'required']) }}
        </div>



        <div class="form-group col-md-6">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Remarks')]) }}

        </div>

      <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('visa_status', __('Visa Status'), ['class' => 'form-label'])}}
                {{Form::select('visa_status', ['Applied' => 'Applied', 'Approved' => 'Approved', 'Rejected' => 'Rejected'], old('visa_status'), ['class' => 'form-control', 'required', 'placeholder' => __('Select Visa Status'), 'style' => 'width: 235px;']) }}
            </div>
        </div>


        @if(!$customFields->isEmpty())
                    @include('customFields.formBuilder')
        @endif
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


<script>
    document.getElementById('pro_image').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }

    //hide & show quantity

    $(document).on('click', '.type', function ()
    {
        var type = $(this).val();
        if (type == 'product') {
            $('.quantity').removeClass('d-none')
            $('.quantity').addClass('d-block');
            $('input[name="quantity"]').prop('required', true);
        } else {
            $('.quantity').addClass('d-none')
            $('.quantity').removeClass('d-block');
            $('input[name="quantity"]').val('').prop('required', false);
        }
    });

    function generateSKU(){
        var sku = 'SKU-' + Math.random().toString(24).substr(2, 7);
        $('input[name=sku]').val(sku.toUpperCase());
    }
</script>
