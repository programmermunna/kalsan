@extends('layouts.admin')

@section('page-title')
    {{ __('Task Calendar') }}
@endsection
@php
    $setting = \App\Models\Utility::settings();
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Task Calendar') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>{{ __('Calendar') }}</h5>
                        </div>
                        <div class="col-lg-6">
                            @if (isset($setting['google_calendar_enable']) && $setting['google_calendar_enable'] == 'on')
                                <select class="form-control" name="calender_type" id="calender_type" onchange="get_data()">
                                    <option value="goggle_calender">{{ __('Google Calender') }}</option>
                                    <option value="local_calender" selected="true">{{ __('Local Calender') }}</option>
                                </select>
                            @endif
                            <input type="hidden" id="task_calendar" value="{{ url('/') }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Tasks') }}</h5>
                </div>
                <div class="card-body task-calendar-scroll">
                    <ul class="task-item-wrp m-0">
                        @forelse($arrTasks as $task)
                            <li class="task-item d-flex align-items-center gap-3">
                                <div class="task-item-info flex-1">
                                    <h5>{{ $task['title'] }}</h5>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span>{{ $task['start'] }} to</span>
                                        <span>{{ $task['end'] }}</span>
                                    </div>
                                </div>
                                <div class="task-item-icon">
                                    <i class="f-20 ti ti-calendar-event text-white"></i>
                            </div>
                            </li>
                        @empty
                            <h6 class="text-dark text-center mb-0">{{ __('No Data Found') }}</h6>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

        function get_data() {
            var calender_type = $('#calender_type :selected').val();
            $('#calendar').removeClass('local_calender');
            $('#calendar').removeClass('goggle_calender');
            if (calender_type == undefined) {
                $('#calendar').addClass('local_calender');
            }
            $('#calendar').addClass(calender_type);
            $.ajax({
                url: $("#task_calendar").val() + "/calendar/get_task_data",

                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'calender_type': calender_type
                },
                success: function(data) {
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
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                dayGridMonth: "{{ __('Month') }}"
                            },
                            themeSystem: 'bootstrap',
                            slotDuration: '00:10:00',
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            events: data,
                        });

                        calendar.render();
                    })();
                }
            });
        }
    </script>
@endpush
