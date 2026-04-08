@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Bulk Attendance') }}
@endsection
@push('script-page')
    <script>
        $('#present_all').click(function(event) {

            if (this.checked) {
                $('.present').each(function() {
                    this.checked = true;
                });

                $('.present_check_in').removeClass('d-none');
                $('.present_check_in').addClass('d-block');

            } else {
                $('.present').each(function() {
                    this.checked = false;
                });
                $('.present_check_in').removeClass('d-block');
                $('.present_check_in').addClass('d-none');

            }
        });

        $('.present').click(function(event) {


            var div = $(this).parent().parent().parent().parent().find('.present_check_in');
            if (this.checked) {
                div.removeClass('d-none');
                div.addClass('d-block');
            } else {
                div.removeClass('d-block');
                div.addClass('d-none');
            }

        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance') }}</li>
@endsection



@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['attendanceemployee.bulkattendance'], 'method' => 'get', 'id' => 'bulkattendance_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('date', isset($_GET['date']) ? $_GET['date'] : '', ['class' => 'form-control', 'placeholder' => __('Select Date')]) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                            {{ Form::select('branch', $branch, isset($_GET['branch']) ? $_GET['branch'] : '', ['class' => 'form-control select', 'required']) }}
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                            {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select', 'required']) }}
                                        </div>
                                    </div>



                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('bulkattendance_filter').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                    data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">

                    {{ Form::open(['route' => ['attendanceemployee.bulkattendances'], 'method' => 'post']) }}
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th width="10%">{{ __('Employee Id') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>
                                        <div class="custom-control d-flex align-items-center gap-2">
                                            <input class="form-check-input mt-0" type="checkbox" name="present_all"
                                                id="present_all" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label f-w-700" for="present_all">
                                                {{ __('Attendance') }}</label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    @php
                                        $attendance = $employee->present_status(
                                            $employee->id,
                                            isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'),
                                        );
                                    @endphp
                                    <tr>
                                        <td class="Id">
                                            <input type="hidden" value="{{ $employee->id }}" name="employee_id[]">
                                            <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                                                class=" btn btn-outline-primary">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ !empty($employee->branch) ? $employee->branch->name : '' }}</td>
                                        <td>{{ !empty($employee->department) ? $employee->department->name : '' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="form-check-input present mb-0" type="checkbox"
                                                            name="present-{{ $employee->id }}"
                                                            id="present{{ $employee->id }}"
                                                            {{ !empty($attendance) && $attendance->status == 'Present' ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="present{{ $employee->id }}"></label>
                                                    </div>
                                                </div>
                                                <div class="present_check_in {{ empty($attendance) ? 'd-none' : '' }} ">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <label class="form-label mb-0">{{ __('In') }}</label>
                                                        <div class="time-box">
                                                            <input type="time" class="form-control timepicker"
                                                                name="in-{{ $employee->id }}"
                                                                value="{{ !empty($attendance) && $attendance->clock_in != '00:00:00' ? $attendance->clock_in : \Utility::getValByName('company_start_time') }}">
                                                        </div>

                                                        <label for="inputValue"
                                                            class="form-label mb-0">{{ __('Out') }}</label>
                                                        <div class="time-box">
                                                            <input type="time" class="form-control timepicker"
                                                                name="out-{{ $employee->id }}"
                                                                value="{{ !empty($attendance) && $attendance->clock_out != '00:00:00' ? $attendance->clock_out : \Utility::getValByName('company_end_time') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="attendance-btn float-end pt-4">
                        <input type="hidden" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}"
                            name="date">
                        <input type="hidden" value="{{ isset($_GET['branch']) ? $_GET['branch'] : '' }}" name="branch">
                        <input type="hidden" value="{{ isset($_GET['department']) ? $_GET['department'] : '' }}"
                            name="department">
                        {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

    </div>
@endsection


