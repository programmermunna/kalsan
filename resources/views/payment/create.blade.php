{{ Form::open(array('url' => 'payment', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="form-check form-check-inline form-group">
                <input type="radio" id="expenses_radio" value="expenses" name="type" class="form-check-input" checked>
                <label class="form-check-label" for="expenses_radio">{{__('Expense')}}</label>
            </div>
            <div class="form-check form-check-inline form-group">
                <input type="radio" id="vendor_radio" value="vendor" name="type" class="form-check-input">
                <label class="form-check-label" for="vendor_radio">{{__('Vendor')}}</label>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <!-- Expense Section -->
                <div class="col-md-6 expenses-section">
                    <div class="form-group" id="expense-box">
                        {{ Form::label('expense_id', __('Expense'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::select('expense_id', $expenses, null, array('class' => 'form-control select', 'id' => 'expense_select')) }}
                    </div>
                    <div id="expense_detail" class="d-none"></div>
                </div>

                <!-- Vendor Section -->
                <div class="col-md-6 vendor-section d-none">
                    <div class="form-group" id="vender-box">
                        {{ Form::label('vender_id', __('Vendor'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::select('vender_id', $venders, $Id ?? null, array('class' => 'form-control select', 'id' => 'vender_select')) }}
                    </div>
                    <div id="vender_detail" class="d-none"></div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{Form::date('date', null, array('class' => 'form-control', 'required' => 'required'))}}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('amount', '', array('class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter Amount'))) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('account_id', $accounts, null, array('class' => 'form-control select', 'required' => 'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create account here.') }} <a
                    href="{{ route('bank-account.index') }}"><b>{{ __('Create account') }}</b></a>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
            {{ Form::text('reference', '', array('class' => 'form-control', 'placeholder' => __('Enter Reference'))) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description'))) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).ready(function () {
        // Initialize select2 if available
        if ($.fn.select2) {
            $('.select').select2({
                dropdownParent: $('#commonModal')
            });
        }

        // Handle radio button change
        $('input[name="type"]').on('change', function () {
            var selectedType = $(this).val();

            if (selectedType === 'expenses') {
                $('.expenses-section').removeClass('d-none');
                $('.vendor-section').addClass('d-none');

                // Remove required attribute from vendor field
                $('#vender_select').removeAttr('required');
                // Add required attribute to expense field
                $('#expense_select').attr('required', 'required');

            } else if (selectedType === 'vendor') {
                $('.expenses-section').addClass('d-none');
                $('.vendor-section').removeClass('d-none');

                // Remove required attribute from expense field
                $('#expense_select').removeAttr('required');
                // Add required attribute to vendor field
                $('#vender_select').attr('required', 'required');
            }
        });

        // Trigger change on page load to set initial state (assuming Expense is default)
        $('input[name="type"]:checked').trigger('change');

        // Handle vendor selection change if you have an endpoint to get vendor details
        $('#vender_select').on('change', function () {
            var venderId = $(this).val();
            var url = $(this).data('url');

            if (venderId > 0 && url) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        vender_id: venderId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        $('#vender_detail').removeClass('d-none').html(data);
                    }
                });
            } else {
                $('#vender_detail').addClass('d-none').empty();
            }
        });
    });
</script>
