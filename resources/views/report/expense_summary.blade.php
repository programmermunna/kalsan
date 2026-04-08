@extends('layouts.admin')
@section('page-title')
    {{__('Expense Summary')}}
@endsection

@push('css-page')
<style>
    .apexcharts-yaxis
    {
        transform: translate(20px, 0px) !important;
    }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Expense Summary')}}</li>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush
@php
    if(isset($_GET['category']) && $_GET['period'] == 'yearly')
    {
        $chartArr = [];

foreach ($chartExpenseArr as $innerArray) {
    foreach ($innerArray as $value) {
        $chartArr[] = $value;
    }
}
    }
    else {
        $chartArr = $chartExpenseArr[0];
    }
@endphp

@push('script-page')
    <script>
        (function () {
            var chartBarOptions = {
                series: [
                    {
                        name: '{{ __("Expense") }}',
                        data:  {!! json_encode($chartArr) !!},

                    },
                ],

                chart: {
                    height: 300,
                    type: 'area',
                    // type: 'line',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($monthList) !!},
                    title: {
                        text: '{{ __("Months") }}'
                    }
                },
                colors: ['#6fd944', '#6fd944'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#ffa21d', '#FF3A6E'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: '{{ __("Expense") }}',
                        offsetX: 40,
                        offsetY: -15,
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();
    </script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{$currentYear}}';
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();

        }
    </script>
@endpush


    @section('action-btn')
        <div class="float-end">
            <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}" data-original-title="{{__('Download')}}">
                <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
            </a>

        </div>
    @endsection


@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                    {{ Form::open(array('route' => array('report.expense.summary'),'method' => 'GET','id'=>'report_expense_summary')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    @if(isset($_GET['period']) && $_GET['period'] == 'yearly')
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">

                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('period', __('Income Period'),['class'=>'form-label']) }}
                                            {{ Form::select('period', $periods,isset($_GET['period'])?$_GET['period']:'', array('class' => 'form-control select period','required'=>'required')) }}
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('period', __('Income Period'),['class'=>'form-label']) }}
                                            {{ Form::select('period', $periods,isset($_GET['period'])?$_GET['period']:'', array('class' => 'form-control select period','required'=>'required')) }}
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('year', __('Year'),['class'=>'form-label'])}}
                                            {{ Form::select('year',$yearList,isset($_GET['year'])?$_GET['year']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('category', __('Category'),['class'=>'form-label'])}}
                                            {{ Form::select('category',$category,isset($_GET['category'])?$_GET['category']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('vender', __('Vendor'),['class'=>'form-label'])}}
                                            {{ Form::select('vender',$vender,isset($_GET['vender'])?$_GET['vender']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary me-1" onclick="document.getElementById('report_expense_summary').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('report.expense.summary')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
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

    <div id="printableArea">
        <div class="row">
            <div class="col mb-4">
                <input type="hidden"
                    value="{{ $filter['category'] . ' ' . __('Expense Summary') . ' ' . 'Report of' . ' ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}"
                    id="filename">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.09766 0.761719V20.668C3.09766 21.089 3.43835 21.4297 3.85938 21.4297H17.5703C17.9913 21.4297 18.332 21.089 18.332 20.668V0.761719C18.332 0.340691 17.9913 0 17.5703 0H3.85938C3.43835 0 3.09766 0.340691 3.09766 0.761719ZM15.2852 17.5703H12.2383C11.8173 17.5703 11.4766 17.2296 11.4766 16.8086C11.4766 16.3876 11.8173 16.0469 12.2383 16.0469H15.2852C15.7062 16.0469 16.0469 16.3876 16.0469 16.8086C16.0469 17.2296 15.7062 17.5703 15.2852 17.5703ZM6.14453 3.85938H10.7148C11.1359 3.85938 11.4766 4.20007 11.4766 4.62109C11.4766 5.04212 11.1359 5.38281 10.7148 5.38281H6.14453C5.7235 5.38281 5.38281 5.04212 5.38281 4.62109C5.38281 4.20007 5.7235 3.85938 6.14453 3.85938ZM6.14453 6.90625H15.2852C15.7062 6.90625 16.0469 7.24694 16.0469 7.66797C16.0469 8.089 15.7062 8.42969 15.2852 8.42969H6.14453C5.7235 8.42969 5.38281 8.089 5.38281 7.66797C5.38281 7.24694 5.7235 6.90625 6.14453 6.90625ZM6.14453 9.95312H15.2852C15.7062 9.95312 16.0469 10.2938 16.0469 10.7148C16.0469 11.1359 15.7062 11.4766 15.2852 11.4766H6.14453C5.7235 11.4766 5.38281 11.1359 5.38281 10.7148C5.38281 10.2938 5.7235 9.95312 6.14453 9.95312ZM6.14453 13H15.2852C15.7062 13 16.0469 13.3407 16.0469 13.7617C16.0469 14.1827 15.7062 14.5234 15.2852 14.5234H6.14453C5.7235 14.5234 5.38281 14.1827 5.38281 13.7617C5.38281 13.3407 5.7235 13 6.14453 13Z"
                                    fill="white" />
                                <path
                                    d="M8.42969 26H22.1406C22.5617 26 22.9023 25.6593 22.9023 25.2383V5.38281C22.9023 4.96179 22.5617 4.62109 22.1406 4.62109H19.8555V20.668C19.8555 21.9281 18.8304 22.9531 17.5703 22.9531H7.66797V25.2383C7.66797 25.6593 8.00866 26 8.42969 26Z"
                                    fill="white" />
                            </svg>
                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{ __('Report') }} :</h5>
                            <p class="text-muted mb-0">{{ __('Expense Summary') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @if ($filter['category'] != __('All'))
                <div class="col mb-4">
                    <div class="card report-card h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="report-icon">
                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9609 2.53288L19.9842 2.55619L20.4437 3.01581L20.4671 3.03912C20.8763 3.44829 21.2178 3.78981 21.474 4.09163C21.7413 4.4064 21.9631 4.73103 22.0898 5.12103C22.2842 5.71937 22.2842 6.3639 22.0898 6.96225C21.9631 7.35224 21.7413 7.67686 21.474 7.99164C21.2178 8.29345 20.8763 8.63498 20.4671 9.04416L20.4437 9.06746L19.9842 9.52708L19.9609 9.55039C19.5518 9.95961 19.2101 10.3011 18.9083 10.5573C18.5935 10.8246 18.269 11.0463 17.879 11.1731C17.2806 11.3675 16.636 11.3675 16.0377 11.1731C15.6477 11.0463 15.3232 10.8246 15.0083 10.5573C14.7065 10.3011 14.3649 9.95953 13.9558 9.55039L13.9325 9.52708L13.4729 9.06746L13.4495 9.04416C13.0403 8.63498 12.6989 8.29345 12.4427 7.99164C12.1754 7.67686 11.9536 7.35224 11.8269 6.96225C11.6325 6.3639 11.6325 5.71937 11.8269 5.12103C11.9536 4.73103 12.1754 4.4064 12.4427 4.09163C12.6989 3.78981 13.0403 3.44829 13.4495 3.03912L13.4729 3.01581L13.9325 2.55619L13.9558 2.53288C14.3649 2.12368 14.7065 1.78215 15.0083 1.52593C15.3232 1.25869 15.6477 1.0369 16.0377 0.910185C16.636 0.71577 17.2806 0.71577 17.879 0.910185C18.269 1.0369 18.5935 1.25869 18.9083 1.52593C19.2101 1.78215 19.5517 2.12368 19.9609 2.53288ZM4.6837 1.43742H4.71667H5.36667H5.39964C5.97831 1.43741 6.46131 1.4374 6.85589 1.46964C7.26744 1.50327 7.65381 1.57597 8.01919 1.76213C8.57975 2.04776 9.03548 2.50351 9.32116 3.06408C9.50728 3.42944 9.57997 3.81583 9.61366 4.22736C9.64583 4.62195 9.64583 5.10493 9.64583 5.68358V5.71659V6.36659V6.39955C9.64583 6.97821 9.64583 7.46124 9.61366 7.85581C9.57997 8.26736 9.50728 8.65373 9.32116 9.01911C9.03548 9.57967 8.57975 10.0355 8.01919 10.321C7.65381 10.5073 7.26744 10.58 6.85589 10.6135C6.46131 10.6458 5.97831 10.6458 5.39963 10.6457H5.36667H4.71667H4.6837C4.10504 10.6458 3.62203 10.6458 3.22744 10.6135C2.8159 10.58 2.42952 10.5073 2.06416 10.321C1.50359 10.0355 1.04784 9.57967 0.762208 9.01911C0.576048 8.65373 0.503346 8.26736 0.469719 7.85581C0.437479 7.46124 0.43749 6.97825 0.4375 6.3996V6.36659V5.71659V5.68362C0.43749 5.10498 0.437479 4.62195 0.469719 4.22736C0.503346 3.81583 0.576048 3.42944 0.762208 3.06408C1.04784 2.50351 1.50359 2.04776 2.06416 1.76213C2.42952 1.57597 2.8159 1.50327 3.22744 1.46964C3.62202 1.4374 4.10503 1.43741 4.6837 1.43742ZM4.71667 13.354H4.6837C4.10503 13.354 3.62202 13.354 3.22744 13.3863C2.8159 13.4199 2.42952 13.4926 2.06416 13.6788C1.50359 13.9644 1.04784 14.4202 0.762208 14.9808C0.576048 15.3461 0.503346 15.7325 0.469719 16.1441C0.437479 16.5386 0.43749 17.0216 0.4375 17.6003V17.6332V18.2832V18.3163C0.43749 18.8949 0.437479 19.3779 0.469719 19.7725C0.503346 20.184 0.576048 20.5705 0.762208 20.9358C1.04784 21.4964 1.50359 21.9521 2.06416 22.2377C2.42952 22.4239 2.8159 22.4966 3.22744 22.5302C3.62201 22.5625 4.10498 22.5625 4.68361 22.5624H4.71667H5.36667H5.39963C5.97826 22.5625 6.46133 22.5625 6.85589 22.5302C7.26744 22.4966 7.65381 22.4239 8.01919 22.2377C8.57975 21.9521 9.03548 21.4964 9.32116 20.9358C9.50728 20.5705 9.57997 20.184 9.61366 19.7725C9.64583 19.3779 9.64583 18.8949 9.64583 18.3163V18.2832V17.6332V17.6003C9.64583 17.0216 9.64583 16.5386 9.61366 16.1441C9.57997 15.7325 9.50728 15.3461 9.32116 14.9808C9.03548 14.4202 8.57975 13.9644 8.01919 13.6788C7.65381 13.4926 7.26744 13.4199 6.85589 13.3863C6.46131 13.354 5.97831 13.354 5.39964 13.354H5.36667H4.71667ZM16.6004 13.354H16.6333H17.2833H17.3163C17.895 13.354 18.3779 13.354 18.7726 13.3863C19.1842 13.4199 19.5705 13.4926 19.9359 13.6788C20.4964 13.9644 20.9522 14.4202 21.2378 14.9808C21.4239 15.3461 21.4966 15.7325 21.5303 16.1441C21.5625 16.5386 21.5625 17.0217 21.5625 17.6003V17.6332V18.2832V18.3163C21.5625 18.8949 21.5625 19.3779 21.5303 19.7725C21.4966 20.184 21.4239 20.5705 21.2378 20.9358C20.9522 21.4964 20.4964 21.9521 19.9359 22.2377C19.5705 22.4239 19.1842 22.4966 18.7726 22.5302C18.378 22.5625 17.895 22.5625 17.3164 22.5624H17.2833H16.6333H16.6004C16.0218 22.5625 15.5386 22.5625 15.1441 22.5302C14.7325 22.4966 14.3462 22.4239 13.9808 22.2377C13.4203 21.9521 12.9645 21.4964 12.6788 20.9358C12.4927 20.5705 12.42 20.184 12.3863 19.7725C12.3542 19.3779 12.3542 18.8949 12.3542 18.3163V18.2832V17.6332V17.6003C12.3542 17.0217 12.3542 16.5386 12.3863 16.1441C12.42 15.7325 12.4927 15.3461 12.6788 14.9808C12.9645 14.4202 13.4203 13.9644 13.9808 13.6788C14.3462 13.4926 14.7325 13.4199 15.1441 13.3863C15.5387 13.354 16.0217 13.354 16.6004 13.354Z" fill="white"/>
                                    </svg>

                            </div>
                            <div class="report-info flex-1">
                                <h5 class="mb-1">{{ __('Category') }} :</h5>
                                <p class="text-muted mb-0">{{ $filter['category'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($filter['vender'] != __('All'))
                <div class="col mb-4">
                    <div class="card report-card h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="report-icon">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_4436_2178)">
                                    <path d="M1.625 23.5625C1.625 24.9031 2.72186 26 4.0625 26H21.9375C23.2781 26 24.375 24.9031 24.375 23.5625V21.9375H1.625V23.5625Z" fill="white"/>
                                    <path d="M24.3747 16.25C24.3738 13.4478 24.3729 10.6456 24.3721 7.84343C25.2984 7.52444 25.8278 6.49633 25.8668 5.51741C25.9057 4.5385 25.5552 3.59089 25.21 2.67402C24.8453 1.70529 24.3947 0.641933 23.449 0.221156C22.9487 -0.00143684 22.3823 -0.00500371 21.8347 -0.00478433C15.8962 -0.00242808 9.95766 -6.37067e-05 4.01912 0.00229254C3.45605 0.00251192 2.86695 0.00892253 2.36993 0.273546C1.66014 0.651456 1.30852 1.4556 1.00771 2.20134C0.575011 3.27401 0.127161 4.39389 0.124114 5.56852C0.121644 6.52079 0.765574 7.44098 1.62624 7.84432C1.62587 10.648 1.62548 13.4517 1.62509 16.2555C0.495418 16.1311 -0.0472588 17.4106 -0.0424488 18.335C-0.0390932 18.979 0.164332 19.6795 0.703678 20.0315C1.12337 20.3054 1.65652 20.3143 2.15768 20.3143C9.44942 20.3132 16.7412 20.3122 24.0329 20.3111C24.4165 20.3111 24.8167 20.3073 25.1584 20.1329C25.7192 19.8468 25.9739 19.1754 26.0273 18.5481C26.1107 17.5692 25.5994 16.152 24.3747 16.25ZM3.24979 8.09643C3.68179 8.0352 4.13698 7.85369 4.49234 7.60131C4.71411 7.44381 5.03507 7.44381 5.25882 7.60211C6.22605 8.28607 7.58444 8.28686 8.55484 7.60131C8.77661 7.44381 9.09797 7.44381 9.32132 7.60211C10.2885 8.28607 11.6469 8.28686 12.6173 7.60131C12.8387 7.44381 13.1601 7.44381 13.3838 7.60211C14.3502 8.28607 15.7102 8.28686 16.6798 7.60131C16.8996 7.44461 17.2218 7.44341 17.4463 7.60211C18.4128 8.28607 19.7727 8.28686 20.7423 7.60131C20.9621 7.44461 21.2843 7.44341 21.5088 7.60211C21.8698 7.85766 22.3137 8.02911 22.7498 8.09643V16.25H17.5964C17.0913 14.8243 15.9425 13.7276 14.5294 13.2573C15.0787 12.81 15.4373 12.137 15.4373 11.375C15.4373 10.0309 14.3439 8.93749 12.9998 8.93749C11.6557 8.93749 10.5623 10.0309 10.5623 11.375C10.5623 12.137 10.9209 12.81 11.4702 13.2573C10.0573 13.7276 8.90852 14.8243 8.40354 16.25H3.24978C3.24978 16.25 3.24979 8.09643 3.24979 8.09643Z" fill="white"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_4436_2178">
                                    <rect width="26" height="26" fill="white"/>
                                    </clipPath>
                                    </defs>
                                    </svg>

                            </div>
                            <div class="report-info flex-1">
                                <h5 class="mb-1">{{ __('Vendor') }} :</h5>
                                <p class="text-muted mb-0">{{ $filter['vender'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.6667 5.22996V2.94866C22.3111 2.57293 22.75 1.8819 22.75 1.08332V0.541684C22.75 0.242277 22.5077 0 22.2083 0H3.79168C3.49228 0 3.25 0.242277 3.25 0.541684V1.08337C3.25 1.8819 3.68885 2.57293 4.33332 2.94871V5.22996C4.33332 7.37653 5.24845 9.43104 6.84384 10.8667L9.21416 13L6.84384 15.1333C5.24845 16.569 4.33332 18.6235 4.33332 20.77V23.0513C3.68885 23.4271 3.25 24.1181 3.25 24.9167V25.4584C3.25 25.7577 3.49228 26 3.79168 26H22.2084C22.5078 26 22.7501 25.7577 22.7501 25.4583V24.9166C22.7501 24.1181 22.3112 23.4271 21.6667 23.0513V20.77C21.6667 18.6235 20.7516 16.569 19.1562 15.1333L16.7858 13L19.1562 10.8667C20.7516 9.43104 21.6667 7.37648 21.6667 5.22996ZM17.7068 9.25646L14.442 12.1949C14.2135 12.4002 14.0833 12.6927 14.0833 13C14.0833 13.3073 14.2135 13.5999 14.442 13.8051L17.7068 16.7435C18.8462 17.7692 19.5 19.2371 19.5 20.77V22.75H18.1456L13.4332 16.4669C13.229 16.1939 12.7709 16.1939 12.5667 16.4669L7.85444 22.75H6.5V20.77C6.5 19.2371 7.15381 17.7692 8.29324 16.7435L11.5581 13.805C11.7866 13.5998 11.9167 13.3073 11.9167 12.9999C11.9167 12.6926 11.7866 12.4001 11.5581 12.1949L8.29324 9.25641C7.15381 8.23078 6.5 6.76289 6.5 5.22996V3.25H19.5V5.22996C19.5 6.76289 18.8462 8.23078 17.7068 9.25646Z" fill="white"/>
                                <path d="M16.7337 7.58331H9.26621C9.05197 7.58331 8.85783 7.70976 8.77109 7.90547C8.68436 8.10174 8.72082 8.33026 8.86524 8.48895L12.6368 11.9685C12.74 12.0622 12.8701 12.1087 13.0002 12.1087C13.1303 12.1087 13.2605 12.0621 13.3636 11.9685L17.1346 8.48895C17.279 8.33026 17.3155 8.10174 17.2288 7.90547C17.1421 7.70976 16.9479 7.58331 16.7337 7.58331Z" fill="white"/>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{ __('Duration') }} :</h5>
                            @if (isset($_GET['period']) && $_GET['period'] == 'yearly')
                                <p class="text-muted mb-0">
                                    {{ array_key_last($yearList) . ' to ' . array_key_first($yearList) }}</p>
                            @else
                                <p class="text-muted mb-0">{{ $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
            <div class="col-12" id="chart-container">
                <div class="card">
                    <div class="card-body">
                        <div class="scrollbar-inner">
                        <div id="chart-sales" data-color="primary" data-height="300" ></div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        {{-- quartly --}}
                        @if(isset($_GET['category']) && $_GET['period'] == 'quarterly')
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Payment :')}}</span></td>
                                </tr>
                                @foreach($expenseArr as $i=>$expense)
                                    <tr>
                                        <td>{{$expense['category']}}</td>
                                        @foreach($expense['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Bill :')}}</span></td>
                                </tr>
                                @foreach($billArray as $i=>$bill)
                                    <tr>
                                        <td>{{$bill['category']}}</td>
                                        @foreach($bill['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Expense = Payment + Bill :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartExpenseArr as $i=>$expense)
                                    @foreach($expense as $key=>$value)

                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- half-yearly --}}
                        @elseif(isset($_GET['category']) && $_GET['period'] == 'half-yearly')
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Payment :')}}</span></td>
                                </tr>
                                @foreach($expenseArr as $i=>$expense)
                                    <tr>
                                        <td>{{$expense['category']}}</td>
                                        @foreach($expense['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Bill :')}}</span></td>
                                </tr>
                                @foreach($billArray as $i=>$bill)
                                    <tr>
                                        <td>{{$bill['category']}}</td>
                                        @foreach($bill['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Expense = Payment + Bill :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartExpenseArr as $i=>$expense)
                                    @foreach($expense as $key=>$value)

                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- yearly --}}
                        @elseif(isset($_GET['category']) && $_GET['period'] == 'yearly')
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Payment :')}}</span></td>
                                </tr>
                                @foreach($expenseArr as $i=>$expense)
                                    <tr>
                                        <td>{{$expense['category']}}</td>
                                        @foreach($expense['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Bill :')}}</span></td>
                                </tr>
                                @foreach($billArray as $i=>$bill)
                                    <tr>
                                        <td>{{$bill['category']}}</td>
                                        @foreach($bill['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Expense = Payment + Bill :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartExpenseArr as $i=>$expense)
                                    @foreach($expense as $key=>$value)

                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Payment :')}}</span></td>
                                </tr>
                                @foreach($expenseArr as $i=>$expense)
                                    <tr>
                                        <td>{{$expense['category']}}</td>
                                        @foreach($expense['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Bill :')}}</span></td>
                                </tr>
                                @foreach($billArray as $i=>$bill)
                                    <tr>
                                        <td>{{$bill['category']}}</td>
                                        @foreach($bill['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Expense = Payment + Bill :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartExpenseArr as $i=>$expense)
                                    @foreach($expense as $key=>$value)

                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach

                                </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</div>


@endsection


