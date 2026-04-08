@extends('layouts.admin')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{ __('Manage Client') }}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Client') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('clients.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            title="{{ __('Create New Client') }}" data-bs-original-title="{{ __('create New Client') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')
<div class="row">
    @foreach ($clients as $client)
        <div class="col-xxl-3 col-lg-4 col-sm-6 mb-4">
            <div class="client-card d-flex flex-column h-100">
                <div class="client-info-wrp d-flex flex-1 align-items-center gap-3 border-bottom pb-3 mb-3">
                    <div class="client-image rounded-1 border-1 border border-primary">
                        <img src="{{ !empty($client->avatar) ? asset(Storage::url('uploads/avatar/' . $client->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                            alt="client-image" height="100%" width="100%">
                    </div>
                    <div class="client-info flex-1">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-1 flex-1">{{ $client->name }}</h5>
                            <div class="btn-group card-option">
                                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                    @can('edit client')
                                        <a href="#!" data-size="md"
                                            data-url="{{ route('clients.edit', $client->id) }}" data-ajax-popup="true"
                                            class="dropdown-item" data-bs-original-title="{{ __('Edit Client') }}">
                                            <i class="ti ti-pencil"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>
                                    @endcan

                                    @can('delete client')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['clients.destroy', $client['id']],
                                            'id' => 'delete-form-' . $client['id'],
                                        ]) !!}
                                        <a href="#!" class="dropdown-item bs-pass-para">
                                            <i class="ti ti-trash"></i>
                                            <span>
                                                @if ($client->delete_status != 0)
                                                    {{ __('Delete') }}
                                                @else
                                                    {{ __('Restore') }}
                                                @endif
                                            </span>
                                        </a>

                                        {!! Form::close() !!}
                                    @endcan
                                    @if ($client->is_enable_login == 1)
                                        <a href="{{ route('users.login', \Crypt::encrypt($client->id)) }}"
                                            class="dropdown-item">
                                            <i class="ti ti-road-sign"></i>
                                            <span class="text-danger"> {{ __('Login Disable') }}</span>
                                        </a>
                                    @elseif ($client->is_enable_login == 0 && $client->password == null)
                                        <a href="#"
                                            data-url="{{ route('clients.reset', \Crypt::encrypt($client->id)) }}"
                                            data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                            data-title="{{ __('New Password') }}" class="dropdown-item">
                                            <i class="ti ti-road-sign"></i>
                                            <span class="text-success"> {{ __('Login Enable') }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('users.login', \Crypt::encrypt($client->id)) }}"
                                            class="dropdown-item">
                                            <i class="ti ti-road-sign"></i>
                                            <span class="text-success"> {{ __('Login Enable') }}</span>
                                        </a>
                                    @endif


                                    <a href="#!"
                                        data-url="{{ route('clients.reset', \Crypt::encrypt($client->id)) }}"
                                        data-ajax-popup="true" class="dropdown-item"
                                        data-bs-original-title="{{ __('Reset Password') }}">
                                        <i class="ti ti-adjustments"></i>
                                        <span> {{ __('Reset Password') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <span class="text-sm text-muted text-break">{{ $client->email }}</span>
                    </div>
                </div>
                <div class="project-info-wrp d-flex align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                    <div class="project-info flex-1 f-w-600">
                        <span>{{ __('Deals: ') }}</span>
                        @if ($client->clientDeals)
                            {{ $client->clientDeals->count() }}
                        @endif
                    </div>
                    <div class="project-info flex-1 text-end f-w-600">
                        <span>{{ __('Projects: ') }}</span>
                        @if ($client->clientProjects)
                            {{ $client->clientProjects->count() }}
                        @endif
                    </div>
                </div>
                <div class="date-wrp d-flex align-items-center justify-content-between gap-2">
                    @php
                        $date = \Carbon\Carbon::parse($client->last_login_at)->format('Y-m-d');
                        $time = \Carbon\Carbon::parse($client->last_login_at)->format('H:i:s');
                    @endphp
                    <div class="date d-flex align-items-center gap-2">
                        <div class="date-icon d-flex align-items-center justify-content-center">
                            <i class="f-16 ti ti-calendar text-white"></i>
                        </div>
                        <span class="text-sm">{{ $date }}</span>
                    </div>
                    <div class="time d-flex align-items-center gap-2">
                        <div class="time-icon d-flex align-items-center justify-content-center">
                            <i class="f-16 ti ti-clock text-white"></i>
                        </div>
                        <span class="text-sm">{{ $time }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
        <a href="#" data-size="md" data-url="{{ route('clients.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create New Client') }}"
            class="btn-addnew-project border-primary">
            <div class="bg-primary proj-add-icon">
                <i class="ti ti-plus"></i>
            </div>
            <h6 class="mt-3 mb-2">{{ __('Create Client') }}</h6>
            <p class="text-muted text-center mb-0">{{ __('Click here to add new client') }}</p>
        </a>
    </div>
</div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#password_switch', function() {
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);

            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });
        $(document).on('click', '.login_enable', function() {
            setTimeout(function() {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>
@endpush
