@if(!empty($customer))
    <div class="row">
        <div class="col-md-9">
            <h5>{{__('Customer Detail')}}</h5>
            <div class="bill-to">
                @if(!empty($customer['name']))
                    <small>
                        <strong style="font-size: large">{{$customer['name']}}</strong><br>
                        <strong >{{$customer['billing_address'] . ' , ' . $customer['contact'] . ' , ' . $customer['email'] . '.'}}</strong><br>

                    </small>
                @else
                    <br> -
                @endif
            </div>
        </div>
        {{-- <div class="col-md-5">
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
        </div> --}}
        <div class="col-md-3">
            <a href="#" id="remove" class="text-sm">{{__(' Remove')}}</a>
        </div>
    </div>
@endif
