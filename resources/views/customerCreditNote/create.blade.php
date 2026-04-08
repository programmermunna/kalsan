{{ Form::open(['route' => ['custom-credits.store'], 'mothod' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('invoice', __('Invoice'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select" required="required" id="invoice" name="invoice">
                <option value>{{ __('Select Invoice') }}</option>
                @foreach ($invoices as $key => $invoice)
                    <option value="{{ $key }}">{{ \Auth::user()->invoiceNumberFormat($invoice) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-12 items d-none">
            {{ Form::label('item', __('Item'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select" required="required" id="item" name="invoice_product">
            </select>
        </div>
        <div class="form-group col-md-12 amount d-none">
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user amountnote">
                {{ Form::number('amount', 0, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0.01']) }}
            </div>
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{ Form::date('date', null, ['class' => 'form-control', 'required' => 'required','max' => date('Y-m-d')]) }}
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3','placeholder'=> __('Enter Description')]) !!}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).on('click' , '#invoice' , function(){
        var invoice_id = $(this).val();
        $.ajax({
            url: "{{route('credit-invoice.items')}}",
            method:'POST',
            data: {
                "invoice_id": invoice_id, 
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                if(data.status == true) {
                    $('.notes').remove();
    
                    $('.amount').removeClass('d-none');
                    $('.items').removeClass('d-none');
                    $('#amount').val(0).attr('max' , data.getDue);
                    $('#item').empty();
                    $('#item').append("<option value=''>{{ __('Select Item') }}</option>");
                    $.each(data.items, function (key, value) {
                        $('#item').append('<option value="' + value.id + '">' + value.product_name + '</option>');
                    });
                    
                    $('.amountnote').after(
                        '<small class="text-danger notes">Note: You can add maximum amount up to ' + data.getDue + '</small>'
                    );            
                }
            }
        });             
    });
</script>