@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{ __('Support') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{ __('Support') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Support') }}</li>
@endsection


@section('action-btn')
    <div class="float-end">
        <a href="{{ route('support.index') }}" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list"></i>
        </a>

        <a href="#" data-size="lg" data-url="{{ route('support.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Support') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('filter')
@endsection
@section('content')
    <div class="row">
        @if (count($supports) > 0)
            @foreach ($supports as $support)
                <div class="col-xxl-3 col-lg-4 col-sm-6 mb-4">
                    <div class="support-user-card d-flex flex-column h-100">
                        <div class="user-info-wrp d-flex flex-1 align-items-center gap-3 border-bottom pb-3 mb-3">
                            <div class="user-image rounded-1 border-1 border border-primary">
                                <img alt=""
                                    @if (!empty($support->createdBy) && !empty($support->createdBy->avatar)) src="{{ asset(Storage::url('uploads/avatar')) . '/' . $support->createdBy->avatar }}" @else  src="{{ asset(Storage::url('uploads/avatar')) . '/avatar.png' }}" @endif
                                    height="100%" width="100%">
                                @if ($support->replyUnread() > 0)
                                    <span class="avatar-child avatar-badge bg-success"></span>
                                @endif
                            </div>
                            <div class="user-info d-flex align-items-center flex-1">
                                <div class="user-content flex-1">
                                    <h5 class="mb-1">
                                        <a href="{{ route('support.reply', \Crypt::encrypt($support->id)) }}"
                                            class="dashboard-link">{{ !empty($support->createdBy) ? $support->createdBy->name : '' }}</a>
                                    </h5>
                                    <span class="text-sm text-muted text-break">{{ $support->subject }}</span>
                                </div>
                                @if (!empty($support->attachment))
                                    <a href="{{ asset(Storage::url('uploads/supports')) . '/' . $support->attachment }}"
                                        download="" class="btn btn-sm btn-light shadow" target="_blank"
                                        data-bs-toggle="tooltip" title="{{ __('Download') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div
                            class="project-info-wrp d-flex align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                            <div class="project-info flex-1 f-w-600">
                                <span class="text-muted">{{ __('Code: ') }}</span>
                                <span>{{ $support->ticket_code }}</span>
                            </div>
                            <div class="project-info flex-1 text-end f-w-600">
                                <span class="text-muted">{{ __('Priority: ') }}</span>
                                @if ($support->priority == 0)
                                    <span
                                        class="badge bg-primary p-1 px-2">{{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                @elseif($support->priority == 1)
                                    <span
                                        class="badge bg-info p-1 px-2">{{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                @elseif($support->priority == 2)
                                    <span
                                        class="badge bg-warning p-1 px-2">{{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                @elseif($support->priority == 3)
                                    <span
                                        class="badge bg-danger p-1 px-2">{{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="date-wrp d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="date d-flex align-items-center gap-2">
                                <div class="date-icon d-flex align-items-center justify-content-center">
                                    <i class="ti ti-calendar text-white"></i>
                                </div>
                                <span class="text-sm">{{ \Auth::user()->dateFormat($support->created_at) }}</span>
                            </div>
                            <div class="action-btn-wrp d-flex align-items-center gap-2">
                                <a href="{{ route('support.reply', \Crypt::encrypt($support->id)) }}"
                                    data-title="{{ __('Support Reply') }}" class="btn btn-sm bg-warning"
                                    data-bs-toggle="tooltip" title="{{ __('Reply') }}"
                                    data-original-title="{{ __('Reply') }}">
                                    <i class="ti ti-corner-up-left text-white"></i>
                                </a>
                                @if (\Auth::user()->id == $support->ticket_created)
                                    <a href="#" data-size="lg" data-url="{{ route('support.edit', $support->id) }}"
                                        data-ajax-popup="true" data-title="{{ __('Edit Support') }}"
                                        class="btn btn-sm bg-info" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                        data-original-title="{{ __('Edit') }}">
                                        <i class="ti ti-pencil text-white"></i>
                                    </a>
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['support.destroy', $support->id],
                                        'id' => 'delete-form-' . $support->id,
                                    ]) !!}
                                    <a href="#!" class="btn btn-sm bs-pass-para bg-danger" data-bs-toggle="tooltip"
                                        title="{{ __('Delete') }}" data-original-title="{{ __('Delete') }}"
                                        data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?"
                                        data-confirm-yes="document.getElementById('delete-form-{{ $support->id }}').submit();">
                                        <i class="ti ti-trash text-white"></i>
                                    </a>
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center">
                <i class="fas fa-folder-open text-primary fs-40"></i>
                <h3>{{ __('Opps...') }}</h3>
                <h6> {!! __('No Data Found') !!} </h6>
            </div>
        @endif
    </div>
@endsection
