@extends('layouts.admin')
@php
     $profile=\App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
    {{__('Manage User Log')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('User Log')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('user.userlog'),'method'=>'get','id'=>'user_userlog')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{Form::label('month',__('Month'),['class'=>'form-label'])}}
                                            {{Form::month('month',isset($_GET['month'])?$_GET['month']:date('Y-m'),array('class'=>'month-btn form-control'))}}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('users', __('User'),['class'=>'form-label']) }}
                                            {{ Form::select('users', $filteruser,isset($_GET['users'])?$_GET['users']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-sm btn-primary me-1" onclick="document.getElementById('user_userlog').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('user.userlog')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('User Name') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Last Login') }}</th>
                                    <th>{{ __('Ip') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Device') }}</th>
                                    <th>{{ __('OS') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userdetails as $user)
                                    @php
                                        $userdetail = json_decode($user->Details);
                                    @endphp
                                    <tr>
                                        <td>{{ $user->user_name }}</td>
                                        <td>
                                            <span class="me-5 badge p-2 px-3 rounded bg-success status_badge">{{$user->user_type}}</span>
                                        </td>
                                        <td>{{ !empty($user->date) ? $user->date : '-' }}</td>
                                        <td>{{ $user->ip }}</td>
                                        <td>{{ !empty($userdetail->country)?$userdetail->country:'-' }}</td>
                                        <td>{{ $userdetail->device_type }}</td>
                                        <td>{{ $userdetail->os_name }}</td>
                                        <td>
                                            <div class="action-btn  me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-warning" data-size="lg" data-url="{{ route('user.userlogview', [$user->id]) }}"
                                                   data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="" data-title="{{ __('View User Logs') }}" data-bs-original-title="{{ __('View') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('delete user')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE','route' => ['user.userlogdestroy', $user->id],'id' => 'delete-form-' . $user->id,]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete" aria-label="Delete">
                                                        <i class="ti ti-trash text-white text-white"></i>
                                                    </a>
                                                    </form>
                                                </div>
                                            @endcan
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
