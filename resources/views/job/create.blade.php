@extends('layouts.admin')
@section('page-title')
    {{ __('Create Job') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('job.index') }}">{{ __('Job') }}</a></li>
    <li class="breadcrumb-item">{{ __('Job Create') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
@endpush
@push('script-page')
    <script src="{{ asset('js/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary"
            })
        });
    </script>
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
@endpush
@php
    $settings = \App\Models\Utility::settings();
@endphp
@section('action-btn')
    <div class="float-end">
        {{-- start for ai module --}}
        @if ($settings['ai_chatgpt_enable'] == 'on')
            <a href="#" data-size="lg" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['job']) }}" data-bs-placement="top"
                data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"> </i> <span>{{ __('Generate with AI') }}</span>
            </a>
        @endif
        {{-- end for ai module --}}
    </div>
@endsection
@section('content')
    {{ Form::open(['url' => 'job', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
    <div class="row mt-3 row-gap-1">
        <div class="col-md-6">
            <div class="card card-fluid mb-0 h-100">
                <div class="card-body job-create">
                    <div class="row">
                        <div class="form-group col-md-12">
                            {!! Form::label('title', __('Job Title'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {!! Form::text('title', old('title'), [
                                'class' => 'form-control',
                                'required' => 'required',
                                'placeholder' => __('Enter Job Title'),
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('branch', __('Branch'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {{ Form::select('branch', $branches, null, ['class' => 'form-control select', 'required' => 'required']) }}
                            <div class="text-xs mt-1">
                                {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('category', __('Job Category'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {{ Form::select('category', $categories, null, ['class' => 'form-control select', 'required' => 'required']) }}
                            <div class="text-xs mt-1">
                                {{ __('Create job category here.') }} <a href="{{ route('job-category.index') }}"><b>{{ __('Create job category') }}</b></a>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            {!! Form::label('position', __('Positions'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {!! Form::number('position', old('positions'), [
                                'class' => 'form-control',
                                'required' => 'required',
                                'placeholder' => __('Enter Positions'),
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('status', __('Status'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {{ Form::select('status', $status, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('start_date', __('Start Date'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {!! Form::date('start_date', old('start_date'), ['class' => 'form-control ', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('end_date', __('End Date'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {!! Form::date('end_date', old('end_date'), ['class' => 'form-control ', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-12">
                            {!! Form::label('skill', __('Skill'), ['class' => 'form-label']) !!}<x-required></x-required>
                            <input type="text" class="form-control" value="" data-toggle="tags" name="skill"
                                placeholder="Skill" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="card card-fluid mb-0 h-100">
                <div class="card-body job-create ">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>{{ __('Need to ask ?') }}</h6>
                                <div class="my-4">
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="applicant[]" value="gender"
                                            id="check-gender">
                                        <label class="form-check-label" for="check-gender">{{ __('Gender') }} </label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="applicant[]" value="dob"
                                            id="check-dob">
                                        <label class="form-check-label" for="check-dob">{{ __('Date Of Birth') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="applicant[]" value="country"
                                            id="check-country">
                                        <label class="form-check-label" for="check-country">{{ __('Country') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>{{ __('Need to show option ?') }}</h6>
                                <div class="my-4">
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="visibility[]" value="profile"
                                            id="check-profile">
                                        <label class="form-check-label" for="check-profile">{{ __('Profile Image') }}
                                        </label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="visibility[]" value="resume"
                                            id="check-resume">
                                        <label class="form-check-label" for="check-resume">{{ __('Resume') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="visibility[]" value="letter"
                                            id="check-letter">
                                        <label class="form-check-label" for="check-letter">{{ __('Cover Letter') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="visibility[]" value="terms"
                                            id="check-terms">
                                        <label class="form-check-label"
                                            for="check-terms">{{ __('Terms And Conditions') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <h6>{{ __('Custom Question') }}</h6>
                            <div class="my-4">
                                @foreach ($customQuestion as $question)
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="custom_question[]"
                                            value="{{ $question->id }}" id="custom_question_{{ $question->id }}">
                                        <label class="form-check-label"
                                            for="custom_question_{{ $question->id }}">{{ $question->question }} </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-fluid mb-0 h-100">
                <div class="card-body ">
                    <div class="form-group col-md-12">
                        {!! Form::label('sescription', __('Job Description'), ['class' => 'form-label mb-4']) !!}<x-required></x-required>
                        <textarea class="form-control summernote-simple-2" name="description" id="exampleFormControlTextarea1"
                            rows="8" required></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-fluid mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-warp">
                        <div class="form-group mb-0">
                            {!! Form::label('requirement', __('Job Requirement'), ['class' => 'form-label']) !!}
                        </div>
                        @if ($settings['ai_chatgpt_enable'] == 'on')
                            <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm"
                                data-ajax-popup-over="true" id="grammarCheck"
                                data-url="{{ route('grammar', ['grammar']) }}" data-bs-placement="top"
                                data-title="{{ __('Grammar check with AI') }}">
                                <i class="ti ti-rotate"></i> <span>{{ __('Grammar check with AI') }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="form-group mt-2">
                        <textarea class="form-control summernote-simple" name="requirement" id="exampleFormControlTextarea2" rows="8"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-end">
            <div class="form-group">
                <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('job.index') }}';"
                class="btn btn-secondary me-1">
                <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
            </div>
        </div>
    </div>
    {{ Form::close() }}
    </div>
@endsection
