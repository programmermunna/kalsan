{{Form::model($saturationdeduction,array('route' => array('saturationdeduction.update', $saturationdeduction->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">

    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('deduction_option', __('Deduction Options'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('deduction_option',$deduction_options,null, array('class' => 'form-control select','required'=>'required')) }}
                    <div class="text-xs mt-1">
                        {{ __('Create deduction option here.') }} <a href="{{ route('deductionoption.index') }}"><b>{{ __('Create deduction option') }}</b></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('type', $saturationdeduc, null, ['class' => 'form-control select amount_type', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('amount', __('Amount'),['class'=>'form-label amount_label']) }}<x-required></x-required>
                    {{ Form::number('amount',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Amount'))) }}
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{Form::close()}}
