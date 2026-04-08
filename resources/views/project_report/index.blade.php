@extends('layouts.admin')
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@push('script-page')
@endpush
@section('page-title')
    {{__('Project Reports')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Project Reports')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('All Project')}}</li>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">

<style>
.table.dataTable.no-footer {
    border-bottom: none !important;
}
.display-none {
    display: none !important;
}
</style>
@endpush

@section('content')
    @if(Auth::user()->type == 'company')
        <div class="row">
            <div class="col-sm-12">
                <div class="mt-2 " >
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(['route' => ['project_report.index'], 'method' => 'GET', 'id' => 'project_report_submit']) }}
                                <div class="row d-flex align-items-center justify-content-end">
                                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12 mr-2 mb-0">
                                    <div class="btn-box">
                                        {{ Form::label('users', __('Users'),['class'=>'form-label'])}}
                                        <select class="select form-select" name="all_users" id="all_users">
                                            <option value="" class="">{{ __('All Users') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{isset($_GET['all_users']) && $_GET['all_users']==$user->id?'selected':''}}>{{ $user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('status', __('Status'),['class'=>'form-label'])}}
                                        {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                    <div class="btn-box">
                                        {{ Form::label('start_date', __('Start Date'),['class'=>'form-label'])}}
                                        {{ Form::date('start_date', isset($_GET['start_date'])?$_GET['start_date']:'', array('class' => 'form-control month-btn')) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                    <div class="btn-box">
                                        {{ Form::label('end_date', __('End Date'),['class'=>'form-label'])}}
                                        {{ Form::date('end_date', isset($_GET['end_date'])?$_GET['end_date']:'', array('class' => 'form-control month-btn')) }}

                                    </div>
                                </div>

                                <div class="col-auto float-end ms-2 mt-4">
                                    <a href="#" class="btn btn-sm btn-primary me-1"
                                       onclick="document.getElementById('project_report_submit').submit(); return false;"
                                       data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('project_report.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                           data-bs-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                                        </a>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-xl-12 mt-3">
        <div class="card table-card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead class="">
                            <tr>
                                <th>{{__('Projects')}}</th>
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Projects Members')}}</th>
                                <th>{{__('Completion')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($projects) && !empty($projects) && count($projects) > 0)
                            @foreach ($projects as $key => $project)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0"><a  class="name mb-0 h6 text-sm">{{ $project->project_name }}</a></p>
                                        </div>
                                    </td>
                                    <td>{{ Utility::getDateFormated($project->start_date) }}</td>
                                    <td>{{ Utility::getDateFormated($project->end_date) }}</td>
                                    <td class="">
                                        <div class="avatar-group" id="project_{{ $project->id }}">
                                            @if(isset($project->users) && !empty($project->users) && count($project->users) > 0)
                                                @foreach($project->users as $key => $user)
                                                    @if($key < 3)
                                                        <a href="#" class="avatar rounded-circle project-image-item">
                                                            <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif
                                                            title="{{ $user->name }}" style="height:36px;width:36px;">
                                                        </a>
                                                    @else
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if(count($project->users) > 3)
                                                    <a href="#" class="avatar project-image-item rounded-circle">
                                                        {{-- <img avatar="+ {{ count($project->users)-3 }}" style="height:36px;width:36px;"> --}}
                                                        <span>+ {{ count($project->users)-3 }}</span>

                                                    </a>
                                                @endif
                                            @else
                                                {{ __('-') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-1 text-success">{{ $project->project_progress($project , $last_task->id)['percentage'] }}</h6>
                                        <div class="progress mb-0">
                                            <div class="progress-bar bg-{{ $project->project_progress($project , $last_task->id)['color'] }}" style="width: {{ $project->project_progress($project , $last_task->id)['percentage'] }};"></div>
                                        </div>
                                    </td>
                                    <td class="">
                                        <span class="badge bg-{{\App\Models\Project::$status_color[$project->status]}} p-2 px-3 rounded status_badge">{{ __(\App\Models\Project::$project_status[$project->status]) }}</span>
                                    </td>
                                    <td class="">
                                        @can('manage project')
                                            <div class="action-btn me-2">
                                                <a href="{{ route('project_report.show', $project->id) }}" class="mx-3 bg-warning  btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('View Project Report')}}" data-original-title="{{__('Detail')}}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                        @endcan
                                        @can('edit project')
                                                <div class="action-btn  me-2">
                                                    <a href="#" class="mx-3 bg-info btn btn-sm align-items-center" data-url="{{ URL::to('projects/'.$project->id.'/edit') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Project')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th scope="col" colspan="7"><h6 class="text-center">{{__('No Projects Found.')}}</h6></th>
                            </tr>
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection




