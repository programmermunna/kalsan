@extends('layouts.admin')
@section('page-title')
    {{ __('Sales Report') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Sales Report') }}</li>
@endsection
@push('script-page')

        <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
        <script>
            var filename = $('#filename').val();

            function saveAsPDF() {
                var printContents = document.getElementById('printableArea').innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        </script>

        <script>
            $(document).ready(function() {
                $("#filter").click(function() {
                    $("#show_filter").toggle();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                callback();
                function callback() {
                    var start_date = $(".startDate").val();
                    var end_date = $(".endDate").val();

                    $('.start_date').val(start_date);
                    $('.end_date').val(end_date);
                }
            });

        </script>

    <script>
            $(document).ready(function() {
                var id1 = $('.nav-item .active').attr('href');
                $('.report').val(id1);

                $("ul.nav-pills > li > a").click(function() {
                    var report = $(this).attr('href');
                    $('.report').val(report);
                });
            });

        </script>

        <script>
            $(document).ready(function() {
                // Store footer HTML
                var footerHtml = '<tr><td colspan="9" class="text-end"><strong>{{ __('Total') }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_fare'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_tax'] ?? 0) }}</strong><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_refund'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_commission'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_cust_commission'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_discount'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_amount'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_paid'] ?? 0) }}</strong></td><td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_balance'] ?? 0) }}</strong></td></tr>';

                // Function to restore footer
                function restoreFooter() {
                    var tableElement = document.querySelector("#item-reort");
                    if (!tableElement) return;

                    var tfoot = tableElement.querySelector('tfoot');
                    if (!tfoot) {
                        tfoot = document.createElement('tfoot');
                        tableElement.appendChild(tfoot);
                    }

                    if (!tfoot.innerHTML.trim() || !tfoot.querySelector('tr')) {
                        tfoot.innerHTML = footerHtml;
                    }
                }

                // Use MutationObserver to watch for footer removal
                var tableElement = document.querySelector("#item-reort");
                if (tableElement) {
                    var observer = new MutationObserver(function(mutations) {
                        var tfoot = tableElement.querySelector('tfoot');
                        if (!tfoot || !tfoot.innerHTML.trim() || !tfoot.querySelector('tr')) {
                            restoreFooter();
                        }
                    });

                    observer.observe(tableElement, {
                        childList: true,
                        subtree: true
                    });
                }

                // Restore footer after DataTables initialization (with multiple attempts)
                var attempts = 0;
                var maxAttempts = 10;
                var checkInterval = setInterval(function() {
                    attempts++;
                    restoreFooter();

                    var tableElement = document.querySelector("#item-reort");
                    if (tableElement && tableElement.datatable && attempts >= 3) {
                        clearInterval(checkInterval);
                        // Final restore after DataTables is fully initialized
                        setTimeout(restoreFooter, 200);
                    }

                    if (attempts >= maxAttempts) {
                        clearInterval(checkInterval);
                    }
                }, 300);

                // Restore footer when tab is shown
                $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                    if ($(e.target).attr('href') === '#item') {
                        setTimeout(restoreFooter, 300);
                    }
                });
            });
        </script>

@endpush

@section('action-btn')

    <div class="float-end">
        <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip"
            title="{{ __('Print') }}" data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>
    </div>

    <div class="float-end me-2">
        {{ Form::open(['route' => ['sales.export']]) }}
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <input type="hidden" name="report" class="report">
        <button type="submit" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            data-original-title="{{ __('Export') }}"><i class="ti ti-file-export"></i></button>
        {{ Form::close() }}
    </div>

    <div class="float-end me-2" id="filter">
        <button id="filter" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
    </div>

@endsection

@section('content')
                                                            <div class="mt-4">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-md-12">
                                                                        <div class="mt-2" id="multiCollapseExample1">
                                                                            <div class="card" id="show_filter" style="display:none;">
                                                                                <div class="card-body">
                                                                                    {{ Form::open(['route' => ['report.sales'], 'method' => 'GET', 'id' => 'report_sales']) }}
                                                                                    <div class="row align-items-center justify-content-end">
                                                                                        <div class="col-xl-10">
                                                                                            <div class="row">
                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                        {{ Form::label('user_id', __('Select User'), ['class' => 'form-label']) }}
                                                                                                        {{ Form::select(
        'user_id',
        (['' => 'All Users'] + $users->pluck('name', 'id')->toArray()),
        $filter['userId'] ?? '',
        ['class' => 'form-control']
    ) }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                        {{ Form::label('vender_id', __('Select Vender'), ['class' => 'form-label']) }}
                                                                                                        {{ Form::select(
        'vender_id',
        (['' => 'All Venders'] + $venders->pluck('name', 'id')->toArray()),
        $filter['venderId'] ?? '',
        ['class' => 'form-control']
    ) }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                        {{ Form::label('customer_id', __('Select Customer'), ['class' => 'form-label']) }}
                                                                                                        {{ Form::select(
        'customer_id',
        (['' => 'All Customers'] + $customers->pluck('name', 'id')->toArray()),
        $filter['customerId'] ?? '',
        ['class' => 'form-control']
    ) }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                        {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                                                                        {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                                                    <div class="btn-box">
                                                                                                        {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                                                                        {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <input type="hidden" name="view" value="horizontal">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-auto mt-4">
                                                                                            <div class="row">
                                                                                                <div class="col-auto">
                                                                                                    <a href="#" class="btn btn-sm btn-primary"
                                                                                                        onclick="document.getElementById('report_sales').submit(); return false;"
                                                                                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                                                                        data-original-title="{{ __('apply') }}">
                                                                                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                                                                    </a>

                                                                                                    <a href="{{ route('report.sales') }}" class="btn btn-sm btn-danger "
                                                                                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                                                                                        data-original-title="{{ __('Reset') }}">
                                                                                                        <span class="btn-inner--icon"><i
                                                                                                                class="ti ti-refresh text-white-off "></i></span>
                                                                                                    </a>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12" id="invoice-container">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <div class="d-flex justify-content-between w-100">
                                                                                <ul class="nav nav-ul nav-pills mb-0 gap-2" id="pills-tab" role="tablist">
                                                                                    <li class="nav-item">
                                                                                        <a class="nav-link active" id="profile-tab3" data-bs-toggle="pill" href="#item" role="tab" aria-controls="pills-item" aria-selected="true">{{__('Sales by Ticket')}}</a>
                                                                                    </li>
                                                                                    {{-- <li class="nav-item">
                                                                                        <a class="nav-link" id="contact-tab4" data-bs-toggle="pill" href="#customer" role="tab" aria-controls="pills-customer" aria-selected="false">{{__('Sales by Customer')}}</a>
                                                                                    </li> --}}
                                                                                </ul>
                                                                            </div>
                                                                        </div>

                                                                        <div class="card-body" id="printableArea">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="tab-content" id="myTabContent2">
                                                                                        <div class="tab-pane fade fade show active" id="item" role="tabpanel" aria-labelledby="profile-tab3">
                                                                                            <div class="table-responsive">
                                                                                                <table class="table pc-dt-simple" id="item-reort" data-preserve-footer="true">
                                                                                                    <thead>
                                                                                                    <tr>
                                                                                                        <th> {{__('#')}}</th>
                                                                                                        <th> {{__('Sales Date')}}</th>
                                                                                                        <th> {{__('User')}}</th>
                                                                                                        <th> {{__('Vender')}}</th>
                                                                                                        <th> {{__('Customer')}}</th>
                                                                                                        <th> {{__('PNR')}}</th>
                                                                                                        <th> {{__('Ticket Number')}}</th>
                                                                                                        <th> {{__('Origin - distination')}}</th>
                                                                                                        <th> {{__('Status')}}</th>
                                                                                                        <th> {{__('Fare')}}</th>
                                                                                                        <th> {{__('Tax')}}</th>
                                                                                                        <th> {{__('Refund')}}</th>
                                                                                                        <th> {{__('Commission')}}</th>
                                                                                                        <th> {{__('Cust Comm')}}</th>
                                                                                                        <th> {{__('Discount')}}</th>
                                                                                                        <th> {{__('Total Amount')}}</th>
                                                                                                        <th> {{__('Paid')}}</th>
                                                                                                        <th> {{__('Balance')}}</th>
                                                                                                    </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @forelse($invoiceItems as $index => $invoiceItem)
                                                                                                                                                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $index + 1 }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['issue_date'] ? \Auth::user()->dateFormat($invoiceItem['issue_date']) : '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['user_name'] ?? '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['vender_name'] ?? '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['customer_name'] ?? '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['pnr'] ?? '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ $invoiceItem['ticket_numbers'] ?? '-' }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                        @php
                                                                                                            // Get origin from invoice level
                                                                                                            $origin = $invoiceItem['origin'] ?? '';
                                                                                                            // Get destination from invoice level first, then fallback to product level
                                                                                                            $destination = $invoiceItem['invoice_destination'] ?? $invoiceItem['product_destination'] ?? '';

                                                                                                            // Format as "Origin - Destination"
                                                                                                            if (!empty($origin) && !empty($destination)) {
                                                                                                                $route = $origin . ' - ' . $destination;
                                                                                                            } elseif (!empty($origin)) {
                                                                                                                $route = $origin;
                                                                                                            } elseif (!empty($destination)) {
                                                                                                                $route = $destination;
                                                                                                            } else {
                                                                                                                $route = '-';
                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                        @endphp
                                                                                                                                                                                                                                                                                                                                                                                                        {{ $route }}
                                                                                                                                                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                        @if(isset($invoiceItem['status']))
                                                                                                                                                                                                                                                                                                                                                                                                            @if ($invoiceItem['status'] == 0)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($invoiceItem['status'] == 1)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($invoiceItem['status'] == 2)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($invoiceItem['status'] == 3)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($invoiceItem['status'] == 4)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($invoiceItem['status'] == 5)
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoiceItem['status']]) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @else
                                                                                                                                                                                                                                                                                                                                                                                                                <span class="status_badge badge bg-secondary p-2 px-3 rounded">{{ $invoiceItem['status'] }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                            @endif
                                                                                                                                                                                                                                                                                                                                                                                                        @else
                                                                                                                                                                                                                                                                                                                                                                                                            <span class="status_badge badge bg-secondary p-2 px-3 rounded">-</span>
                                                                                                                                                                                                                                                                                                                                                                                                        @endif
                                                                                                                                                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_fare'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_tax'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_refund'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_commission'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_cust_commission'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total_discount'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['invoice_total'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['paid'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                    <td>{{ \Auth::user()->priceFormat($invoiceItem['balance'] ?? 0) }}</td>
                                                                                                                                                                                                                                                                                                                                                                                                </tr>
                                                                                                        @empty
                                                                                                            <tr>
                                                                                                                <td colspan="16" class="text-center">{{ __('No Data Found.!') }}</td>
                                                                                                            </tr>

                                                                                                        @endforelse

                                                                                                    </tbody>
                                                                                                    <tfoot>
                                                                                                        <tr>
                                                                                                            <td colspan="9" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_fare'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_tax'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_refund'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_commission'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_cust_commission'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_discount'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_amount2'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_paid'] ?? 0) }}</strong></td>
                                                                                                            <td><strong>{{ \Auth::user()->priceFormat($invoiceTotals['total_balance'] ?? 0) }}</strong></td>
                                                                                                        </tr>
                                                                                                    </tfoot>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="tab-pane fade fade" id="customer" role="tabpanel" aria-labelledby="profile-tab3">
                                                                                            <div class="table-responsive">
                                                                                                <table class="table pc-dt-simple" id="customer-report">
                                                                                                    <thead>
                                                                                                    <tr>
                                                                                                        <th width="33%"> {{__('Customer Name')}}</th>
                                                                                                        <th width="33%"> {{__('Invoice Count')}}</th>
                                                                                                        <th width="33%"> {{__('Sales')}}</th>
                                                                                                        <th class="text-end"> {{__('Sales With Tax')}}</th>
                                                                                                    </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @forelse($invoiceCustomers as $invoiceCustomer)
                                                                                                            <tr>
                                                                                                                <td>{{ $invoiceCustomer['name'] }}</td>
                                                                                                                <td>{{ $invoiceCustomer['invoice_count']}}</td>
                                                                                                                <td>{{ \Auth::user()->priceFormat($invoiceCustomer['price']) }}</td>
                                                                                                                <td>{{ \Auth::user()->priceFormat($invoiceCustomer['price'] + $invoiceCustomer['total_tax']) }}</td>
                                                                                                            </tr>
                                                                                                        @empty
                                                                                                            <tr>
                                                                                                                <td colspan="4" class="text-center">{{ __('No Data Found.!') }}</td>
                                                                                                            </tr>
                                                                                                        @endforelse
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

@endsection
