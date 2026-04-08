@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection
@push('script-page')
    <script>
        (function () {
            var options = {
                chart: {
                    height: 180,
                    type: 'area',
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                series: [{
                    name: 'Refferal',
                    data:{!! json_encode(array_values($home_data['task_overview'])) !!}
                },],
                xaxis: {
                    categories:{!! json_encode(array_keys($home_data['task_overview'])) !!},
                },
                colors: ['#3ec9d6'],
                fill: {
                    type: 'solid',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                // markers: {
                //     size: 4,
                //     colors: ['#3ec9d6', '#FF3A6E',],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // }
            };
            var chart = new ApexCharts(document.querySelector("#task_overview"), options);
            chart.render();
        })();

        (function () {
            var options = {
                chart: {
                    height: 300,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                colors: ["#3ec9d6"],
                dataLabels: {
                    enabled: true,
                    offsetX: -6,
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                grid: {
                    strokeDashArray: 4,
                },
                series: [{
                    data: {!! json_encode(array_values($home_data['timesheet_logged'])) !!}
                }],
                xaxis: {
                    categories: {!! json_encode(array_keys($home_data['timesheet_logged'])) !!},
                },
            };
            var chart = new ApexCharts(document.querySelector("#timesheet_logged"), options);
            chart.render();
        })();

    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Project')}}</li>
@endsection
@section('content')
<div class="row mb-4 gy-3">
    <div class="col-xxl-4 col-md-6 col-12 project-dash-card">
        <div class="project-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="project-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_37_145)">
                        <path
                            d="M9.14062 11.2969C9.52828 11.2969 9.84375 11.6123 9.84375 12C9.84375 12.3877 9.52828 12.7031 9.14062 12.7031C8.75297 12.7031 8.4375 12.3877 8.4375 12C8.4375 11.6123 8.75297 11.2969 9.14062 11.2969Z"
                            fill="white" />
                        <path
                            d="M9.84375 8.55469V10.0116C9.62391 9.93328 9.38719 9.89062 9.14062 9.89062C7.97766 9.89062 7.03125 10.837 7.03125 12C7.03125 13.163 7.97766 14.1094 9.14062 14.1094C9.38719 14.1094 9.62391 14.0667 9.84375 13.9884V15.4453C9.61359 15.4922 9.37828 15.5156 9.14062 15.5156C7.20234 15.5156 5.625 13.9383 5.625 12C5.625 10.0617 7.20234 8.48438 9.14062 8.48438C9.37828 8.48438 9.61359 8.50781 9.84375 8.55469Z"
                            fill="white" />
                        <path
                            d="M4.21875 12C4.21875 14.7141 6.42656 16.9219 9.14062 16.9219C9.37734 16.9219 9.61219 16.9055 9.84375 16.8717V21.1406H7.73438C7.34625 21.1406 7.03125 20.8256 7.03125 20.4375V19.4419C6.45609 19.2783 5.90156 19.0467 5.37328 18.75L4.66594 19.4578C4.39125 19.7325 3.94641 19.7325 3.67172 19.4578L1.68281 17.4689C1.40803 17.1941 1.40911 16.7484 1.68281 16.4747L2.39062 15.7669C2.09344 15.2391 1.86234 14.6845 1.69875 14.1094H0.703125C0.315 14.1094 0 13.7944 0 13.4062V10.5938C0 10.2056 0.315 9.89062 0.703125 9.89062H1.69875C1.86234 9.31547 2.09344 8.76094 2.39062 8.23266L1.68281 7.52531C1.40911 7.25161 1.40803 6.80588 1.68281 6.53109L3.67172 4.54219C3.94641 4.2675 4.39125 4.2675 4.66594 4.54219L5.37375 5.25C5.90156 4.95281 6.45609 4.72172 7.03125 4.55812V3.5625C7.03125 3.17437 7.34625 2.85938 7.73438 2.85938H9.84375V7.12828C9.61219 7.09453 9.37734 7.07812 9.14062 7.07812C6.42656 7.07812 4.21875 9.28594 4.21875 12Z"
                            fill="white" />
                        <path
                            d="M19.7812 3.27094V7.07812H23.5884L19.7812 3.27094ZM13.3594 7.07812H16.2656C16.6537 7.07812 16.9688 7.39313 16.9688 7.78125C16.9688 8.16937 16.6537 8.48438 16.2656 8.48438H13.3594C12.9713 8.48438 12.6562 8.16937 12.6562 7.78125C12.6562 7.39313 12.9713 7.07812 13.3594 7.07812ZM20.4844 16.9219H13.3594C12.9713 16.9219 12.6562 16.6069 12.6562 16.2188C12.6562 15.8306 12.9713 15.5156 13.3594 15.5156H20.4844C20.8725 15.5156 21.1875 15.8306 21.1875 16.2188C21.1875 16.6069 20.8725 16.9219 20.4844 16.9219ZM20.4844 14.1094H13.3594C12.9713 14.1094 12.6562 13.7944 12.6562 13.4062C12.6562 13.0181 12.9713 12.7031 13.3594 12.7031H20.4844C20.8725 12.7031 21.1875 13.0181 21.1875 13.4062C21.1875 13.7944 20.8725 14.1094 20.4844 14.1094ZM20.4844 11.2969H13.3594C12.9713 11.2969 12.6562 10.9819 12.6562 10.5938C12.6562 10.2056 12.9713 9.89062 13.3594 9.89062H20.4844C20.8725 9.89062 21.1875 10.2056 21.1875 10.5938C21.1875 10.9819 20.8725 11.2969 20.4844 11.2969ZM19.0781 8.48438C18.69 8.48438 18.375 8.16937 18.375 7.78125V2.85938H11.25V21.1406H23.2969C23.685 21.1406 24 20.8256 24 20.4375V8.48438H19.0781Z"
                            fill="white" />
                    </g>
                    <defs>
                        <clipPath id="clip0_37_145">
                            <rect width="24" height="24" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="project-content d-flex align-items-start gap-2 justify-content-between flex-1">
                <div class="project-content-left">
                    <h2 class="h5 mb-2">
                        <a href="{{ route('projects.index') }}" class="dashboard-link">{{ __('Total Projects') }}</a>
                    </h2>
                    <h3 class="h5 mb-0">{{ $home_data['total_project']['total'] }}</h3>
                </div>
                <div class="project-right">
                    <div class="d-flex align-items-center gap-2">
                        <span>{{ $home_data['total_project']['percentage'] }}%</span>
                        <p class="mb-0">{{ __('Completed') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-12 project-dash-card">
        <div class="project-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="project-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M20.5243 2.02491C20.1263 1.626 19.5968 1.40625 19.0336 1.40625H18.0987V2.34375C18.0987 3.37762 17.2585 4.21875 16.2257 4.21875H7.79761C6.76486 4.21875 5.92467 3.37762 5.92467 2.34375V1.40625H4.98666C3.82613 1.40625 2.88094 2.35144 2.87963 3.51328L2.85938 21.8883C2.85872 22.4521 3.07763 22.9824 3.47564 23.3813C3.87375 23.7803 4.40316 24 4.96641 24H19.0133C20.1738 24 21.1191 23.0548 21.1203 21.893L21.1406 3.51797C21.1413 2.95411 20.9224 2.42386 20.5243 2.02491ZM12.2388 8.4375H17.2288C17.6167 8.4375 17.9311 8.75231 17.9311 9.14062C17.9311 9.52894 17.6167 9.84375 17.2288 9.84375H12.2388C11.8509 9.84375 11.5365 9.52894 11.5365 9.14062C11.5365 8.75231 11.8509 8.4375 12.2388 8.4375ZM12.2388 13.125H17.2288C17.6167 13.125 17.9311 13.4398 17.9311 13.8281C17.9311 14.2164 17.6167 14.5312 17.2288 14.5312H12.2388C11.8509 14.5312 11.5365 14.2164 11.5365 13.8281C11.5365 13.4398 11.8509 13.125 12.2388 13.125ZM12.2388 17.8125H17.2488C17.6367 17.8125 17.9512 18.1273 17.9512 18.5156C17.9512 18.9039 17.6367 19.2188 17.2488 19.2188H12.2388C11.8509 19.2188 11.5365 18.9039 11.5365 18.5156C11.5365 18.1273 11.8509 17.8125 12.2388 17.8125ZM6.25458 8.46525C6.52889 8.19066 6.97355 8.19066 7.24786 8.46525L7.62975 8.84761L9.24947 7.22611C9.52378 6.95156 9.96844 6.95147 10.2427 7.22611C10.517 7.50066 10.517 7.94587 10.2427 8.22047L8.12635 10.3391C7.99463 10.471 7.81599 10.5451 7.6297 10.5451C7.44342 10.5451 7.26478 10.471 7.13306 10.3391L6.25453 9.45956C5.98027 9.18502 5.98027 8.7398 6.25458 8.46525ZM6.25458 13.5107C6.52889 13.2361 6.97355 13.2361 7.24786 13.5107L7.62975 13.893L9.24947 12.2715C9.52374 11.997 9.96844 11.997 10.2427 12.2715C10.517 12.5461 10.517 12.9913 10.2427 13.2659L8.12635 15.3846C7.99467 15.5165 7.81599 15.5906 7.6297 15.5906C7.44342 15.5906 7.26478 15.5165 7.13306 15.3846L6.25453 14.505C5.98027 14.2305 5.98027 13.7853 6.25458 13.5107ZM6.25458 18.1982C6.52889 17.9236 6.97355 17.9236 7.24786 18.1982L7.62975 18.5805L9.24947 16.959C9.52374 16.6845 9.96844 16.6845 10.2427 16.959C10.517 17.2336 10.517 17.6788 10.2427 17.9534L8.12635 20.0721C7.99467 20.204 7.81599 20.2781 7.6297 20.2781C7.44342 20.2781 7.26478 20.204 7.13306 20.0721L6.25453 19.1925C5.98027 18.918 5.98027 18.4728 6.25458 18.1982Z"
                        fill="white" />
                    <path
                        d="M7.3291 2.34375C7.3291 2.60264 7.53873 2.8125 7.79734 2.8125H16.2255C16.4841 2.8125 16.6937 2.60264 16.6937 2.34375V0.46875C16.6937 0.209859 16.4841 0 16.2255 0H7.79734C7.53873 0 7.3291 0.209859 7.3291 0.46875V2.34375Z"
                        fill="white" />
                </svg>
            </div>
            <div class="project-content d-flex align-items-start gap-2 justify-content-between flex-1">
                <div class="project-content-left">
                    <h2 class="h5 mb-2">
                        <a href="{{ route('taskBoard.view', 'list') }}"
                            class="dashboard-link">{{ __('Total Tasks') }}</a>
                    </h2>
                    <h3 class="h5 mb-0">{{ $home_data['total_task']['total'] }}</h3>
                </div>

                <div class="project-right">
                    <div class="d-flex align-items-center gap-2">
                        <span>{{ $home_data['total_task']['percentage'] }}%</span>
                        <p class="mb-0">{{ __('Completed') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-12 project-dash-card">
        <div class="project-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="project-icon">
                <svg width="23" height="23" viewBox="0 0 23 23" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14.375 16.125C14.375 15.4759 14.375 13.5 16.25 13.5H20V11.25C20 10.2161 19.1589 9.375 18.125 9.375H2C0.966125 9.375 0.125 10.2161 0.125 11.25V21C0.125 22.0339 0.966125 22.875 2 22.875H18.125C19.1589 22.875 20 22.0339 20 21V18.75H16.25C14.375 18.75 14.375 16.7741 14.375 16.125Z"
                        fill="white" />
                    <path
                        d="M15.1245 8.625V6.35175C15.1245 5.96437 14.9224 5.613 14.5838 5.412C14.2328 5.2035 13.809 5.19637 13.4509 5.39175L6.9668 8.625H15.1245Z"
                        fill="white" />
                    <path
                        d="M17.375 16.875C17.7892 16.875 18.125 16.5392 18.125 16.125C18.125 15.7108 17.7892 15.375 17.375 15.375C16.9608 15.375 16.625 15.7108 16.625 16.125C16.625 16.5392 16.9608 16.875 17.375 16.875Z"
                        fill="white" />
                    <path
                        d="M11.7499 5.40225V3.80587C11.7499 3.2355 11.3989 2.87325 11.0708 2.72437C10.6726 2.54362 10.2361 2.61037 9.90307 2.9025L3.37207 8.625H5.28682L11.7499 5.40225Z"
                        fill="white" />
                    <path
                        d="M20.75 14.25H16.25C15.5746 14.25 15.125 14.5691 15.125 16.125C15.125 17.6809 15.5746 18 16.25 18H20.75C21.3703 18 21.875 17.4952 21.875 16.875V15.375C21.875 14.7548 21.3703 14.25 20.75 14.25ZM17.375 17.625C16.5477 17.625 15.875 16.9523 15.875 16.125C15.875 15.2977 16.5477 14.625 17.375 14.625C18.2023 14.625 18.875 15.2977 18.875 16.125C18.875 16.9523 18.2023 17.625 17.375 17.625Z"
                        fill="white" />
                    <path
                        d="M22.1671 3.29925L20.4421 0.97875C20.2186 0.678 19.7679 0.678001 19.5444 0.979126L17.8299 3.288C17.6593 3.45863 17.78 3.75 18.0211 3.75H18.8679C19.097 4.8735 18.5923 6.0195 17.609 6.60938L17.375 6.75L18.875 7.875L18.941 7.83113C20.3052 6.92138 21.125 5.38988 21.125 3.75H21.9804C22.2159 3.75 22.3336 3.46575 22.1671 3.29925Z"
                        fill="white" />
                </svg>
            </div>
            <div class="project-content d-flex align-items-start gap-2 justify-content-between flex-1">
                <div class="project-content-left">
                    <h2 class="h5 mb-2">
                        {{ __('Total Expense') }}
                    </h2>
                    <h3 class="h5 mb-0">{{ $home_data['total_expense']['total'] }}</h3>
                </div>
                <div class="project-right">
                    <div class="d-flex align-items-center gap-2">
                        <span>{{ $home_data['total_expense']['percentage'] }}%</span>
                        <p class="mb-0">{{ __('Completed') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">

                    <h5>{{__('Project Status')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-5">
                        @foreach($home_data['project_status'] as $status => $val)
                            <div class="col-md-6 col-sm-6">
                                <div class="align-items-start">

                                    <div class="ms-2">
                                        <p class="text-sm mb-2">{{__(\App\Models\Project::$project_status[$status])}}</p>
                                        <h3 class="mb-2 text-{{ \App\Models\Project::$status_color[$status] }}">{{ $val['total'] }}%</h3>
                                        <div class="progress mb-0">
                                            <div class="progress-bar bg-{{ \App\Models\Project::$status_color[$status] }}" style="width: {{$val['percentage']}}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Tasks Overview')}} <span class="float-end"> <small class="text-muted">{{__('Total Completed task in last 7 days')}}</small></span></h5>

                </div>
                <div class="card-body">
                    <div id="task_overview"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Top Due Projects')}}</h5>
                </div>
                <div class="card-body project_table">
                    <div class="table-responsive ">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>{{__('Name')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th >{{__('Status')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($home_data['due_project']->count() > 0)
                                @foreach($home_data['due_project'] as $due_project)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{asset(Storage::url('/'.$due_project->project_image ))}}"
                                                    class="wid-40 rounded border-2 border border-primary me-3" >
                                                <div>
                                                    <h6 class="mb-0">{{ $due_project->project_name }}</h6>
                                                    <p class="mb-0"><span class="text-success">{{ \Auth::user()->priceFormat($due_project->budget) }}</p>

                                                </div>
                                            </div>
                                        </td>
                                        <td >{{  Utility::getDateFormated($due_project->end_date) }}</td>
                                        <td class="">
                                            <span class=" status_badge p-2 px-3 rounded badge bg-{{\App\Models\Project::$status_color[$due_project->status]}}">{{ __(\App\Models\Project::$project_status[$due_project->status]) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="py-5">
                                    <td class="text-center mb-0" colspan="3">{{__('No Due Projects Found.')}}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Timesheet Logged Hours')}} <span>  <small class="float-end text-muted flo">{{__('Last 7 days')}}</small></span></h5>
                </div>
                <div class="card-body project_table">
                    <div id="timesheet_logged"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Top Due Tasks')}}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            @foreach($home_data['due_tasks'] as $due_task)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="">
                                                <span class="text-muted d-block mb-2">{{__('Task')}}:</span>
                                                <h6 class="m-0"><a href="{{ route('projects.tasks.index',$due_task->project->id) }}" class="name mb-0 h6">{{ $due_task->name }}</a></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted d-block mb-2">{{__('Project')}}:</span>
                                        <h6 class="m-0">{{$due_task->project->project_name}}</h6>
                                    </td>
                                    <td>

                                        <span class="text-muted d-block mb-2">{{__('Stage')}}:</span>
                                        <div class="d-flex align-items-center h6">
                                            <span class="full-circle bg-{{ \App\Models\ProjectTask::$priority_color[$due_task->priority] }}"></span>
                                            <span class="ms-1">{{ \App\Models\ProjectTask::$priority[$due_task->priority] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted d-block mb-2">{{__('Completion')}}:</span>
                                        <h6 class="m-0">{{ $due_task->taskProgress($due_task)['percentage'] }}</h6>
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
@endsection
