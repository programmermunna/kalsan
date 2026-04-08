@extends('layouts.admin')
@section('page-title')
    {{ __('Payable Reports') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Account Payable') }}</li>
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
@endpush

@section('action-btn')
<div class="float-end">
    <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip"
        title="{{ __('Print') }}" data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>
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
                                    {{ Form::open(['route' => ['report.payables'], 'method' => 'GET', 'id' => 'report_payable_summary']) }}
                                    <div class="row align-items-center justify-content-end">
                                        <div class="col-xl-10">
                                            <div class="row">
                                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                    <div class="btn-box">
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                    <div class="btn-box">
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                    <div class="btn-box">
                                                        {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                        {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                    <div class="btn-box">
                                                        {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                        {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                                    </div>
                                                </div>
                                                <input type="hidden" name="report" class="report">
                                            </div>
                                        </div>
                                        <div class="col-auto mt-4">
                                            <div class="row">
                                                <div class="col-auto">
                                                    <a href="#" class="btn btn-sm btn-primary"
                                                        onclick="document.getElementById('report_payable_summary').submit(); return false;"
                                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                        data-original-title="{{ __('apply') }}">
                                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                    </a>

                                                    <a href="{{ route('report.payables') }}" class="btn btn-sm btn-danger "
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
                                <ul class="nav nav-pills nav-ul gap-2 mb-0" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="payable-tab1" data-bs-toggle="pill" href="#vendor_balance"
                                            role="tab" aria-controls="pills-vendor-balance"
                                            aria-selected="true">{{ __('Vendor Balance') }}</a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a class="nav-link" id="payable-tab2" data-bs-toggle="pill" href="#payable_summary"
                                            role="tab" aria-controls="pills-payable-summary"
                                            aria-selected="false">{{ __('Payable Summary') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="payable-tab3" data-bs-toggle="pill" href="#payable_details"
                                            role="tab" aria-controls="pills-payable-details"
                                            aria-selected="false">{{ __('Payable Details') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="payable-tab4" data-bs-toggle="pill" href="#aging_summary"
                                            role="tab" aria-controls="pills-aging-summary"
                                            aria-selected="false">{{ __('Aging Summary') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="payable-tab5" data-bs-toggle="pill" href="#aging_details"
                                            role="tab" aria-controls="pills-aging-details"
                                            aria-selected="false">{{ __('Aging Details') }}</a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>

                        <div class="card-body" id="printableArea">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="tab-content" id="myTabContent2">
                                        <div class="tab-pane fade fade show active" id="vendor_balance" role="tabpanel" aria-labelledby="payable-tab1">
                                            <div class="table-responsive">
                                                <table class="table pc-dt-simple" id="report-vendor-balance">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%"> {{ __('#') }}</th>
                                                            <th width="30%"> {{ __('Vendor Name') }}</th>
                                                            <th width="20%"> {{ __('Total Amount (CR)') }}</th>
                                                            <th width="20%"> {{ __('Paid Amount (DR)') }}</th>
                                                            <th class="text-end" width="20%"> {{ __('Balance') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
    $totalCr = 0;
    $totalDr = 0;
    $totalBalance = 0;
                                                        @endphp
                                                        @forelse ($payableVendors as $index => $payableVendor)
                                                            @php
        $totalCr += $payableVendor['cr'];
        $totalDr += $payableVendor['dr'];
        $totalBalance += $payableVendor['balance'];
                                                            @endphp
                                                            <tr>
                                                                <td> {{ $index + 1 }}</td>
                                                                <td> {{ $payableVendor['name'] }}</td>
                                                                <td> {{ \Auth::user()->priceFormat($payableVendor['cr']) }} </td>
                                                                <td> {{ \Auth::user()->priceFormat($payableVendor['dr']) }} </td>
                                                                <td class="text-end"> {{ \Auth::user()->priceFormat($payableVendor['balance']) }} </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center">{{ __('No Data Found.!') }}</td>
                                                            </tr>
                                                        @endforelse
                                                        @if (count($payableVendors) > 0)
                                                            <tr>
                                                                <th></th>
                                                                <th class="text-end">{{ __('Total') }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($totalCr) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($totalDr) }}</th>
                                                                <th class="text-end">{{ \Auth::user()->priceFormat($totalBalance) }}</th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- <div class="tab-pane fade fade show" id="payable_summary" role="tabpanel"
                                            aria-labelledby="payable-tab2">
                                            <div class="table-responsive">
                                                <table class="table pc-dt-simple" id="report-payable-summary">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Vendor Name') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Transaction') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Transaction Type') }}</th>
                                                            <th>{{ __('Total') }}</th>
                                                            <th>{{ __('Balance') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $total = 0;
                                                            $totalAmount = 0;
                                                            function compare($a, $b)
                                                            {
                                                                return strtotime($b['bill_date']) - strtotime($a['bill_date']);
                                                            }
                                                            usort($payableSummaries, 'compare');
                                                        @endphp
                                                        @forelse ($payableSummaries as $payableSummary)
                                                            <tr>
                                                                @php
                                                                    if ($payableSummary['bill']) {
                                                                        $payableBalance = $payableSummary['price'] + $payableSummary['total_tax'];
                                                                    } else {
                                                                        $payableBalance = -$payableSummary['price'];
                                                                    }
                                                                    $pay_price = ($payableSummary['pay_price'] != null) ? $payableSummary['pay_price'] : 0;
                                                                    $balance = $payableBalance - $pay_price;
                                                                    $total += $balance;
                                                                    $totalAmount += $payableBalance;
                                                                @endphp
                                                                <td> {!! $payableSummary['name'] ? $payableSummary['name'] : '<span class="p-2 px-3">-</span>' !!}</td>
                                                                <td> {{ $payableSummary['bill_date'] }}</td>
                                                                @if ($payableSummary['bill'])
                                                                    <td> {{  \Auth::user()->billNumberFormat($payableSummary['bill']) }}
                                                                    </td>
                                                                @else
                                                                    <td>{{ __('Debit Note') }}</td>
                                                                @endif
                                                                <td>
                                                                    @if ($payableSummary['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableSummary['status']]) }}</span>
                                                                    @elseif($payableSummary['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableSummary['status']]) }}</span>
                                                                    @elseif($payableSummary['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableSummary['status']]) }}</span>
                                                                    @elseif($payableSummary['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableSummary['status']]) }}</span>
                                                                    @elseif($payableSummary['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableSummary['status']]) }}</span>
                                                                    @else
                                                                        <span class="p-2 px-3">-</span>
                                                                    @endif
                                                                </td>
                                                                @if ($payableSummary['bill'])
                                                                    <td>{{ __('Bill') }}
                                                                @else
                                                                    <td>{{ __('Debit Note') }}</td>
                                                                @endif
                                                                <td> {{ \Auth::user()->priceFormat($payableBalance) }} </td>

                                                                <td> {{ \Auth::user()->priceFormat($balance) }} </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center">{{ __('No Data Found.!') }}</td>
                                                            </tr>
                                                        @endforelse
                                                        @if ($payableSummaries != [])
                                                            <tr>
                                                                <th>{{ __('Total') }}</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($totalAmount) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($total) }}</th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade fade show" id="payable_details" role="tabpanel"
                                            aria-labelledby="payable-tab3">
                                            <div class="table-responsive">
                                                <table class="table pc-dt-simple" id="report-payable-details">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Vendor Name') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Transaction') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Transaction Type') }}</th>
                                                            <th>{{ __('Item Name') }}</th>
                                                            <th>{{ __('Quantity Ordered') }}</th>
                                                            <th>{{ __('Item Price') }}</th>
                                                            <th>{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $total = 0;
                                                            $totalQuantity = 0;

                                                            function compares($a, $b)
                                                            {
                                                                return strtotime($b['bill_date']) - strtotime($a['bill_date']);
                                                            }
                                                            usort($payableDetails, 'compares');
                                                        @endphp
                                                        @forelse ($payableDetails as $payableDetail)
                                                            <tr>
                                                                @php
                                                                    if ($payableDetail['bill']) {
                                                                        $receivableBalance = $payableDetail['price'];
                                                                    } else {
                                                                        $receivableBalance = -$payableDetail['price'];
                                                                    }
                                                                    if ($payableDetail['bill']) {
                                                                        $quantity = $payableDetail['quantity'];
                                                                    }
                                                                    else {
                                                                        $quantity = 0;
                                                                    }

                                                                    if ($payableDetail['bill']) {
                                                                        $itemTotal = $receivableBalance * $payableDetail['quantity'];
                                                                    } else {
                                                                        $itemTotal = -$payableDetail['price'];
                                                                    }
                                                                    $total += $itemTotal;
                                                                    $totalQuantity += $quantity;
                                                                @endphp
                                                                <td> {!! $payableDetail['name'] ? $payableDetail['name'] : '<span class="p-2 px-3">-</span>' !!}</td>
                                                                <td> {{ $payableDetail['bill_date'] }}</td>
                                                                @if ($payableDetail['bill'])
                                                                    <td> {{  \Auth::user()->billNumberFormat($payableDetail['bill']) }}
                                                                    </td>
                                                                @else
                                                                    <td>{{ __('Debit Note') }}</td>
                                                                @endif
                                                                <td>
                                                                    @if ($payableDetail['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableDetail['status']]) }}</span>
                                                                    @elseif($payableDetail['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableDetail['status']]) }}</span>
                                                                    @elseif($payableDetail['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableDetail['status']]) }}</span>
                                                                    @elseif($payableDetail['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableDetail['status']]) }}</span>
                                                                    @elseif($payableDetail['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$payableDetail['status']]) }}</span>
                                                                    @else
                                                                        <span
                                                                            class="p-2 px-3">-</span>
                                                                    @endif
                                                                </td>
                                                                @if ($payableDetail['bill'])
                                                                    <td>{{ __('Bill') }}</td>
                                                                @else
                                                                    <td>{{ __('Debit Note') }}</td>
                                                                @endif
                                                                <td>{{ $payableDetail['product_name'] }}</td>
                                                                <td> {{ $quantity }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($receivableBalance) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($itemTotal) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="9" class="text-center">{{ __('No Data Found.!') }}</td>
                                                            </tr>
                                                        @endforelse
                                                        @if ($payableDetails != [])
                                                            <tr>
                                                                <th>{{ __('Total') }}</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ $totalQuantity }}</th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($total) }}</th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade fade show" id="aging_summary" role="tabpanel"
                                            aria-labelledby="payable-tab4">
                                            <div class="table-responsive">
                                                <table class="table pc-dt-simple" id="report-payable-details">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Vendor Name') }}</th>
                                                            <th>{{ __('Current') }}</th>
                                                            <th>{{ __('1-15 DAYS') }}</th>
                                                            <th>{{ __('16-30 DAYS') }}</th>
                                                            <th>{{ __('31-45 DAYS') }}</th>
                                                            <th>{{ __('> 45 DAYS') }}</th>
                                                            <th>{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $currentTotal = 0;
                                                            $days15 = 0;
                                                            $days30 = 0;
                                                            $days45 = 0;
                                                            $daysMore45 = 0;
                                                            $total = 0;
                                                        @endphp
                                                        @forelse ($agingSummaries as $key => $agingSummary)
                                                            <tr>
                                                                <td> {{ $key }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['current']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['1_15_days']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['16_30_days']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['31_45_days']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['greater_than_45_days']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($agingSummary['total_due']) }}</td>
                                                            </tr>

                                                            @php
                                                                $currentTotal += $agingSummary['current'];
                                                                $days15 += $agingSummary['1_15_days'];
                                                                $days30 += $agingSummary['16_30_days'];
                                                                $days45 += $agingSummary['31_45_days'];
                                                                $daysMore45 += $agingSummary['greater_than_45_days'];
                                                                $total += $agingSummary['total_due'];

                                                            @endphp
                                                        @empty
                                                            <tr>
                                                                <td colspan="9" class="text-center">{{ __('No Data Found.!') }}</td>
                                                            </tr>
                                                        @endforelse
                                                        @if ($agingSummaries != [])
                                                            <tr>
                                                                <th>{{ __('Total') }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($currentTotal) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days15) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days30) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days45) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($daysMore45) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($total) }}</th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade fade show" id="aging_details" role="tabpanel"
                                            aria-labelledby="payable-tab5">
                                            <div class="table-responsive">
                                                <table class="table pc-dt-simple" id="report-payable-details">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Transaction') }}</th>
                                                            <th>{{ __('Type') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Vendor Name') }}</th>
                                                            <th>{{ __('Age') }}</th>
                                                            <th>{{ __('Amount') }}</th>
                                                            <th>{{ __('Balance Due') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $currentTotal = 0;
                                                            $currentDue = 0;
                                                            $days15Total = 0;
                                                            $days15Due = 0;

                                                            $days30Total = 0;
                                                            $days30Due = 0;

                                                            $days45Total = 0;
                                                            $days45Due = 0;

                                                            $daysMore45Total = 0;
                                                            $daysMore45Due = 0;

                                                            $total = 0;
                                                        @endphp

                                                        @if ($moreThan45 != [])
                                                            <tr>
                                                                <th>{{ __(' > 45 Days') }}</th>
                                                            </tr>
                                                        @endif
                                                        @foreach ($moreThan45 as $value)
                                                            @php
                                                                $daysMore45Total += $value['total_price'];
                                                                $daysMore45Due += $value['balance_due'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $value['due_date'] }}</td>
                                                                <td>{{ \Auth::user()->billNumberFormat($value['bill_id']) }}
                                                                </td>
                                                                <td>{{ __('Bill') }}</td>
                                                                <td>
                                                                    @if ($value['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$value['status']]) }}</span>
                                                                    @elseif($value['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$value['status']]) }}</span>
                                                                    @elseif($value['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$value['status']]) }}</span>
                                                                    @elseif($value['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$value['status']]) }}</span>
                                                                    @elseif($value['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$value['status']]) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $value['name'] }}</td>
                                                                <td> {{ $value['age'] . __(' Days') }} </td>
                                                                <td>{{ \Auth::user()->priceFormat($value['total_price']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($value['balance_due']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($moreThan45 != [])
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($daysMore45Total) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($daysMore45Due) }}</th>
                                                            </tr>
                                                        @endif

                                                        @if ($days31to45 != [])
                                                            <tr>
                                                                <th>{{ __(' 31 to 45 Days') }}</th>
                                                            </tr>
                                                        @endif
                                                        @foreach ($days31to45 as $day31to45)
                                                            @php
                                                                $days45Total += $day31to45['total_price'];
                                                                $days45Due += $day31to45['balance_due'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $day31to45['due_date'] }}</td>
                                                                <td>{{ \Auth::user()->billNumberFormat($day31to45['bill_id']) }}
                                                                </td>
                                                                <td>{{ __('Bill') }}</td>
                                                                <td>
                                                                    @if ($day31to45['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day31to45['status']]) }}</span>
                                                                    @elseif($day31to45['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$day31to45['status']]) }}</span>
                                                                    @elseif($day31to45['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$day31to45['status']]) }}</span>
                                                                    @elseif($day31to45['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$day31to45['status']]) }}</span>
                                                                    @elseif($day31to45['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day31to45['status']]) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $day31to45['name'] }}</td>
                                                                <td> {{ $day31to45['age'] . __(' Days') }} </td>
                                                                <td>{{ \Auth::user()->priceFormat($day31to45['total_price']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($day31to45['balance_due']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($days31to45 != [])
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($days45Total) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days45Due) }}</th>
                                                            </tr>
                                                        @endif

                                                        @if ($days16to30 != [])
                                                            <tr>
                                                                <th>{{ __(' 16 to 30 Days') }}</th>
                                                            </tr>
                                                        @endif
                                                        @foreach ($days16to30 as $day16to30)
                                                            @php
                                                                $days30Total += $day16to30['total_price'];
                                                                $days30Due += $day16to30['balance_due'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $day16to30['due_date'] }}</td>
                                                                <td>{{ \Auth::user()->billNumberFormat($day16to30['bill_id']) }}
                                                                </td>
                                                                <td>{{ __('Bill') }}</td>
                                                                <td>
                                                                    @if ($day16to30['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day16to30['status']]) }}</span>
                                                                    @elseif($day16to30['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$day16to30['status']]) }}</span>
                                                                    @elseif($day16to30['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$day16to30['status']]) }}</span>
                                                                    @elseif($day16to30['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$day16to30['status']]) }}</span>
                                                                    @elseif($day16to30['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day16to30['status']]) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $day16to30['name'] }}</td>
                                                                <td> {{ $day16to30['age'] . __(' Days') }} </td>
                                                                <td>{{ \Auth::user()->priceFormat($day16to30['total_price']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($day16to30['balance_due']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($days16to30 != [])
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($days30Total) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days30Due) }}</th>
                                                            </tr>
                                                        @endif

                                                        @if ($days1to15 != [])
                                                            <tr>
                                                                <th>{{ __(' 1 to 15 Days') }}</th>
                                                            </tr>
                                                        @endif
                                                        @foreach ($days1to15 as $day1to15)
                                                            @php
                                                                $days15Total += $day1to15['total_price'];
                                                                $days15Due += $day1to15['balance_due'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $day1to15['due_date'] }}</td>
                                                                <td>{{ \Auth::user()->billNumberFormat($day1to15['bill_id']) }}
                                                                </td>
                                                                <td>{{ __('Bill') }}</td>
                                                                <td>
                                                                    @if ($day1to15['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day1to15['status']]) }}</span>
                                                                    @elseif($day1to15['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$day1to15['status']]) }}</span>
                                                                    @elseif($day1to15['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$day1to15['status']]) }}</span>
                                                                    @elseif($day1to15['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$day1to15['status']]) }}</span>
                                                                    @elseif($day1to15['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$day1to15['status']]) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $day1to15['name'] }}</td>
                                                                <td> {{ $day1to15['age'] . __(' Days') }} </td>
                                                                <td>{{ \Auth::user()->priceFormat($day1to15['total_price']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($day1to15['balance_due']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($days1to15 != [])
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($days15Total) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($days15Due) }}</th>
                                                            </tr>
                                                        @endif

                                                        @if ($currents != [])
                                                            <tr>
                                                                <th>{{ __('Current') }}</th>
                                                            </tr>
                                                        @endif
                                                        @foreach ($currents as $current)
                                                            @php
                                                                $currentTotal += $current['total_price'];
                                                                $currentDue += $current['balance_due'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $current['due_date'] }}</td>
                                                                <td>{{ \Auth::user()->billNumberFormat($current['bill_id']) }}
                                                                </td>
                                                                <td>{{ __('Bill') }}</td>
                                                                <td>
                                                                    @if ($current['status'] == 0)
                                                                        <span
                                                                            class="status_badge badge bg-secondary p-2 px-3">{{ __(\App\Models\Bill::$statues[$current['status']]) }}</span>
                                                                    @elseif($current['status'] == 1)
                                                                        <span
                                                                            class="status_badge badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$current['status']]) }}</span>
                                                                    @elseif($current['status'] == 2)
                                                                        <span
                                                                            class="status_badge badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$current['status']]) }}</span>
                                                                    @elseif($current['status'] == 3)
                                                                        <span
                                                                            class="status_badge badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$current['status']]) }}</span>
                                                                    @elseif($current['status'] == 4)
                                                                        <span
                                                                            class="status_badge badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$current['status']]) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $current['name'] }}</td>
                                                                <td> - </td>
                                                                <td>{{ \Auth::user()->priceFormat($current['total_price']) }}</td>
                                                                <td>{{ \Auth::user()->priceFormat($current['balance_due']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($currents != [])
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($currentTotal) }}</th>
                                                                <th>{{ \Auth::user()->priceFormat($currentDue) }}</th>
                                                            </tr>
                                                        @endif

                                                        @if ($currents != [] || $days1to15 != [] || $days16to30 != [] || $days31to45 != [] || $moreThan45 != [])
                                                            <tr>
                                                                <th>{{ __('Total') }}</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>{{ \Auth::user()->priceFormat($currentTotal + $days15Total + $days30Total + $days45Total + $daysMore45Total) }}
                                                                </th>
                                                                <th>{{ \Auth::user()->priceFormat($currentDue + $days15Due + $days30Due + $days45Due + $daysMore45Due) }}
                                                                </th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
