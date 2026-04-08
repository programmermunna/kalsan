    @php
        $logo = \App\Models\Utility::get_file('uploads/logo');
        $company_favicon = $settings['company_favicon'];
        $setting = DB::table('settings')->where('created_by', $user->creatorId())->pluck('value', 'name')->toArray();
        $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
        if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
            $themeColor = 'custom-color';
        } else {
            $themeColor = $color;
        }
        $company_setting=\App\Models\Utility::settingsById($invoice->created_by);
        $getseo = App\Models\Utility::getSeoSetting();
        $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
        $metsdesc = isset($getseo['meta_desc']) ? $getseo['meta_desc'] : '';
        $meta_image = \App\Models\Utility::get_file('uploads/meta/');
        $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
    @endphp
    <!DOCTYPE html>

    <html lang="en" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>
            {{ Utility::companyData($invoice->created_by, 'title_text') ? Utility::companyData($invoice->created_by, 'title_text') : config('app.name', 'ERPGO') }}
            - {{ __('Invoice') }}</title>

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
        @if ($settings['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
        @endif
        @if ($settings['cust_darklayout'] == 'on')
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
        <header class="header header-transparent" id="header-main">

        </header>

        <div class="main-content container">
            <div class="row justify-content-between align-items-center mb-3">
                <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">

                    <div class="all-button-box mx-2">
                        <a href="{{ route('invoice.pdf', Crypt::encrypt($invoice->id)) }}" target="_blank"
                            class="btn btn-primary mt-3" >
                            {{ __('Download') }}
                        </a>
                    </div>

                    @if (
                        $invoice->status != 0 &&
                            $invoice->getDue() > 0 &&
                            (!empty($company_payment_setting) &&
                                ($company_payment_setting['is_bank_transfer_enabled'] == 'on' ||
                                    $company_payment_setting['is_stripe_enabled'] == 'on' ||
                                    $company_payment_setting['is_paypal_enabled'] == 'on' ||
                                    $company_payment_setting['is_paystack_enabled'] == 'on' ||
                                    $company_payment_setting['is_flutterwave_enabled'] == 'on' ||
                                    $company_payment_setting['is_razorpay_enabled'] == 'on' ||
                                    $company_payment_setting['is_mercado_enabled'] == 'on' ||
                                    $company_payment_setting['is_paytm_enabled'] == 'on' ||
                                    $company_payment_setting['is_mollie_enabled'] == 'on' ||
                                    $company_payment_setting['is_paypal_enabled'] == 'on' ||
                                    $company_payment_setting['is_skrill_enabled'] == 'on' ||
                                    $company_payment_setting['is_coingate_enabled'] == 'on' ||
                                    $company_payment_setting['is_paymentwall_enabled'] == 'on' ||
                                    $company_payment_setting['is_toyyibpay_enabled'] == 'on' ||
                                    $company_payment_setting['is_payfast_enabled'] == 'on' ||
                                    $company_payment_setting['is_iyzipay_enabled'] == 'on' ||
                                    $company_payment_setting['is_sspay_enabled'] == 'on' ||
                                    $company_payment_setting['is_paytab_enabled'] == 'on' ||
                                    $company_payment_setting['is_benefit_enabled'] == 'on' ||
                                    $company_payment_setting['is_cashfree_enabled'] == 'on' ||
                                    $company_payment_setting['is_aamarpay_enabled'] == 'on' ||
                                    $company_payment_setting['is_yookassa_enabled'] == 'on' ||
                                    $company_payment_setting['is_midtrans_enabled'] == 'on' ||
                                    $company_payment_setting['is_nepalste_enabled'] == 'on' ||
                                    $company_payment_setting['is_paiementpro_enabled'] == 'on' ||
                                    $company_payment_setting['is_xendit_enabled'] == 'on' ||
                                    $company_payment_setting['is_cinetpay_enabled'] == 'on' ||
                                    $company_payment_setting['is_fedapay_enabled'] == 'on' ||
                                    $company_payment_setting['is_payhere_enabled'] == 'on' ||
                                    $company_payment_setting['tap_payment_is_on'] == 'on' ||
                                    $company_payment_setting['authorizenet_payment_is_on'] == 'on' ||
                                    $company_payment_setting['khalti_payment_is_on'] == 'on' ||
                                    $company_payment_setting['easebuzz_payment_is_on'] == 'on' ||
                                    $company_payment_setting['company_ozow_payment_is_enabled'] == 'on')))
                        <div class="all-button-box">
                            <a href="#" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#paymentModal">
                                {{ __('Pay Now') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row invoice-title mt-2">
                                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                            <h2>{{ __('Invoice') }}</h2>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                            <h3 class="invoice-number float-right">
                                                {{ $user->invoiceNumberFormat($invoice->invoice_id) }}</h3>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-end">
                                            <div class="d-flex align-items-center justify-content-end">

                                                <div class="me-4">
                                                    <small>
                                                        <strong>{{ __('Issue Date') }} :</strong><br>
                                                        {{ \App\Models\Utility::dateFormat($settings, $invoice->issue_date) }}<br><br>
                                                    </small>
                                                </div>
                                                <small>
                                                    <strong>{{ __('Due Date') }} :</strong><br>
                                                    {{ \App\Models\Utility::dateFormat($settings, $invoice->due_date) }}<br><br>
                                                </small>

                                            </div>
                                        </div>
                                    </div>



                                    <div class="row">
                                        @if (!empty($customer->billing_name))
                                            <div class="col">
                                                <small class="font-style">
                                                    <strong>{{ __('Billed To') }} :</strong><br>
                                                    {{ !empty($customer->billing_name) ? $customer->billing_name : '' }}<br>
                                                    {{ !empty($customer->billing_phone) ? $customer->billing_phone : '' }}<br>
                                                    {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}<br>
                                                    {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' }}<br>
                                                    {{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }}
                                                    {{ !empty($customer->billing_state) ? $customer->billing_state : '', ', ' }}
                                                    {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}
                                                </small>
                                            </div>
                                        @endif
                                        @if (\Utility::companyData($invoice->created_by, 'shipping_display') == 'on')
                                            <div class="col">
                                                <small>
                                                    <strong>{{ __('Shipped To') }} :</strong><br>
                                                    {{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}<br>
                                                    {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}<br>
                                                    {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}<br>
                                                    {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}<br>
                                                    {{ !empty($customer->shipping_city) ? $customer->shipping_city : '' . ', ' }}
                                                    {{ !empty($customer->shipping_state) ? $customer->shipping_state : '' . ', ' }},{{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}
                                                </small>
                                            </div>
                                        @endif
                                        <div class="col">
                                            <div class="float-end mt-3">
                                                @if($settings['invoice_qr_display'] == 'on')
                                                {!! DNS2D::getBarcodeHTML(
                                                    route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                                    'QRCODE',
                                                    2,
                                                    2,
                                                ) !!}
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mt-2">
                                        <div class="col">
                                            @if($company_setting['vat_gst_number_switch'] == 'on')
                                            @if(!empty($company_setting['tax_type']) && !empty($company_setting['vat_number'])){{$company_setting['tax_type'].' '. __('Number')}} : {{$company_setting['vat_number']}} <br>@endif

                                            <strong>{{__('Tax Number ')}} : </strong>{{!empty($customer->tax_number)?$customer->tax_number:'--'}}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <small>
                                                <strong>{{ __('Status') }} :</strong><br>
                                                @if ($invoice->status == 0)
                                                    <span
                                                        class="badge bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 1)
                                                    <span
                                                        class="badge bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 2)
                                                    <span
                                                        class="badge bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 3)
                                                    <span
                                                        class="badge bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 4)
                                                    <span
                                                        class="badge bg-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                @endif
                                            </small>
                                        </div>


                                        @if (!empty($customFields) && count($invoice->customField) > 0)
                                            @foreach ($customFields as $field)
                                                <div class="col text-md-right">
                                                    <small>
                                                        <strong>{{ $field->name }} :</strong><br>
                                                        {{ !empty($invoice->customField) ? $invoice->customField[$field->id] : '-' }}
                                                        <br><br>
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="font-weight-bold">{{ __('Product Summary') }}</div>
                                            <small>{{ __('All items here cannot be deleted.') }}</small>
                                            <div class="table-responsive mt-2">
                                                <table class="table mb-0 table-striped">
                                                    <tr>
                                                        <th data-width="40" class="text-dark">#</th>
                                                        <th class="text-dark">{{ __('Product') }}</th>
                                                        <th class="text-dark">{{ __('Quantity') }}</th>
                                                        <th class="text-dark">{{ __('Rate') }}</th>
                                                        <th class="text-dark">{{ __('Discount') }}</th>
                                                        <th class="text-dark">{{ __('Tax') }}</th>
                                                        <th class="text-dark">{{ __('Description') }}</th>
                                                        <th class="text-end text-dark" width="12%">
                                                            {{ __('Price') }}<br>
                                                            <small
                                                                class="text-danger font-weight-bold">{{ __('after tax & discount') }}</small>
                                                        </th>
                                                    </tr>
                                                    @php
                                                        $totalQuantity = 0;
                                                        $totalRate = 0;
                                                        $totalTaxPrice = 0;
                                                        $totalDiscount = 0;
                                                        $taxesData = [];
                                                    @endphp
                                                    @foreach ($iteams as $key => $iteam)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            @php
                                                                $productName = $iteam->product;
                                                                $totalRate += $iteam->price;
                                                                $totalQuantity += $iteam->quantity;
                                                                $totalDiscount += $iteam->discount;
                                                            @endphp
                                                            <td>{{ !empty($productName) ? $productName->name : '' }}
                                                            </td>
                                                            <td>{{ $iteam->quantity . ' (' . $productName->unit->name . ')' }}
                                                            </td>
                                                            <td>{{ \App\Models\Utility::priceFormat($settings, $iteam->price) }}
                                                            </td>
                                                            <td>{{ \App\Models\Utility::priceFormat($settings, $iteam->discount) }}
                                                            </td>
                                                            <td>
                                                                @if (!empty($iteam->tax))
                                                                    <table>
                                                                        @php
                                                                            $itemTaxes = [];
                                                                            $getTaxData = Utility::getTaxData();

                                                                            $itemTaxPrice = 0;

                                                                            if (!empty($iteam->tax)) {
                                                                                // tax ID গুলো explode ও sanitize করা
                                                                                $taxList = array_filter(explode(',', $iteam->tax));

                                                                                foreach ($taxList as $tax) {

                                                                                    // যদি tax ID getTaxData তে না থাকে → skip
                                                                                    if (!isset($getTaxData[$tax])) {
                                                                                        continue;
                                                                                    }

                                                                                    $taxPrice = \Utility::taxRate(
                                                                                        $getTaxData[$tax]['rate'],
                                                                                        $iteam->price,
                                                                                        $iteam->quantity
                                                                                    );

                                                                                    $itemTaxPrice += $taxPrice;
                                                                                    $totalTaxPrice += $taxPrice;

                                                                                    $itemTax['name']  = $getTaxData[$tax]['name'];
                                                                                    $itemTax['rate']  = $getTaxData[$tax]['rate'] . '%';
                                                                                    $itemTax['price'] = \App\Models\Utility::priceFormat($settings, $taxPrice);

                                                                                    $itemTaxes[] = $itemTax;

                                                                                    // total tax data aggregation
                                                                                    if (array_key_exists($getTaxData[$tax]['name'], $taxesData)) {
                                                                                        $taxesData[$getTaxData[$tax]['name']] += $taxPrice;
                                                                                    } else {
                                                                                        $taxesData[$getTaxData[$tax]['name']] = $taxPrice;
                                                                                    }
                                                                                }

                                                                                $iteam->itemTax = $itemTaxes;
                                                                            } else {
                                                                                $iteam->itemTax = [];
                                                                            }
                                                                        @endphp

                                                                        @foreach ($iteam->itemTax as $tax)
                                                                            <tr>
                                                                                <td>{{ $tax['name'] . ' (' . $tax['rate'] . ')' }}</td>
                                                                                <td>{{ $tax['price'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </table>
                                                                @else
                                                                    @php
                                                                        $itemTaxPrice = 0;
                                                                    @endphp
                                                                    -
                                                                @endif
                                                            </td>

                                                            <td>{{ !empty($iteam->description) ? $iteam->description : '-' }}
                                                            </td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $iteam->price * $iteam->quantity - $iteam->discount + $itemTaxPrice) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>

                                                            <td><b>{{ __('Total') }}</b></td>
                                                            <td><b>{{ $totalQuantity }}</b></td>
                                                            <td><b>{{ \App\Models\Utility::priceFormat($settings, $totalRate) }}</b>
                                                            </td>
                                                            <td><b>{{ \App\Models\Utility::priceFormat($settings, $totalDiscount) }}</b>
                                                            </td>
                                                            <td><b>{{ \App\Models\Utility::priceFormat($settings, $totalTaxPrice) }}</b>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-end"><b>{{ __('Sub Total') }}</b></td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->getSubTotal()) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-end"><b>{{ __('Discount') }}</b></td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->getTotalDiscount()) }}
                                                            </td>
                                                        </tr>

                                                        @if (!empty($taxesData))
                                                            @foreach ($taxesData as $taxName => $taxPrice)
                                                                <tr>
                                                                    <td colspan="6"></td>
                                                                    <td class="text-end"><b>{{ $taxName }}</b>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        {{ \App\Models\Utility::priceFormat($settings, $taxPrice) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="blue-text text-end"><b>{{ __('Total') }}</b>
                                                            </td>
                                                            <td class="blue-text text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->getTotal()) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-end"><b>{{ __('Paid') }}</b></td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->getTotal() - $invoice->getDue() - $invoice->invoiceTotalCreditNote()) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-end"><b>{{ __('Credit Note') }}</b></td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->invoiceTotalCreditNote()) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-end"><b>{{ __('Due') }}</b></td>
                                                            <td class="text-end">
                                                                {{ \App\Models\Utility::priceFormat($settings, $invoice->getDue()) }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Receipt Summary') }}</h5>
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th class="text-dark">{{ __('Date') }}</th>
                                        <th class="text-dark">{{ __('Amount') }}</th>
                                        <th class="text-dark">{{ __('Payment Type') }}</th>
                                        <th class="text-dark">{{ __('Account') }}</th>
                                        <th class="text-dark">{{ __('Reference') }}</th>
                                        <th class="text-dark">{{ __('Description') }}</th>
                                        <th class="text-dark">{{ __('Receipt') }}</th>
                                        <th class="text-dark">{{ __('OrderId') }}</th>
                                    </tr>
                                    @php
                                        $path = \App\Models\Utility::get_file('uploads/order');
                                    @endphp

                                    @if (!empty($invoice->payments) && $invoice->bankPayments)
                                        @foreach ($invoice->payments as $key => $payment)

                                            <tr>
                                                <td>{{ \App\Models\Utility::dateFormat($settings, $payment->date) }}
                                                </td>
                                                <td>{{ \App\Models\Utility::priceFormat($settings, $payment->amount) }}
                                                </td>
                                                <td>{{ $payment->payment_type }}</td>
                                                <td>{{ !empty($payment->bankAccount) ? $payment->bankAccount->bank_name . ' ' . $payment->bankAccount->holder_name : '--' }}
                                                </td>
                                                <td>{{ !empty($payment->reference) ? $payment->reference : '--' }}
                                                </td>
                                                <td>{{ !empty($payment->description) ? $payment->description : '--' }}
                                                </td>
                                                <td>
                                                    @if (!empty($payment->receipt))
                                                        <a href="{{ $path . '/' . $payment->receipt }}"
                                                            target="_blank">
                                                            <i class="ti ti-file"></i>{{ __('Receipt') }}</a>
                                                    @elseif(!empty($payment->add_receipt))
                                                        <a href="{{ asset(Storage::url('uploads/payment')) . '/' . $payment->add_receipt }}"
                                                            target="_blank">
                                                            <i class="ti ti-file"></i>{{ __('Receipt') }}</a>
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td>{{ !empty($payment->order_id) ? $payment->order_id : '--' }}</td>
                                            </tr>
                                        @endforeach

                                        @foreach ($invoice->bankPayments as $key => $bankPayment)
                                            <tr>
                                                <td>{{ \App\Models\Utility::dateFormat($settings, $bankPayment->date) }}
                                                </td>
                                                <td>{{ \App\Models\Utility::priceFormat($settings, $bankPayment->amount) }}
                                                </td>
                                                <td>{{ __('Bank Transfer') }}</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>
                                                    @if (!empty($bankPayment->receipt))
                                                        <a href="{{ $path . '/' . $bankPayment->receipt }}"
                                                            target="_blank">
                                                            <i class="ti ti-file"></i> {{ __('Receipt') }}
                                                        </a>
                                                    @endif

                                                </td>
                                                <td>{{ !empty($bankPayment->order_id) ? $bankPayment->order_id : '--' }}
                                                </td>
                                                @can('delete invoice product')
                                                    <td>
                                                        @if ($bankPayment->status == 'Pending')
                                                            <div class="action-btn">
                                                                <a href="#"
                                                                    data-url="{{ URL::to('invoice/' . $bankPayment->id . '/action') }}"
                                                                    data-size="lg" data-ajax-popup="true"
                                                                    data-title="{{ __('Payment Status') }}"
                                                                    class="mx-3 btn btn-sm align-items-center bg-warning"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ __('Payment Status') }}"
                                                                    data-original-title="{{ __('Payment Status') }}">
                                                                    <i class="ti ti-caret-right text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <div class="action-btn ms-2">
                                                            {!! Form::open([
                                                                'method' => 'post',
                                                                'route' => ['invoice.payment.destroy', $invoice->id, $bankPayment->id],
                                                                'id' => 'delete-form-' . $bankPayment->id,
                                                            ]) !!}

                                                            <a href="#"
                                                                class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                                data-bs-toggle="tooltip" title="Delete"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $bankPayment->id }}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                    </td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ Gate::check('delete invoice product') ? '9' : '8' }}"
                                                class="text-center text-dark">
                                                <p>{{ __('No Data Found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($invoice->getDue() > 0)

                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog"
                    aria-labelledby="paymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="card bg-none card-box">
                                    <section class="nav-tabs p-2">
                                        @if (
                                            (isset($company_payment_setting['is_stripe_enabled']) && $company_payment_setting['is_stripe_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_bank_transfer_enabled']) &&
                                                    $company_payment_setting['is_bank_transfer_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paypal_enabled']) &&
                                                    $company_payment_setting['is_paypal_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paystack_enabled']) &&
                                                    $company_payment_setting['is_paystack_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_flutterwave_enabled']) &&
                                                    $company_payment_setting['is_flutterwave_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_razorpay_enabled']) &&
                                                    $company_payment_setting['is_razorpay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_mercado_enabled']) &&
                                                    $company_payment_setting['is_mercado_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_mollie_enabled']) &&
                                                    $company_payment_setting['is_mollie_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_skrill_enabled']) &&
                                                    $company_payment_setting['is_skrill_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_coingate_enabled']) &&
                                                    $company_payment_setting['is_coingate_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paymentwall_enabled']) &&
                                                    $company_payment_setting['is_paymentwall_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_toyyibpay_enabled']) &&
                                                    $company_payment_setting['is_toyyibpay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_payfast_enabled']) &&
                                                    $company_payment_setting['is_payfast_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_iyzipay_enabled']) &&
                                                    $company_payment_setting['is_iyzipay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paytab_enabled']) &&
                                                    $company_payment_setting['is_paytab_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_benefit_enabled']) &&
                                                    $company_payment_setting['is_benefit_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_cashfree_enabled']) &&
                                                    $company_payment_setting['is_cashfree_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_aamarpay_enabled']) &&
                                                    $company_payment_setting['is_aamarpay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_yookassa_enabled']) &&
                                                    $company_payment_setting['is_yookassa_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_midtrans_enabled']) &&
                                                    $company_payment_setting['is_midtrans_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_nepalste_enabled']) &&
                                                    $company_payment_setting['is_nepalste_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_xendit_enabled']) &&
                                                    $company_payment_setting['is_xendit_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_paiementpro_enabled']) &&
                                                    $company_payment_setting['is_paiementpro_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_cinetpay_enabled']) &&
                                                    $company_payment_setting['is_cinetpay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_fedapay_enabled']) &&
                                                    $company_payment_setting['is_fedapay_enabled'] == 'on') ||
                                                (isset($company_payment_setting['is_payhere_enabled']) &&
                                                    $company_payment_setting['is_payhere_enabled'] == 'on') ||
                                                (isset($company_payment_setting['tap_payment_is_on']) &&
                                                    $company_payment_setting['tap_payment_is_on'] == 'on') ||
                                                (isset($company_payment_setting['authorizenet_payment_is_on']) &&
                                                    $company_payment_setting['authorizenet_payment_is_on'] == 'on') ||
                                                (isset($company_payment_setting['khalti_payment_is_on']) &&
                                                    $company_payment_setting['khalti_payment_is_on'] == 'on') ||
                                                (isset($company_payment_setting['easebuzz_payment_is_on']) &&
                                                    $company_payment_setting['easebuzz_payment_is_on'] == 'on') ||
                                                (isset($company_payment_setting['company_ozow_payment_is_enabled']) &&
                                                    $company_payment_setting['company_ozow_payment_is_enabled'] == 'on'))

                                            <ul class="nav nav-pills  mb-3" role="tablist">
                                                @if ($company_payment_setting['is_bank_transfer_enabled'] == 'on' && !empty($company_payment_setting['bank_details']))
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 active"
                                                            data-bs-toggle="tab" href="#bank-transfer-payment"
                                                            role="tab" aria-controls="bank"
                                                            aria-selected="true">{{ __('Bank Transfer') }}</a>
                                                    </li>
                                                @endif
                                                @if (
                                                    $company_payment_setting['is_stripe_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['stripe_key']) &&
                                                        !empty($company_payment_setting['stripe_secret']))
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm"me-1
                                                            data-bs-toggle="tab" href="#stripe-payment"
                                                            role="tab" aria-controls="stripe"
                                                            aria-selected="true">{{ __('Stripe') }}</a>
                                                    </li>
                                                @endif
                                                @if (
                                                    $company_payment_setting['is_paypal_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['paypal_client_id']) &&
                                                        !empty($company_payment_setting['paypal_secret_key']))
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paypal-payment"
                                                            role="tab" aria-controls="paypal"
                                                            aria-selected="false">{{ __('Paypal') }}</a>
                                                    </li>
                                                @endif

                                                @if (
                                                    $company_payment_setting['is_paystack_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['paystack_public_key']) &&
                                                        !empty($company_payment_setting['paystack_secret_key']))
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paystack-payment"
                                                            role="tab" aria-controls="paystack"
                                                            aria-selected="false">{{ __('Paystack') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_flutterwave_enabled']) &&
                                                        $company_payment_setting['is_flutterwave_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#flutterwave-payment"
                                                            role="tab" aria-controls="flutterwave"
                                                            aria-selected="false">{{ __('Flutterwave') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#razorpay-payment"
                                                            role="tab" aria-controls="razorpay"
                                                            aria-selected="false">{{ __('Razorpay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#mercado-payment"
                                                            role="tab" aria-controls="mercado"
                                                            aria-selected="false">{{ __('Mercado') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paytm-payment" role="tab"
                                                            aria-controls="paytm"
                                                            aria-selected="false">{{ __('Paytm') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#mollie-payment"
                                                            role="tab" aria-controls="mollie"
                                                            aria-selected="false">{{ __('Mollie') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#skrill-payment"
                                                            role="tab" aria-controls="skrill"
                                                            aria-selected="false">{{ __('Skrill') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#coingate-payment"
                                                            role="tab" aria-controls="coingate"
                                                            aria-selected="false">{{ __('Coingate') }}</a>
                                                    </li>
                                                @endif
                                                @if (
                                                    $company_payment_setting['is_paymentwall_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['paymentwall_public_key']) &&
                                                        !empty($company_payment_setting['paymentwall_private_key']))
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paymentwall-payment"
                                                            role="tab" aria-controls="paymentwall"
                                                            aria-selected="false">{{ __('PaymentWall') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#toyyibpay-payment"
                                                            role="tab" aria-controls="toyyibpay"
                                                            aria-selected="false">{{ __('Toyyibpay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_payfast_enabled']) && $company_payment_setting['is_payfast_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            onclick=get_payfast_status() data-bs-toggle="tab"
                                                            href="#payfast-payment" role="tab"
                                                            aria-controls="payfast"
                                                            aria-selected="false">{{ __('PayFast') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_iyzipay_enabled']) && $company_payment_setting['is_iyzipay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#iyzipay-payment"
                                                            role="tab" aria-controls="iyzipay"
                                                            aria-selected="false">{{ __('Iyzipay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#sspay-payment" role="tab"
                                                            aria-controls="sspay"
                                                            aria-selected="false">{{ __('SSpay') }}</a>
                                                    </li>
                                                @endif

                                                @if (isset($company_payment_setting['is_paytab_enabled']) && $company_payment_setting['is_paytab_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paytab-payment"
                                                            role="tab" aria-controls="paytab"
                                                            aria-selected="false">{{ __('PayTab') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_benefit_enabled']) && $company_payment_setting['is_benefit_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#benefit-payment"
                                                            role="tab" aria-controls="paytab"
                                                            aria-selected="false">{{ __('Benefit') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_cashfree_enabled']) && $company_payment_setting['is_cashfree_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1  ml-1"
                                                            data-bs-toggle="tab" href="#cashfree-payment"
                                                            role="tab" aria-controls="cashfree"
                                                            aria-selected="false">{{ __('Cashfree') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_aamarpay_enabled']) && $company_payment_setting['is_aamarpay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#aamarpay-payment"
                                                            role="tab" aria-controls="aamarpay"
                                                            aria-selected="false">{{ __('AamarPay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paytr-payment" role="tab"
                                                            aria-controls="paytr"
                                                            aria-selected="false">{{ __('PayTR') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_yookassa_enabled']) && $company_payment_setting['is_yookassa_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#yookassa-payment"
                                                            role="tab" aria-controls="yookassa"
                                                            aria-selected="false">{{ __('Yookassa') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_midtrans_enabled']) && $company_payment_setting['is_midtrans_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#midtrans-payment"
                                                            role="tab" aria-controls="midtrans"
                                                            aria-selected="false">{{ __('Midtrans') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_xendit_enabled']) && $company_payment_setting['is_xendit_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#xendit-payment"
                                                            role="tab" aria-controls="xendit"
                                                            aria-selected="false">{{ __('Xendit') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_nepalste_enabled']) && $company_payment_setting['is_nepalste_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#nepalste-payment"
                                                            role="tab" aria-controls="nepalste"
                                                            aria-selected="false">{{ __('Nepalste') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_paiementpro_enabled']) &&
                                                        $company_payment_setting['is_paiementpro_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#paiementpro-payment"
                                                            role="tab" aria-controls="paiementpro"
                                                            aria-selected="false">{{ __('Paiement Pro') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_cinetpay_enabled']) && $company_payment_setting['is_cinetpay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#cinetpay-payment"
                                                            role="tab" aria-controls="cinetpay"
                                                            aria-selected="false">{{ __('Cinetpay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_fedapay_enabled']) && $company_payment_setting['is_fedapay_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#fedapay-payment"
                                                            role="tab" aria-controls="fedapay"
                                                            aria-selected="false">{{ __('FedaPay') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['is_payhere_enabled']) && $company_payment_setting['is_payhere_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#payhere-payment"
                                                            role="tab" aria-controls="payhere"
                                                            aria-selected="false">{{ __('PayHere') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['tap_payment_is_on']) && $company_payment_setting['tap_payment_is_on'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#tap-payment" role="tab"
                                                            aria-controls="tap"
                                                            aria-selected="false">{{ __('Tap') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['authorizenet_payment_is_on']) &&
                                                        $company_payment_setting['authorizenet_payment_is_on'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#authorizenet-payment"
                                                            role="tab" aria-controls="tap"
                                                            aria-selected="false">{{ __('AuthorizeNet') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['khalti_payment_is_on']) && $company_payment_setting['khalti_payment_is_on'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#khalti-payment"
                                                            role="tab" aria-controls="tap"
                                                            aria-selected="false">{{ __('Khalti') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['easebuzz_payment_is_on']) && $company_payment_setting['easebuzz_payment_is_on'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#easebuzz-payment"
                                                            role="tab" aria-controls="tap"
                                                            aria-selected="false">{{ __('Easebuzz') }}</a>
                                                    </li>
                                                @endif
                                                @if (isset($company_payment_setting['company_ozow_payment_is_enabled']) && $company_payment_setting['company_ozow_payment_is_enabled'] == 'on')
                                                    <li class="nav-item mb-2">
                                                        <a class="btn btn-outline-primary btn-sm me-1 ml-1"
                                                            data-bs-toggle="tab" href="#ozow-payment"
                                                            role="tab" aria-controls="ozow"
                                                            aria-selected="false">{{ __('Ozow') }}</a>
                                                    </li>
                                                @endif

                                            </ul>
                                        @endif

                                        <div class="tab-content">
                                            @if (
                                                !empty($company_payment_setting) &&
                                                    ($company_payment_setting['is_bank_transfer_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['bank_details'])))
                                                <div class="tab-pane fade active show" id="bank-transfer-payment"
                                                    role="tabpanel" aria-labelledby="bank-transfer-payment">
                                                    <form class="w3-container w3-display-middle w3-card-4 needs-validation"
                                                        method="POST" enctype="multipart/form-data"
                                                        action="{{ route('customer.pay.with.bank') }}" novalidate>

                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="row">
                                                            <div class="col-6 ">
                                                                <div class="custom-radio">
                                                                    <label
                                                                        class="font-16 font-bold">{{ __('Bank Details') }}
                                                                        :</label>
                                                                </div>
                                                                <p class="mb-0 pt-1 text-sm">
                                                                    {!! $company_payment_setting['bank_details'] !!}
                                                                </p>
                                                            </div>
                                                            <div class="col-6">
                                                                {{ Form::label('payment_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
                                                                <div class="choose-file form-group">
                                                                    <input type="file" name="payment_receipt"
                                                                        id="image" class="form-control">
                                                                    <p class="upload_file"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="form-group col-md-12">
                                                                <label for="amount">{{ __('Amount') }}</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-prepend"><span
                                                                            class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                    <input class="form-control" required="required"
                                                                        min="0" name="amount" type="number"
                                                                        value="{{ $invoice->getDue() }}"
                                                                        min="0" step="0.01"
                                                                        max="{{ $invoice->getDue() }}"
                                                                        id="amount">
                                                                    @error('amount')
                                                                        <span class="invalid-amount" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (
                                                !empty($company_payment_setting) &&
                                                    ($company_payment_setting['is_stripe_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['stripe_key']) &&
                                                        !empty($company_payment_setting['stripe_secret'])))
                                                <div class="tab-pane fade" id="stripe-payment" role="tabpanel"
                                                    aria-labelledby="stripe-payment">
                                                    <form method="post"
                                                        action="{{ route('customer.payment', $invoice->id) }}"
                                                        class="require-validation" id="payment-form">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-sm-8">
                                                                <div class="custom-radio">
                                                                    <label
                                                                        class="font-16 font-weight-bold">{{ __('Credit / Debit Card') }}</label>
                                                                </div>
                                                                <p class="mb-0 pt-1 text-sm">
                                                                    {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                                </p>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="card-name-on">{{ __('Name on card') }}</label>
                                                                    <input type="text" name="name"
                                                                        id="card-name-on"
                                                                        class="form-control required"  placeholder="{{ __('Enter Name') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div id="card-element">
                                                                    <div id="card-errors" role="alert"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <br>
                                                                <label for="amount">{{ __('Amount') }}</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-prepend"><span
                                                                            class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                    <input class="form-control" required="required"
                                                                        min="0" name="amount" type="number"
                                                                        value="{{ $invoice->getDue() }}"
                                                                        min="0" step="0.01"
                                                                        max="{{ $invoice->getDue() }}"
                                                                        id="amount" placeholder="{{ __('Enter Amount') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        {{ __('Please correct the errors and try again.') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (
                                                !empty($company_payment_setting) &&
                                                    ($company_payment_setting['is_paypal_enabled'] == 'on' &&
                                                        !empty($company_payment_setting['paypal_client_id']) &&
                                                        !empty($company_payment_setting['paypal_secret_key'])))
                                                <div class="tab-pane fade " id="paypal-payment" role="tabpanel"
                                                    aria-labelledby="paypal-payment">
                                                    <form class="w3-container w3-display-middle w3-card-4 "
                                                        method="POST" id="payment-form"
                                                        action="{{ route('customer.pay.with.paypal', $invoice->id) }}">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <label for="amount">{{ __('Amount') }}</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-prepend"><span
                                                                            class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                    <input class="form-control" required="required"
                                                                        min="0" name="amount" type="number"
                                                                        value="{{ $invoice->getDue() }}"
                                                                        min="0" step="0.01"
                                                                        max="{{ $invoice->getDue() }}"
                                                                        id="amount" placeholder="{{ __('Enter Amount') }}">
                                                                    @error('amount')
                                                                        <span class="invalid-amount" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_paystack_enabled']) &&
                                                    $company_payment_setting['is_paystack_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paystack_public_key']) &&
                                                    !empty($company_payment_setting['paystack_secret_key']))
                                                <div class="tab-pane fade " id="paystack-payment" role="tabpanel"
                                                    aria-labelledby="paypal-payment">
                                                    <form class="w3-container w3-display-middle w3-card-4"
                                                        method="POST" id="paystack-payment-form"
                                                        action="{{ route('customer.pay.with.paystack') }}">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paystack"
                                                                type="button">{{ __('Make Payment') }}</button>

                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_flutterwave_enabled']) &&
                                                    $company_payment_setting['is_flutterwave_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paystack_public_key']) &&
                                                    !empty($company_payment_setting['paystack_secret_key']))
                                                <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel"
                                                    aria-labelledby="paypal-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.flaterwave') }}"
                                                        method="post" class="require-validation"
                                                        id="flaterwave-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_flaterwave"
                                                                type="button">{{ __('Make Payment') }}</button>

                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on')
                                                <div class="tab-pane fade " id="razorpay-payment" role="tabpanel"
                                                    aria-labelledby="paypal-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.razorpay') }}"
                                                        method="post" class="require-validation"
                                                        id="razorpay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_razorpay"
                                                                type="button">{{ __('Make Payment') }}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on')



                                                <div class="tab-pane fade " id="mercado-payment" role="tabpanel"
                                                    aria-labelledby="mercado-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.mercado') }}"
                                                        method="post" class="require-validation"
                                                        id="mercado-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_mercado"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on')
                                                <div class="tab-pane fade" id="paytm-payment" role="tabpanel"
                                                    aria-labelledby="paytm-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.paytm') }}"
                                                        method="post" class="require-validation"
                                                        id="paytm-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="flaterwave_coupon"
                                                                    class=" text-dark">{{ __('Mobile Number') }}</label>
                                                                <input type="text" id="mobile" name="mobile"
                                                                    class="form-control mobile" data-from="mobile"
                                                                    placeholder="{{ __('Enter Mobile Number') }}"
                                                                    required placeholder="{{ __('Enter Mobile Number') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">company_setting
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paytm"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on')
                                                <div class="tab-pane fade " id="mollie-payment" role="tabpanel"
                                                    aria-labelledby="mollie-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.mollie') }}"
                                                        method="post" class="require-validation"
                                                        id="mollie-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_mollie"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on')
                                                <div class="tab-pane fade " id="skrill-payment" role="tabpanel"
                                                    aria-labelledby="skrill-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.skrill') }}"
                                                        method="post" class="require-validation"
                                                        id="skrill-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        @php
                                                            $skrill_data = [
                                                                'transaction_id' => md5(
                                                                    date('Y-m-d') .
                                                                        strtotime('Y-m-d H:i:s') .
                                                                        'user_id',
                                                                ),
                                                                'user_id' => 'user_id',
                                                                'amount' => 'amount',
                                                                'currency' => 'currency',
                                                            ];
                                                            session()->put('skrill_data', $skrill_data);

                                                        @endphp
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_skrill"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on')
                                                <div class="tab-pane fade " id="coingate-payment" role="tabpanel"
                                                    aria-labelledby="coingate-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.coingate') }}"
                                                        method="post" class="require-validation"
                                                        id="coingate-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">

                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_coingate"
                                                                type="submit">{{ __('Make Payment') }}</button>

                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                            @if (
                                                !empty($company_payment_setting) &&
                                                    isset($company_payment_setting['is_paymentwall_enabled']) &&
                                                    $company_payment_setting['is_paymentwall_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paymentwall_public_key']) &&
                                                    !empty($company_payment_setting['paymentwall_private_key']))
                                                <div class="tab-pane fade " id="paymentwall-payment" role="tabpanel"
                                                    aria-labelledby="paypal-payment">
                                                    <form class="w3-container w3-display-middle w3-card-4"
                                                        method="POST" id="paymentwall-payment-form"
                                                        action="{{ route('invoice.paymentwallpayment') }}">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend">

                                                                    <span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span>
                                                                </span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>

                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_coingate"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on')
                                                <div class="tab-pane fade " id="toyyibpay-payment" role="tabpanel"
                                                    aria-labelledby="toyyibpay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.toyyibpay') }}"
                                                        method="post" class="require-validation"
                                                        id="toyyibpay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_toyyibpay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (
                                                !empty($company_payment_setting) &&
                                                    isset($company_payment_setting['is_payfast_enabled']) &&
                                                    $company_payment_setting['is_payfast_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['is_payfast_enabled']) &&
                                                    !empty($company_payment_setting['is_payfast_enabled']))
                                                <div class="tab-pane fade " id="payfast-payment" role="tabpanel"
                                                    aria-labelledby="payfast-payment">
                                                    @php
                                                        $pfHost =
                                                            $company_payment_setting['payfast_mode'] == 'sandbox'
                                                                ? 'sandbox.payfast.co.za'
                                                                : 'www.payfast.co.za';
                                                    @endphp
                                                    <form role="form"
                                                        action={{ 'https://' . $pfHost . '/eng/process' }}
                                                        method="post" id="payfast-payment-form">
                                                        @csrf
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="pay_fast_amount" onchange=get_payfast_status() placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div id="get-payfast-inputs"></div>
                                                        <div class="form-group mt-3">
                                                            <input type="hidden" name="invoice_id" id="invoice_id"
                                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_payfast"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_iyzipay_enabled']) && $company_payment_setting['is_iyzipay_enabled'] == 'on')
                                                <div class="tab-pane fade" id="iyzipay-payment" role="tabpanel"
                                                    aria-labelledby="iyzipay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.iyzipay') }}"
                                                        method="post" class="require-validation"
                                                        id="iyzipay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_toyyibpay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on')
                                                <div class="tab-pane fade" id="sspay-payment" role="tabpanel"
                                                    aria-labelledby="sspay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.sspay') }}"
                                                        method="post" class="require-validation"
                                                        id="sspay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_sspay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            @if (isset($company_payment_setting['is_paytab_enabled']) && $company_payment_setting['is_paytab_enabled'] == 'on')
                                                <div class="tab-pane fade" id="paytab-payment" role="tabpanel"
                                                    aria-labelledby="paytab-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.paytab') }}"
                                                        method="post" class="require-validation"
                                                        id="paytab-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paytab"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_benefit_enabled']) && $company_payment_setting['is_benefit_enabled'] == 'on')
                                                <div class="tab-pane fade" id="benefit-payment" role="tabpanel"
                                                    aria-labelledby="benefit-payment">
                                                    <form role="form"
                                                        action="{{ route('invoice.benefit.initiate') }}"
                                                        method="post" class="require-validation"
                                                        id="benefit-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paytab"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_cashfree_enabled']) && $company_payment_setting['is_cashfree_enabled'] == 'on')
                                                <div class="tab-pane fade" id="cashfree-payment" role="tabpanel"
                                                    aria-labelledby="cashfree-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.cashfree') }}"
                                                        method="post" class="require-validation"
                                                        id="cashfree-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paytab"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_aamarpay_enabled']) && $company_payment_setting['is_aamarpay_enabled'] == 'on')
                                                <div class="tab-pane fade" id="aamarpay-payment" role="tabpanel"
                                                    aria-labelledby="aamarpay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.aamarpay') }}"
                                                        method="post" class="require-validation"
                                                        id="aamarpay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_aamarpay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on')
                                                <div class="tab-pane fade" id="paytr-payment" role="tabpanel"
                                                    aria-labelledby="paytr-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.pay.with.paytr') }}"
                                                        method="post" class="require-validation"
                                                        id="paytr-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paytr"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            @if (isset($company_payment_setting['is_yookassa_enabled']) && $company_payment_setting['is_yookassa_enabled'] == 'on')
                                                <div class="tab-pane fade" id="yookassa-payment" role="tabpanel"
                                                    aria-labelledby="yookassa-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.yookassa') }}"
                                                        method="post" class="require-validation"
                                                        id="yookassa-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_yookassa"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            @if (isset($company_payment_setting['is_midtrans_enabled']) && $company_payment_setting['is_midtrans_enabled'] == 'on')
                                                <div class="tab-pane fade" id="midtrans-payment" role="tabpanel"
                                                    aria-labelledby="midtrans-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.midtrans') }}"
                                                        method="post" class="require-validation"
                                                        id="midtrans-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_midtrans"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_xendit_enabled']) && $company_payment_setting['is_xendit_enabled'] == 'on')
                                                <div class="tab-pane fade" id="xendit-payment" role="tabpanel"
                                                    aria-labelledby="xendit-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.xendit') }}"
                                                        method="post" class="require-validation"
                                                        id="xendit-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_xendit"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_nepalste_enabled']) && $company_payment_setting['is_nepalste_enabled'] == 'on')
                                                <div class="tab-pane fade" id="nepalste-payment" role="tabpanel"
                                                    aria-labelledby="nepalste-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.nepalste') }}"
                                                        method="post" class="require-validation"
                                                        id="nepalste-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_nepalste"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif


                                            @if (isset($company_payment_setting['is_paiementpro_enabled']) &&
                                                    $company_payment_setting['is_paiementpro_enabled'] == 'on')
                                                <div class="tab-pane fade" id="paiementpro-payment"
                                                    role="tabpanel" aria-labelledby="paiementpro-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.paiementpro') }}"
                                                        method="post" class="require-validation"
                                                        id="paiementpro-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="paiementpro_mobile_number"
                                                                        class="form-control-label text-dark">{{ __('Mobile Number') }}</label>
                                                                    <input type="text"
                                                                        id="paiementpro_mobile_number"
                                                                        name="mobile_number"
                                                                        class="form-control mobile_number"
                                                                        data-from="paiementpro"
                                                                        placeholder="{{ __('Enter Mobile Number') }}"
                                                                        required placeholder="{{ __('Enter Mobile Number') }}">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="paiementpro_channel"
                                                                        class="form-control-label text-dark">{{ __('Channel') }}</label>
                                                                    <input type="text" id="paiementpro_channel"
                                                                        name="channel" class="form-control channel"
                                                                        data-from="paiementpro"
                                                                        placeholder="{{ __('Enter Channel') }}"
                                                                        required>
                                                                    <small class="text-danger">Example : OMCIV2 , MOMO
                                                                        , CARD , FLOOZ , PAYPAL</small>
                                                                </div>
                                                            </div>
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_paiementpro"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_cinetpay_enabled']) && $company_payment_setting['is_cinetpay_enabled'] == 'on')
                                                <div class="tab-pane fade" id="cinetpay-payment" role="tabpanel"
                                                    aria-labelledby="cinetpay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.cinetpay') }}"
                                                        method="post" class="require-validation"
                                                        id="cinetpay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_cinetpay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_fedapay_enabled']) && $company_payment_setting['is_fedapay_enabled'] == 'on')
                                                <div class="tab-pane fade" id="fedapay-payment" role="tabpanel"
                                                    aria-labelledby="fedapay-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.fedapay') }}"
                                                        method="post" class="require-validation"
                                                        id="fedapay-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_fedapay"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['is_payhere_enabled']) && $company_payment_setting['is_payhere_enabled'] == 'on')
                                                <div class="tab-pane fade" id="payhere-payment" role="tabpanel"
                                                    aria-labelledby="payhere-payment">
                                                    <form role="form"
                                                        action="{{ route('customer.with.payhere') }}"
                                                        method="post" class="require-validation"
                                                        id="payhere-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_payhere"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['tap_payment_is_on']) && $company_payment_setting['tap_payment_is_on'] == 'on')
                                                <div class="tab-pane fade" id="tap-payment" role="tabpanel"
                                                    aria-labelledby="tap-payment">
                                                    <form role="form"
                                                        action="{{ route('invoice.pay.with.tap') }}"
                                                        method="post" class="require-validation"
                                                        id="tap-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}"
                                                                    min="0" step="0.01"
                                                                    max="{{ $invoice->getDue() }}" id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_tap"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['authorizenet_payment_is_on']) && $company_payment_setting['authorizenet_payment_is_on'] == 'on')
                                                <div class="tab-pane fade" id="authorizenet-payment" role="tabpanel"
                                                    aria-labelledby="authorizenet-payment">
                                                    <form role="form"
                                                        action="{{ route('invoice.pay.with.authorizenet') }}"
                                                        method="post" class="require-validation"
                                                        id="authorizenet-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_authorizenet"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['khalti_payment_is_on']) && $company_payment_setting['khalti_payment_is_on'] == 'on')
                                                <div class="tab-pane fade" id="khalti-payment" role="tabpanel"
                                                    aria-labelledby="khalti-payment">
                                                    @csrf
                                                        <input type="hidden" name="invoice_id" id="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control amount" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_khalti"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['easebuzz_payment_is_on']) && $company_payment_setting['easebuzz_payment_is_on'] == 'on')
                                                <div class="tab-pane fade" id="easebuzz-payment" role="tabpanel"
                                                    aria-labelledby="easebuzz-payment">
                                                    <form role="form"
                                                        action="{{ route('invoice.pay.with.easebuzz') }}"
                                                        method="post" class="require-validation"
                                                        id="easebuzz-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_easebuzz"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if (isset($company_payment_setting['company_ozow_payment_is_enabled']) && $company_payment_setting['company_ozow_payment_is_enabled'] == 'on')
                                                <div class="tab-pane fade" id="ozow-payment" role="tabpanel"
                                                    aria-labelledby="ozow-payment">
                                                    <form role="form"
                                                        action="{{ route('invoice.pay.with.ozow') }}"
                                                        method="post" class="require-validation"
                                                        id="ozow-payment-form">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ $settings['site_currency'] }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount" placeholder="{{ __('Enter Amount') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button class="btn btn-primary" name="submit"
                                                                id="pay_with_easebuzz"
                                                                type="submit">{{ __('Make Payment') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
            <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"> </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
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


        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
            integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
        </script>

<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

<script type="text/javascript">
    @if (
        $invoice->status != 0 &&
            $invoice->getDue() > 0 &&
            !empty($company_payment_setting) &&
            $company_payment_setting['is_stripe_enabled'] == 'on' &&
            !empty($company_payment_setting['stripe_key']) &&
            !empty($company_payment_setting['stripe_secret']))
        var stripe = Stripe('{{ $company_payment_setting['stripe_key'] }}');
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '14px',
                color: '#32325d',
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {
            style: style
        });

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                    show_toastr('error', result.error.message, 'error');
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    @endif

    @if (isset($company_payment_setting['paystack_public_key']))
    $(document).on("click", "#pay_with_paystack", function() {
        var account = "{{$paystackAccount}}";
                if(account == '')
                {
                    show_toastr('Error', '{{ __("Bank account not connected with Paystack.") }}', 'error')
                    return;
                }
            $('#paystack-payment-form').ajaxForm(function(res) {
                var amount = res.total_price;
                if (res.flag == 1) {
                    var paystack_callback = "{{ url('/customer/paystack') }}";

                    var handler = PaystackPop.setup({
                        key: '{{ $company_payment_setting['paystack_public_key'] }}',
                        email: res.email,
                        amount: res.total_price * 100,
                        currency: res.currency,
                        ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                            1
                        ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                        metadata: {
                            custom_fields: [{
                                display_name: "Email",
                                variable_name: "email",
                                value: res.email,
                            }]
                        },

                        callback: function(response) {

                            window.location.href = paystack_callback + '/' + response
                                .reference + '/' + '{{ encrypt($invoice->id) }}' +
                                '?amount=' + amount;
                        },
                        onClose: function() {
                        }
                    });
                    handler.openIframe();
                } else if (res.flag == 2) {
                    toastrs('Error', res.msg, 'msg');
                } else {
                    toastrs('Error', res.message, 'msg');
                }

            }).submit();
        });
    @endif

    @if (isset($company_payment_setting['flutterwave_public_key']))
        //    Flaterwave Payment
        $(document).on("click", "#pay_with_flaterwave", function() {
            var account = "{{$flutterwaveAccount}}";
            if(account == '')
            {
                show_toastr('Error', '{{ __("Bank account not connected with Flutterwave.") }}', 'error')
                return;
            }

            $('#flaterwave-payment-form').ajaxForm(function(res) {

                if (res.flag == 1) {
                    var amount = res.total_price;
                    var API_publicKey = '{{ $company_payment_setting['flutterwave_public_key'] }}';
                    var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                    var flutter_callback = "{{ url('/customer/flaterwave') }}";
                    var x = getpaidSetup({
                        PBFPubKey: API_publicKey,
                        customer_email: '{{ $user->email }}',
                        amount: res.total_price,
                        currency: '{{ $settings['site_currency'] }}',
                        txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                            'fluttpay_online-' + '{{ date('Y-m-d') }}',
                        meta: [{
                            metaname: "payment_id",
                            metavalue: "id"
                        }],
                        onclose: function() {},
                        callback: function(response) {
                            var txref = response.tx.txRef;
                            if (
                                response.tx.chargeResponseCode == "00" ||
                                response.tx.chargeResponseCode == "0"
                            ) {
                                window.location.href = flutter_callback + '/' + txref +
                                    '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }} ?amount=' +
                                    amount;
                            } else {
                                // redirect to a failure page.
                            }
                            x
                                .close(); // use this to close the modal immediately after payment.
                        }
                    });
                } else if (res.flag == 2) {
                    toastrs('Error', res.msg, 'msg');
                } else {
                    toastrs('Error', data.message, 'msg');
                }

            }).submit();
        });
    @endif

    @if (isset($company_payment_setting['razorpay_public_key']))
        // Razorpay Payment
        $(document).on("click", "#pay_with_razorpay", function() {
            var account = "{{$razorpayAccount}}";Add commentMore actions
            if(account == '')
            {
                show_toastr('Error', '{{ __("Bank account not connected with Razorpay.") }}', 'error')
                return;
            }

            $('#razorpay-payment-form').ajaxForm(function(res) {
                if (res.flag == 1) {
                    var amount = res.total_price;
                    var razorPay_callback = '{{ url('/customer/razorpay') }}';
                    var totalAmount = res.total_price * 100;
                    var coupon_id = res.coupon;
                    var options = {
                        "key": "{{ $company_payment_setting['razorpay_public_key'] }}", // your Razorpay Key Id
                        "amount": totalAmount,
                        "name": 'Invoice',
                        "currency": '{{ $settings['site_currency'] }}',
                        "description": "",
                        "handler": function(response) {
                            window.location.href = razorPay_callback + '/' + response
                                .razorpay_payment_id + '/' +
                                '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' +
                                '?amount=' + amount;
                        },
                        "theme": {
                            "color": "#528FF0"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                } else if (res.flag == 2) {
                    toastrs('Error', res.msg, 'msg');
                } else {
                    toastrs('Error', data.message, 'msg');
                }

            }).submit();
        });
    @endif

    //start payfast payment

    @if (isset($company_payment_setting['is_payfast_enabled']) &&
            $company_payment_setting['is_payfast_enabled'] == 'on' &&
            !empty($company_payment_setting['payfast_merchant_id']) &&
            !empty($company_payment_setting['payfast_merchant_key']))
        function get_payfast_status() {
            var invoice_id = $('#invoice_id').val();
            var amount = $('#pay_fast_amount').val();

            $.ajax({
                url: '{{ route('invoice.with.payfast') }}',
                method: 'POST',
                data: {
                    'invoice_id': invoice_id,
                    'amount': amount,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success == true) {
                        $('#get-payfast-inputs').append(data.inputs);

                    } else {
                        show_toastr('Error', data.inputs, 'error')
                    }
                }
            });
        }
    @endif

    @if (isset($company_payment_setting['is_payfast_enabled']) && $company_payment_setting['is_payfast_enabled'] == 'on')
        function get_payfast_status() {
            var invoice_id = $('#invoice_id').val();
            var amount = $('#pay_fast_amount').val();

            $.ajax({
                url: '{{ route('invoice.with.payfast') }}',
                method: 'POST',
                data: {
                    'invoice_id': invoice_id,
                    'amount': amount,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {

                    if (data.success == true) {
                        $('#get-payfast-inputs').append(data.inputs);

                    } else {
                        show_toastr('Error', data.inputs, 'error')
                    }
                }
            });
        }
    @endif

    //end payfast payment
</script>

<script>

var config = {
    "publicKey": "{{ isset($company_payment_setting['khalti_public_key']) ? $company_payment_setting['khalti_public_key'] : '' }}",
    "productIdentity": "1234567890",
    "productName": "demo",
    "productUrl": "{{env('APP_URL')}}",
    "paymentPreference": [
        "KHALTI",
        "EBANKING",
        "MOBILE_BANKING",
        "CONNECT_IPS",
        "SCT",
    ],
    "eventHandler": {
        onSuccess (payload) {
            if(payload.status==200) {
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-Token': '{{csrf_token()}}'
                        }
                    });
                $.ajax({
                    url: '{{ route('invoice.pay.with.khalti') }}',
                    method: 'POST',
                    data : {
                        'payload' : payload,
                        'invoice_id' : $('#invoice_id').val(),
                        'amount' : $('.amount').val(),
                    },
                    beforeSend: function () {
                        $(".loader-wrapper").removeClass('d-none');
                    },
                    success: function(data) {
                        $(".loader-wrapper").addClass('d-none');
                        if(data.status_code === 200){
                            show_toastr('Success','Payment Done Successfully', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 100);
                        }
                        else{
                            show_toastr('Error','Payment Failed', 'error');
                        }
                    },
                    error: function(err) {
                        show_toastr('Error', err.response, 'error')
                    },
                });
            }
        },
        onError (error) {
            show_toastr('Error', error, 'error')
        },
        onClose () {
        }
    }

};

    $(document).on('click', '#pay_with_khalti', function () {
        var account = "{{$khaltiAccount}}";
        if(account == '')
        {
            show_toastr('Error', '{{ __("Bank account not connected with Khalti.") }}', 'error')
            return;
        }
        var checkout = new KhaltiCheckout(config);
            var btn = document.getElementById("pay_with_khalti");
            btn.onclick = function () {
                let price =  $('.amount').val()*100;
                checkout.show({amount: price});
        }
    });
</script>



        @if ($settings['enable_cookie'] == 'on')
            @include('layouts.cookie_consent')
        @endif

    </body>

    </html>
