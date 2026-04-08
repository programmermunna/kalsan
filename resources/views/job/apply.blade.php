@php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
    $company_logo = Utility::getValByName('company_logo_light');
    $setting = App\Models\Utility::colorset();
    $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';

    $getseo= App\Models\Utility::getSeoSetting();
    $metatitle =  isset($getseo['meta_title']) ? $getseo['meta_title'] :'';
    $metsdesc= isset($getseo['meta_desc'])?$getseo['meta_desc']:'';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image'])?$getseo['meta_image']:'';
    $get_cookie = \App\Models\Utility::getCookieSetting();

@endphp

    <!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        {{ !empty($companySettings['header_text']) ? $companySettings['header_text']->value : config('app.name', 'ERPGO SaaS') }}
        - {{ __('Career') }}</title>

    <meta name="title" content="{{$metatitle}}">
    <meta name="description" content="{{$metsdesc}}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{$metatitle}}">
    <meta property="og:description" content="{{$metsdesc}}">
    <meta property="og:image" content="{{$meta_image.$meta_logo}}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{$metatitle}}">
    <meta property="twitter:description" content="{{$metsdesc}}">
    <meta property="twitter:image" content="{{$meta_image.$meta_logo}}">


    <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')}}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}" id="stylesheet">
    @if (isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"id="main-style-link">
    @endif
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @if (isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="{{$color}}">
<div class="job-wrapper">
    <div class="job-content">
        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-light.png') }}" alt="logo" style="width: 90px">

                </a>
            </div>
        </nav>
        <section class="job-banner">
            <div class="job-banner-bg">
                <img src="{{asset('/storage/uploads/job/banner.png')}}" alt="">
            </div>
            <div class="container">
                <div class="job-banner-content text-center text-white">
                    <h1 class="text-white mb-3">
                        {{__(' We help')}} <br> {{__('businesses grow')}}
                    </h1>
                    <p>{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>
                    </p>
                </div>
            </div>
        </section>
        <section class="apply-job-section">
            <div class="container">
                <div class="apply-job-wrapper bg-light">
                    <div class="section-title text-center">
                        <h2 class="h1 mb-3"> {{$job->title}}</h2>
                        <div class="d-flex flex-wrap justify-content-center gap-1 mb-4">
                            @foreach (explode(',', $job->skill) as $skill)
                                <span class="badge rounded p-2 bg-primary">{{ $skill }}</span>
                            @endforeach
                        </div>
                        @if(!empty($job->branches)?$job->branches->name:'')
                            <p> <i class="ti ti-map-pin ms-1"></i> {{!empty($job->branches)?$job->branches->name:''}}</p>
                        @endif

                    </div>
                    <div class="apply-job-form">
                        <h2 class="mb-4">{{__('Apply for this job')}}</h2>
                        {{Form::open(array('route'=>array('job.apply.data',$job->code),'method'=>'post', 'enctype' => "multipart/form-data" , 'class'=>'needs-validation', 'novalidate'))}}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name'),['class'=>'form-label'])}}
                                    {{Form::text('name',null,array('class'=>'form-control name','required'=>'required', 'placeholder'=>__('Enter Name')))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('email',__('Email'),['class'=>'form-label'])}}
                                    {{Form::text('email',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Email')))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-Mobile name="phone" label="{{__('Phone')}}" placeholder="Enter Phone"></x-Mobile>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(!empty($job->applicant) && in_array('dob',explode(',',$job->applicant)))
                                    <div class="form-group">
                                        {!! Form::label('dob', __('Date of Birth'),['class'=>'form-label']) !!}
                                        {!! Form::date('dob', old('dob'), ['class' => 'form-control datepicker w-100','required'=>'required']) !!}
                                    </div>
                                @endif
                            </div>
                            @if(!empty($job->applicant) && in_array('gender',explode(',',$job->applicant)))
                                <div class="form-group col-md-6 ">
                                    {!! Form::label('gender', __('Gender'),['class'=>'form-label']) !!}
                                    <div class="d-flex radio-check">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_male" value="Male" name="gender" class="custom-control-input" >
                                            <label class="custom-control-label" for="g_male">{{__('Male')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_female" value="Female" name="gender" class="custom-control-input">
                                            <label class="custom-control-label" for="g_female">{{__('Female')}}</label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($job->applicant) && in_array('country',explode(',',$job->applicant)))
                                <div class="form-group col-md-6 ">
                                    {{Form::label('country',__('Country'),['class'=>'form-label'])}}
                                    {{Form::text('country',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Country')))}}
                                </div>
                                <div class="form-group col-md-6 country">
                                    {{Form::label('state',__('State'),['class'=>'form-label'])}}
                                    {{Form::text('state',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter State')))}}
                                </div>
                                <div class="form-group col-md-6 country">
                                    {{Form::label('city',__('City'),['class'=>'form-label'])}}
                                    {{Form::text('city',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter City')))}}
                                </div>
                            @endif

                            @if(!empty($job->visibility) && in_array('profile',explode(',',$job->visibility)))
                                <div class="form-group col-md-6 ">
                                    {{Form::label('profile',__('Profile'),['class'=>'col-form-label'])}}
                                    <input type="file" class="form-control" name="profile" id="profile" data-filename="profile_create" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                    <img id="blah" src="" class="mt-3" width="25%"/>
                                    <p class="profile_create"></p>
                                </div>

                            @endif

                            @if(!empty($job->visibility) && in_array('resume',explode(',',$job->visibility)))
                                <div class="form-group col-md-6 ">
                                    {{Form::label('resume',__('CV / Resume'),['class'=>'col-form-label'])}}
                                    <input type="file" class="form-control" name="resume" id="resume" data-filename="resume_create" onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])" required>
                                    <img id="blah1" class="mt-3" src="" width="25%"/>
                                    <p class="resume_create"></p>

                                </div>
                            @endif

                            @if(!empty($job->visibility) && in_array('letter',explode(',',$job->visibility)))
                                <div class="form-group col-md-12 ">
                                    {{Form::label('cover_letter',__('Cover Letter'),['class'=>'form-label'])}}
                                    {{Form::textarea('cover_letter',null,array('class'=>'form-control','rows'=>'3', 'placeholder'=>__('Enter Cover Letter')))}}
                                </div>
                            @endif

                            @foreach($questions as $question)
                                <div class="form-group col-md-12  question question_{{$question->id}}">
                                    {{Form::label($question->question,$question->question,['class'=>'form-label'])}}
                                    <input type="text" class="form-control" placeholder="Write here...." name="question[{{$question->question}}]" {{($question->is_required=='yes')?'required':''}}>
                                </div>
                            @endforeach
                            <div class="col-12">
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">{{__('Submit your application')}}</button>
                                </div>
                            </div>

                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"> </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('js/site.core.js') }}"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script src="{{ asset('js/demo.js') }} "></script>
<script src="{{ asset('js/custom.js') }}"></script>

@if($get_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
</body>

@if($message = Session::get('success'))
    <script>
        show_toastr('success', '{!! $message !!}');
    </script>
@endif
@if($message = Session::get('error'))
    <script>
        show_toastr('error', '{!! $message !!}');
    </script>
    @endif
    </body>

</html>
