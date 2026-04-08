
{{ Form::model($debitNote, array('route' => array('bill.custom-note.edit',$debitNote->bill, $debitNote->id), 'method' => 'post', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('bill', __('Bill'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select" required="required" id="bill" name="bill" disabled>
                <option value>{{ __('Select Bill') }}</option>
                @foreach ($bills as $key => $bill)
                    <option value="{{ $key }}" {{ $key == $debitNote->bill ? 'selected'  : ''}}>{{ \Auth::user()->billNumberFormat($bill) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-12 items d-none">
            {{ Form::label('item', __('Item'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select" required="required" id="item" name="bill_product">
            </select>
        </div>
        <div class="form-group amount col-md-12 d-none">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user amountnote">
                {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01','min'=> 0.01)) }}
            </div>
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control ','required'=>'required','placeholder'=>__('Select Issue Date'),'max' => date('Y-m-d')))}}
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3','placeholder'=>__('Enter Description')]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    $(document).ready (function(){
        var bill_id = $('#bill').val();
        $.ajax({
        url: "{{route('debit-bill.items')}}",
        method:'POST',
        data: {
            "bill_id": bill_id, 
            "_token": "{{ csrf_token() }}",
        },
        success: function (data) {       
                if(data.status == true) {

                    $('.notes').remove();

                    $('.amount').removeClass('d-none');
                    $('.items').removeClass('d-none');       
                    $('#amount').attr('max' , data.getDue);
                    $('#item').empty();
                    $('#item').append("<option value=''>{{ __('Select Item') }}</option>");
                    $.each(data.items, function (key, value) {
                        var select = '';
                        if (value.id == '{{ $debitNote->bill_product }}') {
                            select = 'selected';
                        }
                        $('#item').append('<option value="' + value.id + '"  ' + select + '>' +
                        value.product_name + '</option>');
                    });
                    
                    $('.amountnote').after(
                        '<small class="text-danger notes">Note: You can add maximum amount up to ' + data.getDue + '</small>'
                    );    
                }
            }            
        });    
    });
</script>