@extends('layouts.admin')
@php
    $attachments = \App\Models\Utility::get_file('contract_attechment');
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
@endpush

@section('page-title')
    {{ __('Contract Detail') }}
@endsection
@push('script-page')
    <script>
        $(document).on("click", ".status", function() {
            var status = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            $.ajax({
                url: url,
                type: 'POST',
                data: {

                    "status": status,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (data) {
                        show_toastr('{{ __('success') }}', 'Status Update Successfully!', 'success');
                    } else {
                        show_toastr('{{ __('error') }}', 'Status Update Failed!', 'error');
                    }
                    location.reload();
                }

            });
        });
    </script>

    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        @can('manage contract')
            $('.summernote-simple').on('summernote.blur', function() {

                $.ajax({
                    url: "{{ route('contract.contract_description.store', $contract->id) }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        contract_description: $(this).val()
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.is_success) {
                            show_toastr('success', response.success, 'success');
                        } else {
                            show_toastr('error', response.error, 'error');
                        }
                    },
                    error: function(response) {

                        response = response.responseJSON;
                        if (response.is_success) {
                            show_toastr('error', response.error, 'error');
                        } else {
                            show_toastr('error', response.error, 'error');
                        }
                    }
                })
            });
        @else
        @endcan
    </script>
    <script>
        Dropzone.autoDiscover = true;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            parallelUploads: 1,

            url: "{{ route('contract.file.upload', [$contract->id]) }}",
            success: function(file, response) {
                location.reload()
                if (response.is_success) {
                    show_toastr('{{ __('success') }}', 'Attachment Create Successfully!', 'success');
                    dropzoneBtn(file, response);
                } else {

                    myDropzone.removeFile(file);
                    show_toastr('{{ __('Error') }}', 'The attachment must be same as stoarge setting',
                        'Error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{ __('Error') }}', 'The attachment must be same as stoarge setting',
                        'error');
                } else {
                    show_toastr('{{ __('Error') }}', 'The attachment must be same as stoarge setting',
                        'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("contract_id", {{ $contract->id }});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{ __('Download') }}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "action-btn btn-danger mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
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
                            location.reload();
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            file.previewTemplate.appendChild(html);
        }
        $(document).on('click', '#comment_submit', function(e) {
            var curr = $(this);

            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            if (comment != '') {
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {
                        comment: comment,
                        "_token": "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    success: function(data) {
                        show_toastr('{{ __('success') }}', 'Comment Create Successfully!', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 500)
                        data = JSON.parse(data);
                        var html = "<div class='list-group-item px-0'>" +
                            "                    <div class='row align-items-center'>" +
                            "                        <div class='col-auto'>" +
                            "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
                            "                                <img src=" + data.default_img +
                            " alt='' class='avatar-sm rounded border-2 border border-primary'>" +
                            "                            </a>" +
                            "                        </div>" +
                            "                        <div class='col ml-n2'>" +
                            "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" +
                            data.comment + "</p>" +
                            "                            <small class='d-block'>" + data.current_time +
                            "</small>" +
                            "                        </div>" +
                            "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" +
                            data.deleteUrl +
                            "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                            "                    </div>" +
                            "                </div>";

                        $("#comments").prepend(html);
                        $("#form-comment textarea[name='comment']").val('');
                        load_task(curr.closest('.task-id').attr('id'));
                        show_toastr('{{ __('success') }}', '{{ __('Comment Added Successfully!') }}');
                    },
                    error: function(data) {
                        show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                    }
                });
            } else {
                show_toastr('error', '{{ __('Please write comment!') }}');
            }
        });

        $(document).on("click", ".delete-comment", function() {
            var btn = $(this);

            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    load_task(btn.closest('.task-id').attr('id'));
                    show_toastr('{{ __('success') }}', '{{ __('Comment Deleted Successfully!') }}');
                    btn.closest('.list-group-item').remove();
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('error', data.message);
                    } else {
                        show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                    }
                }
            });
        });
    </script>


    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">{{ __('contract') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ \Auth::user()->contractNumberFormat($contract->id) }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex align-items-center">
        <a href="{{ route('contract.download.pdf', \Crypt::encrypt($contract->id)) }}"
            class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Download') }}" target="_blanks">
            <i class="ti ti-download"></i>
        </a>
        <a href="{{ route('get.contract', $contract->id) }}" target="_blank"
            class="btn btn-sm bg-brown-subtitle btn-icon m-1">
            <i class="ti ti-eye text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('PreView') }}"> </i>
        </a>

        @if (\Auth::user()->type == 'company')
            <a href="{{ route('send.mail.contract', $contract->id) }}" class="btn btn-sm bg-warning-subtle btn-icon m-1"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Send Email') }}">
                <i class="ti ti-mail text-white"></i>
            </a>
            <a href="#" class="btn btn-sm btn-secondary btn-icon m-1" data-size="lg"
                data-url="{{ route('contract.copy', $contract->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                title="{{ __('Duplicate') }}">
                <i class="ti ti-copy text-white"></i>
            </a>
        @endif

        @if (\Auth::user()->type == 'company')
            <a href="#" class="btn btn-sm btn-primary-subtle btn-icon m-1" data-size="lg"
                data-url="{{ route('signature', $contract->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Add signature') }}" data-title="{{ __('Add signature') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        @elseif(\Auth::user()->type == 'client' && $contract->status == 'accept')
            <a href="#" class="btn btn-sm btn-primary-subtle btn-icon m-1" data-size="lg"
                data-url="{{ route('signature', $contract->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Add signature') }}" data-title="{{ __('Add signature') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        @endif

        @php
            $status = App\Models\Contract::status();
        @endphp
        @php
            $status = App\Models\Contract::status();
        @endphp

        @if (\Auth::user()->type == 'client')
            <ul class="list-unstyled m-0 ">
                <li class="dropdown dash-h-item status-drp">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="drp-text hide-mob text-primary">{{ ucfirst($contract->status) }}
                            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @foreach ($status as $k => $status)
                            <a class="dropdown-item status" data-id="{{ $k }}"
                                data-url="{{ route('contract.status', $contract->id) }}"
                                href="#">{{ ucfirst($status) }}
                            </a>
                        @endforeach
                    </div>
                </li>
            </ul>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#useradd-1" class="list-group-item list-group-item-action border-0">{{ __('General') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                    <a href="#useradd-2" class="list-group-item list-group-item-action border-0">{{ __('Attachment') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                    <a href="#useradd-3" class="list-group-item list-group-item-action border-0">{{ __('Comment') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                    <a href="#useradd-4" class="list-group-item list-group-item-action border-0">{{ __('Notes') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div id="useradd-1">
                <div class="row mb-4 gy-4">
                    <div class="col-md-6">
                        <div class="row h-100">
                            <div class="col-12 leave-card mb-3">
                                <div class="leave-card-inner d-flex gap-3">
                                    <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                            fill="#FF3A6E"></path>
                                    </svg>
                                    <div class="leave-icon">
                                        <i class="ti ti-user-plus fs-4"></i>
                                    </div>
                                    <div
                                        class="leave-info d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h2 class="h4 mb-0">{{ __('Attachment') }}</h2>
                                        <h3 class="h4 mb-2">{{ count($contract->files) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 leave-card mb-3">
                                <div class="leave-card-inner d-flex gap-3">
                                    <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                            fill="#FF3A6E"></path>
                                    </svg>
                                    <div class="leave-icon">
                                        <i class="ti ti-click fs-4"></i>
                                    </div>
                                    <div
                                        class="leave-info d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h2 class="h4 mb-0">{{ __('Comment') }}</h2>
                                        <h3 class="h4 mb-2">{{ count($contract->comment) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 leave-card">
                                <div class="leave-card-inner d-flex gap-3">
                                    <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                                            fill="#FF3A6E"></path>
                                    </svg>
                                    <div class="leave-icon">
                                        <i class="ti ti-file fs-4"></i>
                                    </div>
                                    <div
                                        class="leave-info d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h2 class="h4 mb-0">{{ __('Notes') }}</h2>
                                        <h3 class="h4 mb-2">{{ count($contract->note) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card report_card total_amount_card mb-0 h-100">
                            <div class="card-body pt-0">
                                <address class="mb-0 text-sm">
                                    <dl class="row mt-4 align-items-center">
                                        <h5>{{ __('Contract Detail') }}</h5>
                                        <br>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Subject') }}</dt>
                                        <dd class="col-sm-8 text-sm">{{ $contract->subject }}</dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Project') }}</dt>
                                        <dd class="col-sm-8 text-sm">
                                            {{ !empty($contract->projects) ? $contract->projects->project_name : '-' }}
                                        </dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Value') }}</dt>
                                        <dd class="col-sm-8 text-sm">
                                            {{ \Auth::user()->priceFormat($contract->value) }}</dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Type') }}</dt>
                                        <dd class="col-sm-8 text-sm">
                                            {{ !empty($contract->types) ? $contract->types->name : '-' }}</dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Status') }}</dt>
                                        <dd class="col-sm-8 text-sm">{{ $contract->status }}</dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('Start Date') }}</dt>
                                        <dd class="col-sm-8 text-sm">
                                            {{ Auth::user()->dateFormat($contract->start_date) }}</dd>
                                        <dt class="col-sm-4 h6 text-sm">{{ __('End Date') }}</dt>
                                        <dd class="col-sm-8 text-sm">
                                            {{ Auth::user()->dateFormat($contract->end_date) }}</dd>
                                    </dl>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Contract Description ') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea class="summernote-simple">{!! $contract->contract_description !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="useradd-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Contract Attachments') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            @if (\Auth::user()->type == 'company')
                                <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                            @elseif(\Auth::user()->type == 'client' && $contract->status == 'accept')
                                <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                            @endif
                        </div>

                        <div class="scrollbar-inner">
                            <div class="card-wrapper  lead-common-box">
                                @foreach ($contract->files as $file)
                                    <div class="card mb-3 border shadow-none">
                                        <div class="px-3 py-3">

                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h6 class="text-sm mb-1">
                                                        <a href="#!">{{ $file->files }}</a>
                                                    </h6>
                                                    <p class="card-text small">
                                                        @if (!empty($file->files) && file_exists(storage_path('contract_attechment/' . $file->files)))
                                                            {{ number_format(\File::size(storage_path('contract_attechment/' . $file->files)) / 1048576, 2) . ' ' . __('MB') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="action-btn ">
                                                    <a href="{{ $attachments . '/' . $file->files }}"
                                                        class=" btn btn-sm bg-warning align-items-center" download=""
                                                        data-bs-toggle="tooltip" title="Download">
                                                        <span class="text-white"> <i class="ti ti-download"></i></span>
                                                    </a>
                                                </div>

                                                @if ((\Auth::user()->type == 'company' && $contract->status == 'accept') || \Auth::user()->id == $file->user_id)
                                                    <div class="col-auto actions">
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['contracts.file.delete', $contract->id, $file->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para ">
                                                                <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('Delete') }}"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div id ="useradd-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Comments') }}</h5>
                    </div>
                    <div class="card-body">
                        @if (\Auth::user()->type == 'company')
                            <div class="col-12 d-flex">
                                <div class="form-group mb-0 form-send w-100">
                                    <form method="post" class="card-comment-box" id="form-comment"
                                        data-action="{{ route('comment.store', [$contract->id]) }}">
                                        <textarea rows="1" class="form-control" name="comment" data-toggle="autosize"
                                            placeholder="{{ __('Add a comment...') }}"></textarea>
                                    </form>
                                </div>
                                <button id="comment_submit" class="btn btn-send mt-2"><i
                                        class="f-16 text-primary ti ti-brand-telegram"></i></button>
                            </div>
                        @elseif(\Auth::user()->type == 'client' && $contract->status == 'accept')
                            <div class="col-12 d-flex">
                                <div class="form-group mb-0 form-send w-100">
                                    <form method="post" class="card-comment-box" id="form-comment"
                                        data-action="{{ route('comment.store', [$contract->id]) }}">
                                        <textarea rows="1" class="form-control" name="comment" data-toggle="autosize"
                                            placeholder="{{ __('Add a comment...') }}"></textarea>
                                    </form>
                                </div>
                                <button id="comment_submit" class="btn btn-send mt-2"><i
                                        class="f-16 text-primary ti ti-brand-telegram"></i></button>
                            </div>
                        @endif

                        <div class="list-group list-group-flush mb-0" id="comments">
                            @foreach ($contract->comment as $comment)
                                @php
                                    $user = \App\Models\User::find($comment->user_id);
                                    $logo = \App\Models\Utility::get_file('uploads/avatar/');
                                @endphp
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <a href="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}"
                                                target="_blank">
                                                <img class="rounded border-2 border border-primary" width="40"
                                                    height="40"
                                                    src="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}">
                                            </a>
                                        </div>

                                        <div class="col">
                                            <p class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                {{ $comment->comment }}</p>
                                            <small class="d-block">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>

                                        @if ((\Auth::user()->type == 'company' && $contract->status == 'accept') || \Auth::user()->id == $comment->user_id)
                                            <div class="col-auto actions">
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['comment_store.destroy', $comment->id]]) !!}
                                                    <a href="#!"
                                                        class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para">
                                                        <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Delete') }}"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div id ="useradd-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>{{ __('Notes') }}</h5>
                        @php
                            $settings = \App\Models\Utility::settings();
                        @endphp
                        @if ($settings['ai_chatgpt_enable'] == 'on')
                            <div class="d-flex justify-content-end">
                                <div class="mt-0">
                                    <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm text-right"
                                        data-ajax-popup-over="true" id="grammarCheck"
                                        data-url="{{ route('grammar', ['grammar']) }}" data-bs-placement="top"
                                        data-title="{{ __('Grammar check with AI') }}">
                                        <i class="ti ti-rotate"></i> <span>{{ __('Grammar check with AI') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (\Auth::user()->type == 'company')
                            <div class="col-12 d-flex">
                                <div class="form-group mb-0 form-send w-100">
                                    {{ Form::open(['route' => ['note_store.store', $contract->id]]) }}
                                    <div class="form-group">
                                        <textarea rows="3" class="form-control grammer_textarea" name="notes" data-toggle="autosize"
                                            placeholder="{{ __('Add a Notes...') }}" required></textarea>
                                    </div>
                                    <div class="col-md-12 text-end mb-0">
                                        {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        @elseif(\Auth::user()->type == 'client' && $contract->status == 'accept')
                            <div class="col-12 d-flex">
                                <div class="form-group mb-0 form-send w-100">
                                    {{ Form::open(['route' => ['note_store.store', $contract->id]]) }}
                                    <div class="form-group">
                                        <textarea rows="3" class="form-control grammer_textarea" name="notes" data-toggle="autosize"
                                            placeholder="{{ __('Add a Notes...') }}" required></textarea>
                                    </div>
                                    <div class="col-md-12 text-end mb-0">
                                        {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        @endif

                        <div class=" list-group list-group-flush mb-0" id="notes">
                            @foreach ($contract->note as $note)
                                @php
                                    $user = \App\Models\User::find($note->user_id);
                                    $logo = \App\Models\Utility::get_file('uploads/avatar/');
                                @endphp
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <a href="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}"
                                                target="_blank">
                                                <img class="rounded border-2 border border-primary" width="40"
                                                    height="40"
                                                    src="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}">
                                            </a>
                                        </div>
                                        <div class="col">
                                            <p class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                {{ $note->notes }}</p>
                                            <small class="d-block">{{ $note->created_at->diffForHumans() }}</small>
                                        </div>

                                        @if ((\Auth::user()->type == 'company' && $contract->status == 'accept') || \Auth::user()->id == $note->user_id)
                                            <div class="col-auto actions">
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['note_store.destroy', $note->id]]) !!}
                                                    <a href="#!"
                                                        class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para ">
                                                        <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Delete') }}"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
