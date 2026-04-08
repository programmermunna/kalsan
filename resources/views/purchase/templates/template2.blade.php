@php
    $settings_data = \App\Models\Utility::settingsById($purchase->created_by);

@endphp

    <!DOCTYPE html>
<html lang="en" dir="{{$settings_data['SITE_RTL'] == 'on'?'rtl':''}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <style type="text/css">
        :root {
            --theme-color:{{$color}};
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

        .purchase-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .purchase-logo {
            max-width: 200px;
            width: 100%;
        }

        .purchase-header table td {
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

        .purchase-body {
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

        .purchase-summary td,
        .purchase-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .purchase-footer {
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
        .purchase-summary p{
            margin-bottom: 0;
        }
    </style>

    @if($settings_data['SITE_RTL']=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body>
<div class="purchase-preview-main" id="boxes">
    <div class="purchase-header" style="">
        <table class="vertical-align-top">
            <tbody>
            <tr>
                <td>
                    <img class="purchase-logo"
                         src="{{$img}}"
                         alt="">
                </td>
                <td class="text-right">
                    <p>
                        @if($settings['company_name']){{$settings['company_name']}}@endif<br>
                        @if($settings['mail_from_address']){{$settings['mail_from_address']}}@endif<br><br>
                        @if($settings['company_address']){{$settings['company_address']}}@endif
                        @if($settings['company_city']) <br> {{$settings['company_city']}}, @endif
                        @if($settings['company_state']){{$settings['company_state']}}@endif
                        @if($settings['company_zipcode']) - {{$settings['company_zipcode']}}@endif
                        @if($settings['company_country']) <br>{{$settings['company_country']}}@endif
                        @if($settings['company_telephone']){{$settings['company_telephone']}}@endif<br>
                        @if(!empty($settings['registration_number'])){{__('Registration Number')}} : {{$settings['registration_number']}} @endif<br>
                        @if($settings['vat_gst_number_switch'] == 'on')
                            @if(!empty($settings['tax_type']) && !empty($settings['vat_number'])){{$settings['tax_type'].' '. __('Number')}} : {{$settings['vat_number']}} <br>@endif
                        @endif
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="vertical-align-top">
            <tbody>
            <tr>
                <td>
                    <h3 style="text-transform: uppercase; font-size: 25px; font-weight: bold; margin-bottom: 15px;">{{__('PURCHASE')}}</h3>
                    <table class="no-space">
                        <tbody>
                        <tr>
                            <td>{{__('Number')}}:</td>
                            <td class="text-right">{{Utility::purchaseNumberFormat($settings,$purchase->purchase_id)}}</td>
                        </tr>
                        <tr>
                            <td>{{__('Purchase Date')}}:</td>
                            <td class="text-right">{{Utility::dateFormat($settings,$purchase->purchase_date)}}</td>
                        </tr>

                        @if(!empty($customFields) && count($purchase->customField)>0)
                            @foreach($customFields as $field)
                                <tr>
                                    <td>{{$field->name}} :</td>
                                    <td> {{!empty($purchase->customField)?$purchase->customField[$field->id]:'-'}}</td>
                                </tr>
                            @endforeach
                        @endif


                        </tbody>
                    </table>
                </td>
                @if($settings['pos_purchase_qr_display'] == 'on')
                <td>
                    <div class="view-qrcode">
                        {!! DNS2D::getBarcodeHTML(route('purchase.link.copy',\Crypt::encrypt($purchase->purchase_id)), "QRCODE",2,2) !!}

                    </div>
                </td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
    <div class="purchase-body">
        <table>
            <tbody>
            <tr>
                <td>
                    <strong style="margin-bottom: 10px; display:block;">{{__('Bill To')}}:</strong>
                    @if(!empty($vendor->billing_name))
                        <p>
                            {{!empty($vendor->billing_name)?$vendor->billing_name:''}}<br>
                            {{!empty($vendor->billing_address)?$vendor->billing_address:''}}<br>
                            {{!empty($vendor->billing_city)?$vendor->billing_city:'' .', '}}<br>
                            {{!empty($vendor->billing_state)?$vendor->billing_state:'',', '}},
                            {{!empty($vendor->billing_zip)?$vendor->billing_zip:''}}<br>
                            {{!empty($vendor->billing_country)?$vendor->billing_country:''}}<br>
                            {{!empty($vendor->billing_phone)?$vendor->billing_phone:''}}<br>
                        </p>
                    @else
                        -
                    @endif
                </td>
                @if($settings['shipping_display']=='on')
                    <td class="text-right">
                        <strong style="margin-bottom: 10px; display:block;">{{__('Ship To')}}:</strong>
                        @if(!empty($vendor->shipping_name))
                            <p>
                                {{!empty($vendor->shipping_name)?$vendor->shipping_name:''}}<br>
                                {{!empty($vendor->shipping_address)?$vendor->shipping_address:''}}<br>
                                {{!empty($vendor->shipping_city)?$vendor->shipping_city:'' . ', '}}<br>
                                {{!empty($vendor->shipping_state)?$vendor->shipping_state:'' .', '}},
                                {{!empty($vendor->shipping_zip)?$vendor->shipping_zip:''}}<br>
                                {{!empty($vendor->shipping_country)?$vendor->shipping_country:''}}<br>
                                {{!empty($vendor->shipping_phone)?$vendor->shipping_phone:''}}<br>
                            </p>
                        @else
                            -
                        @endif
                    </td>
                @endif
            </tr>
            </tbody>
        </table>
        <table class="add-border purchase-summary" style="margin-top: 30px;">
            <thead style="background: {{$color}};color:{{$font_color}}">
            <tr>
                <th>{{__('Item')}}</th>
                <th>{{__('Quantity')}}</th>
                <th>{{__('Rate')}}</th>
                <th>{{__('Discount')}}</th>
                <th>{{__('Tax')}} (%)</th>
                <th>{{__('Price')}} <small>after tax & discount</small></th>
            </tr>
            </thead>
            <tbody>
            @if(isset($purchase->itemData) && count($purchase->itemData) > 0)
                @foreach($purchase->itemData as $key => $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>{{Utility::priceFormat($settings,$item->price)}}</td>
                        <td>{{($item->discount!=0)?Utility::priceFormat($settings,$item->discount):'-'}}</td>
                        @php
                            $itemtax = 0;
                        @endphp
                        <td>
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
                        <td>{{Utility::priceFormat($settings,$item->price * $item->quantity -  $item->discount + $itemtax)}}</td>
                    @if(!empty($item->description))
                        <tr class="border-0 itm-description ">
                            <td colspan="6">{{$item->description}}</td>
                        </tr>
                        @endif
                        </tr>
                        @endforeach
                    @else
                    @endif

            </tbody>
            <tfoot>
            <tr>
                <td>{{__('Total')}}</td>
                <td>{{$purchase->totalQuantity}}</td>
                <td>{{Utility::priceFormat($settings,$purchase->totalRate)}}</td>
                <td>{{Utility::priceFormat($settings,$purchase->totalDiscount)}}</td>
                <td>{{Utility::priceFormat($settings,$purchase->totalTaxPrice) }}</td>
                <td>{{Utility::priceFormat($settings,$purchase->getSubTotal())}}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2" class="sub-total">
                    <table class="total-table">
                        <tr>
                            <td>{{__('Subtotal')}}:</td>
                            <td>{{Utility::priceFormat($settings,$purchase->getSubTotal())}}</td>
                        </tr>
                        @if($purchase->getTotalDiscount())
                            <tr>
                                <td>{{__('Discount')}}:</td>
                                <td>{{Utility::priceFormat($settings,$purchase->getTotalDiscount())}}</td>
                            </tr>
                        @endif
                        @if(!empty($purchase->taxesData))
                            @foreach($purchase->taxesData as $taxName => $taxPrice)
                                <tr>
                                    <td>{{$taxName}} :</td>
                                    <td>{{ Utility::priceFormat($settings,$taxPrice)  }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td>{{__('Total')}}:</td>
                            <td>{{Utility::priceFormat($settings,$purchase->getSubTotal()-$purchase->getTotalDiscount()+$purchase->getTotalTax())}}</td>
                        </tr>
                        <tr>
                            <td>{{__('Paid')}}:</td>
                            <td>{{Utility::priceFormat($settings,($purchase->getTotal()-$purchase->getDue()))}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Due Amount')}}:</td>
                            <td>{{Utility::priceFormat($settings,$purchase->getDue())}}</td>
                        </tr>

                    </table>
                </td>
            </tr>
            </tfoot>
        </table>
        <div class="purchase-footer">
            <b>{{$settings['footer_title']}}</b> <br>
            {!! $settings['footer_notes'] !!}
        </div>
    </div>
</div>
@if(!isset($preview))
    @include('purchase.script');
@endif

</body>

</html>
