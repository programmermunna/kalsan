@extends('layouts.admin')
@section('page-title')
    {{ __('Balance Sheet') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Balance Sheet') }}</li>
@endsection
@push('script-page')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

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
        <button id="filter" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Filter') }}" data-original-title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
    </div>

    <div class="float-end me-2">
        <a href="{{ route('report.balance.sheet', 'vertical') }}" class="btn btn-sm bg-light-blue-subtitle" data-bs-toggle="tooltip"
            title="{{ __('Vertical View') }}" data-original-title="{{ __('Vertical View') }}"><i
                class="ti ti-separator-horizontal"></i></a>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card" id="show_filter" style="display:none;">
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.balance.sheet'], 'method' => 'GET', 'id' => 'report_bill_summary']) }}
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
                                            <input type="hidden" name="view" value="horizontal">

                                            <div class="col-auto mt-4">
                                                <a href="#" class="btn btn-sm btn-primary"
                                                    onclick="document.getElementById('report_bill_summary').submit(); return false;"
                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                    data-original-title="{{ __('apply') }}">
                                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                </a>

                                                <a href="{{ route('report.balance.sheet', 'horizontal') }}"
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
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ 'Balance Sheet of ' . Auth::user()->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                        </h5>
                    </div>
                    <div class="card-body {{ $collapseview == 'expand' ? 'collapse-view' : '' }}">
                        @php
                            $totalAmount = 0;
                        @endphp

                        <div class="row row-gap-4">
                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Liabilities & Equity') }}</h5>
                                </div>
                                <div class="border-start border-end overflow-auto">
                                <div class="account-table-inner">
                                    @foreach ($totalAccounts as $type => $accounts)
                                        @if ($type != 'Assets')
                                            <div class="account-main-inner py-2">
                                                <p class="fw-bold ps-2 mb-2">{{ $type }}</p>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach ($accounts as $account)
                                                <div class="border-bottom py-2">
                                                    <p class="fw-bold ps-4 mb-2">
                                                        {{ (isset($account['subType']) && $account['subType'] == true) ? $account['subType'] : '' }}
                                                    </p>
                                                    @foreach ($account['account'] as $records)
                                                            @if ($collapseview == 'collapse')
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                            <div class="mb-2 account-arrow">
                                                                                <div class="">
                                                                                    <a
                                                                                        href="{{ route('report.balance.sheet', ['horizontal', 'expand']) }}"><i
                                                                                            class="ti ti-chevron-down account-icon"></i></a>
                                                                                </div>
                                                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                   class="text-primary">{{ str_replace('Total ', '', $record['account_name']) }}</a>
                                                                            </div>
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-primary mb-2 float-end text-end me-3">
                                                                                {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] != 'subAccount')
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
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                            @if ($record['account'] == 'parent')
                                                                                <div class="mb-2 account-arrow">
                                                                                    <div class="">
                                                                                        <a
                                                                                            href="{{ route('report.balance.sheet', ['horizontal', 'collapse']) }}"><i
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
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-dark fw-bold mb-2 float-end text-end me-3">
                                                                                {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] == 'subAccount' || $record['account_name'] == 'Current Year Earnings')
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
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
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
                                                                <p class="fw-bold mb-2 text-end me-3">
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
                                                    class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                                    <h6 class="fw-bold mb-0">{{ 'Total for ' . $type }}</h6>
                                                    <h6 class="fw-bold mb-0 text-end me-3">
                                                        {{ \Auth::user()->priceFormat($total) }}</h6>
                                                </div>
                                                @php
                                                    if ($type != 'Assets') {
                                                        $totalAmount += $total;
                                                    }
                                                @endphp
                                            </div>
                                        @endif
                                    @endforeach
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom py-2 px-0">
                                        <h6 class="fw-bold mb-0 ms-2">{{ 'Total for Liabilities & Equity' }}</h6>
                                        <h6 class="fw-bold mb-0 text-end me-3">
                                            {{ \Auth::user()->priceFormat($totalAmount) }}</h6>
                                    </div>
                                </div>
                                </div>

                            </div>

                            @php
                                $total = 0;
                                $totalAmounts = 0;
                            @endphp

                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Assets') }}</h5>
                                </div>
                                <div class="border-start border-end overflow-auto">
                                <div class="account-table-inner">
                                    @foreach ($totalAccounts as $type => $accounts)
                                        @if ($type == 'Assets')
                                            <div class="account-main-inner py-2">
                                                <p class="fw-bold ps-2 mb-2">{{ $type }}</p>
                                                @foreach ($accounts as $account)
                                                    <div class="border-bottom py-2">
                                                        <p class="fw-bold ps-4 mb-2">
                                                            {{ $account['subType'] == true ? $account['subType'] : '' }}
                                                        </p>
                                                        @foreach ($account['account'] as $records)
                                                            @if ($collapseview == 'collapse')
                                                                @foreach ($records as $key => $record)
                                                                    @php
                                                                        $record['netAmount'] = -$record['netAmount'];
                                                                    @endphp
                                                                    @if ($record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                            <div class="mb-2 account-arrow">
                                                                                <div class="">
                                                                                    <a
                                                                                        href="{{ route('report.balance.sheet', ['horizontal', 'expand']) }}"><i
                                                                                            class="ti ti-chevron-down account-icon"></i></a>
                                                                                </div>
                                                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                   class="text-primary">{{ str_replace('Total ', '', $record['account_name']) }}</a>
                                                                            </div>
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-primary mb-2 float-end text-end me-3">
                                                                                {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] != 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                @foreach ($records as $key => $record)
                                                                    @php
                                                                        $record['netAmount'] = -$record['netAmount'];
                                                                    @endphp
                                                                    @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                            @if ($record['account'] == 'parent')
                                                                                <div class="mb-2 account-arrow">
                                                                                    <div class="">
                                                                                        <a
                                                                                            href="{{ route('report.balance.sheet', ['horizontal', 'collapse']) }}"><i
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
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-dark fw-bold mb-2 float-end text-end me-3">
                                                                                {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] == 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-md-5 ps-3">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ \Auth::user()->priceFormat($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach

                                                        <div
                                                            class="account-inner d-flex align-items-center justify-content-between ps-4">
                                                            <p class="fw-bold mb-2">
                                                                {{ $record['account_name'] ? $record['account_name'] : 0 }}
                                                            </p>
                                                            <p class="fw-bold mb-2 text-end me-3">
                                                                {{ $record['netAmount'] ? \Auth::user()->priceFormat($record['netAmount']) : \Auth::user()->priceFormat(0) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $total += $record['netAmount'] ? $record['netAmount'] : 0;
                                                    @endphp
                                                @endforeach
                                                @php
                                                    if ($type == 'Assets') {
                                                        $totalAmounts += $total;
                                                    }
                                                @endphp

                                            </div>
                                        @endif
                                    @endforeach
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom py-2 px-0">
                                        <h6 class="fw-bold mb-0 ms-2">{{ 'Total for Assets' }}</h6>
                                        <h6 class="fw-bold mb-0 text-end me-3">
                                            {{ \Auth::user()->priceFormat($totalAmounts) }}</h6>
                                    </div>
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
