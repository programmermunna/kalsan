{{ Form::open(array('route' => array('bill.debit.storenote',$bill_id),'mothod'=>'post', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('debit_note', __('Debit Note'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                <select class="form-control select" required="required" id="debit_note" name="debit_note">
                    <option value>{{ __('Select Debit Note') }}</option>
                    @foreach ($debitNotes as $key => $debitNote)
                        <option value="{{ $key }}">{{ \App\Models\CustomerDebitNotes::debitNumberFormat($debitNote) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control ','required'=>'required','placeholder'=>'Select Issue Date','max' => date('Y-m-d')))}}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{ Form::number('amount', 0, array('class' => 'form-control','required'=>'required','step'=>'0.01','min'=>'0')) }}
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', '', ['class'=>'form-control','rows'=>'3','placeholder' => 'Enter Description']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    $(document).on('click' , '#debit_note', function(){
        var debit_note = $(this).val();
        $.ajax({
            url: "{{route('debit-note.price')}}",
            method:'POST',
            data: {
                "debit_note": debit_note, 
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                if (data !== undefined) {
                    $('#amount').val(data);
                    $('input[name="amount"]').attr('max', data);
                    $('input[name="amount"]').attr('min', 0);
                }
            }
        });  
    });
</script>