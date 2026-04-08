@extends('layouts.admin')

@section('page-title')
    {{__('Manage Leave')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Manage Leave')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create leave')
            <a href="#" data-size="lg" data-url="{{ route('leave.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Leave')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                @if(\Auth::user()->type!='Employee')
                                    <th>{{__('Employee')}}</th>
                                @endif
                                <th>{{__('Leave Type')}}</th>
                                <th>{{__('Applied On')}}</th>
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th>{{__('Total Days')}}</th>
                                <th>{{__('Leave Reason')}}</th>
                                <th>{{__('status')}}</th>
                                @can('edit leave')
                                    <th width="200px">{{__('Action')}}</th>
                                @endcan
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($leaves as $item)
                                <tr>
                                    @if(\Auth::user()->type!='Employee')
                                        <td>{{ !empty($item->employees)?$item->employees->name:'' }}</td>
                                    @endif
                                    <td>{{ !empty($item->leaveType)?$item->leaveType->title:'' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($item->applied_on )}}</td>
                                    <td>{{ \Auth::user()->dateFormat($item->start_date ) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($item->end_date )  }}</td>
                                    <td>{{ $item->total_leave_days }}</td>
                                    <td>{{ $item->leave_reason }}</td>
                                    <td>

                                        @if($item->status=="Pending")<div class="status_badge badge bg-warning p-2 px-3 rounded">{{ $item->status }}</div>
                                        @elseif($item->status=="Approved")
                                            <div class="status_badge badge bg-success p-2 px-3 rounded">{{ $item->status }}</div>
                                        @else($item->status=="Reject")
                                            <div class="status_badge badge bg-danger p-2 px-3 rounded">{{ $item->status }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(\Auth::user()->type == 'Employee')
                                            @if($item->status == "Pending")
                                                @can('edit leave')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('leave/'.$item->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Leave')}}" class="mx-3 btn btn-sm  align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                @endcan
                                            @endif
                                        @else
                                        <div class="action-btn me-2">
                                            <a href="#" data-url="{{ URL::to('leave/'.$item->id.'/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Leave Action')}}" class="mx-3 btn btn-sm  align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('Leave Action')}}" data-original-title="{{__('Leave Action')}}">
                                                <i class="ti ti-caret-right text-white"></i> </a>
                                        </div>
                                            @can('edit leave')
                                            <div class="action-btn me-2">
                                                <a href="#" data-url="{{ URL::to('leave/'.$item->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Leave')}}" class="mx-3 btn btn-sm  align-items-center bg-info"  data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i></a>
                                            </div>
                                            @endcan
                                        @endif
                                        @can('delete leave')
                                            <div class="action-btn">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                                <a href="#" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();">
                                                    <i class="ti ti-trash text-white"></i></a>
                                                {!! Form::close() !!}
                                            </div>
                                        @endif
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

@push('script-page')
    <script>
        $(document).on('change', '#employee_id', function () {
            var employee_id = $(this).val();
            var leave_type_id = "{{ isset($leave) ? $leave->leave_type_id : null }}";
            leaveCount(employee_id, leave_type_id)
        });

        function leaveCount(employee_id, leave_type_id = null) {
            $.ajax({
                url: '{{route('leave.jsoncount')}}',
                type: 'POST',
                data: {
                    "employee_id": employee_id, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {

                    $('#leave_type_id').empty();
                    $('#leave_type_id').append('<option value="">{{__('Select Leave Type')}}</option>');

                    $.each(data, function (key, value) {

                        var selected = (leave_type_id == value.id) ? 'selected' : '';
                        if (value.total_leave >= value.days) {
                            $('#leave_type_id').append('<option value="' + value.id + '" disabled ' + selected + '>' + value.title + '&nbsp(' + value.total_leave + '/' + value.days + ')</option>');
                        } else {
                            $('#leave_type_id').append('<option value="' + value.id + '" ' + selected + '>' + value.title + '&nbsp(' + value.total_leave + '/' + value.days + ')</option>');
                        }
                    });
                }
            });
        }

    </script>
@endpush
