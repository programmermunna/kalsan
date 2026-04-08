@extends('layouts.admin')
@section('page-title')
    {{ __('View Project Type') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project-types.index') }}">{{ __('Project Types') }}</a></li>
    <li class="breadcrumb-item">{{ __('View') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('edit project type')
            <a href="{{ route('project-types.edit', $projectType->project_type_id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                <i class="ti ti-edit"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ __('Project Type Name') }}:</strong>
                                {{ $projectType->project_type_name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ __('Created By') }}:</strong>
                                {{ $projectType->createdBy ? $projectType->createdBy->name : '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ __('Created At') }}:</strong>
                                {{ \App\Models\Utility::getDateFormated($projectType->created_at) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ __('Last Updated At') }}:</strong>
                                {{ \App\Models\Utility::getDateFormated($projectType->updated_at) }}
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Associated Projects') }}</h5>
                            @if(count($projectType->projects) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Project Name') }}</th>
                                                <th>{{ __('Start Date') }}</th>
                                                <th>{{ __('End Date') }}</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($projectType->projects as $project)
                                                <tr>
                                                    <td>{{ $project->name }}</td>
                                                    <td>{{ \App\Models\Utility::getDateFormated($project->start_date) }}</td>
                                                    <td>{{ $project->end_date ? \App\Models\Utility::getDateFormated($project->end_date) : '-' }}</td>
                                                    <td>{{ $project->customer ? $project->customer->name : '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">{{ __('No projects associated with this project type.') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <a href="{{ route('project-types.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection