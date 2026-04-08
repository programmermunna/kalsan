@extends('layouts.admin')
@section('page-title')
    {{__('Manage Debit Notes')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Debit Note')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create debit note')
            <a href="#" data-url="{{ route('create.custom.debit.note') }}"data-bs-toggle="tooltip" title="{{__('Create')}}" data-ajax-popup="true" data-title="{{__('Create New Debit Note')}}" class="btn btn-sm btn-primary">
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
                                <th> {{__('Debit Note')}}</th>
                                <th> {{__('Bill')}}</th>
                                <th> {{__('Date')}}</th>
                                <th> {{__('Amount')}}</th>
                                <th> {{__('Description')}}</th>
                                <th> {{__('Status')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($customDebitNotes as $debitNote)
                                    <tr>
                                        <td class="Id">
                                            <a href="#" class="btn btn-outline-primary">{{ \App\Models\CustomerDebitNotes::debitNumberFormat($debitNote->debit_id) }}</a>
                                        </td>
                                        <td class="Id">
                                            <a href="{{ route('bill.show',\Crypt::encrypt($debitNote->bill)) }}" class="btn btn-outline-primary">{{ \Auth::user()->billNumberFormat($debitNote->bills->bill_id) }}</a>
                                        </td>
                                        <td>{{ Auth::user()->dateFormat($debitNote->date) }}</td>
                                        <td>{{ Auth::user()->priceFormat($debitNote->amount) }}</td>
                                        <td>{{!empty($debitNote->description)?$debitNote->description:'-'}}</td>
                                        <td>
                                        @if ($debitNote->status == 0)
                                            <span
                                                class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\CustomerDebitNotes::$statues[$debitNote->status]) }}</span>
                                        @elseif($debitNote->status == 1)
                                            <span
                                                class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\CustomerDebitNotes::$statues[$debitNote->status]) }}</span>
                                        @elseif($debitNote->status == 2)
                                            <span
                                                class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\CustomerDebitNotes::$statues[$debitNote->status]) }}</span>
                                        @endif
                                    </td>
                                        <td>
                                            @can('edit debit note')
                                                <div class="action-btn me-2">
                                                    <a data-url="{{ route('bill.edit.custom-debit',[$debitNote->bill,$debitNote->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Debit Note')}}" href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete debit note')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => array('bill.custom-note.delete', $debitNote->bill,$debitNote->id),'class'=>'delete-form-btn','id'=>'delete-form-'.$debitNote->id]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$debitNote->id}}').submit();">
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
            url: "{{route('debit-bill.itemprice')}}",
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