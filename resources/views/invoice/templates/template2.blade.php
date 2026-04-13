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
if (!empty($invoice->grade)) {
    if (is_numeric($invoice->grade)) {
        $originName = optional(\App\Models\ProductService::find($invoice->grade))->name ?? $invoice->grade;
    } else {
        $originName = $invoice->grade;
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

            .page {
            width: 210mm;
            min-height: 300mm;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
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
<!-- Bill To and Invoice Details Section -->
<!-- Row 1: Receipt Voucher Centered with QR Code on Right -->
<div
    style="display: flex; justify-content: space-between; align-items: center; margin: 0 0 10px 0; position: relative;">
    <!-- Empty div for balance (invisible spacer) -->
    <div style="width: 60px;"></div>

    <!-- Centered Receipt Voucher Title -->
    <h2 style="margin: 0; font-size: 20px; text-align: center; flex: 1;">Receipt Voucher</h2>



</div>


<!-- Row 2: Customer Info and Receipt Details -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin: 0 0 15px 0;">
    <!-- Left Column: Customer Info -->
    <div>
        @if(!empty($customer->name))
            <p style="margin-left: 20px;"><b>Full Name: {{!empty($customer->name) ? $customer->name : ''}}</b></p>
        @else
            <p style="margin: 0 0 3px 0;">-</p>
        @endif
        <p style="margin-left: 20px;"><b>Mobile #: {{!empty($customer->contact) ? $customer->contact : '-'}}</b></p>
        <p style="margin-left: 20px;"><b>Receipt No:</b>
            {{Utility::invoiceNumberFormat($settings, $invoice->invoice_id)}}</p>
        <p style="margin-left: 20px;"><b>Receipt Date:</b> {{Utility::dateFormat($settings, $invoice->issue_date)}}</p>
        <!-- QR Code on Right -->
        @if($settings['invoice_qr_display'] == 'on')
            <div class="view-qrcode"
                style="margin-left: 20px; max-width: 60px !important; width: 60px !important; height: 60px !important;">
                {!! DNS2D::getBarcodeHTML(route('invoice.link.copy', \Crypt::encrypt($invoice->invoice_id)), "QRCODE", 1, 1) !!}
            </div>
        @else
            <div style="width: 60px;"></div>
        @endif
    </div>

    <!-- Right Column: Receipt No and Date -->
    <div class="photo-frame" style="display: flex; justify-content: flex-end; margin: 0 20px 10px 0;">
        <div class="photo-icon" style="align-content: right">
            @if($customer && !empty($customer->cust_image))
                <img src="{{ asset('storage/uploads/cust_image/' . $customer->cust_image) }}" alt="{{ $customer->name }}"
                    class="user-photo" style="width: 180px; height: 180px; object-fit: cover; border-radius: 0%;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="photo-placeholder" style="display:none;">
                    <svg width="180" height="180" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                        <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                    </svg>
                </div>
            @elseif($customer)
                <img src="{{ asset('storage/uploads/cust_image/default.png') }}" alt="{{ $customer->name }}" class="user-photo"
                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="photo-placeholder" style="display:none;">
                    <svg width="80" height="80" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                        <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                    </svg>
                </div>
            @else
                <div class="photo-placeholder">
                    <svg width="80" height="80" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                        <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                    </svg>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Wavy Line Separator -->
<div class="wavy-line" style="margin: 0 0 -20px 0;"></div>
        </div>
        <div class="invoice-body">
            <table class="invoice-summary" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>{{__('#')}}</th>
                        <th>{{__('Nooca Ruqsada')}}</th>
                        <th>{{__('Grade')}}</th>
                        <th>{{__('Diiwangalinta')}}</th>
                        <th>{{__('Barashada Tabeelaha')}}</th>
                        <th>{{__('Shahado')}}</th>
                        <th>{{__('Buugga')}}</th>
                        <th>{{__('Tijaabo Qadka')}}</th>
                        <th>{{__('Total Amount')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($invoice->items) && count($invoice->items) > 0)
                        @foreach($invoice->items as $key => $item)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{!empty($item->type) ? $item->type : '-'}}</td>
                                <td>{{!empty($item->grade) ? $item->grade : '-'}}</td>
                                <td>{{!empty($item->net) ? $item->net : '-'}}</td>
                                <td>{{!empty($item->fare) ? $item->fare : '-'}}</td>
                                <td>{{!empty($item->tax) ? $item->tax : '-'}}</td>
                                <td>{{!empty($item->refund) ? $item->refund : '-'}}</td>
                                <td>{{!empty($item->commission) ? $item->commission : '-'}}</td>
                                {{-- <td>{{!empty($item->discount) ? $item->discount : '-'}}</td> --}}
                                <td>{{!empty($item->total) ? Utility::priceFormat($settings, $item->total) : Utility::priceFormat($settings, ($item->net ?? 0) + ($item->fare ?? 0) + ($item->tax ?? 0) + ($item->refund ?? 0) + ($item->commission ?? 0) - ($item->discount ?? 0))}}
                                </td>
                            </tr>
                        @endforeach
                    @elseif(isset($invoice->itemData) && count($invoice->itemData) > 0)
                        @foreach($invoice->itemData as $key => $item)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$item->name ?? '-'}}</td>
                                <td>-</td>
                                <td>-</td>
                                @php
        $itemtax = 0;
        if (!empty($item->itemTax)) {
            foreach ($item->itemTax as $taxes) {
                $itemtax += $taxes['tax_price'];
            }
        }
        $totalAmount = ($item->cost ?? 0) * ($item->quantity ?? 1) - ($item->discount ?? 0) + $itemtax;
                                @endphp
                                <td>{{Utility::priceFormat($settings, $totalAmount)}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center;">{{__('No items found')}}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7"></td>
                        <td colspan="3" class="sub-total" style="text-align: right;">
                            <table class="total-table" style="margin-left: auto;">

                                <tr>
                                    @php
$itemtax = 0;
if (!empty($item->itemTax)) {
    foreach ($item->itemTax as $taxes) {
        $itemtax += $taxes['tax_price'];
    }
}
$totalAmount = ($item->cost ?? 0) * ($item->quantity ?? 1) - ($item->discount ?? 0) + $itemtax;
$totalVAT = ($totalAmount * 5 / 100); // Assuming VAT is 5% for this example
                                    @endphp
                                    <td style="text-align: right; padding-right: 10px;">{{__('VAT 5 %')}}:</td>
                                    <td style="text-align: right;">
                                        {{Utility::priceFormat($settings, $totalVAT)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; padding-right: 10px;">{{__('Total')}}:</td>
                                    <td style="text-align: right;">
                                        {{Utility::priceFormat($settings, $item->cost)}}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right; padding-right: 10px;">{{__('Paid')}}:</td>
                                    <td style="text-align: right;">
                                        {{Utility::priceFormat($settings, ($invoice->getTotal() - $invoice->getDue()) - ($invoice->invoiceTotalCreditNote()))}}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td style="text-align: right; padding-right: 10px;">{{__('Credit Note')}}:</td>
                                    <td style="text-align: right;">{{Utility::priceFormat($settings,
                                        ($invoice->invoiceTotalCreditNote()))}}</td>
                                </tr> --}}
                                <tr>

                                    <td style="text-align: right; padding-right: 10px;">
                                        <strong>{{__('Balance')}}:</strong></td>
                                    <td style="text-align: right;">
                                        <strong>{{Utility::priceFormat($settings, $invoice->getDue() + +$totalVAT)}}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="invoice-footer">
                <h4 style="margin-top: 0px;">{{$settings['footer_title'] ?? 'Developed By Miftah Technology'}}</h4>
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



        @if(!isset($preview))
            @include('invoice.script');
        @endif
    </div>
