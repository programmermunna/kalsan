@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Contract') }}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Contract') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('contract.index') }}" data-bs-toggle="tooltip" title="{{ __('List View') }}"
            class="btn btn-sm bg-light-blue-subtitle">
            <i class="ti ti-list"></i>
        </a>
        @if (\Auth::user()->type == 'company')
            <a href="#" data-size="lg" data-url="{{ route('contract.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create New Contract') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        @foreach ($contracts as $contract)
            <div class="col-xxl-3 col-lg-4 col-sm-6 col-12 mb-4">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex align-items-center gap-2 justify-content-between p-3">
                        <h6 class="mb-0"><a href="{{ route('contract.show', $contract->id) }}"
                                class="dashboard-link">{{ $contract->subject }}</a></h6>
                        @if (\Auth::user()->type == 'company')
                            <div class="btn-group card-option">
                                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                    <a href="#!" data-size="lg" data-url="{{ route('contract.edit', $contract->id) }}"
                                        data-ajax-popup="true" class="dropdown-item"
                                        data-bs-original-title="{{ __('Edit User') }}">
                                        <i class="ti ti-pencil"></i>
                                        <span>{{ __('Edit') }}</span>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['contract.destroy', $contract->id]]) !!}
                                    <a href="#!" class="dropdown-item bs-pass-para">
                                        <i class="ti ti-trash"></i>
                                        <span> {{ __('Delete') }}</span>
                                    </a>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-3 text-center">
                        <p class="mb-0 f-w-500 text-dark">
                            {{ $contract->description }}
                        </p>
                    </div>
                    <div class="card-footer py-0 p-3">
                        <ul class="list-group list-group-flush">
                            @if (\Auth::user()->type != 'client')
                                <li class="list-group-item px-0 border-0 pb-0">
                                    <div class="d-flex align-items-center justify-content-center gap-2 f-w-600 client-name">
                                        <span>{{ __('Client') }}:</span>
                                        {{ !empty($contract->clients) ? $contract->clients->name : '' }}
                                    </div>
                                </li>
                            @endif
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-column">
                                    <span>{{ __('Contract Type') }}:</span>
                                    <span
                                        class="badge status_badge bg-secondary p-2 rounded">{{ !empty($contract->types) ? $contract->types->name : '' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-column">
                                    <span>{{ __('Contract Value') }}:</span>
                                    <span
                                        class="badge status_badge bg-secondary p-2 rounded">{{ \Auth::user()->priceFormat($contract->value) }}</span>
                                </div>
                            </li>

                            <li class="list-group-item px-0">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <small>{{ __('Start Date') }}:</small>
                                        <div class="h6 mt-1 mb-0">{{ \Auth::user()->dateFormat($contract->start_date) }}
                                        </div>
                                    </div>
                                    <div>
                                        <small>{{ __('End Date') }}:</small>
                                        <div class="h6 mt-1 mb-0">{{ \Auth::user()->dateFormat($contract->end_date) }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
