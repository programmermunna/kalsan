@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Holiday') }}
@endsection
@php
    $setting = Utility::settings();
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Holiday') }}</li>
@endsection

@section('action-btn')
    @can('create holiday')
        <div class="float-end">
            <a href="{{ route('holiday.index') }}" class="btn btn-sm bg-light-blue-subtitle me-1" data-bs-toggle="tooltip"
                title="{{ __('List View') }}" data-original-title="{{ __('List View') }}">
                <i class="ti ti-list"></i>
            </a>
            <a href="#" data-size="lg" data-url="{{ route('holiday.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create New Holiday') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['holiday.calender'], 'method' => 'get', 'id' => 'holiday_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box"></div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box"></div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'month-btn form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control ']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary me-1"
                                            onclick="document.getElementById('holiday_filter').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{ route('holiday.calender') }}" class="btn btn-sm btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
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

    <div class="row">
        <div class="col-lg-7 mb-4">
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
                            <input type="hidden" id="holiday_calendar" value="{{ url('/') }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Holiday List') }}</h5>
                </div>
                <div class="card-body task-calendar-scroll">
                    <div class="event-wrp holiday-wrp">
                        @if (!$holidays->isEmpty())
                            @foreach ($holidays as $holiday)
                                <div class="event-item d-flex align-items-center">
                                    <div class="event-content flex-1">
                                        <h6 class="mb-2">
                                            {{ $holiday->occasion }}
                                        </h6>
                                        <div class="date-wrp d-flex flex-wrap align-items-center">
                                            <div class="date">
                                                <span
                                                    class="f-w-600">{{ __('Start Date : ') }}</span>{{ \Auth::user()->dateFormat($holiday->date) }}
                                            </div>
                                            <div class="date">
                                                <span
                                                    class="f-w-600">{{ __('End Date : ') }}</span>{{ \Auth::user()->dateFormat($holiday->end_date) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-btns d-flex flex-column gap-2">
                                        @can('edit interview schedule')
                                            <a href="#" data-url="{{ route('holiday.edit', $holiday->id) }}"
                                                data-title="{{ __('Edit Interview Schedule') }}" data-ajax-popup="true"
                                                class="btn btn-sm bg-info shadow-sm d-flex" data-bs-toggle="tooltip"
                                                title="{{ __('Edit') }}" data-original-title="{{ __('Edit') }}"><i
                                                    class="ti ti-pencil text-white"></i></a>
                                        @endcan
                                        @can('delete interview schedule')
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['holiday.destroy', $holiday->id],
                                                'id' => 'delete-form-' . $holiday->id,
                                            ]) !!}
                                            <a href="#"
                                                class="btn btn-sm bs-pass-para bg-danger shadow-sm d-flex"
                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                data-original-title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $holiday->id }}').submit();"><i
                                                    class="ti ti-trash text-white"></i></a>
                                            {!! Form::close() !!}
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                <h6>{{ __('There is no holiday in this month') }}</h6>
                            </div>
                        @endif
                    </div>
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
                url: $("#holiday_calendar").val() + "/holiday/get_holiday_data",
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
                                right: 'timeGridDay,timeGridWeek,dayGridMonth'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                dayGridMonth: "{{ __('Month') }}"
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
                            events: data,
                        });
                        calendar.render();
                    })();
                }
            });
        }
    </script>
@endpush
