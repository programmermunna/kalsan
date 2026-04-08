@extends('layouts.admin')
@section('page-title')
    {{__('Product Stock')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Product Stock')}}</li>
@endsection


@section('action-btn')
    <div class="float-end">
        <a href="{{ route('productstock.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
           class="btn btn-sm btn-secondary">
            <i class="ti ti-file-export"></i>
        </a>


    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Product Name')}}</th>
                                <th>{{__('Quantity')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Description')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td class="font-style">{{$stock->created_at->format('d M Y')}}</td>
                                    <td>{{ !empty($stock->product) ? $stock->product->name : '' }}
                                    <td class="font-style">{{ $stock->quantity }}</td>
                                    <td>
                                        @if ($stock->type == "manually")
                                            <span class="status_badge badge bg-secondary p-2 px-3 rounded">{{ ucfirst($stock->type) }}</span>
                                        @elseif($stock->type == "invoice")
                                            <span class="status_badge badge bg-warning p-2 px-3 rounded">{{ ucfirst($stock->type) }}</span>
                                        @elseif($stock->type == "bill")
                                            <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ ucfirst($stock->type) }}</span>
                                        @elseif($stock->type == "purchase")
                                            <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ ucfirst($stock->type) }}</span>
                                        @elseif($stock->type == "pos")
                                            <span class="status_badge badge bg-info p-2 px-3 rounded">{{ ucfirst($stock->type) }}</span>
                                        @endif
                                    </td>
                                    <td class="font-style">{{$stock->description}}</td>

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

