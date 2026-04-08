@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection
@php
    use App\Models\WebhookSetting;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $logo_light = \App\Models\Utility::getValByName('company_logo_light');
    $logo_dark = \App\Models\Utility::getValByName('company_logo_dark');
    $company_favicon = \App\Models\Utility::getValByName('company_favicon');
    $lang = App\Models\Utility::getValByName('default_language');
    $setting = \App\Models\Utility::colorset();
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    $SITE_RTL = $setting['SITE_RTL'];
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL == 'off';
    }
    $currantLang = Utility::languages();

    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $webhookSetting = WebhookSetting::where('created_by', '=', \Auth::user()->creatorId())->get();
    $google_recaptcha_version = ['v2-checkbox' => __('v2'), 'v3' => __('v3')];

@endphp

{{-- Storage setting --}}
@php
    $file_type = config('files_types');
    $setting = App\Models\Utility::settings();

    $local_storage_validation = $setting['local_storage_validation'];
    $local_storage_validations = explode(',', $local_storage_validation);

    $s3_storage_validation = $setting['s3_storage_validation'];
    $s3_storage_validations = explode(',', $s3_storage_validation);

    $wasabi_storage_validation = $setting['wasabi_storage_validation'];
    $wasabi_storage_validations = explode(',', $wasabi_storage_validation);

