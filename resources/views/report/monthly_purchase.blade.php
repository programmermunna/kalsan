@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Purchase') }}
@endsection

@push('css-page')
    <style>
        .apexcharts-yaxis {
            transform: translate(45px, 0px) !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Daily Purchase Report') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

    <script>
        (function() {
            var chartBarOptions = {
                series: [{
                    name: '{{ __('Purchase') }}',
                    data: {!! json_encode($data) !!},
                    //                         data:  [100,300,150,300,120,290,150,270,180,250,190,260],
                }, ],

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
                        text: '{{ __('Months') }}'
                    }
                },
                colors: ['#6fd944', '#6fd944'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                yaxis: {
                    title: {
                        text: '{{ __('Amount') }}',
                        offsetX: 60,
                        offsetY: -25,
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#monthly-purchase"), chartBarOptions);
            arChart.render();
        })();
    </script>
@endpush

@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip"
            title="{{ __('Download') }}" data-original-title="{{ __('Download') }}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a>
    </div>
@endsection

@section('content')
    <ul class="nav nav-ul nav-pills my-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="pills-home-tab" data-bs-toggle="pill" href="{{ route('report.daily.purchase') }}"
                onclick="window.location.href = '{{ route('report.daily.purchase') }}'" role="tab"
                aria-controls="pills-home" aria-selected="true">{{ __('Daily') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill" href="#monthly-chart" role="tab"
                aria-controls="pills-profile" aria-selected="false">{{ __('Monthly') }}</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 ">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['report.monthly.purchase'], 'method' => 'GET', 'id' => 'monthly_purchase_report_submit']) }}
                        <div class="row d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('year', __('Year'), ['class' => 'form-label']) }}
                                    {{ Form::select('year', $yearList, isset($_GET['year']) ? $_GET['year'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('warehouse', __('Warehouse'), ['class' => 'form-label']) }}
                                    {{ Form::select('warehouse', $warehouse, isset($_GET['warehouse']) ? $_GET['warehouse'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('vendor', __('Vendor'), ['class' => 'form-label']) }}
                                    {{ Form::select('vendor', $vendor, isset($_GET['vendor']) ? $_GET['vendor'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>

                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('monthly_purchase_report_submit').submit(); return false;"
                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('report.monthly.purchase') }}" class="btn btn-sm btn-danger"
                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                                </a>
                            </div>

                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="printableArea">
        <div class="row mt-0">
            <div class="col mb-4">
                <input type="hidden" value="{{$filter['warehouse'].' '.__('Monthly Purchase').' '.'Report of'.' '.$filter['startMonth'].' to '.$filter['endMonth']}}" id="filename">
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
                            <h5 class="mb-1">{{ __('Report') }}</h5>
                            <p class="text-muted mb-0">{{__('Monthly Purchase Report')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($filter['warehouse']))
                <div class="col mb-4">
                    <div class="card report-card h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="report-icon">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_4436_2165)">
                                    <path d="M25.649 8.53883L13.4615 0.143C13.1842 -0.0476667 12.818 -0.0476667 12.5396 0.143L0.352083 8.53883C0.131083 8.6905 0 8.94075 0 9.20833V24.9167C0 25.5147 0.485333 26 1.08333 26H3.25V13C3.25 12.402 3.73533 11.9167 4.33333 11.9167H21.6667C22.2647 11.9167 22.75 12.402 22.75 13V26H24.9167C25.5147 26 26 25.5147 26 24.9167V9.20833C26 8.94075 25.8689 8.6905 25.649 8.53883Z" fill="white"/>
                                    <path d="M10.021 20.5833V21.3958C10.021 21.8443 9.657 22.2083 9.2085 22.2083C8.76 22.2083 8.396 21.8443 8.396 21.3958V20.5833H6.50016C6.20116 20.5833 5.9585 20.826 5.9585 21.125V25.4583C5.9585 25.7573 6.20116 26 6.50016 26H11.9168C12.2158 26 12.4585 25.7573 12.4585 25.4583V21.125C12.4585 20.826 12.2158 20.5833 11.9168 20.5833H10.021Z" fill="white"/>
                                    <path d="M17.604 20.5833V21.3958C17.604 21.8443 17.24 22.2083 16.7915 22.2083C16.343 22.2083 15.979 21.8443 15.979 21.3958V20.5833H14.0832C13.7842 20.5833 13.5415 20.826 13.5415 21.125V25.4583C13.5415 25.7573 13.7842 26 14.0832 26H19.4998C19.7988 26 20.0415 25.7573 20.0415 25.4583V21.125C20.0415 20.826 19.7988 20.5833 19.4998 20.5833H17.604Z" fill="white"/>
                                    <path d="M13.8125 14.0833V14.8958C13.8125 15.3443 13.4485 15.7083 13 15.7083C12.5515 15.7083 12.1875 15.3443 12.1875 14.8958V14.0833H10.2917C9.99267 14.0833 9.75 14.326 9.75 14.625V18.9583C9.75 19.2573 9.99267 19.5 10.2917 19.5H15.7083C16.0073 19.5 16.25 19.2573 16.25 18.9583V14.625C16.25 14.326 16.0073 14.0833 15.7083 14.0833H13.8125Z" fill="white"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_4436_2165">
                                    <rect width="26" height="26" fill="white"/>
                                    </clipPath>
                                    </defs>
                                    </svg>
                                    
                            </div>
                            <div class="report-info flex-1">
                                <h5 class="mb-1">{{ __('Warehouse') }}</h5>
                                <p class="text-muted mb-0">{{ $filter['warehouse'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(!empty($filter['vendor']))
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
                                <h5 class="mb-1">{{ __('Vendor') }}</h5>
                                <p class="text-muted mb-0">{{ $filter['vendor'] }}</p>
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
                            <h5 class="mb-1">{{ __('Duration') }}</h5>
                            <p class="text-muted mb-0">{{ $filter['startMonth'].' to '.$filter['endMonth'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="setting-tab">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="monthly-chart" role="tabpanel">
                                <div class="col-lg-12">
                                    <div class="card-header">
                                        <div class="row ">
                                            <div class="col-6">
                                                <h6>{{ __('Monthly Report') }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="monthly-purchase"></div>
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
