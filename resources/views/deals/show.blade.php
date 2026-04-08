@extends('layouts.admin')
@section('page-title')
    {{ $deal->name }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#deal-sidenav',
            offset: 300
        })
        Dropzone.autoDiscover = false;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 20,
            parallelUploads: 1,
            filename: false,
            // acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
            url: "{{ route('deals.file.upload', $deal->id) }}",
            success: function(file, response) {
                if (response.is_success) {
                    if (response.status == 1) {
                        show_toastr('success', response.success_msg, 'success');
                    }
                    dropzoneBtn(file, response);
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("deal_id", {{ $deal->id }});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "badge bg-info mx-1");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{ __('Download') }}");
            download.innerHTML = "<i class='ti ti-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "badge bg-danger mx-1");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{ __('Delete') }}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        success: function(response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('error', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('error', response.error, 'error');
                            } else {
                                show_toastr('error', response, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.appendChild(download);
            @if (Auth::user()->type != 'client')
                @can('edit deal')
                    html.appendChild(del);
                @endcan
            @endif

            file.previewTemplate.appendChild(html);
        }

        @foreach ($deal->files as $file)
            @if (file_exists(storage_path('deal_files/' . $file->file_path)))
                // Create the mock file:
                var mockFile = {
                    name: "{{ $file->file_name }}",
                    size: {{ \File::size(storage_path('deal_files/' . $file->file_path)) }}
                };
                // Call the default addedfile event handler
                myDropzone.emit("addedfile", mockFile);
                // And optionally show the thumbnail of the file:
                myDropzone.emit("thumbnail", mockFile, "{{ asset(Storage::url('deal_files/' . $file->file_path)) }}");
                myDropzone.emit("complete", mockFile);

                dropzoneBtn(mockFile, {
                    download: "{{ route('deals.file.download', [$deal->id, $file->id]) }}",
                    delete: "{{ route('deals.file.delete', [$deal->id, $file->id]) }}"
                });
            @endif
        @endforeach

        @can('edit deal')
            $('.summernote-simple').on('summernote.blur', function() {

                $.ajax({
                    url: "{{ route('deals.note.store', $deal->id) }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        notes: $(this).val()
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.is_success) {
                            // show_toastr('Success', response.success,'success');
                        } else {
                            show_toastr('error', response.error, 'error');
                        }
                    },
                    error: function(response) {
                        response = response.responseJSON;
                        if (response.is_success) {
                            show_toastr('error', response.error, 'error');
                        } else {
                            show_toastr('error', response, 'error');
                        }
                    }
                })
            });
        @else
            $('.summernote-simple').summernote('disable');
        @endcan

        @can('edit task')
            $(document).on("click", ".task-checkbox", function() {
                var chbox = $(this);
                var lbl = chbox.parent().parent().find('label');

                $.ajax({
                    url: chbox.attr('data-url'),
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: chbox.val()
                    },
                    type: 'PUT',
                    success: function(response) {
                        if (response.is_success) {
                            chbox.val(response.status);
                            if (response.status) {
                                lbl.addClass('strike');
                                lbl.find('.badge').removeClass('badge-warning').addClass(
                                    'badge-success');
                            } else {
                                lbl.removeClass('strike');
                                lbl.find('.badge').removeClass('badge-success').addClass(
                                    'badge-warning');
                            }
                            lbl.find('.badge').html(response.status_label);

                            show_toastr('success', response.success);
                        } else {
                            show_toastr('error', response.error);
                        }
                    },
                    error: function(response) {
                        response = response.responseJSON;
                        if (response.is_success) {
                            show_toastr('success', response.success);
                        } else {
                            show_toastr('error', response.error);
                        }
                    }
                })
            });
        @endcan
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}">{{ __('Deal') }}</a></li>
    <li class="breadcrumb-item"> {{ $deal->name }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('convert deal to deal')
            @if (!empty($deal))
                <a href="@can('View Deal') @if ($deal->is_active) {{ route('deals.show', $deal->id) }} @else # @endif @else # @endcan"
                    data-size="lg" data-bs-toggle="tooltip" title=" {{ __('Already Converted To Deal') }}"
                    class="btn btn-sm bg-warning-subtle me-1">
                    <i class="ti ti-exchange"></i>
                </a>
            @else
                <a href="#" data-size="lg" data-url="{{ URL::to('deals/' . $deal->id . '/show_convert') }}"
                    data-bs-toggle="tooltip" title="{{ __('Convert [' . $deal->subject . '] To Deal') }}"
                    class="btn btn-sm bg-warning-subtle me-1">
                    <i class="ti ti-exchange"></i>
                </a>
            @endif
        @endcan
        <a href="#" data-url="{{ URL::to('deals/' . $deal->id . '/labels') }}" data-ajax-popup="true" data-size="lg"
            data-bs-toggle="tooltip" title="{{ __('Label') }}" class="btn btn-sm btn-primary me-1">
            <i class="ti ti-bookmark"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('deals.edit', $deal->id) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Edit') }}" class="btn btn-sm btn-info me-1">
            <i class="ti ti-pencil"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="deal-sidenav">

                            <a href="#general" class="list-group-item list-group-item-action border-0">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#tasks" class="list-group-item list-group-item-action border-0">{{ __('Task') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#users_products"
                                class="list-group-item list-group-item-action border-0">{{ __('Users') . ' | ' . __('Products') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#sources_emails"
                                class="list-group-item list-group-item-action border-0">{{ __('Sources') . ' | ' . __('Emails') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#discussion_note"
                                class="list-group-item list-group-item-action border-0">{{ __('Discussion') . ' | ' . __('Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#files" class="list-group-item list-group-item-action border-0">{{ __('Files') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#calls" class="list-group-item list-group-item-action border-0">{{ __('Calls') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#activity"
                                class="list-group-item list-group-item-action border-0">{{ __('Activity') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <?php
                    $tasks = $deal->tasks;
                    $products = $deal->products();
                    $sources = $deal->sources();
                    $calls = $deal->calls;
                    $emails = $deal->emails;
                    ?>
                    <div id="general" class="row">
                        <div class="col-xxl-3 col-sm-6 col-12 mb-4">
                            <div class="card report-card h-100 mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <i class="ti ti-test-pipe text-white fs-4"></i>
                                    </div>
                                    <div class="report-info flex-1">
                                        <h5 class="mb-1">{{ __('Pipeline') }}</h5>
                                        <p class="text-muted mb-0">{{ $deal->pipeline->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 mb-4">
                            <div class="card report-card h-100 mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <i class="ti ti-server text-white fs-4"></i>
                                    </div>
                                    <div class="report-info flex-1">
                                        <h5 class="mb-1">{{ __('Stage') }}</h5>
                                        <p class="text-muted mb-0">{{ $deal->stage->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 mb-4">
                            <div class="card report-card h-100 mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <i class="ti ti-calendar text-white fs-4"></i>
                                    </div>
                                    <div class="report-info flex-1">
                                        <h5 class="mb-1">{{ __('Created') }}</h5>
                                        <p class="text-muted mb-0">{{ \Auth::user()->dateFormat($deal->created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 mb-4">
                            <div class="card report-card h-100 mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="report-icon">
                                        <i class="ti ti-test-pipe text-white fs-4"></i>
                                    </div>
                                    <div class="report-info flex-1">
                                        <h5 class="mb-1">{{ __('Price') }}</h5>
                                        <p class="text-muted mb-0">{{ \Auth::user()->priceFormat($deal->price) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-3 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-subtask text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info">
                                    <h5 class="mb-2">{{ __('Task') }}</h5>
                                    <span class="h3">{{ count($tasks) }}</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-shopping-cart text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info">
                                    <h5 class="mb-2">{{ __('Product') }}</h5>
                                    <span class="h3">{{ count($products) }}</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-social text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info">
                                    <h5 class="mb-2">{{ __('Source') }}</h5>
                                    <span class="h3">{{ count($sources) }}</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6 col-12 leave-card mb-4">
                            <div class="leave-card-inner d-flex align-items-center gap-3">
                                <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                        fill="#FF3A6E"></path>
                                </svg>
                                <div class="leave-icon">
                                    <div class="leave-icon-inner">
                                        <i class="ti ti-file text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="leave-info">
                                    <h5 class="mb-2">{{ __('Files') }}</h5>
                                    <span class="h3">{{ count($deal->files) }}</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div id="tasks" class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>{{ __('Tasks') }}</h5>
                                @can('create task')
                                    <div class="float-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('deals.tasks.create', $deal->id) }}" data-ajax-popup="true"
                                            data-bs-toggle="tooltip" title="{{ __('Add Task') }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                @endcan

                            </div>
                        </div>
                        <div class="card-body">
                            @if (!$tasks->isEmpty())
                                <ul class="list-group list-group-flush mt-2">
                                    @foreach ($tasks as $task)
                                        <li class="list-group-item px-0">
                                            <div class="d-block d-sm-flex align-items-start">
                                                <div
                                                    class="form-check form-switch form-switch-right img-fluid me-3 mb-2 mb-sm-0">
                                                    <input class="form-check-input task-checkbox" type="checkbox"
                                                        role="switch" id="task_{{ $task->id }}"
                                                        @if ($task->status) checked="checked" @endcan value="{{ $task->status }}" data-url="{{ route('deals.tasks.update_status', [$deal->id, $task->id]) }}">
                                                    <label class="form-check-label pe-5" for="task_{{ $task->id }}"></label>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h5 class="mb-0">
                                                                {{ $task->name }}
                                                                @if ($task->status)
                                                                    <div class="badge bg-primary mb-1">{{ __(\App\Models\DealTask::$status[$task->status]) }}</div>
                                                                @else
                                                                    <div class="badge bg-warning mb-1">{{ __(\App\Models\DealTask::$status[$task->status]) }}</div> @endif
                                                        </h5>
                                                    <small
                                                        class="text-sm">{{ __(\App\Models\DealTask::$priorities[$task->priority]) }}
                                                        - {{ Auth::user()->dateFormat($task->date) }}
                                                        {{ Auth::user()->timeFormat($task->time) }}</small>
                                                    <span class="text-muted text-sm">
                                                        @if ($task->status)
                                                            <div class="badge status_badge badge-pill badge-success mb-1">
                                                                {{ __(\App\Models\DealTask::$status[$task->status]) }}
                                                            </div>
                                                        @else
                                                            <div class="badge status_badge badge-pill badge-warning mb-1">
                                                                {{ __(\App\Models\DealTask::$status[$task->status]) }}
                                                            </div>
                                                        @endif
                                                    </span>
                                                </div>
                                                <span>
                                                    @can('edit task')
                                                        <div class="action-btn  me-2">
                                                            <a href="#" class="bg-info btn btn-sm mx-3"
                                                                data-title="{{ __('Edit Task') }}"
                                                                data-url="{{ route('deals.tasks.edit', [$deal->id, $task->id]) }}"
                                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"><i
                                                                    class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('delete task')
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.tasks.destroy', $deal->id, $task->id]]) !!}
                                                            <a href="#"
                                                                class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </span>
                                            </div>
                        </div>
                    </div>
                    </li>
                    @endforeach
                    </ul>
                @else
                    <div class="text-center">
                        {{ __('No Tasks Available.!') }}
                    </div>
                    @endif
                </div>
            </div>
            <div id="users_products">
                <div class="row">
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Users') }}</h5>

                                    <div class="float-end">
                                        <a data-size="md" data-url="{{ route('deals.users.edit', $deal->id) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add User') }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deal->users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <img @if ($user->avatar) src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}" @else src="{{ asset('/storage/uploads/avatar/avatar.png') }}" @endif
                                                                    class="wid-40 rounded border-2 border border-primary me-3">
                                                            </div>
                                                            <p class="mb-0">{{ $user->name }}</p>
                                                        </div>
                                                    </td>
                                                    @can('edit deal')
                                                        <td>
                                                            <div class="action-btn">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['deals.users.destroy', $deal->id, $user->id],
                                                                    'id' => 'delete-form-' . $deal->id,
                                                                ]) !!}
                                                                <a href="#"
                                                                    class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                        class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Products') }}</h5>

                                    <div class="float-end">
                                        <a data-size="md" data-url="{{ route('deals.products.edit', $deal->id) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            title="{{ __('Add Product') }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deal->products() as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>
                                                        {{ \Auth::user()->priceFormat($product->sale_price) }}
                                                    </td>
                                                    @can('edit deal')
                                                        <td>
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['deals.products.destroy', $deal->id, $product->id]]) !!}
                                                                <a href="#"
                                                                    class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                        class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sources_emails">
                <div class="row">
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Sources') }}</h5>

                                    <div class="float-end">
                                        <a data-size="md" data-url="{{ route('deals.sources.edit', $deal->id) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            title="{{ __('Add Source') }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sources as $source)
                                                <tr>
                                                    <td>{{ $source->name }} </td>
                                                    @can('edit deal')
                                                        <td>
                                                            <div class="action-btn">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['deals.sources.destroy', $deal->id, $source->id],
                                                                    'id' => 'delete-form-' . $deal->id,
                                                                ]) !!}
                                                                <a href="#"
                                                                    class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                        class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Emails') }}</h5>
                                    @can('create deal email')
                                        <div class="float-end">
                                            <a data-size="lg" data-url="{{ route('deals.emails.create', $deal->id) }}"
                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                title="{{ __('Create Email') }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus"></i>
                                            </a>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush mt-2">
                                    @if (!$emails->isEmpty())
                                        @foreach ($emails as $email)
                                            <li class="list-group-item px-0">
                                                <div class="d-block d-sm-flex align-items-start">
                                                    <img src="{{ asset('/storage/uploads/avatar/avatar.png') }}"
                                                        class="rounded border-2 border border-primary wid-40 me-3 mb-2 mb-sm-0"
                                                        alt="image">
                                                    <div class="w-100">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h5 class="mb-0">{{ $email->subject }}</h5>
                                                                <span
                                                                    class="text-muted text-sm">{{ $email->to }}</span>
                                                            </div>
                                                            <div class="form-check form-switch form-switch-right mb-2">
                                                                {{ $email->created_at->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-center">
                                            {{ __(' No Emails Available.!') }}
                                        </li>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="discussion_note">
                <div class="row">
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Discussion') }}</h5>

                                    <div class="float-end">
                                        <a data-size="lg" data-url="{{ route('deals.discussions.create', $deal->id) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            title="{{ __('Add Message') }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush mt-2">
                                    @if (!$deal->discussions->isEmpty())
                                        @foreach ($deal->discussions as $discussion)
                                            <li class="list-group-item px-0">
                                                <div class="d-block d-sm-flex align-items-start">
                                                    <img src="@if ($discussion->user->avatar) {{ asset('/storage/uploads/avatar/' . $discussion->user->avatar) }} @else {{ asset('/storage/uploads/avatar/avatar.png') }} @endif"
                                                        class="rounded border-2 border border-primary wid-40 me-3 mb-2 mb-sm-0"
                                                        alt="image">
                                                    <div class="w-100">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h5 class="mb-0"> {{ $discussion->comment }}</h5>
                                                                <span
                                                                    class="text-muted text-sm">{{ $discussion->user->name }}</span>
                                                            </div>
                                                            <div class=" form-switch form-switch-right mb-4">
                                                                {{ $discussion->created_at->diffForHumans() }}
                                                            </div>



                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-center">
                                            {{ __(' No Data Available.!') }}
                                        </li>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-4">
                        <div class="card h-100 mb-0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>{{ __('Notes') }}</h5>
                                    @php
                                        $settings = \App\Models\Utility::settings();
                                    @endphp
                                    @if ($settings['ai_chatgpt_enable'] == 'on')
                                        <div class="float-end">
                                            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm"
                                                data-ajax-popup-over="true" data-url="{{ route('generate', ['deal']) }}"
                                                data-bs-placement="top"
                                                data-title="{{ __('Generate content with AI') }}">
                                                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <textarea class="summernote-simple" name="note">{!! $deal->notes !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="files" class="card">
                <div class="card-header ">
                    <h5>{{ __('Files') }}</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                </div>
            </div>
            <div id="calls" class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>{{ __('Calls') }}</h5>
                        @can('create deal call')
                            <div class="float-end">
                                <a data-size="lg" data-url="{{ route('deals.calls.create', $deal->id) }}"
                                    data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Call') }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="">{{ __('Subject') }}</th>
                                    <th>{{ __('Call Type') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calls as $call)
                                    <tr>
                                        <td>{{ $call->subject }}</td>
                                        <td>{{ ucfirst($call->call_type) }}</td>
                                        <td>{{ $call->duration }}</td>
                                        <td>{{ isset($call->getLeadCallUser) ? $call->getLeadCallUser->name : '-' }}</td>
                                        <td>
                                            @can('edit deal call')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 bg-info btn btn-sm align-items-center"
                                                        data-url="{{ URL::to('deals/' . $deal->id . '/call/' . $call->id . '/edit') }}"
                                                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}" data-title="{{ __('Edit Call') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete deal call')
                                                <div class="action-btn">
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['deals.calls.destroy', $deal->id, $user->id],
                                                        'id' => 'delete-form-' . $deal->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                            class="ti ti-trash text-white"></i></a>
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
            <div id="activity" class="card">
                <div class="card-header">
                    <h5>{{ __('Activity') }}</h5>
                </div>
                <div class="card-body ">
                    <div class="row leads-scroll">
                        <ul class="event-cards list-group list-group-flush mt-3 w-100">
                            @if (!$deal->activities->isEmpty())
                                @foreach ($deal->activities as $activity)
                                    <li class="list-group-item card mb-3">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="theme-avtar bg-primary badge">
                                                        <i class="ti {{ $activity->logIcon() }}"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span
                                                            class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                        <h6 class="m-0">{!! $activity->getRemark() !!}</h6>
                                                        <small
                                                            class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">

                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                No activity found yet.
                            @endif
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
    </div>
    </div>
@endsection
