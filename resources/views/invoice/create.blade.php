@extends('layouts.admin')
@section('page-title')
    {{__('New License')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('invoice.index')}}">{{__('License')}}</a></li>
    <li class="breadcrumb-item">{{__('Create New License')}}</li>
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
                    initEmpty: false,
                    defaultValues: {
                        'status': 1
                    },
                    show: function () {
                        $(this).slideDown();
                        var file_uploads = $(this).find('input.multi');
                        if (file_uploads.length) {
                            $(this).find('input.multi').MultiFile({
                                max: 3,
                                accept: 'png|jpg|jpeg',
                                max_size: 2048
                            });
                        }

                        setTimeout(function() {
                            recalculateAllRows();
                        }, 100);
                    },
                    hide: function (deleteElement) {
                        if (confirm('Are you sure you want to delete this element?')) {
                            $(this).slideUp(deleteElement);
                            $(this).remove();

                            $('.item option').prop('hidden', false);
                            $('.item :selected').each(function () {
                                var id = $(this).val();
                                if (id) {
                                    $('.item').not(this).find("option[value=" + id + "]").prop('hidden', true);
                                }
                            });

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

            function initializeProductPrice(container) {
                var row = container.find('tr:first-child');
                if (row.length === 0) {
                    return;
                }

                var hasProduct = !!row.find('.item').val();
                if (hasProduct) {
                    setProductPriceField(row, row.find('.product_price').val() || 0, true);
                } else {
                    setProductPriceField(row, 0, false);
                }
            }

            function setProductPriceField(row, price, enableField) {
                if (typeof enableField === 'undefined') {
                    enableField = true;
                }

                var productField = row.find('.product_price');

                if (enableField === false) {
                    productField.val('');
                    productField.prop('disabled', true);
                    return;
                }

                var numericPrice = parseFloat(price);
                if (isNaN(numericPrice)) {
                    numericPrice = 0;
                }

                productField.prop('disabled', false);
                productField.val(numericPrice.toFixed(2));
            }

            function getNumericValue(element) {
                var value = parseFloat(element.val());
                return isNaN(value) ? 0 : value;
            }

            function computeRowTotals(row) {
                var net = getNumericValue(row.find('.net'));
                var fare = getNumericValue(row.find('.fare'));
                var tax = getNumericValue(row.find('.tax'));
                var refund = getNumericValue(row.find('.refund'));
                var commission = getNumericValue(row.find('.commission'));
                var discount = getNumericValue(row.find('.discount'));

                // Total Amount = (net +fare + tax + cust_commission - discount)
                var total = net + fare + tax + commission + refund - discount ;
                var subTotal = net + fare + tax + commission + refund;

                return {
                    productTotal: 0,
                    subTotal: subTotal,
                    discount: discount,
                    total: total
                };
            }

            // Calculate Fare Commission based on formula: (Fare amount * Commission %) / 100
           /*  function calculateFareCommission(row) {
                var fareAmount = parseFloat(row.find('.fare').val()) || 0;

                var commissionPercent = parseFloat($('#commission').val()) || 0;
                var fareCommission = (fareAmount * commissionPercent) / 100;

                // Find the commission field in the same repeater item (second row)
                var repeaterItem = row.closest('tbody[data-repeater-item]');
                var commissionField = repeaterItem.find('.commission');

                if (commissionField.length) {
                    commissionField.val(fareCommission.toFixed(2));
                    // After setting commission, calculate discount
                    calculateDiscount(row);
                }
            }
            // Calculate tax based on formula: (Net - Fare)
            function calculateTax(row) {
                    var net = parseFloat(row.find('.net').val()) || 0;
                    var fare = parseFloat(row.find('.fare').val()) || 0;
                    var tax =  = parseFloat(row.find('.tax').val()) || 0;

                     // Set the tax field value
                var taxField = row.find('.tax');
                if (taxField.length) {
                    taxField.val(tax.toFixed(2));
                }

                return tax;

                }
     */
            // Calculate Discount based on formula: (Commission * Customer Commission / 100)
            function calculateDiscount(row) {
                //var commission = parseFloat(row.find('.commission').val()) || 0;

                // Formula: Discount = (Commission * Customer Commission / 100)
                var discount = parseFloat(row.find('.discount').val()) || 0;

                var discountField = row.find('.discount');
                if (discountField.length) {
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
                    // Calculate fare commission if fare is entered (this will also calculate discount)
                    if (row.find('.fare').val()) {
                        calculateFareCommission(row);
                    } else {
                        // If commission already exists, calculate discount
                        // Even if commission is 0, discount should be calculated (will be 0)
                        calculateDiscount(row);
                    }
                    calculateItemTotal(row);
                });
                calculateGrandTotal();
            }



            // Real-time calculation on input changes
            $(document).on('keyup change input', '.net', function () {
                var row = $(this).closest('tbody[data-repeater-item]').find('tr:first-child');
               // calculateTax(row);
               // calculateFareCommission(row); // This will also call calculateDiscount internally
                calculateItemTotal(row);
                calculateItemTotal(row);
                calculateGrandTotal();
            });

            // Calculate Fare Commission when Commission % changes
            $(document).on('change', '#commission', function () {
                $('tbody[data-repeater-item]').each(function() {
                    var row = $(this).find('tr:first-child');
                    if (row.find('.fare').val()) {
                        calculateFareCommission(row); // This will also call calculateDiscount internally
                        calculateItemTotal(row);
                    }
                });
                calculateGrandTotal();
            });

            // Calculate Discount when Customer Commission changes
            $(document).on('keyup change input', '#customer_commission', function () {
                $('tbody[data-repeater-item]').each(function() {
                    var row = $(this).find('tr:first-child');
                    // Always calculate discount (even if commission is 0, discount should be 0)
                    calculateDiscount(row);
                    calculateItemTotal(row);
                });
                calculateGrandTotal();
            });

            // Note: .commission field is readonly, so we don't need event handler for it
            // Discount is calculated automatically when commission is set via calculateFareCommission

            // Real-time calculation on input changes for other fields
            $(document).on('keyup change input', '.net,.fare,.tax,.refund, .commission, .discount', function () {
                var row = $(this).closest('tbody[data-repeater-item]').find('tr:first-child');
                calculateItemTotal(row);
                calculateGrandTotal();
            })

            // Filter destination options based on selected origin
           /*  $(document).on('change', '#origin', function () {
                var selectedOriginId = $(this).val();
                var destinationSelect = $('#destination');

                // Show all destination options first
                destinationSelect.find('option').show().prop('disabled', false);

                // If origin is selected, hide/disable the same option in destination
                if (selectedOriginId && selectedOriginId !== '') {
                    destinationSelect.find('option[value="' + selectedOriginId + '"]').hide().prop('disabled', true);

                    // If the currently selected destination is the same as origin, reset it
                    if (destinationSelect.val() === selectedOriginId) {
                        destinationSelect.val('').trigger('change');
                    }
                }
            }); */

            // Initialize on page load
            $(document).ready(function() {
                if ($('#origin').val()) {
                    $('#origin').trigger('change');
                }
            });

            var customerId = '{{$customerId}}';
            if (customerId > 0) {
                $('#customer').val(customerId).change();
            }

            // Initial calculation on page load
            $(document).ready(function() {
                setTimeout(function() {
                    recalculateAllRows();
                }, 300);
            });

            // Handle Create & New button
                $(document).ready(function () {
                    $('form').on('submit', function (e) {
                        $(this).attr('action', '{{ route("invoice.store") }}')
                    });
                });

        </script>
@endpush
@section('content')
    <div class="row">
        {{ Form::open(array('url' => 'invoice', 'id' => 'invoice-form', 'class' => 'w-100 needs-validation', 'novalidate')) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::select('customer_id', $customers, $customerId, array('class' => 'form-control select', 'id' => 'customer', 'data-url' => route('invoice.customer'), 'data-commission-url' => route('invoice.customer.commission'), 'required' => 'required')) }}
                                <div class="text-xs mt-1">
                                    {{ __('Create customer here.') }} <a href="{{ route('customer.index') }}"><b>{{ __('Create customer') }}</b></a>
                                </div>
                            </div>

                            <div id="customer_detail" class="d-none">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Remarks')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('invoice_number', __('License Number'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            <input type="text" class="form-control" value="{{$invoice_number}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <div class="form-icon-user">
                                            {{Form::date('issue_date', null, array('class' => 'form-control', 'required' => 'required'))}}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <div class="form-icon-user">
                                            {{Form::date('due_date', null, array('class' => 'form-control', 'required' => 'required'))}}

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
            <h5 class=" d-inline-block mb-4">{{__('License Details')}}</h5>
            <div class="card repeater">
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal" data-target="#add-bank">
                                    <i class="ti ti-plus"></i> {{__('Add License')}}
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
                                <th>{{__('Nooca Ruqsada')}}</th>
                                <th>{{__('Grade')}}</th>
                                <th>{{__('Diiwangalinta')}} </th>
                                <th>{{__('B-Tabeelaha')}} </th>
                                <th>{{__('Shahado')}} </th>
                                <th>{{__('Buugga')}} </th>
                                <th>{{__('Tijaabo Qadka')}} </th>
                                <th>{{__('Discount')}}</th>
                                <th class="text-end">{{__('Total Amount')}} <br><small class="text-danger font-weight-bold">{{__('after discount')}}</small></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody class="ui-sortable" data-repeater-item>
                            <tr>
                                <td class="form-group pt-0">
                                    {{ Form::select('type', ['Babuur' => 'Babuur', 'Babuurta Waaween' => 'Babuurta Waaween', 'Moto' => 'Moto'], old('title', $user->title ?? ''), ['class' => 'form-control', 'style' => 'width: 110px;']) }}
                                </td>

                                <td class="form-group pt-0">
                                    {{ Form::select('grade', ['A' => 'A', 'A1' => 'A1', 'B' => 'B', 'C' => 'C', 'C1' => 'C1', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G'], old('title', $user->title ?? ''), ['class' => 'form-control', 'style' => 'width: 110px;']) }}
                                </td>


                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('net', '', array('class' => 'form-control net', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                    </div>
                                </td>

                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('fare', '', array('class' => 'form-control fare', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                    </div>
                                </td>

                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('tax', '', array('class' => 'form-control tax', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                    </div>
                                </td>

                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('refund', '', array('class' => 'form-control refund', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                    </div>
                                </td>

                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('commission', '', array('class' => 'form-control commission', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}
                                    </div>
                                </td>


                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('discount', '', array('class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('$0.00'), 'step' => '0.01', 'style' => 'width: 90px;')) }}

                                    </div>
                                </td>

                                {{ Form::hidden('price', '', array('class' => 'form-control price')) }}
                                <td class="text-end amount">0.00</td>
                                <td>
                                    <div class="action-btn me-2">
                                        <a href="#" class="ti ti-trash text-white btn btn-sm repeater-action-btn bg-danger ms-2  delete_item" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-repeater-delete></a>
                                    </div>
                                </td>
                            </tr>

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
            <input type="submit" name="submit_button" value="{{__('Create')}}" class="btn btn-primary">
            {{-- <input type="submit" name="submit_button" value="Create & New" class="btn btn-success" style="margin-left:7px"> --}}
        </div>
        {{ Form::close() }}

    </div>
@endsection


