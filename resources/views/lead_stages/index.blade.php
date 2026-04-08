@extends('layouts.admin')
@section('page-title')
    {{__('Manage Lead Stages')}}
@endsection
@push('script-page')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        $(function () {
            $(".sortable").sortable();
            $(".sortable").disableSelection();
            $(".sortable").sortable({
                stop: function () {
                    var order = [];
                    $(this).find('li').each(function (index, data) {
                        order[index] = $(data).attr('data-id');
                    });

                    $.ajax({
                        url: "{{route('lead_stages.order')}}",
                        data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        success: function (data) {
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('error', data.error, 'error')
                        }
                    })
                }
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead Stage')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('lead_stages.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Lead Stage')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')

    <div class="row">
        @include('layouts.crm_setup')
        <div class="col-lg-9">
            <div class="row justify-content-center">
                <div class="p-3 card">
                    <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                        @php($i=0)
                        @foreach($pipelines as $key => $pipeline)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($i==0) active @endif" id="pills-user-tab-1" data-bs-toggle="pill"
                                        data-bs-target="#tab{{$key}}" type="button">{{$pipeline['name']}}
                                </button>
                            </li>
                            @php($i++)
                        @endforeach
                    </ul>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            @php($i=0)
                            @foreach($pipelines as $key => $pipeline)
                                <div class="tab-pane fade show @if($i==0) active @endif" id="tab{{$key}}" role="tabpanel" aria-labelledby="pills-user-tab-1">
                                    <ul class="list-unstyled list-group sortable stage">
                                        @foreach ($pipeline['lead_stages'] as $lead_stages)
                                            <li class="d-flex align-items-center justify-content-between list-group-item" data-id="{{$lead_stages->id}}">
                                                <h6 class="mb-0">
                                                    <i class="me-3 ti ti-arrows-maximize " data-feather="move"></i>
                                                    <span>{{$lead_stages->name}}</span>
                                                </h6>
                                                <span class="float-end">
                                                    @can('edit lead stage')
                                                    <div class="action-btn me-2"><a href="#" class="mx-3 bg-info btn btn-sm  align-items-center" data-url="{{ URL::to('lead_stages/'.$lead_stages->id.'/edit') }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Lead Stages')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                    </div>
                                                    @endcan
                                                    @if(count($pipeline['lead_stages']))
                                                        @can('delete lead stage')
                                                        <div class="action-btn ">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['lead_stages.destroy', $lead_stages->id]]) !!}
                                                            <a href="#" class="mx-3 bg-danger  btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                        @endcan
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @php($i++)
                            @endforeach
                        </div>
                        <p class=" mt-4"><strong>{{__('Note')}} : </strong><b>{{__('You can easily change order of lead stage using drag & drop.')}}</b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
