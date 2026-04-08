@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Leads') }} @if ($pipeline)
        - {{ $pipeline->name }}
    @endif
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
@endpush
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        ! function(a) {
            "use strict";
            var t = function() {
                this.$body = a("body")
            };
            t.prototype.init = function() {
                a('[data-plugin="dragula"]').each(function() {
                    var t = a(this).data("containers"),
                        n = [];
                    if (t)
                        for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                    else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function(a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function(el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function() {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');

                        var old_status = $("#" + source.id).data('status');
                        var new_status = $("#" + target.id).data('status');
                        var stage_id = $(target).attr('data-id');
                        var pipeline_id = '{{ $pipeline->id }}';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div")
                            .length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div")
                            .length);
                        $.ajax({
                            url: '{{ route('leads.order') }}',
                            type: 'POST',
                            data: {
                                lead_id: id,
                                stage_id: stage_id,
                                order: order,
                                new_status: new_status,
                                old_status: old_status,
                                pipeline_id: pipeline_id,
                                "_token": $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                show_toastr(data.status,data.message,data.status);
                            },
                            error: function(data) {
                                data = data.responseJSON;
                                show_toastr(data.status, data.message, data.status)
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery),
        function(a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
    <script>
        $(document).on("change", "#default_pipeline_id", function() {
            $('#change-pipeline').submit();
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Lead') }}</li>
@endsection

@section('action-btn')
    <div class="float-end ">
        {{ Form::open(['route' => 'deals.change.pipeline', 'id' => 'change-pipeline', 'class' => 'btn btn-sm']) }}
        {{ Form::select('default_pipeline_id', $pipelines, $pipeline->id, ['class' => 'form-control select me-2', 'id' => 'default_pipeline_id']) }}
        {{ Form::close() }}

        <a href="{{ route('leads.list') }}" data-size="lg" data-bs-toggle="tooltip" title="{{ __('List View') }}"
            class="btn btn-sm bg-light-blue-subtitle me-1">
            <i class="ti ti-list"></i>
        </a>
        <a href="#" data-size="md" data-bs-toggle="tooltip" title="{{ __('Import') }}"
            data-url="{{ route('leads.import') }}" data-ajax-popup="true" data-title="{{ __('Import Lead CSV file') }}"
            class="btn btn-sm bg-brown-subtitle me-1">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{ route('leads.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            class="btn btn-sm btn-secondary me-1">
            <i class="ti ti-file-export"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('leads.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Create New Lead') }}" data-title="{{ __('Create Lead') }}"
            class="btn btn-sm btn-primary me-1">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @php
                $lead_stages = $pipeline->leadStages;
                $json = [];
                foreach ($lead_stages as $lead_stage) {
                    $json[] = 'task-list-' . $lead_stage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}'
                data-plugin="dragula">
                @foreach ($lead_stages as $lead_stage)
                    @php($leads = $lead_stage->lead())
                    <div class="col">
                        <div class="crm-sales-card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                                <h4 class="mb-0">{{ $lead_stage->name }}</h4>
                                <span class="f-w-600 count">{{ count($leads) }}</span>
                            </div>
                            <div class="sales-item-wrp" id="task-list-{{ $lead_stage->id }}"
                                data-id="{{ $lead_stage->id }}">
                                @foreach ($leads as $lead)
                                    <div class="sales-item" data-id="{{ $lead->id }}">
                                        <div class="sales-item-top border-bottom">
                                            <div class="d-flex align-items-center">
                                                <h5 class="mb-0 flex-1">
                                                    <a href="@can('view lead')@if ($lead->is_active){{ route('leads.show', $lead->id) }}@else#@endif @else#@endcan"
                                                        class="dashboard-link">{{ $lead->name }}</a>
                                                </h5>
                                                @if (Auth::user()->type != 'client')
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn p-0 border-0"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                                            @can('edit lead')
                                                                <a href="#!" data-size="md"
                                                                    data-url="{{ URL::to('leads/' . $lead->id . '/labels') }}"
                                                                    data-ajax-popup="true" class="dropdown-item"
                                                                    data-bs-original-title="{{ __('Add Labels') }}">
                                                                    <i class="ti ti-bookmark"></i>
                                                                    <span>{{ __('Labels') }}</span>
                                                                </a>

                                                                <a href="#!" data-size="lg"
                                                                    data-url="{{ URL::to('leads/' . $lead->id . '/edit') }}"
                                                                    data-ajax-popup="true" class="dropdown-item"
                                                                    data-bs-original-title="{{ __('Edit Lead') }}">
                                                                    <i class="ti ti-pencil"></i>
                                                                    <span>{{ __('Edit') }}</span>
                                                                </a>
                                                            @endcan
                                                            @can('delete lead')
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['leads.destroy', $lead->id],
                                                                    'id' => 'delete-form-' . $lead->id,
                                                                ]) !!}
                                                                <a href="#!" class="dropdown-item bs-pass-para">
                                                                    <i class="ti ti-trash"></i>
                                                                    <span> {{ __('Delete') }} </span>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endcan


                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="badge-wrp d-flex flex-wrap align-items-center gap-2">
                                                @php($labels = $lead->labels())
                                                @if ($labels)
                                                    @foreach ($labels as $label)
                                                        <div
                                                            class="badge-xs badge bg-light-{{ $label->color }} p-2 rounded text-md f-w-600">
                                                            {{ $label->name }}</div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <?php
                                        $products = $lead->products();
                                        $sources = $lead->sources();
                                        ?>
                                        <div class="sales-item-bottom d-flex align-items-center justify-content-between">
                                            <ul class="d-flex flex-wrap align-items-center gap-2 p-0 m-0">
                                                <li class="d-inline-flex align-items-center gap-1 p-1 px-2 border rounded-1"
                                                    data-bs-toggle="tooltip" title="{{ __('Product') }}">
                                                    <i class="f-16 ti ti-shopping-cart"></i> {{ count($products) }}
                                                </li>
                                                <li class="d-inline-flex align-items-center gap-1 p-1 px-2 border rounded-1"
                                                    data-bs-toggle="tooltip" title="{{ __('Source') }}">
                                                    <i class="f-16 ti ti-social"></i>{{ count($sources) }}
                                                </li>
                                            </ul>
                                            <div class="user-group">
                                                @foreach ($lead->users as $user)
                                                    <img src="@if ($user->avatar) {{ asset('/storage/uploads/avatar/' . $user->avatar) }} @else {{ asset('storage/uploads/avatar/avatar.png') }} @endif"
                                                        alt="image" data-bs-toggle="tooltip"
                                                        title="{{ $user->name }}">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
