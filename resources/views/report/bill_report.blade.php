@extends('layouts.admin')
@section('page-title')
    {{__('Bill Summary')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Bill Summary')}}</li>
@endsection
@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@push('css-page')
<style>
    .apexcharts-yaxis
    {
        transform: translate(20px, 0px) !important;
    }
</style>
@endpush

@push('script-page')
    <script>
        (function () {
            var chartBarOptions = {
                series: [
                    {
                        name: '{{ __("Bill") }}',
                        data:  {!! json_encode($billTotal) !!},

                    },
                ],

                chart: {
                    height: 300,
                    type: 'bar',
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
                        text: '{{ __("Bill") }}',
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

        $(document).ready(function () {
            var filename = $('#filename').val();
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: filename
                    },
                    {
                        extend: 'pdf',
                        title: filename
                    }, {
                        extend: 'csv',
                        title: filename
                    }
                ]
            });
        });
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
                        {{ Form::open(array('route' => array('report.bill.summary'),'method' => 'GET','id'=>'report_bill_summary')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('start_month', __('Start Month'),['class'=>'form-label']) }}

                                            {{Form::month('start_month',isset($_GET['start_month'])?$_GET['start_month']:date('Y-m', strtotime("-5 month")),array('class'=>'month-btn form-control'))}}

                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('end_month', __('End Month'),['class'=>'form-label']) }}
                                            {{Form::month('end_month',isset($_GET['end_month'])?$_GET['end_month']:date('Y-m'),array('class'=>'month-btn form-control'))}}

                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('vender', __('Vender'),['class'=>'form-label']) }}
                                            {{ Form::select('vender',$vender,isset($_GET['vender'])?$_GET['vender']:'', array('class' => 'form-control select')) }}

                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('status', __('Status'),['class'=>'form-label']) }}

                                            {{ Form::select('status', [''=>'Select Status']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">

                                        <a href="#" class="btn btn-sm btn-primary me-1" onclick="document.getElementById('report_bill_summary').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{route('report.bill.summary')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
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
                <input type="hidden" value="{{$filter['status'].' '.__('Bill').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange'].' '.__('of').' '.$filter['vender']}}" id="filename">
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
                            <h5 class="mb-1">{{__('Report')}} :</h5>
                            <p class="text-muted mb-0">{{__('Bill Summary')}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @if($filter['vender']!= __('All'))
            <div class="col mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_4436_2172)">
                                <path d="M1.625 23.5625C1.625 24.9031 2.72186 26 4.0625 26H21.9375C23.2781 26 24.375 24.9031 24.375 23.5625V21.9375H1.625V23.5625Z" fill="white"/>
                                <path d="M24.3747 16.25C24.3738 13.4478 24.3729 10.6456 24.3721 7.84343C25.2984 7.52444 25.8278 6.49633 25.8668 5.51741C25.9057 4.5385 25.5552 3.59089 25.21 2.67402C24.8453 1.70529 24.3947 0.641933 23.449 0.221156C22.9487 -0.00143684 22.3823 -0.00500371 21.8347 -0.00478433C15.8962 -0.00242808 9.95766 -6.37067e-05 4.01912 0.00229254C3.45605 0.00251192 2.86695 0.00892253 2.36993 0.273546C1.66014 0.651456 1.30852 1.4556 1.00771 2.20134C0.575011 3.27401 0.127161 4.39389 0.124114 5.56852C0.121644 6.52079 0.765574 7.44098 1.62624 7.84432C1.62587 10.648 1.62548 13.4517 1.62509 16.2555C0.495418 16.1311 -0.0472588 17.4106 -0.0424488 18.335C-0.0390932 18.979 0.164332 19.6795 0.703678 20.0315C1.12337 20.3054 1.65652 20.3143 2.15768 20.3143C9.44942 20.3132 16.7412 20.3122 24.0329 20.3111C24.4165 20.3111 24.8167 20.3073 25.1584 20.1329C25.7192 19.8468 25.9739 19.1754 26.0273 18.5481C26.1107 17.5692 25.5994 16.152 24.3747 16.25ZM3.24979 8.09643C3.68179 8.0352 4.13698 7.85369 4.49234 7.60131C4.71411 7.44381 5.03507 7.44381 5.25882 7.60211C6.22605 8.28607 7.58444 8.28686 8.55484 7.60131C8.77661 7.44381 9.09797 7.44381 9.32132 7.60211C10.2885 8.28607 11.6469 8.28686 12.6173 7.60131C12.8387 7.44381 13.1601 7.44381 13.3838 7.60211C14.3502 8.28607 15.7102 8.28686 16.6798 7.60131C16.8996 7.44461 17.2218 7.44341 17.4463 7.60211C18.4128 8.28607 19.7727 8.28686 20.7423 7.60131C20.9621 7.44461 21.2843 7.44341 21.5088 7.60211C21.8698 7.85766 22.3137 8.02911 22.7498 8.09643V16.25H17.5964C17.0913 14.8243 15.9425 13.7276 14.5294 13.2573C15.0787 12.81 15.4373 12.137 15.4373 11.375C15.4373 10.0309 14.3439 8.93749 12.9998 8.93749C11.6557 8.93749 10.5623 10.0309 10.5623 11.375C10.5623 12.137 10.9209 12.81 11.4702 13.2573C10.0573 13.7276 8.90852 14.8243 8.40354 16.25H3.24978C3.24978 16.25 3.24979 8.09643 3.24979 8.09643Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_4436_2172">
                                <rect width="26" height="26" fill="white"/>
                                </clipPath>
                                </defs>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{__('Vendor')}} :</h5>
                            <p class="text-muted mb-0">{{$filter['vender']}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($filter['status']!= __('All'))
            <div class="col mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_4436_2257)">
                                <path d="M22.1638 4.98535L17.5396 9.60961C18.1142 10.3784 18.4827 11.2814 18.6101 12.2327C18.7015 12.2065 18.7957 12.1914 18.8907 12.1875H24.9845C25.0395 12.1925 25.094 12.2013 25.1478 12.2138C24.9792 9.54081 23.9299 6.99892 22.1638 4.98535Z" fill="white"/>
                                <path d="M0.812509 13C0.815126 16.0941 1.99485 19.0712 4.11227 21.3273C6.22968 23.5833 9.12615 24.9493 12.2138 25.1479C12.2013 25.094 12.1925 25.0394 12.1875 24.9844V18.8906C12.1915 18.7956 12.2066 18.7014 12.2327 18.61C11.3714 18.4973 10.5475 18.1883 9.82456 17.7068C9.10159 17.2253 8.49884 16.5843 8.06278 15.833C7.62672 15.0818 7.369 14.2405 7.30949 13.3739C7.24998 12.5073 7.39027 11.6386 7.71955 10.8349C8.04882 10.0311 8.55828 9.31363 9.20865 8.73785C9.85902 8.16208 10.6329 7.74335 11.4707 7.51395C12.3085 7.28454 13.1877 7.2506 14.0407 7.41472C14.8937 7.57884 15.6976 7.93665 16.3904 8.46056L21.0147 3.8363C19.2549 2.29187 17.0871 1.28772 14.7711 0.944219C12.455 0.60072 10.0891 0.932451 7.9568 1.89965C5.82453 2.86684 4.01645 4.42845 2.74929 6.39729C1.48213 8.36612 0.809666 10.6586 0.812509 13Z" fill="white"/>
                                <path d="M24.9845 13.8125H18.8907C18.7957 13.8086 18.7015 13.7935 18.6101 13.7673C18.4423 14.9925 17.8782 16.1291 17.0037 17.0035C16.1293 17.8779 14.9927 18.4421 13.7676 18.6098C13.7936 18.7013 13.8087 18.7956 13.8126 18.8906V24.9844C13.8076 25.0394 13.7988 25.094 13.7863 25.1479C16.7367 24.9566 19.5168 23.6981 21.6075 21.6074C23.6981 19.5167 24.9565 16.7367 25.1478 13.7862C25.094 13.7987 25.0394 13.8075 24.9845 13.8125Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_4436_2257">
                                <rect width="26" height="26" fill="white"/>
                                </clipPath>
                                </defs>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{__('Status')}} :</h5>
                            <p class="text-muted mb-0">{{$filter['status']}}
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
                            <h5 class="mb-1">{{__('Duration')}} :</h5>
                            <p class="text-muted mb-0">{{$filter['startDateRange'].' to '.$filter['endDateRange']}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 17.2292C4.28451 17.2292 4.07785 17.1436 3.92548 16.9912C3.7731 16.8388 3.6875 16.6322 3.6875 16.4167C3.6875 16.2012 3.7731 15.9945 3.92548 15.8421C4.07785 15.6898 4.28451 15.6042 4.5 15.6042H7.49C7.49 15.5175 7.49 15.42 7.49 15.3333C7.48832 14.1093 7.77796 12.9024 8.335 11.8125H4.5C4.28451 11.8125 4.07785 11.7269 3.92548 11.5745C3.7731 11.4222 3.6875 11.2155 3.6875 11C3.6875 10.7845 3.7731 10.5778 3.92548 10.4255C4.07785 10.2731 4.28451 10.1875 4.5 10.1875H9.4075C9.97112 9.54561 10.6329 8.99713 11.3683 8.5625H4.5C4.28451 8.5625 4.07785 8.4769 3.92548 8.32452C3.7731 8.17215 3.6875 7.96549 3.6875 7.75C3.6875 7.53451 3.7731 7.32785 3.92548 7.17548C4.07785 7.0231 4.28451 6.9375 4.5 6.9375H12.0833C12.2979 6.94031 12.503 7.02681 12.6548 7.17858C12.8065 7.33035 12.893 7.53538 12.8958 7.75C12.8958 7.75 12.8958 7.83667 12.8958 7.88C13.6815 7.61612 14.5046 7.48077 15.3333 7.47917C15.6039 7.46298 15.8752 7.46298 16.1458 7.47917V2.33333C16.143 1.8314 15.9423 1.35084 15.5874 0.995919C15.2325 0.640996 14.7519 0.440344 14.25 0.4375H2.33333C1.8314 0.440344 1.35084 0.640996 0.995919 0.995919C0.640996 1.35084 0.440344 1.8314 0.4375 2.33333V18.5833C0.440344 19.0853 0.640996 19.5658 0.995919 19.9207C1.35084 20.2757 1.8314 20.4763 2.33333 20.4792H9.41833C8.59758 19.5489 8.01405 18.4338 7.7175 17.2292H4.5ZM4.5 3.6875H12.0833C12.2988 3.6875 12.5055 3.7731 12.6579 3.92548C12.8102 4.07785 12.8958 4.28451 12.8958 4.5C12.8958 4.71549 12.8102 4.92215 12.6579 5.07452C12.5055 5.2269 12.2988 5.3125 12.0833 5.3125H4.5C4.28451 5.3125 4.07785 5.2269 3.92548 5.07452C3.7731 4.92215 3.6875 4.71549 3.6875 4.5C3.6875 4.28451 3.7731 4.07785 3.92548 3.92548C4.07785 3.7731 4.28451 3.6875 4.5 3.6875ZM15.3333 9.10417C14.1013 9.10417 12.897 9.4695 11.8726 10.154C10.8482 10.8384 10.0498 11.8113 9.57833 12.9495C9.10686 14.0878 8.98351 15.3402 9.22386 16.5486C9.46421 17.7569 10.0575 18.8669 10.9286 19.738C11.7998 20.6092 12.9097 21.2025 14.1181 21.4428C15.3264 21.6832 16.5789 21.5598 17.7171 21.0883C18.8554 20.6169 19.8282 19.8185 20.5127 18.7941C21.1972 17.7697 21.5625 16.5653 21.5625 15.3333C21.5596 13.6821 20.9024 12.0994 19.7349 10.9318C18.5673 9.76424 16.9845 9.10703 15.3333 9.10417ZM15.9833 18.2475V18.5833C15.9833 18.7557 15.9149 18.9211 15.793 19.043C15.6711 19.1649 15.5057 19.2333 15.3333 19.2333C15.1609 19.2333 14.9956 19.1649 14.8737 19.043C14.7518 18.9211 14.6833 18.7557 14.6833 18.5833V18.2258C14.3371 18.1623 14.0039 18.0415 13.6975 17.8683C13.6054 17.8369 13.5215 17.7852 13.452 17.717C13.3826 17.6488 13.3293 17.5658 13.2962 17.4743C13.2631 17.3827 13.2509 17.2849 13.2607 17.1881C13.2704 17.0912 13.3018 16.9978 13.3525 16.9147C13.4032 16.8316 13.4719 16.7609 13.5536 16.7079C13.6352 16.6549 13.7278 16.6209 13.8243 16.6085C13.9209 16.5961 14.019 16.6055 14.1114 16.636C14.2038 16.6665 14.2882 16.7175 14.3583 16.785C14.6418 16.9479 14.963 17.0338 15.29 17.0342H15.55C15.6659 17.0342 15.7773 16.9889 15.8603 16.9079C15.9433 16.827 15.9913 16.7167 15.9942 16.6008C15.9946 16.5103 15.9665 16.4218 15.9141 16.348C15.8616 16.2741 15.7873 16.2186 15.7017 16.1892L14.5317 15.7667C14.1928 15.6477 13.8992 15.4264 13.6916 15.1333C13.4839 14.8403 13.3724 14.49 13.3725 14.1308C13.3766 13.7459 13.5071 13.373 13.744 13.0696C13.9809 12.7662 14.3109 12.5491 14.6833 12.4517V12.0833C14.6833 11.9109 14.7518 11.7456 14.8737 11.6237C14.9956 11.5018 15.1609 11.4333 15.3333 11.4333C15.5057 11.4333 15.6711 11.5018 15.793 11.6237C15.9149 11.7456 15.9833 11.9109 15.9833 12.0833V12.4408C16.3295 12.5043 16.6627 12.6251 16.9692 12.7983C17.0613 12.8298 17.1452 12.8815 17.2147 12.9497C17.2841 13.0179 17.3374 13.1008 17.3705 13.1924C17.4036 13.2839 17.4157 13.3817 17.406 13.4786C17.3962 13.5755 17.3648 13.6689 17.3142 13.752C17.2635 13.8351 17.1947 13.9058 17.1131 13.9588C17.0314 14.0117 16.9389 14.0457 16.8424 14.0582C16.7458 14.0706 16.6477 14.0612 16.5553 14.0307C16.4628 14.0001 16.3784 13.9492 16.3083 13.8817C16.0248 13.7188 15.7036 13.6329 15.3767 13.6325H15.1167C15.0007 13.6325 14.8894 13.6778 14.8064 13.7587C14.7234 13.8397 14.6753 13.9499 14.6725 14.0658C14.6721 14.1564 14.7001 14.2448 14.7526 14.3187C14.8051 14.3925 14.8793 14.4481 14.965 14.4775L16.135 14.9C16.4739 15.019 16.7674 15.2403 16.9751 15.5333C17.1827 15.8264 17.2942 16.1767 17.2942 16.5358C17.2972 16.9263 17.17 17.3067 16.9326 17.6167C16.6951 17.9267 16.3611 18.1487 15.9833 18.2475Z" fill="white"/>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{__('Total Bill')}}</h5>
                            <p class="text-muted mb-0">{{Auth::user()->priceFormat($totalBill)}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_4436_2310)">
                                <path d="M7.3125 12.1875C3.72938 12.1875 0.8125 15.1044 0.8125 18.6875C0.8125 22.2706 3.72938 25.1875 7.3125 25.1875C10.8956 25.1875 13.8125 22.2706 13.8125 18.6875C13.8125 15.1044 10.8956 12.1875 7.3125 12.1875ZM6.70312 17.875H7.92188C9.04313 17.875 9.95312 18.785 9.95312 19.9062C9.95312 20.9544 9.14875 21.8156 8.125 21.9131V22.3438C8.125 22.7906 7.75938 23.1562 7.3125 23.1562C6.86562 23.1562 6.5 22.7906 6.5 22.3438V21.9375H5.48438C5.0375 21.9375 4.67188 21.5719 4.67188 21.125C4.67188 20.6781 5.0375 20.3125 5.48438 20.3125H7.92188C8.14937 20.3125 8.32812 20.1337 8.32812 19.9062C8.32812 19.6788 8.14937 19.5 7.92188 19.5H6.70312C5.58187 19.5 4.67188 18.59 4.67188 17.4688C4.67188 16.4206 5.47625 15.5594 6.5 15.4619V15.0312C6.5 14.5844 6.86562 14.2188 7.3125 14.2188C7.75938 14.2188 8.125 14.5844 8.125 15.0312V15.4375H9.14062C9.5875 15.4375 9.95312 15.8031 9.95312 16.25C9.95312 16.6969 9.5875 17.0625 9.14062 17.0625H6.70312C6.47563 17.0625 6.29688 17.2413 6.29688 17.4688C6.29688 17.6962 6.47563 17.875 6.70312 17.875Z" fill="white"/>
                                <path d="M24.375 0.8125C23.9281 0.8125 23.5625 1.17812 23.5625 1.625V1.73063L21.1981 2.07187V8.125C21.1981 8.57188 20.8325 8.9375 20.3856 8.9375C19.9388 8.9375 19.5731 8.57188 19.5731 8.125V2.29937L13.8125 3.12V11.4969L14.625 11.6106V14.2106C14.625 15.3319 15.535 16.2419 16.6562 16.2419H18.6956C19.5081 16.2419 20.2394 15.7625 20.5644 15.0069L21.5963 12.6019L23.5625 12.8862V12.9919C23.5625 13.4388 23.9281 13.8044 24.375 13.8044C24.8219 13.8044 25.1875 13.4388 25.1875 12.9919V1.625C25.1875 1.17812 24.8219 0.8125 24.375 0.8125ZM19.0694 14.3813C19.0044 14.5275 18.8581 14.625 18.6956 14.625H16.6562C16.4288 14.625 16.25 14.4462 16.25 14.2188V11.8462L19.9306 12.3744L19.0694 14.3813Z" fill="white"/>
                                <path d="M10.5625 10.1562H12.1875V4.46875H8.9375V8.53125C8.9375 9.425 9.66875 10.1562 10.5625 10.1562Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_4436_2310">
                                <rect width="26" height="26" fill="white"/>
                                </clipPath>
                                </defs>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{__('Total Paid')}}</h5>
                            <p class="text-muted mb-0">{{Auth::user()->priceFormat($totalPaidBill)}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <div class="card report-card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.6668 6.50001C21.6668 5.30293 20.6972 4.33334 19.5002 4.33334H18.4168V3.25001C18.4168 2.95209 18.1731 2.70834 17.8752 2.70834C17.5772 2.70834 17.3335 2.95209 17.3335 3.25001V4.33334H15.1668V3.25001C15.1668 2.95209 14.9231 2.70834 14.6252 2.70834C14.3272 2.70834 14.0835 2.95209 14.0835 3.25001V4.33334H11.9168V3.25001C11.9168 2.95209 11.6731 2.70834 11.3752 2.70834C11.0772 2.70834 10.8335 2.95209 10.8335 3.25001V4.33334H8.66683V3.25001C8.66683 2.95209 8.42308 2.70834 8.12516 2.70834C7.82725 2.70834 7.5835 2.95209 7.5835 3.25001V4.33334H5.41683V3.25001C5.41683 2.95209 5.17308 2.70834 4.87516 2.70834C4.57725 2.70834 4.3335 2.95209 4.3335 3.25001V4.33334H3.25016C2.05308 4.33334 1.0835 5.30293 1.0835 6.50001V8.12501H21.6668V6.50001Z" fill="white"/>
                                <path d="M10.8335 12.4583H11.9168V13.5417H10.8335V12.4583Z" fill="white"/>
                                <path d="M10.8335 17.875H11.9168V18.9583H10.8335V17.875Z" fill="white"/>
                                <path d="M5.4165 17.875H6.49984V18.9583H5.4165V17.875Z" fill="white"/>
                                <path d="M5.4165 12.4583H6.49984V13.5417H5.4165V12.4583Z" fill="white"/>
                                <path d="M16.6292 13.5417C16.8513 13.3846 17.0896 13.2438 17.3333 13.1138V12.4583H16.25V13.5417H16.6292Z" fill="white"/>
                                <path d="M14.0835 18.4167C14.0835 16.9542 14.6143 15.6163 15.4918 14.5763C15.3022 14.495 15.1668 14.3054 15.1668 14.0833V11.9167C15.1668 11.6188 15.4106 11.375 15.7085 11.375H17.8752C18.1731 11.375 18.4168 11.6188 18.4168 11.9167V12.6913C18.9368 12.545 19.4785 12.4583 20.0418 12.4583C20.6052 12.4583 21.1468 12.5396 21.6668 12.6913V9.20834H1.0835V21.125C1.0835 22.3221 2.05308 23.2917 3.25016 23.2917H16.6293C15.0964 22.2138 14.0835 20.4317 14.0835 18.4167ZM7.5835 19.5C7.5835 19.7979 7.33975 20.0417 7.04183 20.0417H4.87516C4.57725 20.0417 4.3335 19.7979 4.3335 19.5V17.3333C4.3335 17.0354 4.57725 16.7917 4.87516 16.7917H7.04183C7.33975 16.7917 7.5835 17.0354 7.5835 17.3333V19.5ZM7.5835 14.0833C7.5835 14.3813 7.33975 14.625 7.04183 14.625H4.87516C4.57725 14.625 4.3335 14.3813 4.3335 14.0833V11.9167C4.3335 11.6188 4.57725 11.375 4.87516 11.375H7.04183C7.33975 11.375 7.5835 11.6188 7.5835 11.9167V14.0833ZM13.0002 19.5C13.0002 19.7979 12.7564 20.0417 12.4585 20.0417H10.2918C9.99391 20.0417 9.75016 19.7979 9.75016 19.5V17.3333C9.75016 17.0354 9.99391 16.7917 10.2918 16.7917H12.4585C12.7564 16.7917 13.0002 17.0354 13.0002 17.3333V19.5ZM13.0002 14.0833C13.0002 14.3813 12.7564 14.625 12.4585 14.625H10.2918C9.99391 14.625 9.75016 14.3813 9.75016 14.0833V11.9167C9.75016 11.6188 9.99391 11.375 10.2918 11.375H12.4585C12.7564 11.375 13.0002 11.6188 13.0002 11.9167V14.0833Z" fill="white"/>
                                <path d="M20.0415 13.5417C17.3548 13.5417 15.1665 15.73 15.1665 18.4167C15.1665 21.1033 17.3548 23.2917 20.0415 23.2917C22.7282 23.2917 24.9165 21.1033 24.9165 18.4167C24.9165 15.73 22.7282 13.5417 20.0415 13.5417ZM21.1248 17.875C21.7207 17.875 22.2082 18.3625 22.2082 18.9583V19.5C22.2082 20.0958 21.7207 20.5833 21.1248 20.5833H20.5832C20.5832 20.8812 20.3394 21.125 20.0415 21.125C19.7436 21.125 19.4998 20.8812 19.4998 20.5833H18.4165C18.1186 20.5833 17.8748 20.3396 17.8748 20.0417C17.8748 19.7437 18.1186 19.5 18.4165 19.5H21.1248V18.9583H18.9582C18.3623 18.9583 17.8748 18.4708 17.8748 17.875V17.3333C17.8748 16.7375 18.3623 16.25 18.9582 16.25H19.4998C19.4998 15.9521 19.7436 15.7083 20.0415 15.7083C20.3394 15.7083 20.5832 15.9521 20.5832 16.25H21.6665C21.9644 16.25 22.2082 16.4937 22.2082 16.7917C22.2082 17.0896 21.9644 17.3333 21.6665 17.3333H18.9582V17.875H21.1248Z" fill="white"/>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{__('Total Due')}}</h5>
                            <p class="text-muted mb-0">{{Auth::user()->priceFormat($totalDueBill)}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="bill-container">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="profile-tab3" data-bs-toggle="pill" href="#summary" role="tab" aria-controls="pills-summary" aria-selected="true">{{__('Summary')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="contact-tab4" data-bs-toggle="pill" href="#bills" role="tab" aria-controls="pills-invoice" aria-selected="false">{{__('Bills')}}</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="tab-content" id="myTabContent2">
                                    <div class="tab-pane fade fade" id="bills" role="tabpanel" aria-labelledby="profile-tab3">
                                        <div class="table-responsive">
                                        <table class="table table-flush datatable" id="report-dataTable">
                                            <thead>
                                            <tr>
                                                <th> {{__('Bill')}}</th>
                                                <th> {{__('Date')}}</th>
                                                <th> {{__('Customer')}}</th>
                                                <th> {{__('Category')}}</th>
                                                <th> {{__('Status')}}</th>
                                                <th> {{__('	Paid Amount')}}</th>
                                                <th> {{__('Due Amount')}}</th>
                                                <th> {{__('Payment Date')}}</th>
                                                <th> {{__('Amount')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($bills as $bill)
                                                <tr>
                                                    <td class="Id">
                                                        <a href="{{ route('bill.show',\Crypt::encrypt($bill->id)) }}" class="btn btn-outline-primary">{{ Auth::user()->billNumberFormat($bill->bill_id) }}</a>
                                                    </td>

                                                    </td>
                                                    <td>{{ Auth::user()->dateFormat($bill->send_date) }}</td>
                                                    <td> {{!empty($bill->vender)? $bill->vender->name:'-' }} </td>
                                                    <td>{{ !empty($bill->category)?$bill->category->name:'-'}}</td>
                                                    <td>
                                                        @if($bill->status == 0)
                                                            <span class="badge status_badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 1)
                                                            <span class="badge status_badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 2)
                                                            <span class="badge status_badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 3)
                                                            <span class="badge status_badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 4)
                                                            <span class="badge status_badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                                        @endif
                                                    </td>
                                                    <td> {{\Auth::user()->priceFormat($bill->getTotal()-$bill->getDue())}}</td>
                                                    <td> {{\Auth::user()->priceFormat($bill->getDue())}}</td>
                                                    <td>{{!empty($bill->lastPayments)?\Auth::user()->dateFormat($bill->lastPayments->date):''}}</td>
                                                    <td> {{\Auth::user()->priceFormat($bill->getTotal())}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade fade show active" id="summary" role="tabpanel" aria-labelledby="profile-tab3">
                                        <div class="scrollbar-inner">
                                            <div id="chart-sales" data-color="primary" data-type="bar" data-height="300"></div>
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
