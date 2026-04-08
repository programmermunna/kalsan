@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection
@push('script-page')
    <script>
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
                url: $("#event_dashboard").val()+"/event/get_event_data" ,
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
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            height: 'auto',
                            timeFormat: 'H(:mm)',
                            {{--events: {!! json_encode($arrEvents) !!},--}}
                            events: data,
                            locale: '{{basename(App::getLocale())}}',
                            dayClick: function (e) {
                                var t = moment(e).toISOString();
                                $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start").val(t), $(".new-event--end").val(t)
                            },
                            eventResize: function (event) {
                                var eventObj = {
                                    start: event.start.format(),
                                    end: event.end.format(),
                                };
                            },
                            viewRender: function (t) {
                                e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
                            },
                            eventClick: function (e, t) {
                                var title = e.title;
                                var url = e.url;

                                if (typeof url != 'undefined') {
                                    $("#commonModal .modal-title").html(title);
                                    $("#commonModal .modal-dialog").addClass('modal-md');
                                    $("#commonModal").modal('show');
                                    $.get(url, {}, function (data) {
                                        $('#commonModal .modal-body').html(data);
                                    });
                                    return false;
                                }
                            }
                        });
                        calendar.render();
                    })();
                }
            });
        }
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('HRM')}}</li>
@endsection
@php
    $setting = \App\Models\Utility::settings();
