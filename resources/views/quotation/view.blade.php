@extends('layouts.admin')
@section('page-title')
    {{__('Quotation Detail')}}
@endsection
@push('script-page')
@endpush

@php
    $settings = Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('quotation.index')}}">{{__('Quotation Summary')}}</a></li>
    <li class="breadcrumb-item">{{ AUth::user()->quotationNumberFormat($quotation->quotation_id) }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('quotation.pdf', Crypt::encrypt($quotation->id))}}" class="btn btn-primary" target="_blank" >{{__('Download')}}</a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                            <h4>{{__('quotation')}}</h4>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                            <h4 class="invoice-number">{{ Auth::user()->quotationNumberFormat($quotation->quotation_id) }}</h4>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <small class="font-style">
                                <strong>{{__('Billed To')}} :</strong><br>
                                @if(!empty($customer->billing_name))
                                    {{!empty($customer->billing_name)?$customer->billing_name:''}}<br>
                                    {{!empty($customer->billing_address)?$customer->billing_address:''}}<br>
                                    {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}}<br>
                                    {{!empty($customer->billing_state)?$customer->billing_state:'',', '}},
                                    {{!empty($customer->billing_zip)?$customer->billing_zip:''}}<br>
                                    {{!empty($customer->billing_country)?$customer->billing_country:''}}<br>
                                    {{!empty($customer->billing_phone)?$customer->billing_phone:''}}<br>
                                    @if($settings['vat_gst_number_switch'] == 'on')
                                    <strong>{{__('Tax Number ')}} : </strong>{{!empty($customer->tax_number)?$customer->tax_number:''}}
                                    @endif
                                @else
                                    -
                                @endif
                            </small>
                        </div>
                        <div class="col-4">
                            @if(App\Models\Utility::getValByName('shipping_display')=='on')
                                <small>
                                    <strong>{{__('Shipped To')}} :</strong><br>
                                        @if(!empty($customer->shipping_name))
                                        {{!empty($customer->shipping_name)?$customer->shipping_name:''}}<br>
                                        {{!empty($customer->shipping_address)?$customer->shipping_address:''}}<br>
                                        {{!empty($customer->shipping_city)?$customer->shipping_city:'' . ', '}}<br>
                                        {{!empty($customer->shipping_state)?$customer->shipping_state:'' .', '}},
                                        {{!empty($customer->shipping_zip)?$customer->shipping_zip:''}}<br>
                                        {{!empty($customer->shipping_country)?$customer->shipping_country:''}}<br>
                                        {{!empty($customer->shipping_phone)?$customer->shipping_phone:''}}<br>
                                    @else
                                    -
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="col-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-4">
                                    <small>
                                        <strong>{{__('Issue Date')}} :</strong>
                                        {{\Auth::user()->dateFormat($quotation->quotation_date)}}<br><br>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="text-dark" >#</th>
                                        <th class="text-dark">{{__('Items')}}</th>
                                        <th class="text-dark">{{__('Quantity')}}</th>
                                        <th class="text-dark">{{__('Price')}}</th>
                                        <th class="text-dark">{{__('Tax')}}</th>
                                        <th class="text-dark">{{__('Tax Amount')}}</th>
                                        <th class="text-dark">{{__('Total')}}</th>
                                    </tr>
                                    </thead>
                                    @php
                                        $totalQuantity=0;
                                        $totalRate=0;
                                        $totalTaxPrice=0;
                                        $totalDiscount=0;
                                        $subTotal = 0;
                                        $total = 0;
                                        $taxesData=[];
                                    @endphp
                                    @foreach($iteams as $key =>$iteam)
                                        @if(!empty($iteam->tax))
                                            @php
                                                $taxes=App\Models\Utility::tax($iteam->tax);
                                                $totalQuantity+=$iteam->quantity;
                                                $totalRate+=$iteam->price;
                                                $totalDiscount+=$iteam->discount;

                                                foreach($taxes as $taxe){

                                                    $taxDataPrice=App\Models\Utility::taxRate($taxe->rate,$iteam->price,$iteam->quantity);
                                                    if (array_key_exists($taxe->name,$taxesData))
                                                    {
                                                        $taxesData[$taxe->name] = $taxesData[$taxe->name]+$taxDataPrice;
                                                    }
                                                    else
                                                    {
                                                        $taxesData[$taxe->name] = $taxDataPrice;
                                                    }
                                                }
                                            @endphp
                                        @endif
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{!empty($iteam->product())?$iteam->product()->name:''}}</td>
                                            <td>{{$iteam->quantity}}</td>
                                            <td>{{\Auth::user()->priceFormat($iteam->price)}}</td>
                                            <td>
                                                @if(!empty($iteam->tax))
                                                    <table>
                                                        @php
                                                            $totalTaxRate = 0;
                                                            $totalTaxPrice = 0;
                                                        @endphp
                                                        @foreach($taxes as $tax)
                                                            @php
                                                                $taxPrice=App\Models\Utility::taxRate($tax->rate,$iteam->price,$iteam->quantity);
                                                                $totalTaxPrice+=$taxPrice;
                                                            @endphp
                                                            <tr>
                                                                <span class="badge bg-primary">{{$tax->name .' ('.$tax->rate .'%)'}}</span> <br>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{\Auth::user()->priceFormat($totalTaxPrice)}}</td>
                                            <td >{{\Auth::user()->priceFormat(($iteam->price*$iteam->quantity) + $totalTaxPrice)}}</td>
                                        </tr>
                                        @php
                                                $subTotal +=  (($iteam->price*$iteam->quantity) + $totalTaxPrice);
                                                $total = $subTotal - $totalDiscount;
                                    @endphp
                                    @endforeach


                                    <tr>
                                        <td><b>{{__(' Sub Total')}}</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{\Auth::user()->priceFormat($subTotal)}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{__('Discount')}}</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{\Auth::user()->priceFormat($totalDiscount)}}</td>
                                    </tr>
                                    <tr class="pos-header">
                                        <td><b>{{__('Total')}}</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{\Auth::user()->priceFormat($total)}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
