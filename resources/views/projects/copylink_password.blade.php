@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo=\App\Models\Utility::get_file('uploads/logo');
    $company_logo=Utility::getValByName('company_logo');
    $settings = Utility::settings();

@endphp

@section('page-title')
    {{__('Copylink')}}
@endsection

@section('auth-topbar')

@endsection
@section('content')
<div class="card-body">
    <div class="">
        <h2 class="h3">{{__('Password required')}}</h2>
        <h6>{{ __('This document is password-protected. Please enter a password.') }}</h6>
    </div>
    <form method="POST" action="{{ route('projects.link', \Illuminate\Support\Facades\Crypt::encrypt($id)) }}">
        @csrf
            <div class="">
                <div class="form-group ">
                    <label class="form-control-label mt-2 mb-2">{{__('Password')}}</label>
                    <div class="input-group input-group-merge">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn-login btn btn-primary btn-block mt-2" >{{__('Save')}}</button>
                </div>
            </div>
    </form>
</div>
@endsection



