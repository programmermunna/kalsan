@php
    $logo=\App\Models\Utility::get_file('uploads/logo');
    $company_logo = Utility::getValByName('company_logo_light');
    $setting = App\Models\Utility::settingsById($id);

    $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';
  if(isset($setting['color_flag']) && $setting['color_flag'] == 'true')
    {
        $themeColor = 'custom-color';
    }
    else {
        $themeColor = $color;
    }

    $getseo= App\Models\Utility::getSeoSetting();
    $metatitle =  isset($getseo['meta_title']) ? $getseo['meta_title'] :'';
    $metsdesc= isset($getseo['meta_desc'])?$getseo['meta_desc']:'';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image'])?$getseo['meta_image']:'';
    $get_cookie = \App\Models\Utility::settings();

@endphp
    <!DOCTYPE html>

    <html lang="en" dir="{{ isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

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

    @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
@endif

@if (isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
@endif

@if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] != 'on' && isset($setting['cust_darklayout']) && $setting['cust_darklayout'] != 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
@endif

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @if (isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}">
    @endif

    <style>
        :root {
            --color-customColor: <?= $color ?>;    
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="{{$themeColor}}">
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
                </div>
            </div>
        </section>
        <section class="placedjob-section">
            <div class="container">
                <div class="section-title">
                    @php
                        $totaljob    = \App\Models\Job::where('created_by', '=', $id)->count();

                    @endphp

                    <h2 class="h1 mb-3"> <span class="text-primary">+{{$totaljob}}  </span>{{__('Job openings')}}</h2>
                    <p class="mb-0">{{__('Always looking for better ways to do things, innovate')}} <br> {{__('and help people achieve their goals')}}.</p>
                </div>
                <div class="row g-4">
                    @foreach ($jobs as $job)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 job-card">
                            <div class="job-card-body">
                                <div class="d-flex mb-3 align-items-center justify-content-between ">
                                    <img src="{{asset('/storage/uploads/job/figma.png')}}" alt="">
                                    @if(!empty($job->branches)?$job->branches->name:'')
                                        <span>{{!empty($job->branches)?$job->branches->name:''}} <i  class="ti ti-map-pin ms-1"></i></span>
                                    @endif
                                </div>
                                <h5 class="mb-2">
                                    <a href="{{ route('job.requirement', [$job->code, !empty($job) ? (!empty($job->createdBy->lang) ? $job->createdBy->lang : 'en') : 'en']) }}"
                                       class="text-dark">{{ $job->title }}
                                    </a>
                                </h5>
                                <div class="d-flex mb-3 align-items-start flex-column flex-xl-row flex-md-row flex-lg-column">
                                    <span class="d-inline-block me-2"> <i class="ti ti-circle-plus "></i> {{ $job->position }} {{__('position available')}}</span>
                                </div>

                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @foreach (explode(',', $job->skill) as $skill)
                                        <span class="badge border-primary border rounded p-2">{{ $skill }}</span>
                                    @endforeach

                                </div>

                                <a href="{{ route('job.requirement', [$job->code, !empty($job) ? (!empty($job->createdBy->lang) ? $job->createdBy->lang : 'en') : 'en']) }}" class="btn btn-primary w-100 mt-3">{{__('Read more')}}</a>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </div>
</div>
</body>


<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('js/site.core.js') }}"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script src="{{ asset('js/demo.js') }} "></script>

@if($get_cookie['enable_cookie'] == 'on')
@include('layouts.cookie_consent')
@endif

</body>

</html>
