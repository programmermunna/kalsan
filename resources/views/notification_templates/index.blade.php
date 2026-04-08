@extends('layouts.admin')
@section('page-title')
    {{ $notification_template->name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Template') }}</li>
@endsection
@push('pre-purpose-css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@section('action-btn')

@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table datatable" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th scope="col" class="sort" data-sort="name"> {{ __('Name') }}</th>
                                @if (\Auth::user()->type == 'company')
                                    <th class="">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notification_templates as $notification_template)
                                <tr>
                                    <td>{{ $notification_template->name }}</td>
                                    <td>
                                        @if (\Auth::user()->type == 'company')
                                            <div class="">
                                                <div class="dt-buttons">
                                                    <span>
                                                        <div class="action-btn">
                                                            <a href="{{ route('manage.notification.language', [$notification_template->id, \Auth::user()->lang]) }}"
                                                                class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                data-bs-toggle="tooltip" data-bs-original-title="{{__('View')}}" title="">
                                                                <span class="text-white"><i class="ti ti-eye"></i></span>
                                                            </a>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
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

