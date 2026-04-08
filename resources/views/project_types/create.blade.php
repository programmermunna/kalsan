@extends('layouts.admin')
@section('page-title')
{{ __('Create Project Type') }}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => 'project-types.store']) }}
                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('project_type_name', __('Project Type Name'), ['class' => 'form-label']) }}
                        {{ Form::text('project_type_name', '', ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="col-12">
                        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
                        <a href="{{ route('project-types.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection