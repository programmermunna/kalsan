@php
    $settings_data = \App\Models\Utility::settingsById($invoice->created_by);

@endphp
    <!DOCTYPE html>
<html lang="en" dir="{{$settings_data['SITE_RTL'] == 'on'?'rtl':''}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New York - invoice</title>
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
            padding: 15px 30px;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
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

        .invoice-body {
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
        }

        .itm-description td {
            padding-top: 0;
        }
        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th{
            text-align: right;
        }
        html[dir="rtl"]  .text-right{
            text-align: left;
        }
        html[dir="rtl"] .view-qrcode{
            margin-left: 0;
            margin-right: auto;
        }
        p:not(:last-of-type){
            margin-bottom: 15px;
        }
        .invoice-summary p{
            margin-bottom: 0;
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
        .invoice-header table td{
            padding: 15px 10px
        }

        .company-detail {
            font-size: 10px
        }

}
    </style>
    @if($settings_data['SITE_RTL']=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body>
<div class="invoice-preview-main" id="boxes">
    <div class="invoice-header"  style="border-top: 15px solid {{ $color }};">
        <table>
            <tbody>
            <tr>
                <td >
                    <h3 style="text-transform: uppercase; font-size: 40px; font-weight: bold;">{{__('INVOICE')}}</h3>
                </td>
                <td class="text-right">
                    <img class="invoice-logo"
                         src="{{$img}}"
                         alt="">
                </td>

            </tr>
            </tbody>
        </table>

    </div>
    <div class="invoice-body">
        <table class="vertical-align-top">
            <tbody >
            <tr>
                @if (!empty($settings['company_name']) && !empty($settings['mail_from_address']) && !empty($settings['company_address']))
                <td style="font-size: 13px;">
                    <strong style="margin-bottom: 10px; display:block;">{{__('From:')}}</strong>
                    <p class="company-detail">
                        @if($settings['company_name']){{$settings['company_name']}}@endif<br>
                        @if($settings['mail_from_address']){{$settings['mail_from_address']}}@endif<br><br>
                        @if($settings['company_address']){{$settings['company_address']}}@endif
                        @if($settings['company_city']) <br> {{$settings['company_city']}}, @endif
                        @if($settings['company_state']){{$settings['company_state']}}@endif
                        @if($settings['company_zipcode']) - {{$settings['company_zipcode']}}@endif
                        @if($settings['company_country']) <br>{{$settings['company_country']}}@endif
                        @if($settings['company_telephone']){{$settings['company_telephone']}}@endif<br>

                    </p>
                </td>
                @endif
                <td  style="font-size: 13px;">
                    <strong style="margin-bottom: 10px; display:block;">{{__('Bill To:')}}</strong>
                    @if(!empty($customer->billing_name))
                        <p>
                            {{!empty($customer->billing_name)?$customer->billing_name:''}}<br>
                            {{!empty($customer->billing_address)?$customer->billing_address:''}}<br>
                            {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}}<br>
                            {{!empty($customer->billing_state)?$customer->billing_state:'',', '}},
                            {{!empty($customer->billing_zip)?$customer->billing_zip:''}}<br>
                            {{!empty($customer->billing_country)?$customer->billing_country:''}}<br>
                            {{!empty($customer->billing_phone)?$customer->billing_phone:''}}<br>
                        </p>
                    @else
                        -
                    @endif
                </td>
                @if($settings['shipping_display']=='on')
                <td style="font-size: 13px;" class="text-right">
                    <strong style="margin-bottom: 10px; display:block;">{{__('Ship To:')}}</strong>
                    @if(!empty($customer->shipping_name))
                        <p>
                            {{!empty($customer->shipping_name)?$customer->shipping_name:''}}<br>
                            {{!empty($customer->shipping_address)?$customer->shipping_address:''}}<br>
                            {{!empty($customer->shipping_city)?$customer->shipping_city:'' . ', '}}<br>
                            {{!empty($customer->shipping_state)?$customer->shipping_state:'' .', '}},
                            {{!empty($customer->shipping_zip)?$customer->shipping_zip:''}}<br>
                            {{!empty($customer->shipping_country)?$customer->shipping_country:''}}<br>
                            {{!empty($customer->shipping_phone)?$customer->shipping_phone:''}}<br>
                        </p>
                    @else
                        -
                    @endif
                </td>
                @endif
            </tr>
            <tr style="border-bottom:1px solid {{ $color }};">

                <td>
                    <p>
                        @if (!empty($settings['registration_number'])){{ __('Registration Number') }} : {{ $settings['registration_number'] }} @endif<br>
                            @if (!empty($settings['tax_type']) && !empty($settings['vat_number'])){{ $settings['tax_type'] . ' ' . __('Number') }} : {{ $settings['vat_number'] }} <br>@endif

                            <strong>{{__('Tax Number ')}} : </strong>{{!empty($customer->tax_number)?$customer->tax_number:''}}
                    </p>
                </td>
                @if($settings['invoice_qr_display'] == 'on')
                <td colspan="2">
                    <div class="view-qrcode" style="margin-top: 0;">
                        {!! DNS2D::getBarcodeHTML(route('invoice.link.copy',\Crypt::encrypt($invoice->invoice_id)), "QRCODE",2,2) !!}
                    </div>
                </td>
                @endif
            </tr>
            </tbody>
        </table>
        <table >
            <tbody >
            <tr >
                <td >
                    <table class="no-space" >
                        <tbody  >
                        <tr >
                            <td>{{__('Number')}}:</td>
                            <td class="text-right">{{Utility::invoiceNumberFormat($settings,$invoice->invoice_id)}}</td>
                        </tr>
                        <tr>
                            <td>{{__('Issue Date')}}:</td>
                            <td class="text-right">{{Utility::dateFormat($settings,$invoice->issue_date)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{__('Due Date:')}}</b></td>
                            <td class="text-right">{{Utility::dateFormat($settings,$invoice->due_date)}}</td>
                        </tr>
                        @if(!empty($customFields) && count($invoice->customField)>0)
                            @foreach($customFields as $field)
                                <tr>
                                    <td>{{$field->name}} :</td>
                                    <td> {{!empty($invoice->customField)?$invoice->customField[$field->id]:'-'}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>

                @if ($invoice->status == 0)
                    <td
                        class="badge bg-primary"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</td>
                @elseif($invoice->status == 1)
                    <td
                        class="badge bg-warning"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</td>
                @elseif($invoice->status == 2)
                    <td
                        class="badge bg-danger"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</td>
                @elseif($invoice->status == 3)
                    <td
                        class="badge bg-info"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</td>
                @elseif($invoice->status == 4)
                    <td
                        class="badge bg-primary"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</td>
                @endif
        </tr>
            </tbody>
        </table>
        <table class="add-border invoice-summary" style="margin-top: 30px;">
            <thead style="background: {{ $color }};color:{{ $font_color }}">
            <tr style="border-bottom:1px solid {{ $color }};" >
                <th>{{__('Item')}}</th>
                <th>{{__('Quantity')}}</th>
                <th>{{__('Rate')}}</th>
                <th>{{__('Discount')}}</th>
                <th>{{__('Tax')}} (%)</th>
                <th>{{__('Price')}} <small>{{ __('after tax & discount') }}</small></th>
            </tr>
            </thead >
            <tbody style="border-bottom:1px solid {{ $color }};" >
            @if(isset($invoice->itemData) && count($invoice->itemData) > 0)
                @foreach($invoice->itemData as $key => $item)
                    <tr >
                        <td>{{$item->name}}</td>
                        @php
                        $unit = $item->unit;
                        $unitName = App\Models\ProductServiceUnit::find($unit);
                    @endphp
                    <td>{{$item->quantity}} {{ ($unitName != null) ?  '('. $unitName->name .')' : ''}}</td>
                        <td>{{Utility::priceFormat($settings,$item->price)}}</td>
                        <td>{{($item->discount!=0)?Utility::priceFormat($settings,$item->discount):'-'}}</td>
                        @php
                            $itemtax = 0;
                        @endphp
                        <td >
                            @if(!empty($item->itemTax))

                                @foreach($item->itemTax as $taxes)
                                    @php
                                        $itemtax += $taxes['tax_price'];
                                    @endphp
                                    <p>{{$taxes['name']}} ({{$taxes['rate']}}) {{$taxes['price']}}</p>
                                @endforeach
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td >{{Utility::priceFormat($settings,$item->price * $item->quantity -  $item->discount + $itemtax)}}</td>
                    @if(!empty($item->description))
                        <tr class="border-0 itm-description">
                        <td colspan="6" style="border-bottom:1px solid {{ $color }};">{{$item->description}}</td>
                        </tr>
                        @endif
                        </tr>
                        @endforeach

                    @else
                    @endif
            </tbody>
            <tfoot >
            <tr style="border-bottom:1px solid {{ $color }};">
                <td>{{__('Total')}}</td>
                <td>{{$invoice->totalQuantity}}</td>
                <td>{{Utility::priceFormat($settings,$invoice->totalRate)}}</td>
                <td>{{Utility::priceFormat($settings,$invoice->totalDiscount)}}</td>
                <td>{{Utility::priceFormat($settings,$invoice->totalTaxPrice) }}</td>
                <td>{{Utility::priceFormat($settings,$invoice->getSubTotal())}}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2" class="sub-total">
                    <table class="total-table">
                        <tr style="border-bottom:1px solid {{ $color }};">
                            <td >{{__('Subtotal')}}:</td>
                            <td>{{Utility::priceFormat($settings,$invoice->getSubTotal())}}</td>
                        </tr>
                        @if($invoice->getTotalDiscount())
                            <tr style="border-bottom:1px solid {{ $color }};">
                                <td>{{__('Discount')}}:</td>
                                <td>{{Utility::priceFormat($settings,$invoice->getTotalDiscount())}}</td>
                            </tr>
                        @endif
                        @if(!empty($invoice->taxesData))
                            @foreach($invoice->taxesData as $taxName => $taxPrice)
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td>{{$taxName}} :</td>
                                    <td>{{ Utility::priceFormat($settings,$taxPrice)  }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr style="border-bottom:1px solid {{ $color }};">
                            <td>{{__('Total')}}:</td>
                            <td>{{Utility::priceFormat($settings,$invoice->getSubTotal()-$invoice->getTotalDiscount()+$invoice->getTotalTax())}}</td>
                        </tr>
                        <tr style="border-bottom:1px solid {{ $color }};">
                            <td>{{__('Paid')}}:</td>
                            <td>{{Utility::priceFormat($settings,($invoice->getTotal()-$invoice->getDue())-($invoice->invoiceTotalCreditNote()))}}</td>
                        </tr>
                        <tr style="border-bottom:1px solid {{ $color }};">
                            <td>{{__('Credit Note')}}:</td>
                            <td>{{Utility::priceFormat($settings,($invoice->invoiceTotalCreditNote()))}}</td>
                        </tr>
                        <tr style="border-bottom:1px solid {{ $color }};">
                            <td>{{__('Due Amount')}}:</td>
                            <td>{{Utility::priceFormat($settings,$invoice->getDue())}}</td>
                        </tr>

                    </table>
                </td>
            </tr>
            </tfoot>
        </table>
        <div class="invoice-footer">
            <b>{{$settings['footer_title']}}</b> <br>
            {!! $settings['footer_notes'] !!}
        </div>
    </div>
</div>
@if(!isset($preview))
    @include('invoice.script');
@endif

</body>

</html>
