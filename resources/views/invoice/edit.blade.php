@extends('layouts.admin')
@section('page-title')
    {{__('Invoice Edit')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('invoice.index')}}">{{__('Invoice')}}</a></li>
    <li class="breadcrumb-item">{{__('Invoice Edit')}}</li>
@endsection

@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: true,
                defaultValues: {
                    'status': 1
                },
                show: function () {
                    $(this).slideDown();
                    setTimeout(function() {
                        recalculateAllRows();
                        var newRow = $(this).find('tr:first-child');
                        if (newRow.find('.fare_amount').val()) {
                            calculateFareCommission(newRow, true);
                        }
                    }.bind(this), 200);
                },
                hide: function (deleteElement) {


                    if (confirm('Are you sure you want to delete this element?')) {
                        var el = $(this);
                        var id = $(el.find('.id')).val();
                        var amount = $(el.find('.amount')).html();

                        $(".price").change();
                        $(".discount").change();
                        $('.item option').prop('hidden', false);
                        $('.item :selected').each(function () {
                            var ids = $(this).val();
                            if (ids) {
                                $('.item').not(this).find("option[value=" + ids + "]").prop('hidden', true);
                            }
                        });

                        if (id != undefined && id != null && id != '') {
                            $.ajax({
                                url: '{{route('invoice.product.destroy')}}',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('#token').val()
                                },
                                data: {
                                    'id': id,
                                    'amount': amount,
                                },
                                cache: false,
                                success: function (data) {
                                    $('.item option').prop('hidden', false);
                                    $('.item :selected').each(function () {
                                        var id = $(this).val();
                                        $(".item option[value=" + id + "]").prop("hidden", true);
                                    });

                                    if (data.status) {
                                        show_toastr('success', data.message);
                                    } else {
                                        show_toastr('error', data.message);
                                    }
                                },
                            });
                        }

                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        calculateGrandTotal();
                    }
                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');

            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                $('tbody[data-repeater-item]').each(function(index) {
                    var rowWrapper = $(this);
                    var row = rowWrapper.find('tr:first-child');
                    var itemData = value[index] || {};

                    row.find('.fare_amount').val(itemData.fare || 0);
                    row.find('.tax').val(itemData.tax || 0);
                    row.find('.cust_commission').val(itemData.cust_commission || 0);
                    row.find('.discount').val(itemData.discount || 0);
                    row.find('.ticket_number').val(itemData.ticket_number || '');
                    row.find('.passanger_name').val(itemData.passanger_name || '');
                    rowWrapper.find('.destination').val(itemData.destination || '');
                    rowWrapper.find('.pro_description').val(itemData.description || '');
                    rowWrapper.find('.commission').val(itemData.commission || 0);

                    setTimeout(function() {
                        calculateItemTotal(row);
                    }, 150);
                });
                setTimeout(function() {
                    $('tbody[data-repeater-item]').each(function() {
                        var row = $(this).find('tr:first-child');
                        calculateItemTotal(row);
                    });
                    calculateGrandTotal();
                    setTimeout(function() {
                        isInitialLoad = false;
                    }, 100);
                }, 300);

                // Remove delete button for first row
                $('.repeater [data-repeater-item]').first().find('[data-repeater-delete]').remove();

                // Initial calculation
                setTimeout(function() {
                    calculateGrandTotal();
                }, 200);
            }

        }

       $(document).on('change', '#customer', function () {
            $('#customer_detail').removeClass('d-none');
            $('#customer_detail').addClass('d-block');
            $('#customer-box').removeClass('d-block');
            $('#customer-box').addClass('d-none');

            var id = $(this).val();
            var url = $(this).data('url');

            // 기존: 고객 상세 HTML 로드
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data != '') {
                        $('#customer_detail').html(data);
                    } else {
                        $('#customer-box').removeClass('d-none');
                        $('#customer-box').addClass('d-block');
                        $('#customer_detail').removeClass('d-block');
                        $('#customer_detail').addClass('d-none');
                    }
                },
            });

            // 추가: 고객 커미션(%)를 동적으로 불러와서 입력창에 세팅
            var commissionUrl = $(this).data('commission-url');

            if (!commissionUrl) {
                return;
            }

            if (!id || id === '') {
                $('#customer_commission').val('');
                recalculateAllRows();
                return;
            }

            $.ajax({
                url: commissionUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data.success && data.commission !== undefined) {
                        $('#customer_commission').val(data.commission);
                    } else {
                        $('#customer_commission').val('0');
                    }

                    // 커미션 변경에 맞춰 전체 금액 재계산
                    recalculateAllRows();
                },
                error: function () {
                    $('#customer_commission').val('0');
                    recalculateAllRows();
                }
            });
        });

        $(document).on('click', '#remove', function () {
            $('#customer-box').removeClass('d-none');
            $('#customer-box').addClass('d-block');
            $('#customer_detail').removeClass('d-block');
            $('#customer_detail').addClass('d-none');
        })

        $(document).on('change', '#vender_id', function () {
            var id = $(this).val();
            var url = $(this).data('url');

            if (!id || id === '') {
                $('#commission').val('');
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data.success && data.commission !== undefined) {
                        $('#commission').val(data.commission);
                    } else {
                        $('#commission').val('');
                    }
                },
                error: function () {
                    $('#commission').val('');
                }
            });
        });

        // Load commission on page load if vender_id is selected
        $(document).ready(function() {
            var venderId = $('#vender_id').val();
            if (venderId && venderId !== '') {
                var url = $('#vender_id').data('url');
                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('#token').val()
                    },
                    data: {
                        'id': venderId
                    },
                    cache: false,
                    success: function (data) {
                        if (data.success && data.commission !== undefined) {
                            $('#commission').val(data.commission);
                        }
                        setTimeout(function() {
                            isInitialLoad = false;
                        }, 500);
                    },
                    error: function () {
                        setTimeout(function() {
                            isInitialLoad = false;
                        }, 500);
                    }
                });
            } else {
                // No vender selected, allow recalculation immediately
                isInitialLoad = false;
            }
        });

        var invoice_id = '{{$invoice->id}}';
        var isInitialLoad = true; // Flag to prevent recalculation during initial load

        function getNumericValue(element) {
            var value = parseFloat(element.val());
            return isNaN(value) ? 0 : value;
        }


        function computeRowTotals(row) {
                var fare = getNumericValue(row.find('.fare'));
                var tax = getNumericValue(row.find('.tax'));
                var custCommission = getNumericValue(row.find('.cust_commission'));
                var discount = getNumericValue(row.find('.discount'));

                // Total Amount = fare + tax + cust_commission - discount
                var total = fare + tax + custCommission - discount;
                var subTotal = fare + tax + custCommission;

                // Ensure total is not negative
                total = Math.max(total, 0);

                return {
                    productTotal: 0,
                    subTotal: subTotal,
                    discount: discount,
                    total: total
                };
            }

        // Calculate Fare Commission based on formula: (Fare amount * Commission %) / 100
        function calculateFareCommission(row, forceRecalculate) {
            var fareAmount = parseFloat(row.find('.fare_amount').val()) || 0;
            var commissionPercent = parseFloat($('#commission').val()) || 0;
            var fareCommission = (fareAmount * commissionPercent) / 100;

            // Find the commission field in the same repeater item (second row)
            var repeaterItem = row.closest('tbody[data-repeater-item]');
            var commissionField = repeaterItem.find('.commission');

            if (commissionField.length) {
                // During initial load, only recalculate if field is empty or forceRecalculate is true
                if (isInitialLoad && !forceRecalculate) {
                    var currentValue = parseFloat(commissionField.val()) || 0;
                    if (currentValue > 0) {
                        return; // preserve saved value
                    }
                }
                commissionField.val(fareCommission.toFixed(2));
                calculateDiscount(row, forceRecalculate);
            }
        }

        // Calculate tax based on formula: (Net - Fare)
            function calculateTax(row) {
                var net = parseFloat(row.find('.net').val()) || 0;
                var fare = parseFloat(row.find('.fare').val()) || 0;
                var tax = net - fare;

                // Set the tax field value
                var taxField = row.find('.tax');
                if (taxField.length) {
                    taxField.val(tax.toFixed(2));
                }

                return tax;

            }

        // Calculate Discount based on formula: (Commission * Customer Commission / 100)
        function calculateDiscount(row, forceRecalculate) {
            var repeaterItem = row.closest('tbody[data-repeater-item]');
            var commission = parseFloat(repeaterItem.find('.commission').val()) || 0;
            var customerCommission = parseFloat($('#customer_commission').val()) || 0;

            var discount = (commission * customerCommission) / 100;

            var discountField = row.find('.discount');
            if (discountField.length) {
                if (isInitialLoad && !forceRecalculate) {
                    var currentValue = parseFloat(discountField.val()) || 0;
                    if (currentValue > 0) {
                        return; // preserve saved discount on initial load
                    }
                }
                discountField.val(discount.toFixed(2));
            }
        }

        function calculateItemTotal(row) {
            var totals = computeRowTotals(row);
            $(row).find('.amount').html(totals.total.toFixed(2));
            $(row).find('.price').val(totals.total.toFixed(2));
            return totals;
        }

        function calculateGrandTotal() {
            var subTotal = 0;
            var totalDiscount = 0;

            $('tbody[data-repeater-item]').each(function() {
                var row = $(this).find('tr:first-child');
                var rowTotals = computeRowTotals(row);
                subTotal += rowTotals.subTotal;
                totalDiscount += rowTotals.discount;
            });

            var grandTotal = subTotal - totalDiscount;

            $('.subTotal').html(subTotal.toFixed(2));
            $('.totalDiscount').html(totalDiscount.toFixed(2));
            $('.totalAmount').html(grandTotal.toFixed(2));
        }

        function recalculateAllRows() {
            $('tbody[data-repeater-item]').each(function() {
                var row = $(this).find('tr:first-child');
                if (row.find('.fare_amount').val()) {
                    calculateFareCommission(row, !isInitialLoad);
                }
                calculateItemTotal(row);
            });
            calculateGrandTotal();
        }

        // Real-time calculation on input changes
        $(document).on('keyup change input', '.fare_amount', function () {
            var row = $(this).closest('tbody[data-repeater-item]').find('tr:first-child');
            calculateFareCommission(row, true);
            calculateItemTotal(row);
            calculateGrandTotal();
        });

        // Calculate Fare Commission when Commission % changes
        $(document).on('change', '#commission', function () {
            if (isInitialLoad) {
                return;
            }
            $('tbody[data-repeater-item]').each(function() {
                var row = $(this).find('tr:first-child');
                if (row.find('.fare_amount').val()) {
                    calculateFareCommission(row, true);
                    calculateItemTotal(row);
                }
            });
            calculateGrandTotal();
        });

        // Calculate Discount when Customer Commission changes
        $(document).on('keyup change input', '#customer_commission', function () {
            $('tbody[data-repeater-item]').each(function() {
                var row = $(this).find('tr:first-child');
                calculateDiscount(row, !isInitialLoad);
                calculateItemTotal(row);
            });
            calculateGrandTotal();
        });

        // Real-time calculation on input changes for other fields
        $(document).on('keyup change input', '.tax, .cust_commission, .discount', function () {
            var row = $(this).closest('tbody[data-repeater-item]').find('tr:first-child');
            calculateItemTotal(row);
            calculateGrandTotal();
        });

        // Filter destination options based on selected origin
        $(document).on('change', '#origin', function () {
            var selectedOriginId = $(this).val();
            var destinationSelect = $('#destination');

            destinationSelect.find('option').show().prop('disabled', false);

            if (selectedOriginId && selectedOriginId !== '') {
                destinationSelect.find('option[value="' + selectedOriginId + '"]').hide().prop('disabled', true);

                if (destinationSelect.val() === selectedOriginId) {
                    destinationSelect.val('').trigger('change');
                }
            }
        });

        // Initialize on page load
        $(document).ready(function() {
            if ($('#origin').val()) {
                $('#origin').trigger('change');
            }
        });

    </script>
