{{ Form::model($transfer, array('route' => array('bank-transfer.update', $transfer->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-6">
            {{ Form::label('from_account', __('From Account'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('from_account', $bankAccount,null, array('class' => 'form-control select', 'id' => 'from_account', 'required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a href="{{ route('bank-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('to_account', __('To Account'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('to_account', $bankAccount,null, array('class' => 'form-control select', 'id' => 'to_account', 'required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a href="{{ route('bank-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Amount'))) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            {{ Form::text('reference', null, array('class' => 'form-control', 'placeholder'=>__('Enter Reference'))) }}
        </div>

        <div class="form-group mb-0 col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control','rows'=>3, 'placeholder'=>__('Enter Description'))) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
