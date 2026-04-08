@extends('layouts.admin')
@section('page-title')
    {{__('Manage Credit Notes')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Credit Note')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create credit note')
            <a href="#" data-url="{{ route('create.custom.credit.note') }}"data-bs-toggle="tooltip" title="{{__('Create')}}" data-ajax-popup="true" data-title="{{__('Create New Credit Note')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style mt-2">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{__('Credit Note')}}</th>
                                <th> {{__('Invoice')}}</th>
                                <th> {{__('Date')}}</th>
                                <th> {{__('Amount')}}</th>
                                <th> {{__('Description')}}</th>
                                <th> {{__('Status')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($customcreditNotes as $creditNote)
                                    <tr>
                                        <td class="Id">
                                            <a href="#" class="btn btn-outline-primary">{{ \App\Models\CustomerCreditNotes::creditNumberFormat($creditNote->credit_id) }}</a>
                                        </td>
                                        <td class="Id">
                                            <a href="{{ route('invoice.show',\Crypt::encrypt($creditNote->invoice)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($creditNote->invoices->invoice_id) }}</a>
                                        </td>
                                        <td>{{ Auth::user()->dateFormat($creditNote->date) }}</td>
                                        <td>{{ Auth::user()->priceFormat($creditNote->amount) }}</td>
                                        <td>{{!empty($creditNote->description)?$creditNote->description:'-'}}</td>
                                        <td>
                                        @if ($creditNote->status == 0)
                                            <span
                                                class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\CustomerCreditNotes::$statues[$creditNote->status]) }}</span>
                                        @elseif($creditNote->status == 1)
                                            <span
                                                class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\CustomerCreditNotes::$statues[$creditNote->status]) }}</span>
                                        @elseif($creditNote->status == 2)
                                            <span
                                                class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\CustomerCreditNotes::$statues[$creditNote->status]) }}</span>
                                        @endif
                                    </td>
                                        <td>
                                            @can('edit credit note')
                                                <div class="action-btn me-2">
                                                    <a data-url="{{ route('invoice.edit.custom-credit',[$creditNote->invoice,$creditNote->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Credit Note')}}" href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete credit note')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => array('invoice.custom-note.delete', $creditNote->invoice,$creditNote->id),'class'=>'delete-form-btn','id'=>'delete-form-'.$creditNote->id]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-page')
<script>
    $(document).on('click' , '#item' , function(){
        var item_id = $(this).val();
        $.ajax({
            url: "{{route('credit-invoice.itemprice')}}",
            method:'POST',
            data: {
                "item_id": item_id, 
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                if (data !== undefined) {
                    $('#amount').val(data);
                    $('input[name="amount"]').attr('min', 0);
                }
            }
        });        
    });
</script>
@endpush