@extends('layouts.admin')
@section('page-title')
    {{__('Manage Leave Report')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Leave Report')}}</li>
@endsection
@push('script-page')

    <script type="text/javascript" src="{{ asset('js/jszip.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.buttons.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/buttons.html5.js') }}"></script>
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
                jsPDF: {unit: 'in', format: 'A4'}
            };
            html2pdf().set(opt).from(element).save();

        }

        $(document).ready(function () {
            var filename = $('#filename').val();
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'pdf',
                        title: filename
                    },
                    {
                        extend: 'excel',
                        title: filename
                    }, {
                        extend: 'csv',
                        title: filename
                    }
                ]
            });
        });
    </script>
    <script>
        $('input[name="type"]:radio').on('change', function (e) {
            var type = $(this).val();
            if (type == 'monthly') {
                $('.month').addClass('d-block');
                $('.month').removeClass('d-none');
                $('.year').addClass('d-none');
                $('.year').removeClass('d-block');
            } else {
                $('.year').addClass('d-block');
                $('.year').removeClass('d-none');
                $('.month').addClass('d-none');
                $('.month').removeClass('d-block');
            }
        });

        $('input[name="type"]:radio:checked').trigger('change');

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
                        {{ Form::open(array('route' => array('report.leave'),'method'=>'get','id'=>'report_leave')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">

                                    <div class="col-3 mt-2">
                                        <label class="form-label">{{__('Type')}}</label> <br>

                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="monthly" value="monthly" name="type" class="form-check-input" {{isset($_GET['type']) && $_GET['type']=='monthly' ?'checked':'checked'}}>
                                            <label class="form-check-label" for="monthly">{{__('Monthly')}}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="daily" value="daily" name="type" class="form-check-input" {{isset($_GET['type']) && $_GET['type']=='daily' ?'checked':''}}>
                                            <label class="form-check-label" for="daily">{{__('Daily')}}</label>
                                        </div>

                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{Form::label('month',__('Month'),['class'=>'form-label'])}}
                                            {{Form::month('month',isset($_GET['month'])?$_GET['month']:date('Y-m'),array('class'=>'month-btn form-control'))}}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 year d-none">
                                        <div class="btn-box">
                                            {{ Form::label('year', __('Year'),['class'=>'form-label']) }}
                                            <select class="form-control select" id="year" name="year" tabindex="-1" aria-hidden="true">
                                                @for($filterYear['starting_year']; $filterYear['starting_year'] <= $filterYear['ending_year']; $filterYear['starting_year']++)
                                                    <option {{(isset($_GET['year']) && $_GET['year'] == $filterYear['starting_year'] ?'selected':'')}} {{(!isset($_GET['year']) && date('Y') == $filterYear['starting_year'] ?'selected':'')}} value="{{$filterYear['starting_year']}}">{{$filterYear['starting_year']}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('branch', __('Branch'),['class'=>'form-label']) }}
                                            {{ Form::select('branch', $branch,isset($_GET['branch'])?$_GET['branch']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('department', __('Department'),['class'=>'form-label'])}}
                                            {{ Form::select('department', $department,isset($_GET['department'])?$_GET['department']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">

                                        <a href="#" class="btn btn-sm btn-primary me-1" onclick="document.getElementById('report_leave').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{route('report.leave')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
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
    <div id="printableArea" class="">
        <div class="row align-items-center mb-4">
            <div class="col-xl-3 mb-4 mb-xl-0">
                <div class="row gy-3">
                    <div class="col-xl-12 col-sm-6 col-12">
                        <div class="card report-card mb-0">
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
                                <div class="report-info">
                                    <input type="hidden"
                                        value="{{ $filterYear['branch'] . ' ' . __('Branch') . ' ' . $filterYear['dateYearRange'] . ' ' . $filterYear['type'] . ' ' . __('Leave Report of') . ' ' . $filterYear['department'] . ' ' . 'Department' }}"
                                        id="filename">
                                    <h5 class="mb-1">{{ __('Report') }}</h5>
                                    <p class="text-muted mb-0">{{ $filterYear['type'] . ' ' . __('Leave Summary') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($filterYear['branch'] != 'All')
                        <div class="col-xl-12 col-sm-6 col-12">
                            <div class="card report-card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M12.9998 0.8125C8.61229 0.8125 5.04541 4.38344 5.04541 8.76687C5.04541 13.1909 7.88916 17.4322 10.1195 20.0769C8.12072 20.4262 6.2276 21.2103 6.2276 22.5103C6.2276 24.2491 9.71729 25.1875 12.9998 25.1875C16.2823 25.1875 19.772 24.2491 19.772 22.5103C19.772 21.2103 17.8788 20.4262 15.8801 20.0769C18.1104 17.4322 20.9542 13.1909 20.9542 8.76687C20.9542 4.38344 17.3873 0.8125 12.9998 0.8125ZM12.9998 21.9538C11.6673 20.6781 5.85791 14.7794 5.85791 8.76687C5.85791 4.83031 9.05916 1.625 12.9998 1.625C16.9404 1.625 20.1417 4.83031 20.1417 8.76687C20.1417 14.7753 14.3323 20.6781 12.9998 21.9538Z"
                                                fill="white" />
                                            <path
                                                d="M13.8168 3.25C13.8127 3.25 13.8046 3.24594 13.8005 3.24594C12.6549 3.07938 11.5621 3.26219 10.5668 3.7375C8.6168 4.66781 7.35742 6.66656 7.35742 8.83188C7.35742 9.08781 7.37773 9.34781 7.4143 9.59563V9.59969C7.52398 10.4122 7.80836 11.18 8.23492 11.8544C8.66148 12.5288 9.2343 13.1138 9.92086 13.5566C10.839 14.1538 11.8993 14.4706 13.0002 14.4706C13.374 14.4706 13.7437 14.43 14.1093 14.3569C14.1905 14.3406 14.2677 14.3203 14.349 14.3C14.6821 14.2188 15.0071 14.1131 15.3199 13.9709C16.043 13.6459 16.6971 13.1666 17.2334 12.5613C17.7818 11.9397 18.1962 11.1881 18.4237 10.3878C18.5699 9.88 18.643 9.35594 18.643 8.83188C18.643 6.045 16.5671 3.64406 13.8168 3.25ZM16.8637 11.7203C16.5549 11.5213 16.1974 11.3872 15.8399 11.3628C15.3687 11.3263 15.0071 11.4806 14.743 11.5944C14.5602 11.6756 14.4465 11.7244 14.3937 11.6959C14.3165 11.6594 14.2109 11.4806 14.158 11.0947C14.1093 10.7575 13.9184 10.4609 13.638 10.2863C13.3902 10.1359 13.1018 10.0872 12.8134 10.14C12.3218 10.2416 11.6027 10.4853 10.9649 11.0703C10.8187 11.2044 10.2255 11.7934 9.9168 12.545C9.19773 11.9519 8.67773 11.1638 8.40148 10.2781C8.76711 10.4041 9.14898 10.4772 9.53492 10.4772C9.85586 10.4772 10.1768 10.4325 10.4855 10.3391C10.9121 10.2131 11.2493 10.0222 11.493 9.76625C11.818 9.43313 11.9846 8.97813 11.9318 8.55563C11.9115 8.40125 11.8668 8.27531 11.7815 8.14531C11.7002 8.03563 11.6027 7.95844 11.5052 7.88938C11.3915 7.82031 11.2696 7.75938 11.1477 7.7025C10.973 7.61719 10.8187 7.54406 10.7374 7.4425C10.583 7.24344 10.6602 6.89 10.7821 6.61781C10.8349 6.50406 10.8918 6.39438 10.9487 6.28469C11.103 5.99219 11.2615 5.6875 11.3224 5.32188C11.363 5.06594 11.3468 4.68813 11.2534 4.33063C11.8059 4.11531 12.3909 4.00156 13.0002 4.00156H13.0652C12.9962 4.13563 12.9352 4.26969 12.8905 4.42C12.7402 4.91563 12.7971 5.41531 13.049 5.78906C13.2684 6.10188 13.5893 6.28063 13.8696 6.43906C14.1337 6.58531 14.3612 6.71125 14.4262 6.89C14.4871 7.0525 14.4343 7.26781 14.3693 7.51969C14.3327 7.66188 14.3002 7.80406 14.2799 7.94625C14.2109 8.40125 14.3002 8.85625 14.5237 9.22188C14.7755 9.63219 15.198 9.9125 15.653 9.97344C15.9455 10.0141 16.2299 9.96938 16.4818 9.92469C16.7662 9.87594 17.0099 9.83531 17.1765 9.93281C17.2659 9.98156 17.3552 10.0953 17.4487 10.2172C17.4812 10.2619 17.5137 10.3025 17.5543 10.3513C17.5584 10.3553 17.5665 10.3634 17.5705 10.3716C17.408 10.855 17.1684 11.3141 16.8637 11.7203Z"
                                                fill="white" />
                                        </svg>

                                    </div>
                                    <div class="report-info">
                                        <h5 class="mb-1">{{ __('Branch') }}</h5>
                                        <p class="text-muted mb-0">{{ $filterYear['branch'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($filterYear['department'] != 'All')
                        <div class="col-xl-12 col-sm-6 col-12">
                            <div class="card report-card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_4436_1950)">
                                                <path
                                                    d="M4.85177 18.8417C2.16578 18.8417 0 21.0075 0 23.6935C0 24.0438 0.0424662 24.373 0.100857 24.7074C1.44385 25.4081 3.07349 25.8221 4.84646 25.8221C6.61942 25.8221 8.25437 25.4081 9.59206 24.7074C9.65576 24.3783 9.69292 24.0492 9.69292 23.6935C9.72477 21.0287 7.53245 18.8417 4.85177 18.8417Z"
                                                    fill="white" />
                                                <path
                                                    d="M4.85156 17.7217C6.26463 17.7217 7.41015 16.5762 7.41015 15.1631C7.41015 13.75 6.26463 12.6045 4.85156 12.6045C3.43849 12.6045 2.29297 13.75 2.29297 15.1631C2.29297 16.5762 3.43849 17.7217 4.85156 17.7217Z"
                                                    fill="white" />
                                                <path
                                                    d="M21.1589 18.8417C18.4729 18.8417 16.3071 21.0075 16.3071 23.6935C16.3071 24.0438 16.3496 24.373 16.408 24.7074C17.751 25.4081 19.3806 25.8221 21.1536 25.8221C22.9266 25.8221 24.5615 25.4081 25.8992 24.7074C25.9629 24.3783 26 24.0492 26 23.6935C26.0107 21.0287 23.8449 18.8417 21.1589 18.8417Z"
                                                    fill="white" />
                                                <path
                                                    d="M21.1587 17.7217C22.5718 17.7217 23.7173 16.5762 23.7173 15.1631C23.7173 13.75 22.5718 12.6045 21.1587 12.6045C19.7456 12.6045 18.6001 13.75 18.6001 15.1631C18.6001 16.5762 19.7456 17.7217 21.1587 17.7217Z"
                                                    fill="white" />
                                                <path
                                                    d="M13.0055 13.4114C14.7785 13.4114 16.4134 12.9973 17.7511 12.2966C17.8148 11.9675 17.852 11.6384 17.852 11.2827C17.852 8.59676 15.6862 6.43098 13.0002 6.43098C10.3142 6.43098 8.14844 8.59676 8.14844 11.2827C8.14844 11.6331 8.1909 11.9622 8.24929 12.2966C9.5976 12.9973 11.2325 13.4114 13.0055 13.4114Z"
                                                    fill="white" />
                                                <path
                                                    d="M13.0054 5.29501C14.4184 5.29501 15.564 4.14949 15.564 2.73642C15.564 1.32335 14.4184 0.177828 13.0054 0.177828C11.5923 0.177828 10.4468 1.32335 10.4468 2.73642C10.4468 4.14949 11.5923 5.29501 13.0054 5.29501Z"
                                                    fill="white" />
                                                <path
                                                    d="M13.7061 17.5412V14.5845C13.7061 14.2925 13.4673 14.043 13.17 14.0484C12.8887 14.059 12.671 14.2819 12.671 14.5633V17.5359L10.2982 19.8875C10.0965 20.0892 10.0912 20.413 10.2876 20.62C10.4946 20.8323 10.845 20.827 11.052 20.6147L13.1647 18.502L15.3092 20.6253C15.5163 20.827 15.8454 20.827 16.0524 20.6253C16.2594 20.4183 16.2594 20.0839 16.0524 19.8822L13.7061 17.5412Z"
                                                    fill="white" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4436_1950">
                                                    <rect width="26" height="26" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>

                                    </div>
                                    <div class="report-info">
                                        <h5 class="mb-1">{{ __('Department') }}</h5>
                                        <p class="text-muted mb-0">{{ $filterYear['department'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-xl-12 col-sm-6 col-12">
                        <div class="card report-card mb-0">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="report-icon">
                                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21.6667 5.22996V2.94866C22.3111 2.57293 22.75 1.8819 22.75 1.08332V0.541684C22.75 0.242277 22.5077 0 22.2083 0H3.79168C3.49228 0 3.25 0.242277 3.25 0.541684V1.08337C3.25 1.8819 3.68885 2.57293 4.33332 2.94871V5.22996C4.33332 7.37653 5.24845 9.43104 6.84384 10.8667L9.21416 13L6.84384 15.1333C5.24845 16.569 4.33332 18.6235 4.33332 20.77V23.0513C3.68885 23.4271 3.25 24.1181 3.25 24.9167V25.4584C3.25 25.7577 3.49228 26 3.79168 26H22.2084C22.5078 26 22.7501 25.7577 22.7501 25.4583V24.9166C22.7501 24.1181 22.3112 23.4271 21.6667 23.0513V20.77C21.6667 18.6235 20.7516 16.569 19.1562 15.1333L16.7858 13L19.1562 10.8667C20.7516 9.43104 21.6667 7.37648 21.6667 5.22996ZM17.7068 9.25646L14.442 12.1949C14.2135 12.4002 14.0833 12.6927 14.0833 13C14.0833 13.3073 14.2135 13.5999 14.442 13.8051L17.7068 16.7435C18.8462 17.7692 19.5 19.2371 19.5 20.77V22.75H18.1456L13.4332 16.4669C13.229 16.1939 12.7709 16.1939 12.5667 16.4669L7.85444 22.75H6.5V20.77C6.5 19.2371 7.15381 17.7692 8.29324 16.7435L11.5581 13.805C11.7866 13.5998 11.9167 13.3073 11.9167 12.9999C11.9167 12.6926 11.7866 12.4001 11.5581 12.1949L8.29324 9.25641C7.15381 8.23078 6.5 6.76289 6.5 5.22996V3.25H19.5V5.22996C19.5 6.76289 18.8462 8.23078 17.7068 9.25646Z"
                                            fill="white" />
                                        <path
                                            d="M16.7337 7.58332H9.26621C9.05197 7.58332 8.85783 7.70976 8.77109 7.90547C8.68436 8.10174 8.72082 8.33026 8.86524 8.48895L12.6368 11.9685C12.74 12.0622 12.8701 12.1087 13.0002 12.1087C13.1303 12.1087 13.2605 12.0621 13.3636 11.9685L17.1346 8.48895C17.279 8.33026 17.3155 8.10174 17.2288 7.90547C17.1421 7.70976 16.9479 7.58332 16.7337 7.58332Z"
                                            fill="white" />
                                    </svg>

                                </div>
                                <div class="report-info">
                                    <h5 class="mb-1">{{ __('Duration') }}</h5>
                                    <p class="text-muted mb-0">{{ $filterYear['dateYearRange'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="row gy-4">
                    <div class="col-md-4 col-sm-6 col-12 leave-card">
                        <div class="leave-card-inner d-flex align-items-center gap-3">
                            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                    fill="#FF3A6E"></path>
                            </svg>

                            <div class="leave-icon">
                                <div class="leave-icon-inner">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_766_1759)">
                                        <path d="M5.3125 9.45312C2.40473 9.45312 0.0390625 11.8188 0.0390625 14.7266C0.0390625 17.6343 2.40473 20 5.3125 20C8.22027 20 10.5859 17.6343 10.5859 14.7266C10.5859 11.8188 8.22027 9.45312 5.3125 9.45312ZM7.64086 14.0862L4.90648 16.8205C4.79211 16.935 4.64215 16.9922 4.49219 16.9922C4.34223 16.9922 4.19227 16.935 4.07789 16.8205L2.90602 15.6487C2.67719 15.4199 2.67719 15.0489 2.90602 14.82C3.1348 14.5913 3.50582 14.5913 3.73465 14.82L4.49219 15.5776L6.81227 13.2575C7.04105 13.0288 7.41207 13.0288 7.6409 13.2575C7.86969 13.4864 7.86969 13.8574 7.64086 14.0862Z" fill="#FF3A6E"/>
                                        <path d="M15.4688 5.07812C15.1452 5.07812 14.8828 4.81578 14.8828 4.49219V0H6.48438C5.51512 0 4.72656 0.788555 4.72656 1.75781V8.30816C4.91961 8.29066 5.11496 8.28125 5.3125 8.28125C7.30969 8.28125 9.09754 9.19437 10.2807 10.625H16.6406C16.9642 10.625 17.2266 10.8873 17.2266 11.2109C17.2266 11.5345 16.9642 11.7969 16.6406 11.7969H11.0527C11.4189 12.5116 11.6551 13.3033 11.7309 14.1406H16.6406C16.9642 14.1406 17.2266 14.403 17.2266 14.7266C17.2266 15.0502 16.9642 15.3125 16.6406 15.3125H11.7309C11.5557 17.2476 10.5218 18.9385 9.01398 20H18.2031C19.1724 20 19.9609 19.2114 19.9609 18.2422V5.07812H15.4688ZM16.6406 8.28125H8.04688C7.72328 8.28125 7.46094 8.01891 7.46094 7.69531C7.46094 7.37172 7.72328 7.10938 8.04688 7.10938H16.6406C16.9642 7.10938 17.2266 7.37172 17.2266 7.69531C17.2266 8.01891 16.9642 8.28125 16.6406 8.28125Z" fill="#FF3A6E"/>
                                        <path d="M16.0547 0.343018V3.90618H19.6176L16.0547 0.343018Z" fill="#FF3A6E"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_766_1759">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                        </defs>
                                        </svg>

                                </div>
                            </div>
                            <div class="leave-info">
                                <h5 class="mb-2">{{ __('Approved Leaves') }}</h5>
                                <span class="h3">{{ $filter['totalApproved'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 leave-card">
                        <div class="leave-card-inner d-flex align-items-center gap-3">
                            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                    fill="#FF3A6E"></path>
                            </svg>

                            <div class="leave-icon">
                                <div class="leave-icon-inner">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_766_1748)">
                                        <path d="M15.4688 5.07812C15.1452 5.07812 14.8828 4.81578 14.8828 4.49219V0H6.48438C5.51512 0 4.72656 0.788555 4.72656 1.75781V8.30816C4.91961 8.29066 5.11496 8.28125 5.3125 8.28125C7.30969 8.28125 9.09754 9.19438 10.2807 10.625H16.6406C16.9642 10.625 17.2266 10.8873 17.2266 11.2109C17.2266 11.5345 16.9642 11.7969 16.6406 11.7969H11.0527C11.4189 12.5116 11.6551 13.3033 11.7309 14.1406H16.6406C16.9642 14.1406 17.2266 14.403 17.2266 14.7266C17.2266 15.0502 16.9642 15.3125 16.6406 15.3125H11.7309C11.5557 17.2476 10.5218 18.9385 9.01398 20H18.2031C19.1724 20 19.9609 19.2114 19.9609 18.2422V5.07812H15.4688ZM16.6406 8.28125H8.04688C7.72328 8.28125 7.46094 8.01891 7.46094 7.69531C7.46094 7.37172 7.72328 7.10938 8.04688 7.10938H16.6406C16.9642 7.10938 17.2266 7.37172 17.2266 7.69531C17.2266 8.01891 16.9642 8.28125 16.6406 8.28125Z" fill="#0CAF60"/>
                                        <path d="M16.0547 0.343018V3.90618H19.6176L16.0547 0.343018Z" fill="#0CAF60"/>
                                        <path d="M5.3125 9.45312C2.40473 9.45312 0.0390625 11.8188 0.0390625 14.7266C0.0390625 17.6343 2.40473 20 5.3125 20C8.22027 20 10.5859 17.6343 10.5859 14.7266C10.5859 11.8188 8.22027 9.45312 5.3125 9.45312ZM7.48461 16.0701C7.71344 16.2989 7.71344 16.6699 7.48461 16.8987C7.37023 17.0131 7.22027 17.0703 7.07031 17.0703C6.92035 17.0703 6.77039 17.0131 6.65602 16.8987L5.3125 15.5552L3.96898 16.8987C3.85461 17.0131 3.70465 17.0703 3.55469 17.0703C3.40473 17.0703 3.25477 17.0131 3.14039 16.8987C2.91156 16.6699 2.91156 16.2989 3.14039 16.07L4.48387 14.7266L3.14035 13.383C2.91152 13.1543 2.91152 12.7832 3.14035 12.5544C3.36914 12.3256 3.74016 12.3256 3.96898 12.5544L5.3125 13.8979L6.65602 12.5544C6.8848 12.3256 7.25582 12.3256 7.48465 12.5544C7.71348 12.7832 7.71348 13.1542 7.48465 13.383L6.14113 14.7266L7.48461 16.0701Z" fill="#0CAF60"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_766_1748">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                        </defs>
                                        </svg>

                                </div>
                            </div>
                            <div class="leave-info">
                                <h5 class="mb-2">{{ __('Rejected Leave') }}</h5>
                                <span class="h3">{{ $filter['totalReject'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 leave-card">
                        <div class="leave-card-inner d-flex align-items-center gap-3">
                            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                    fill="#FF3A6E"></path>
                            </svg>
                            <div class="leave-icon">
                                <div class="leave-icon-inner">
                                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.9995 0.8125C10.5891 0.8125 8.23273 1.52728 6.22851 2.86646C4.22428 4.20564 2.66218 6.10907 1.73974 8.33605C0.817292 10.563 0.575939 13.0135 1.0462 15.3777C1.51645 17.7418 2.6772 19.9134 4.38165 21.6179C6.08611 23.3223 8.25771 24.4831 10.6219 24.9533C12.986 25.4236 15.4365 25.1822 17.6635 24.2598C19.8904 23.3373 21.7939 21.7752 23.1331 19.771C24.4722 17.7668 25.187 15.4105 25.187 13C25.1837 9.76869 23.8986 6.67067 21.6137 4.38579C19.3288 2.10091 16.2308 0.815809 12.9995 0.8125ZM17.412 17.4125C17.2362 17.5881 16.998 17.6867 16.7495 17.6867C16.5011 17.6867 16.2628 17.5881 16.087 17.4125L12.337 13.6625C12.1612 13.4869 12.0622 13.2486 12.062 13V5.5C12.062 5.25136 12.1608 5.0129 12.3366 4.83709C12.5124 4.66127 12.7509 4.5625 12.9995 4.5625C13.2482 4.5625 13.4866 4.66127 13.6624 4.83709C13.8382 5.0129 13.937 5.25136 13.937 5.5V12.6125L17.412 16.0875C17.5876 16.2633 17.6862 16.5016 17.6862 16.75C17.6862 16.9984 17.5876 17.2367 17.412 17.4125Z"
                                            fill="white" />
                                    </svg>
                                </div>
                            </div>
                            <div class="leave-info">
                                <h5 class="mb-2">{{ __('Pending Leaves') }}</h5>
                                <span class="h3">{{ $filter['totalPending'] }}</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive py-4">
                            <table class="table mb-0" id="report-dataTable">
                                <thead>
                                <tr>
                                    <th>{{__('Employee ID')}}</th>
                                    <th>{{__('Employee')}}</th>
                                    <th>{{__('Approved Leaves')}}</th>
                                    <th>{{__('Rejected Leaves')}}</th>
                                    <th>{{__('Pending Leaves')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($leaves as $leave)
                                    <tr>
                                        <td><a href="#" class="btn btn-sm btn-outline-primary">{{ \Auth::user()->employeeIdFormat($leave['employee_id']) }}</a></td>

                                        <td>{{$leave['employee']}}</td>
                                        <td>
                                            <div class="m-view-btn badge bg-info p-2 px-3 rounded">{{$leave['approved']}}
                                                <a href="#" class="text-white text-decoration-none" data-url="{{ route('report.employee.leave',[$leave['id'],'Approved',isset($_GET['type']) ?$_GET['type']:'no',isset($_GET['month'])?$_GET['month']:date('Y-m'),isset($_GET['year'])?$_GET['year']:date('Y')]) }}" data-ajax-popup="true" data-title="{{__('Approved Leave Detail')}}" data-bs-toggle="tooltip" title="{{__('View')}}" data-original-title="{{__('View')}}">{{__('View')}}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="m-view-btn badge bg-danger p-2 px-3 rounded">{{$leave['reject']}}
                                                <a href="#" class="text-white text-decoration-none" data-url="{{ route('report.employee.leave',[$leave['id'],'Reject',isset($_GET['type']) ?$_GET['type']:'no',isset($_GET['month'])?$_GET['month']:date('Y-m'),isset($_GET['year'])?$_GET['year']:date('Y')]) }}" class="table-action table-action-delete" data-ajax-popup="true" data-title="{{__('Rejected Leave Detail')}}" data-bs-toggle="tooltip" title="{{__('View')}}" data-original-title="{{__('View')}}">{{__('View')}}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="m-view-btn badge bg-warning p-2 px-3 rounded">{{$leave['pending']}}
                                                <a href="#" class="text-white text-decoration-none" data-url="{{ route('report.employee.leave',[$leave['id'],'Pending',isset($_GET['type']) ?$_GET['type']:'no',isset($_GET['month'])?$_GET['month']:date('Y-m'),isset($_GET['year'])?$_GET['year']:date('Y')]) }}" class="table-action table-action-delete" data-ajax-popup="true" data-title="{{__('Pending Leave Detail')}}" data-bs-toggle="tooltip" title="{{__('View')}}" data-original-title="{{__('View')}}">{{__('View')}}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

