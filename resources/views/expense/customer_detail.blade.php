@if(!empty($customer))
    <div class="row">
        <div class="col-md-5">
            <h6>{{__('Bill to')}}</h6>
            <div class="bill-to">
                @if(!empty($customer['billing_name']))
                    <small>
                        <span>{{$customer['billing_name']}}</span><br>
                        <span>{{$customer['billing_phone']}}</span><br>
                        <span>{{$customer['billing_address']}}</span><br>
                        <span>{{$customer['billing_city'] . ' , '.$customer['billing_state'].' , '.$customer['billing_country'].'.'}}</span><br>
                        <span>{{$customer['billing_zip']}}</span>

                    </small>
                @else
                    <br> -
                @endif
            </div>
        </div>
        <div class="col-md-5">
            <h6>{{__('Ship to')}}</h6>
            <div class="bill-to">
                @if(!empty($customer['shipping_name']))
                    <small>
                        <span>{{$customer['shipping_name']}}</span><br>
                        <span>{{$customer['shipping_phone']}}</span><br>
                        <span>{{$customer['shipping_address']}}</span><br>
                        <span>{{$customer['shipping_city'] . ' , '.$customer['shipping_state'].' , '.$customer['shipping_country'].'.'}}</span><br>
                        <span>{{$customer['shipping_zip']}}</span>

                    </small>
                @else
                    <br> -
                @endif
            </div>
        </div>
        <div class="col-md-2">
            <a href="#" id="remove" class="text-sm">{{__(' Remove')}}</a>
        </div>
    </div>
@endif