@endpush

@section('content')
                                                        @php
    $issueDateValue = old('issue_date', !empty($invoice->issue_date) ? \Illuminate\Support\Carbon::parse($invoice->issue_date)->format('Y-m-d') : null);
    $dueDateValue = old('due_date', !empty($invoice->due_date) ? \Illuminate\Support\Carbon::parse($invoice->due_date)->format('Y-m-d') : null);
    $arrivalDateValue = old('arrival_date', !empty($invoice->arrival_date) ? \Illuminate\Support\Carbon::parse($invoice->arrival_date)->format('Y-m-d') : null);
                                                        @endphp
                                                        <div class="row">
                                                            {{ Form::model($invoice, array('route' => array('invoice.update', $invoice->id), 'method' => 'PUT', 'class' => 'w-100', 'class' => 'needs-validation', 'novalidate')) }}
                                                            <div class="col-12">
                                                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                                                <div class="form-group" id="customer-box">
                                                                                    {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                    {{ Form::select('customer_id', $customers, null, array('class' => 'form-control select', 'id' => 'customer', 'data-url' => route('invoice.customer'), 'required' => 'required')) }}
                                                                                    <div class="text-xs mt-1">
                                                                                        {{ __('Create customer here.') }} <a href="{{ route('customer.index') }}"><b>{{ __('Create customer') }}</b></a>
                                                                                    </div>
                                                                                </div>

                                                                                <div id="customer_detail" class="d-none">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        {{ Form::label('commission1', __('Customer Commission'), ['class' => 'form-label']) }}
                                                                                        <div class="form-icon-user">
                                                                                            <span><i class="ti ti-joint"></i></span>
                                                                                            {{ Form::number('commission1', '', array('class' => 'form-control', 'id' => 'customer_commission', 'placeholder' => __('Commission %'))) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        {{ Form::label('origin', __('Orogin'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                        {{ Form::select('origin', $product_services, old('origin', $invoice->origin), array('class' => 'form-control select', 'id' => 'origin', 'required' => 'required')) }}
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        {{ Form::label('destination', __('Destination'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                        {{ Form::select('destination', $destination, old('destination', $invoice->destination), array('class' => 'form-control select', 'id' => 'destination', 'required' => 'required')) }}
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Remarks')]) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('invoice_number', __('Invoice Number'), ['class' => 'form-label']) }}
                                                                                            <div class="form-icon-user">
                                                                                                <input type="text" class="form-control" value="{{$invoice_number}}" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('issue_date', __('Sales Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            <div class="form-icon-user">
                                                                                                {{Form::date('issue_date', $issueDateValue, array('class' => 'form-control', 'required' => 'required'))}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('due_date', __('Departure Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            <div class="form-icon-user">
                                                                                                {{Form::date('due_date', $dueDateValue, array('class' => 'form-control', 'required' => 'required'))}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('arrival_date', __('Return Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            <div class="form-icon-user">
                                                                                                {{Form::date('arrival_date', $arrivalDateValue, array('class' => 'form-control', 'required' => 'required'))}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('vender_id', __('Company'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            {{ Form::select('vender_id', $topup, old('vender_id', $invoice->vender_id), array('class' => 'form-control select', 'id' => 'vender_id', 'data-url' => route('invoice.vender'), 'required' => 'required')) }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('commission', __('Commission'), ['class' => 'form-label']) }}
                                                                                            <div class="form-icon-user">
                                                                                                <span><i class="ti ti-joint"></i></span>
                                                                                                {{ Form::number('commission', '', array('class' => 'form-control', 'id' => 'commission', 'placeholder' => __('Commission %'))) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('category_id', __('Ticket Trip'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            {{ Form::select('category_id', $category, null, array('class' => 'form-control select', 'required' => 'required')) }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            {{ Form::label('pnr', __('PNR'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                                            <div class="form-icon-user">
                                                                                                <span><i class="ti ti-joint"></i></span>
                                                                                                {{ Form::text('pnr', null, array('class' => 'form-control', 'placeholder' => __('Enter PNR'), 'required' => 'required')) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    @if(!$customFields->isEmpty())
                                                                                        @include('customFields.formBuilder')
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <h5 class=" d-inline-block mb-4">{{__('List of Passengers')}}</h5>
                                                                <div class="card repeater" data-value='{{ json_encode($invoice->items) }}'>
                                                                    <div class="item-section py-2">
                                                                        <div class="row justify-content-between align-items-center">
                                                                            <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                                                                                <div class="all-button-box me-2">
                                                                                    <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal" data-target="#add-bank">
                                                                                        <i class="ti ti-plus"></i> {{__('Add Passenger')}}
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body table-border-style mt-2">
                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0 table-custom-style" data-repeater-list="items" id="sortable-table">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>{{__('Type')}}</th>
                                                                                    <th>{{__('Ticket Number')}}</th>
                                                                                    <th>{{__('Passanger Name')}}</th>
                                                                                    <th>{{__('Net')}} </th>
                                                                                    <th>{{__('Fare Amount')}} </th>
                                                                                    <th>{{__('Tax')}} </th>
                                                                                    <th>{{__('Refund')}} </th>
                                                                                    <th>{{__('Commission')}} </th>
                                                                                    <th>{{__('Cust Comm')}} </th>
                                                                                    <th>{{__('Discount')}}</th>
                                                                                    <th class="text-end">{{__('Total Amount')}} <br><small class="text-danger font-weight-bold">{{__('after discount')}}</small></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                                </thead>

                                                                                <tbody class="ui-sortable" data-repeater-item>
                                                                                <tr>
                                                                                    {{ Form::hidden('id', null, array('class' => 'form-control id')) }}
                                                                                    <td class="form-group pt-0">
                                                                                        {{ Form::select('type', ['Adult' => 'Adult', 'Child' => 'Child', 'Infant' => 'Infant'], null, ['class' => 'form-control', 'style' => 'width: 90px;']) }}
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('ticket_number', null, ['class' => 'form-control ticket_number', 'required' => 'required', 'placeholder' => __('Ticket #')]) }}
                                                                                        </div>
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::text('passanger_name', null, ['class' => 'form-control passanger_name', 'required' => 'required', 'placeholder' => __('Passanger Name'), 'style' => 'width: 200px;']) }}
                                                                                        </div>
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('net', '', array('class' => 'form-control net', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                                                                        </div>
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('fare', null, ['class' => 'form-control fare', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01']) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('tax', '', array('class' => 'form-control tax', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 70px;', 'readonly' => 'readonly')) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('refund', '', array('class' => 'form-control refund', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('commission', '', array('class' => 'form-control commission', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;', 'readonly' => 'readonly')) }}

                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('cust_commission', null, ['class' => 'form-control cust_commission', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 70px;']) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group price-input input-group search-form">
                                                                                            {{ Form::number('discount', null, ['class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 50px;', 'readonly' => 'readonly']) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    {{ Form::hidden('price', null, array('class' => 'form-control price')) }}
                                                                                    <td class="text-end amount">0.00</td>
                                                                                    <td>
                                                                                        <div class="action-btn me-2">
                                                                                            <a href="#" class="ti ti-trash text-white btn btn-sm repeater-action-btn bg-danger ms-2  delete_item" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-repeater-delete></a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                {{-- <tr>
                                                                                    <td colspan="4">
                                                                                        <div class="form-group">
                                                                                            {{ Form::textarea('destination', null, ['class' => 'form-control destination', 'rows' => '2', 'placeholder' => __('Destinations')]) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td colspan="2">
                                                                                        <div class="form-group">
                                                                                            {{ Form::number('commission', '', array('class' => 'form-control commission', 'required' => 'required', 'placeholder' => __('Fare Commission'), 'step' => '0.01', 'style' => 'width: 150px;', 'readonly' => 'readonly')) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td colspan="4"></td>
                                                                                </tr> --}}
                                                                                </tbody>
                                                                                <tfoot>
                                                                                <tr>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td><strong>{{__('Sub Total')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                                                                    <td class="text-end subTotal">0.00</td>
                                                                                    <td></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td><strong>{{__('Discount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                                                                    <td class="text-end totalDiscount">0.00</td>
                                                                                    <td></td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td class="blue-text"><strong>{{__('Total Amount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                                                                    <td class="text-end totalAmount blue-text">0.00</td>
                                                                                    <td></td>
                                                                                </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                @php $route = route("invoice.index"); @endphp
                                                                <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{ $route }}'" class="btn btn-secondary me-2">
                                                                <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
                                                            </div>
                                                            {{ Form::close() }}
                                                        </div>
@endsection

