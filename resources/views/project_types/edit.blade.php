@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Project Type') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project-types.index') }}">{{ __('Project Types') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($projectType, ['route' => ['project-types.update', $projectType->project_type_id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{ Form::label('project_type_name', __('Project Type Name'), ['class' => 'form-label']) }}
                            {{ Form::text('project_type_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Project Type Name')]) }}
                        </div>
                        <div class="col-12 text-end">
                            <div class="form-group">
                                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
                                <a href="{{ route('project-types.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection