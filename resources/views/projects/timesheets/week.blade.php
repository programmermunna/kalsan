
<table class="table mb-0">
    <thead>
    <tr>
        <th>{{__('Title')}}</th>
        @foreach ($days['datePeriod'] as $key => $perioddate)
            <th scope="col" class="heading"><span>{{ $perioddate->format('D') }}</span><span>{{ $perioddate->format('d M') }}</span></th>
        @endforeach
        <th class="text-center">{{ __('Total') }}</th>
    </tr>
    </thead>
    <tbody class="tbody">
    @if(isset($allProjects) && $allProjects == true)
        @foreach ($timesheetArray as $key => $timesheet)
            <tr>
                <td class="project-name" data-bs-toggle="tooltip" title="{{__('Project')}}">{{ $timesheet['project_name'] }}</td>
            </tr>
            @foreach ($timesheet['taskArray'] as $key => $taskTimesheet)
                @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                    <tr class="timesheet-user">
                        <td class="task-name" data-bs-toggle="tooltip" title="{{__('Task')}}">{{ $taskTimesheet['task_name'] }}</td>
                        @foreach ($dateTimeArray['week'] as $dateSubArray)
                            <td>
                                <input class="form-control {{ $dateSubArray['time'] != '00:00' ? 'border-dark' : '-' }} wid-120 task-time day-time"
                                       data-type="{{ $dateSubArray['type'] }}" data-user-id="{{ $dateTimeArray['user_id'] }}"
                                       data-project-id="{{ $timesheet['project_id'] }}" data-task-id="{{ $taskTimesheet['task_id'] }}"
                                       data-date="{{ $dateSubArray['date'] }}" data-ajax-timesheet-popup="true"
                                       data-url="{{ $dateSubArray['url'] }}"
                                       type="text" value="{{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}">
                            </td>
                        @endforeach
                        <td class="text-center total-task-time day-time">
                            <input class="form-control border-dark wid-120 total-task-time day-time"
                                   type="text" value="{{$dateTimeArray['totaltime'] != '00:00' ? $dateTimeArray['totaltime'] : '00:00' }}">
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @else
        @foreach ($timesheetArray as $key => $timesheet)
            <tr>
                <td class="task-name">{{ $timesheet['task_name'] }}</td>
                @foreach ($timesheet['dateArray'] as $day => $datetime)
                    <td>
                        <input class="form-control {{ $datetime['time'] != '00:00' ? 'border-dark' : '00:00' }} wid-120 task-time day-time1"
                               data-type="{{ $datetime['type'] }}" data-task-id="{{ $timesheet['task_id'] }}"
                               data-date="{{ $datetime['date'] }}" data-ajax-timesheet-popup="true"
                               data-url="{{ $datetime['url'] }}" type="text"
                               value="{{ $datetime['time'] != '00:00' ? $datetime['time'] : '00:00' }}">
                    </td>
                @endforeach
                <td class="text-center total-task-time day-time1"> <input class="form-control border-dark wid-120 task-time day-time1"  type="text" value="{{$timesheet['totaltime'] != '00:00' ? $timesheet['totaltime'] : '00:00' }}" >
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
    <tfooter>
        <tr class="bg-primary">
            <td>{{ __('Total') }}</td>
            @foreach ($totalDateTimes as $key => $totaldatetime)
                <td class="total-date-time" > <input class="form-control bg-transparent {{ $totaldatetime != '00:00' ? 'border-dark' : 'border-dark' }}  wid-120" type="text" value="{{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}"> </td>
            @endforeach
            <td class="text-center total-value1">
                <input class="form-control bg-transparent {{ $calculatedtotaltaskdatetime != '00:00' ? 'border-dark' : 'border-dark' }} wid-120" type="text" value="{{ $calculatedtotaltaskdatetime != '00:00' ? $calculatedtotaltaskdatetime : '00:00' }}">
            </td>
        </tr>
    </tfooter>
</table>

<div class="text-center d-flex align-items-center justify-content-center mt-4 mb-5 timelogged">
    <h5 class="f-w-900 me-2 mb-0">{{ __('Time Logged') }} :</h5>
    <span class="p-2  f-w-900 rounded  bg-primary d-inline-block border border-dark">{{ $calculatedtotaltaskdatetime . __(' Hours') }}</span>
</div>

