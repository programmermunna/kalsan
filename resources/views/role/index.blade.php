@extends('layouts.admin')
@section('page-title')
    {{__('Manage Role')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Role')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="lg" data-url="{{ route('roles.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Role')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
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
                                <th>{{__('Role')}} </th>
                                <th>{{__('Permissions')}} </th>
                                <th width="150">{{__('Action')}} </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                @if($role->name != 'client')
                                    <tr class="font-style">
                                        <td class="Role">{{ $role->name }}</td>
                                        <td class="Permission">
                                            <div class="permission-inner">

                                            @foreach ($role->permissions()->pluck('name') as $permissionName)
                                            <span
                                                class="badge rounded p-2 m-1 px-3 bg-primary">{{ $permissionName }}</span>
                                        @endforeach
                                            </div>
                                        </td>
                                        <td class="Action">
                                        <span>
                                            @can('edit role')
                                                <div class="action-btn  me-2">
                                                <a href="#" class="mx-3 bg-info btn btn-sm align-items-center" data-url="{{ route('roles.edit',$role->id) }}" data-ajax-popup="true"  data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Role Edit')}}">
                                                <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endcan
                                                @if($role->name != 'Employee')
                                                    @can('delete role')
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                            <a href="#" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>
                                                            {!! Form::close() !!}
                                                         </div>
                                                    @endcan
                                                @endif
                                        </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
