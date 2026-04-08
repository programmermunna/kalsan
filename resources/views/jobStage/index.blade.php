@extends('layouts.admin')
@push('script-page')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    @if (\Auth::user()->type == 'company')
        <script>
            $(function() {
                $(".sortable").sortable();
                $(".sortable").disableSelection();
                $(".sortable").sortable({
                    stop: function() {
                        var order = [];
                        $(this).find('li').each(function(index, data) {
                            order[index] = $(data).attr('data-id');
                        });

                        $.ajax({
                            url: "{{ route('job.stage.order') }}",
                            data: {
                                order: order,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            success: function(data) {},
                            error: function(data) {
                                data = data.responseJSON;
                                toastr('Error', data.error, 'error')
                            }
                        })
                    }
                });
            });
        </script>
    @endif
@endpush
@section('page-title')
    {{ __('Manage Job Stage') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Job Stage') }}</li>
@endsection

@section('action-btn')

@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @include('layouts.hrm_setup')
        </div>
        <div class="col-12">
            <div class="my-3 d-flex justify-content-end">
                @can('create job stage')
                    <a href="#" data-url="{{ route('job-stage.create') }}" data-ajax-popup="true"
                        data-title="{{ __('Create New Job Stage') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content tab-bordered">
                                <div class="tab-pane fade show active" role="tabpanel">
                                    <ul class="list-unstyled list-group sortable stage">
                                        @foreach ($stages as $stage)
                                            <li class="d-flex align-items-center justify-content-between list-group-item"
                                                data-id="{{ $stage->id }}">
                                                <h6 class="mb-0">
                                                    <i class="me-3 ti ti-arrows-maximize " data-feather="move"></i>
                                                    <span>{{ $stage->title }}</span>
                                                </h6>
                                                <span class="float-end">
                                                    @can('edit job stage')
                                                        <div class="action-btn me-2">
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                                data-url="{{ route('job-stage.edit', $stage->id) }}"
                                                                data-ajax-popup="true" data-title="{{ __('Edit Job Stage') }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('delete job stage')
                                                        <div class="action-btn ">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['job-stage.destroy', $stage->id],
                                                                'id' => 'delete-form-' . $stage->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <p class=" mt-4"><strong>{{ __('Note') }} :
                                </strong><b>{{ __('You can easily change order of job stage using drag & drop.') }}</b>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
