@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_favicon = Utility::companyData($user->created_by, 'company_favicon');
    $setting = DB::table('settings')->where('created_by', $user->creatorId())->pluck('value', 'name')->toArray();
    $settings_data = \App\Models\Utility::settingsById($user->created_by);
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
    $getseo = App\Models\Utility::getSeoSetting();
    $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
    $metsdesc = isset($getseo['meta_desc']) ? $getseo['meta_desc'] : '';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
    $get_cookie = \App\Models\Utility::getCookieSetting();
    $company_logo=Utility::getValByName('company_logo');

@endphp
<!DOCTYPE html>

<html lang="en" dir="{{ $settings_data['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $settings_data['company_name']. ' - ' . __('Paylip') }}</title>

    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metsdesc }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metsdesc }}">
    <meta property="og:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metsdesc }}">
    <meta property="twitter:image" content="{{ $meta_image . $meta_logo }}">

    <link rel="icon"
        href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}"
        type="image" sizes="16x16">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">


    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->
    @if ($settings_data['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
    @if ($settings_data['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="style">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="style">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="main-style-link">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">

    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @stack('css-page')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #card-element {
            border: 1px solid #a3afbb !important;
            border-radius: 10px !important;
            padding: 10px !important;
        }
    </style>
</head>

<body class="{{ $themeColor }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="main-content">
                    <div class="text-md-right text-end my-3">
                        <a href="#" class="btn btn-warning" onclick="saveAsPDF()"><span class="fa fa-download"></span></a>
                    </div>
                    <div class="card invoice" id="printableArea">
                        <div class="card-body invoice-print">
                            <div class="invoice-title">
                                <h4 class="mb-4 fs-3">{{__('Payslip')}}</h4>
                                <div class="payslip-number">
                                    <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}" width="170px;" alt="">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled mb-1">
                                        <li class="mb-1"><strong>{{__('Name')}} :</strong> {{$employee->name}}</li>
                                        <li class="mb-1"><strong>{{__('Position')}} :</strong> {{__('Employee')}}</li>
                                        <li> <strong>{{__('Salary Date')}} :</strong> {{$user->dateFormat( $employee->created_at)}}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 text-md-right mb-0">
                                    <ul class="list-unstyled mb-md-2 mb-0">
                                        <li class="mb-1"> <strong class="d-block">{{$settings_data['company_name'] ?? 'ERPGo'}} : </strong>{{$settings_data['company_address']}} , {{$settings_data['company_city']}},
                                            {{$settings_data['company_state']}}-{{$settings_data['company_zipcode']}}</li>
                                            <li><strong>{{__('Salary Slip')}} : </strong>{{ $user->dateFormat( $payslip->salary_month)}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="table-responsive m-0 w-100">
                                        <table class="table table-striped table-hover table-md">
                                            <tbody>
                                            <tr>
                                                <th>{{__('Earning')}}</th>
                                                <th>{{__('Title')}}</th>
                                                <th class="text-end">{{__('Amount')}}</th>
                                            </tr>
                                            <tr>
                                                <td>{{__('Basic Salary')}}</td>
                                                <td>-</td>
                                                <td class="text-end">{{  $user->priceFormat( $payslip->basic_salary)}}</td>
                                            </tr>
                                            @foreach($payslipDetail['earning']['allowance'] as $allowance)
                                                <tr>
                                                    <td>{{__('Allowance')}}</td>
                                                    <td>{{$allowance->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $allowance->amount)}}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($payslipDetail['earning']['commission'] as $commission)
                                                <tr>
                                                    <td>{{__('Commission')}}</td>
                                                    <td>{{$commission->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $commission->amount)}}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($payslipDetail['earning']['otherPayment'] as $otherPayment)
                                                <tr>
                                                    <td>{{__('Other Payment')}}</td>
                                                    <td>{{$otherPayment->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $otherPayment->amount)}}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($payslipDetail['earning']['overTime'] as $overTime)
                                                <tr>
                                                    <td>{{__('OverTime')}}</td>
                                                    <td>{{$overTime->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $overTime->amount)}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive m-0 w-100 mt-4">
                                        <table class="table table-striped table-hover table-md">
                                            <tbody>
                                            <tr>
                                                <th>{{__('Deduction')}}</th>
                                                <th>{{__('Title')}}</th>
                                                <th class="text-end">{{__('Amount')}}</th>
                                            </tr>

                                            @foreach($payslipDetail['deduction']['loan'] as $loan)
                                                <tr>
                                                    <td>{{__('Loan')}}</td>
                                                    <td>{{$loan->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $loan->amount)}}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($payslipDetail['deduction']['deduction'] as $deduction)
                                                <tr>
                                                    <td>{{__('Saturation Deduction')}}</td>
                                                    <td>{{$deduction->title}}</td>
                                                    <td class="text-end">{{  $user->priceFormat( $deduction->amount)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-4 justify-content-end">
                                        <div class="col-lg-3 col-md-5 text-end">
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center justify-content-between mb-1"><strong>{{__('Total Earning')}}</strong><p class="mb-0">{{ $user->priceFormat($payslipDetail['totalEarning'])}}</p></li>
                                                <li class="d-flex align-items-center justify-content-between mb-1"><strong>{{__('Total Deduction')}}</strong><p class="mb-0">{{ $user->priceFormat($payslipDetail['totalDeduction'])}}</p></li>
                                                <li class="d-flex align-items-center justify-content-between pt-2 border-top mt-2"><strong>{{__('Net Salary')}}</strong><p class="mb-0 f-w-600">{{ $user->priceFormat($payslip->net_payble)}}</p></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-md-right pt-2 border-top mt-2">
                                <div class="float-lg-left mb-lg-0 mb-3 ">
                                    <p class="mt-2">{{__('Employee Signature')}}</p>
                                </div>
                                <p class="mt-2 "> {{__('Paid By')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer id="footer-main">
        <div class="footer-dark">
            <div class="container">
                <div class="row align-items-center justify-content-md-between py-4 mt-4 delimiter-top">
                    <div class="col-md-6">
                        <div class="copyright text-sm font-weight-bold text-center text-md-left">
                            {{ !empty($companySettings['footer_text']) ? $companySettings['footer_text']->value : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/dash.js') }}"></script>

    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>

    <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>

    <!-- Apex Chart -->
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>


    <script src="{{ asset('js/jscolor.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    @if ($message = Session::get('success'))
        <script>
            show_toastr('success', '{!! $message !!}');
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            show_toastr('error', '{!! $message !!}');
        </script>
    @endif

    @if ($get_cookie['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif

    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: '{{$employee->name}}',
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };
            html2pdf().set(opt).from(element).save();
        }

        // $(document).ready(function() {
        //     saveAsPDF();
        //     setTimeout(() => {
        //         window.close();
        //     }, 2000);
        // });
    </script>

</body>

</html>

