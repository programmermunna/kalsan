@if(!empty($customer))
<div class="d-flex gap-2 align-items-start">
    <div class="row row-gap-1 flex-1">
        <div class="col-lg-6 col-12">
            <h5>{{__('Bill to')}}</h5>
            <div class="bill-to">
                @if(!empty($customer['billing_name']))
                        <span>{{$customer['billing_name']}}</span><br>
                        <span>{{$customer['billing_phone']}}</span><br>
                        <span>{{$customer['billing_address']}}</span><br>
                        <span>{{$customer['billing_city'] . ' , '.$customer['billing_state'].' , '.$customer['billing_country'].'.'}}</span><br>
                        <span>{{$customer['billing_zip']}}</span>

                @else
                    <br> -
                @endif
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <h5>{{__('Ship to')}}</h5>
            <div class="bill-to">
                @if(!empty($customer['shipping_name']))
                        <span>{{$customer['shipping_name']}}</span><br>
                        <span>{{$customer['shipping_phone']}}</span><br>
                        <span>{{$customer['shipping_address']}}</span><br>
                        <span>{{$customer['shipping_city'] . ' , '.$customer['shipping_state'].' , '.$customer['shipping_country'].'.'}}</span><br>
                        <span>{{$customer['shipping_zip']}}</span>
                @else
                    <br> -
                @endif
            </div>
        </div>
    </div>
    <a href="#" id="remove" class="text-sm">{{__(' Remove')}}</a>
</div>

@endif
