@extends('layouts.admin')

@section('title')
    {{ __('Dashboard') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Client') }}</li>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush

@push('script-page')
    <script>
        @if ($calenderTasks)
            (function() {
                var etitle;
                var etype;
                var etypeclass;
                var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    themeSystem: 'bootstrap',
                    initialDate: '{{ $transdate }}',
                    slotDuration: '00:10:00',
                    navLinks: true,
                    droppable: true,
                    selectable: true,
                    selectMirror: true,
                    editable: true,
                    dayMaxEvents: true,
                    handleWindowResize: true,
                    events: {!! json_encode($calenderTasks) !!},

                });
                calendar.render();
            })();
        @endif

        $(document).on('click', '.fc-day-grid-event', function(e) {
            if (!$(this).hasClass('deal')) {
                e.preventDefault();
                var event = $(this);
                var title = $(this).find('.fc-content .fc-title').html();
                var size = 'md';
                var url = $(this).attr('href');
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);

                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#commonModal .modal-body').html(data);
                        $("#commonModal").modal('show');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('error', data.error, 'error')
                    }
                });
            }
        });
    </script>
    <script>
        (function() {
            var chartBarOptions = {
                series: {!! json_encode($taskData['dataset']) !!},


                chart: {
                    height: 250,
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
                    categories: {!! json_encode($taskData['label']) !!},
                    title: {
                        text: "{{ __('Days') }}"
                    }
                },
                colors: ['#6fd944', '#883617', '#4e37b9', '#8f841b'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#3b6b1d', '#be7713' ,'#2037dc','#cbbb27'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: "{{ __('Amount') }}"
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();



        (function() {
            var options = {
                chart: {
                    height: 140,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: {!! json_encode(array_values($projectData)) !!},
                colors: ["#bd9925", "#2f71bd", "#720d3a", "#ef4917"],
                labels: {!! json_encode($project_status) !!},
                legend: {
                    show: true
                }
            };
            var chart = new ApexCharts(document.querySelector("#chart-doughnut"), options);
            chart.render();
        })();
    </script>
@endpush

@section('content')
    @php

        $project_task_percentage = $project['project_task_percentage'];
        $label = '';
        if ($project_task_percentage <= 15) {
            $label = 'bg-danger';
        } elseif ($project_task_percentage > 15 && $project_task_percentage <= 33) {
            $label = 'bg-warning';
        } elseif ($project_task_percentage > 33 && $project_task_percentage <= 70) {
            $label = 'bg-primary';
        } else {
            $label = 'bg-success';
        }

        $project_percentage = $project['project_percentage'];
        $label1 = '';
        if ($project_percentage <= 15) {
            $label1 = 'bg-danger';
        } elseif ($project_percentage > 15 && $project_percentage <= 33) {
            $label1 = 'bg-warning';
        } elseif ($project_percentage > 33 && $project_percentage <= 70) {
            $label1 = 'bg-primary';
        } else {
            $label1 = 'bg-success';
        }

        $project_bug_percentage = $project['project_bug_percentage'];
        $label2 = '';
        if ($project_bug_percentage <= 15) {
            $label2 = 'bg-danger';
        } elseif ($project_bug_percentage > 15 && $project_bug_percentage <= 33) {
            $label2 = 'bg-warning';
        } elseif ($project_bug_percentage > 33 && $project_bug_percentage <= 70) {
            $label2 = 'bg-primary';
        } else {
            $label2 = 'bg-success';
        }
    @endphp

    <div class="row">
        @if (!empty($arrErr))
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @if (!empty($arrErr['system']))
                    <div class="alert alert-danger text-xs">
                        {{ __('are required in') }} <a href="{{ route('settings') }}" class=""><u>
                                {{ __('System Setting') }}</u></a>
                    </div>
                @endif
                @if (!empty($arrErr['user']))
                    <div class="alert alert-danger text-xs">
                        <a href="{{ route('users') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
                @if (!empty($arrErr['role']))
                    <div class="alert alert-danger text-xs">
                        <a href="{{ route('roles.index') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-xxl-6">
                <div class="row mb-4 gy-4">
                    @if (isset($arrCount['deal']))
                        <div class="col-lg-6 col-md-6 deals-card">
                            <div class="deals-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="deals-icon">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_63_1597)">
                                            <path
                                                d="M12.4261 4.64887C12.8269 4.64887 13.1519 4.32388 13.1519 3.92301V1.52567C13.1519 1.1248 12.8269 0.799805 12.4261 0.799805C12.0252 0.799805 11.7002 1.1248 11.7002 1.52567V3.92301C11.7002 4.32388 12.0252 4.64887 12.4261 4.64887Z"
                                                fill="white"></path>
                                            <path
                                                d="M15.8138 5.88077C15.9996 5.88077 16.1853 5.80987 16.327 5.66814L18.2627 3.7325C18.5461 3.44907 18.5461 2.98945 18.2627 2.70598C17.9792 2.42255 17.5196 2.42255 17.2361 2.70598L15.3005 4.64162C15.017 4.92504 15.017 5.38466 15.3005 5.66814C15.4422 5.80987 15.628 5.88077 15.8138 5.88077Z"
                                                fill="white"></path>
                                            <path
                                                d="M8.52525 5.66814C8.66698 5.80987 8.85276 5.88077 9.03848 5.88077C9.22421 5.88077 9.41003 5.80987 9.55172 5.66814C9.83519 5.38471 9.83519 4.92509 9.55172 4.64162L7.61608 2.70598C7.33265 2.42255 6.87303 2.42255 6.58956 2.70598C6.30608 2.9894 6.30608 3.44902 6.58956 3.7325L8.52525 5.66814Z"
                                                fill="white"></path>
                                            <path
                                                d="M18.9459 11.0591L16.1282 8.24129C15.5836 7.69675 14.8451 7.39087 14.0751 7.39087H9.66765C8.56893 7.39087 7.53603 7.81874 6.75916 8.59561L6.52717 8.82755L6.51217 8.84255H0.763951C0.36308 8.84255 0.0380859 9.16754 0.0380859 9.56841V17.311C0.0380859 17.7118 0.36308 18.0368 0.763951 18.0368H4.38297L6.10602 19.7598C6.24215 19.896 6.42676 19.9725 6.61926 19.9725H7.27651C7.03324 19.5959 6.87665 19.1753 6.81534 18.735C6.27268 18.5585 5.78437 18.2326 5.40828 17.7843C4.31493 16.4812 4.48551 14.5316 5.78853 13.4382L8.5474 11.1233H14.0559C14.2484 11.1233 14.4331 11.1997 14.5692 11.3359L16.5959 13.3626C17.2468 14.0135 18.3261 14.0274 18.9658 13.3655C19.5873 12.7225 19.5806 11.6938 18.9459 11.0591Z"
                                                fill="white"></path>
                                            <path
                                                d="M24.8138 11.0684C24.8138 10.6674 24.4888 10.3425 24.0879 10.3425H20.2425C21.1706 11.5557 21.081 13.3031 19.972 14.4122C19.3875 14.9966 18.6097 15.3185 17.7821 15.3185C16.9543 15.3185 16.1766 14.9966 15.5922 14.4122L13.7549 12.5749H8.93948L8.36929 13.0533L6.52777 14.5985C5.8368 15.1783 5.74669 16.2084 6.32646 16.8994C6.90624 17.5904 7.93638 17.6805 8.62736 17.1007C7.93638 17.6805 7.84628 18.7106 8.42605 19.4016C9.00583 20.0926 10.036 20.1827 10.7269 19.6029C10.036 20.1827 9.94587 21.2128 10.5256 21.9038C11.1054 22.5948 12.1356 22.6849 12.8265 22.1051L14.112 21.0264L13.9387 21.1719C13.2477 21.7517 13.1576 22.7819 13.7374 23.4728C14.3171 24.1638 15.3473 24.2539 16.0382 23.6741L20.969 19.5367H24.088C24.4889 19.5367 24.8139 19.2117 24.8139 18.8109L24.8138 11.0684Z"
                                                fill="white"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_63_1597">
                                                <rect width="24.7762" height="24.7762" fill="white"
                                                    transform="translate(0.0380859 0.0400391)"></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="deals-content d-flex align-items-start gap-2 justify-content-between flex-1">
                                    <div class="deals-content-inner">
                                        <span class="text-sm d-block mb-1">{{ __('Total') }}</span>
                                        <h2 class="h4 mb-0"><a href="{{ route('deals.index') }}"
                                                class="dashboard-link">{{ __('Deal') }}</a></h2>
                                    </div>
                                    <h3 class="h4 m-0">{{ $arrCount['deal'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (isset($arrCount['task']))
                        <div class="col-lg-6 col-md-6 deals-card">
                            <div class="deals-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="deals-icon">
                                    <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M477.184 242.688l-21.504-21.504c-6.144-6.144-15.36-6.144-21.504 0L299.008 356.352l-54.272-54.272c-6.144-6.144-15.36-6.144-21.504 0l-21.504 21.504c-6.144 6.144-6.144 15.36 0 21.504l75.776 75.776c6.144 6.144 14.336 9.216 21.504 9.216 8.192 0 15.36-3.072 21.504-9.216l156.672-156.672c5.12-5.12 5.12-15.36 0-21.504zM788.48 389.12H522.24c-11.264 0-20.48-9.216-20.48-20.48v-40.96c0-11.264 9.216-20.48 20.48-20.48h266.24c11.264 0 20.48 9.216 20.48 20.48v40.96c0 11.264-9.216 20.48-20.48 20.48z m0 184.32H460.8c-11.264 0-20.48-9.216-20.48-20.48v-40.96c0-11.264 9.216-20.48 20.48-20.48h327.68c11.264 0 20.48 9.216 20.48 20.48v40.96c0 11.264-9.216 20.48-20.48 20.48z m-450.56 0h-40.96c-11.264 0-20.48-9.216-20.48-20.48v-40.96c0-11.264 9.216-20.48 20.48-20.48h40.96c11.264 0 20.48 9.216 20.48 20.48v40.96c0 11.264-9.216 20.48-20.48 20.48z m0 184.32h-40.96c-11.264 0-20.48-9.216-20.48-20.48v-40.96c0-11.264 9.216-20.48 20.48-20.48h40.96c11.264 0 20.48 9.216 20.48 20.48v40.96c0 11.264-9.216 20.48-20.48 20.48z m450.56 0H460.8c-11.264 0-20.48-9.216-20.48-20.48v-40.96c0-11.264 9.216-20.48 20.48-20.48h327.68c11.264 0 20.48 9.216 20.48 20.48v40.96c0 11.264-9.216 20.48-20.48 20.48z" />
                                    </svg>
                                </div>
                                <div class="deals-content d-flex align-items-start gap-2 justify-content-between flex-1">
                                    <div class="deals-content-inner">
                                        <span class="text-sm d-block mb-1">{{ __('Total') }}</span>
                                        <h2 class="h4 m-0">{{ __('Deal Task') }}</h2>
                                    </div>
                                    <h3 class="h4 m-0">{{ $arrCount['task'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-xxl-12">
                        <div class="card mb-0">
                            <div class="card-header">
                                <h5>{{ __('Calendar') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id='calendar' class='calendar'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card client-card-wrp">
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-md-4 col-sm-6 total-client-card">
                                        <div class="client-card-content d-flex align-items-center gap-2 mb-3">
                                            <p class="mb-0">{{ __('Total Project') }}</p>
                                            <h3 class="mb-0 h4">{{ $project['project_percentage'] }}%</h3>
                                        </div>
                                        <div class="progress mb-0">
                                            <div class="progress-bar"
                                                style="width: {{ $project['project_percentage'] }}%;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 total-client-card">
                                        <div class="client-card-content d-flex align-items-center gap-2 mb-3">
                                            <p class="mb-0">{{ __('Total Project Tasks') }}</p>
                                            <h3 class="mb-0 h4">{{ $project['projects_tasks_count'] }}%</h3>
                                        </div>
                                        <div class="progress mb-0">
                                            <div class="progress-bar bg-{{ $label1 }}"
                                                style="width: {{ $project['project_task_percentage'] }}%;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 total-client-card">
                                        <div class="client-card-content d-flex align-items-center gap-2 mb-3">
                                            <p class="mb-0">{{ __('Total Bugs') }}</p>
                                            <h3 class="mb-0 h4">{{ $project['projects_bugs_count'] }}%</h3>
                                        </div>
                                        <div class="progress mb-0">
                                            <div class="progress-bar bg-{{ $label1 }}"
                                                style="width: {{ $project['project_bug_percentage'] }}%;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                <h5>{{ __('Tasks Overview') }}</h5>
                                <h6 class="last-day-text mb-0">{{ __('Last 7 Days') }}</h6>
                            </div>
                            <div class="card-body">
                                <div id="chart-sales" height="200" class="p-3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Project Status') }}
                                    <span class="float-end text-muted">{{ __('Year') . ' - ' . $currentYear }}</span>
                                </h5>

                            </div>
                            <div class="card-body">
                                <div id="chart-doughnut"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div
                class="{{ Auth::user()->type == 'company' || Auth::user()->type == 'client' ? 'col-xl-6 col-lg-6 col-md-6' : 'col-xl-8 col-lg-8 col-md-8' }} col-sm-12">
                <div class="card bg-none min-410 mx-410">
                    <div class="card-header">
                        <h5>{{ __('Top Due Project') }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Task Name') }}</th>
                                        <th>{{ __('Remain Task') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @forelse($project['projects'] as $project)
                                        @php
                                            $datetime1 = new DateTime($project->due_date);
                                            $datetime2 = new DateTime(date('Y-m-d'));
                                            $interval = $datetime1->diff($datetime2);
                                            $days = $interval->format('%a');

                                            $project_last_stage = $project->project_last_stage($project->id)
                                                ? $project->project_last_stage($project->id)->id
                                                : '';
                                            $total_task = $project->project_total_task($project->id);
                                            $completed_task = $project->project_complete_task(
                                                $project->id,
                                                $project_last_stage,
                                            );
                                            $remain_task = $total_task - $completed_task;
                                        @endphp
                                        <tr>
                                            <td class="id-web">
                                                {{ $project->project_name }}
                                            </td>
                                            <td>{{ $remain_task }}</td>
                                            <td>{{ Auth::user()->dateFormat($project->end_date) }}</td>
                                            <td>
                                                <div class="action-btn ms-2">
                                                    <a href="{{ route('projects.show', $project->id) }}"
                                                        class="mx-3 bg-warning btn btn-sm align-items-center"><i
                                                            class="ti ti-eye text-white"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="4">{{ __('No Data Found.!') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="card bg-none min-410 mx-410">
                    <div class="card-header">
                        <h5>{{ __('Top Due Task') }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Task Name') }}</th>
                                        <th>{{ __('Assign To') }}</th>
                                        <th>{{ __('Task Stage') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top_tasks as $top_task)
                                        <tr>
                                            <td class="id-web">
                                                {{ $top_task->name }}
                                            </td>
                                            <td>
                                                <div class="avatar-group">
                                                    @if ($top_task->users()->count() > 0)
                                                        @if ($users = $top_task->users())
                                                            @foreach ($users as $key => $user)
                                                                @if ($key < 3)
                                                                    <a href="#"
                                                                        class="avatar rounded-circle avatar-sm">
                                                                        <img data-original-title="{{ !empty($user) ? $user->name : '' }}"
                                                                            @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('assets/img/avatar/avatar-1.png') }}" @endif
                                                                            title="{{ $user->name }}" class="hweb">
                                                                    </a>
                                                                @else
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (count($users) > 3)
                                                            <a href="#" class="avatar rounded-circle avatar-sm">
                                                                <img data-original-title="{{ !empty($user) ? $user->name : '' }}"
                                                                    @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('assets/img/avatar/avatar-1.png') }}" @endif
                                                                    class="hweb">
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{ __('-') }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td><span
                                                    class="p-2 px-3 status_badge rounded badge bg-">{{ $top_task->stage->name }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="4">{{ __('No Data Found.!') }}</td>
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
@endsection
