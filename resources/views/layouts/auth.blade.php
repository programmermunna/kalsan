<!DOCTYPE html>
@php
$logo = \App\Models\Utility::get_file('uploads/logo/');
$company_logo = Utility::getValByName('company_logo_dark');
$company_logos = Utility::getValByName('company_logo_light');
$company_favicon = Utility::getValByName('company_favicon');
$setting = \App\Models\Utility::settings();
$color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';
if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
    $themeColor = 'custom-color';
} else {
    $themeColor = $color;
}
$company_logo = \App\Models\Utility::GetLogo();
$SITE_RTL = isset($setting['SITE_RTL']) ? $setting['SITE_RTL'] : 'off';
$lang = \App::getLocale('lang');
if ($lang == 'ar' || $lang == 'he') {
    $setting['SITE_RTL'] = 'on';
} elseif ($setting['SITE_RTL'] == 'on') {
    $setting['SITE_RTL'] = 'on';
} else {
    $setting['SITE_RTL'] = 'off';
}

$getseo = App\Models\Utility::getSeoSetting();
$metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
$metsdesc = isset($getseo['meta_desc']) ? $getseo['meta_desc'] : '';
$meta_image = \App\Models\Utility::get_file('uploads/meta/');
$meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';

@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">
<head>
    <title>{{(Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'Miftah System')}} - @yield('page-title')</title>

    <meta name="title" content="{{$metatitle}}">
    <meta name="description" content="{{$metsdesc}}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{$metatitle}}">
    <meta property="og:description" content="{{$metsdesc}}">
    <meta property="og:image" content="{{$meta_image . $meta_logo}}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{$metatitle}}">
    <meta property="twitter:description" content="{{$metsdesc}}">
    <meta property="twitter:image" content="{{$meta_image . $meta_logo}}">

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content="Dashboard Template Description"/>
    <meta name="keywords" content="Dashboard Template"/>
    <meta name="author" content="Miftah Technology"/>

    <!-- Favicon icon -->
    <link rel="icon" href="{{$logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}" type="image/x-icon"/>

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    @if ($setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css')}}" id="main-style-link">
    @endif
    @if($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css')}}">
    @endif
    @if($setting['SITE_RTL'] != 'on' && $setting['cust_darklayout'] != 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}" id="main-style-link">
    @endif


    @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css') }}" id="main-style-link">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-dark.css') }}" id="main-style-link">
    @endif


    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
</head>

<body class="{{ $themeColor }}">
    <div class="custom-login">
       {{--  <div class="login-bg-img">
            <img src="{{ isset($setting['color_flag']) && $setting['color_flag'] == 'false' ? asset('assets/images/auth/'.$color.'.svg') : asset('assets/images/auth/theme-3.svg') }}" class="login-bg-1">
            <img src="{{ asset('assets/images/auth/common.svg') }}" class="login-bg-2">
        </div> --}}
        <div class="bg-primary"></div>
        <div class="custom-login-inner">
            {{-- <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">

                        <a class="navbar-brand" href="#">
                            @if ($setting['cust_darklayout'] == 'on')
                                <img class="logo"
                                    src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-light.png') . '?' . time() }}"
                                    alt="" loading="lazy"/>
                            @else
                                <img class="logo"
                                    src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') . '?' . time() }}"
                                    alt="" loading="lazy"/>
                            @endif
                        </a>


                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                @include('landingpage::layouts.buttons')
                                @yield('language-bar')
                            </ul>
                        </div>
                    </div>
                </nav>
            </header> --}}
            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; {{ date('Y') }}
                                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'Storego Saas') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
<!-- [ auth-signup ] end -->

<!-- Required Js -->
<script src="{{ asset('assets/js/vendor-all.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>
    feather.replace();
</script>

@if (\App\Models\Utility::getValByName('cust_darklayout') == 'on')
    <style>
        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }
    </style>
@endif


<script>
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function () {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function (event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }



    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>
@stack('custom-scripts')
@if($setting['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif

<script>
    (function () {
        const open = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function (method, url) {
            if (url.includes('envato.workdo.io')) {
                console.warn('Blocked request to:', url);
                return;
            }
            return open.apply(this, arguments);
        };

        const origFetch = window.fetch;
        window.fetch = function () {
            if (arguments[0] && arguments[0].includes('envato.workdo.io')) {
                console.warn('Blocked fetch to:', arguments[0]);
                return Promise.resolve(new Response('{}', { status: 200 }));
            }
            return origFetch.apply(this, arguments);
        };
    })();
</script>
</body>
</html>
