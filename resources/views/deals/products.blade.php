{{ Form::model($deal, array('route' => array('deals.products.update', $deal->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('products', __('Products'),['class'=>'form-label']) }}
            {{ Form::select('products[]', $products,false, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'','required'=>'required', 'placeholder' => __('Select Products'))) }}
            <div class="text-xs mt-1">
                {{ __('Create product here.') }} <a href="{{ route('productservice.index') }}"><b>{{ __('Create product') }}</b></a>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

