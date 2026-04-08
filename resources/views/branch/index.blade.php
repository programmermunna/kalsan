@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Branch') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Branch') }}</li>
@endsection

@section('action-btn')

@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @include('layouts.hrm_setup')
        </div>
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-end">
                @can('create branch')
                    <a href="#" data-url="{{ route('branch.create') }}" data-ajax-popup="true"
                        data-title="{{ __('Create New Branch') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body table-border-style">

                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Branch') }}</th>
                                            <th>{{ __('Manager') }}</th>
                                            <th>{{ __('Address') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th width="200px" class="flex-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-style">
                                        @foreach ($branches as $index => $branch)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $branch->name }}</td>
                                                <td>{{ $branch->manager }}</td>
                                                <td>{{ $branch->address }}</td>
                                                <td>{{ $branch->mobile }}</td>
                                                <td>{{ $branch->email }}</td>
                                                <td>{{ $branch->type }}</td>
                                                <td class="Action">
                                                    <span>
                                                        @can('edit branch')
                                                            <div class="action-btn me-2">

                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm align-items-center bg-info"
                                                                    data-url="{{ URL::to('branch/' . $branch->id . '/edit') }}"
                                                                    data-ajax-popup="true" data-title="{{ __('Edit Branch') }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-original-title="{{ __('Edit') }}"><i
                                                                        class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('delete branch')
                                                            <div class="action-btn ">
                                                                {!! Form::open([
            'method' => 'DELETE',
            'route' => ['branch.destroy', $branch->id],
            'id' => 'delete-form-' . $branch->id,
        ]) !!}

                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $branch->id }}').submit();"><i
                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
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
        </div>
    </div>
@endsection
