@extends('layouts.admin')
@section('page-title')
    {{__('Bill Detail')}}
@endsection
@push('script-page')
@endpush
@php
    $settings = Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('bill.index')}}">{{__('Bill')}}</a></li>
    <li class="breadcrumb-item">{{ Auth::user()->billNumberFormat($bill->bill_id) }}</li>
@endsection

@section('content')

    @can('send bill')
        @if($bill->status!=4)
            <div class="row">
                <div class="col-12">
                    <div class="bill-timeline-card mb-4">
                        <div class="row timeline-wrapper">
                            <div class="col-xl-4 col-md-5 col-sm-7 create-invoice invoice">
                                <div class="progress mb-3">
                                    <div class="progress-value"></div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_69_767)">
                                            <path d="M9.15642 18C8.92315 18 8.69942 17.9074 8.53447 17.7426C8.36952 17.5777 8.27686 17.3542 8.27686 17.1211V0.878906C8.27686 0.645806 8.36952 0.422253 8.53447 0.257426C8.69942 0.0925988 8.92315 0 9.15642 0C9.3897 0 9.61342 0.0925988 9.77837 0.257426C9.94332 0.422253 10.036 0.645806 10.036 0.878906V17.1211C10.036 17.3542 9.94332 17.5777 9.77837 17.7426C9.61342 17.9074 9.3897 18 9.15642 18Z" fill="white"/>
                                            <path d="M17.2838 9.87891H1.02947C0.796193 9.87891 0.572472 9.78631 0.407521 9.62148C0.242571 9.45665 0.149902 9.2331 0.149902 9C0.149902 8.7669 0.242571 8.54335 0.407521 8.37852C0.572472 8.21369 0.796193 8.12109 1.02947 8.12109H17.2838C17.5171 8.12109 17.7408 8.21369 17.9058 8.37852C18.0707 8.54335 18.1634 8.7669 18.1634 9C18.1634 9.2331 18.0707 9.45665 17.9058 9.62148C17.7408 9.78631 17.5171 9.87891 17.2838 9.87891Z" fill="white"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_69_767">
                                            <rect width="18.0135" height="18" fill="white" transform="translate(0.149902)"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{__('Create Bill')}}</h5>
                                        <p class="text-muted mb-2">{{__('Created on ')}}{{\Auth::user()->dateFormat($bill->bill_date)}}</p>
                                        @can('edit bill')
                                            @if ($bill->status != 3 && $bill->status != 4)
                                                <a href="{{ route('bill.edit',\Crypt::encrypt($bill->id)) }}" class="btn btn-sm d-inline-flex align-items-center gap-2" data-bs-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil"></i>
                                                    {{__('Edit')}}
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-5 col-sm-7 send-invoice invoice">
                                <div class="progress mb-3">
                                    <div class="{{ $bill->status !== 0 ? 'progress-value' : '' }}"></div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.3797 5.12359L7.43524 9.24773L1.21757 7.17645C0.783563 7.03159 0.491171 6.62471 0.493668 6.16758C0.496198 5.71046 0.791942 5.30607 1.22762 5.16627L17.1241 0.0508648C17.502 -0.0705155 17.9167 0.0290971 18.1974 0.309582C18.4781 0.590066 18.5778 1.00447 18.4563 1.38208L13.337 17.2666C13.1971 17.702 12.7924 17.9975 12.335 18C11.8775 18.0025 11.4703 17.7103 11.3253 17.2767L9.24246 11.0335L13.3797 5.12359Z" fill="white"/>
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{__('Approve Bill')}}</h5>
                                        <p class="text-muted mb-2">
                                            @if($bill->status!=0)
                                                {{__('Sent on')}} {{\Auth::user()->dateFormat($bill->send_date)}}
                                            @else
                                                @can('send bill')
                                                    <small>{{__('Status')}} : {{__('Not Sent')}}</small>
                                                @endcan
                                            @endif
                                        </p>
                                        @if($bill->status==0)
                                            @can('send bill')
                                                <a href="{{ route('bill.sent',$bill->id) }}" class="btn btn-sm d-inline-flex align-items-center gap-2 btn-warning border-0 data-bs-toggle="tooltip" data-original-title="{{__('Mark Sent')}}"><i class="ti ti-send"></i>{{__('Approve')}}</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-5 col-sm-7 get-paid invoice">
                                <div class="progress mb-3">
                                    <div class="{{ $bill->status == 4 ? 'progress-value' : '' }}"></div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.386719 5.30859H8.47266V6.36328H0.386719V5.30859Z" fill="white"/>
                                            <path d="M8.47266 7.06641H0.386719V9.52734H8.47266V7.06641ZM3.72656 8.47266H1.79297C1.5988 8.47266 1.44141 8.31526 1.44141 8.12109C1.44141 7.92693 1.5988 7.76953 1.79297 7.76953H3.72656C3.92073 7.76953 4.07812 7.92693 4.07812 8.12109C4.07812 8.31526 3.92073 8.47266 3.72656 8.47266ZM7.06641 8.47266H5.13281C4.93864 8.47266 4.78125 8.31526 4.78125 8.12109C4.78125 7.92693 4.93864 7.76953 5.13281 7.76953H7.06641C7.26057 7.76953 7.41797 7.92693 7.41797 8.12109C7.41797 8.31526 7.26057 8.47266 7.06641 8.47266Z" fill="white"/>
                                            <path d="M10.5981 3.55005C10.5708 3.55131 4.92694 3.55075 3.55078 3.55086V4.60555H9.17578V10.2305H3.55078V14.4493H14.4492V7.06648C12.4352 7.06648 10.776 5.51852 10.5981 3.55005Z" fill="white"/>
                                            <path d="M10.598 2.84846C10.7029 1.68683 11.3235 0.671766 12.2291 0.0351562H4.95703C4.18148 0.0351562 3.55078 0.665859 3.55078 1.44141V2.84766C4.94831 2.84776 10.5702 2.8472 10.598 2.84846ZM9.52734 1.79297H7.76953C7.57536 1.79297 7.41797 1.63557 7.41797 1.44141C7.41797 1.24724 7.57536 1.08984 7.76953 1.08984H9.52734C9.72148 1.08984 9.87891 1.24724 9.87891 1.44141C9.87891 1.63557 9.72148 1.79297 9.52734 1.79297Z" fill="white"/>
                                            <path d="M3.55078 16.5586C3.55078 17.3341 4.18148 17.9648 4.95703 17.9648H13.043C13.8185 17.9648 14.4492 17.3341 14.4492 16.5586V15.1523H3.55078V16.5586ZM8.12109 16.207H9.87891C10.073 16.207 10.2305 16.3645 10.2305 16.5586C10.2305 16.7527 10.073 16.9102 9.87891 16.9102H8.12109C7.92703 16.9102 7.76953 16.7527 7.76953 16.5586C7.76953 16.3645 7.92703 16.207 8.12109 16.207Z" fill="white"/>
                                            <path d="M14.4492 0.0351562C12.7017 0.0351562 11.2852 1.45174 11.2852 3.19922C11.2852 4.9467 12.7017 6.36328 14.4492 6.36328C16.1967 6.36328 17.6133 4.9467 17.6133 3.19922C17.6133 1.45174 16.1967 0.0351562 14.4492 0.0351562ZM16.0162 2.52496L14.1705 4.37066C14.0331 4.50795 13.8106 4.50795 13.6733 4.37066L12.8822 3.57964C12.7449 3.44236 12.7449 3.21975 12.8822 3.08243C13.0196 2.94514 13.2421 2.94514 13.3794 3.08243L13.9219 3.62489L15.519 2.02778C15.6563 1.89049 15.8788 1.89049 16.0162 2.02778C16.1535 2.16506 16.1535 2.38767 16.0162 2.52496Z" fill="white"/>
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{__('Get Paid')}}</h5>
                                        <p class="text-muted mb-2">
                                            {{__('Status')}} : {{__('Awaiting payment')}}
                                        </p>
                                        @if($bill->status!=0)
                                            @can('create payment bill')
                                                <a href="#" data-url="{{ route('bill.payment',$bill->id) }}" data-ajax-popup="true" data-title="{{__('Add Payment')}}" class="btn btn-sm d-inline-flex align-items-center gap-2" data-original-title="{{__('Add Payment')}}"><i class="ti ti-report-money"></i>{{__('Add Payment')}}</a> <br>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @if(\Auth::user()->type=='company')
        @if($bill->status!=0)
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-md-12 d-flex flex-wrap gap-2 align-items-center justify-content-end">
                 @if ($bill->status != 4)
                    <div class="all-button-box">
                        <a href="#" data-url="{{ route('bill.debit.note', $bill->id) }}" data-ajax-popup="true"
                            data-title="{{ __('Apply Debit Note') }}" class="btn btn-sm btn-primary">
                            {{ __('Apply Debit Note') }}
                        </a>
                    </div>
                @endif
                <div class="all-button-box">
                    <a href="{{ route('bill.resent', $bill->id) }}" class="btn btn-sm btn-primary">
                        {{ __('Reapprove Bill') }}
                    </a>
                </div>
                <div class="all-button-box">
                    <a href="{{ route('bill.pdf', Crypt::encrypt($bill->id)) }}" target="_blank"
                        class="btn btn-sm btn-primary">
                        {{ __('Download') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h4>{{__('Bill')}}</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                    <h4 class="invoice-number">{{ Auth::user()->billNumberFormat($bill->bill_id) }}</h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="me-4">
                                            <small>
                                                <strong>{{__('Issue Date')}} :</strong><br>
                                                {{\Auth::user()->dateFormat($bill->bill_date)}}<br><br>
                                            </small>
                                        </div>
                                        <div>
                                            <small>
                                                <strong>{{__('Due Date')}} :</strong><br>
                                                {{\Auth::user()->dateFormat($bill->due_date)}}<br><br>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <small class="font-style">
                                        <strong>{{__('Billed From')}} :</strong><br>
                                        @if(!empty($vendor->name))
                                            {{!empty($vendor->name)?$vendor->name:''}}<br>
                                            {{!empty($vendor->contact)?$vendor->contact:''}}<br>
                                            {{!empty($vendor->email)?$vendor->email:'' .', '}}<br>
                                            {{!empty($vendor->billing_state)?$vendor->billing_state:'',', '}},
                                            {{!empty($vendor->billing_zip)?$vendor->billing_zip:''}}<br>
                                            {{!empty($vendor->billing_country)?$vendor->billing_country:''}}<br>
                                            {{!empty($vendor->billing_phone)?$vendor->billing_phone:''}}<br>
                                            @if($settings['vat_gst_number_switch'] == 'on')
                                                @if(!empty($settings['tax_type']) && !empty($settings['vat_number'])){{$settings['tax_type'].' '. __('Number')}} : {{$settings['vat_number']}} <br>@endif

                                                <strong>{{__('Tax Number ')}} : </strong>{{!empty($vendor->tax_number)?$vendor->tax_number:''}}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </small>
                                </div>

                                @if(App\Models\Utility::getValByName('shipping_display')=='on')
                                    <div class="col">
                                        <small>
                                            <strong>{{__('Shipped To')}} :</strong><br>
                                            @if(!empty($vendor->shipping_name))
                                                {{!empty($vendor->shipping_name)?$vendor->shipping_name:''}}<br>
                                                {{!empty($vendor->shipping_address)?$vendor->shipping_address:''}}<br>
                                                {{!empty($vendor->shipping_city)?$vendor->shipping_city:'' . ', '}}<br>
                                                {{!empty($vendor->shipping_state)?$vendor->shipping_state:'' .', '}},
                                                {{!empty($vendor->shipping_zip)?$vendor->shipping_zip:''}}<br>
                                                {{!empty($vendor->shipping_country)?$vendor->shipping_country:''}}<br>
                                                {{!empty($vendor->shipping_phone)?$vendor->shipping_phone:''}}<br>
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </div>
                                @endif

                                <div class="col">
                                    <div class="float-end mt-3">
                                        @if($settings['bill_qr_display'] == 'on')
                                        {!! DNS2D::getBarcodeHTML(route('bill.link.copy',\Illuminate\Support\Facades\Crypt::encrypt($bill->id)), "QRCODE",2,2) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <small>
                                        <strong>{{__('Status')}} :</strong><br>
                                        @if($bill->status == 0)
                                            <span class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 1)
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 2)
                                            <span class="badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 3)
                                            <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 4)
                                            <span class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @endif
                                    </small>
                                </div>


                                @if(!empty($customFields) && count($bill->customField)>0)
                                    @foreach($customFields as $field)
                                        <div class="col text-md-end">
                                            <small>
                                                <strong>{{$field->name}} :</strong><br>
                                                {{!empty($bill->customField)?$bill->customField[$field->id]:'-'}}
                                                <br><br>
                                            </small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="font-bold mb-2">{{__('Product Summary')}}</div>
                                    <small class="mb-2">{{__('All items here cannot be deleted.')}}</small>
                                    <div class="table-responsive mt-3">
                                        <table class="table mb-0 table-striped">
                                            <tr>
                                                <th class="text-dark" data-width="40">#</th>
                                                <th class="text-dark">{{__('Expense')}}</th>
                                                <th class="text-dark">{{__('Quantity')}}</th>
                                                <th class="text-dark">{{__('Price')}}</th>
                                                <th class="text-dark">{{__('Discount')}}</th>
                                                {{-- <th class="text-dark">{{__('Tax')}}</th> --}}
                                                <th class="text-dark">{{__('Description')}}</th>
                                                <th class="text-end text-dark" width="12%">{{__('Price')}}<br>
                                                    <small class="text-danger font-weight-bold">{{__('after discount')}}</small>
                                                </th>
                                            </tr>
                                            @php
                                                $totalQuantity=0;
                                               $totalRate=0;
                                               $totalTaxPrice=0;
                                               $totalDiscount=0;
                                               $taxesData=[];
                                            @endphp



                                            @foreach($items as $key =>$item)

                                                @if(!empty($item->product_id))
                                                    <tr>
                                                        <td>{{$key+1}}</td>

                                                        @php
                                                        $productName = $item->product;
                                                        $totalQuantity += $item->quantity;
                                                        $totalRate += $item->price;
                                                        $totalDiscount += $item->discount;
                                                    @endphp
                                                    <td>{{!empty($productName)?$productName->name:'-'}}</td>
                                                    <td>{{ $item->quantity}}</td>
                                                        <td>{{\Auth::user()->priceFormat($item->price)}}</td>
                                                        <td>{{\Auth::user()->priceFormat($item->discount)}}</td>
                                                        {{-- <td>
                                                            @if (!empty($item->tax))
                                                                <table>
                                                                    @php
                                                                        $itemTaxes = [];
                                                                        $getTaxData = Utility::getTaxData();
                                                                        $itemTaxPrice = 0;
                                                                        if (!empty($item->tax)) {
                                                                            foreach (explode(',', $item->tax) as $tax) {
                                                                                $taxPrice = \Utility::taxRate($getTaxData[$tax]['rate'], $item->price, $item->quantity);
                                                                                $totalTaxPrice += $taxPrice;
                                                                                $itemTax['name'] = $getTaxData[$tax]['name'];
                                                                                $itemTax['rate'] = $getTaxData[$tax]['rate'] . '%';
                                                                                $itemTax['price'] = \Auth::user()->priceFormat($taxPrice);

                                                                                $itemTaxes[] = $itemTax;
                                                                                if (array_key_exists($getTaxData[$tax]['name'], $taxesData)) {
                                                                                    $taxesData[$getTaxData[$tax]['name']] = $taxesData[$getTaxData[$tax]['name']] + $taxPrice;
                                                                                } else {
                                                                                    $taxesData[$getTaxData[$tax]['name']] = $taxPrice;
                                                                                }
                                                                            }
                                                                            $item->itemTax = $itemTaxes;
                                                                        } else {
                                                                            $item->itemTax = [];
                                                                        }
                                                                    @endphp
                                                                    @foreach ($item->itemTax as $tax)

                                                                            <tr>
                                                                                <td>{{$tax['name'] .' ('.$tax['rate'] .')'}}</td>
                                                                                <td>{{ $tax['price']}}</td>
                                                                            </tr>
                                                                    @endforeach
                                                                </table>
                                                            @else
                                                                @php
                                                                    $itemTaxPrice = 0;
                                                                @endphp
                                                                -
                                                            @endif
                                                        </td> --}}
                                                        <td>{{!empty($item->description)?$item->description:'-'}}</td>

                                                        <td class="text-end">{{\Auth::user()->priceFormat($item->price * $item->quantity - $item->discount)}}</td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        @php
                                                            $chartAccount = \App\Models\ChartOfAccount::find($item['chart_account_id']);
                                                        @endphp
                                                        <td>{{!empty($chartAccount) ? $chartAccount->name : '-'}}</td>
                                                        <td>{{\Auth::user()->priceFormat($item['amount'])}}</td>
                                                        <td>-</td>
                                                        <td class="text-end">{{\Auth::user()->priceFormat($item['amount'])}}</td>


                                                    </tr>

                                                @endif


                                            @endforeach
                                            <tfoot>
                                            <tr>
                                                <td></td>
                                                <td><b>{{__('Total')}}</b></td>
                                                <td><b>{{$totalQuantity}}</b></td>
                                                <td><b>{{\Auth::user()->priceFormat($totalRate)}}</b></td>
                                                <td><b>{{\Auth::user()->priceFormat($totalDiscount)}}</b></td>
                                                {{-- <td><b>{{\Auth::user()->priceFormat($totalTaxPrice)}}</b></td> --}}
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="text-center"><b>{{__('Sub Total')}}</b></td>
                                                <td class="text-end">{{\Auth::user()->priceFormat($bill->getSubTotal())}}</td>
                                            </tr>

                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="text-center"><b>{{__('Discount')}}</b></td>
                                                <td class="text-end">{{\Auth::user()->priceFormat($bill->getTotalDiscount())}}</td>
                                            </tr>

                                            @if(!empty($taxesData))
                                                @foreach($taxesData as $taxName => $taxPrice)
                                                    <tr>
                                                        <td colspan="5"></td>
                                                        <td class="text-center"><b>{{$taxName}}</b></td>
                                                        <td class="text-end">{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="blue-text text-center"><b>{{__('Total')}}</b></td>
                                                <td class="blue-text text-end">{{\Auth::user()->priceFormat($bill->getTotal())}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="text-center"><b>{{__('Paid')}}</b></td>
                                                <td class="text-end">{{\Auth::user()->priceFormat(($bill->getTotal()-$bill->getDue())-($bill->billTotalDebitNote()))}}</td>
                                            </tr>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-center"><b>{{ __('Debit Note Applied') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->billTotalDebitNote()) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-center"><b>{{ __('Debit Note Issued') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->billTotalCustomerDebitNote()) }}
                                                    </td>
                                                </tr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="text-center"><b>{{__('Due')}}</b></td>
                                                <td class="text-end">{{\Auth::user()->priceFormat($bill->getDue())}}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class=" d-inline-block mb-5">{{__('Payment Summary')}}</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-dark">{{__('Payment Receipt')}}</th>
                                <th class="text-dark">{{__('Date')}}</th>
                                <th class="text-dark">{{__('Amount')}}</th>
                                <th class="text-dark">{{__('Account')}}</th>
                                <th class="text-dark">{{__('Reference')}}</th>
                                <th class="text-dark">{{__('Description')}}</th>
                                @can('delete payment bill')
                                    <th class="text-dark">{{__('Action')}}</th>
                                @endcan
                            </tr>
                            </thead>
                            @forelse($bill->payments as $key =>$payment)
                                <tr>
                                    <td>
                                        @if(!empty($payment->add_receipt))
                                            <a href="{{asset(Storage::url('uploads/payment')).'/'.$payment->add_receipt}}" download="" class="btn btn-sm btn-secondary btn-icon" target="_blank"><span class="btn-inner--icon"><i class="ti ti-download"></i></span></a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{\Auth::user()->dateFormat($payment->date)}}</td>
                                    <td>{{\Auth::user()->priceFormat($payment->amount)}}</td>
                                    <td>{{!empty($payment->bankAccount)?$payment->bankAccount->bank_name.' '.$payment->bankAccount->holder_name:''}}</td>
                                    <td>{{$payment->reference}}</td>
                                    <td>{{$payment->description}}</td>
                                    <td class="text-dark">
                                        @can('delete bill product')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'post', 'route' => ['bill.payment.destroy',$bill->id,$payment->id],'id'=>'delete-form-'.$payment->id]) !!}
                                            <a href="#" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip"  title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();">
                                                <i class="ti ti-trash text-white text-white text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-dark"><p>{{__('No Data Found')}}</p></td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="d-inline-block mb-5">{{__('Debit Note Summary')}}</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-dark">{{__('Debit Note')}}</th>
                                <th class="text-dark">{{__('Date')}}</th>
                                <th class="text-dark">{{__('Amount')}}</th>
                                <th class="text-dark">{{__('Description')}}</th>
                                @if(Gate::check('edit debit note') || Gate::check('delete debit note'))
                                    <th class="text-dark">{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            @forelse($bill->debitNote as $key =>$debitNote)
                                <tr>
                                    <td><span class="btn btn-outline-primary">{{ !empty($debitNote->debitNote) ? \App\Models\CustomerDebitNotes::debitNumberFormat($debitNote->debitNote->debit_id) : '---'}}</span></td>
                                    <td>{{\Auth::user()->dateFormat($debitNote->date)}}</td>
                                    <td>{{\Auth::user()->priceFormat($debitNote->amount)}}</td>
                                    <td>{{$debitNote->description}}</td>
                                    <td>
                                        <div class="action-btn">
                                        @can('edit debit note')
                                            <a data-url="{{ route('bill.edit.debit.note',[$debitNote->bill,$debitNote->id]) }}" data-ajax-popup="true" data-title="{{__('Add Debit Note')}}" href="#" class="btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" data-bs-original-title="{{__('Edit')}}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        @endcan
                                        @can('delete debit note')
                                            {!! Form::open(['method' => 'DELETE', 'route' => array('bill.delete.debit.note', $debitNote->bill,$debitNote->id),'id'=>'delete-form-'.$debitNote->id]) !!}
                                            <a href="#" class="mx-2 btn btn-sm bg-danger align-items-center bs-pass-para" data-bs-toggle="tooltip"  title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$debitNote->id}}').submit();">
                                                <i class="ti ti-trash text-white text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-dark"><p>{{__('No Data Found')}}</p></td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
