@extends('layouts.admin')
@section('page-title')
    {{__('Income Summary')}}
@endsection

@push('css-page')
<style>
    .apexcharts-yaxis
    {
        transform: translate(30px, 0px) !important;
    }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Income Summary')}}</li>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush

@php
    if(isset($_GET['category']) && $_GET['period'] == 'yearly')
    {
        $chartArr = [];

foreach ($chartIncomeArr as $innerArray) {
    foreach ($innerArray as $value) {
        $chartArr[] = $value;
    }
}
    }
    else {
        $chartArr = $chartIncomeArr[0];
    }
@endphp
@push('script-page')
    <script>
        (function () {
            var chartBarOptions = {
                series: [
                    {
                        name: '{{ __("Income") }}',
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
                    // categories: {!! json_encode($monthList) !!},
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
                        text: '{{ __("Income") }}',
                        offsetX: 30,
                        offsetY: -25,
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
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('report.income.summary'),'method' => 'GET','id'=>'report_income_summary')) }}
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
                                            {{ Form::label('customer', __('Customer'),['class'=>'form-label'])}}
                                            {{ Form::select('customer',$customer,isset($_GET['customer'])?$_GET['customer']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary me-1" onclick="document.getElementById('report_income_summary').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('report.income.summary')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
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
                    value="{{ $filter['category'] . ' ' . __('Income Summary') . ' ' . 'Report of' . ' ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}"
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
                            <p class="text-muted mb-0">{{ __('Income Summary') }}
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
            @if ($filter['customer'] != __('All'))
                <div class="col mb-4">
                    <div class="card report-card h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="report-icon">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.01333 12.7314C4.62163 12.7314 4.23872 12.8475 3.91304 13.0651C3.58735 13.2828 3.33351 13.5921 3.18361 13.954C3.03371 14.3158 2.99449 14.714 3.07091 15.0982C3.14733 15.4824 3.33595 15.8353 3.61292 16.1123C3.8899 16.3892 4.24278 16.5778 4.62696 16.6543C5.01113 16.7307 5.40934 16.6915 5.77122 16.5416C6.1331 16.3917 6.44241 16.1378 6.66003 15.8121C6.87764 15.4865 6.99379 15.1035 6.99379 14.7118C6.99379 14.1866 6.78514 13.6829 6.41373 13.3114C6.04232 12.94 5.53858 12.7314 5.01333 12.7314ZM5.16008 3.57044L5.85629 5.72814C5.86609 5.7593 5.88565 5.78648 5.91207 5.80568C5.93849 5.82488 5.97039 5.83508 6.00305 5.83478L8.27043 5.83021C8.30355 5.829 8.33616 5.83862 8.36332 5.85761C8.39048 5.87661 8.4107 5.90394 8.42092 5.93546C8.43114 5.96699 8.43081 6.00099 8.41996 6.0323C8.40912 6.06362 8.38835 6.09054 8.36083 6.109L6.52407 7.43794C6.49747 7.45692 6.47768 7.48396 6.46763 7.51505C6.45757 7.54613 6.45777 7.57963 6.46821 7.6106L7.17254 9.76525C7.18376 9.79629 7.18459 9.83013 7.17491 9.86168C7.16524 9.89323 7.14557 9.92079 7.11889 9.94021C7.0922 9.95962 7.05993 9.96984 7.02693 9.96934C6.99393 9.96883 6.96198 9.95762 6.9359 9.9374L5.10422 8.60134C5.07799 8.58192 5.04622 8.57144 5.01358 8.57144C4.98094 8.57144 4.94917 8.58192 4.92294 8.60134L3.09126 9.9374C3.0652 9.95785 3.03318 9.96926 3.00005 9.96988C2.96693 9.9705 2.9345 9.96031 2.90769 9.94085C2.88088 9.92139 2.86114 9.89371 2.85147 9.86203C2.84181 9.83034 2.84273 9.79636 2.85411 9.76525L3.55895 7.6106C3.56939 7.57963 3.56959 7.54613 3.55953 7.51505C3.54947 7.48396 3.52969 7.45692 3.50309 7.43794L1.66633 6.109C1.6388 6.09054 1.61804 6.06362 1.6072 6.0323C1.59635 6.00099 1.59602 5.96699 1.60624 5.93546C1.61646 5.90394 1.63668 5.87661 1.66384 5.85761C1.69099 5.83862 1.72361 5.829 1.75672 5.83021L4.0236 5.83478C4.05626 5.83508 4.08816 5.82488 4.11458 5.80568C4.14101 5.78648 4.16056 5.7593 4.17036 5.72814L4.86708 3.57044C4.87616 3.53859 4.89537 3.51057 4.92181 3.49062C4.94824 3.47067 4.98046 3.45987 5.01358 3.45987C5.0467 3.45987 5.07892 3.47067 5.10535 3.49062C5.13179 3.51057 5.151 3.53859 5.16008 3.57044ZM21.1333 3.57044L21.83 5.72814C21.8398 5.7593 21.8594 5.78648 21.8858 5.80568C21.9122 5.82488 21.9441 5.83508 21.9768 5.83478L24.2437 5.83021C24.2768 5.829 24.3094 5.83862 24.3366 5.85761C24.3637 5.87661 24.3839 5.90394 24.3942 5.93546C24.4044 5.96699 24.404 6.00099 24.3932 6.0323C24.3824 6.06362 24.3616 6.09054 24.3341 6.109L22.4963 7.43794C22.4697 7.45692 22.4499 7.48396 22.4399 7.51505C22.4298 7.54613 22.43 7.57963 22.4404 7.6106L23.1463 9.76525C23.1577 9.79636 23.1586 9.83034 23.1489 9.86203C23.1393 9.89371 23.1195 9.92139 23.0927 9.94085C23.0659 9.96031 23.0335 9.9705 23.0003 9.96988C22.9672 9.96926 22.9352 9.95785 22.9091 9.9374L21.0775 8.60134C21.0512 8.58192 21.0195 8.57144 20.9868 8.57144C20.9542 8.57144 20.9224 8.58192 20.8962 8.60134L19.0645 9.9374C19.0384 9.95762 19.0065 9.96883 18.9735 9.96934C18.9405 9.96984 18.9082 9.95962 18.8815 9.94021C18.8548 9.92079 18.8352 9.89323 18.8255 9.86168C18.8158 9.83013 18.8166 9.79629 18.8279 9.76525L19.5322 7.6106C19.5426 7.57963 19.5428 7.54613 19.5328 7.51505C19.5227 7.48396 19.5029 7.45692 19.4763 7.43794L17.6396 6.109C17.612 6.09054 17.5913 6.06362 17.5804 6.0323C17.5696 6.00099 17.5693 5.96699 17.5795 5.93546C17.5897 5.90394 17.6099 5.87661 17.6371 5.85761C17.6642 5.83862 17.6968 5.829 17.73 5.83021L19.9974 5.83478C20.03 5.83508 20.0619 5.82488 20.0883 5.80568C20.1148 5.78648 20.1343 5.7593 20.1441 5.72814L20.8403 3.57044C20.8494 3.53859 20.8686 3.51057 20.8951 3.49062C20.9215 3.47067 20.9537 3.45987 20.9868 3.45987C21.0199 3.45987 21.0522 3.47067 21.0786 3.49062C21.105 3.51057 21.1242 3.53859 21.1333 3.57044ZM13.1465 1.53919L13.8432 3.69689C13.853 3.72805 13.8725 3.75523 13.8989 3.77443C13.9254 3.79363 13.9573 3.80383 13.9899 3.80353L16.2568 3.79896C16.29 3.79763 16.3227 3.80716 16.3499 3.82611C16.3772 3.84507 16.3975 3.8724 16.4078 3.90395C16.418 3.93551 16.4177 3.96956 16.4069 4.00093C16.3961 4.0323 16.3753 4.05927 16.3477 4.07775L14.5109 5.40669C14.4843 5.42561 14.4644 5.45261 14.4542 5.4837C14.4441 5.5148 14.4442 5.54833 14.4546 5.57935L15.1594 7.734C15.1708 7.76511 15.1717 7.79909 15.1621 7.83078C15.1524 7.86246 15.1326 7.89014 15.1058 7.9096C15.079 7.92906 15.0466 7.93925 15.0135 7.93863C14.9803 7.93801 14.9483 7.9266 14.9223 7.90615L13.0911 6.57009C13.0648 6.55055 13.0329 6.54 13.0002 6.54C12.9675 6.54 12.9356 6.55055 12.9093 6.57009L11.0781 7.90615C11.0521 7.9266 11.0201 7.93801 10.9869 7.93863C10.9538 7.93925 10.9214 7.92906 10.8946 7.9096C10.8678 7.89014 10.848 7.86246 10.8383 7.83078C10.8287 7.79909 10.8296 7.76511 10.841 7.734L11.5458 5.57935C11.5562 5.54833 11.5563 5.5148 11.5462 5.4837C11.536 5.45261 11.5161 5.42561 11.4895 5.40669L9.6527 4.07775C9.62513 4.05927 9.60434 4.0323 9.5935 4.00093C9.58266 3.96956 9.58236 3.93551 9.59265 3.90395C9.60294 3.8724 9.62325 3.84507 9.6505 3.82611C9.67774 3.80716 9.71043 3.79763 9.7436 3.79896L12.0105 3.80353C12.0431 3.80383 12.075 3.79363 12.1015 3.77443C12.1279 3.75523 12.1474 3.72805 12.1572 3.69689L12.854 1.53919C12.8632 1.50755 12.8824 1.47975 12.9088 1.45997C12.9352 1.4402 12.9672 1.4295 13.0002 1.4295C13.0332 1.4295 13.0652 1.4402 13.0916 1.45997C13.118 1.47975 13.1372 1.50755 13.1465 1.53919ZM13.0002 17.1676C10.2819 17.2083 8.02364 19.2466 7.63973 21.872C7.59888 22.1243 7.63162 22.383 7.73402 22.6172C7.83642 22.8513 8.00415 23.051 8.21711 23.1923C9.67047 24.1851 11.8134 24.5695 13.0002 24.5695C14.187 24.5695 16.3299 24.1851 17.7833 23.1923C17.9963 23.051 18.164 22.8513 18.2664 22.6172C18.3688 22.383 18.4015 22.1243 18.3607 21.872C17.9768 19.2461 15.719 17.2083 13.0002 17.1676ZM13.0002 11.2415C12.4993 11.2414 12.0096 11.3898 11.593 11.668C11.1765 11.9463 10.8518 12.3418 10.6601 12.8046C10.4684 13.2673 10.4182 13.7766 10.5159 14.2679C10.6136 14.7591 10.8548 15.2104 11.209 15.5646C11.5632 15.9188 12.0145 16.16 12.5058 16.2577C12.9971 16.3554 13.5063 16.3052 13.9691 16.1135C14.4318 15.9218 14.8273 15.5971 15.1056 15.1806C15.3838 14.764 15.5323 14.2743 15.5322 13.7734C15.5322 13.1019 15.2654 12.4579 14.7906 11.9831C14.3157 11.5082 13.6717 11.2415 13.0002 11.2415ZM20.9871 17.2951C22.0073 17.3141 22.9877 17.694 23.7545 18.3672C24.5212 19.0404 25.0247 19.9635 25.1755 20.9727C25.2071 21.1695 25.1813 21.3712 25.1014 21.5539C25.0214 21.7365 24.8906 21.8922 24.7246 22.0025C23.5886 22.7779 21.9143 23.0786 20.9871 23.0786C20.4285 23.0786 19.5947 22.9689 18.7735 22.7114C18.8787 22.4197 18.9094 22.1064 18.8629 21.7999C18.7118 20.7686 18.2934 19.7948 17.6492 18.9754C18.0435 18.4626 18.5484 18.0452 19.1263 17.7546C19.7042 17.4639 20.3403 17.3074 20.9871 17.2966V17.2951ZM5.01333 17.2951C5.66014 17.3058 6.29619 17.4624 6.87411 17.753C7.45203 18.0437 7.95692 18.461 8.35118 18.9739C7.70701 19.7933 7.28858 20.7671 7.13751 21.7984C7.09103 22.1049 7.12175 22.4182 7.22688 22.7099C6.40575 22.9674 5.57344 23.077 5.01333 23.077C4.08606 23.077 2.4118 22.7764 1.27583 22.001C1.11005 21.8908 0.979422 21.7353 0.89948 21.553C0.819537 21.3707 0.793647 21.1693 0.824888 20.9727C0.976052 19.9638 1.47965 19.0411 2.24637 18.3681C3.01309 17.6952 3.99336 17.3156 5.01333 17.2966V17.2951ZM20.9871 12.7314C20.5954 12.7314 20.2125 12.8475 19.8868 13.0651C19.5611 13.2828 19.3073 13.5921 19.1574 13.954C19.0075 14.3158 18.9682 14.714 19.0447 15.0982C19.1211 15.4824 19.3097 15.8353 19.5867 16.1123C19.8636 16.3892 20.2165 16.5778 20.6007 16.6543C20.9849 16.7307 21.3831 16.6915 21.745 16.5416C22.1069 16.3917 22.4162 16.1378 22.6338 15.8121C22.8514 15.4865 22.9675 15.1035 22.9675 14.7118C22.9675 14.1866 22.7589 13.6829 22.3875 13.3114C22.0161 12.94 21.5123 12.7314 20.9871 12.7314Z" fill="white"/>
                                    </svg>
                                    
                            </div>
                            <div class="report-info flex-1">
                                <h5 class="mb-1">{{ __('Customer') }} :</h5>
                                <p class="text-muted mb-0">{{ $filter['customer'] }}
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




                        {{-- quarterly --}}
                        @if(isset($_GET['category']) && $_GET['period'] == 'quarterly')

                        <div class="table-responsive" id="quarterly">
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
                                    <td colspan="5" class="text-dark"><span>{{__('Revenue :')}}</span></td>
                                </tr>
                                @foreach($incomeArr as $i=>$income)
                                    <tr>
                                        <td>{{$income['category']}}</td>
                                        @foreach($income['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5" class="text-dark"><span>{{__('Invoice :')}}</span></td>
                                </tr>
                                @foreach($invoiceArray as $i=>$invoice)
                                    <tr>
                                        <td>{{$invoice['category']}}</td>
                                        @foreach($invoice['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5" class="text-dark"><span>{{__('Income = Revenue + Invoice :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartIncomeArr as $i => $income)
                                    @foreach($income as $key => $value)
                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>


                        {{-- half yearly --}}
                        @elseif(isset($_GET['category']) && $_GET['period'] == 'half-yearly')

                        <div class="table-responsive" id="half-yearly">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category half')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Revenue :')}}</span></td>
                                </tr>
                                @foreach($incomeArr as $i=>$income)
                                    <tr>
                                        <td>{{$income['category']}}</td>
                                        @foreach($income['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Invoice :')}}</span></td>
                                </tr>
                                @foreach($invoiceArray as $i=>$invoice)
                                    <tr>
                                        <td>{{$invoice['category']}}</td>
                                        @foreach($invoice['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Income = Revenue + Invoice :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartIncomeArr as $i => $income)
                                    @foreach($income as $key => $value)
                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach

                                </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- yearly --}}
                        @elseif(isset($_GET['category']) && $_GET['period'] == 'yearly')

                        <div class="table-responsive" id="yearly">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Category year')}}</th>
                                    @foreach($monthList as $month)
                                        <th>{{$month}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="2" class="text-dark"><span>{{__('Revenue :')}}</span></td>
                                </tr>
                                @foreach($incomeArr as $i=>$income)
                                    <tr>
                                        <td>{{$income['category']}}</td>
                                        @foreach($income['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-dark"><span>{{__('Invoice :')}}</span></td>
                                </tr>
                                @foreach($invoiceArray as $i=>$invoice)
                                    <tr>
                                        <td>{{$invoice['category']}}</td>
                                        @foreach($invoice['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-dark"><span>{{__('Income = Revenue + Invoice :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>

                                    @foreach($chartIncomeArr as $i => $income)
                                    @foreach($income as $key => $value)
                                        <td>{{\Auth::user()->priceFormat($value)}}</td>
                                    @endforeach
                                    @endforeach

                                </tr>
                                </tbody>
                            </table>
                        </div>

                        @else
                        <div class="table-responsive" id="monthly">
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
                                    <td colspan="13" class="text-dark"><span>{{__('Revenue :')}}</span></td>
                                </tr>
                                @foreach($incomeArr as $i=>$income)
                                    <tr>
                                        <td>{{$income['category']}}</td>
                                        @foreach($income['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Invoice :')}}</span></td>
                                </tr>
                                @foreach($invoiceArray as $i=>$invoice)
                                    <tr>
                                        <td>{{$invoice['category']}}</td>
                                        @foreach($invoice['data'] as $j=>$data)
                                            <td>{{\Auth::user()->priceFormat($data)}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="13" class="text-dark"><span>{{__('Income = Revenue + Invoice :')}}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-dark"><h6>{{__('Total')}}</h6></td>
                                    @foreach($chartIncomeArr as $i => $income)
                                    @foreach($income as $key => $value)
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


