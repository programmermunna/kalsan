@extends('layouts.admin')
@section('page-title')
    {{ __('Project Types') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Project Types') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
            <a href="{{ route('project-types.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create New Project Type') }}">
                <i class="ti ti-plus"></i>
            </a>
        {{-- @endcan --}}
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
                                    <th>{{ __('Project Type Name') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th width="10%">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectTypes as $projectType)
                                    <tr>
                                        <td>{{ $projectType->project_type_name }}</td>
                                        <td>{{ $projectType->createdBy ? $projectType->createdBy->name : '-' }}</td>
                                        <td>{{ \App\Models\Utility::getDateFormated($projectType->created_at) }}</td>
                                        <td class="Action">
                                            <span>
                                                {{-- @can('edit project type') --}}
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="{{ route('project-types.edit', $projectType->project_type_id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="ti ti-edit text-white"></i>
                                                        </a>
                                                    </div>
                                                {{-- @endcan --}}
                                                @can('view project type')
                                                    <div class="action-btn bg-warning ms-2">
                                                        <a href="{{ route('project-types.show', $projectType->project_type_id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                {{-- @can('delete project type') --}}
                                                <div class="action-btn bg-danger ms-2">
                                                    <a href="#" 
                                                       onclick="if(confirm('Are you sure you want to delete this? This action cannot be undone.')) { document.getElementById('delete-form-{{ $projectType->project_type_id }}').submit(); } event.preventDefault();"
                                                       class="mx-3 btn btn-sm d-inline-flex align-items-center" 
                                                       title="Delete">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                </div>
                                                
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['project-types.destroy', $projectType->project_type_id], 'id' => 'delete-form-' . $projectType->project_type_id]) !!}
                                                {!! Form::close() !!}
                                                
                                                {{-- @endcan --}}
                                            </span>
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
