@php
    $path =\App\Models\Utility::get_file('uploads/order');
@endphp
{{ Form::open(['route' => ['invoice.changestatus',$invoiceBankTransfer->id],'method' => 'post' ]) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table">
                <tr>
                    <th>{{__('Invoice Number')}}</th>
                    <td>{{\Auth::user()->invoiceNumberFormat($invoice->invoice_id)}}</td>

                </tr>
                <tr >
                    <th>{{__('Order Id')}}</th>
                    <td>{{$invoiceBankTransfer->order_id}}</td>
                </tr>
                <tr>
                    <th>{{__('Amount')}}</th>
                    <td>{{$invoiceBankTransfer->amount}}</td>
                </tr>
                <tr>
                    <th>{{__('Payment Type')}}</th>
                    <td>{{__('Bank Transfer')}}</td>
                </tr>
                <tr>
                    <th>{{__('Payment Status')}}</th>
                    <td>{{$invoiceBankTransfer->status}}</td>
                </tr>
                <tr>
                    <th>{{__('Bank Details')}}</th>
                    <td>{!! $company_payment_setting['bank_details'] !!}</td>
                </tr>
                @if(!empty( $invoiceBankTransfer->receipt))
                    <tr>
                        <th>{{__('Payment Receipt')}}</th>
                        <td>
                            <div class="action-btn">
                                <a  class="bg-primary ms-2 btn btn-sm align-items-center" href="{{ $path . '/' . $invoiceBankTransfer->receipt }}" download=""  data-bs-toggle="tooltip" title="{{__('Download')}}" target="_blank">
                                    <i class="ti ti-download text-white"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
                <input type="hidden" value="{{ $invoiceBankTransfer->id }}" name="order_id">
            </table>
        </div>
    </div>

</div>
<div class="modal-footer">
    <input type="submit" value="{{__('Approval')}}" class="btn btn-success" data-bs-dismiss="modal" name="status">
    <input type="submit" value="{{__('Reject')}}" class="btn btn-danger" name="status">
</div>
{{Form::close()}}
