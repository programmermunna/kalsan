@if(isset($projects) && !empty($projects) && count($projects) > 0)
    <div class="col-12">
        <div class="row">

            @foreach ($projects as $key => $project)
            <div class="col-xxl-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="card w-100 border-0 rounded-3 overflow-hidden">
                    <div class="card-header d-flex align-items-center justify-content-between bg-light p-3">
                        <div class="d-flex align-items-center flex-1">
                            <img {{ $project->img_image }}
                                class="img-fluid rounded-1 border border-2 border-primary me-2" alt=""
                                width="40" height="40">
                            <h5 class="mb-0 text-truncate">
                                <a class="text-dark text-decoration-none"
                                    href="{{ route('projects.show', $project) }}">{{ $project->project_name }}</a>
                            </h5>
                        </div>
                        <div class="dropdown">
                            @if (Gate::check('create project') ||
                                Gate::check('edit project') ||
                                Gate::check('delete project'))
                                <button class="btn p-0 border-0 text-muted" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu icon-dropdown dropdown-menu-end">
                                    @can('create project')
                                        <li><a class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                data-title="{{ __('Duplicate Project') }}"
                                                data-url="{{ route('project.copy', [$project->id]) }}">
                                                <i class="ti ti-copy"></i> {{ __('Duplicate') }}</a></li>
                                    @endcan
                                    @can('edit project')
                                        <li><a class="dropdown-item" href="#!" data-size="lg"
                                                data-url="{{ route('projects.edit', $project->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Edit Project') }}">
                                                <i class="ti ti-pencil"></i> {{ __('Edit') }}</a></li>
                                    @endcan
                                    @can('delete project')
                                        <li>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['projects.destroy', $project->id]]) !!}
                                            <a href="#!" class="dropdown-item text-danger bs-pass-para">
                                                <i class="ti ti-trash"></i> {{ __('Delete') }}</a>
                                            {!! Form::close() !!}
                                        </li>
                                    @endcan
                                </ul>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <span
                            class="badge bg-light-{{ \App\Models\Project::$status_color[$project->status] }} py-1 px-2 text-uppercase mb-3">{{ __(\App\Models\Project::$project_status[$project->status]) }}</span>
                        <p class="text-muted text-sm">{{ $project->description }}</p>
                        <small class="fw-bold text-muted">{{ __('MEMBERS') }}</small>
                        <div class="d-flex mt-2">
                            @foreach ($project->users->take(3) as $user)
                                <img src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('/storage/uploads/avatar/avatar.png') }}"
                                    class="rounded-circle border shadow-sm me-1" width="30" height="30"
                                    title="{{ $user->name }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 d-flex justify-content-between">
                        <div>
                            <h6 class="{{ strtotime($project->start_date) < time() ? 'text-danger' : '' }}">
                                {{ Utility::getDateFormated($project->start_date) }}</h6>
                            <p class="text-muted text-sm mb-0">{{ __('Start Date') }}</p>
                        </div>
                        <div class="text-end">
                            <h6>{{ isset($project->end_date) ? Utility::getDateFormated($project->end_date) : '-' }}</h6>
                            <p class="text-muted text-sm mb-0">{{ __('Due Date') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

@else
    <div class="col-xl-12 col-lg-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h6 class="text-center mb-0">{{__('No Projects Found.')}}</h6>
            </div>
        </div>
    </div>
@endif
