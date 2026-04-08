@extends('layouts.admin')

@section('page-title')
    {{__('Manage Company Policy')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Company Policy')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
    @can('create company policy')
        <a href="#" data-size="lg" data-url="{{ route('company-policy.create') }}" data-ajax-popup="true" data-title="{{__('Create New Company Policy')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-body table-border-style">
                    <div class="table-responsive">
                    <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Branch')}}</th>
                                <th>{{__('Title')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Attachment')}}</th>
                                @if(Gate::check('edit company policy') || Gate::check('delete company policy'))
                                    <th>{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="font-style">
                            @foreach ($companyPolicy as $policy)
                                @php
                                    $policyPath=asset(Storage::url('uploads/companyPolicy'));
                                @endphp
                                <tr>
                                    <td>{{ !empty($policy->branches)?$policy->branches->name:'' }}</td>
                                    <td>{{ $policy->title }}</td>
                                    <td>{{ $policy->description }}</td>
                                    <td>
                                        @if (!empty($policy->attachment))
                                        <div class="action-btn me-2">

                                            <a  class="mx-3 btn bg-primary btn-sm align-items-center" href="{{ $policyPath . '/' . $policy->attachment }}" download="">
                                                <i class="ti ti-download text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn">
                                            <a class="mx-3 btn bg-secondary btn-sm align-items-center" href="{{ $policyPath . '/' . $policy->attachment }}" target="_blank"  >
                                                <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Preview') }}"></i>
                                            </a>
                                        </div>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </td>
                                    @if(Gate::check('edit company policy') || Gate::check('delete company policy'))
                                        <td>
                                            @can('edit company policy')
                                            <div class="action-btn  me-2">
                                                <a href="#" data-url="{{ route('company-policy.edit',$policy->id)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Company Policy')}}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                            </div>
                                                @endcan
                                                @can('delete company policy')
                                                <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['company-policy.destroy', $policy->id],'id'=>'delete-form-'.$policy->id]) !!}
                                            <a href="#" class="mx-3 btn bg-danger btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$policy->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                                {!! Form::close() !!}
                                            </div>
                                            @endif
                                        </td>
                                    @endif
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
