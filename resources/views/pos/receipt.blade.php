@php
    $settings = Utility::settings();
@endphp
    <!DOCTYPE html>
<html lang="en" dir="{{$settings == 'on'?'rtl':''}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">

    <title>{{env('APP_NAME')}} - POS Barcode</title>
    @if (isset($settings['SITE_RTL'] ) && $settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css')}}" id="main-style-link">
    @endif
</head>
<body>
<div id="bot" class="mt-5">
    <div class="row">
        @foreach($productServices as $product)
            @for($i=1;$i<=$quantity;$i++)
                <div class="col-auto mb-2">
                    <small class="">{{$product->name}}</small>
                    <div data-id="{{$product->id}}" class="product_barcode product_barcode_hight_de product_barcode_{{$product->id}} mt-2" data-skucode="{{ $product->sku }}"></div>
                </div>
            @endfor
        @endforeach
    </div>
</div>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('public/js/jquery-barcode.min.js') }}"></script>
<script src="{{ asset('public/js/jquery-barcode.js') }}"></script>
<script>
    $(document).ready(function() {
        let barcodeCount = $(".product_barcode").length;
        let generatedCount = 0;

        $(".product_barcode").each(function() {
            var id = $(this).data("id");
            var sku = $(this).data('skucode');
            sku = encodeURIComponent(sku);

            generateBarcode(sku, id, function() {
                generatedCount++;
                if (generatedCount === barcodeCount) {
                    setTimeout(() => {
                        window.print();
                    }, 500);
                }
            });
        });
        function generateBarcode(val, id, callback) {
            var value = val;
            var btype = '{{ $barcode['barcodeType'] }}';
            var renderer = '{{ $barcode['barcodeFormat'] }}';
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: '1',
                barHeight: '50',
                moduleSize: '5',
                posX: '10',
                posY: '20',
                addQuietZone: '1'
            };
            $('.product_barcode_' + id).html("").show().barcode(value, btype, settings);

            setTimeout(() => {
                if (typeof callback === "function") callback();
            }, 100);
        }

        window.onafterprint = function () {
            window.close();
            window.history.back();
        };
    });
</script>
</body>
</html>
