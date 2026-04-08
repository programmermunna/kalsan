@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Job') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Job') }}</li>
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


@section('action-btn')
    <div class="float-end">
        @can('create job')
            <a href="{{ route('job.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-title="{{ __('Create New Job') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="row mb-4 gy-4">
    <div class="col-xl-4 col-sm-6 col-12 job-info-card">
        <div class="job-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="job-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect y="6" width="20" height="9" fill="white"/>
                    <path d="M10.5859 13.5156C10.9095 13.5156 11.1719 13.2533 11.1719 12.9297V10.5859C11.1719 10.2624 10.9096 10 10.5859 10H9.41406C9.09051 10 8.82812 10.2623 8.82812 10.5859V12.9297C8.82812 13.2532 9.09047 13.5156 9.41406 13.5156H10.5859Z" fill="#FF3A6E"/>
                    <path d="M18.2422 3.55469H14.1016C14.1016 3.43668 14.1016 2.85074 14.1016 2.96875C14.1016 1.99945 13.3131 1.21094 12.3438 1.21094C12.2166 1.21094 7.48223 1.21094 7.65625 1.21094C6.68695 1.21094 5.89844 1.99941 5.89844 2.96875C5.89844 3.08676 5.89844 3.6727 5.89844 3.55469H1.75781C0.788516 3.55469 0 4.34316 0 5.3125C0 6.46004 0.140625 7.465 0.419844 8.32145C0.699062 9.17789 1.11691 9.88586 1.67141 10.4395C2.60449 11.3713 3.79301 11.7578 5.12754 11.7578H7.65625C7.65625 11.6398 7.65625 10.4679 7.65625 10.5859C7.65625 9.61664 8.44473 8.82813 9.41406 8.82813C9.53207 8.82813 10.7039 8.82813 10.5859 8.82813C11.5552 8.82813 12.3438 9.6166 12.3438 10.5859C12.3438 10.7039 12.3438 11.8758 12.3438 11.7578H13.6457C14.8121 11.7093 16.72 12.064 18.328 10.4635C18.8828 9.91129 19.3007 9.20207 19.5801 8.3416C19.8594 7.48113 20 6.46949 20 5.3125C20 4.3432 19.2115 3.55469 18.2422 3.55469ZM7.07031 2.96875C7.07031 2.64547 7.33293 2.38281 7.65625 2.38281H12.3438C12.667 2.38281 12.9297 2.64543 12.9297 2.96875C12.9297 3.08676 12.9297 3.6727 12.9297 3.55469H7.07031C7.07031 3.43668 7.07031 2.85074 7.07031 2.96875Z" fill="#FF3A6E"/>
                    <path d="M13.6617 12.9297H12.3438C12.3438 13.899 11.5553 14.6875 10.5859 14.6875C10.4679 14.6875 9.29605 14.6875 9.41406 14.6875C8.44477 14.6875 7.65625 13.899 7.65625 12.9297C7.52238 12.9297 4.98797 12.9297 5.13266 12.9297C3.46141 12.9297 1.99348 12.4169 0.843398 11.2686C0.518516 10.9443 0.241094 10.5791 0 10.1885V18.2031C0 18.527 0.26207 18.7891 0.585938 18.7891H19.4141C19.7379 18.7891 20 18.527 20 18.2031V10.2126C19.7557 10.6091 19.4771 10.9734 19.1548 11.2943C17.306 13.1335 15.337 12.8505 13.6617 12.9297Z" fill="#FF3A6E"/>
                    </svg>

            </div>
            <div class="job-content d-flex align-items-start justify-content-between flex-1">
                <div class="job-content-inner">
                    <span class="text-sm d-block mb-1">{{ __('Total') }}</span>
                    <h2 class="h5 mb-0">{{ __('Jobs') }}</h2>
                </div>
                <h3 class="h4 mb-0">{{ $data['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12 job-info-card">
        <div class="job-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="job-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.2004 2.5456L17.4559 6.80112C17.508 6.85316 17.534 6.9214 17.534 6.98964L17.5341 13.0102C17.5341 13.0847 17.5035 13.152 17.4542 13.2004L13.1987 17.456C13.1467 17.508 13.0785 17.5341 13.0102 17.5341L6.98962 17.5341C6.91514 17.5341 6.84778 17.5036 6.79938 17.4543L2.54386 13.1988C2.49178 13.1467 2.46578 13.0785 2.46578 13.0103L2.46574 6.98972C2.46574 6.91524 2.4963 6.84788 2.54558 6.79948L6.80106 2.54392C6.8531 2.49184 6.92134 2.46584 6.98958 2.46584L13.0102 2.4658C13.0847 2.4658 13.1521 2.49636 13.2004 2.5456ZM2.23034 3.91596V2.37092C2.23034 2.35132 2.2463 2.33536 2.2659 2.33536H3.94046C4.08774 2.33536 4.20714 2.21596 4.20714 2.06868C4.20714 1.9214 4.08774 1.802 3.94046 1.802H1.9637C1.81642 1.802 1.69702 1.9214 1.69702 2.06868V3.91596C1.69702 4.06324 1.81642 4.18264 1.9637 4.18264C2.11098 4.18264 2.23034 4.06324 2.23034 3.91596ZM1.69702 16.084V17.9312C1.69702 18.0785 1.81642 18.1979 1.9637 18.1979H3.94046C4.08774 18.1979 4.20714 18.0785 4.20714 17.9312C4.20714 17.7839 4.08774 17.6645 3.94046 17.6645H2.2659C2.2463 17.6645 2.23034 17.6486 2.23034 17.629V16.084C2.23034 15.9367 2.11094 15.8173 1.96366 15.8173C1.81638 15.8173 1.69702 15.9367 1.69702 16.084ZM17.7695 16.084V17.629C17.7695 17.6486 17.7535 17.6646 17.7339 17.6646H16.0593C15.9121 17.6646 15.7927 17.784 15.7927 17.9312C15.7927 18.0785 15.9121 18.1979 16.0593 18.1979H18.0361C18.1834 18.1979 18.3028 18.0785 18.3028 17.9312V16.084C18.3028 15.9367 18.1834 15.8173 18.0361 15.8173C17.8888 15.8173 17.7695 15.9367 17.7695 16.084ZM18.3028 3.91596V2.06872C18.3028 1.92144 18.1834 1.80204 18.0361 1.80204H16.0593C15.9121 1.80204 15.7927 1.92144 15.7927 2.06872C15.7927 2.216 15.9121 2.3354 16.0593 2.3354H17.7339C17.7535 2.3354 17.7695 2.35136 17.7695 2.37096V3.91596C17.7695 4.06324 17.8889 4.18264 18.0361 4.18264C18.1834 4.18264 18.3028 4.06324 18.3028 3.91596ZM13.7813 6.98244L13.0174 6.21856C12.8733 6.07444 12.6358 6.07444 12.4917 6.21856L10.6128 8.09744C10.2749 8.43532 9.7249 8.43532 9.38698 8.09744L7.5081 6.21856C7.36398 6.07444 7.1265 6.07444 6.98238 6.21856L6.2185 6.98244C6.07438 7.12656 6.07438 7.36404 6.2185 7.50816L8.09738 9.38708C8.43526 9.72496 8.43526 10.275 8.09738 10.6129L6.21846 12.4918C6.07434 12.636 6.07434 12.8734 6.21846 13.0176L6.98234 13.7814C7.12646 13.9256 7.36394 13.9256 7.50806 13.7814L9.38698 11.9025C9.72486 11.5646 10.2749 11.5646 10.6128 11.9025L12.4917 13.7814C12.6359 13.9256 12.8733 13.9256 13.0175 13.7814L13.7813 13.0176C13.9255 12.8734 13.9255 12.636 13.7813 12.4918L11.9025 10.613C11.5646 10.2751 11.5646 9.72504 11.9025 9.38712L13.7813 7.50824C13.9254 7.36404 13.9254 7.12656 13.7813 6.98244Z" fill="#0CAF60"/>
                    <circle cx="10" cy="10" r="7" fill="#0CAF60"/>
                    <path class="active-checkmark" d="M13.9981 7.83076C13.9984 7.91906 13.9811 8.00654 13.9473 8.08812C13.9135 8.16971 13.8639 8.24378 13.8013 8.30605L9.56273 12.5446C9.37207 12.7347 9.11382 12.8415 8.84458 12.8415C8.57534 12.8415 8.31708 12.7347 8.12642 12.5446L6.19881 10.617C6.13603 10.5546 6.08617 10.4805 6.0521 10.3989C6.01802 10.3172 6.0004 10.2297 6.00025 10.1412C6.00009 10.0527 6.0174 9.96507 6.05119 9.8833C6.08497 9.80153 6.13457 9.72723 6.19713 9.66467C6.25969 9.60211 6.33399 9.55251 6.41576 9.51873C6.49753 9.48494 6.58517 9.46763 6.67364 9.46779C6.76212 9.46795 6.84969 9.48557 6.93134 9.51965C7.01299 9.55372 7.08711 9.60358 7.14945 9.66636L8.84458 11.3615L12.8507 7.35541C12.9447 7.26139 13.0645 7.19737 13.1949 7.17143C13.3253 7.14549 13.4604 7.1588 13.5832 7.20968C13.7061 7.26057 13.811 7.34673 13.8849 7.45728C13.9588 7.56783 13.9982 7.6978 13.9981 7.83076Z" fill="white"/>
                    </svg>


            </div>
            <div class="job-content d-flex align-items-start justify-content-between flex-1">

                <div class="job-content-inner">
                    <span class="text-sm d-block mb-1">{{ __('Active') }}</span>
                    <h2 class="h5 mb-0">{{ __('Jobs') }}</h2>
                </div>
                <h3 class="h4 mb-0">{{ $data['active'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12 job-info-card">
        <div class="job-card-inner d-flex align-items-center gap-3">
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="job-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="6" fill="white"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.2004 2.5456L17.4559 6.80112C17.508 6.85316 17.534 6.9214 17.534 6.98964L17.5341 13.0102C17.5341 13.0847 17.5035 13.152 17.4542 13.2004L13.1987 17.456C13.1467 17.508 13.0785 17.5341 13.0102 17.5341L6.98962 17.5341C6.91514 17.5341 6.84778 17.5036 6.79938 17.4543L2.54386 13.1988C2.49178 13.1467 2.46578 13.0785 2.46578 13.0103L2.46574 6.98972C2.46574 6.91524 2.4963 6.84788 2.54558 6.79948L6.80106 2.54392C6.8531 2.49184 6.92134 2.46584 6.98958 2.46584L13.0102 2.4658C13.0847 2.4658 13.1521 2.49636 13.2004 2.5456ZM2.23034 3.91596V2.37092C2.23034 2.35132 2.2463 2.33536 2.2659 2.33536H3.94046C4.08774 2.33536 4.20714 2.21596 4.20714 2.06868C4.20714 1.9214 4.08774 1.802 3.94046 1.802H1.9637C1.81642 1.802 1.69702 1.9214 1.69702 2.06868V3.91596C1.69702 4.06324 1.81642 4.18264 1.9637 4.18264C2.11098 4.18264 2.23034 4.06324 2.23034 3.91596ZM1.69702 16.084V17.9312C1.69702 18.0785 1.81642 18.1979 1.9637 18.1979H3.94046C4.08774 18.1979 4.20714 18.0785 4.20714 17.9312C4.20714 17.7839 4.08774 17.6645 3.94046 17.6645H2.2659C2.2463 17.6645 2.23034 17.6486 2.23034 17.629V16.084C2.23034 15.9367 2.11094 15.8173 1.96366 15.8173C1.81638 15.8173 1.69702 15.9367 1.69702 16.084ZM17.7695 16.084V17.629C17.7695 17.6486 17.7535 17.6646 17.7339 17.6646H16.0593C15.9121 17.6646 15.7927 17.784 15.7927 17.9312C15.7927 18.0785 15.9121 18.1979 16.0593 18.1979H18.0361C18.1834 18.1979 18.3028 18.0785 18.3028 17.9312V16.084C18.3028 15.9367 18.1834 15.8173 18.0361 15.8173C17.8888 15.8173 17.7695 15.9367 17.7695 16.084ZM18.3028 3.91596V2.06872C18.3028 1.92144 18.1834 1.80204 18.0361 1.80204H16.0593C15.9121 1.80204 15.7927 1.92144 15.7927 2.06872C15.7927 2.216 15.9121 2.3354 16.0593 2.3354H17.7339C17.7535 2.3354 17.7695 2.35136 17.7695 2.37096V3.91596C17.7695 4.06324 17.8889 4.18264 18.0361 4.18264C18.1834 4.18264 18.3028 4.06324 18.3028 3.91596ZM13.7813 6.98244L13.0174 6.21856C12.8733 6.07444 12.6358 6.07444 12.4917 6.21856L10.6128 8.09744C10.2749 8.43532 9.7249 8.43532 9.38698 8.09744L7.5081 6.21856C7.36398 6.07444 7.1265 6.07444 6.98238 6.21856L6.2185 6.98244C6.07438 7.12656 6.07438 7.36404 6.2185 7.50816L8.09738 9.38708C8.43526 9.72496 8.43526 10.275 8.09738 10.6129L6.21846 12.4918C6.07434 12.636 6.07434 12.8734 6.21846 13.0176L6.98234 13.7814C7.12646 13.9256 7.36394 13.9256 7.50806 13.7814L9.38698 11.9025C9.72486 11.5646 10.2749 11.5646 10.6128 11.9025L12.4917 13.7814C12.6359 13.9256 12.8733 13.9256 13.0175 13.7814L13.7813 13.0176C13.9255 12.8734 13.9255 12.636 13.7813 12.4918L11.9025 10.613C11.5646 10.2751 11.5646 9.72504 11.9025 9.38712L13.7813 7.50824C13.9254 7.36404 13.9254 7.12656 13.7813 6.98244Z" fill="#FFA21D"/>
                    </svg>

            </div>
            <div class="job-content d-flex align-items-start justify-content-between flex-1">
                <div class="job-content-inner">
                    <span class="text-sm d-block mb-1">{{ __('Inactive') }}</span>
                    <h2 class="h5 mb-0">{{ __('Jobs') }}</h2>
                </div>
                <h3 class="h4 mb-0">{{ $data['in_active'] }}</h3>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    @if (Gate::check('edit job') || Gate::check('delete job') || Gate::check('show job'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="font-style">
                                @foreach ($jobs as $job)
                                    <tr>
                                        <td>{{ !empty($job->branches) ? $job->branches->name : __('All') }}</td>
                                        <td>{{ $job->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($job->start_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($job->end_date) }}</td>
                                        <td>
                                            @if ($job->status == 'active')
                                                <span
                                                    class="status_badge badge bg-success p-2 px-3 rounded">{{ App\Models\Job::$status[$job->status] }}</span>
                                            @else
                                                <span
                                                    class="status_badge badge bg-danger p-2 px-3 rounded">{{ App\Models\Job::$status[$job->status] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($job->created_at) }}</td>
                                        @if (Gate::check('edit job') || Gate::check('delete job') || Gate::check('show job'))
                                            <td>

                                                @if ($job->status != 'in_active')

                                                    <div class="action-btn  me-2">
                                                        <a href="#"
                                                            id="{{ route('job.requirement', [$job->code, !empty($job) ? $job->createdBy->lang : 'en']) }}"
                                                            class="mx-3 btn btn-sm align-items-center bg-secondary"
                                                            onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                            title="{{ __('Copy') }}"
                                                            data-original-title="{{ __('Click to copy') }}"><i
                                                                class="ti ti-link text-white"></i></a>
                                                    </div>
                                                @endif
                                                @can('show job')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('job.show', $job->id) }}"
                                                            data-title="{{ __('Job Detail') }}" title="{{ __('View') }}"
                                                            class="mx-3 btn btn-sm align-items-center bg-warning"
                                                            data-bs-toggle="tooltip"
                                                            data-original-title="{{ __('View Detail') }}">
                                                            <i class="ti ti-eye text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @can('edit job')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('job.edit', $job->id) }}"
                                                            data-title="{{ __('Edit Job') }}"
                                                            class="mx-3 btn btn-sm align-items-center bg-info"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @can('delete job')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['job.destroy', $job->id], 'id' => 'delete-form-' . $job->id]) !!}

                                                        <a href="#"
                                                            class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{ $job->id }}').submit();">
                                                            <i class="ti ti-trash text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
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
@endsection
