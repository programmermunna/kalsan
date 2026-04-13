@php
    $settings_data = \App\Models\Utility::settingsById($invoice->created_by);
    // Get airline/vendor information
    $airline = null;
    if (!empty($invoice->vender_id)) {
        $airline = \App\Models\Vender::find($invoice->vender_id);
    }

    // Resolve human-readable Origin / Destination.
    // If stored as numeric ID, try to map to ProductService name; otherwise use raw text.
    $originName = null;
    if (!empty($invoice->origin)) {
        if (is_numeric($invoice->origin)) {
            $originName = optional(\App\Models\ProductService::find($invoice->origin))->name ?? $invoice->origin;
        } else {
            $originName = $invoice->origin;
        }
    }

    $destinationName = null;
    if (!empty($invoice->destination)) {
        if (is_numeric($invoice->destination)) {
            $destinationName = optional(\App\Models\ProductService::find($invoice->destination))->name ?? $invoice->destination;
        } else {
            $destinationName = $invoice->destination;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{$settings_data['SITE_RTL'] == 'on' ? 'rtl' : ''}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">

    <style type="text/css">
        :root {
            --theme-color:
                {{$color}}
            ;
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: 'Lato', sans-serif;
        }

        p,
        li,
        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            padding: 0.75rem;
            text-align: left;
        }

        table tr td {
            padding: 0.75rem;
            text-align: left;
        }

        table th small {
            display: block;
            font-size: 12px;
        }

        .invoice-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .invoice-logo {
            max-width: 200px;
            width: 100%;
        }

        .invoice-header table td {
            padding: 10px 20px;
        }

        .company-details-center {
            text-align: center;
        }

        .wavy-line {
            border-top: 2px solid var(--theme-color);
            margin: 10px 0;
            height: 0;
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .no-space tr td {
            padding: 0;
            white-space: nowrap;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 70px !important;
            width: 70px !important;
            height: 70px !important;
            margin-left: auto;
            margin-top: 5px;
            background: var(--white);
            padding: 5px;
            border-radius: 8px;
            display: inline-block;
            overflow: hidden;
        }

        .view-qrcode img,
        .view-qrcode svg {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain;
        }

        .invoice-body {
            padding: 15px 25px 0;
        }

        .invoice-summary {
            border: 1px solid #ddd;
        }

        .invoice-summary th,
        .invoice-summary td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .invoice-summary thead th {
            background:
                {{$color}}
            ;
            color:
                {{$font_color}}
            ;
        }



        table.add-border tr {
            border-top: 1px solid var(--theme-color);
        }

        tfoot tr:first-of-type {
            border-bottom: 1px solid var(--theme-color);
        }

        .total-table tr:first-of-type td {
            padding-top: 0;
        }

        .total-table tr:first-of-type {
            border-top: 0;
        }

        .sub-total {
            padding-right: 0;
            padding-left: 0;
        }

        .border-0 {
            border: none !important;
        }

        .invoice-summary td,
        .invoice-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .invoice-footer {
            padding: 15px 20px;
            text-align: right;
        }

        .invoice-actions {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }

        .invoice-actions button {
            margin: 0 10px;
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background: var(--theme-color);
            color: var(--white);
            font-weight: bold;
        }

        .invoice-actions button:hover {
            opacity: 0.9;
        }

        .invoice-actions .btn-print {
            background: #28a745;
        }

        .invoice-actions .btn-download {
            background: #007bff;
        }

        .itm-description td {
            padding-top: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        html[dir="rtl"] .view-qrcode {
            margin-left: 0;
            margin-right: auto;
        }

        @media print {
            .invoice-actions {
                display: none !important;
            }
        }

        @media (max-width: 426px) {

            .invoice-summary td,
            .invoice-summary th {
                font-size: 10px;
                padding: 5px
            }

            .no-space tr td {
                font-size: 10px
            }

            .invoice-header table td {
                padding: 15px 10px
            }

            .company-detail {
                font-size: 10px
            }

            .invoice-actions {
                padding: 10px;
            }

            .invoice-actions button {
                margin: 5px;
                padding: 8px 20px;
                font-size: 14px;
            }


            body {
                font-family: 'DejaVu Sans', 'Arial', sans-serif;
                margin: 0;
                padding: 40px;
                background: white;
            }

            .certificate {
                border: 10px double #333;
                padding: 40px;
                min-height: 600px;
                position: relative;
            }

            .header {
                text-align: center;
                margin-bottom: 40px;
            }

            .header h1 {
                font-size: 36px;
                text-transform: uppercase;
                margin: 0;
                letter-spacing: 4px;
            }

            .header h2 {
                font-size: 24px;
                color: #666;
                margin-top: 10px;
            }

            .grade {
                text-align: center;
                margin: 30px 0;
            }

            .grade .badge {
                font-size: 48px;
                font-weight: bold;
                background: #28a745;
                color: white;
                padding: 15px 40px;
                display: inline-block;
                border-radius: 10px;
            }

            .student-name {
                text-align: center;
                margin: 40px 0;
            }

            .student-name h3 {
                font-size: 28px;
                text-transform: uppercase;
                font-weight: bold;
                margin: 0;
            }

            .student-name p {
                font-size: 16px;
                margin-top: 5px;
            }

            .certify-text {
                text-align: center;
                margin: 50px 0;
                font-size: 16px;
            }

            .certify-text p {
                margin: 10px 0;
            }

            .certify-text .lead {
                font-size: 18px;
                font-style: italic;
                line-height: 1.6;
            }

            .footer {
                margin-top: 80px;
                display: flex;
                justify-content: space-between;
            }

            .signature {
                width: 45%;
                text-align: center;
            }

            .signature hr {
                margin: 20px 0 10px;
                border: none;
                border-top: 1px solid #000;
            }

            .signature p {
                margin: 5px 0;
            }

            @page {
                margin: 0;
            }

        }
    </style>

    @if($settings_data['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body class="">
    @if(!isset($preview))
        <div class="invoice-actions">
            <button type="button" class="btn-print" onclick="printInvoice()">
                {{__('Print')}}
            </button>
            <button type="button" class="btn-download" onclick="downloadInvoice()">
                {{__('Download')}}
            </button>
        </div>
    @endif
    <div class="invoice-preview-main" id="boxes">
        <div class="invoice-header" style="background: {{$color}};color:{{$font_color}}">
            <!-- Logo Section -->
            <div style="text-align: center; padding: 20px 0 5px 0;">
                <img class="invoice-logo" src="{{$img}}" alt=""
                    style="width: auto; height: auto; max-width: 550px; max-height: none; margin-top: 15px;">
                <p style="border-color: #007bff; border-width: 2px;">
                    _______________________________________________________________________________________________________
                </p>
            </div>

            <!-- Wavy Line -->
            <div class="wavy-line"></div>


            <!-- Company Details - Centered -->

            <!-- Bill To and Invoice Details Section -->
            <table class="vertical-align-top" style="width: 100%; margin-top: 10px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top;">
                            <table class="no-space" style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong
                                                style="margin-bottom: 10px; display:block;">{{__('Student Information')}}:</strong>
                                            @if(!empty($customer->name))
                                                <p>
                                                    <b> {{!empty($customer->name) ? $customer->name : ''}}</b><br>
                                                </p>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td><b>{{__('Mother Name')}}: <br> Magaca Hooyo</b></td>
                                                    <td>{{!empty($customer->mother_name) ? $customer->mother_name : '-'}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('gender')}}:</b></td>
                                                    <td>{{!empty($customer->gender) ? $customer->gender : '-'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('Place of Birth')}}:</b></td>
                                                    <td>{{!empty($customer->pob) ? $customer->pob : '-'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('Date of Birth')}}:</b></td>
                                                    <td>{{ !empty($customer->dob) ? $customer->dob : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('Address')}}:</b></td>
                                                    <td>{{ !empty($customer->billing_address) ? $customer->billing_address : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('City')}}:</b></td>
                                                    <td>{{ !empty($customer->billing_city) ? $customer->billing_city : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{__('Country')}}:</b></td>
                                                    <td>{{ !empty($customer->billing_country) ? $customer->billing_country : '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top;">
                            <table class="no-space" style="width: auto; margin-left: auto;">
                                <tbody>
                                    <tr>
                                        <td><strong
                                                style="margin-bottom: 10px; display:block;">{{__('License Details')}}:</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('License No')}}:</b></td>
                                        <td class="text-right">
                                            {{Utility::invoiceNumberFormat($settings, $invoice->invoice_id)}}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('Issue Date')}}:</b></td>
                                        <td class="text-right">{{Utility::dateFormat($settings, $invoice->issue_date)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('Due Date')}}:</b></td>
                                        <td class="text-right">{{Utility::dateFormat($settings, $invoice->due_date)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('Document')}}:</b></td>
                                        <td class="text-right">{{ !empty($customer->type) ? $customer->type : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('Serial No')}}:</b></td>
                                        <td class="text-right">
                                            {{ !empty($customer->serial_no) ? $customer->serial_no : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px;"><b>{{__('Invoice Status')}}:</b></td>
                                        <td class="text-right">
                                            @if ($invoice->status == 0)
                                                {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                                            @elseif($invoice->status == 1)
                                                {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                                            @elseif($invoice->status == 2)
                                                {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                                            @elseif($invoice->status == 3)
                                                {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                                            @elseif($invoice->status == 4)
                                                {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($settings['invoice_qr_display'] == 'on')
                                        <tr>
                                            <td colspan="2" class="text-right">
                                                <div class="view-qrcode">
                                                    {!! DNS2D::getBarcodeHTML(route('invoice.link.copy', \Crypt::encrypt($invoice->invoice_id)), "QRCODE", 1, 1) !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if(!empty($customFields) && count($invoice->customField) > 0)
                                        @foreach($customFields as $field)
                                            <tr>
                                                <td style="padding-right: 5px;">{{$field->name}} :</td>
                                                <td class="text-right">
                                                    {{!empty($invoice->customField) ? $invoice->customField[$field->id] : '-'}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Wavy Line Separator -->
            <div class="wavy-line" style="margin: 10px 0 5px 0;"></div>
        </div>
        <div class="invoice-body">

            <div class="invoice-footer">
                <h4 style="margin-top: 75px;">{{$settings['footer_title'] ?? 'Developed By Miftah Technology'}}</h4>
                <br>
                {!! $settings['footer_notes'] ?? 'Note Refundable' !!}
            </div>
        </div>
        <table align="center" style="width: 100%;">
            <tbody>
                <tr>
                    <td class="company-details-center">
                        _________________________________________________________________________________________________
                        <p class="company-detail">
                            @if($settings['mail_from_address']){{$settings['mail_from_address']}}@endif
                            @if($settings['company_address']){{$settings['company_address']}}@endif
                            @if($settings['company_city']){{$settings['company_city']}}, @endif
                            @if($settings['company_country']) {{$settings['company_country']}}@endif
                            @if($settings['company_telephone'])<br>{{$settings['company_telephone']}}@endif<br>
                        </p>
                    </td>
                </tr>

            </tbody>
        </table>

    </div>

    <div class="invoice-preview-main" id="boxes">
        <div class="invoice-header" style="background: {{$color}};color:{{$font_color}}">
            <!-- Logo Section -->
            <div style="text-align: center; padding: 20px 0 5px 0;">
                <img class="invoice-logo" src="{{$img}}" alt=""
                    style="width: auto; height: auto; max-width: 550px; max-height: none; margin-top: 75px;">
                <p style="border-color: #007bff; border-width: 2px;">
            </div>

            <!-- Wavy Line -->
            <div class="wavy-line"></div>

            <!-- Company Details - Centered -->

            <!-- Bill To and Invoice Details Section -->
            <table class="vertical-align-top" style="width: 100%; margin-top: 10px;">
                <tbody>

                    <tr>
                        <td style="width: 100%; vertical-align: top;">
                            <table class="no-space" style="width: auto; margin-left: 450px;">
                                <tbody>
                                    <tr>
                                        <td style="padding-right: 15px;"><b>{{__('Date')}}:</b></td>
                                        <td class="text-right">{{Utility::dateFormat($settings, $invoice->issue_date)}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <h1 style="color: #007bff">(Certificate)</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <h2 style="color: #007bff">Driving Course Completion</h2>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <img class="invoice-logo" src="{{$img}}" alt=""
                                style="width: auto; height: 180px; max-width: 180px; margin-top: 10px; border: 1px solid #000000;">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <h3>{{ strtoupper($customer->name) }}</h3>
                        </td>
                    </tr>
                    {{-- <tr>
                        <td class="text-center">
                            <h3>Grade: {{ strtoupper($item->grade) }}</h3>
                        </td>
                    </tr> --}}
                    <tr>
                        <td class="text-center">
                            <h3>Serial No: {{Utility::invoiceNumberFormat($settings, $invoice->invoice_id)}}</h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <h2>This is to certify that</h2>
                            <h2 style="margin-left: 35px;">The person named above has successfully completed proficiency
                                driving skills, road safety, and
                                traffic regulations.</h2>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                        </td>
                    </tr>

                    <tr>
                        <td class="text-left">
                            <h3 style="margin-left: 35px;">Director: ____________________</h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                        </td>
                    </tr>


                </tbody>
            </table>

            @if(!isset($preview))
                @include('invoice.script');
            @endif

</body>

</html>
