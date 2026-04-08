{{ Form::open(array('url' => 'revenue','enctype' => 'multipart/form-data' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01' , 'placeholder'=>__('Enter Amount'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a href="{{ route('bank-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
        {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::select('customer_id', $customers,null, array('class' => 'form-control select','required'=>'required')) }}
        <div class="text-xs mt-1">
            {{ __('Create customer here.') }} <a href="{{ route('customer.index') }}"><b>{{ __('Create customer') }}</b></a>
        </div>
    </div>
    <div class="form-group  col-md-12">
        {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
        {{ Form::textarea('description', '', array('class' => 'form-control','rows'=>3 , 'placeholder'=>__('Enter Description'))) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required')) }}
        <div class="text-xs mt-1">
            {{ __('Create category here.') }} <a href="{{ route('product-category.index') }}"><b>{{ __('Create category') }}</b></a>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
        {{ Form::text('reference', '', array('class' => 'form-control' , 'placeholder'=>__('Enter Reference'))) }}
    </div>

    <div class="form-group col-md-6">
        {{Form::label('add_receipt',__('Payment Receipt'),['class' => 'col-form-label'])}}
        {{Form::file('add_receipt',array('class'=>'form-control', 'id'=>'files'))}}
        <img id="image" class="mt-3" style="width:25%;"/>
    </div>

</div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    document.getElementById('files').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>
