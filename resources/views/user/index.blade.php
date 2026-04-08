@extends('layouts.admin')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
    {{ __('Manage User') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Client') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'HR')
            <a href="{{ route('user.userlog') }}"
                class="btn btn-primary-subtle me-1 btn-sm {{ Request::segment(1) == 'user' }} " data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ __('User Logs History') }}"><i class="ti ti-user-check"></i>
            </a>
        @endif
        @can('create user')
            <a href="#" data-size="lg" data-url="{{ route('users.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}" title="{{ __('Create User') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="row">
        @foreach ($users as $user)
            <div class="col-xxl-3 col-lg-4 col-sm-6 mb-4">
                <div class="user-card d-flex flex-column h-100">
                    <div class="user-card-top d-flex align-items-center justify-content-between flex-1 gap-2 mb-3">
                        <div class="badge bg-primary p-1 px-2">
                            {{ ucfirst($user->type) }}
                        </div>
                        <div class="btn-group card-option">
                            <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>

                            <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                @can('edit user')
                                    <a href="#!" data-size="lg" data-url="{{ route('users.edit', $user->id) }}"
                                        data-ajax-popup="true" class="dropdown-item"
                                        data-bs-original-title="{{ \Auth::user()->type == 'super admin' ? __('Edit Company') : __('Edit User') }}">
                                        <i class="ti ti-pencil"></i>
                                        <span>{{ __('Edit') }}</span>
                                    </a>
                                @endcan

                                @can('delete user')
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['users.destroy', $user['id']],
                                        'id' => 'delete-form-' . $user['id'],
                                    ]) !!}
                                    <a href="#!" class="dropdown-item bs-pass-para">
                                        <i class="ti ti-trash"></i>
                                        <span>
                                            @if ($user->delete_status != 0)
                                                {{ __('Delete') }}
                                            @else
                                                {{ __('Restore') }}
                                            @endif
                                        </span>
                                    </a>
                                    {!! Form::close() !!}
                                @endcan

                                <a href="#!" data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                    data-ajax-popup="true" data-size="md" class="dropdown-item"
                                    data-bs-original-title="{{ __('Reset Password') }}">
                                    <i class="ti ti-adjustments"></i>
                                    <span> {{ __('Reset Password') }}</span>
                                </a>

                                @if ($user->is_enable_login == 1)
                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}" class="dropdown-item">
                                        <i class="ti ti-road-sign"></i>
                                        <span class="text-danger"> {{ __('Login Disable') }}</span>
                                    </a>
                                @elseif ($user->is_enable_login == 0 && $user->password == null)
                                    <a href="#" data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                        data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                        data-title="{{ __('New Password') }}" class="dropdown-item">
                                        <i class="ti ti-road-sign"></i>
                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}" class="dropdown-item">
                                        <i class="ti ti-road-sign"></i>
                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="user-info-wrp d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div class="user-image rounded-1 border-1 border border-primary">
                            <img src="{{ !empty($user->avatar) ? Utility::get_file('uploads/avatar/') . $user->avatar : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                                alt="user-image" height="100%" width="100%">
                        </div>
                        <div class="user-info flex-1">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            @if ($user->delete_status == 0)
                                <h6 class="mb-1">{{ __('Soft Deleted') }}</h6>
                            @endif
                            <span class="text-sm text-muted text-break">{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="date-wrp d-flex align-items-center justify-content-between gap-2">
                        @php
                            $date = \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d');
                            $time = \Carbon\Carbon::parse($user->last_login_at)->format('H:i:s');
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
        <div class="col-xxl-3 col-lg-4 col-sm-6 mb-4">
            <a href="#" class="btn-addnew-project border-primary" data-ajax-popup="true"
                data-url="{{ route('users.create') }}"
                data-title="{{ \Auth::user()->type == 'super admin' ? __('Create Company') : __('Create User') }}"
                data-bs-toggle="tooltip" title=""
                data-bs-original-title="{{ \Auth::user()->type == 'super admin' ? __('Create Company') : __('Create User') }}">
                <div class="bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-3 mb-2">
                    {{ \Auth::user()->type == 'super admin' ? __('Create Company') : __('Create User') }}</h6>
                <p class="text-muted text-center mb-0">
                    {{ \Auth::user()->type == 'super admin' ? __('Click here to add new company') : __('Click here to add new user') }}
                </p>
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
