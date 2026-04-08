@extends('layouts.admin')
@section('page-title')
    {{ __('Balance Sheet') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Balance Sheet') }}</li>
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
        html2pdf().set(opt).from(element).save();
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
@endpush

@section('action-btn')
    <div class="float-end">
        <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip"
            title="{{ __('Print') }}" data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>
    </div>
    <div class="float-end me-2">
        {{ Form::open(['route' => ['balance.sheet.export']]) }}
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <button type="submit" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            data-original-title="{{ __('Export') }}"><i class="ti ti-file-export"></i></button>
        {{ Form::close() }}
    </div>

    <div class="float-end me-2" id="filter">
        <button id="filter" class="btn btn-sm btn-primary"  data-bs-toggle="tooltip" title="{{ __('Filter') }}" data-original-title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
    </div>

    <div class="float-end me-2">
        <a href="{{ route('report.balance.sheet', 'horizontal') }}" class="btn btn-sm bg-light-blue-subtitle" data-bs-toggle="tooltip"
            title="{{ __('Horizontal View') }}" data-original-title="{{ __('Horizontal View') }}"><i
                class="ti ti-separator-vertical"></i></a>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card" id="show_filter" style="display:none;">
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.balance.sheet'], 'method' => 'GET', 'id' => 'report_balancesheet']) }}
                            <div class="col-xl-12">

                                <div class="row justify-content-between">
                                    <div class="col-xl-12">
                                        <div class="row justify-content-end align-items-center">
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

                                            <div class="col-auto mt-4">
                                                <a href="#" class="btn btn-sm btn-primary"
                                                    onclick="document.getElementById('report_balancesheet').submit(); return false;"
                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                    data-original-title="{{ __('apply') }}">
                                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                </a>

                                                <a href="{{ route('report.balance.sheet') }}"
                                                    class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                                    title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
                                                    <span class="btn-inner--icon"><i
                                                            class="ti ti-refresh text-white-off "></i></span>
                                                </a>

                                            </div>
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

        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ 'Balance Sheet of ' . Auth::user()->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                        </h5>
                    </div>
                    <div class="card-body {{ $collapseview == 'expand' ? 'collapse-view' : '' }} overflow-auto">
                        <div class="account-table-inner">
                        <div
                            class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2">
                            <h6 class="mb-0">{{ __('Account') }}</h6>
                            <h6 class="mb-0 text-center">{{ __('Account Code') }}</h6>
                            <h6 class="mb-0 text-end">{{ __('Total') }}</h6>
                        </div>
                        @php
                            $totalAmount = 0;
                            $show = '';
                        @endphp
                        @foreach ($totalAccounts as $type => $accounts)
                                <div class="account-main-inner py-2">
                                    @if (($type == 'Liabilities' || $type == 'Equity') && $show == '')
                                        <p class="fw-bold mb-3"> {{ __('Liabilities & Equity') }}</p>
                                        @php $show = true; @endphp
                                    @endif
                                    <p class="fw-bold ps-2 mb-2">{{ $type }}</p>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach ($accounts as $account)
                                        <div class="border-bottom py-2">
                                            <p class="fw-bold ps-md-4 ps-3 mb-2">
                                                {{ (isset($account['subType']) && $account['subType'] == true) ? $account['subType'] : '' }}</p>
                                            @foreach ($account['account'] as $records)
                                                @if ($collapseview == 'collapse')
                                                    @foreach ($records as $key => $record)
                                                        @php
                                                        if($type == 'Assets'){
                                                            $record['netAmount'] = -$record['netAmount'];
                                                        }
                                                        @endphp
                                                        @if ($record['account'] == 'parentTotal')
                                                            <div
                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                <div class="mb-2 account-arrow">
                                                                    <div class="">
                                                                        <a
                                                                            href="{{ route('report.balance.sheet', ['vertical', 'expand']) }}"><i
                                                                                class="ti ti-chevron-down account-icon"></i></a>
                                                                    </div>
                                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                       class="text-primary">{{ str_replace('Total ', '', $record['account_name']) }}</a>
                                                                </div>
                                                                <p class="mb-2 ms-3 text-center">
                                                                    {{ $record['account_code'] }}
                                                                </p>
                                                                <p class="text-primary mb-2 float-end text-end">
                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                </p>
                                                            </div>
                                                        @endif

                                                        @if (
                                                            !preg_match('/\btotal\b/i', $record['account_name']) &&
                                                                $record['account'] == '' &&
                                                                $record['account'] != 'subAccount')
                                                            <div
                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                <p class="mb-2 ms-3">
                                                                @if($record['account_name'] == 'Current Year Earnings')
                                                                    <a
                                                                        href="{{ route('report.profit.loss') }}"
                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                @endif
                                                                </p>
                                                                <p class="mb-2 text-center">{{ $record['account_code'] }}
                                                                </p>
                                                                <p class="text-primary mb-2 float-end text-end">
                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach ($records as $key => $record)
                                                        @php
                                                        if($type == 'Assets'){
                                                            $record['netAmount'] = -$record['netAmount'];
                                                        }
                                                        @endphp
                                                        @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                            <div
                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                @if ($record['account'] == 'parent')
                                                                    <div class="mb-2 account-arrow">
                                                                        <div class="">
                                                                            <a
                                                                                href="{{ route('report.balance.sheet', ['vertical', 'collapse']) }}"><i
                                                                                    class="ti ti-chevron-down account-icon"></i></a>
                                                                        </div>
                                                                        <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                           class="{{ $record['account'] == 'parent' ? 'text-primary' : 'text-dark' }} fw-bold">{{ $record['account_name'] }}</a>
                                                                    </div>
                                                                @else
                                                                    <p class="mb-2"><a href="#"
                                                                                       class="text-dark fw-bold">{{ $record['account_name'] }}</a>
                                                                    </p>
                                                                @endif
                                                                <p class="mb-2 ms-3 text-center">
                                                                    {{ $record['account_code'] }}
                                                                </p>
                                                                <p class="text-dark fw-bold mb-2 float-end text-end">
                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        @if (
                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                $record['account'] == 'subAccount')
                                                            <div
                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                <p class="mb-2 ms-3">
                                                                @if($record['account_name'] == 'Current Year Earnings')                                           
                                                                    <a
                                                                        href="{{ route('report.profit.loss') }}"
                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                @else    
                                                                    <a
                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                        class="text-primary">{{ $record['account_name'] }}</a> 
                                                                @endif
                                                                </p>
                                                                <p class="mb-2 text-center">{{ $record['account_code'] }}
                                                                </p>
                                                                <p class="text-primary mb-2 float-end text-end">
                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            @if($record['account_name'] != 'Current Year Earnings')
                                                <div
                                                    class="account-inner d-flex align-items-center justify-content-between ps-4">
                                                    <p class="fw-bold mb-2">
                                                        {{ $record['account_name'] ? $record['account_name'] : '' }}
                                                    </p>
                                                    <p class="fw-bold mb-2 text-end">
                                                        {{ $record['netAmount'] ? \Auth::user()->priceFormat($record['netAmount']) : \Auth::user()->priceFormat(0) }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        @php
                                            $total += $record['netAmount'] ? $record['netAmount'] : 0;
                                        @endphp
                                    @endforeach
                                    <div
                                        class="account-title d-flex align-items-center justify-content-between border-bottom py-2 px-2 pe-0">
                                        <h6 class="fw-bold mb-0">{{ 'Total for ' . $type }}</h6>
                                        <h6 class="fw-bold mb-0 text-end">{{ \Auth::user()->priceFormat($total) }}</h6>
                                    </div>
                                    @php
                                        if ($type != 'Assets') {
                                            $totalAmount += $total;
                                        }
                                    @endphp
                                </div>
                        @endforeach

                        @foreach ($totalAccounts as $type => $accounts)
                            @php
                                if ($type == 'Assets') {
                                    continue;
                                }
                            @endphp

                                <div
                                    class="account-title d-flex align-items-center justify-content-between border-bottom py-2 px-0">
                                    <h6 class="fw-bold mb-0">{{ 'Total for Liabilities & Equity' }}</h6>
                                    <h6 class="fw-bold mb-0 text-end">{{ \Auth::user()->priceFormat($totalAmount) }}</h6>
                                </div>
                            @php
                                if ($type == 'Liabilities' || $type == 'Equity') {
                                    break;
                                }
                            @endphp
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-page')
    <script>
        $(document).on('click', '#collapse', function() {
            view = "collapse";
            $.ajax({
                url: '{{ route("report.balance.sheet", "vertical") }}',
                type: 'GET',
                data: {
                    "view": view,                    
                },
                success: function(data) {
                    return false;

                    $('#employee_id').empty();
                    $('#employee_id').append('<option value="">{{ __("Select Employee") }}</option>');
                    $('#employee_id').append('<option value="0"> {{ __("All Employee") }} </option>');

                    $.each(data, function(key, value) {
                        $('#employee_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        });
    </script>
@endpush
