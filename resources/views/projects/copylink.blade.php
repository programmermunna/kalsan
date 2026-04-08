@extends('layouts.shareproject')
@php
    $result = json_decode($project->copylinksetting);
@endphp

@section('page-title')
    {{ __('Projects Details') }}
@endsection

@push('script-page')
    <script>
        loadProjectUser();
        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: ["#ffa21d"],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [{
                    name: 'Bandwidth',
                    data: {{ json_encode(array_map('intval', $project_data['timesheet_chart']['chart'])) }}
                }],

                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function(seriesName) {
                                return ''
                            }
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#timesheet_chart"), options);
            chart.render();
        })();

        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: ["#ffa21d"],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [{
                    name: 'Bandwidth',
                    data: {{ json_encode($project_data['task_chart']['chart']) }}
                }],

                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function(seriesName) {
                                return ''
                            }
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#task_chart"), options);
            chart.render();
        })();

        $(document).on('click', '.invite_usr', function() {
            var project_id = $('#project_id').val();
            var user_id = $(this).attr('data-id');

            $.ajax({
                url: '{{ route('invite.project.user.member') }}',
                method: 'POST',
                dataType: 'json',
                data: {
                    'project_id': project_id,
                    'user_id': user_id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    if (data.code == '200') {
                        show_toastr(data.status, data.success, 'success')
                        setInterval('location.reload()', 5000);
                        loadProjectUser();
                    } else if (data.code == '404') {
                        show_toastr(data.status, data.errors, 'error')
                    }
                }
            });
        });

        function loadProjectUser() {

            var mainEle = $('#project_users');
            var project_id = '{{ $project->id }}';

            $.ajax({
                url: '{{ route('project.user') }}',
                data: {
                    project_id: project_id
                },
                beforeSend: function() {
                    $('#project_users').html(
                        '<tr><th colspan="2" class="h6 text-center pt-5">{{ __('Loading...') }}</th></tr>');
                },
                success: function(data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                }
            });
        }
    </script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })

        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
    </script>

    <script>
        function ajaxFilterTimesheetTableView() {

            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');
            var notfound1 = $('.notfound-timesheet1');

            var week = parseInt($('#weeknumber').val());
            var project_id = '{{ $project->id }}';

            var data = {
                week: week,
                project_id: project_id,
            };

            $.ajax({

                url: '{{ route('filter.timesheet.table.view') }}',

                data: data,
                success: function(data) {

                    $('.weekly-dates-div .weekly-dates').text(data.onewWeekDate);
                    $('.weekly-dates-div #selected_dates').val(data.selectedDate);

                    $('#project_tasks').find('option').not(':first').remove();

                    $.each(data.tasks, function(i, item) {
                        $('#project_tasks').append($("<option></option>")
                            .attr("value", i)
                            .text(item));
                    });

                    if (data.totalrecords == 0) {
                        mainEle.hide();
                        notfound.css('display', 'block');
                        notfound1.hide();
                    } else {
                        notfound.hide();
                        mainEle.show();
                    }

                    mainEle.html(data.html);
                }
            });
        }

        $(function() {
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '.weekly-dates-div i', function() {

            var weeknumber = parseInt($('#weeknumber').val());

            if ($(this).hasClass('previous')) {

                weeknumber--;
                $('#weeknumber').val(weeknumber);

            } else if ($(this).hasClass('next')) {

                weeknumber++;
                $('#weeknumber').val(weeknumber);
            }

            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '[data-ajax-timesheet-popup="true"]', function(e) {
            e.preventDefault();

            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');
            var p_id = $(this).data('project-id');

            data.date = date;
            data.task_id = task_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }

            if (type == 'create') {
                var title = '{{ __('Create Timesheet') }}';
                data.p_id = '{{ $project->id }}';
                data.project_id = data.p_id != '-1' ? data.p_id : p_id;

            } else if (type == 'edit') {
                var title = '{{ __('Edit Timesheet') }}';
            }

            $("#commonModal .modal-title").html(title + ` <small>(` + moment(date).format("ddd, Do MMM YYYY") +
                `)</small>`);

            $.ajax({
                url: url,
                data: data,
                dataType: 'html',
                success: function(data) {
                    $('#commonModal .body').html(data);
                    $("#commonModal").modal('show');
                    commonLoader();
                    loadConfirm();
                }
            });
        });

        $(document).on('click', '#project_tasks', function(e) {
            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');

            var selectEle = $(this).children("option:selected");
            var task_id = selectEle.val();
            var selected_dates = $('#selected_dates').val();

            if (task_id != '') {

                $.ajax({
                    url: '{{ route('filter.timesheet.table.view') }}',
                    data: {
                        project_id: '{{ $project->id }}',
                        task_id: task_id,
                        selected_dates: selected_dates,
                    },
                    success: function(data) {

                        notfound.hide();
                        mainEle.show();

                        $('#timesheet-table-view tbody').append(data.html);
                        selectEle.remove();
                    }
                });
            }
        });

        $(document).on('change', '#time_hour, #time_minute', function() {

            var hour = $('#time_hour').children("option:selected").val();
            var minute = $('#time_minute').children("option:selected").val();
            var total = $('#totaltasktime').val().split(':');

            if (hour == '00' && minute == '00') {
                $(this).val('');
                return;
            }

            hour = hour != '' ? hour : 0;
            hour = parseInt(hour) + parseInt(total[0]);

            minute = minute != '' ? minute : 0;
            minute = parseInt(minute) + parseInt(total[1]);

            if (minute > 50) {
                minute = minute - 60;
                hour++;
            }

            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;

            $('.display-total-time span').text('{{ __('Total Time') }} : ' + hour + ' {{ __('Hours') }} ' +
                minute + ' {{ __('Minutes') }}');
        });
    </script>

    <script type="text/javascript">
        function init_slider() {
            if ($(".product-left").length) {
                var productSlider = new Swiper('.product-slider', {
                    spaceBetween: 0,
                    centeredSlides: false,
                    loop: false,
                    direction: 'horizontal',
                    loopedSlides: 5,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    resizeObserver: true,
                });
                var productThumbs = new Swiper('.product-thumbs', {
                    spaceBetween: 0,
                    centeredSlides: true,
                    loop: false,
                    slideToClickedSlide: true,
                    direction: 'horizontal',
                    slidesPerView: 7,
                    loopedSlides: 5,
                });
                productSlider.controller.control = productThumbs;
                productThumbs.controller.control = productSlider;
            }
        }

        $(document).on('click', '.view-images', function() {

            var p_url = "{{ route('tracker.image.view') }}";
            var data = {
                'id': $(this).attr('data-id')
            };
            postAjax(p_url, data, function(res) {
                $('.image_sider_div').html(res);
                $('#exampleModalCenter').modal('show');
                setTimeout(function() {
                    var total = $('.product-left').find('.product-slider').length
                    if (total > 0) {
                        init_slider();
                    }

                }, 200);

            });
        });

        // ============================ Remove Track Image ===============================//
        $(document).on("click", '.track-image-remove', function() {
            var rid = $(this).attr('data-pid');
            $('.confirm_yes').addClass('image_remove');
            $('.confirm_yes').attr('image_id', rid);
            $('#cModal').modal('show');
            var total = $('.product-left').find('.swiper-slide').length
        });

        function removeImage(id) {
            var p_url = "{{ route('tracker.image.remove') }}";
            var data = {
                id: id
            };
            deleteAjax(p_url, data, function(res) {

                if (res.flag) {
                    $('#slide-thum-' + id).remove();
                    $('#slide-' + id).remove();
                    setTimeout(function() {
                        var total = $('.product-left').find('.swiper-slide').length
                        if (total > 0) {
                            init_slider();
                        } else {
                            $('.product-left').html(
                                '<div class="no-image"><h5 class="text-muted">Images Not Available .</h5></div>'
                                );
                        }
                    }, 200);
                }

                $('#cModal').modal('hide');
                show_toastr('error', res.msg, 'error');
            });
        }
    </script>
@endpush

@section('action-button')
    <a href="" class="pt-3">
        <select name="language" id="language" class="btn btn-primary my-2"
            onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach (\App\Models\Utility::languages() as $language)
                <option @if ($lang == $language) selected @endif
                    value="{{ route('projects.link', [\Illuminate\Support\Facades\Crypt::encrypt($project->id), $language]) }}">
                    {{ Str::upper($language) }}</option>
            @endforeach
        </select>
    </a>
@endsection

@php
    $logo = \App\Models\Utility::get_file('tasks/');
    $logo_path = \App\Models\Utility::get_file('/');
@endphp
@php

@endphp

@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    @if (isset($result->basic_details) && $result->basic_details == 'on')
                        <a href="#basic" class="list-group-item list-group-item-action border-0">{{ __('Basic details') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->member) && $result->member == 'on')
                        <a href="#members" class="list-group-item list-group-item-action border-0 ">{{ __('Members') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->task) && $result->task == 'on')
                        <a href="#task" class="list-group-item list-group-item-action border-0">{{ __('Task') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->milestone) && $result->milestone == 'on')
                        <a href="#milestone" class="list-group-item list-group-item-action border-0">{{ __('Milestones') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->attachment) && $result->attachment == 'on')
                        <a href="#attachment" class="list-group-item list-group-item-action border-0">{{ __('Files') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->bug_report) && $result->bug_report == 'on')
                        <a href="#bug_report"
                            class="list-group-item list-group-item-action border-0">{{ __('Bug Report') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->timesheet) && $result->timesheet == 'on')
                        <a href="#timesheet" class="list-group-item list-group-item-action border-0">{{ __('Timesheet') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->tracker_details) && $result->tracker_details == 'on')
                        <a href="#tracker_details"
                            class="list-group-item list-group-item-action border-0">{{ __('Tracker details') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->expense) && $result->expense == 'on')
                        <a href="#expense" class="list-group-item list-group-item-action border-0">{{ __('Expense') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->activity) && $result->activity == 'on')
                        <a href="#activity"
                            class="list-group-item list-group-item-action border-0">{{ __('Activity Log') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            @if (isset($result->basic_details) && $result->basic_details == 'on')
                <div id="basic" class="">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-list text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info d-flex align-items-center justify-content-between gap-3 text-end">
                                        <h6 class="mb-0">{{ __('Total Task') }}</h6>
                                        <span class="h4 mb-0">{{ $project_data['task']['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-currency-dollar text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info d-flex align-items-center justify-content-between gap-3 text-end">
                                        <h6 class="mb-0">{{ __('Done Task') }}</h6>
                                        <span class="h4 mb-0">{{ $project_data['task']['done'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-report-money text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info d-flex align-items-center justify-content-between gap-3 text-end">
                                        <h6 class="mb-0">{{ __('Total Milestone') }}</h6>
                                        <span class="h4 mb-0">{{ count($project->milestones) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 mb-4">
                            <div class="card h-100 mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img {{ $project->img_image }} alt=""
                                                class="img-user wid-45 rounded border-2 border border-primary">
                                        </div>
                                        <div class="d-block  align-items-center justify-content-between w-100">
                                            <div class="mb-3 mb-sm-0">
                                                <h5 class="mb-1"> {{ $project->project_name }}</h5>
                                                <p class="mb-0 text-sm">
                                                <div class="progress-wrapper">
                                                    <span class="progress-percentage"><small
                                                            class="font-weight-bold">{{ __('Completed:') }} :
                                                        </small>{{ $project->project_progress_copy($usr->id)['percentage'] }}</span>
                                                    <div class="progress progress-xs mt-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            aria-valuenow="{{ $project->project_progress_copy($usr->id)['percentage'] }}"
                                                            aria-valuemin="0" aria-valuemax="100"
                                                            style="width: {{ $project->project_progress_copy($usr->id)['percentage'] }};">
                                                        </div>
                                                    </div>
                                                </div>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <h4 class="mt-3 mb-1"></h4>
                                            <p> {{ $project->description }}</p>
                                        </div>
                                    </div>
                                    <div class="card bg-primary mb-0">
                                        <div class="card-body">
                                            <div class="d-block d-sm-flex align-items-center justify-content-between">
                                                <div class="row align-items-center">
                                                    <span class="text-white text-sm">{{ __('Start Date') }}</span>
                                                    <h5 class="text-white text-nowrap">
                                                        {{ Utility::getDateFormated($project->start_date) }}</h5>
                                                </div>
                                                <div class="row align-items-center">
                                                    <span class="text-white text-sm">{{ __('End Date') }}</span>
                                                    <h5 class="text-white text-nowrap">
                                                        {{ Utility::getDateFormated($project->end_date) }}</h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <span class="text-white text-sm">{{ __('Client') }}</span>
                                                <h5 class="text-white text-nowrap">
                                                    {{ !empty($project->client) ? $project->client->name : '-' }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mb-4">
                            <div class="card h-100 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-primary badge">
                                            <i class="ti ti-clipboard-list"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-0">{{ __('Last 7 days task done') }}</p>
                                            <h4 class="mb-0">{{ $project_data['task_chart']['total'] }}</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted">{{ __('Day Left') }}</span>
                                        </div>
                                        <span>{{ $project_data['day_left']['day'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['day_left']['percentage'] }}%"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">

                                            <span class="text-muted">{{ __('Open Task') }}</span>
                                        </div>
                                        <span>{{ $project_data['open_task']['tasks'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['open_task']['percentage'] }}%"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted">{{ __('Completed Milestone') }}</span>
                                        </div>
                                        <span>{{ $project_data['milestone']['total'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['milestone']['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mb-4">
                            <div class="card h-100 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-primary badge">
                                            <i class="ti ti-clipboard-list"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-0">{{ __('Last 7 days hours spent') }}</p>
                                            <h4 class="mb-0">{{ $project_data['timesheet_chart']['total'] }}</h4>

                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted">{{ __('Total project time spent') }}</span>
                                        </div>
                                        <span>{{ $project_data['time_spent']['total'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['time_spent']['percentage'] }}%"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">

                                            <span class="text-muted">{{ __('Allocated hours on task') }}</span>
                                        </div>
                                        <span>{{ $project_data['task_allocated_hrs']['hrs'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['task_allocated_hrs']['percentage'] }}%"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted">{{ __('User Assigned') }}</span>
                                        </div>
                                        <span>{{ $project_data['user_assigned']['total'] }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $project_data['user_assigned']['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->member) && $result->member == 'on')
                <div id="members" class="col-md-12">
                    <div class="card ">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Members') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Avatar') }}</th>
                                            <th> {{ __('Name') }}</th>
                                            <th> {{ __('Type') }}</th>
                                            <th> {{ __('Email') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    @if ($project->users || $project->users != '' || $project->users != null)
                                        @foreach ($project->users as $user)
                                            <tr>
                                                <td class="">
                                                    <img @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('/storage/uploads/avatar/avatar.png') }}" @endif
                                                        class="avatar rounded border-2 border border-primary"
                                                        style="height:36px;width:36px;">
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->type }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->task) && $result->task == 'on')
                <div id="task" class="">
                    <div class="card" style="background-color:transparent !important">
                        <div class="card-header"
                            style="padding: 25px 35px !important; background-color:#ffffff !important">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="row">
                                    <h5 class="mb-0">{{ __('Task') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('Task') }}</th>
                                            <th scope="col">{{ __('Project') }}</th>
                                            <th scope="col">{{ __('Stage') }}</th>
                                            <th scope="col">{{ __('Assigned To') }}</th>
                                            <th scope="col">{{ __('Priority') }}</th>
                                            <th scope="col">{{ __('End Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @if (!empty(count($tasks)) > 0)
                                            @foreach ($tasks as $task)
                                                <tr>
                                                    <td>{{ $task->name }}</td>
                                                    <td>{{ $task->project->project_name }}</td>
                                                    <td>{{ $task->stage->name }}</td>
                                                    <td>
                                                        <div class="avatar-group">
                                                            @if ($task->users()->count() > 0)
                                                                @if ($users = $task->users())
                                                                    @foreach ($users as $key => $user)
                                                                        @if ($key < 3)
                                                                            <a href="#"
                                                                                class="avatar rounded-circle avatar-sm">
                                                                                <img data-original-title="{{ !empty($user) ? $user->name : '' }}"
                                                                                    @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('/storage/uploads/avatar/avatar.png') }}" @endif
                                                                                    title="{{ $user->name }}"
                                                                                    class="hweb">
                                                                            </a>
                                                                        @else
                                                                            @break
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                                @if (count($users) > 3)
                                                                    <a href="#"
                                                                        class="avatar rounded-circle avatar-sm">
                                                                        <img data-original-title="{{ !empty($user) ? $user->name : '' }}"
                                                                            @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('/storage/uploads/avatar/avatar.png') }}" @endif
                                                                            class="hweb">
                                                                    </a>
                                                                @endif
                                                            @else
                                                                {{ __('-') }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="status_badge badge p-2 px-3 rounded bg-{{ __(\App\Models\ProjectTask::$priority_color[$task->priority]) }}">{{ __(\App\Models\ProjectTask::$priority[$task->priority]) }}</span>
                                                    </td>
                                                    <td
                                                        class="{{ strtotime($task->end_date) < time() ? 'text-danger' : '' }}">
                                                        {{ Utility::getDateFormated($task->end_date) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <th scope="col" colspan="7">
                                                    <h6 class="text-center">{{ __('No tasks found') }}</h6>
                                                </th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->milestone) && $result->milestone == 'on')
                <div id="milestone" class="">
                    <div class="card" style="overflow-x: none;">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Milestones') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="" class="table  px-2">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Start Date') }}</th>
                                            <th>{{ __('Due Date') }}</th>
                                            <th>{{ __('Task') }}</th>
                                            <th>{{ __('Cost') }}</th>
                                            <th>{{ __('Progress') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty(count($project->milestones)) > 0)
                                            @foreach ($project->milestones as $key => $milestone)
                                                <tr>
                                                    <td>{{ $milestone->title }}</td>
                                                    <td>
                                                        <span
                                                            class="badge-xs status_badge badge bg-{{ \App\Models\Project::$status_color[$milestone->status] }} p-2 px-3 rounded">
                                                            {{ __(\App\Models\Project::$project_status[$milestone->status]) }}
                                                        </span>
                                                    </td>
                                                    @if ($milestone->start_date)
                                                        <td>{{ $milestone->start_date }}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                    @if ($milestone->due_date)
                                                        <td>{{ $milestone->due_date }}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                    <td>{{ $milestone->tasks->count() . ' ' . __('Tasks') }}</td>
                                                    <td>{{ $user->priceFormat($milestone->cost) }}
                                                    </td>
                                                    <td>
                                                        <div class="progress_wrapper">
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $milestone->progress }}%;"
                                                                    aria-valuenow="55" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="progress_labels">
                                                                <div class="total_progress">
                                                                    <strong>
                                                                        @if ($milestone->progress)
                                                                            {{ $milestone->progress }}%
                                                                        @else
                                                                            0%
                                                                        @endif
                                                                    </strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <th scope="col" colspan="7">
                                                    <h6 class="text-center">{{ __('No milestone found') }}</h6>
                                                </th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->attachment) && $result->attachment == 'on')
                <div id="attachment" class="">
                    <div class="card" style="overflow-x: none;">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Files') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @if ($project->projectAttachments()->count() > 0)
                                    @foreach ($project->projectAttachments() as $attachment)
                                        <li class="list-group-item px-0">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col mb-3 mb-sm-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="div">
                                                            <h6 class="m-0">{{ $attachment->name }}</h6>
                                                            <small
                                                                class="text-muted">{{ $attachment->file_size }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto text-sm-end d-flex align-items-center">
                                                    <div class="action-btn ms-2">
                                                        <a href="{{ asset(Storage::url('tasks/' . $attachment->file)) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Download') }}"
                                                            class="btn btn-sm bg-primary" download>
                                                            <i class="ti ti-download text-white"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    <div class="py-5">
                                        <h6 class="h6 text-center">{{ __('No Attachments Found.') }}</h6>
                                    </div>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->bug_report) && $result->bug_report == 'on')
                <div id="bug_report">
                    <div class="card" style="background-color:transparent !important">
                        <div class="card-header"
                            style="padding: 25px 35px !important; background-color:#ffffff !important">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="row">
                                    <h5 class="mb-0">{{ __('Bug Report') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Bug Id') }}</th>
                                            <th> {{ __('Assign To') }}</th>
                                            <th> {{ __('Bug Title') }}</th>
                                            <th> {{ __('Start Date') }}</th>
                                            <th> {{ __('Due Date') }}</th>
                                            <th> {{ __('Status') }}</th>
                                            <th> {{ __('Priority') }}</th>
                                            <th> {{ __('Created By') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty(count($bugs)) > 0)
                                            @foreach ($bugs as $bug)
                                                <tr>
                                                    <td>{{ $user->bugNumberFormat($bug->bug_id) }}</td>
                                                    <td>{{ !empty($bug->assignTo) ? $bug->assignTo->name : '' }}</td>
                                                    <td>{{ $bug->title }}</td>
                                                    <td>{{ $user->dateFormat($bug->start_date) }}</td>
                                                    <td>{{ $user->dateFormat($bug->due_date) }}</td>
                                                    <td>{{ !empty($bug->bug_status) ? $bug->bug_status->title : '' }}</td>
                                                    <td>{{ $bug->priority }}</td>
                                                    <td>{{ $bug->createdBy->name }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <th scope="col" colspan="7">
                                                    <h6 class="text-center">{{ __('No Bug found') }}</h6>
                                                </th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->timesheet) && $result->timesheet == 'on')
                <div id="timesheet" class="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card notfound-timesheet1">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Timesheet') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="" id="timesheet-table-view" style="width:100%;overflow: auto">
                                    </div>
                                </div>
                            </div>

                            <div class="card notfound-timesheet text-center">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Timesheet') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="page-error">
                                        <div class="page-inner">
                                            <div class="page-description">
                                                {{ __("We couldn't find any data") }}
                                            </div>
                                            <div class="page-search">
                                                <p class="text-muted mt-3">
                                                    {{ __("Sorry we can't find any timesheet records on this week.") }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->tracker_details) && $result->tracker_details == 'on')
                <div id="tracker_details" class="">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Tracker details') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-border-style ">
                            <div class="table-responsive">
                                <table class=" table" id="selection-datatable">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Description') }}</th>
                                            <th> {{ __('Project') }}</th>
                                            <th> {{ __('Task') }}</th>
                                            <th> {{ __('Start Time') }}</th>
                                            <th> {{ __('End Time') }}</th>
                                            <th>{{ __('Total Time') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($treckers as $trecker)
                                            @php
                                                $total_name = App\Models\Utility::second_to_time($trecker->total_time);
                                            @endphp
                                            <tr>
                                                <td>{{ __($trecker->name) }}</td>
                                                <td>{{ __($trecker->project_name) }}</td>
                                                <td>{{ __($trecker->project_task) }}</td>
                                                <td>{{ __(date('H:i:s', strtotime($trecker->start_time))) }}</td>
                                                <td>{{ __(date('H:i:s', strtotime($trecker->end_time))) }}</td>
                                                <td>{{ __($total_name) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->expense) && $result->expense == 'on')
                <div id="expense">
                    <div class="card" style="background-color:transparent !important">
                        <div class="card-header"
                            style="padding: 25px 35px !important; background-color:#ffffff !important">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="row">
                                    <h5 class="mb-0">{{ __('Expense') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('Attachment') }}</th>
                                            <th scope="col">{{ __('Name') }}</th>
                                            <th scope="col">{{ __('Task') }}</th>
                                            <th scope="col">{{ __('Date') }}</th>
                                            <th scope="col">{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @if (isset($project->expense) && !empty($project->expense) && count($project->expense) > 0)
                                            @foreach ($project->expense as $expense)
                                                <tr>
                                                    <th scope="row">
                                                        @if (!empty($expense->attachment))
                                                            <a href="{{ asset(Storage::url($expense->attachment)) }}"
                                                                class="btn btn-sm btn-primary btn-icon rounded-pill"
                                                                data-bs-toggle="tooltip" title="{{ __('Download') }}"
                                                                download>
                                                                <span class="btn-inner--icon"><i
                                                                        class="ti ti-download"></i></span>
                                                            </a>
                                                        @else
                                                        @endif
                                                    </th>
                                                    <td>{{ $expense->name }}</td>
                                                    <td>{{ !empty($expense->task) ? $expense->task->name : '-' }}</td>
                                                    <td>{{ !empty($expense->date) ? Utility::getDateFormated($expense->date) : '-' }}
                                                    </td>
                                                    <td>{{ $user->priceFormat($expense->amount) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <th scope="col" colspan="5">
                                                    <h6 class="text-center">{{ __('No Expense Found.') }}</h6>
                                                </th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($result->activity) && $result->activity == 'on')
                <div id="activity" class="">
                    <div class="card  activity-scroll">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Activity') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 vertical-scroll-cards">
                            @if (!empty(count($project->activities)) > 0)
                                @foreach ($project->activities as $activity)
                                    <div class="card p-2 mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary badge">
                                                    <i class="ti {{ $activity->logIcon($activity->log_type) }}"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-0">{{ __($activity->log_type) }}</h6>
                                                    <p class="text-muted text-sm mb-0">{!! $activity->getRemark() !!}</p>
                                                </div>
                                            </div>
                                            <p class="text-muted text-sm mb-0">
                                                {{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="7">
                                        <h6 class="text-center">{{ __('No activities found') }}</h6>
                                    </th>
                                </tr>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg ss_modale " role="document">
            <div class="modal-content image_sider_div">
            </div>
        </div>
    </div>
@endsection
