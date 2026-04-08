@extends('layouts.admin')
@section('page-title')
    {{__('Manage Visa')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Visa')}}</li>

@endsection
@section('action-btn')
    <div class="float-end">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('visa.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm bg-brown-subtitle me-1">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('visa.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-secondary me-1">
            <i class="ti ti-file-export"></i>
        </a> --}}

        <a href="#" data-size="lg" data-url="{{ route('visa.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-bs-original-title="{{__('Create')}}" title="{{__('Create New Visa')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
         <div class="col-sm-12">
            <div class=" mt-2 {{isset($_GET['category']) ? 'show' : ''}}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['visa.index'], 'method' => 'GET', 'id' => 'product_service']) }}
                        <div class="row d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('issue_date', __('Issue Date'),['class'=>'form-label'])}}
                                    {{ Form::date('issue_date', isset($_GET['issue_date'])?$_GET['issue_date']:'', array('class' => 'form-control month-btn','id'=>'pc-daterangepicker-1','placeholder'=>__('Issue Date'))) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('country', __('Country'),['class'=>'form-label'])}}
                                    {{ Form::select('country', $country, isset($_GET['country']) ? $_GET['country'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('customer', __('Customer'),['class'=>'form-label'])}}
                                    {{ Form::select('customer', $customers, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('status', __('Status'),['class'=>'form-label'])}}
                                    {{Form::select('visa_status', ['' => 'Select Status', 'Applied' => 'Applied', 'Approved' => 'Approved', 'Rejected' => 'Rejected'], isset($_GET['visa_status']) ? $_GET['visa_status'] : '', ['class' => 'form-control select']) }}

                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary me-1"
                                onclick="document.getElementById('product_service').submit(); return false;"
                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}" >
                                 <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('visa.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                data-original-title="{{ __('Reset') }}">
                                 <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                             </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('#')}}</th>
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Applican Name')}}</th>
                                <th>{{__('Passport #')}}</th>
                                <th>{{__('Country')}}</th>
                                <th>{{__('Visa Type')}}</th>
                                <th>{{__('Vender')}}</th>
                                <th>{{__('Payment Status')}}</th>
                                <th>{{__('Visa Status')}}</th>
                                <th>{{__(key: 'Date')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($visas as $index => $visa)
                                <tr class="font-style">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $visa->customer_name}}</td>
                                    <td>{{ $visa->name}}</td>
                                    <td>{{ $visa->passport_no}}</td>
                                    <td>{{ $visa->country}}</td>
                                    <td>{{ $visa->visa_type}}</td>
                                    <td>{{ $visa->vender_name}}</td>
                                    <td><span class="status_badge badge bg-danger p-2 px-3 rounded">{{ $visa->payment_status ?? 'N/A'}}</span></td>
                                    <td><span class="status_badge badge bg-secondary p-2 px-3 rounded">{{ $visa->visa_status ?? 'N/A'}}</span></td>
                                    <td>{{ $visa->issue_date }}</td>

                                    @if(Gate::check('edit visa') || Gate::check('delete visa'))
                                        <td class="Action">

                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 bg-warning btn btn-sm align-items-center" data-url="{{ route('visa.detail', $visa->id) }}"
                                                   data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Warehouse Details')}}" data-title="{{__('Warehouse Details')}}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>



                                            @can('edit visa')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 bg-info btn btn-sm  align-items-center" data-url="{{ route('visa.edit', $visa->id) }}" data-ajax-popup="true"  data-size="lg " data-bs-toggle="tooltip" title="{{__('Edit')}}"  data-title="{{__('Edit Visa')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete visa')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['visa.destroy', $visa->id], 'id' => 'delete-form-' . $visa->id]) !!}
                                                    <a href="#" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
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

