@extends('layouts.admin')

@section('page-title')
    {{__('Manage Zoom-Meeting')}}
@endsection
@php
    $setting = \App\Models\Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Zoom Meeting')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('zoom-meeting.index') }}" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip" title="{{__('List View')}}" data-original-title="{{__('List View')}}">
            <i class="ti ti-list"></i>
        </a>
        @can('create zoom meeting')
        <a href="#"
         data-size="lg" data-url="{{ route('zoom-meeting.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create New Meeting')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
        @endcan
    </div>
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function()
        {
            get_data();
        });
        function get_data()
        {
            var calender_type=$('#calender_type :selected').val();

            $('#calendar').removeClass('local_calender');
            $('#calendar').removeClass('goggle_calender');
            if(calender_type==undefined){
                $('#calendar').addClass('local_calender');
            }
            $('#calendar').addClass(calender_type);
            $.ajax({
                url: $("#zoom_calendar").val()+"/zoom-meeting/get_zoom_meeting_data" ,
                method:"POST",
                data: {"_token": "{{ csrf_token() }}",'calender_type':calender_type},
                success: function(data) {
                    (function () {
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
                                timeGridDay: "{{__('Day')}}",
                                timeGridWeek: "{{__('Week')}}",
                                dayGridMonth: "{{__('Month')}}"
                            },
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                            },
                            themeSystem: 'bootstrap',
                            // slotDuration: '00:10:00',
                            allDaySlot:false,
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            height: 'auto',
                            timeFormat: 'H(:mm)',
                            events: data,
                        });
                        calendar.render();
                    })();
                }
            });
        }
    </script>

@endpush


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
                                    <option value="goggle_calender">{{__('Google Calender')}}</option>
                                    <option value="local_calender" selected="true">{{__('Local Calender')}}</option>
                                </select>
                            @endif
                            <input type="hidden" id="zoom_calendar" value="{{url('/')}}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar' data-toggle="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{__('Mettings')}}</h5>
                </div>
                <div class="card-body task-calendar-scroll">
                    <ul class="task-item-wrp m-0">
                        @foreach ($calandar as $event)
                            @php
                                $month = date('m', strtotime($event['start']));
                            @endphp
                            @if ($month == date('m'))
                                <li class="task-item d-flex align-items-center gap-3">
                                    <div class="task-item-info flex-1">
                                        <h5>
                                            <a href="#!" data-size="lg" data-url="{{$event['url']}}" data-ajax-popup="true" class="dashboard-link">{{$event['title']}}</a>
                                        </h5>
                                        <span class="text-muted">{{ $event['start'] }}</span>
                                    </div>
                                    <div class="task-item-icon">
                                        <i class="f-20 ti ti-video text-white"></i>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

