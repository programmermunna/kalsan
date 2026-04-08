@extends('layouts.admin')

@section('page-title')
    {{ucwords($project->project_name).__("'s Expenses")}}
@endsection

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('projects.index')}}">{{__('Project')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('projects.show',$project->id)}}">    {{ucwords($project->project_name)}}</a></li>
    <li class="breadcrumb-item">{{ucwords($project->project_name).__("'s Expenses")}}</li>

@endsection

@section('action-btn')
    <div class="float-end">
        @can('create project expense')
            <a href="#" class="btn btn-primary btn-sm" data-url="{{ route('projects.expenses.create',$project->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-size="lg" data-title="{{__('Create Expense')}}">
                <span class="btn-inner--icon"><i class="ti ti-plus"></i></span>
            </a>
        @endcan
        <a href="{{ route('projects.show',$project->id) }}" class="btn bg-brown-subtitle text-white btn-sm" data-bs-toggle="tooltip" title="{{__('Back')}}">
            <span class="btn-inner--icon"><i class="ti ti-arrow-left"></i></span>
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
                            <th>{{__('Attachment')}}</th>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Date')}}</th>
                            <th>{{__('Amount')}}</th>
                            @if(Gate::check('edit project expense') || Gate::check('delete project expense'))
                                <th class="text-end">{{__('Action')}}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody class="">
                            @if(isset($project->expense) && !empty($project->expense) && count($project->expense) > 0)
                                @foreach($project->expense as $expense)
                                    <tr>
                                        <th>
                                            @if(!empty($expense->attachment))
                                                <a href="{{ asset(Storage::url($expense->attachment)) }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" title="{{__('Download')}}" download>
                                                    <span class="btn-inner--icon"><i class="ti ti-download text-white"></i></span>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </th>
                                        <td>
                                            <span class="h6 text-sm font-weight-bold mb-0">{{ $expense->name }}</span>
                                            @if(!empty($expense->task))<span class="d-block text-sm text-muted">{{ $expense->task->name }}</span>@endif
                                        </td>
                                        <td>{{ (!empty($expense->date)) ? Utility::getDateFormated($expense->date) : '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                        @if(Gate::check('edit project expense') || Gate::check('delete project expense'))
                                            <td class="text-end">
                                                <div class="actions">
                                                    @can('edit project expense')

                                                    <div class="action-btn  ms-2">
                                                        <a href="#" data-url="{{ route('projects.expenses.edit',[$project->id,$expense->id]) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit project Expense')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}" class="mx-3 btn btn-sm align-items-center bg-info">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete project expense')
                                                    <div class="action-btn  ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['projects.expenses.destroy',$expense->id],'id'=>'delete-expense-'.$expense->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm-yes="document.getElementById('delete-expense-{{$expense->id}}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan

                                                </div>
                                                {!! Form::close() !!}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="5"><h6 class="text-center">{{__('No Expense Found.')}}</h6></th>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection
