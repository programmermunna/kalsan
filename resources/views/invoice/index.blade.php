@extends('layouts.admin')
@section('page-title')
    {{__('Manage License')}}
@endsection
@push('script-page')
    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            //
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>
@endpush


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('License')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">

        <a href="{{ route('invoice.export') }}" class="btn btn-sm btn-secondary me-1" data-bs-toggle="tooltip" title="{{__('Export')}}">
            <i class="ti ti-file-export"></i>
        </a>

        @can('create invoice')
            <a href="{{ route('invoice.create', 0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection



@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['invoice.index'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                        <div class="row d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('issue_date', __('Sales Date'), ['class' => 'form-label'])}}
                                    {{ Form::date('issue_date', isset($_GET['issue_date']) ? $_GET['issue_date'] : '', array('class' => 'form-control month-btn', 'id' => 'pc-daterangepicker-1', 'placeholder' => __('Issue Date'))) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('user', __('User'), ['class' => 'form-label'])}}
                                    {{ Form::select('user', $users, isset($_GET['user']) ? $_GET['user'] : '', ['class' => 'form-control select']) }} </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('customer', __('Customer'), ['class' => 'form-label'])}}
                                    {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('status', __('Status'), ['class' => 'form-label'])}}
                                    {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', array('class' => 'form-control select')) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary me-1"
                                onclick="document.getElementById('customer_submit').submit(); return false;"
                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}" >
                                 <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                data-original-title="{{ __('Reset') }}">
                                 <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                             </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('#') }}</th>
                                    <th> {{ __('License No') }}</th>
                                    <th> {{ __('Customer') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('User') }}</th>
                                    @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($invoices->items() as $index => $invoice)
                                    <tr>
                                        <td>{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $index + 1 }}</td>
                                        <td class="Id">
                                            <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                        </td>
                                        <td>{{ !empty($invoice->customer) ? $invoice->customer->name : '-' }}</td>
                                        <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                        <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                        {{-- <td>
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                <p class="text-danger mt-3">
                                                    {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                            @else
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                            @endif
                                        </td> --}}
                                        <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>

                                        <td>
                                            @if ($invoice->status == 0)
                                                <span
                                                    class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span
                                                    class="status_badge badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span
                                                    class="status_badge badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span
                                                    class="status_badge badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span
                                                    class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 5)
                                                <span
                                                    class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ !empty($invoice->user) ? $invoice->user->name : '-' }}</td>

                                        @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                            <td class="Action">
                                                <span>
                                                    @php $invoiceID = Crypt::encrypt($invoice->id); @endphp

                                                        @can('copy invoice')
                                                        <div class="action-btn  me-2">
                                                            <a href="#" id="{{ route('invoice.link.copy', [$invoiceID]) }}"
                                                               class="mx-3 btn bg-secondary btn-sm align-items-center"   onclick="copyToClipboard(this)"
                                                               data-bs-toggle="tooltip" title="{{__('Copy Invoice')}}" data-original-title="{{__('Copy Invoice')}}"><i class="ti ti-link text-white"></i></a>
                                                        </div>
                                                        @endcan
                                                        @can('duplicate invoice')
                                                        <div class="action-btn  me-2">
                                                            {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id], 'id' => 'duplicate-form-' . $invoice->id]) !!}
                                                             <a href="#" class="mx-3 bg-success btn btn-sm align-items-center bs-pass-para" data-toggle="tooltip"
                                                                data-original-title="{{ __('Duplicate') }}" data-bs-toggle="tooltip" title="Duplicate Invoice"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                                data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
                                                                 <i class="ti ti-copy text-white"></i>
                                                                 {!! Form::close() !!}
                                                             </a>
                                                         </div>
                                                        @endcan
                                                        @can('show invoice')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                                class="mx-3 btn bg-warning btn-sm align-items-center" data-bs-toggle="tooltip" title="Show "
                                                                data-original-title="{{ __('Detail') }}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('edit invoice')
                                                            @if ($invoice->status != 3 && $invoice->status != 4)
                                                                <div class="action-btn  me-2">
                                                                    <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                    class="mx-3 btn bg-info btn-sm align-items-center" data-bs-toggle="tooltip" title="Edit "
                                                                    data-original-title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil text-white"></i>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                        @can('delete invoice')
                                                        <div class="action-btn me-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id], 'id' => 'delete-form-' . $invoice->id]) !!}
                                                            <a href="#" class="mx-3  bg-danger btn btn-sm align-items-center bs-pass-para " data-bs-toggle="tooltip" title="{{__('Delete')}}"
                                                               data-original-title="{{ __('Delete') }}"
                                                               data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                               data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                        @endcan
                                                    </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="dataTables_info">
                    Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} entries
                </div>
                <div class="dataTables_paginate paging_simple_numbers">
                    {{ $invoices->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
