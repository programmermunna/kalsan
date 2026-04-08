@php
    $settings_data = \App\Models\Utility::settingsById($quotation->created_by);

@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings_data['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">


    <style type="text/css">
        :root {
            --theme-color: {{ $color }};
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

        .quotation-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .quotation-logo {
            max-width: 200px;
            width: 100%;
        }

        .quotation-header table td {
            padding: 15px 30px;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
            white-space: nowrap;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 114px;
            height: 114px;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .quotation-body {
            padding: 30px 25px 0;
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

        .quotation-summary td,
        .quotation-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .quotation-footer {
            padding: 15px 20px;
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

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }

        .quotation-summary p {
            margin-bottom: 0;
        }
    </style>

    @if ($settings_data['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body>
    <div class="quotation-preview-main" id="boxes">
        <div class="quotation-header">
            <table>
                <tbody>
                    <tr style="border-bottom:1px solid var(--theme-color);">
                        <td>
                            <img class="quotation-logo" src="{{ $img }}" alt="">
                        </td>
                        <td class="text-right">
                            <h3
                                style="text-transform: uppercase; font-size: 40px; font-weight: bold; color: var(--theme-color);">
                                {{ __('quotation') }}</h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td>
                            <p>
                                @if ($settings['company_name'])
                                    {{ $settings['company_name'] }}
                                @endif
                                <br>
                                @if ($settings['mail_from_address'])
                                    {{ $settings['mail_from_address'] }}
                                @endif
                                <br>
                                <br>
                                @if ($settings['company_address'])
                                    {{ $settings['company_address'] }}
                                @endif
                                @if ($settings['company_city'])
                                    <br> {{ $settings['company_city'] }},
                                @endif
                                @if ($settings['company_state'])
                                    {{ $settings['company_state'] }}
                                @endif
                                @if ($settings['company_zipcode'])
                                    - {{ $settings['company_zipcode'] }}
                                @endif
                                @if ($settings['company_country'])
                                    <br>{{ $settings['company_country'] }}
                                @endif
                                @if ($settings['company_telephone'])
                                    {{ $settings['company_telephone'] }}
                                @endif
                                <br>
                                @if (!empty($settings['registration_number']))
                                    {{ __('Registration Number') }} : {{ $settings['registration_number'] }}
                                @endif
                                <br>
                                @if ($settings['vat_gst_number_switch'] == 'on')
                                    @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                        {{ $settings['tax_type'] . ' ' . __('Number') }} : {{ $settings['vat_number'] }}
                                        <br>
                                    @endif
                                @endif
                            </p>
                        </td>
                        <td>
                            <table class="no-space" style="width: 45%;margin-left: auto;">
                                <tbody>

                                    <tr>
                                        <td>{{ __('Number') }}:</td>
                                        <td class="text-right">
                                            {{ Utility::quotationNumberFormat($settings, $quotation->quotation_id) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Issue Date') }}:</td>
                                        <td class="text-right">
                                            {{ Utility::dateFormat($settings, $quotation->quotation_date) }}</td>
                                    </tr>

                                    @if (!empty($customFields) && count($quotation->customField) > 0)
                                        @foreach ($customFields as $field)
                                            <tr>
                                                <td>{{ $field->name }} :</td>
                                                <td> {{ !empty($quotation->customField) ? $quotation->customField[$field->id] : '-' }}
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
        </div>
        <div class="quotation-body">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <strong style="margin-bottom: 10px; display:block;">{{ __('Bill To') }}:</strong>
                            @if (!empty($customer->billing_name))
                                <p>
                                    {{ !empty($customer->billing_name) ? $customer->billing_name : '' }}<br>
                                    {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}<br>
                                    {{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }}<br>
                                    {{ !empty($customer->billing_state) ? $customer->billing_state : '', ', ' }},
                                    {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' }}<br>
                                    {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}<br>
                                    {{ !empty($customer->billing_phone) ? $customer->billing_phone : '' }}<br>
                                </p>
                            @else
                                -
                            @endif
                        </td>
                        @if ($settings['shipping_display'] == 'on')
                            <td class="text-right">
                                <strong style="margin-bottom: 10px; display:block;">{{ __('Ship To') }}:</strong>
                                @if (!empty($customer->shipping_name))
                                    <p>
                                        {{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}<br>
                                        {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}<br>
                                        {{ !empty($customer->shipping_city) ? $customer->shipping_city : '' . ', ' }}<br>
                                        {{ !empty($customer->shipping_state) ? $customer->shipping_state : '' . ', ' }},
                                        {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}<br>
                                        {{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}<br>
                                        {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}<br>
                                    </p>
                                @else
                                    -
                                @endif
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="add-border quotation-summary" style="margin-top: 30px;">
                <thead style="background: {{ $color }};color:{{ $font_color }}">
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Tax') }}</th>
                        <th>{{ __('Tax Amount') }}</th>
                        <th>{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQuantity = 0;
                        $totalRate = 0;
                        $totalTaxPrice = 0;
                        $totalDiscount = 0;
                        $subTotal = 0;
                        $total = 0;
                        $taxesData = [];
                    @endphp
                    @if (isset($quotation->itemData) && count($quotation->itemData) > 0)
                        @foreach ($quotation->itemData as $key => $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ Utility::priceFormat($settings, $item->price) }}</td>
                                <td>
                                    @php
                                        $totalTaxRate = 0;
                                        $totalTaxPrice = 0;
                                    @endphp
                                    @if (!empty($item->itemTax))
                                        @foreach ($item->itemTax as $taxes)
                                            @php
                                                $res = str_ireplace(['%'], ' ', $taxes['rate']);
                                                $taxPrice = App\Models\Utility::taxRate($res, $item->price, $item->quantity);
                                                $totalTaxPrice += $taxPrice;
                                            @endphp
                                            <span>{{ $taxes['name'] }}</span> <span>({{ $taxes['rate'] }})</span><br>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ Utility::priceFormat($settings, $totalTaxPrice) }}</td>
                                <td>{{ Utility::priceFormat($settings, $item->price * $item->quantity + $totalTaxPrice) }}
                                </td>

                            </tr>
                            @php
                                $totalDiscount += $item->discount;
                                $subTotal += $item->price * $item->quantity + $totalTaxPrice;
                                $total = $subTotal - $totalDiscount;
                            @endphp
                        @endforeach
                    @else
                    @endif

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td colspan="2" class="sub-total">
                            <table class="total-table">
                                <tr>
                                    <td>{{ __('Subtotal') }}:</td>
                                    <td>{{ Utility::priceFormat($settings, $subTotal) }}</td>
                                </tr>

                                <tr>
                                    <td>{{ __('Discount') }}:</td>
                                    @if (!empty($totalDiscount))
                                        <td>{{ Utility::priceFormat($settings, $totalDiscount) }}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>{{ __('Total') }}:</td>

                                    <td>{{ Utility::priceFormat($settings, $total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="quotation-footer">
                <b>{{ $settings['footer_title'] }}</b> <br>
                {!! $settings['footer_notes'] !!}
            </div>
        </div>
    </div>


    @if (!isset($preview))
        @include('quotation.script');
    @endif

</body>

</html>
