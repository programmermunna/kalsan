{{ Form::model($productService, array('route' => array('productservice.update', $productService->id), 'method' => 'PUT', 'enctype' => "multipart/form-data", 'class' => 'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
$settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate', ['productservice']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name '), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name'))) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('type', __('Type'), ['class' => 'form-label'])}}
                {{Form::select('type', ['Destination' => 'Destination', 'Expense' => 'Expense', 'Cargo' => 'Cargo',], old('type'), ['class' => 'form-control', 'placeholder' => __('Select Type'), 'style' => 'width: 235px;']) }}
            </div>
        </div>



        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Income Account'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="sale_chartaccount_id" class="form-control" required>
                <option value="">{{ __('Select Chart of Account') }}</option>
                @foreach ($incomeChartAccounts as $typeName => $subtypes)
                    <optgroup label="{{ $typeName }}">
                        @foreach ($subtypes as $subtypeId => $subtypeData)
                            <option disabled style="color: #000; font-weight: bold;">{{ $subtypeData['account_name'] }}</option>
                            @foreach ($subtypeData['chart_of_accounts'] as $chartOfAccount)
                                <option value="{{ $chartOfAccount['id'] }}" {{ $productService->sale_chartaccount_id == $chartOfAccount['id'] ? 'selected' : ''}}>
                                    &nbsp;&nbsp;&nbsp;{{ $chartOfAccount['account_name'] }}
                                </option>
                                @foreach ($subtypeData['subAccounts'] as $subAccount)
                                    @if ($chartOfAccount['id'] == $subAccount['parent_account'])
                                    <option value="{{ $subAccount['id'] }}" class="ms-5" {{ $productService->sale_chartaccount_id == $subAccount['id'] ? 'selected' : ''}}> &nbsp; &nbsp;&nbsp;&nbsp; {{' - ' . $subAccount['account_name'] }}</option>
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a href="{{ route('chart-of-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Account'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="expense_chartaccount_id" class="form-control" required>
                <option value="">{{ __('Select Chart of Account') }}</option>
                @foreach ($expenseChartAccounts as $typeName => $subtypes)
                    <optgroup label="{{ $typeName }}">
                        @foreach ($subtypes as $subtypeId => $subtypeData)
                            <option disabled style="color: #000; font-weight: bold;">{{ $subtypeData['account_name'] }}</option>
                            @foreach ($subtypeData['chart_of_accounts'] as $chartOfAccount)
                                <option value="{{ $chartOfAccount['id'] }}" {{ $productService->expense_chartaccount_id == $chartOfAccount['id'] ? 'selected' : ''}}>
                                    &nbsp;&nbsp;&nbsp;{{ $chartOfAccount['account_name'] }}
                                </option>
                                @foreach ($subtypeData['subAccounts'] as $subAccount)
                                    @if ($chartOfAccount['id'] == $subAccount['parent_account'])
                                    <option value="{{ $subAccount['id'] }}" class="ms-5" {{ $productService->expense_chartaccount_id == $subAccount['id'] ? 'selected' : ''}}> &nbsp; &nbsp;&nbsp;&nbsp; {{' - ' . $subAccount['account_name'] }}</option>
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a href="{{ route('chart-of-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>
    {{--  <div class="form-group col-md-6">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit, null, array('class' => 'form-control select', 'required' => 'required')) }}
            <div class=" text-xs mt-1">
                {{__('Create unit here. ')}}<a href="{{route('product-unit.index')}}"><b>{{__('Create unit')}}</b></a>
            </div>
        </div>
         <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'),['class'=>'form-label']) }}<x-required></x-required>
                <div class="input-group">
                    {{ Form::text('sku', null, ['class' => 'form-control','required' => 'required', 'placeholder' => __('Enter
                    SKU')]) }}
                    <button class="btn btn-outline-primary" type="button" onclick="generateSKU()">{{__('Generate')}}</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('purchase_price', null, array('class' => 'form-control', 'required' => 'required', 'step' =>
                '0.01', 'placeholder' => __('Enter Purchase Price'))) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::number('sale_price', null, array('class' => 'form-control','required'=>'required','step'=>'0.01',
                'placeholder'=>__('Enter Sale Price'))) }}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('tax_id', __('Tax'),['class'=>'form-label']) }}
            {{ Form::select('tax_id[]', $tax,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'')) }}
            <div class=" text-xs mt-1">
                {{__('Create tax here. ')}}<a href="{{route('taxes.index')}}"><b>{{__('Create tax')}}</b></a>
            </div>
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $category,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class=" text-xs mt-1">
                {{__('Create category here. ')}}<a href="{{route('product-category.index')}}"><b>{{__('Create Category')}}</b></a>
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('unit_id', __('Unit'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class=" text-xs mt-1">
                {{__('Create unit here. ')}}<a href="{{route('product-unit.index')}}"><b>{{__('Create unit')}}</b></a>
            </div>
        </div>

        <div class="col-md-6 form-group">
            {{Form::label('pro_image',__('Product Image'),['class'=>'form-label'])}}
            <div class="choose-file ">
                <label for="pro_image" class="form-label">
                    <input type="file" class="form-control file-validate" name="pro_image" id="pro_image" data-filename="pro_image_create">
                <p id="" class="file-error text-danger"></p>
                    <img id="image" class="mt-3" width="100" src="{{ $productService->pro_image ? \App\Models\Utility::get_file('uploads/pro_image/'.$productService->pro_image) : asset(Storage::url('uploads/pro_image/user-2_1654779769.jpg'))}}" />
                </label>
            </div>
        </div>



        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block form-label">{{__('Type')}}</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="customRadio5" name="type" value="product" @if($productService->type=='product') checked @endif >
                            <label class="custom-control-label form-label" for="customRadio5">{{__('Product')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="customRadio6" name="type" value="service" @if($productService->type=='service') checked @endif >
                            <label class="custom-control-label form-label" for="customRadio6">{{__('Service')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6 quantity {{$productService->type=='service' ? 'd-none':''}}">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('quantity',null, array('class' => 'form-control','required'=>'required', 'placeholder'=>__('Enter Quantity'))) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2', 'placeholder'=>__('Enter Description')]) !!}
        </div> --}}


    </div>
    @if(!$customFields->isEmpty())
                @include('customFields.formBuilder')
    @endif
</div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
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