@endphp
@section('content')
    @if(\Auth::user()->type != 'client' && \Auth::user()->type != 'company')
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xxl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{__('Mark Attandance')}}</h5>
                            </div>
                            <div class="card-body dash-card-body">
                                <p>{{__('My Office Time: '.$officeTime['startTime'].' to '.$officeTime['endTime'])}}</p>
                                <center>
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{Form::open(array('url'=>'attendanceemployee/attendance','method'=>'post'))}}
                                            @if(empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')
                                                <button type="submit" value="0" name="in" id="clock_in" class="btn btn-success ">{{__('CLOCK IN')}}</button>
                                            @else
                                                <button type="submit" value="0" name="in" id="clock_in" class="btn btn-success disabled" disabled>{{__('CLOCK IN')}}</button>
                                            @endif
                                            {{Form::close()}}
                                        </div>
                                        <div class="col-md-6 ">
                                            @if(!empty($employeeAttendance) && $employeeAttendance->clock_out == '00:00:00')
                                                {{Form::model($employeeAttendance,array('route'=>array('attendanceemployee.update',$employeeAttendance->id),'method' => 'PUT')) }}
                                                <button type="submit" value="1" name="out" id="clock_out" class="btn btn-danger">{{__('CLOCK OUT')}}</button>
                                            @else
                                                <button type="submit" value="1" name="out" id="clock_out" class="btn btn-danger disabled" disabled>{{__('CLOCK OUT')}}</button>
                                            @endif
                                            {{Form::close()}}
                                        </div>
                                    </div>
                                </center>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5>{{ __('Event') }}</h5>
                                    </div>
                                    <div class="col-lg-6">
                                        @if (isset($setting['google_calendar_enable']) && $setting['google_calendar_enable'] == 'on')
                                            <select class="form-control" name="calender_type" id="calender_type" onchange="get_data()">
                                                <option value="goggle_calender">{{__('Google Calender')}}</option>
                                                <option value="local_calender" selected="true">{{__('Local Calender')}}</option>
                                            </select>
                                        @endif
                                        <input type="hidden" id="event_dashboard" value="{{url('/')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id='calendar' class='calendar e-height'></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="card list_card">
                            <div class="card-header">
                                <h5>{{__('Announcement List')}}</h5>
                            </div>
                            <div class="card-body dash-card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                        <tr>
                                            <th>{{__('Title')}}</th>
                                            <th>{{__('Start Date')}}</th>
                                            <th>{{__('End Date')}}</th>
                                            <th>{{__('description')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($announcements as $announcement)
                                            <tr>
                                                <td>{{ $announcement->title }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->start_date)  }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>
                                                <td>{{ $announcement->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">
                                                    <div class="text-center">
                                                        <h6>{{__('There is no Announcement List')}}</h6>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card list_card">
                            <div class="card-header">
                                <h5>{{__('Meeting List')}}</h5>
                            </div>
                            <div class="card-body dash-card-body">
                                @if(count($meetings) > 0)
                                    <div class="table-responsive">
                                        <table class="table align-items-center">
                                            <thead>
                                            <tr>
                                                <th>{{__('Meeting title')}}</th>
                                                <th>{{__('Meeting Date')}}</th>
                                                <th>{{__('Meeting Time')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($meetings as $meeting)
                                                <tr>
                                                    <td>{{ $meeting->title }}</td>
                                                    <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                                    <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-2">
                                        {{__('No meeting scheduled yet.')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{__("Today's Not Clock In")}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex gap-1 team-lists horizontal-scroll-cards">
                                    @foreach($notClockIns as $notClockIn)
                                    @php
                                        $user = $notClockIn->user;
                                        $logo= asset(Storage::url('uploads/avatar/'));
                                        $avatar = !empty($notClockIn->user->avatar) ? $notClockIn->user->avatar : 'avatar.png';
                                    @endphp
                                        <div>
                                            <img src="{{ $logo . $avatar }}" alt="" class="rounded border-2 border border-primary">
                                            <p class="mt-2 mb-1 p-0">{{ $notClockIn->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xl-8 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5>{{ __('Event') }}</h5>
                                    </div>
                                    <div class="col-lg-6">

                                        @if(isset($setting['google_calendar_enable']) && $setting['google_calendar_enable'] == 'on')
                                            <select class="form-control" name="calender_type" id="calender_type"  onchange="get_data()">
                                                <option value="goggle_calender">{{__('Google Calender')}}</option>
                                                <option value="local_calender" selected="true">{{__('Local Calender')}}</option>
                                            </select>
                                        @endif
                                        <input type="hidden" id="event_dashboard" value="{{url('/')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id='calendar' class='calendar'></div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-4">
                        <div class="col-xxl-12">
                            <div class="card staff-info-card-wrp">
                                <div class="card-body p-3">
                                    <h4 class="mb-3">{{__('Staff')}}</h4>
                                    <div class="row row-gap-1">
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M11.0423 6.94758C13.2185 7.59464 15.5784 5.86974 15.5442 3.53761C15.3502 -1.15819 8.65015 -1.15791 8.4563 3.53767C8.45631 5.15785 9.5522 6.52972 11.0423 6.94758Z" fill="white"/>
                                                        <path d="M17.7126 12.6242C17.2475 9.89627 14.8625 7.81485 12.0005 7.81485C8.76806 7.79105 6.06201 10.5753 6.22529 13.7359C6.27656 13.8936 6.42636 14 6.59587 14H17.4052C18.0555 13.9452 17.7338 13.0378 17.7126 12.6242Z" fill="white"/>
                                                        <path d="M18.8793 8.12234C20.3063 8.12234 21.4693 6.95941 21.4693 5.53237C21.3392 2.10642 16.4188 2.10735 16.2893 5.5324C16.2893 6.95941 17.4522 8.12234 18.8793 8.12234Z" fill="white"/>
                                                        <path d="M18.8794 8.39832C18.1461 8.39832 17.4208 8.59147 16.79 8.95809C16.9753 9.15125 17.1448 9.36019 17.3025 9.577C18.0136 10.5366 18.4577 11.7291 18.5443 12.9475H22.6441C22.8609 12.9475 23.0383 12.7701 23.0383 12.5533C23.0383 10.2629 21.1737 8.39832 18.8794 8.39832Z" fill="white"/>
                                                        <path d="M5.12147 8.12234C6.54851 8.12234 7.71144 6.95941 7.71144 5.53237C7.58143 2.10642 2.66099 2.10735 2.53149 5.5324C2.53149 6.95941 3.69442 8.12234 5.12147 8.12234Z" fill="white"/>
                                                        <path d="M7.21051 8.95806C4.52252 7.33743 0.906211 9.40945 0.962315 12.5533C0.962268 12.7701 1.13967 12.9475 1.35648 12.9475H5.45629C5.56285 11.4514 6.1974 10.0316 7.21051 8.95806Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1 dashboard-link">{{__('Total Staff')}}</p>
                                                    <h4 class="mb-0">{{ $countUser +   $countClient}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.36694 8.87775C11.7508 8.87775 13.6833 6.94523 13.6833 4.56134C13.6833 2.17746 11.7508 0.244934 9.36694 0.244934C6.98306 0.244934 5.05054 2.17746 5.05054 4.56134C5.05054 6.94523 6.98306 8.87775 9.36694 8.87775Z" fill="white"/>
                                                        <path d="M9.87953 12.7625H8.85438L8.31885 16.7611L9.36696 18.0712L10.4151 16.7611L9.87953 12.7625Z" fill="white"/>
                                                        <path d="M8.83643 11.4676H9.89766L10.1135 10.1727H8.62061L8.83643 11.4676Z" fill="white"/>
                                                        <path d="M6.99866 16.8635L7.63322 12.1255L7.30772 10.1727H6.12964C3.26898 10.1727 0.949951 12.4917 0.949951 15.3524V19.1076C0.949951 19.4652 1.23984 19.7551 1.59741 19.7551H9.05577L7.1348 17.3539C7.02439 17.2159 6.97518 17.0387 6.99866 16.8635Z" fill="white"/>
                                                        <path d="M13.794 10.3108C13.412 10.2209 13.0139 10.1727 12.6044 10.1727H11.4263L11.1008 12.1255L11.7354 16.8635C11.7589 17.0387 11.7097 17.2159 11.5992 17.3539L9.67822 19.7551H13.0378C12.6304 19.2136 12.3885 18.5409 12.3885 17.8127V12.9783C12.3885 11.8727 12.9458 10.8951 13.794 10.3108Z" fill="white"/>
                                                        <path d="M21.1076 11.0359H20.7407V9.95685C20.7407 9.12382 20.063 8.44611 19.2299 8.44611H17.5034C16.6703 8.44611 15.9926 9.12382 15.9926 9.95685V11.0359H15.6257C14.553 11.0359 13.6833 11.9056 13.6833 12.9783V13.41H23.05V12.9783C23.05 11.9056 22.1803 11.0359 21.1076 11.0359ZM19.4458 11.0359H17.2875V9.95685C17.2875 9.83784 17.3844 9.74103 17.5034 9.74103H19.2299C19.3489 9.74103 19.4458 9.83784 19.4458 9.95685V11.0359Z" fill="white"/>
                                                        <path d="M13.6833 17.8127C13.6833 18.8855 14.553 19.7551 15.6257 19.7551H21.1076C22.1803 19.7551 23.05 18.8855 23.05 17.8127V14.7049H13.6833V17.8127Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1">
                                                        <a href="{{ route('employee.index') }}" class="dashboard-link">{{__('Total Employee')}}</a>
                                                    </p>
                                                    <h4 class="mb-0">{{$countUser}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1357 3.18426L11.8316 5.01718C11.8525 5.0723 11.9012 5.10765 11.9601 5.11054L13.9183 5.20598C13.9796 5.20896 14.0301 5.24759 14.0491 5.30599C14.0681 5.36439 14.0499 5.42534 14.0021 5.4638L12.4739 6.69203C12.428 6.72894 12.4094 6.78622 12.4248 6.84306L12.9392 8.73494C12.9553 8.79421 12.9342 8.85416 12.8845 8.89025C12.8348 8.92633 12.7713 8.92789 12.7199 8.89426L11.0795 7.82047C11.0302 7.78818 10.97 7.78818 10.9207 7.82047L9.28038 8.89426C9.22902 8.92789 9.16544 8.92633 9.1158 8.89025C9.06612 8.85416 9.04501 8.79417 9.06111 8.73494L9.57545 6.84306C9.59091 6.78622 9.5723 6.72894 9.52637 6.69203L7.99824 5.4638C7.95037 5.42534 7.9322 5.36439 7.95119 5.30599C7.97014 5.24759 8.02068 5.209 8.08202 5.20598L10.0402 5.11054C10.0991 5.1077 10.1478 5.0723 10.1687 5.01718L10.8646 3.18426C10.8864 3.12685 10.9387 3.09077 11.0001 3.09077C11.0615 3.09077 11.1139 3.12685 11.1357 3.18426ZM7.34309 2.60763L8.56792 3.71052C8.70945 3.8379 8.92747 3.82641 9.05485 3.68488C9.18223 3.54334 9.17075 3.32532 9.02921 3.19794L7.80439 2.09506C7.66285 1.96768 7.44483 1.97916 7.31745 2.1207C7.19008 2.26223 7.20152 2.48026 7.34309 2.60763ZM14.1958 2.09506L12.9638 3.20438C12.8223 3.33175 12.8108 3.54978 12.9382 3.69131C13.0656 3.83285 13.2836 3.84433 13.4251 3.71695L14.6571 2.60763C14.7987 2.48026 14.8101 2.26223 14.6828 2.1207C14.5553 1.97916 14.3374 1.96768 14.1958 2.09506ZM11.3454 1.80806C11.3454 1.99876 11.1908 2.15337 11.0001 2.15337C10.8094 2.15337 10.6548 1.99876 10.6548 1.80806V0.540808C10.6548 0.350109 10.8094 0.195496 11.0001 0.195496C11.1908 0.195496 11.3454 0.350109 11.3454 0.540808V1.80806ZM11.0001 10.0022C12.3382 10.0022 13.4229 11.087 13.4229 12.425C13.4229 13.763 12.3382 14.8477 11.0001 14.8477C9.66208 14.8477 8.57741 13.763 8.57741 12.425C8.57741 11.0869 9.66208 10.0022 11.0001 10.0022ZM15.7642 19.5555C15.507 17.1516 13.4723 15.2793 11.0001 15.2793C8.52795 15.2793 6.49324 17.1516 6.23598 19.5555V20.524C6.23598 21.2293 6.81123 21.8046 7.51653 21.8046H14.4837C15.189 21.8046 15.7642 21.2293 15.7642 20.524V19.5555ZM18.2408 11.7616C19.2418 11.7616 20.0533 12.5731 20.0533 13.5741C20.0533 14.5751 19.2418 15.3866 18.2408 15.3866C17.2398 15.3866 16.4283 14.5751 16.4283 13.5741C16.4283 12.5731 17.2397 11.7616 18.2408 11.7616ZM3.75944 11.7616C4.76046 11.7616 5.57194 12.5731 5.57194 13.5741C5.57194 14.5751 4.76046 15.3866 3.75944 15.3866C2.75842 15.3866 1.94694 14.5751 1.94694 13.5741C1.94694 12.5731 2.75842 11.7616 3.75944 11.7616ZM3.7594 15.8708C4.81571 15.8708 5.76527 16.3276 6.42137 17.0545C5.95835 17.7556 5.65132 18.5714 5.55239 19.4557H1.11711C0.848326 19.4557 0.617053 19.3388 0.457562 19.1225C0.298071 18.9062 0.254864 18.6507 0.334372 18.3939C0.786947 16.9324 2.14925 15.8708 3.7594 15.8708ZM18.2408 15.8708C19.851 15.8708 21.2133 16.9324 21.6658 18.3939C21.7453 18.6507 21.7021 18.9061 21.5426 19.1225C21.3831 19.3389 21.1519 19.4557 20.8831 19.4557H16.4478C16.3488 18.5714 16.0418 17.7556 15.5788 17.0545C16.2349 16.3276 17.1845 15.8708 18.2408 15.8708ZM3.48729 3.18426L4.18318 5.01718C4.20412 5.0723 4.25281 5.10765 4.31168 5.11054L6.26991 5.20598C6.33124 5.20896 6.38174 5.24759 6.40074 5.30599C6.41968 5.36439 6.40156 5.42534 6.35369 5.4638L4.82555 6.69203C4.77962 6.72894 4.76102 6.78622 4.77647 6.84306L5.29081 8.73494C5.30692 8.79421 5.28581 8.85416 5.23613 8.89025C5.18644 8.92633 5.12291 8.92789 5.0715 8.89426L3.43113 7.82047C3.38184 7.78818 3.32163 7.78818 3.27233 7.82047L1.63201 8.89426C1.58065 8.92789 1.51707 8.92633 1.46743 8.89025C1.41775 8.85416 1.39664 8.79417 1.41274 8.73494L1.92708 6.84306C1.94254 6.78622 1.92393 6.72894 1.87801 6.69203L0.349868 5.46376C0.301999 5.4253 0.283827 5.36435 0.302819 5.30595C0.321768 5.24755 0.372313 5.20896 0.433649 5.20594L2.39187 5.1105C2.45075 5.10765 2.49944 5.07226 2.52033 5.01714L3.21626 3.18426C3.23806 3.12685 3.29038 3.09077 3.3518 3.09077C3.41322 3.09077 3.46549 3.12685 3.48729 3.18426ZM18.7612 3.18426L19.457 5.01718C19.478 5.0723 19.5267 5.10765 19.5855 5.11054L21.5438 5.20598C21.6051 5.20896 21.6556 5.24759 21.6746 5.30599C21.6935 5.36439 21.6754 5.42534 21.6276 5.4638L20.0994 6.69203C20.0535 6.72894 20.0349 6.78622 20.0503 6.84306L20.5647 8.73494C20.5808 8.79421 20.5597 8.85416 20.51 8.89025C20.4604 8.92633 20.3968 8.92789 20.3454 8.89426L18.705 7.82047C18.6557 7.78818 18.5955 7.78818 18.5462 7.82047L16.9059 8.89426C16.8546 8.92789 16.791 8.92633 16.7413 8.89025C16.6916 8.85416 16.6705 8.79417 16.6866 8.73494L17.2009 6.84306C17.2164 6.78622 17.1978 6.72894 17.1519 6.69203L15.6237 5.4638C15.5759 5.42534 15.5577 5.36439 15.5767 5.30599C15.5956 5.24759 15.6462 5.209 15.7075 5.20598L17.6657 5.11054C17.7246 5.1077 17.7733 5.0723 17.7942 5.01718L18.4901 3.18426C18.5119 3.12685 18.5642 3.09077 18.6256 3.09077C18.687 3.09077 18.7394 3.12685 18.7612 3.18426Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1">
                                                        <a href="{{ route('clients.index') }}" class="dashboard-link">{{__('Total Client')}}</a>
                                                    </p>
                                                    <h4 class="mb-0">{{$countClient}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div class="card staff-info-card-wrp">
                                <div class="card-body p-3">
                                    <h4 class="mb-3">{{__('Job')}}</h4>
                                    <div class="row row-gap-1">
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12.6475 13.8848C13.005 13.8848 13.2949 13.5949 13.2949 13.2373V10.6475C13.2949 10.2899 13.0051 10 12.6475 10H11.3525C10.995 10 10.7051 10.2898 10.7051 10.6475V13.2373C10.7051 13.5948 10.995 13.8848 11.3525 13.8848H12.6475Z" fill="white"/>
                                                        <path d="M21.1076 2.87793H16.5322C16.5322 2.74753 16.5322 2.10007 16.5322 2.23047C16.5322 1.1594 15.6609 0.288086 14.5898 0.288086C14.4493 0.288086 9.21781 0.288086 9.41011 0.288086C8.33903 0.288086 7.46772 1.15935 7.46772 2.23047C7.46772 2.36087 7.46772 3.00833 7.46772 2.87793H2.89233C1.82126 2.87793 0.949951 3.7492 0.949951 4.82031C0.949951 6.08834 1.10534 7.19883 1.41388 8.1452C1.72242 9.09157 2.18414 9.87388 2.79685 10.4856C3.82791 11.5153 5.14122 11.9424 6.61588 11.9424H9.41011C9.41011 11.812 9.41011 10.5171 9.41011 10.6475C9.41011 9.57639 10.2814 8.70508 11.3525 8.70508C11.4829 8.70508 12.7778 8.70508 12.6474 8.70508C13.7185 8.70508 14.5898 9.57635 14.5898 10.6475C14.5898 10.7779 14.5898 12.0728 14.5898 11.9424H16.0284C17.3173 11.8888 19.4256 12.2807 21.2024 10.5121C21.8154 9.90197 22.2773 9.11829 22.5859 8.16747C22.8946 7.21665 23.05 6.09879 23.05 4.82031C23.05 3.74924 22.1787 2.87793 21.1076 2.87793ZM8.76265 2.23047C8.76265 1.87324 9.05284 1.58301 9.41011 1.58301H14.5898C14.947 1.58301 15.2373 1.8732 15.2373 2.23047C15.2373 2.36087 15.2373 3.00833 15.2373 2.87793H8.76265C8.76265 2.74753 8.76265 2.10007 8.76265 2.23047Z" fill="white"/>
                                                        <path d="M16.0461 13.2373H14.5898C14.5898 14.3084 13.7185 15.1797 12.6474 15.1797C12.517 15.1797 11.2221 15.1797 11.3525 15.1797C10.2814 15.1797 9.41011 14.3084 9.41011 13.2373C9.26218 13.2373 6.46166 13.2373 6.62154 13.2373C4.77481 13.2373 3.15274 12.6707 1.88191 11.4018C1.52291 11.0434 1.21636 10.6399 0.949951 10.2083V19.0645C0.949951 19.4223 1.23954 19.7119 1.59741 19.7119H22.4025C22.7604 19.7119 23.05 19.4223 23.05 19.0645V10.2349C22.78 10.6731 22.4722 11.0757 22.1161 11.4303C20.0731 13.4626 17.8973 13.1499 16.0461 13.2373Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1">
                                                        <a href="{{ route('job.index') }}" class="dashboard-link">{{__('Total Jobs')}}</a>
                                                    </p>
                                                    <h4 class="mb-0">{{$activeJob + $inActiveJOb}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.2004 2.5456L17.4559 6.80112C17.508 6.85316 17.534 6.9214 17.534 6.98964L17.5341 13.0102C17.5341 13.0847 17.5035 13.152 17.4542 13.2004L13.1987 17.456C13.1467 17.508 13.0785 17.5341 13.0102 17.5341L6.98962 17.5341C6.91514 17.5341 6.84778 17.5036 6.79938 17.4543L2.54386 13.1988C2.49178 13.1467 2.46578 13.0785 2.46578 13.0103L2.46574 6.98972C2.46574 6.91524 2.4963 6.84788 2.54558 6.79948L6.80106 2.54392C6.8531 2.49184 6.92134 2.46584 6.98958 2.46584L13.0102 2.4658C13.0847 2.4658 13.1521 2.49636 13.2004 2.5456ZM2.23034 3.91596V2.37092C2.23034 2.35132 2.2463 2.33536 2.2659 2.33536H3.94046C4.08774 2.33536 4.20714 2.21596 4.20714 2.06868C4.20714 1.9214 4.08774 1.802 3.94046 1.802H1.9637C1.81642 1.802 1.69702 1.9214 1.69702 2.06868V3.91596C1.69702 4.06324 1.81642 4.18264 1.9637 4.18264C2.11098 4.18264 2.23034 4.06324 2.23034 3.91596ZM1.69702 16.084V17.9312C1.69702 18.0785 1.81642 18.1979 1.9637 18.1979H3.94046C4.08774 18.1979 4.20714 18.0785 4.20714 17.9312C4.20714 17.7839 4.08774 17.6645 3.94046 17.6645H2.2659C2.2463 17.6645 2.23034 17.6486 2.23034 17.629V16.084C2.23034 15.9367 2.11094 15.8173 1.96366 15.8173C1.81638 15.8173 1.69702 15.9367 1.69702 16.084ZM17.7695 16.084V17.629C17.7695 17.6486 17.7535 17.6646 17.7339 17.6646H16.0593C15.9121 17.6646 15.7927 17.784 15.7927 17.9312C15.7927 18.0785 15.9121 18.1979 16.0593 18.1979H18.0361C18.1834 18.1979 18.3028 18.0785 18.3028 17.9312V16.084C18.3028 15.9367 18.1834 15.8173 18.0361 15.8173C17.8888 15.8173 17.7695 15.9367 17.7695 16.084ZM18.3028 3.91596V2.06872C18.3028 1.92144 18.1834 1.80204 18.0361 1.80204H16.0593C15.9121 1.80204 15.7927 1.92144 15.7927 2.06872C15.7927 2.216 15.9121 2.3354 16.0593 2.3354H17.7339C17.7535 2.3354 17.7695 2.35136 17.7695 2.37096V3.91596C17.7695 4.06324 17.8889 4.18264 18.0361 4.18264C18.1834 4.18264 18.3028 4.06324 18.3028 3.91596ZM13.7813 6.98244L13.0174 6.21856C12.8733 6.07444 12.6358 6.07444 12.4917 6.21856L10.6128 8.09744C10.2749 8.43532 9.7249 8.43532 9.38698 8.09744L7.5081 6.21856C7.36398 6.07444 7.1265 6.07444 6.98238 6.21856L6.2185 6.98244C6.07438 7.12656 6.07438 7.36404 6.2185 7.50816L8.09738 9.38708C8.43526 9.72496 8.43526 10.275 8.09738 10.6129L6.21846 12.4918C6.07434 12.636 6.07434 12.8734 6.21846 13.0176L6.98234 13.7814C7.12646 13.9256 7.36394 13.9256 7.50806 13.7814L9.38698 11.9025C9.72486 11.5646 10.2749 11.5646 10.6128 11.9025L12.4917 13.7814C12.6359 13.9256 12.8733 13.9256 13.0175 13.7814L13.7813 13.0176C13.9255 12.8734 13.9255 12.636 13.7813 12.4918L11.9025 10.613C11.5646 10.2751 11.5646 9.72504 11.9025 9.38712L13.7813 7.50824C13.9254 7.36404 13.9254 7.12656 13.7813 6.98244Z" fill="#0CAF60"/>
                                                        <circle cx="10" cy="10" r="7" fill="#0CAF60"/>
                                                        <path class="active-checkmark" d="M13.9981 7.83076C13.9984 7.91906 13.9811 8.00654 13.9473 8.08812C13.9135 8.16971 13.8639 8.24378 13.8013 8.30605L9.56273 12.5446C9.37207 12.7347 9.11382 12.8415 8.84458 12.8415C8.57534 12.8415 8.31708 12.7347 8.12642 12.5446L6.19881 10.617C6.13603 10.5546 6.08617 10.4805 6.0521 10.3989C6.01802 10.3172 6.0004 10.2297 6.00025 10.1412C6.00009 10.0527 6.0174 9.96507 6.05119 9.8833C6.08497 9.80153 6.13457 9.72723 6.19713 9.66467C6.25969 9.60211 6.33399 9.55251 6.41576 9.51873C6.49753 9.48494 6.58517 9.46763 6.67364 9.46779C6.76212 9.46795 6.84969 9.48557 6.93134 9.51965C7.01299 9.55372 7.08711 9.60358 7.14945 9.66636L8.84458 11.3615L12.8507 7.35541C12.9447 7.26139 13.0645 7.19737 13.1949 7.17143C13.3253 7.14549 13.4604 7.1588 13.5832 7.20968C13.7061 7.26057 13.811 7.34673 13.8849 7.45728C13.9588 7.56783 13.9982 7.6978 13.9981 7.83076Z" fill="white"/>
                                                        </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1 dashboard-link">{{__('Active Jobs')}}</p>
                                                    <h4 class="mb-0">{{$activeJob}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.5365 3.76293L20.2388 8.46528C20.2963 8.52279 20.3251 8.59819 20.3251 8.6736L20.3251 15.3263C20.3251 15.4086 20.2914 15.483 20.2369 15.5365L15.5346 20.2389C15.477 20.2964 15.4017 20.3252 15.3263 20.3252L8.67352 20.3252C8.59122 20.3252 8.51679 20.2915 8.4633 20.2371L3.76095 15.5347C3.70341 15.4772 3.67467 15.4018 3.67467 15.3264L3.67463 8.67369C3.67463 8.59138 3.7084 8.51695 3.76285 8.46347L8.46516 3.76108C8.52266 3.70353 8.59807 3.6748 8.67347 3.6748L15.3262 3.67475C15.4086 3.67475 15.483 3.70852 15.5365 3.76293ZM3.41451 5.27718V3.56991C3.41451 3.54825 3.43215 3.53062 3.45381 3.53062H5.3042C5.46694 3.53062 5.59888 3.39868 5.59888 3.23594C5.59888 3.07319 5.46694 2.94125 5.3042 2.94125H3.11988C2.95713 2.94125 2.8252 3.07319 2.8252 3.23594V5.27718C2.8252 5.43992 2.95713 5.57186 3.11988 5.57186C3.28262 5.57186 3.41451 5.43992 3.41451 5.27718ZM2.8252 18.7228V20.764C2.8252 20.9268 2.95713 21.0587 3.11988 21.0587H5.3042C5.46694 21.0587 5.59888 20.9268 5.59888 20.764C5.59888 20.6013 5.46694 20.4693 5.3042 20.4693H3.45381C3.43215 20.4693 3.41451 20.4517 3.41451 20.43V18.7228C3.41451 18.5601 3.28258 18.4281 3.11983 18.4281C2.95709 18.4281 2.8252 18.5601 2.8252 18.7228ZM20.5852 18.7228V20.4301C20.5852 20.4517 20.5676 20.4694 20.5459 20.4694H18.6956C18.5328 20.4694 18.4009 20.6013 18.4009 20.7641C18.4009 20.9268 18.5328 21.0587 18.6956 21.0587H20.8799C21.0426 21.0587 21.1746 20.9268 21.1746 20.7641V18.7228C21.1746 18.5601 21.0426 18.4281 20.8799 18.4281C20.7171 18.4281 20.5852 18.5601 20.5852 18.7228ZM21.1746 5.27718V3.23598C21.1746 3.07324 21.0426 2.9413 20.8799 2.9413H18.6956C18.5328 2.9413 18.4009 3.07324 18.4009 3.23598C18.4009 3.39872 18.5328 3.53066 18.6956 3.53066H20.5459C20.5676 3.53066 20.5852 3.5483 20.5852 3.56995V5.27718C20.5852 5.43992 20.7172 5.57186 20.8799 5.57186C21.0427 5.57186 21.1746 5.43992 21.1746 5.27718ZM16.1783 8.66564L15.3342 7.82155C15.175 7.6623 14.9126 7.6623 14.7533 7.82155L12.6772 9.89772C12.3038 10.2711 11.696 10.2711 11.3226 9.89772L9.24644 7.82155C9.08719 7.6623 8.82477 7.6623 8.66552 7.82155L7.82143 8.66564C7.66218 8.82489 7.66218 9.08731 7.82143 9.24656L9.89759 11.3228C10.271 11.6961 10.271 12.3039 9.89759 12.6773L7.82139 14.7535C7.66213 14.9128 7.66213 15.1752 7.82139 15.3344L8.66547 16.1785C8.82473 16.3378 9.08714 16.3378 9.24639 16.1785L11.3226 14.1023C11.696 13.729 12.3038 13.729 12.6772 14.1023L14.7534 16.1785C14.9126 16.3378 15.175 16.3378 15.3343 16.1785L16.1784 15.3344C16.3376 15.1752 16.3376 14.9128 16.1784 14.7535L14.1022 12.6774C13.7288 12.304 13.7288 11.6962 14.1022 11.3228L16.1784 9.24665C16.3375 9.08731 16.3375 8.82489 16.1783 8.66564Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1 dashboard-link">{{__('Inactive Jobs')}}</p>
                                                    <h4 class="mb-0">{{$inActiveJOb}}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div class="card staff-info-card-wrp">
                                <div class="card-body p-3">
                                    <h4 class="mb-3">{{ __('Training') }}</h4>
                                    <div class="row row-gap-1">
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_449_2760)">
                                                        <path d="M14.6136 4.78959C14.6083 4.81202 14.6055 4.83547 14.6055 4.85957C14.607 5.07374 14.5793 5.30263 14.5309 5.54704C14.4822 5.79313 14.4093 6.06394 14.3225 6.35591C14.2782 6.50501 14.3556 6.66188 14.497 6.71624C14.6243 6.78206 15.3902 7.23983 14.0719 8.15476C13.9183 8.26144 13.8308 8.48454 13.7126 8.78595C13.4666 9.41333 13.0494 10.4774 11.9058 10.8065C11.7574 10.8492 11.6898 10.9362 11.6056 11.0446C11.5216 11.1527 11.3994 11.31 10.9999 11.31C10.6005 11.31 10.4783 11.1527 10.3943 11.0446C10.3101 10.9362 10.2425 10.8492 10.0941 10.8065C8.95053 10.4774 8.53331 9.41333 8.28732 8.78595C8.16913 8.48454 8.08163 8.26144 7.92801 8.15476C6.50034 7.16391 7.51708 6.70923 7.51992 6.70791L7.39964 6.44086L7.52053 6.70878C7.65878 6.64463 7.72359 6.48415 7.67314 6.34165C7.54043 5.89057 7.48638 5.49584 7.43583 5.12652C7.41908 5.00432 7.40269 4.8847 7.39208 4.81471C7.38939 4.7969 7.38523 4.77969 7.3797 4.7632L6.81992 4.91535C6.83885 5.04055 6.85022 5.12322 6.86169 5.20711C6.90772 5.54303 6.95639 5.89864 7.05957 6.30044C6.61996 6.59641 5.81947 7.40391 7.60123 8.64045C7.61001 8.64654 7.66938 8.79787 7.74952 9.0023C8.03489 9.73011 8.5189 10.9645 9.93753 11.3727C9.9722 11.3827 9.9186 11.383 9.93819 11.4083C10.0935 11.6082 10.3197 11.8992 10.9999 11.8992C11.6802 11.8992 11.9064 11.6082 12.0617 11.4083C12.0813 11.383 12.0277 11.3827 12.0624 11.3727C13.481 10.9645 13.965 9.73011 14.2504 9.0023C14.3305 8.79787 14.3899 8.64654 14.3987 8.64045C16.1773 7.40599 15.3827 6.59926 14.9427 6.30201C15.0037 6.08378 15.0567 5.87149 15.0982 5.66215C15.1478 5.41144 15.1786 5.17215 15.1836 4.94245L15.1613 4.93844L14.6136 4.78959Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11 0C11.2433 0 11.4407 0.171334 11.4407 0.38266C11.4407 0.464419 11.4111 0.54019 11.3609 0.602359C11.2812 0.700917 11.1493 0.76537 11 0.76537C10.8507 0.76537 10.7188 0.700917 10.6391 0.602359C10.5889 0.54019 10.5593 0.464419 10.5593 0.38266C10.5593 0.171334 10.7566 0 11 0Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8621 13.1839C10.0577 13.1582 9.27188 12.9074 8.82898 12.4314C8.97057 13.0896 9.38871 14.3482 10.5716 14.9695C10.6545 15.0129 10.6925 15.1063 10.6677 15.1914C10.7699 15.1486 10.8822 15.1249 11 15.1249C11.1179 15.1249 11.2301 15.1486 11.3324 15.1914C11.3075 15.1063 11.3456 15.0129 11.4285 14.9695C12.6114 14.3481 13.0295 13.0895 13.1711 12.4314C12.6777 12.9617 11.7585 13.2126 10.8621 13.1839ZM8.37451 12.0935L4.4936 13.4061C3.5533 13.7241 2.94556 14.4075 2.94556 15.3193V22H5.92355V19.6199L6.50201 22H15.5908L16.1693 19.6199V22H19.0545V15.3193C19.0545 14.4075 18.4468 13.7241 17.5065 13.4061L13.6256 12.0935C13.5858 12.4381 13.2869 14.4286 11.6126 15.3081C11.594 15.3178 11.5745 15.3243 11.5548 15.3278C11.7417 15.4855 11.8603 15.7215 11.8603 15.9852C11.8603 16.5881 11.8563 17.1911 11.8566 17.794H11.2667L11.1325 16.8353C11.0894 16.842 11.0451 16.8455 11 16.8455C10.5249 16.8455 10.1398 16.4603 10.1398 15.9852C10.1398 15.7215 10.2584 15.4856 10.4452 15.3278C10.4255 15.3243 10.4061 15.3178 10.3874 15.3081C8.71316 14.4286 8.41424 12.4381 8.37451 12.0935ZM14.7089 15.1916H16.9126V16.8999C16.9126 17.5059 16.4168 18.0018 15.8108 18.0018C15.2047 18.0018 14.7089 17.5059 14.7089 16.8999V15.1916Z" fill="white"/>
                                                        <path d="M13.7299 10.0574C13.7299 9.89471 13.598 9.76276 13.4353 9.76276C13.2726 9.76276 13.1407 9.89471 13.1407 10.0574V13.7501C13.1407 13.9128 13.2726 14.0447 13.4353 14.0447C13.598 14.0447 13.7299 13.9128 13.7299 13.7501V10.0574ZM8.27026 13.7501C8.27026 13.9128 8.40216 14.0447 8.56487 14.0447C8.72763 14.0447 8.85953 13.9128 8.85953 13.7501V10.0547C8.85953 9.89202 8.72763 9.76012 8.56487 9.76012C8.40216 9.76012 8.27026 9.89202 8.27026 10.0547V13.7501Z" fill="white"/>
                                                        <path d="M14.4213 15.7608C14.3128 15.7608 14.2249 15.8488 14.2249 15.9573C14.2249 16.0657 14.3128 16.1537 14.4213 16.1537H17.2597C17.3682 16.1537 17.4561 16.0657 17.4561 15.9573C17.4561 15.8488 17.3682 15.7608 17.2597 15.7608H14.4213Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5819 5.54705C14.631 5.30263 14.6591 5.07375 14.6577 4.85958C14.6576 4.83547 14.6603 4.81203 14.6658 4.7896C10.7243 3.86791 8.1434 4.49971 7.32764 4.76321C7.33327 4.77965 7.33748 4.7969 7.34017 4.81472C7.35093 4.8847 7.36758 5.00432 7.38453 5.12653C7.41457 5.34298 7.44589 5.56816 7.49369 5.80826C8.33082 6.07308 10.0888 5.30725 10.9553 5.30573C11.8552 5.30421 13.6988 6.13271 14.532 5.77121C14.5506 5.69443 14.5673 5.61967 14.5819 5.54705Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9512 4.45286V2.49982C14.9512 2.49982 15.0474 1.60519 13.6355 1.11722L11.361 0.602356C11.2812 0.700914 11.1493 0.765367 11.0001 0.765367C10.8508 0.765367 10.7189 0.700914 10.6392 0.602356L8.3647 1.11722C6.95272 1.60519 7.04899 2.49982 7.04899 2.49982V4.44037L7.20693 4.38937C8.0264 4.1247 8.94468 3.99209 9.80069 3.93982C11.4578 3.83867 13.1419 4.02975 14.7553 4.40703L14.9512 4.45286Z" fill="white"/>
                                                        </g>
                                                        <defs>
                                                        <clipPath id="clip0_449_2760">
                                                        <rect width="22" height="22" fill="white"/>
                                                        </clipPath>
                                                        </defs>
                                                        </svg>

                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1">
                                                        <a href="{{ route('trainer.index') }}"
                                                            class="dashboard-link">{{ __('Trainer') }}</a>
                                                    </p>
                                                    <h4 class="mb-0">{{ $countTrainer }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                                                        <path d="M10.5417 11.2237C13.3261 11.2237 15.5833 8.96644 15.5833 6.182C15.5833 3.39757 13.3261 1.14034 10.5417 1.14034C7.75723 1.14034 5.5 3.39757 5.5 6.182C5.5 8.96644 7.75723 11.2237 10.5417 11.2237Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.848 19.943C10.8955 18.92 10.3125 17.5487 10.3125 16.0417C10.3125 14.4989 10.924 13.0973 11.9176 12.067C11.4685 12.0322 11.0092 12.0138 10.5417 12.0138C7.49654 12.0138 4.80062 12.7756 3.12679 13.9168C1.84987 14.7877 1.14587 15.8941 1.14587 17.0555V18.3847C1.14587 18.7981 1.30996 19.195 1.60237 19.4865C1.89479 19.7789 2.29079 19.943 2.70421 19.943H11.848Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.0417 11.2292C13.3852 11.2292 11.2292 13.3852 11.2292 16.0417C11.2292 18.6982 13.3852 20.8542 16.0417 20.8542C18.6982 20.8542 20.8542 18.6982 20.8542 16.0417C20.8542 13.3852 18.6982 11.2292 16.0417 11.2292ZM13.8271 16.8428L15.2021 17.7595C15.4743 17.941 15.8382 17.9053 16.0692 17.6733L18.3609 15.3817C18.6295 15.114 18.6295 14.6777 18.3609 14.41C18.0932 14.1414 17.6569 14.1414 17.3892 14.41L15.4954 16.3029L14.5897 15.6988C14.2744 15.488 13.8472 15.5742 13.6364 15.8895C13.4256 16.2048 13.5117 16.632 13.8271 16.8428Z" fill="white"/>
                                                    </svg>
                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1 dashboard-link">{{ __('Active Training') }}</p>
                                                    <h4 class="mb-0">{{ $onGoingTraining }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-md-4 col-sm-6 col-12 staff-info-card">
                                            <div class="staff-info-inner d-flex align-items-center gap-3">
                                                <div class="staff-info-icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_449_2799)">
                                                        <path d="M19.9749 1.375H10.3095C9.19096 1.375 8.28442 2.28171 8.28442 3.40007V8.16104C8.79535 8.41499 9.3474 8.68959 9.86269 8.94572L10.6511 7.3683C11.1284 6.41443 12.2921 6.02687 13.2456 6.50339C14.2012 6.98158 14.5884 8.14207 14.11 9.09779L12.7047 11.9092C12.7419 12.2813 12.7089 12.6565 12.604 13.0195H13.5318L10.6511 14.6282C10.5059 15.0273 11.2201 16.1159 11.6905 16.1159C11.9536 16.1159 11.7674 15.3308 11.8624 15.0686L14.9033 13.0195H15.355L14.145 15.6936C14.2408 15.9545 14.4875 16.1159 14.7504 16.1159C15.19 16.1159 15.5122 15.6768 15.355 15.2493L16.7281 13.0195H19.9749C21.0933 13.0195 22 12.1128 22 10.9945V3.40007C22 2.28171 21.0933 1.375 19.9749 1.375ZM18.5939 10.2583H15.7021C15.3462 10.2583 15.0575 9.96959 15.0575 9.61375C15.0575 9.25758 15.3462 8.96922 15.7021 8.96922H18.5939C18.9501 8.96922 19.2384 9.25758 19.2384 9.61375C19.2384 9.96959 18.9501 10.2583 18.5939 10.2583ZM18.5939 7.8418H15.7021C15.3462 7.8418 15.0575 7.55344 15.0575 7.19727C15.0575 6.84143 15.3462 6.55273 15.7021 6.55273H18.5939C18.9501 6.55273 19.2384 6.84143 19.2384 7.19727C19.2384 7.55344 18.9501 7.8418 18.5939 7.8418ZM18.5939 5.42564H11.6905C11.3344 5.42564 11.046 5.13695 11.046 4.78111C11.046 4.42528 11.3344 4.13658 11.6905 4.13658H18.5939C18.9501 4.13658 19.2384 4.42528 19.2384 4.78111C19.2384 5.13695 18.9501 5.42564 18.5939 5.42564Z" fill="white"/>
                                                        <path d="M11.3404 12.7431C11.4569 12.4019 11.456 12.0453 11.3577 11.7201L12.9573 8.52124C13.1164 8.20284 12.9875 7.81561 12.6691 7.65649C12.3508 7.49721 11.9636 7.62628 11.8043 7.94469L10.4401 10.6729C9.62466 10.2668 8.38579 9.65085 7.38777 9.15453C6.45924 8.69278 5.79659 8.2787 4.66597 8.2787H4.09647C4.97682 8.2787 5.76251 7.87352 6.27679 7.2394C6.66922 6.75684 6.90387 6.14185 6.90387 5.47163C6.90387 3.9209 5.64703 2.66406 4.09613 2.66406C2.5459 2.66406 1.28906 3.9209 1.28906 5.47163C1.28906 6.21637 1.5791 6.89313 2.05226 7.39532C2.56386 7.93932 3.29047 8.2787 4.09596 8.27887C3.01604 8.27887 1.96028 8.71645 1.20078 9.47797C0.426498 10.2502 0 11.2791 0 12.3752V14.4462C0 15.3378 0.578903 16.0965 1.38071 16.3664V19.9691C1.38071 20.3251 1.66924 20.6136 2.02524 20.6136H6.16736C6.52336 20.6136 6.81189 20.3251 6.81189 19.9691V12.7273C7.42302 13.0328 8.22583 13.4341 8.8754 13.7591C9.33076 13.9867 9.86284 14.0071 10.3373 13.8141C10.8105 13.6194 11.1752 13.2319 11.3379 12.7506C11.3387 12.7481 11.3396 12.7456 11.3404 12.7431Z" fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.1706 16.0853C20.1706 18.8938 17.8938 21.1706 15.0853 21.1706C12.2768 21.1706 10 18.8938 10 16.0853C10 13.2768 12.2768 11 15.0853 11C17.8938 11 20.1706 13.2768 20.1706 16.0853ZM17.9953 14.153C18.3069 14.426 18.3383 14.8998 18.0654 15.2114L15.4965 18.1444C15.0182 18.6905 14.1768 18.7156 13.6667 18.199L12.2241 16.7379C11.9331 16.4432 11.9361 15.9683 12.2309 15.6773C12.5256 15.3863 13.0005 15.3893 13.2915 15.684L14.1675 16.5713C14.3715 16.7779 14.7081 16.7679 14.8994 16.5494L16.937 14.2231C17.2099 13.9115 17.6838 13.8801 17.9953 14.153Z" fill="white"/>
                                                        </g>
                                                        <defs>
                                                        <clipPath id="clip0_449_2799">
                                                        <rect width="22" height="22" fill="white"/>
                                                        </clipPath>
                                                        </defs>
                                                        </svg>

                                                </div>
                                                <div class="staff-info">
                                                    <p class="mb-1 dashboard-link">{{ __('Done Training') }}</p>
                                                    <h4 class="mb-0">{{ $doneTraining }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">

                                <h5>{{__('Announcement List')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-items-center">
                                        <thead>
                                        <tr>
                                            <th>{{__('Title')}}</th>
                                            <th>{{__('Start Date')}}</th>
                                            <th>{{__('End Date')}}</th>
                                            <th>{{__('description')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody class="list">
                                        @forelse($announcements as $announcement)
                                            <tr>
                                                <td>{{ $announcement->title }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->start_date)  }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>
                                                <td>{{ $announcement->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">
                                                    <div class="text-center">
                                                        <h6>{{__('There is no Announcement List')}}</h6>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <h5>{{__('Meeting schedule')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if(count($meetings) > 0)
                                        <table class="table align-items-center">
                                            <thead>
                                            <tr>
                                                <th>{{__('Title')}}</th>
                                                <th>{{__('Date')}}</th>
                                                <th>{{__('Time')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody class="list">
                                            @foreach($meetings as $meeting)
                                                <tr>
                                                    <td>{{ $meeting->title }}</td>
                                                    <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                                    <td>{{  \Auth::user()->timeFormat($meeting->time) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-2">
                                            {{__('No meeting scheduled yet.')}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@endsection