@endphp
{{-- end Storage setting --}}

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script>
        $('.summernote-simple0').on('summernote.blur', function() {
            $.ajax({
                url: "{{ route('offerlatter.update', $offerlang) }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    content: $(this).val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('success', response.success, 'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function(response) {

                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                }
            })
        });
        $('.summernote-simple1').on('summernote.blur', function() {
            $.ajax({
                url: "{{ route('joiningletter.update', $joininglang) }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    content: $(this).val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('success', response.success, 'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function(response) {

                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                }
            })
        });
        $('.summernote-simple2').on('summernote.blur', function() {
            $.ajax({
                url: "{{ route('experiencecertificate.update', $explang) }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    content: $(this).val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('success', response.success, 'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function(response) {

                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                }
            })
        });
        $('.summernote-simple3').on('summernote.blur', function() {
            $.ajax({
                url: "{{ route('noc.update', $noclang) }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    content: $(this).val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('success', response.success, 'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function(response) {

                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                }
            })
        });

        //footer notes
        $('.summernote-simple4').on('summernote.blur', function() {

            $.ajax({
                url: "{{ route('system.settings.footernote') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    notes: $(this).val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        // show_toastr('Success', response.success,'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function(response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response, 'error');
                    }
                }
            })
        });
    </script>

    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{ url('/invoices/preview') }}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='proposal_template'], input[name='proposal_color']", function() {
            var template = $("select[name='proposal_template']").val();
            var color = $("input[name='proposal_color']:checked").val();
            $('#proposal_frame').attr('src', '{{ url('/proposal/preview') }}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function() {
            var template = $("select[name='bill_template']").val();
            var color = $("input[name='bill_color']:checked").val();
            $('#bill_frame').attr('src', '{{ url('/bill/preview') }}/' + template + '/' + color);
        });
    </script>

    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })

        $('.colorPicker').on('click', function(e) {
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass('custom-color');
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');

            const input = document.getElementById("color-picker");
            setColor();
            input.addEventListener("input", setColor);

            function setColor() {
                $(':root').css('--color-customColor', input.value);
            }

            $(`input[name='color_flag`).val('true');
        });


        $('.themes-color-change').on('click', function() {

            $(`input[name='color_flag`).val('false');

            var color_val = $(this).data('value');
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass(color_val);
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $('.colorPicker').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);
        });

        $.fn.removeClassRegex = function(regex) {
            return $(this).removeClass(function(index, classes) {
                return classes.split(/\s+/).filter(function(c) {
                    return regex.test(c);
                }).join(' ');
            });
        };


        // storage setting
        $(document).on('change', '[name=storage_setting]', function() {
            if ($(this).val() == 's3') {
                $('.s3-setting').removeClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').addClass('d-none');
            } else if ($(this).val() == 'wasabi') {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').removeClass('d-none');
                $('.local-setting').addClass('d-none');
            } else {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').removeClass('d-none');
            }
        });
    </script>

    <script>
        document.getElementById('company_logo').onchange = function() {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image').src = src
        }
        document.getElementById('company_logo_light').onchange = function() {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image1').src = src
        }
        document.getElementById('company_favicon').onchange = function() {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image2').src = src
        }
    </script>

    <script type="text/javascript">
        $(document).on("click", '.send_email', function(e) {

            e.preventDefault();
            var title = $(this).attr('data-title');
            var size = 'md';
            var url = $(this).attr('data-url');

            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');


                $.post(url, {
                    _token: '{{ csrf_token() }}',
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val(),

                }, function(data) {
                    $('#commonModal .body').html(data);
                });
            }
        });
        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                beforeSend: function() {
                    $('#test_email .btn-create').attr('disabled', 'disabled');
                },
                success: function(data) {
                    if (data.success) {
                        show_toastr('success', data.message, 'success');
                    } else {
                        show_toastr('error', data.message, 'error');
                    }
                    $('#commonModal').modal('hide');


                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        });
    </script>

    <script>
        $(document).on('change', '#vat_gst_number_switch', function() {
            if ($(this).is(':checked')) {
                $('.tax_type_div').removeClass('d-none');
            } else {
                $('.tax_type_div').addClass('d-none');
            }
        });
    </script>

    {{--    for cookie setting --}}
    <script type="text/javascript">
        function enablecookie() {
            const element = $('#enable_cookie').is(':checked');
            $('.cookieDiv').addClass('disabledCookie');
            if (element == true) {
                $('.cookieDiv').removeClass('disabledCookie');
                $("#cookie_logging").attr('checked', true);
            } else {
                $('.cookieDiv').addClass('disabledCookie');
                $("#cookie_logging").attr('checked', false);
            }
        }
    </script>

    <script>
        if ($('#cust-darklayout').length > 0) {
            var custthemedark = document.querySelector("#cust-darklayout");
            custthemedark.addEventListener("click", function() {
                if (custthemedark.checked) {
                    $('#main-style-link').attr('href', '{{ env('APP_URL') }}' +
                        '/public/assets/css/style-dark.css');

                    document.body.style.background = 'linear-gradient(141.55deg, #22242C 3.46%, #22242C 99.86%)';
                    $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . '/logo-light.png' }}');

                } else {
                    document.body.style.setProperty('background',
                        'linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #f0f4f3 99.86%)', 'important');

                    $('#main-style-link').attr('href', '{{ env('APP_URL') }}' + '/public/assets/css/style.css');
                    $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . '/logo-dark.png' }}');

                }
            });
        }
        if ($('#cust-theme-bg').length > 0) {
            var custthemebg = document.querySelector("#cust-theme-bg");
            custthemebg.addEventListener("click", function() {
                if (custthemebg.checked) {
                    document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                    document
                        .querySelector(".dash-header:not(.dash-mob-header)")
                        .classList.add("transprent-bg");
                } else {
                    document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                    document
                        .querySelector(".dash-header:not(.dash-mob-header)")
                        .classList.remove("transprent-bg");
                }
            });
        }
    </script>

    <script>
        $(document).on('keyup change', '.currency_preview', function() {
            var data = $('#currency_setting').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('currency.preview') }}',
                data: data,
                success: function(price) {
                    $('.preview').text(price);
                }
            });
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top setting-sidebar" style="top:30px;">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#brand-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#system-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('System Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#company-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#currency-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Currency Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#tracker-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Time Tracker Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#payment-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#pusher-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Pusher Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#zoom-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Zoom Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            {{-- <a href="#slack-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Slack Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}
                            {{-- <a href="#telegram-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Telegram Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}
                            {{-- <a href="#twilio-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Twilio Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>--}}
                            <a href="#recaptcha-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('ReCaptcha Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-notification-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#storage-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Storage Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#offer-letter-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Offer Letter Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#joining-letter-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Joining Letter Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#experience-certificate-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Experience Certificate Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#noc-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('NOC Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                           {{-- <a href="#google-calender"
                                class="list-group-item list-group-item-action border-0">{{ __('Google Calendar Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}

                           {{-- <a href="#seo-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('SEO Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}

                            {{-- <a href="#webhook-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}

                            <a href="#cookie-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Cookie Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#cache-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Cache Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#chat-gpt-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Chat GPT Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#ip-restriction-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('IP Restriction Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#biometric-attendance" id="biometric-attendance-tab"
                                class="list-group-item list-group-item-action border-0">{{ __('Biometric Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="col-xl-9 setting-menu-div">
                    <!--Brand Settings-->
                    <div id="brand-settings" class="card">
                        {{ Form::model($settings, ['route' => 'business.setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header p-3">
                            <h5>{{ __('Brand Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your brand details') }}</small>
                        </div>

                        <div class="card-body p-3">
                            <div class="row row-gap-1 mb-4">
                                {{-- <div class="col-lg-4 col-sm-6 col-md-6">
                                    <div class="card mb-0">
                                        <div class="card-header p-3">
                                            <h5>{{ __('Logo dark') }}</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="setting-card setting-logo-box">
                                                <div class="logo-content">
                                                    <img id="image"
                                                        src="{{ $logo . '/' . (isset($logo_dark) && !empty($logo_dark) ? $logo_dark : 'logo-dark.png') . '?' . time() }}">
                                                </div>
                                                <div class="choose-files mt-3 text-center">
                                                    <label for="company_logo">
                                                        <div class=" bg-primary company_logo_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" name="company_logo_dark" id="company_logo"
                                                            class="form-control file setting_logo"
                                                            data-filename="company_logo_update">
                                                    </label>
                                                </div>
                                                @error('company_logo')
                                                    <div class="row">
                                                        <span class="invalid-logo" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-lg-4 col-sm-6 col-md-6">
                                    <div class="card mb-0">
                                        <div class="card-header p-3">
                                            <h5>{{ __('Company Logo') }}</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="setting-card setting-logo-box">
                                                <div class="logo-content">
                                                    <img id="image1"
                                                        src="{{ $logo . '/' . (isset($logo_light) && !empty($logo_light) ? $logo_light : 'logo-light.png') . '?' . time() }}">
                                                </div>
                                                <div class="choose-files  mt-3 text-center">
                                                    <label for="company_logo_light">
                                                        <div class=" bg-primary dark_logo_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="form-control file setting_logo"
                                                            name="company_logo_light" id="company_logo_light"
                                                            data-filename="dark_logo_update">
                                                    </label>
                                                </div>
                                                @error('company_logo_light')
                                                    <div class="row">
                                                        <span class="invalid-logo" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-md-6">
                                    <div class="card mb-0">
                                        <div class="card-header p-3">
                                            <h5>{{ __('Icon') }}</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="setting-card setting-logo-box">
                                                <div class="logo-content">
                                                    <img id="image2"
                                                        src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}">
                                                </div>
                                                <div class="choose-files mt-3 text-center">
                                                    <label for="company_favicon">
                                                        <div class="bg-primary company_favicon_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="form-control file setting_logo"
                                                            id="company_favicon" name="company_favicon"
                                                            data-filename="company_favicon_update">
                                                    </label>
                                                </div>
                                                @error('company_favicon')
                                                    <div class="row">
                                                        <span class="invalid-logo" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-gap-1">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-0">
                                        {{ Form::label('title_text', __('Title Text'), ['class' => 'form-label']) }}
                                        {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Title Text')]) }}
                                        @error('title_text')
                                            <span class="invalid-title_text" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-0">
                                        {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-label']) }}
                                        {{ Form::text('footer_text', Utility::getValByName('footer_text'), ['class' => 'form-control', 'placeholder' => __('Enter Footer Text')]) }}
                                        @error('footer_text')
                                            <span class="invalid-footer_text" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-0">
                                        {{ Form::label('default_language', __('Default Language'), ['class' => 'col-form-label']) }}
                                        <div class="changeLanguage">
                                            <select name="default_language" id="default_language"
                                                class="form-control select">
                                                @foreach (\App\Models\Utility::languages() as $code => $language)
                                                    <option @if ($lang == $code) selected @endif
                                                        value="{{ $code }}">
                                                        {{ ucFirst($language) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('default_language')
                                            <span class="invalid-default_language" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body border-top border-1 p-3">
                            <h4 class="small-title h5 mb-3">{{ __('Theme Customizer') }}</h4>
                            <div class="setting-card setting-logo-box">
                                <div class="row row-gap-1">
                                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-header p-2">
                                                <h6 class="mb-0 d-flex align-items-center gap-2">
                                                    <i data-feather="credit-card"></i>{{ __('Primary color settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="color-wrp mt-0">
                                                    <div class="theme-color themes-color">
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                            data-value="theme-1"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-1"{{ $color == 'theme-1' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }}"
                                                            data-value="theme-2"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-2"{{ $color == 'theme-2' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                            data-value="theme-3"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-3"{{ $color == 'theme-3' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                            data-value="theme-4"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-4"{{ $color == 'theme-4' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                            data-value="theme-5"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-5"{{ $color == 'theme-5' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                            data-value="theme-6"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-6"{{ $color == 'theme-6' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                            data-value="theme-7"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-7"{{ $color == 'theme-7' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                            data-value="theme-8"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-8"{{ $color == 'theme-8' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                            data-value="theme-9"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-9"{{ $color == 'theme-9' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                            data-value="theme-10"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-10"{{ $color == 'theme-10' ? 'checked' : '' }}>
                                                    </div>
                                                    <div class="color-picker-wrp ">
                                                        <input type="color" value="{{ $color ? $color : '' }}"
                                                            class="colorPicker {{ isset($settings['color_flag']) && $setting['color_flag'] == 'true' ? 'active_color' : '' }}"
                                                            name="custom_color" id="color-picker">
                                                        <input type='hidden' name="color_flag"
                                                            value={{ isset($settings['color_flag']) && $settings['color_flag'] == 'true' ? 'true' : 'false' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-2 col-md-4 col-sm-6 col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-header p-2">
                                                <h6 class="mb-0 d-flex align-items-center gap-2">
                                                    <i data-feather="layout"></i>{{ __('Sidebar settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-check form-switch d-flex gap-2 flex-column p-0">
                                                    <label class="form-check-label f-w-600 pl-1"
                                                        for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                    <input type="checkbox" class="form-check-input mx-0"
                                                        id="cust-theme-bg" name="cust_theme_bg"
                                                        {{ !empty($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-xxl-2 col-md-4 col-sm-6 col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-header p-2">
                                                <h6 class="mb-0 d-flex align-items-center gap-2">
                                                    <i data-feather="sun"></i>{{ __('Layout settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-check form-switch d-flex gap-2 flex-column p-0">
                                                    <label class="form-check-label f-w-600 pl-1"
                                                        for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                    <input type="checkbox" class="form-check-input mx-0"
                                                        id="cust-darklayout"
                                                        name="cust_darklayout"{{ !empty($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-xxl-2 col-md-4 col-sm-6 col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-header p-2">
                                                <h6 class="mb-0 d-flex align-items-center gap-2">
                                                    <i class="ti ti-align-right"></i>{{ __('Enable RTL') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-check form-switch d-flex gap-2 flex-column p-0">
                                                    <label class="form-check-label f-w-600 pl-1"
                                                        for="SITE_RTL">{{ __('RTL Layout') }}</label>
                                                    <input type="checkbox" class="form-check-input mx-0" id="SITE_RTL"
                                                        name="SITE_RTL"{{ $SITE_RTL == 'on' ? 'checked' : '' }}
                                                        data-onstyle="primary" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-xxl-2 col-md-4 col-sm-6 col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-header p-2">
                                                <h6 class="mb-0 d-flex align-items-center gap-2">
                                                    <i class="ti ti-align-right"></i>{{ __('Landing Page') }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-check form-switch d-flex gap-2 flex-column p-0">
                                                    <label class="form-check-label f-w-600"
                                                        for="display_landing_page">{{ __('Enable Landing Page') }}</label>
                                                    <input type="checkbox" name="display_landing_page"
                                                        class="form-check-input mx-0" id="display_landing_page"
                                                        {{ Utility::getValByName('display_landing_page') == 'on' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--System Settings-->
                    <div id="system-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('System Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your system details') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'system.settings', 'method' => 'post']) }}
                        <div class="card-body p-3">
                            <div class="row">
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('site_currency', __('Currency *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency', null, ['class' => 'form-control font-style']) }}
                                    <small> {{ __('Note: Add currency code as per three-letter ISO code.') }}<br>
                                        <a href="https://stripe.com/docs/currencies"
                                            target="_blank">{{ __('You can find out how to do that here.') }}</a></small>
                                    <br>
                                    @error('site_currency')
                                        <span class="invalid-site_currency" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency_symbol', null, ['class' => 'form-control']) }}
                                    @error('site_currency_symbol')
                                        <span class="invalid-site_currency_symbol" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="form-label"
                                        for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                                    <div class="row ms-1">
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input" type="radio"
                                                name="site_currency_symbol_position" value="pre"
                                                @if (@$settings['site_currency_symbol_position'] == 'pre') checked @endif id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                {{ __('Pre') }}
                                            </label>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input" type="radio"
                                                name="site_currency_symbol_position" value="post"
                                                @if (@$settings['site_currency_symbol_position'] == 'post') checked @endif id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                                {{ __('Post') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('decimal_number', __('Decimal Number Format'), ['class' => 'form-label']) }}
                                    {{ Form::number('decimal_number', null, ['class' => 'form-control']) }}
                                    @error('decimal_number')
                                        <span class="invalid-decimal_number" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}

                                <div class="form-group col-md-6">
                                    <label for="site_date_format" class="form-label">{{ __('Date Format') }}</label>
                                    <select type="text" name="site_date_format" class="form-control selectric"
                                        id="site_date_format">
                                        <option value="M j, Y"
                                            @if (@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015</option>
                                        <option value="d-m-Y"
                                            @if (@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>dd-mm-yyyy</option>
                                        <option value="m-d-Y"
                                            @if (@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>mm-dd-yyyy</option>
                                        <option value="Y-m-d"
                                            @if (@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>yyyy-mm-dd</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="site_time_format" class="form-label">{{ __('Time Format') }}</label>
                                    <select type="text" name="site_time_format" class="form-control selectric"
                                        id="site_time_format">
                                        <option value="g:i A"
                                            @if (@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM</option>
                                        <option value="g:i a"
                                            @if (@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm</option>
                                        <option value="H:i"
                                            @if (@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('customer_prefix', __('Customer Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('customer_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Customer Prefix')]) }}
                                    @error('customer_prefix')
                                        <span class="invalid-customer_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('vender_prefix', __('Vendor Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('vender_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Vendor Prefix')]) }}
                                    @error('vender_prefix')
                                        <span class="invalid-vender_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('proposal_prefix', __('Proposal Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('proposal_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Proposal Prefix')]) }}
                                    @error('proposal_prefix')
                                        <span class="invalid-proposal_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    {{ Form::label('invoice_prefix', __('Invoice Prefix'), ['class' => 'form-label']) }}

                                    {{ Form::text('invoice_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Invoice Prefix')]) }}
                                    @error('invoice_prefix')
                                        <span class="invalid-invoice_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('bill_prefix', __('Bill Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('bill_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Bill Prefix')]) }}
                                    @error('bill_prefix')
                                        <span class="invalid-bill_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('quotation_prefix', __('Quotation Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('quotation_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Quotation Prefix')]) }}
                                    @error('quotation_prefix')
                                        <span class="invalid-quotation_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    {{ Form::label('purchase_prefix', __('Cargo Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('purchase_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Purchase Prefix')]) }}
                                    @error('purchase_prefix')
                                        <span class="invalid-purchase_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('pos_prefix', __('Pos Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('pos_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Pos Prefix')]) }}
                                    @error('pos_prefix')
                                        <span class="invalid-pos_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    {{ Form::label('journal_prefix', __('Journal Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('journal_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Journal Prefix')]) }}
                                    @error('journal_prefix')
                                        <span class="invalid-journal_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('expense_prefix', __('Expense Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('expense_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Expense Prefix')]) }}
                                    @error('expense_prefix')
                                        <span class="invalid-expense_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('employee_prefix', __('Employee Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('employee_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Employee Prifix')]) }}
                                    @error('employee_prefix')
                                        <span class="invalid-employee_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('contract_prefix', __('Contract Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('contract_prefix', null, ['class' => 'form-control', 'placeholder' => __('Enter Contract Prifix')]) }}
                                    @error('contract_prefix')
                                        <span class="invalid-contract_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('shipping_display', __('Display Shipping in Proposal / Invoice / Bill'), ['class' => 'form-label']) }}
                                    <div class=" form-switch form-switch-left">
                                        <input type="checkbox" class="form-check-input" name="shipping_display"
                                            id="email_tempalte_13"
                                            {{ $settings['shipping_display'] == 'on' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_tempalte_13"></label>
                                    </div>
                                    @error('shipping_display')
                                        <span class="invalid-shipping_display" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    {{ Form::label('footer_title', __('Ticket Invoice/Bill/Cargo/Visa Footer Title'), ['class' => 'form-label']) }}
                                    {{ Form::text('footer_title', null, ['class' => 'form-control', 'placeholder' => __('Enter Footer Title')]) }}
                                    @error('footer_title')
                                        <span class="invalid-footer_title" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-12 mb-0">
                                    {{ Form::label('footer_notes', __('Ticket Invoice/Bill/Cargo/Visa Footer Note'), ['class' => 'form-label']) }}
                                    <textarea class="summernote-simple4 summernote-simple">{!! $settings['footer_notes'] !!}</textarea>
                                </div>

                                {{--                                <div class="form-group col-md-6"> --}}
                                {{--                                    {{Form::label('invoice_starting_number',__('Invoice Starting Number'),array('class'=>'form-label')) }} --}}
                                {{--                                    {{Form::text('invoice_starting_number',null,array('class'=>'form-control'))}} --}}
                                {{--                                    @error('invoice_starting_number') --}}
                                {{--                                    <span class="invalid-invoice_starting_number" role="alert"> --}}
                                {{--                                        <strong class="text-danger">{{ $message }}</strong> --}}
                                {{--                                    </span> --}}
                                {{--                                    @enderror --}}
                                {{--                                </div> --}}

                                {{--                                <div class="form-group col-md-6"> --}}
                                {{--                                    {{Form::label('proposal_starting_number',__('Proposal Starting Number'),array('class'=>'form-label')) }} --}}
                                {{--                                    {{Form::text('proposal_starting_number',null,array('class'=>'form-control'))}} --}}
                                {{--                                    @error('proposal_starting_number') --}}
                                {{--                                    <span class="invalid-proposal_starting_number" role="alert"> --}}
                                {{--                                        <strong class="text-danger">{{ $message }}</strong> --}}
                                {{--                                    </span> --}}
                                {{--                                    @enderror --}}
                                {{--                                </div> --}}


                                {{--                                <div class="form-group col-md-6"> --}}
                                {{--                                    {{Form::label('bill_starting_number',__('Bill Starting Number'),array('class'=>'form-label')) }} --}}
                                {{--                                    {{Form::text('bill_starting_number',null,array('class'=>'form-control'))}} --}}
                                {{--                                    @error('bill_starting_number') --}}
                                {{--                                    <span class="invalid-bill_starting_number" role="alert"> --}}
                                {{--                                        <strong class="text-danger">{{ $message }}</strong> --}}
                                {{--                                    </span> --}}
                                {{--                                    @enderror --}}
                                {{--                                </div> --}}



                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice  btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}

                    </div>

                    <!--Company Settings-->
                    <div id="company-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Company Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your company details') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'company.settings', 'method' => 'post']) }}
                        <div class="card-body p-3 pb-0">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_name *', __('Company Name *'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_name', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Company Name')]) }}
                                    @error('company_name')
                                        <span class="invalid-company_name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_address', __('Address'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_address', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Company Address')]) }}
                                    @error('company_address')
                                        <span class="invalid-company_address" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_city', __('City'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_city', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Company City')]) }}
                                    @error('company_city')
                                        <span class="invalid-company_city" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_state', __('State'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_state', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Company State')]) }}
                                    @error('company_state')
                                        <span class="invalid-company_state" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_zipcode', __('Zip/Post Code'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_zipcode', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Zip')]) }}
                                    @error('company_zipcode')
                                        <span class="invalid-company_zipcode" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group  col-md-6">
                                    {{ Form::label('company_country', __('Country'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_country', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Company Country')]) }}
                                    @error('company_country')
                                        <span class="invalid-company_country" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_telephone', __('Telephone'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_telephone', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Telephone')]) }}
                                    @error('company_telephone')
                                        <span class="invalid-company_telephone" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('registration_number', __('Company Registration Number *'), ['class' => 'form-label']) }}
                                    {{ Form::text('registration_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Registration Number')]) }}
                                    @error('registration_number')
                                        <span class="invalid-registration_number" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <div class="form-group col-md-6">
                                    {{ Form::label('company_start_time', __('Company Start Time *'), ['class' => 'form-label']) }}
                                    {{ Form::time('company_start_time', null, ['class' => 'form-control', 'placeholder' => __('Enter Start Time')]) }}
                                    @error('company_start_time')
                                        <span class="invalid-company_start_time" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('company_end_time', __('Company End Time *'), ['class' => 'form-label']) }}
                                    {{ Form::time('company_end_time', null, ['class' => 'form-control', 'placeholder' => __('Enter End Time')]) }}
                                    @error('company_end_time')
                                        <span class="invalid-company_end_time" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-12">
                                    {{ Form::label('timezone', __('Timezone'), ['class' => 'form-label']) }}
                                    <select type="text" name="timezone" class="form-control custom-select"
                                        id="timezone">
                                        <option value="">{{ __('Select Timezone') }}</option>
                                        @foreach ($timezones as $k => $timezone)
                                            <option value="{{ $k }}"
                                                {{ $settings['timezone'] == $k ? 'selected' : '' }}>{{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xxl-3 col-sm-6">
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <label class="form-check-label"
                                                for="ip_restrict">{{ __('Ip Restrict') }}</label>
                                            <div class="form-check form-switch custom-switch-v1 float-end">
                                                <input type="checkbox" class="form-check-input input-primary pointer"
                                                    name="ip_restrict" id="ip_restrict"
                                                    {{ $settings['ip_restrict'] == 'on' ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-xxl-3 col-sm-6">
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <label for="vat_gst_number_switch">{{ __('Tax Number') }}</label>
                                            <div class="form-check form-switch custom-switch-v1 float-end">
                                                <input type="checkbox" name="vat_gst_number_switch"
                                                    class="form-check-input input-primary pointer" value="on"
                                                    id="vat_gst_number_switch"
                                                    {{ $settings['vat_gst_number_switch'] == 'on' ? ' checked ' : '' }}>
                                                <label class="form-check-label" for="vat_gst_number_switch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="col-xxl-6 col-12 tax_type_div mb-3 {{ $settings['vat_gst_number_switch'] != 'on' ? ' d-none ' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="form-check form-check-inline form-group mb-0">
                                                <input type="radio" id="customRadio8" name="tax_type" value="VAT"
                                                    class="form-check-input"
                                                    {{ $settings['tax_type'] == 'VAT' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="customRadio8">{{ __('VAT Number') }}</label>
                                            </div>

                                            <div class="form-check form-check-inline form-group mb-0">
                                                <input type="radio" id="customRadio7" name="tax_type" value="GST"
                                                    class="form-check-input"
                                                    {{ $settings['tax_type'] == 'GST' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="customRadio7">{{ __('GST Number') }}</label>
                                            </div>
                                        </div>
                                        {{ Form::text('vat_number', null, ['class' => 'form-control flex-1', 'placeholder' => __('Enter VAT / GST Number')]) }}
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}

                    </div>


                    <!--Currency Settings-->
                    <div id="currency-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Currency Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your currency details') }}</small>
                        </div>
                        {{ Form::model($setting, ['route' => 'currency.settings', 'method' => 'post', 'id' => 'currency_setting']) }}
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency', __('Currency'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency', $setting['site_currency'], ['class' => 'form-control font-style currency_preview', 'required', 'placeholder' => __('Enter Currency')]) }}
                                    <small> {{ __('Note: Add currency code as per three-letter ISO code.') }}<br>
                                        <a href="https://stripe.com/docs/currencies"
                                            target="_blank">{{ __('You can find out how to do that here.') }}</a></small>
                                    <br>
                                    @error('site_currency')
                                        <span class="invalid-site_currency" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency_symbol', __('Currency Symbol'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency_symbol', null, ['class' => 'form-control currency_preview', 'placeholder' => __('Enter Currency Symbol')]) }}
                                    @error('site_currency_symbol')
                                        <span class="invalid-site_currency_symbol" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('decimal_number', __('Decimal Number Format'), ['class' => 'form-label']) }}
                                    {{ Form::number('decimal_number', null, ['class' => 'form-control currency_preview', 'placeholder' => __('Enter Number')]) }}
                                    @error('decimal_number')
                                        <span class="invalid-decimal_number" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="float_number" class="form-label">{{ __('Float Number') }}</label>
                                    <select type="text" name="float_number"
                                        class="form-control selectric currency_preview" id="float_number">
                                        <option value="comma"
                                            @if (@$setting['float_number'] == 'comma') selected="selected" @endif>
                                            {{ __('Comma') }}</option>
                                        <option value="dot"
                                            @if (@$setting['float_number'] == 'dot') selected="selected" @endif>
                                            {{ __('Dot') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="decimal_separator"
                                        class="form-label">{{ __('Decimal Separator') }}</label>
                                    <select type="text" name="decimal_separator"
                                        class="form-control selectric currency_preview" id="decimal_separator">
                                        <option value="dot"
                                            @if (@$setting['decimal_separator'] == 'dot') selected="selected" @endif>
                                            {{ __('Dot') }}</option>
                                        <option value="comma"
                                            @if (@$setting['decimal_separator'] == 'comma') selected="selected" @endif>
                                            {{ __('Comma') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="thousand_separator"
                                        class="form-label">{{ __('Thousands Separator') }}</label>
                                    <select type="text" name="thousand_separator"
                                        class="form-control selectric currency_preview" id="thousand_separator">
                                        <option value="dot"
                                            @if (@$setting['thousand_separator'] == 'dot') selected="selected" @endif>
                                            {{ __('Dot') }}</option>
                                        <option value="comma"
                                            @if (@$setting['thousand_separator'] == 'comma') selected="selected" @endif>
                                            {{ __('Comma') }}</option>
                                    </select>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            <label class="form-label mb-0"
                                                for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="form-group mb-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="site_currency_symbol_position" value="pre"
                                                        @if (@$settings['site_currency_symbol_position'] == 'pre') checked @endif
                                                        id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{ __('Pre') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="site_currency_symbol_position" value="post"
                                                        @if (@$settings['site_currency_symbol_position'] == 'post') checked @endif
                                                        id="flexCheckChecked">
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        {{ __('Post') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            {{ Form::label('currency_space', __('Currency Symbol Space'), ['class' => 'form-label mb-0']) }}
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="form-group mb-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="currency_space" value="withspace"
                                                        @if (@$settings['currency_space'] == 'withspace') checked @endif id="withspace">
                                                    <label class="form-check-label" for="withspace">
                                                        {{ __('With space') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="currency_space" value="withoutspace"
                                                        @if (@$settings['currency_space'] == 'withoutspace') checked @endif
                                                        id="withoutspace">
                                                    <label class="form-check-label" for="withoutspace">
                                                        {{ __('Without space') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            {{ Form::label('currency_symbol', __('Currency Symbol & Name'), ['class' => 'form-label mb-0']) }}
                                        </div>
                                        <div class="form-group mb-0">
                                            <div class="card-body p-2">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="currency_symbol" value="withcurrencysymbol"
                                                        @if (@$settings['currency_symbol'] == 'withcurrencysymbol') checked @endif
                                                        id="withcurrencysymbol">
                                                    <label class="form-check-label" for="withcurrencysymbol">
                                                        {{ __('With Currency Symbol') }}
                                                    </label>
                                                </div>
                                                <div class="form-check ">
                                                    <input class="form-check-input currency_preview" type="radio"
                                                        name="currency_symbol" value="withcurrencyname"
                                                        @if (@$settings['currency_symbol'] == 'withcurrencyname') checked @endif
                                                        id="withcurrencyname">
                                                    <label class="form-check-label" for="withcurrencyname">
                                                        {{ __('With Currency Name') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto form-group mb-0">
                                    <div class="card mb-0">
                                        <div class="card-body p-3 d-flex align-items-center gap-3">
                                            {{ Form::label('preview', __('Preview :'), ['class' => 'form-label mb-0']) }}
                                            <div class="preview">
                                                {{ __('$ 10.000,00') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--Time-Tracker Settings-->
                    <div id="tracker-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Time Tracker Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your Time Tracker settings') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'tracker.settings', 'method' => 'post']) }}
                        <div class="card-body p-3">
                            <div class="row row-gap-2">
                                <div class="form-group mb-0 col-md-6">
                                    <label class="form-label">{{ __('Application URL') }}</label> <br>
                                    <span
                                        class="text-sm d-block mb-2">{{ __('Application URL to log into the app.') }}</span>
                                    {{ Form::text('apps_url', URL::to('/'), ['class' => 'form-control', 'placeholder' => __('Application URL'), 'readonly' => 'true']) }}
                                </div>
                                <div class="form-group mb-0 col-md-6">
                                    <label class="form-label">{{ __('Tracking Interval') }}</label> <br>
                                    <span
                                        class="text-sm d-block mb-2">{{ __('Image Screenshot Take Interval time ( 1 = 1 min)') }}</span>
                                    {{ Form::number('interval_time', isset($settings['interval_time']) ? $settings['interval_time'] : '10', ['class' => 'form-control', 'placeholder' => __('Enter Tracking Interval Time')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--Payment Settings-->
                    <div class="card" id="payment-settings">
                        <div class="card-header p-3">
                            <h5>{{ 'Payment Settings' }}</h5>
                            <small
                                class="text-secondary font-weight-bold">{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'company.payment.settings', 'method' => 'POST']) }}
                        @csrf
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="faq justify-content-center">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="accordion accordion-flush setting-accordion"
                                                    id="accordionExample">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseBank"
                                                                aria-expanded="false" aria-controls="collapseOne">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Bank Transfer') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="is_bank_transfer_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_bank_transfer_enabled"
                                                                            name="is_bank_transfer_enabled"
                                                                            {{ isset($company_payment_setting['is_bank_transfer_enabled']) && $company_payment_setting['is_bank_transfer_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseBank" class="accordion-collapse collapse"
                                                            aria-labelledby="headingOne"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                {{ Form::label('bank_details', __('Bank Details'), ['class' => 'col-form-label']) }}
                                                                                {{ Form::textarea('bank_details', isset($company_payment_setting['bank_details']) ? $company_payment_setting['bank_details'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Your Bank Details'), 'rows' => 4]) }}
                                                                                <small class="text-xs">
                                                                                    {{ __('Example : Bank : bank name </br> Account Number : 0000 0000 </br>') }}
                                                                                </small>
                                                                                @if ($errors->has('bank_details'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('bank_details') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Stripe
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                                aria-expanded="false" aria-controls="collapseOne">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Stripe') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_stripe_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_stripe_enabled"
                                                                            name="is_stripe_enabled"
                                                                            {{ isset($company_payment_setting['is_stripe_enabled']) && $company_payment_setting['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse"
                                                            aria-labelledby="headingOne"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                {{ Form::label('stripe_key', __('Stripe Key'), ['class' => 'col-form-label']) }}
                                                                                {{ Form::text('stripe_key', isset($company_payment_setting['stripe_key']) ? $company_payment_setting['stripe_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Stripe Key')]) }}
                                                                                @if ($errors->has('stripe_key'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('stripe_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                {{ Form::label('stripe_secret', __('Stripe Secret'), ['class' => 'col-form-label']) }}
                                                                                {{ Form::text('stripe_secret', isset($company_payment_setting['stripe_secret']) ? $company_payment_setting['stripe_secret'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Stripe Secret')]) }}
                                                                                @if ($errors->has('stripe_secret'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('stripe_secret') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Paypal
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwo">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                                aria-expanded="false" aria-controls="collapseTwo">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Paypal') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_paypal_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_paypal_enabled"
                                                                            name="is_paypal_enabled"
                                                                            {{ isset($company_payment_setting['is_paypal_enabled']) && $company_payment_setting['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwo"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label
                                                                                    class="form-check-label text-dark me-2">
                                                                                    <input type="radio"
                                                                                        name="paypal_mode" value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ (isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == '') || (isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label
                                                                                    class="form-check-label text-dark me-2">
                                                                                    <input type="radio"
                                                                                        name="paypal_mode" value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label class="col-form-label"
                                                                                    for="paypal_client_id">{{ __('Client ID') }}</label>
                                                                                <input type="text"
                                                                                    name="paypal_client_id"
                                                                                    id="paypal_client_id"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['paypal_client_id']) || is_null($company_payment_setting['paypal_client_id']) ? '' : $company_payment_setting['paypal_client_id'] }}"
                                                                                    placeholder="{{ __('Client ID') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label class="col-form-label"
                                                                                    for="paypal_secret_key">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paypal_secret_key"
                                                                                    id="paypal_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paypal_secret_key']) ? $company_payment_setting['paypal_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Paystack
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThree">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                                aria-expanded="false" aria-controls="collapseThree">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Paystack') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_paystack_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_paystack_enabled"
                                                                            name="is_paystack_enabled"
                                                                            {{ isset($company_payment_setting['is_paystack_enabled']) && $company_payment_setting['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThree" class="accordion-collapse collapse"
                                                            aria-labelledby="headingThree"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paypal_client_id"
                                                                                    class="col-form-label">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paystack_public_key"
                                                                                    id="paystack_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paystack_public_key']) ? $company_payment_setting['paystack_public_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paystack_secret_key"
                                                                                    class="col-form-label">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paystack_secret_key"
                                                                                    id="paystack_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paystack_secret_key']) ? $company_payment_setting['paystack_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Flutterwave
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFour">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                                aria-expanded="false" aria-controls="collapseFour">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Flutterwave') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="is_flutterwave_enabled" value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_flutterwave_enabled"
                                                                            name="is_flutterwave_enabled"
                                                                            {{ isset($company_payment_setting['is_flutterwave_enabled']) && $company_payment_setting['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFour" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paypal_client_id"
                                                                                    class="col-form-label">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="flutterwave_public_key"
                                                                                    id="flutterwave_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['flutterwave_public_key']) ? $company_payment_setting['flutterwave_public_key'] : '' }}"
                                                                                    placeholder="Public Key">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paystack_secret_key"
                                                                                    class="col-form-label">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="flutterwave_secret_key"
                                                                                    id="flutterwave_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['flutterwave_secret_key']) ? $company_payment_setting['flutterwave_secret_key'] : '' }}"
                                                                                    placeholder="Secret Key">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Razorpay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFive">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                                aria-expanded="false" aria-controls="collapseFive">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Razorpay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_razorpay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_razorpay_enabled"
                                                                            name="is_razorpay_enabled"
                                                                            {{ isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFive" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFive"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paypal_client_id"
                                                                                    class="col-form-label">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="razorpay_public_key"
                                                                                    id="razorpay_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['razorpay_public_key']) || is_null($company_payment_setting['razorpay_public_key']) ? '' : $company_payment_setting['razorpay_public_key'] }}"
                                                                                    placeholder="Public Key">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paystack_secret_key"
                                                                                    class="col-form-label">
                                                                                    {{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="razorpay_secret_key"
                                                                                    id="razorpay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['razorpay_secret_key']) || is_null($company_payment_setting['razorpay_secret_key']) ? '' : $company_payment_setting['razorpay_secret_key'] }}"
                                                                                    placeholder="Secret Key">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Paytm
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingSix">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                                aria-expanded="false" aria-controls="collapseSix">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Paytm') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_paytm_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_paytm_enabled"
                                                                            name="is_paytm_enabled"
                                                                            {{ isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSix" class="accordion-collapse collapse"
                                                            aria-labelledby="headingSix"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="paypal-label col-form-label"
                                                                        for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="paytm_mode"
                                                                                            value="local"
                                                                                            class="form-check-input"
                                                                                            {{ !isset($company_payment_setting['paytm_mode']) || $company_payment_setting['paytm_mode'] == '' || $company_payment_setting['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Local') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="paytm_mode"
                                                                                            value="production"
                                                                                            class="form-check-input"
                                                                                            {{ isset($company_payment_setting['paytm_mode']) && $company_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Production') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-4">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paytm_public_key"
                                                                                    class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_merchant_id"
                                                                                    id="paytm_merchant_id"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paytm_merchant_id']) ? $company_payment_setting['paytm_merchant_id'] : '' }}"
                                                                                    placeholder="{{ __('Merchant ID') }}" />
                                                                                @if ($errors->has('paytm_merchant_id'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytm_merchant_id') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paytm_secret_key"
                                                                                    class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_merchant_key"
                                                                                    id="paytm_merchant_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paytm_merchant_key']) ? $company_payment_setting['paytm_merchant_key'] : '' }}"
                                                                                    placeholder="{{ __('Merchant Key') }}" />
                                                                                @if ($errors->has('paytm_merchant_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytm_merchant_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paytm_industry_type"
                                                                                    class="col-form-label">{{ __('Industry Type') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_industry_type"
                                                                                    id="paytm_industry_type"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['paytm_industry_type']) ? $company_payment_setting['paytm_industry_type'] : '' }}"
                                                                                    placeholder="{{ __('Industry Type') }}" />
                                                                                @if ($errors->has('paytm_industry_type'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytm_industry_type') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Mercado Pago
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingseven">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseseven" aria-expanded="false"
                                                                aria-controls="collapseseven">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Mercado Pago') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_mercado_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_mercado_enabled"
                                                                            name="is_mercado_enabled"
                                                                            {{ isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseseven" class="accordion-collapse collapse"
                                                            aria-labelledby="headingseven"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="coingate-label col-form-label"
                                                                        for="mercado_mode">{{ __('Mercado Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == '') || (isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="mercado_access_token"
                                                                                    class="col-form-label">{{ __('Access Token') }}</label>
                                                                                <input type="text"
                                                                                    name="mercado_access_token"
                                                                                    id="mercado_access_token"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['mercado_access_token']) ? $company_payment_setting['mercado_access_token'] : '' }}"
                                                                                    placeholder="{{ __('Access Token') }}" />
                                                                                @if ($errors->has('mercado_secret_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('mercado_access_token') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Mollie
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingeight">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseeight" aria-expanded="false"
                                                                aria-controls="collapseeight">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Mollie') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_mollie_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_mollie_enabled"
                                                                            name="is_mollie_enabled"
                                                                            {{ isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseeight" class="accordion-collapse collapse"
                                                            aria-labelledby="headingeight"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="mollie_api_key"
                                                                                    class="col-form-label">{{ __('Mollie Api Key') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_api_key"
                                                                                    id="mollie_api_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['mollie_api_key']) || is_null($company_payment_setting['mollie_api_key']) ? '' : $company_payment_setting['mollie_api_key'] }}"
                                                                                    placeholder="Mollie Api Key">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="mollie_profile_id"
                                                                                    class="col-form-label">{{ __('Mollie Profile Id') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_profile_id"
                                                                                    id="mollie_profile_id"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['mollie_profile_id']) || is_null($company_payment_setting['mollie_profile_id']) ? '' : $company_payment_setting['mollie_profile_id'] }}"
                                                                                    placeholder="Mollie Profile Id">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="mollie_partner_id"
                                                                                    class="col-form-label">{{ __('Mollie Partner Id') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_partner_id"
                                                                                    id="mollie_partner_id"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['mollie_partner_id']) || is_null($company_payment_setting['mollie_partner_id']) ? '' : $company_payment_setting['mollie_partner_id'] }}"
                                                                                    placeholder="Mollie Partner Id">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Skrill
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingnine">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapsenine"
                                                                aria-expanded="false" aria-controls="collapsenine">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Skrill') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_skrill_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_skrill_enabled"
                                                                            name="is_skrill_enabled"
                                                                            {{ isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapsenine" class="accordion-collapse collapse"
                                                            aria-labelledby="headingnine"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="mollie_api_key"
                                                                                    class="col-form-label">{{ __('Skrill Email') }}</label>
                                                                                <input type="email"
                                                                                    name="skrill_email"
                                                                                    id="skrill_email"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['skrill_email']) ? $company_payment_setting['skrill_email'] : '' }}"
                                                                                    placeholder="{{ __('Enter Skrill Email') }}" />
                                                                                @if ($errors->has('skrill_email'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('skrill_email') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- CoinGate
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingten">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseten"
                                                                aria-expanded="false" aria-controls="collapseten">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('CoinGate') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_coingate_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_coingate_enabled"
                                                                            name="is_coingate_enabled"
                                                                            {{ isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseten" class="accordion-collapse collapse"
                                                            aria-labelledby="headingten"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="col-form-label"
                                                                        for="coingate_mode">{{ __('CoinGate Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ !isset($company_payment_setting['coingate_mode']) || $company_payment_setting['coingate_mode'] == '' || $company_payment_setting['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark me-2">
                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($company_payment_setting['coingate_mode']) && $company_payment_setting['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="coingate_auth_token"
                                                                                    class="col-form-label">{{ __('CoinGate Auth Token') }}</label>
                                                                                <input type="text"
                                                                                    name="coingate_auth_token"
                                                                                    id="coingate_auth_token"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['coingate_auth_token']) || is_null($company_payment_setting['coingate_auth_token']) ? '' : $company_payment_setting['coingate_auth_token'] }}"
                                                                                    placeholder="CoinGate Auth Token">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- PaymentWall
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingeleven">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseeleven" aria-expanded="false"
                                                                aria-controls="collapseeleven">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('PaymentWall') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="is_paymentwall_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_paymentwall_enabled"
                                                                            name="is_paymentwall_enabled"
                                                                            {{ isset($company_payment_setting['is_paymentwall_enabled']) && $company_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseeleven" class="accordion-collapse collapse"
                                                            aria-labelledby="headingeleven"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paymentwall_public_key"
                                                                                    class="col-form-label">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paymentwall_public_key"
                                                                                    id="paymentwall_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['paymentwall_public_key']) || is_null($company_payment_setting['paymentwall_public_key']) ? '' : $company_payment_setting['paymentwall_public_key'] }}"
                                                                                    placeholder="{{ __('Public Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="paymentwall_secret_key"
                                                                                    class="col-form-label">{{ __('Private Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paymentwall_secret_key"
                                                                                    id="paymentwall_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['paymentwall_secret_key']) || is_null($company_payment_setting['paymentwall_secret_key']) ? '' : $company_payment_setting['paymentwall_secret_key'] }}"
                                                                                    placeholder="{{ __('Private Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Toyyibpay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingtwelve">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapsetwelve" aria-expanded="false"
                                                                aria-controls="collapsetwelve">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Toyyibpay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">Enable:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="is_toyyibpay_enabled" value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_toyyibpay_enabled"
                                                                            name="is_toyyibpay_enabled"
                                                                            {{ isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapsetwelve" class="accordion-collapse collapse"
                                                            aria-labelledby="headingtwelve"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="toyyibpay_category_code"
                                                                                    class="col-form-label">{{ __('Category Key') }}</label>
                                                                                <input type="text"
                                                                                    name="toyyibpay_category_code"
                                                                                    id="toyyibpay_category_code"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['toyyibpay_category_code']) || is_null($company_payment_setting['toyyibpay_category_code']) ? '' : $company_payment_setting['toyyibpay_category_code'] }}"
                                                                                    placeholder="{{ __('Category Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label for="toyyibpay_secret_key"
                                                                                    class="col-form-label">{{ __('Secrect Key') }}</label>
                                                                                <input type="text"
                                                                                    name="toyyibpay_secret_key"
                                                                                    id="toyyibpay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['toyyibpay_secret_key']) || is_null($company_payment_setting['toyyibpay_secret_key']) ? '' : $company_payment_setting['toyyibpay_secret_key'] }}"
                                                                                    placeholder="{{ __('Secrect Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Payfast
                                                    <div class="accordion accordion-flush setting-accordion"
                                                        id="accordionExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseOne13"
                                                                    aria-expanded="false" aria-controls="collapseOne13">
                                                                    <span class="d-flex align-items-center">
                                                                        {{ __('PayFast') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">{{ __('Enable') }}:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_payfast_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_payfast_enabled"
                                                                                id="is_payfast_enabled"
                                                                                {{ isset($company_payment_setting['is_payfast_enabled']) && $company_payment_setting['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne13" class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row">
                                                                        <label class="paypal-label col-form-label"
                                                                            for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                                        <div class="d-flex">
                                                                            <div class="mr-2"
                                                                                style="margin-right: 15px;">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                            <input type="radio"
                                                                                                name="payfast_mode"
                                                                                                value="sandbox"
                                                                                                class="form-check-input"
                                                                                                {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                            {{ __('Sandbox') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mr-2">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark">
                                                                                            <input type="radio"
                                                                                                name="payfast_mode"
                                                                                                value="live"
                                                                                                class="form-check-input"
                                                                                                {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                                            {{ __('Live') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="paytm_public_key"
                                                                                    class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_merchant_id"
                                                                                    id="payfast_merchant_id"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['payfast_merchant_id']) || is_null($company_payment_setting['payfast_merchant_id']) ? '' : $company_payment_setting['payfast_merchant_id'] }}"
                                                                                    placeholder="Merchant ID">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="paytm_secret_key"
                                                                                    class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_merchant_key"
                                                                                    id="payfast_merchant_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['payfast_merchant_key']) || is_null($company_payment_setting['payfast_merchant_key']) ? '' : $company_payment_setting['payfast_merchant_key'] }}"
                                                                                    placeholder="Merchant Key">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="payfast_signature"
                                                                                    class="col-form-label">{{ __('Salt Passphrase') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_signature"
                                                                                    id="payfast_signature"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['payfast_signature']) || is_null($company_payment_setting['payfast_signature']) ? '' : $company_payment_setting['payfast_signature'] }}"
                                                                                    placeholder="Industry Type">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Iyzipay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFourteen">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseFourteen" aria-expanded="false"
                                                                aria-controls="collapseFourteen">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Iyzipay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">Enable:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_iyzipay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            id="customswitchv1-1 is_iyzipay_enabled"
                                                                            name="is_iyzipay_enabled"
                                                                            {{ isset($company_payment_setting['is_iyzipay_enabled']) && $company_payment_setting['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFourteen" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFourteen"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="col-md-12 pb-4">
                                                                    {{--                                                                        <label class="coingate-label col-form-label" --}}
                                                                    {{--                                                                               for="iyzipay_mode">{{ __('Iyzipay Mode') }}</label> --}}
                                                                    {{--                                                                        <br> --}}
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == '') || (isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label class="col-form-label"
                                                                                    for="iyzipay_public_key">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="iyzipay_public_key"
                                                                                    id="iyzipay_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ !isset($company_payment_setting['iyzipay_public_key']) || is_null($company_payment_setting['iyzipay_public_key']) ? '' : $company_payment_setting['iyzipay_public_key'] }}"
                                                                                    placeholder="{{ __('Public Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="input-edits">
                                                                            <div class="form-group">
                                                                                <label class="col-form-label"
                                                                                    for="iyzipay_secret_key">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="iyzipay_secret_key"
                                                                                    id="iyzipay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($company_payment_setting['iyzipay_secret_key']) ? $company_payment_setting['iyzipay_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Merchant Key') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- SSPAY
                                                    <div class="accordion accordion-flush setting-accordion"
                                                        id="accordionExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFourteen">
                                                                <button class="accordion-button collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse15" aria-expanded="false"
                                                                    aria-controls="collapse15">
                                                                    <span class="d-flex align-items-center">
                                                                        {{ __('SSpay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">{{ __('Enable') }}:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_sspay_enabled" value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input input-primary"
                                                                                id="customswitchv1-1 is_sspay_enabled"
                                                                                name="is_sspay_enabled"
                                                                                {{ isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse15" class="accordion-collapse collapse"
                                                                aria-labelledby="headingFourteen"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row gy-4">
                                                                        <div class="col-lg-6">
                                                                            <div class="input-edits">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label"
                                                                                        for="sspay_category_code">{{ __('Category Code') }}</label>
                                                                                    <input type="text"
                                                                                        name="sspay_category_code"
                                                                                        id="sspay_category_code"
                                                                                        class="form-control"
                                                                                        value="{{ !isset($company_payment_setting['sspay_category_code']) || is_null($company_payment_setting['sspay_category_code']) ? '' : $company_payment_setting['sspay_category_code'] }}"
                                                                                        placeholder="{{ __('Category Code') }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <div class="input-edits">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label"
                                                                                        for="sspay_secret_key">{{ __('Secret Key') }}</label>
                                                                                    <input type="text"
                                                                                        name="sspay_secret_key"
                                                                                        id="sspay_secret_key"
                                                                                        class="form-control"
                                                                                        value="{{ isset($company_payment_setting['sspay_secret_key']) ? $company_payment_setting['sspay_secret_key'] : '' }}"
                                                                                        placeholder="{{ __('Secret Key') }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- Paytab
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwenty" aria-expanded="true"
                                                                aria-controls="collapseTwenty">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('PayTab') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                                    <div
                                                                        class="form-check form-switch d-inline-block custom-switch-v1">
                                                                        <input type="hidden" name="is_paytab_enabled"
                                                                            value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_paytab_enabled"
                                                                            id="is_paytab_enabled"
                                                                            {{ isset($company_payment_setting['is_paytab_enabled']) && $company_payment_setting['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_paytab_enabled"></label>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwenty"
                                                            class="accordion-collapse collapse"aria-labelledby="headingTwenty"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="paytab_profile_id"
                                                                                class="col-form-label">{{ __('Profile Id') }}</label>
                                                                            <input type="text"
                                                                                name="paytab_profile_id"
                                                                                id="paytab_profile_id"
                                                                                class="form-control"
                                                                                value="{{ isset($company_payment_setting['paytab_profile_id']) ? $company_payment_setting['paytab_profile_id'] : '' }}"
                                                                                placeholder="{{ __('Profile Id') }}">
                                                                        </div>
                                                                        @if ($errors->has('paytab_profile_id'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytab_profile_id') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="paytab_server_key"
                                                                                class="col-form-label">{{ __('Server Key') }}</label>
                                                                            <input type="text"
                                                                                name="paytab_server_key"
                                                                                id="paytab_server_key"
                                                                                class="form-control"
                                                                                value="{{ isset($company_payment_setting['paytab_server_key']) ? $company_payment_setting['paytab_server_key'] : '' }}"
                                                                                placeholder="{{ __('Server Key') }}">
                                                                        </div>
                                                                        @if ($errors->has('paytab_server_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytab_server_key') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="paytab_region"
                                                                                class="form-label">{{ __('Region') }}</label>
                                                                            <input type="text" name="paytab_region"
                                                                                id="paytab_region"
                                                                                class="form-control form-label"
                                                                                value="{{ isset($company_payment_setting['paytab_region']) ? $company_payment_setting['paytab_region'] : '' }}"
                                                                                placeholder="{{ __('Region') }}" /><br>
                                                                            @if ($errors->has('paytab_region'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('paytab_region') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!--Benefit
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwentyOne">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentyOne"
                                                                aria-expanded="false" aria-controls="collapseTwentyOne">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Benefit') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_benefit_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_benefit_enabled"
                                                                            id="is_benefit_enabled"
                                                                            {{ isset($company_payment_setting['is_benefit_enabled']) && $company_payment_setting['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="is_benefit_enabled"></label>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentyOne" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwentyOne"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">

                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('benefit_api_key', __('Benefit Key'), ['class' => 'col-form-label']) }}
                                                                            {{ Form::text('benefit_api_key', isset($company_payment_setting['benefit_api_key']) ? $company_payment_setting['benefit_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Benefit Key')]) }}
                                                                            @error('benefit_api_key')
                                                                                <span class="invalid-benefit_api_key"
                                                                                    role="alert">
                                                                                    <strong
                                                                                        class="text-danger">{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('benefit_secret_key', __('Benefit Secret Key'), ['class' => 'col-form-label']) }}
                                                                            {{ Form::text('benefit_secret_key', isset($company_payment_setting['benefit_secret_key']) ? $company_payment_setting['benefit_secret_key'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Benefit Secret key')]) }}
                                                                            @error('benefit_secret_key')
                                                                                <span class="invalid-benefit_secret_key"
                                                                                    role="alert">
                                                                                    <strong
                                                                                        class="text-danger">{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Cashfree
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwentyTwo">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentyTwo"
                                                                aria-expanded="false" aria-controls="collapseTwentyTwo">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Cashfree') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_cashfree_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_cashfree_enabled"
                                                                            id="is_cashfree_enabled"
                                                                            {{ isset($company_payment_setting['is_cashfree_enabled']) && $company_payment_setting['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="is_cashfree_enabled"></label>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentyTwo" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwentyTwo"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row gy-4">
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('cashfree_api_key', __('Cashfree Key'), ['class' => 'col-form-label']) }}
                                                                            {{ Form::text('cashfree_api_key', isset($company_payment_setting['cashfree_api_key']) ? $company_payment_setting['cashfree_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Cashfree Key')]) }}
                                                                            @error('cashfree_api_key')
                                                                                <span class="invalid-cashfree_api_key"
                                                                                    role="alert">
                                                                                    <strong
                                                                                        class="text-danger">{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('cashfree_secret_key', __('Cashfree Secret Key'), ['class' => 'col-form-label']) }}
                                                                            {{ Form::text('cashfree_secret_key', isset($company_payment_setting['cashfree_secret_key']) ? $company_payment_setting['cashfree_secret_key'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Cashfree Secret key')]) }}
                                                                            @error('cashfree_secret_key')
                                                                                <span class="invalid-cashfree_secret_key"
                                                                                    role="alert">
                                                                                    <strong
                                                                                        class="text-danger">{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Aamarpay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-One">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwenty-One"
                                                                aria-expanded="true" aria-controls="collapseTwenty-One">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Aamarpay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ __('Enable') }}:</span>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_aamarpay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_aamarpay_enabled"
                                                                            id="is_aamarpay_enabled"
                                                                            {{ isset($company_payment_setting['is_aamarpay_enabled']) && $company_payment_setting['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="is_aamarpay_enabled"></label>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwenty-One" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-One"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-12">
                                                                        <label for="aamarpay_mode"
                                                                            class="col-form-label">{{ __('Aamarpay Mode') }}</label>
                                                                        <div class="d-flex">
                                                                            <div class="me-2">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['aamarpay_mode']) && $company_payment_setting['aamarpay_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                            <input type="radio"
                                                                                                name="aamarpay_mode"
                                                                                                value="sandbox"
                                                                                                class="form-check-input"
                                                                                                {{ (isset($company_payment_setting['aamarpay_mode']) && $company_payment_setting['aamarpay_mode'] == '') || (isset($company_payment_setting['aamarpay_mode']) && $company_payment_setting['aamarpay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="me-2">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['aamarpay_mode']) && $company_payment_setting['aamarpay_mode'] == 'live' ? 'active' : '' }}">
                                                                                            <input type="radio"
                                                                                                name="aamarpay_mode"
                                                                                                value="live"
                                                                                                class="form-check-input"
                                                                                                {{ isset($company_payment_setting['aamarpay_mode']) && $company_payment_setting['aamarpay_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('aamarpay_store_id', __('Store Id'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('aamarpay_store_id', isset($company_payment_setting['aamarpay_store_id']) ? $company_payment_setting['aamarpay_store_id'] : '', ['class' => 'form-control', 'placeholder' => __('Store Id')]) }}<br>
                                                                            @if ($errors->has('aamarpay_store_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('aamarpay_store_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('aamarpay_signature_key', __('Signature Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('aamarpay_signature_key', isset($company_payment_setting['aamarpay_signature_key']) ? $company_payment_setting['aamarpay_signature_key'] : '', ['class' => 'form-control', 'placeholder' => __('Signature Key')]) }}<br>
                                                                            @if ($errors->has('aamarpay_signature_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('aamarpay_signature_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('aamarpay_description', __('Description'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('aamarpay_description', isset($company_payment_setting['aamarpay_description']) ? $company_payment_setting['aamarpay_description'] : '', ['class' => 'form-control', 'placeholder' => __('Description')]) }}<br>
                                                                            @if ($errors->has('aamarpay_description'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('aamarpay_description') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--PayTR
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-Two">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentyfive"
                                                                aria-expanded="true" aria-controls="collapseTwentyfive">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('PayTR') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_paytr_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_paytr_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_paytr_enabled"
                                                                            id="is_paytr_enabled"
                                                                            {{ isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentyfive" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-Two"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            {{ Form::label('paytr_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('paytr_merchant_id', isset($company_payment_setting['paytr_merchant_id']) ? $company_payment_setting['paytr_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                            @if ($errors->has('paytr_merchant_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('paytr_merchant_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            {{ Form::label('paytr_merchant_key', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('paytr_merchant_key', isset($company_payment_setting['paytr_merchant_key']) ? $company_payment_setting['paytr_merchant_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                            @if ($errors->has('paytr_merchant_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('paytr_merchant_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            {{ Form::label('paytr_merchant_salt', __('Merchant Salt'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('paytr_merchant_salt', isset($company_payment_setting['paytr_merchant_salt']) ? $company_payment_setting['paytr_merchant_salt'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Salt')]) }}<br>
                                                                            @if ($errors->has('paytr_merchant_salt'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('paytr_merchant_salt') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Yookassa
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-Three">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentysix" aria-expanded="true"
                                                                aria-controls="collapseTwentysix">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Yookassa') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_yookassa_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_yookassa_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_yookassa_enabled"
                                                                            id="is_yookassa_enabled"
                                                                            {{ isset($company_payment_setting['is_yookassa_enabled']) && $company_payment_setting['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentysix" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-Three"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('yookassa_shop_id', __('Shop ID Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('yookassa_shop_id', isset($company_payment_setting['yookassa_shop_id']) ? $company_payment_setting['yookassa_shop_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                            @if ($errors->has('yookassa_shop_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('yookassa_shop_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('yookassa_secret', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('yookassa_secret', isset($company_payment_setting['yookassa_secret']) ? $company_payment_setting['yookassa_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                            @if ($errors->has('yookassa_secret'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('yookassa_secret') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Midtrans
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-four">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentyseven"
                                                                aria-expanded="true"
                                                                aria-controls="collapseTwentyseven">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Midtrans') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_midtrans_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_midtrans_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_midtrans_enabled"
                                                                            id="is_midtrans_enabled"
                                                                            {{ isset($company_payment_setting['is_midtrans_enabled']) && $company_payment_setting['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentyseven"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-four"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="midtrans-label col-form-label"
                                                                        for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == '') || (isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-label text-dark">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('midtrans_secret', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('midtrans_secret', isset($company_payment_setting['midtrans_secret']) ? $company_payment_setting['midtrans_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                            @if ($errors->has('midtrans_secret'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('midtrans_secret') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Xendit
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-five">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentyeight"
                                                                aria-expanded="true"
                                                                aria-controls="collapseTwentyeight">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Xendit') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_xendit_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_xendit_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_xendit_enabled"
                                                                            id="is_xendit_enabled"
                                                                            {{ isset($company_payment_setting['is_xendit_enabled']) && $company_payment_setting['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentyeight"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-five"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('xendit_api', __('API Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('xendit_api', isset($company_payment_setting['xendit_api']) ? $company_payment_setting['xendit_api'] : '', ['class' => 'form-control', 'placeholder' => __('API Key')]) }}<br>
                                                                            @if ($errors->has('xendit_api'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('xendit_api') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('xendit_token', __('Token'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('xendit_token', isset($company_payment_setting['xendit_token']) ? $company_payment_setting['xendit_token'] : '', ['class' => 'form-control', 'placeholder' => __('Token')]) }}<br>
                                                                            @if ($errors->has('xendit_token'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('xendit_token') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Nepalste
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-six">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTwentynine"
                                                                aria-expanded="true" aria-controls="collapseTwentynine">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Nepalste') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_nepalste_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_nepalste_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_nepalste_enabled"
                                                                            id="is_nepalste_enabled"
                                                                            {{ isset($company_payment_setting['is_nepalste_enabled']) && $company_payment_setting['is_nepalste_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwentynine" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-six"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="nepalste_mode"
                                                                                        value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ (isset($company_payment_setting['nepalste_mode']) && $company_payment_setting['nepalste_mode'] == '') || (isset($company_payment_setting['nepalste_mode']) && $company_payment_setting['nepalste_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="nepalste_mode"
                                                                                        value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($company_payment_setting['nepalste_mode']) && $company_payment_setting['nepalste_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('nepalste_public_key', __('Public Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('nepalste_public_key', isset($company_payment_setting['nepalste_public_key']) ? $company_payment_setting['nepalste_public_key'] : '', ['class' => 'form-control', 'placeholder' => __('API Key')]) }}<br>
                                                                            @if ($errors->has('nepalste_public_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('nepalste_public_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('nepalste_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('nepalste_secret_key', isset($company_payment_setting['nepalste_secret_key']) ? $company_payment_setting['nepalste_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Token')]) }}<br>
                                                                            @if ($errors->has('nepalste_secret_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('nepalste_secret_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Paiement Pro
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-seven">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty" aria-expanded="true"
                                                                aria-controls="collapseThirty">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Paiement Pro') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_paiementpro_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="is_paiementpro_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_paiementpro_enabled"
                                                                            id="is_paiementpro_enabled"
                                                                            {{ isset($company_payment_setting['is_paiementpro_enabled']) && $company_payment_setting['is_paiementpro_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-six"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('paiementpro_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('paiementpro_merchant_id', isset($company_payment_setting['paiementpro_merchant_id']) ? $company_payment_setting['paiementpro_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                            @if ($errors->has('paiementpro_merchant_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('paiementpro_merchant_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Cinetpay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-eight">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-one"
                                                                aria-expanded="true" aria-controls="collapseThirty-one">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Cinetpay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_cinetpay_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_cinetpay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_cinetpay_enabled"
                                                                            id="is_cinetpay_enabled"
                                                                            {{ isset($company_payment_setting['is_cinetpay_enabled']) && $company_payment_setting['is_cinetpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-one" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-six"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('cinetpay_api_key', __('CinetPay API Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('cinetpay_api_key', isset($company_payment_setting['cinetpay_api_key']) ? $company_payment_setting['cinetpay_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay API Key')]) }}<br>
                                                                            @if ($errors->has('cinetpay_api_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('cinetpay_api_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('cinetpay_site_id', __('CinetPay Site ID'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('cinetpay_site_id', isset($company_payment_setting['cinetpay_site_id']) ? $company_payment_setting['cinetpay_site_id'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay Site ID')]) }}<br>
                                                                            @if ($errors->has('cinetpay_site_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('cinetpay_site_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->

                                                    <!--Fedapay
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwenty-nine">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-two"
                                                                aria-expanded="true" aria-controls="collapseThirty-two">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Fedapay') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_fedapay_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_fedapay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_fedapay_enabled"
                                                                            id="is_fedapay_enabled"
                                                                            {{ isset($company_payment_setting['is_fedapay_enabled']) && $company_payment_setting['is_fedapay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-two" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-six"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="fedapay_mode"
                                                                                        value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ (isset($company_payment_setting['fedapay_mode']) && $company_payment_setting['fedapay_mode'] == '') || (isset($company_payment_setting['fedapay_mode']) && $company_payment_setting['fedapay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="fedapay_mode"
                                                                                        value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($company_payment_setting['fedapay_mode']) && $company_payment_setting['fedapay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('fedapay_public', __('Public Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('fedapay_public', isset($company_payment_setting['fedapay_public']) ? $company_payment_setting['fedapay_public'] : '', ['class' => 'form-control', 'placeholder' => __('Public Key')]) }}<br>
                                                                            @if ($errors->has('fedapay_public'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('fedapay_public') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('fedapay_secret', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('fedapay_secret', isset($company_payment_setting['fedapay_secret']) ? $company_payment_setting['fedapay_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}<br>
                                                                            @if ($errors->has('fedapay_secret'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('fedapay_secret') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- PayHere --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThirty">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-three"
                                                                aria-expanded="true"
                                                                aria-controls="collapseThirty-three">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('PayHere') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="is_payhere_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="is_payhere_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_payhere_enabled"
                                                                            id="is_payhere_enabled"
                                                                            {{ isset($company_payment_setting['is_payhere_enabled']) && $company_payment_setting['is_payhere_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-three"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-six"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <label class="payhere-label col-form-label"
                                                                    for="payhere_mode">{{ __('PayHere Environment') }}</label>
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="payhere_mode"
                                                                                        value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ (isset($company_payment_setting['payhere_mode']) && $company_payment_setting['payhere_mode'] == '') || (isset($company_payment_setting['payhere_mode']) && $company_payment_setting['payhere_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="payhere_mode"
                                                                                        value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($company_payment_setting['payhere_mode']) && $company_payment_setting['payhere_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('payhere_merchant_id', __('Merchant ID'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('payhere_merchant_id', isset($company_payment_setting['payhere_merchant_id']) ? $company_payment_setting['payhere_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant ID')]) }}<br>
                                                                            @if ($errors->has('payhere_merchant_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('payhere_merchant_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('payhere_merchant_secret', __('Merchant Secret'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('payhere_merchant_secret', isset($company_payment_setting['payhere_merchant_secret']) ? $company_payment_setting['payhere_merchant_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Secret')]) }}<br>
                                                                            @if ($errors->has('payhere_merchant_secret'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('payhere_merchant_secret') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('payhere_app_id', __('App ID'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('payhere_app_id', isset($company_payment_setting['payhere_app_id']) ? $company_payment_setting['payhere_app_id'] : '', ['class' => 'form-control', 'placeholder' => __('App ID')]) }}<br>
                                                                            @if ($errors->has('payhere_app_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('payhere_app_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('payhere_app_secret', __('App Secret'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('payhere_app_secret', isset($company_payment_setting['payhere_app_secret']) ? $company_payment_setting['payhere_app_secret'] : '', ['class' => 'form-control', 'placeholder' => __('App Secret')]) }}<br>
                                                                            @if ($errors->has('payhere_app_secret'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('payhere_app_secret') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Tap --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThirty-one">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-four"
                                                                aria-expanded="true"
                                                                aria-controls="collapseThirty-four">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Tap') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="tap_payment_is_on">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden" name="tap_payment_is_on"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="tap_payment_is_on"
                                                                            id="tap_payment_is_on"
                                                                            {{ isset($company_payment_setting['tap_payment_is_on']) && $company_payment_setting['tap_payment_is_on'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-four"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-seaven"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('company_tap_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('company_tap_secret_key', isset($company_payment_setting['company_tap_secret_key']) ? $company_payment_setting['company_tap_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}<br>
                                                                            @if ($errors->has('company_tap_secret_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('company_tap_secret_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- AuthorizeNet --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThirty-two">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-five"
                                                                aria-expanded="true"
                                                                aria-controls="collapseThirty-five">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('AuthorizeNet') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="authorizenet_payment_is_on">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="authorizenet_payment_is_on"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="authorizenet_payment_is_on"
                                                                            id="authorizenet_payment_is_on"
                                                                            {{ isset($company_payment_setting['authorizenet_payment_is_on']) && $company_payment_setting['authorizenet_payment_is_on'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-five"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-eight"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <label class="authorizenet-label col-form-label"
                                                                    for="company_authorizenet_mode">{{ __('Authorizenet Mode') }}</label>
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="company_authorizenet_mode"
                                                                                        value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ (isset($company_payment_setting['company_authorizenet_mode']) && $company_payment_setting['company_authorizenet_mode'] == '') || (isset($company_payment_setting['company_authorizenet_mode']) && $company_payment_setting['company_authorizenet_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="company_authorizenet_mode"
                                                                                        value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($company_payment_setting['company_authorizenet_mode']) && $company_payment_setting['company_authorizenet_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Production') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('company_authorizenet_client_id', __('Merchant Login Id'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('company_authorizenet_client_id', isset($company_payment_setting['company_authorizenet_client_id']) ? $company_payment_setting['company_authorizenet_client_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Login Id')]) }}<br>
                                                                            @if ($errors->has('company_authorizenet_client_id'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('company_authorizenet_client_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('company_authorizenet_secret_key', __('Merchant Transaction Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('company_authorizenet_secret_key', isset($company_payment_setting['company_authorizenet_secret_key']) ? $company_payment_setting['company_authorizenet_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Transaction Key')]) }}<br>
                                                                            @if ($errors->has('company_authorizenet_secret_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('company_authorizenet_secret_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Khalti --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThirty-three">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-six"
                                                                aria-expanded="true" aria-controls="collapseThirty-six">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Khalti') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="khalti_payment_is_on">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="khalti_payment_is_on" value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="khalti_payment_is_on"
                                                                            id="khalti_payment_is_on"
                                                                            {{ isset($company_payment_setting['khalti_payment_is_on']) && $company_payment_setting['khalti_payment_is_on'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-six" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-eight"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('khalti_public_key', __('Public Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('khalti_public_key', isset($company_payment_setting['khalti_public_key']) ? $company_payment_setting['khalti_public_key'] : '', ['class' => 'form-control', 'placeholder' => __('Public Key')]) }}<br>
                                                                            @if ($errors->has('khalti_public_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('khalti_public_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('khalti_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('khalti_secret_key', isset($company_payment_setting['khalti_secret_key']) ? $company_payment_setting['khalti_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}<br>
                                                                            @if ($errors->has('khalti_secret_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('khalti_secret_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Easebuzz --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThirty-four">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseThirty-seven"
                                                                aria-expanded="true"
                                                                aria-controls="collapseThirty-seven">
                                                                <span class="d-flex align-items-center">
                                                                    {{ __('Easebuzz') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="easebuzz_payment_is_on">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="easebuzz_payment_is_on"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="easebuzz_payment_is_on"
                                                                            id="easebuzz_payment_is_on"
                                                                            {{ isset($company_payment_setting['easebuzz_payment_is_on']) && $company_payment_setting['easebuzz_payment_is_on'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThirty-seven"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingTwenty-nine"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row pt-2">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('easebuzz_merchant_key', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('easebuzz_merchant_key', isset($company_payment_setting['easebuzz_merchant_key']) ? $company_payment_setting['easebuzz_merchant_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                            @if ($errors->has('easebuzz_merchant_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('easebuzz_merchant_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('easebuzz_salt_key', __('Salt Key'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('easebuzz_salt_key', isset($company_payment_setting['easebuzz_salt_key']) ? $company_payment_setting['easebuzz_salt_key'] : '', ['class' => 'form-control', 'placeholder' => __('Salt Key')]) }}<br>
                                                                            @if ($errors->has('easebuzz_salt_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('easebuzz_salt_key') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="easebuzz_enviroment_name"
                                                                                class="col-form-label">{{ __('Easebuzz Enviroment Name') }}</label>
                                                                            <input class="form-control"
                                                                                placeholder="Enter Easebuzz Salt Key"
                                                                                name="easebuzz_enviroment_name"
                                                                                type="text"
                                                                                value="{{ $company_payment_setting['easebuzz_enviroment_name'] ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Ozow --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="ozow-payment">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#ozow-payment-settings"
                                                                aria-expanded="true"
                                                                aria-controls="ozow-payment-settings">
                                                                <span class="d-flex align-items-center"
                                                                    for="ozow-payment">
                                                                    {{ __('Ozow') }}
                                                                </span>
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check-label m-1"
                                                                        for="company_ozow_payment_is_enabled">{{ __('Enable') }}</label>
                                                                    <div class="form-check form-switch custom-switch-v1">
                                                                        <input type="hidden"
                                                                            name="company_ozow_payment_is_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="company_ozow_payment_is_enabled"
                                                                            id="company_ozow_payment_is_enabled"
                                                                            {{ isset($company_payment_setting['company_ozow_payment_is_enabled']) && $company_payment_setting['company_ozow_payment_is_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="ozow-payment-settings"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="headingThirty"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="company_ozow_payment_mode"
                                                                            class="col-form-label">{{ __('Ozow Mode') }}</label>
                                                                        <div class="d-flex">
                                                                            <div class="me-2">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                            <input type="radio"
                                                                                                name="company_ozow_payment_mode"
                                                                                                value="sandbox"
                                                                                                class="form-check-input"
                                                                                                {{ (isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == '') || (isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="me-2">
                                                                                <div class="border card p-3">
                                                                                    <div class="form-check">
                                                                                        <label
                                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == 'live' ? 'active' : '' }}">
                                                                                            <input type="radio"
                                                                                                name="company_ozow_payment_mode"
                                                                                                value="live"
                                                                                                class="form-check-input"
                                                                                                {{ isset($company_payment_setting['company_ozow_payment_mode']) && $company_payment_setting['company_ozow_payment_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="company_ozow_site_key"
                                                                                class="form-label">{{ __('Ozow Site Key') }}</label>
                                                                            <input type="text"
                                                                                name="company_ozow_site_key"
                                                                                id="company_ozow_site_key"
                                                                                class="form-control"
                                                                                value="{{ !isset($company_payment_setting['company_ozow_site_key']) || is_null($company_payment_setting['company_ozow_site_key']) ? '' : $company_payment_setting['company_ozow_site_key'] }}"
                                                                                placeholder="{{ __('Ozow Site Key') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="company_ozow_private_key"
                                                                                class="form-label">{{ __('Ozow Private Key') }}</label>
                                                                            <input type="text"
                                                                                name="company_ozow_private_key"
                                                                                id="company_ozow_private_key"
                                                                                class="form-control"
                                                                                value="{{ !isset($company_payment_setting['company_ozow_private_key']) || is_null($company_payment_setting['company_ozow_private_key']) ? '' : $company_payment_setting['company_ozow_private_key'] }}"
                                                                                placeholder="{{ __('Ozow Private Key') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="company_ozow_api_key"
                                                                                class="form-label">{{ __('Ozow Api Key') }}</label>
                                                                            <input type="text"
                                                                                name="company_ozow_api_key"
                                                                                id="company_ozow_api_key"
                                                                                class="form-control"
                                                                                value="{{ !isset($company_payment_setting['company_ozow_api_key']) || is_null($company_payment_setting['company_ozow_api_key']) ? '' : $company_payment_setting['company_ozow_api_key'] }}"
                                                                                placeholder="{{ __('Ozow Api Key') }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ----->


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice  btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        </form>
                    </div>

                    <!--Email Settings-->
                    <div id="email-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Email Settings') }}</h5>
                        </div>
                        <div class="card-body p-3">
                            {{ Form::open(['route' => 'email.settings', 'method' => 'post']) }}
                            @csrf
                            <div class="row row-gap-2">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_driver', isset($settings['mail_driver']) ? $settings['mail_driver'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver')]) }}
                                        @error('mail_driver')
                                            <span class="invalid-mail_driver" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_host', isset($settings['mail_host']) ? $settings['mail_host'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                        @error('mail_host')
                                            <span class="invalid-mail_driver" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_port', isset($settings['mail_port']) ? $settings['mail_port'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                        @error('mail_port')
                                            <span class="invalid-mail_port" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_username', isset($settings['mail_username']) ? $settings['mail_username'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                        @error('mail_username')
                                            <span class="invalid-mail_username" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_password', isset($settings['mail_password']) ? $settings['mail_password'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                        @error('mail_password')
                                            <span class="invalid-mail_password" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_encryption', isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                        @error('mail_encryption')
                                            <span class="invalid-mail_encryption" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_from_address', isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                        @error('mail_from_address')
                                            <span class="invalid-mail_from_address" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_from_name', isset($settings['mail_from_name']) ? $settings['mail_from_name'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                        @error('mail_from_name')
                                            <span class="invalid-mail_from_name" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                            <div class="card-footer p-3 d-flex justify-content-end">
                                    <div
                                        class="form-group d-flex flex-wrap gap-2 align-items-center justify-content-end mb-0">
                                        <a href="#" data-url="{{ route('test.mail') }}"
                                            data-title="{{ __('Send Test Mail') }}"
                                            class="btn btn-primary send_email">
                                            {{ __('Send Test Mail') }}
                                        </a>
                                        <input class="btn btn-primary" type="submit"
                                            value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                        {{ Form::close() }}
                    </div>

                    <!--Pusher Settings-->
                    <div id="pusher-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Pusher Settings') }}</h5>
                        </div>
                        {{ Form::model($settings, ['route' => 'pusher.setting', 'method' => 'post']) }}
                        @csrf
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('pusher_app_id', __('Pusher App Id'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_id', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Pusher App Id')]) }}
                                        @error('pusher_app_id')
                                            <span class="invalid-pusher_app_id" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('pusher_app_key', __('Pusher App Key'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_key', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Pusher App Key')]) }}
                                        @error('pusher_app_key')
                                            <span class="invalid-pusher_app_key" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('pusher_app_secret', __('Pusher App Secret'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_secret', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Pusher App Secret')]) }}
                                        @error('pusher_app_secret')
                                            <span class="invalid-pusher_app_secret" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('pusher_app_cluster', __('Pusher App Cluster'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_cluster', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Pusher App Cluster')]) }}
                                        @error('pusher_app_cluster')
                                            <span class="invalid-pusher_app_cluster" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice  btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--Zoom - Metting Settings-->
                    <div id="zoom-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Zoom Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your Zoom settings') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'zoom.settings', 'method' => 'post']) }}
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Zoom Account ID') }}</label> <br>
                                    {{ Form::text('zoom_account_id', isset($settings['zoom_account_id']) ? $settings['zoom_account_id'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom Accound Id')]) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Zoom Client ID') }}</label> <br>
                                    {{ Form::text('zoom_client_id', isset($settings['zoom_client_id']) ? $settings['zoom_client_id'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom Client Id')]) }}
                                </div>
                                <div class="form-group col-md-6 mb-0">
                                    <label class="form-label">{{ __('Zoom Client Secret Key') }}</label> <br>
                                    {{ Form::text('zoom_client_secret', isset($settings['zoom_client_secret']) ? $settings['zoom_client_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom Client Secret Key')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--Slack Settings-->
                    {{-- <div id="slack-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Slack Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your Slack settings') }}</small>
                        </div>
                        {{ Form::open(['route' => 'slack.settings', 'id' => 'slack-setting', 'method' => 'post', 'class' => 'd-contents']) }}
                        <div class="card-body p-3">
                            <div class="row row-gap-1">
                                <div class="form-group mb-1 col-md-12">
                                    <label class="form-label">{{ __('Slack Webhook URL') }}</label> <br>
                                    {{ Form::text('slack_webhook', isset($comSetting['slack_webhook']) ? $comSetting['slack_webhook'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required']) }}
                                </div>
                                <div class="col-md-12">
                                    <h5 class="small-title">{{ __('Module Settings') }}</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Lead') }}</span>
                                                {{ Form::checkbox('lead_notification', '1', isset($comSetting['lead_notification']) && $comSetting['lead_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'lead_notification']) }}
                                                <label class="form-check-label" for="lead_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Deal') }}</span>
                                                {{ Form::checkbox('deal_notification', '1', isset($comSetting['deal_notification']) && $comSetting['deal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'deal_notification']) }}
                                                <label class="form-check-label" for="deal_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('Lead to Deal Conversion') }}</span>
                                                {{ Form::checkbox('leadtodeal_notification', '1', isset($comSetting['leadtodeal_notification']) && $comSetting['leadtodeal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'leadtodeal_notification']) }}
                                                <label class="form-check-label" for="leadtodeal_notification"></label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Contract') }}</span>
                                                {{ Form::checkbox('contract_notification', '1', isset($comSetting['contract_notification']) && $comSetting['contract_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'contract_notification']) }}
                                                <label class="form-check-label" for="contract_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Project') }}</span>
                                                {{ Form::checkbox('project_notification', '1', isset($comSetting['project_notification']) && $comSetting['project_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'project_notification']) }}
                                                <label class="form-check-label" for="project_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Task') }}</span>
                                                {{ Form::checkbox('task_notification', '1', isset($comSetting['task_notification']) && $comSetting['task_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'task_notification']) }}
                                                <label class="form-check-label" for="task_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('Task Stage Updated') }}</span>
                                                {{ Form::checkbox('taskmove_notification', '1', isset($comSetting['taskmove_notification']) && $comSetting['taskmove_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskmove_notification']) }}
                                                <label class="form-check-label" for="taskmove_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Task Comment') }}</span>
                                                {{ Form::checkbox('taskcomment_notification', '1', isset($comSetting['taskcomment_notification']) && $comSetting['taskcomment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskcomment_notification']) }}
                                                <label class="form-check-label" for="taskcomment_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Monthly Payslip') }}</span>
                                                {{ Form::checkbox('payslip_notification', '1', isset($comSetting['payslip_notification']) && $comSetting['payslip_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'payslip_notification']) }}
                                                <label class="form-check-label" for="payslip_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Award') }}</span>
                                                {{ Form::checkbox('award_notification', '1', isset($comSetting['award_notification']) && $comSetting['award_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'award_notification']) }}
                                                <label class="form-check-label" for="award_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Announcement') }}</span>
                                                {{ Form::checkbox('announcement_notification', '1', isset($comSetting['announcement_notification']) && $comSetting['announcement_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'announcement_notification']) }}
                                                <label class="form-check-label" for="announcement_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class=" form-switch form-switch-right">
                                                <span>{{ __('New Holiday') }}</span>
                                                {{ Form::checkbox('holiday_notification', '1', isset($comSetting['holiday_notification']) && $comSetting['holiday_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'holiday_notification']) }}
                                                <label class="form-check-label" for="holiday_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Support Ticket') }}</span>
                                                {{ Form::checkbox('support_notification', '1', isset($comSetting['support_notification']) && $comSetting['support_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'support_notification']) }}
                                                <label class="form-check-label" for="support_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Event') }}</span>
                                                {{ Form::checkbox('event_notification', '1', isset($comSetting['event_notification']) && $comSetting['event_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'event_notification']) }}
                                                <label class="form-check-label" for="event_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Meeting') }}</span>
                                                {{ Form::checkbox('meeting_notification', '1', isset($comSetting['meeting_notification']) && $comSetting['meeting_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'meeting_notification']) }}
                                                <label class="form-check-label" for="meeting_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Company Policy') }}</span>
                                                {{ Form::checkbox('policy_notification', '1', isset($comSetting['policy_notification']) && $comSetting['policy_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'policy_notification']) }}
                                                <label class="form-check-label" for="policy_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Invoice') }}</span>
                                                {{ Form::checkbox('invoice_notification', '1', isset($comSetting['invoice_notification']) && $comSetting['invoice_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notification']) }}
                                                <label class="form-check-label" for="invoice_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Revenue') }}</span>
                                                {{ Form::checkbox('revenue_notification', '1', isset($comSetting['revenue_notification']) && $comSetting['revenue_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'revenue_notification']) }}
                                                <label class="form-check-label" for="revenue_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Bill') }}</span>
                                                {{ Form::checkbox('bill_notification', '1', isset($comSetting['bill_notification']) && $comSetting['bill_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'bill_notification']) }}
                                                <label class="form-check-label" for="bill_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Invoice Payment') }}</span>
                                                {{ Form::checkbox('payment_notification', '1', isset($comSetting['payment_notification']) && $comSetting['payment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'payment_notification']) }}
                                                <label class="form-check-label" for="payment_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Budget') }}</span>
                                                {{ Form::checkbox('budget_notification', '1', isset($comSetting['budget_notification']) && $comSetting['budget_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'budget_notification']) }}
                                                <label class="form-check-label" for="budget_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div> --}}

                    <!--Telegram Settings-->
                    {{-- <div id="telegram-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Telegram Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your Telegram settings') }}</small>
                        </div>
                        {{ Form::open(['route' => 'telegram.settings', 'id' => 'telegram-setting', 'method' => 'post', 'class' => 'd-contents']) }}
                        <div class="card-body p-3">
                            <div class="row row-gap-1">
                                <div class="form-group mb-0 col-md-6">
                                    <label class="form-label">{{ __('Telegram AccessToken') }}</label> <br>
                                    {{ Form::text('telegram_accestoken', isset($comSetting['telegram_accestoken']) ? $comSetting['telegram_accestoken'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram AccessToken')]) }}
                                </div>
                                <div class="form-group mb-0 col-md-6">
                                    <label class="form-label ">{{ __('Telegram ChatID') }}</label> <br>
                                    {{ Form::text('telegram_chatid', isset($comSetting['telegram_chatid']) ? $comSetting['telegram_chatid'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram ChatID')]) }}
                                </div>

                                <div class="col-md-12">
                                    <h5 class="small-title">{{ __('Module Settings') }}</h5>
                                </div>
                            </div>

                            <div class="row row-gap-1">
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Lead') }}</span>
                                                {{ Form::checkbox('telegram_lead_notification', '1', isset($comSetting['telegram_lead_notification']) && $comSetting['telegram_lead_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_lead_notification']) }}
                                                <label class="form-check-label" for="telegram_lead_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Deal') }}</span>
                                                {{ Form::checkbox('telegram_deal_notification', '1', isset($comSetting['telegram_deal_notification']) && $comSetting['telegram_deal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_deal_notification']) }}
                                                <label class="form-check-label" for="telegram_deal_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('Lead to Deal Conversion') }}</span>
                                                {{ Form::checkbox('telegram_leadtodeal_notification', '1', isset($comSetting['telegram_leadtodeal_notification']) && $comSetting['telegram_leadtodeal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_leadtodeal_notification']) }}
                                                <label class="form-check-label" for="telegram_leadtodeal_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Contract') }}</span>
                                                {{ Form::checkbox('telegram_contract_notification', '1', isset($comSetting['telegram_contract_notification']) && $comSetting['telegram_contract_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_contract_notification']) }}
                                                <label class="form-check-label" for="telegram_contract_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Project') }}</span>
                                                {{ Form::checkbox('telegram_project_notification', '1', isset($comSetting['telegram_project_notification']) && $comSetting['telegram_project_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_project_notification']) }}
                                                <label class="form-check-label" for="telegram_project_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Task') }}</span>
                                                {{ Form::checkbox('telegram_task_notification', '1', isset($comSetting['telegram_task_notification']) && $comSetting['telegram_task_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_task_notification']) }}
                                                <label class="form-check-label" for="telegram_task_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('Task Stage Updated') }}</span>
                                                {{ Form::checkbox('telegram_taskmove_notification', '1', isset($comSetting['telegram_taskmove_notification']) && $comSetting['telegram_taskmove_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_taskmove_notification']) }}
                                                <label class="form-check-label" for="telegram_taskmove_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Task Comment') }}</span>
                                                {{ Form::checkbox('telegram_taskcomment_notification', '1', isset($comSetting['telegram_taskcomment_notification']) && $comSetting['telegram_taskcomment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_taskcomment_notification']) }}
                                                <label class="form-check-label" for="telegram_taskcomment_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Monthly Payslip') }}</span>
                                                {{ Form::checkbox('telegram_payslip_notification', '1', isset($comSetting['telegram_payslip_notification']) && $comSetting['telegram_payslip_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_payslip_notification']) }}
                                                <label class="form-check-label" for="telegram_payslip_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Award') }}</span>
                                                {{ Form::checkbox('telegram_award_notification', '1', isset($comSetting['telegram_award_notification']) && $comSetting['telegram_award_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_award_notification']) }}
                                                <label class="form-check-label" for="telegram_award_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Announcement') }}</span>
                                                {{ Form::checkbox('telegram_announcement_notification', '1', isset($comSetting['telegram_announcement_notification']) && $comSetting['telegram_announcement_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_announcement_notification']) }}
                                                <label class="form-check-label" for="telegram_announcement_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Holiday') }}</span>
                                                {{ Form::checkbox('telegram_holiday_notification', '1', isset($comSetting['telegram_holiday_notification']) && $comSetting['telegram_holiday_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_holiday_notification']) }}
                                                <label class="form-check-label" for="telegram_holiday_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Support Ticket') }}</span>
                                                {{ Form::checkbox('telegram_support_notification', '1', isset($comSetting['telegram_support_notification']) && $comSetting['telegram_support_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_support_notification']) }}
                                                <label class="form-check-label" for="telegram_support_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Event') }}</span>
                                                {{ Form::checkbox('telegram_event_notification', '1', isset($comSetting['telegram_event_notification']) && $comSetting['telegram_event_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_event_notification']) }}
                                                <label class="form-check-label" for="telegram_event_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Meeting') }}</span>
                                                {{ Form::checkbox('telegram_meeting_notification', '1', isset($comSetting['telegram_meeting_notification']) && $comSetting['telegram_meeting_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_meeting_notification']) }}
                                                <label class="form-check-label" for="telegram_meeting_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Company Policy') }}</span>
                                                {{ Form::checkbox('telegram_policy_notification', '1', isset($comSetting['telegram_policy_notification']) && $comSetting['telegram_policy_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_policy_notification']) }}
                                                <label class="form-check-label" for="telegram_policy_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Invoice') }}</span>
                                                {{ Form::checkbox('telegram_invoice_notification', '1', isset($comSetting['telegram_invoice_notification']) && $comSetting['telegram_invoice_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_invoice_notification']) }}
                                                <label class="form-check-label" for="telegram_invoice_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Revenue') }}</span>
                                                {{ Form::checkbox('telegram_revenue_notification', '1', isset($comSetting['telegram_revenue_notification']) && $comSetting['telegram_revenue_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_revenue_notification']) }}
                                                <label class="form-check-label" for="telegram_revenue_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Bill') }}</span>
                                                {{ Form::checkbox('telegram_bill_notification', '1', isset($comSetting['telegram_bill_notification']) && $comSetting['telegram_bill_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_bill_notification']) }}
                                                <label class="form-check-label" for="telegram_bill_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Invoice Payment') }}</span>
                                                {{ Form::checkbox('telegram_payment_notification', '1', isset($comSetting['telegram_payment_notification']) && $comSetting['telegram_payment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_payment_notification']) }}
                                                <label class="form-check-label" for="telegram_payment_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Budget') }}</span>
                                                {{ Form::checkbox('telegram_budget_notification', '1', isset($comSetting['telegram_budget_notification']) && $comSetting['telegram_budget_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_budget_notification']) }}
                                                <label class="form-check-label" for="telegram_budget_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end">
                            <div class="form-group mb-0">
                                <input class="btn btn-print-invoice btn-primary" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div> --}}

                    <!--Twilio Settings-->
                    {{-- <div id="twilio-settings" class="card">
                        <div class="card-header p-3">
                            <h5>{{ __('Twilio Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your Twilio settings') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'twilio.setting', 'method' => 'post']) }}
                        <div class="card-body p-3">
                            <div class="row row-gap-1">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('twilio_sid', __('Twilio SID '), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_sid', isset($comSetting['twilio_sid']) ? $comSetting['twilio_sid'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio SID'), 'required' => 'required']) }}
                                        @error('twilio_sid')
                                            <span class="invalid-twilio_sid" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('twilio_token', __('Twilio Token'), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_token', isset($comSetting['twilio_token']) ? $comSetting['twilio_token'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio Token'), 'required' => 'required']) }}
                                        @error('twilio_token')
                                            <span class="invalid-twilio_token" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        {{ Form::label('twilio_from', __('Twilio From'), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_from', isset($comSetting['twilio_from']) ? $comSetting['twilio_from'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio From'), 'required' => 'required']) }}
                                        @error('twilio_from')
                                            <span class="invalid-twilio_from" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                    <div class="col-md-12">
                                        <h5 class="small-title">{{ __('Module Settings') }}</h5>
                                    </div>
                                </div>
                                <div class="row row-gap-1">
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Customer') }}</span>
                                                {{ Form::checkbox('twilio_customer_notification', '1', isset($comSetting['twilio_customer_notification']) && $comSetting['twilio_customer_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_customer_notification']) }}
                                                <label class="form-check-label" for="twilio_customer_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Vendor') }}</span>
                                                {{ Form::checkbox('twilio_vender_notification', '1', isset($comSetting['twilio_vender_notification']) && $comSetting['twilio_vender_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_vender_notification']) }}
                                                <label class="form-check-label" for="twilio_vender_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Invoice') }}</span>
                                                {{ Form::checkbox('twilio_invoice_notification', '1', isset($comSetting['twilio_invoice_notification']) && $comSetting['twilio_invoice_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_invoice_notification']) }}
                                                <label class="form-check-label" for="twilio_invoice_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Revenue') }}</span>
                                                {{ Form::checkbox('twilio_revenue_notification', '1', isset($comSetting['twilio_revenue_notification']) && $comSetting['twilio_revenue_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_revenue_notification']) }}
                                                <label class="form-check-label" for="twilio_revenue_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Bill') }}</span>
                                                {{ Form::checkbox('twilio_bill_notification', '1', isset($comSetting['twilio_bill_notification']) && $comSetting['twilio_bill_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_bill_notification']) }}
                                                <label class="form-check-label" for="twilio_bill_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Proposal') }}</span>
                                                {{ Form::checkbox('twilio_proposal_notification', '1', isset($comSetting['twilio_proposal_notification']) && $comSetting['twilio_proposal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_proposal_notification']) }}
                                                <label class="form-check-label" for="twilio_proposal_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('New Payment') }}</span>
                                                {{ Form::checkbox('twilio_payment_notification', '1', isset($comSetting['twilio_payment_notification']) && $comSetting['twilio_payment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_payment_notification']) }}
                                                <label class="form-check-label" for="twilio_payment_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="list-group">
                                        <div class="list-group-item shadow-sm">
                                            <div class="form-switch form-switch-right">
                                                <span>{{ __('Invoice Reminder') }}</span>
                                                {{ Form::checkbox('twilio_reminder_notification', '1', isset($comSetting['twilio_reminder_notification']) && $comSetting['twilio_reminder_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'twilio_reminder_notification']) }}
                                                <label class="form-check-label" for="twilio_reminder_notification"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="card-footer p-3 text-end">
                                <div class="form-group mb-0">
                                    <input class="btn btn-print-invoice  btn-primary" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div> --}}

                        <!--ReCaptcha Settings-->
                        <div id="recaptcha-settings" class="card">
                            <form method="POST" action="{{ route('recaptcha.settings.store') }}"
                                accept-charset="UTF-8">
                                @csrf
                                <div class="card-header p-3 d-flex align-items-center gap-3 flex-wrap justify-content-between">
                                    <div class="d-flex flex-column">
                                            <h5>{{ __('ReCaptcha Settings') }}</h5>
                                            <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                target="_blank" class="text-primary">
                                                <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                            </a>
                                        </div>
                                            <div class="form-group mb-0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" name="recaptcha_module"
                                                        id="recaptcha_module"
                                                        {{ !empty($setting['recaptcha_module']) && $setting['recaptcha_module'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label" for="recaptcha_module"></label>
                                                </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group col switch-width">
                                                {{ Form::label('google_recaptcha_version', __('Google Recaptcha Version'), ['class' => ' col-form-label']) }}

                                                {{ Form::select('google_recaptcha_version', $google_recaptcha_version, isset($setting['google_recaptcha_version']) ? $setting['google_recaptcha_version'] : 'v2-checkbox', ['id' => 'google_recaptcha_version', 'class' => 'form-control choices', 'searchEnabled' => 'true']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-gap-1">

                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="google_recaptcha_key"
                                                    class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                                <input class="form-control"
                                                    placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                    name="google_recaptcha_key" type="text"
                                                    value="{{ !empty($setting['google_recaptcha_key']) ? $setting['google_recaptcha_key'] : '' }}"
                                                    id="google_recaptcha_key" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="google_recaptcha_secret"
                                                    class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                                <input class="form-control"
                                                    placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                    name="google_recaptcha_secret" type="text"
                                                    value="{{ !empty($setting['google_recaptcha_secret']) ? $setting['google_recaptcha_secret'] : '' }}"
                                                    id="google_recaptcha_secret" required>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer p-3 text-end">
                                    <div class="form-group mb-0">
                                        <input class="btn btn-print-invoice btn-primary" type="submit"
                                            value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}
                        </div>

                        <!--Email Notification Settings-->
                        <div id="email-notification-settings" class="card">
                            {{ Form::model($settings, ['route' => ['status.email.language'], 'method' => 'post']) }}
                            @csrf
                            <div class="col-md-12">
                                <div class="card-header p-3">
                                    <h5>{{ __('Email Notification Settings') }}</h5>
                                    <small class="text-muted">{{ __('Edit email notification settings') }}</small>
                                </div>

                                <div class="card-body p-3">
                                    <div class="row row-gap-1">
                                        @foreach ($EmailTemplates as $EmailTemplate)
                                            @isset($EmailTemplate->template)
                                                <div class="col-lg-4 col-md-6 col-sm-6 form-group mb-0">
                                                    <div class="list-group">
                                                        <div class="list-group-item form-switch form-switch-right p-3 shadow-sm">
                                                            <label
                                                                class="form-label mb-0">{{ $EmailTemplate->name }}</label>
                                                            {{--                                                    <input class="form-check-input email-template-checkbox" --}}
                                                            {{--                                                           id="email_tempalte_{{!empty($EmailTemplate->template)?$EmailTemplate->template->id:''}}" type="checkbox" --}}
                                                            {{--                                                           @if (!empty($EmailTemplate->template) ? $EmailTemplate->template->is_active : 0 == 1) checked="checked" @endif --}}
                                                            {{--                                                           type="checkbox" --}}
                                                            {{--                                                           value="{{!empty($EmailTemplate->template)?$EmailTemplate->template->is_active:1}}" --}}
                                                            {{--                                                           data-url="{{route('status.email.language',[!empty($EmailTemplate->template)?$EmailTemplate->template->id:''])}}" /> --}}
                                                            {{--                                                    <label class="form-check-label" for="email_tempalte_{{!empty($EmailTemplate->template)?$EmailTemplate->template->id:''}}"></label> --}}

                                                            <input class="form-check-input"
                                                                name='{{ $EmailTemplate->id }}'
                                                                id="email_tempalte_{{ $EmailTemplate->template->id }}"
                                                                type="checkbox"
                                                                @if ($EmailTemplate->template->is_active == 1) checked="checked" @endif
                                                                type="checkbox" value="1"
                                                                data-url="{{ route('status.email.language', [$EmailTemplate->template->id]) }}" />
                                                            <label class="form-check-label"
                                                                for="email_tempalte_{{ $EmailTemplate->template->id }}"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endisset
                                        @endforeach
                                    </div>

                                </div>
                                <div class="card-footer p-3 text-end">
                                    <div class="form-group mb-0">
                                        <input class="btn btn-print-invoice  btn-primary " type="submit"
                                            value="{{ __('Save Changes') }}">
                                    </div>

                                </div>
                            </div>
                            {{ Form::close() }}

                        </div>

                        <!-- Storage Settings -->
                        <div id="storage-settings" class="card mb-3">
                            {{ Form::open(['route' => 'storage.setting.store', 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header p-3">
                                <div class="row">
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        <h5 class="">{{ __('Storage Settings') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex mb-3">
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="local-outlined" autocomplete="off"
                                            {{ $setting['storage_setting'] == 'local' ? 'checked' : '' }} value="local"
                                            checked>
                                        <label class="btn btn-outline-primary"
                                            for="local-outlined">{{ __('Local') }}</label>
                                    </div>
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="s3-outlined" autocomplete="off"
                                            {{ $setting['storage_setting'] == 's3' ? 'checked' : '' }} value="s3">
                                        <label class="btn btn-outline-primary" for="s3-outlined">
                                            {{ __('AWS S3') }}</label>
                                    </div>

                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="wasabi-outlined" autocomplete="off"
                                            {{ $setting['storage_setting'] == 'wasabi' ? 'checked' : '' }}
                                            value="wasabi">
                                        <label class="btn btn-outline-primary"
                                            for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                    </div>
                                </div>
                                    <div
                                        class="local-setting row row-gap-1{{ $setting['storage_setting'] == 'local' ? ' ' : 'd-none' }}">
                                        {{-- <h4 class="small-title">{{ __('Local Settings') }}</h4> --}}
                                        <div class="form-group mb-0 col-8 switch-width">
                                            {{ Form::label('local_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                            <select name="local_storage_validation[]" class="select2"
                                                id="local_storage_validation" multiple>
                                                @foreach ($file_type as $f)
                                                    <option @if (in_array($f, $local_storage_validations)) selected @endif>
                                                        {{ $f }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                <input type="number" name="local_storage_max_upload_size"
                                                    class="form-control"
                                                    value="{{ !isset($setting['local_storage_max_upload_size']) || is_null($setting['local_storage_max_upload_size']) ? '' : $setting['local_storage_max_upload_size'] }}"
                                                    placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="s3-setting row row-gap-1 {{ $setting['storage_setting'] == 's3' ? ' ' : 'd-none' }}">

                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_key">{{ __('S3 Key') }}</label>
                                                    <input type="text" name="s3_key" class="form-control"
                                                        value="{{ !isset($setting['s3_key']) || is_null($setting['s3_key']) ? '' : $setting['s3_key'] }}"
                                                        placeholder="{{ __('S3 Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_secret">{{ __('S3 Secret') }}</label>
                                                    <input type="text" name="s3_secret" class="form-control"
                                                        value="{{ !isset($setting['s3_secret']) || is_null($setting['s3_secret']) ? '' : $setting['s3_secret'] }}"
                                                        placeholder="{{ __('S3 Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_region">{{ __('S3 Region') }}</label>
                                                    <input type="text" name="s3_region" class="form-control"
                                                        value="{{ !isset($setting['s3_region']) || is_null($setting['s3_region']) ? '' : $setting['s3_region'] }}"
                                                        placeholder="{{ __('S3 Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                    <input type="text" name="s3_bucket" class="form-control"
                                                        value="{{ !isset($setting['s3_bucket']) || is_null($setting['s3_bucket']) ? '' : $setting['s3_bucket'] }}"
                                                        placeholder="{{ __('S3 Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_url">{{ __('S3 URL') }}</label>
                                                    <input type="text" name="s3_url" class="form-control"
                                                        value="{{ !isset($setting['s3_url']) || is_null($setting['s3_url']) ? '' : $setting['s3_url'] }}"
                                                        placeholder="{{ __('S3 URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_endpoint">{{ __('S3 Endpoint') }}</label>
                                                    <input type="text" name="s3_endpoint" class="form-control"
                                                        value="{{ !isset($setting['s3_endpoint']) || is_null($setting['s3_endpoint']) ? '' : $setting['s3_endpoint'] }}"
                                                        placeholder="{{ __('S3 Endpoint') }}">
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 col-8 switch-width">
                                                {{ Form::label('s3_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                <select name="s3_storage_validation[]" class="select2"
                                                    id="s3_storage_validation" multiple>
                                                    @foreach ($file_type as $f)
                                                        <option @if (in_array($f, $s3_storage_validations)) selected @endif>
                                                            {{ $f }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="s3_max_upload_size"
                                                        class="form-control"
                                                        value="{{ !isset($setting['s3_max_upload_size']) || is_null($setting['s3_max_upload_size']) ? '' : $setting['s3_max_upload_size'] }}"
                                                        placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>

                                    </div>

                                    <div
                                        class="wasabi-setting row row-gap-1 {{ $setting['storage_setting'] == 'wasabi' ? ' ' : 'd-none' }}">
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_key">{{ __('Wasabi Key') }}</label>
                                                    <input type="text" name="wasabi_key" class="form-control"
                                                        value="{{ !isset($setting['wasabi_key']) || is_null($setting['wasabi_key']) ? '' : $setting['wasabi_key'] }}"
                                                        placeholder="{{ __('Wasabi Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                    <input type="text" name="wasabi_secret" class="form-control"
                                                        value="{{ !isset($setting['wasabi_secret']) || is_null($setting['wasabi_secret']) ? '' : $setting['wasabi_secret'] }}"
                                                        placeholder="{{ __('Wasabi Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="s3_region">{{ __('Wasabi Region') }}</label>
                                                    <input type="text" name="wasabi_region" class="form-control"
                                                        value="{{ !isset($setting['wasabi_region']) || is_null($setting['wasabi_region']) ? '' : $setting['wasabi_region'] }}"
                                                        placeholder="{{ __('Wasabi Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                    <input type="text" name="wasabi_bucket" class="form-control"
                                                        value="{{ !isset($setting['wasabi_bucket']) || is_null($setting['wasabi_bucket']) ? '' : $setting['wasabi_bucket'] }}"
                                                        placeholder="{{ __('Wasabi Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="wasabi_url">{{ __('Wasabi URL') }}</label>
                                                    <input type="text" name="wasabi_url" class="form-control"
                                                        value="{{ !isset($setting['wasabi_url']) || is_null($setting['wasabi_url']) ? '' : $setting['wasabi_url'] }}"
                                                        placeholder="{{ __('Wasabi URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="wasabi_root">{{ __('Wasabi Root') }}</label>
                                                    <input type="text" name="wasabi_root" class="form-control"
                                                        value="{{ !isset($setting['wasabi_root']) || is_null($setting['wasabi_root']) ? '' : $setting['wasabi_root'] }}"
                                                        placeholder="{{ __('Wasabi Root') }}">
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 col-8 switch-width">
                                                {{ Form::label('wasabi_storage_validation', __('Only Upload Files'), ['class' => 'form-label']) }}

                                                <select name="wasabi_storage_validation[]" class="select2"
                                                    id="wasabi_storage_validation" multiple>
                                                    @foreach ($file_type as $f)
                                                        <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>
                                                            {{ $f }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="wasabi_root">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="wasabi_max_upload_size"
                                                        class="form-control"
                                                        value="{{ !isset($setting['wasabi_max_upload_size']) || is_null($setting['wasabi_max_upload_size']) ? '' : $setting['wasabi_max_upload_size'] }}"
                                                        placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>
                                    </div>
                            </div>
                            <div class="card-footer p-3 text-end">
                                <div class="form-group mb-0">
                                    <input class="btn btn-print-invoice  btn-primary" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>

                        <div id="offer-letter-settings" class="card">
                            <div class="col-md-12">
                                <div
                                    class="card-header p-3 d-flex flex-wrap align-items-center gap-3 justify-content-between">
                                    <h5>{{ __('Offer Letter Settings') }}</h5>
                                    <div class="d-flex drp-languages">
                                        <ul class="list-unstyled m-0">
                                            <li class="dropdown dash-h-item drp-language">
                                                <a class="dash-head-link dropdown-toggle arrow-none m-0"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false" id="dropdownLanguage">
                                                    <span class="drp-text hide-mob text-primary me-2">
                                                        {{ ucfirst($offerlangName->full_name) }}
                                                    </span>
                                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                                </a>
                                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                                    aria-labelledby="dropdownLanguage">
                                                    @foreach ($currantLang as $code => $offerlangs)
                                                        <a href="{{ route('get.offerlatter.language', ['noclangs' => $noclang, 'explangs' => $explang, 'offerlangs' => $code, 'joininglangs' => $joininglang]) }}"
                                                            class="dropdown-item ms-1 {{ $offerlangs == $code ? 'text-primary' : '' }}">{{ ucFirst($offerlangs) }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                                <div class="card-body p-3 ">
                                    <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card mb-0">
                                            <div class="card-header p-3 card-body p-3">
                                                <div class="row text-xs">
                                                    <div class="row">
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Applicant Name') }} : <span
                                                                class="pull-end text-primary">{applicant_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{app_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Job title') }} : <span
                                                                class="pull-right text-primary">{job_title}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Job type') }} : <span
                                                                class="pull-right text-primary">{job_type}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Proposed Start Date') }} : <span
                                                                class="pull-right text-primary">{start_date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Working Location') }} : <span
                                                                class="pull-right text-primary">{workplace_location}</span>
                                                        </p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Days Of Week') }} : <span
                                                                class="pull-right text-primary">{days_of_week}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Salary') }} : <span
                                                                class="pull-right text-primary">{salary}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Salary Type') }} : <span
                                                                class="pull-right text-primary">{salary_type}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Salary Duration') }} : <span
                                                                class="pull-end text-primary">{salary_duration}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Offer Expiration Date') }} : <span
                                                                class="pull-right text-primary">{offer_expiration_date}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 table-border-style ">
                                    {{ Form::open(['route' => ['offerlatter.update', $offerlang], 'method' => 'post']) }}
                                    <div class="form-group col-12 mb-0">
                                        {{ Form::label('content', __(' Format'), ['class' => 'form-label text-dark']) }}
                                        <textarea name="content" class="summernote-simple0 summernote-simple">{!! isset($currOfferletterLang->content) ? $currOfferletterLang->content : '' !!}</textarea>

                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <div id="joining-letter-settings" class="card">
                            <div class="col-md-12">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <h5>{{ __('Joining Letter Settings') }}</h5>
                                    <div class="d-flex justify-content-end drp-languages">
                                        <ul class="list-unstyled mb-0">
                                            <li class="dropdown dash-h-item drp-language">
                                                <a class="dash-head-link dropdown-toggle arrow-none me-0"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false"
                                                    id="dropdownLanguage1">
                                                    <span class="drp-text hide-mob text-primary me-2">
                                                        {{ ucfirst($joininglangName->full_name) }}
                                                    </span>
                                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                                </a>
                                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                                    aria-labelledby="dropdownLanguage1">
                                                    @foreach ($currantLang as $code => $joininglangs)
                                                        <a href="{{ route('get.joiningletter.language', ['noclangs' => $noclang, 'explangs' => $explang, 'offerlangs' => $offerlang, 'joininglangs' => $code]) }}"
                                                            class="dropdown-item {{ $joininglangs == $code ? 'text-primary' : '' }}">{{ ucFirst($joininglangs) }}</a>
                                                    @endforeach
                                                </div>
                                            </li>

                                        </ul>
                                    </div>

                                </div>
                                <div class="card-body p-3 ">
                                    <h5 class= "font-weight-bold mb-2">{{ __('Placeholders') }}</h5>

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card mb-0">
                                            <div class="card-header p-3 card-body p-3">
                                                <div class="row text-xs">
                                                    <div class="row">
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Applicant Name') }} : <span
                                                                class="pull-end text-primary">{date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{app_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Address') }} : <span
                                                                class="pull-right text-primary">{address}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Designation') }} : <span
                                                                class="pull-right text-primary">{designation}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Start Date') }} : <span
                                                                class="pull-right text-primary">{start_date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Branch') }} : <span
                                                                class="pull-right text-primary">{branch}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Start Time') }} : <span
                                                                class="pull-end text-primary">{start_time}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('End Time') }} : <span
                                                                class="pull-right text-primary">{end_time}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Number of Hours') }} : <span
                                                                class="pull-right text-primary">{total_hours}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 table-border-style ">

                                    {{ Form::open(['route' => ['joiningletter.update', $joininglang], 'method' => 'post']) }}
                                    <div class="form-group col-12">
                                        {{ Form::label('content', __(' Format'), ['class' => 'form-label text-dark']) }}
                                        <textarea name="content" class="summernote-simple1 summernote-simple">{!! isset($currjoiningletterLang->content) ? $currjoiningletterLang->content : '' !!}</textarea>

                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>

                        </div>

                        <div id="experience-certificate-settings" class="card">
                            <div class="col-md-12">
                                <div
                                    class="card-header p-3 d-flex flex-wrap align-items-center gap-3 justify-content-between">
                                    <h5>{{ __('Experience Certificate Settings') }}</h5>
                                    <div class="d-flex drp-languages">
                                        <ul class="list-unstyled m-0">
                                            <li class="dropdown dash-h-item drp-language">
                                                <a class="dash-head-link dropdown-toggle arrow-none me-0"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false"
                                                    id="dropdownLanguage1">
                                                    <span class="drp-text hide-mob text-primary me-2">
                                                        {{ ucfirst($explangName->full_name) }}
                                                    </span>
                                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                                </a>
                                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                                    aria-labelledby="dropdownLanguage1">
                                                    @foreach ($currantLang as $code => $explangs)
                                                        <a href="{{ route('get.experiencecertificate.language', ['noclangs' => $noclang, 'explangs' => $code, 'offerlangs' => $offerlang, 'joininglangs' => $joininglang]) }}"
                                                            class="dropdown-item {{ $explangs == $code ? 'text-primary' : '' }}">{{ ucFirst($explangs) }}</a>
                                                    @endforeach
                                                </div>
                                            </li>

                                        </ul>
                                    </div>

                                </div>
                                <div class="card-body p-3 ">
                                    <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card mb-0">
                                            <div class="card-header p-3 card-body p-3">
                                                <div class="row text-xs">
                                                    <div class="row">
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{app_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Date of Issuance') }} : <span
                                                                class="pull-right text-primary">{date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Designation') }} : <span
                                                                class="pull-right text-primary">{designation}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Start Date') }} : <span
                                                                class="pull-right text-primary">{start_date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Branch') }} : <span
                                                                class="pull-right text-primary">{branch}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Start Time') }} : <span
                                                                class="pull-end text-primary">{start_time}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('End Time') }} : <span
                                                                class="pull-right text-primary">{end_time}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Number of Hours') }} : <span
                                                                class="pull-right text-primary">{total_hours}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 table-border-style ">

                                    {{ Form::open(['route' => ['experiencecertificate.update', $explang], 'method' => 'post']) }}
                                    <div class="form-group col-12">
                                        {{ Form::label('content', __(' Format'), ['class' => 'form-label text-dark']) }}
                                        <textarea name="content" class="summernote-simple2 summernote-simple">{!! isset($curr_exp_cetificate_Lang->content) ? $curr_exp_cetificate_Lang->content : '' !!}</textarea>

                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <div id="noc-settings" class="card">
                            <div class="col-md-12">
                                <div
                                    class="card-header p-3 d-flex flex-wrap align-items-center gap-3 justify-content-between">
                                    <h5>{{ __('NOC Settings') }}</h5>
                                    <div class="d-flex drp-languages">
                                        <ul class="list-unstyled m-0">
                                            <li class="dropdown dash-h-item drp-language">
                                                <a class="dash-head-link dropdown-toggle arrow-none m-0"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false"
                                                    id="dropdownLanguage1">
                                                    <span class="drp-text hide-mob text-primary me-2">
                                                        {{ ucfirst($noclangName->full_name) }}
                                                    </span>
                                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                                </a>
                                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                                    aria-labelledby="dropdownLanguage1">
                                                    @foreach ($currantLang as $code => $noclangs)
                                                        <a href="{{ route('get.noc.language', ['noclangs' => $code, 'explangs' => $explang, 'offerlangs' => $offerlang, 'joininglangs' => $joininglang]) }}"
                                                            class="dropdown-item {{ $noclangs == $code ? 'text-primary' : '' }}">{{ ucfirst($noclangs) }}</a>
                                                    @endforeach
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body p-3 ">
                                    <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card mb-0">
                                            <div class="card-header p-3 card-body p-3">
                                                <div class="row text-xs">
                                                    <div class="row">
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Date') }} : <span
                                                                class="pull-end text-primary">{date}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{app_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                        <p class="col-12 col-md-4 col-sm-6">{{ __('Designation') }} : <span
                                                                class="pull-right text-primary">{designation}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 table-border-style ">
                                    {{ Form::open(['route' => ['noc.update', $noclang], 'method' => 'post']) }}
                                    <div class="form-group col-12">
                                        {{ Form::label('content', __(' Format'), ['class' => 'form-label text-dark']) }}
                                        <textarea name="content" class="summernote-simple3 summernote-simple">{!! isset($currnocLang->content) ? $currnocLang->content : '' !!}</textarea>

                                    </div>

                                    {{ Form::close() }}
                                </div>
                            </div>

                        </div>



                        {{-- SEO settings --}
                        {{-- <div id="seo-settings" class="card">
                            <div class="card-header p-3 d-flex justify-content-between">
                                <h5>{{ __('SEO Settings') }}</h5>
                                @php
                                    $settings = \App\Models\Utility::settings();
                                @endphp
                                @if (!empty($settings['chat_gpt_key']))
                                    <div class="d-flex justify-content-end">
                                        <div class="mt-0">
                                            <a data-size="md" class="btn btn-primary text-white btn-sm"
                                                data-ajax-popup-over="true"
                                                data-url="{{ route('generate', ['seo']) }}" data-bs-placement="top"
                                                data-title="{{ __('Generate content with AI') }}">
                                                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{ Form::open(['url' => route('seo.settings.store'), 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                            @csrf
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Meta Keywords', __('Meta Keywords'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('meta_title', !empty($setting['meta_title']) ? $setting['meta_title'] : '', ['class' => 'form-control ', 'placeholder' => 'Meta Keywords']) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('Meta Description', __('Meta Description'), ['class' => 'col-form-label']) }}
                                            {{ Form::textarea('meta_desc', !empty($setting['meta_desc']) ? $setting['meta_desc'] : '', ['class' => 'form-control ', 'placeholder' => 'Meta Description', 'rows' => 7]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            {{ Form::label('Meta Image', __('Meta Image'), ['class' => 'col-form-label']) }}
                                        </div>
                                        <div class="setting-card">
                                            <div class="logo-content">
                                                <img id="image2"
                                                    src="{{ $meta_image . '/' . (isset($setting['meta_image']) && !empty($setting['meta_image']) ? $setting['meta_image'] : 'default.png') }}"
                                                    class="img_setting seo_image">
                                            </div>
                                            <div class="choose-files mt-4">
                                                <label for="meta_image" class="col-form-label">
                                                    <div class="bg-primary company_favicon_update"> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" class="form-control file" id="meta_image"
                                                        name="meta_image" data-filename="meta_image">
                                                </label>
                                            </div>
                                            @error('meta_image')
                                                <div class="row">
                                                    <span class="invalid-logo" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-3 text-end">
                                <div class="form-group mb-0">
                                    <input class="btn btn-print-invoice btn-primary" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div> --}}

                        {{-- webhook settings
                        <div id="webhook-settings" class="card">
                            <div class="col-md-12">
                                <div class="card-header p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="mb-2">{{ __('Webhook Settings') }}</h5>
                                        </div>
                                        @can('create webhook')
                                            <div class="col-6 text-end">
                                                <a href="#" data-size="lg"
                                                    data-url="{{ route('webhook.create') }}" data-ajax-popup="true"
                                                    data-bs-toggle="tooltip" title="{{ __('Create') }}"
                                                    data-title="{{ __('Create New Webhook') }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body table-border-style">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Module') }}</th>
                                                    <th>{{ __('Url') }}</th>
                                                    <th>{{ __('Method') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="font-style">
                                                @forelse ($webhookSetting as $webhooksetting)
                                                    <tr>
                                                        <td>{{ ucwords($webhooksetting->module) }}</td>
                                                        <td>{{ $webhooksetting->url }}</td>
                                                        <td>{{ ucwords($webhooksetting->method) }}</td>
                                                        <td class="Action">
                                                            <span>
                                                                @can('edit webhook')
                                                                    <div class="action-btn  me-2">
                                                                        <a href="#"
                                                                            class="mx-3 bg-info btn btn-sm  align-items-center"
                                                                            data-url="{{ URL::to('webhook-settings/' . $webhooksetting->id . '/edit') }}"
                                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                            title="{{ __('Edit') }}"
                                                                            data-title="{{ __('Webhook Edit') }}">
                                                                            <i class="ti ti-pencil text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                @endcan
                                                                @can('delete webhook')
                                                                    <div class="action-btn">
                                                                        {!! Form::open([
                                                                            'method' => 'DELETE',
                                                                            'route' => ['webhook.destroy', $webhooksetting->id],
                                                                            'id' => 'delete-form-' . $webhooksetting->id,
                                                                        ]) !!}
                                                                        <a href="#!"
                                                                            class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para"
                                                                            data-bs-toggle="tooltip"
                                                                            title="{{ __('Delete') }}">
                                                                            <i class="ti ti-trash text-white text-white"></i>
                                                                        </a>
                                                                        {!! Form::close() !!}
                                                                    </div>
                                                                @endcan
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="text-center">
                                                        <td colspan="4">{{ __('No Data Found.!') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- Cookie settings --}}
                        <div class="card" id="cookie-settings">
                            {{ Form::model($settings, ['route' => 'cookie.setting', 'method' => 'post']) }}
                            <div
                                class="card-header p-3 flex-column flex-lg-row d-flex align-items-lg-center gap-2 justify-content-between">
                                <h5>{{ __('Cookie Settings') }}</h5>
                                <div class="d-flex align-items-center">
                                    {{ Form::label('enable_cookie', __('Enable cookie'), ['class' => 'col-form-label p-0 fw-bold me-3']) }}
                                    <div class="custom-control custom-switch" onclick="enablecookie()">
                                        <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary"
                                            name="enable_cookie" class="form-check-input input-primary "
                                            id="enable_cookie"
                                            {{ $settings['enable_cookie'] == 'on' ? ' checked ' : '' }}>
                                        <label class="custom-control-label mb-1" for="enable_cookie"></label>
                                    </div>

                                </div>
                            </div>
                            <div
                                class="card-body p-3 cookieDiv {{ $settings['enable_cookie'] == 'off' ? 'disabledCookie ' : '' }}">
                                @php
                                    $settings = \App\Models\Utility::settings();
                                @endphp
                                <div class="row text-end">
                                    @if (!empty($settings['chat_gpt_key']))
                                        <div class="mt-0">
                                            <a data-size="md" class="btn btn-primary text-white btn-sm"
                                                data-ajax-popup-over="true"
                                                data-url="{{ route('generate', ['cookie']) }}"
                                                data-bs-placement="top"
                                                data-title="{{ __('Generate content with AI') }}">
                                                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="row row-gap-1">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body p-3">
                                        <div class="form-check form-switch custom-switch-v1" id="cookie_log">
                                            <input type="checkbox" name="cookie_logging"
                                                class="form-check-input input-primary cookie_setting"
                                                id="cookie_logging"
                                                {{ $settings['cookie_logging'] == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label"
                                                for="cookie_logging">{{ __('Enable logging') }}</label>
                                        </div>


                                    </div>
                                    </div>
                                        <div class="form-group">
                                            {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('cookie_title', null, ['class' => 'form-control cookie_setting', 'placeholder' => __('Enter Cookie Title')]) }}
                                        </div>
                                        <div class="form-group ">
                                            {{ Form::label('cookie_description', __('Cookie Description'), ['class' => ' form-label']) }}
                                            {!! Form::textarea('cookie_description', null, [
                                                'class' => 'form-control cookie_setting',
                                                'rows' => '3',
                                                'placeholder' => __('Enter Cookie Description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body p-3">
                                        <div class="form-check form-switch custom-switch-v1 ">
                                            <input type="checkbox" name="necessary_cookies"
                                                class="form-check-input input-primary" id="necessary_cookies" checked
                                                onclick="return false">
                                            <label class="form-check-label"
                                                for="necessary_cookies">{{ __('Strictly necessary cookies') }}</label>
                                        </div>


                                    </div>
                                </div>
                                        <div class="form-group ">
                                            {{ Form::label('strictly_cookie_title', __(' Strictly Cookie Title'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('strictly_cookie_title', null, ['class' => 'form-control cookie_setting', 'placeholder' => __('Enter Strictly Cookie Title')]) }}
                                        </div>
                                        <div class="form-group ">
                                            {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => ' form-label']) }}
                                            {!! Form::textarea('strictly_cookie_description', null, [
                                                'class' => 'form-control cookie_setting ',
                                                'rows' => '3',
                                                'placeholder' => __('Enter Strictly Cookie Description'),
                                            ]) !!}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <h5 class="mb-0">{{ __('More Information') }}</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            {{ Form::label('more_information_description', __('Contact Us Description'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('more_information_description', null, ['class' => 'form-control cookie_setting', 'placeholder' => __('Enter Contact Us Description')]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            {{ Form::label('contactus_url', __('Contact Us URL'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('contactus_url', null, ['class' => 'form-control cookie_setting', 'placeholder' => __('Enter Contact Us URL')]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-3 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if (isset($settings['cookie_logging']) && $settings['cookie_logging'] == 'on')
                                            <label for="file"
                                                class="form-label m-0">{{ __('Download cookie accepted data') }}</label>
                                            <a href="{{ asset(Storage::url('uploads/sample')) . '/data.csv' }}"
                                                class="btn btn-primary mr-3" download="">
                                                <i class="ti ti-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                        <div class="form-group mb-0">
                                            <input class="btn btn-print-invoice  btn-primary cookie_btn m-0" type="submit"
                                                value="{{ __('Save Changes') }}">
                                        </div>
                            </div>
                            {{ Form::close() }}
                        </div>

                        {{-- Cache settings --}}
                        <div class="card" id="cache-settings">
                            <div class="card-header p-3">
                                <h5>{{ 'Cache Settings' }}</h5>
                                <small class="text-secondary font-weight-bold">
                                    {{ __("This is a page meant for more advanced users, simply ignore it if you don't understand what cache is.") }}
                                </small>
                            </div>
                            <form method="POST" action="{{ route('cache.settings.store') }}"
                                accept-charset="UTF-8">
                                @csrf
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-12 form-group mb-0">
                                            {{ Form::label('Current cache size', __('Current cache size'), ['class' => 'col-form-label']) }}
                                            <div class="input-group mb-0">
                                                <input type="text" class="form-control"
                                                    value="{{ $file_size }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"
                                                        id="basic-addon6">{{ __('MB') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer p-3 text-end">
                                    <div class="form-group mb-0">
                                        <input class="btn btn-print-invoice btn-primary" type="submit"
                                            value="{{ __('Cache Clear') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}

                        </div>

                        {{-- chat gpt settings --}}
                        <div id="chat-gpt-settings" class="card">
                            {{ Form::model($settings, ['route' => 'chatgpt.settings', 'method' => 'post']) }}

                            <div class="card-header p-3 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h5 class="mb-2">{{ __('Chat GPT Settings') }}</h5>
                                        <div class="form-group mb-0">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="ai_chatgpt_enable" id="ai_chatgpt_enable"
                                                    data-toggle="switchbutton" data-onstyle="primary"
                                                    {{ $settings['ai_chatgpt_enable'] == 'on' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ai_chatgpt_enable"></label>
                                            </div>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        {{ Form::label('chat_gpt_key', __('Chat GPT key'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('chat_gpt_key', isset($settings['chat_gpt_key']) ? $settings['chat_gpt_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Chat GPT API Key'), 'required' => 'required']) }}
                                    </div>
                                    @php
                                        $ai_models = Utility::getAiModelName();
                                    @endphp
                                    <div class="form-group col-md-12 mb-0">
                                        {{ Form::label('chat_gpt_model', __('Chat GPT Model Name'), ['class' => 'col-form-label']) }}
                                        {{ Form::select('chat_gpt_model', $ai_models, isset($settings['chat_gpt_model']) ? $settings['chat_gpt_model'] : '', ['class' => 'form-control', 'placeholder' => __('Select Chat GPT Model'), 'required' => 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-3 text-end">
                                <div class="form-group mb-0">
                                    <input class="btn btn-print-invoice btn-primary" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>

                        <div id="ip-restriction-settings" class="card">
                            <div class="col-md-12">
                                <div class="card-header p-3 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                            <h5>{{ __('IP Restriction Settings') }}</h5>
                                        @can('create webhook')
                                                <a data-size="md" data-url="{{ route('create.ip') }}"
                                                    data-ajax-popup="true" data-bs-toggle="tooltip"
                                                    title="{{ __('Create') }}" data-title="{{ __('Create New IP') }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>

                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body table-border-style">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="w-75">{{ __('IP') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="font-style">
                                                @forelse ($ips as $ip)
                                                    <tr>
                                                        <td>{{ $ip->ip }}</td>

                                                        <td class="Action">
                                                            <span>
                                                                @can('edit webhook')
                                                                    <div class="action-btn  me-2">
                                                                        <a class="mx-3 bg-info btn btn-sm  align-items-center"
                                                                            data-url="{{ route('edit.ip', $ip->id) }}"
                                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                            title="{{ __('Edit') }}"
                                                                            data-title="{{ __('IP Edit') }}">
                                                                            <i class="ti ti-pencil text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                @endcan
                                                                @can('delete webhook')
                                                                    <div class="action-btn">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['destroy.ip', $ip->id], 'id' => 'delete-form-' . $ip->id]) !!}
                                                                        <a class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                                            data-bs-toggle="tooltip"
                                                                            title="{{ __('Delete') }}">
                                                                            <i class="ti ti-trash text-white text-white"></i>
                                                                        </a>
                                                                        {!! Form::close() !!}
                                                                    </div>
                                                                @endcan
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="text-center">
                                                        <td colspan="4">{{ __('No Data Found.!') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        <!-- Biometric Attendance Seetings -->
                        <div class="card" id="biometric-attendance">
                        <div class="col-md-12">
                            {{ Form::open(['route' => ['biometric-settings.store'], 'method' => 'post']) }}
                            <div class="card-header p-3">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5 class="">
                                            {{ __('Biometric Attendance') }}
                                        </h5>
                                        <small class="text-muted">
                                            <b class="text-danger">{{ __('Note') }}: </b>
                                            {{ __('Note that you can use the biometric attendance system only if you are using the ZKTeco machine for biometric attendance.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        {{ Form::label('zkteco_api_url', __('ZKTeco Api URL'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('zkteco_api_url', !empty($settings['zkteco_api_url']) ? $settings['zkteco_api_url'] : '', ['class' => 'form-control ', 'placeholder' => 'ZKTeco Api URL', 'required' => 'required']) }}
                                        <small>
                                            <b class="text-dark">{{ __('Example:') }}</b> http://110.78.645.123:8080
                                        </small>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        {{ Form::label('username', __('Username'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('username', !empty($settings['username']) ? $settings['username'] : '', ['class' => 'form-control ', 'placeholder' => 'Username', 'required' => 'required']) }}
                                    </div>
                                    <div class="col-md-3 form-group">
                                        {{ Form::label('user_password', __('Password'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('user_password', !empty($settings['user_password']) ? $settings['user_password'] : '', ['class' => 'form-control ', 'placeholder' => 'Password', 'required' => 'required']) }}
                                    </div>
                                    <div class="col-md-8 form-group">
                                        {{ Form::label('auth_token', __('Auth Token'), ['class' => 'form-label']) }}
                                        @if (empty($settings['auth_token']))
                                            <small class="text-danger">
                                                {{ __('Please first generate auth token.') }}
                                            </small>
                                        @endif
                                        {{ Form::textarea('', !empty($settings['auth_token']) ? $settings['auth_token'] : null, ['class' => 'form-control font-style', 'enabled' => 'enabled', 'rows' => 3]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-3 text-end">
                                <button class="btn-submit btn btn-primary" type="submit">
                                    {{ __('Generate Token') }}
                                </button>
                            </div>
                            {{ Form::close() }}
                        </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
@endsection
