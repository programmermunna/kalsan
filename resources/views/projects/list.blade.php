<div class="col-xl-12">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                    <tr>
                        <th>{{__('Project')}}</th>
                        <th>{{__('Status')}}</th>
                        <th>{{__('Users')}}</th>
                        <th>{{__('Completion')}}</th>
                        @if (Gate::check('create project') ||
                            Gate::check('edit project') ||
                            Gate::check('delete project'))
                            <th class="text-end">{{__('Action')}}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($projects) && !empty($projects) && count($projects) > 0)
                        @foreach ($projects as $key => $project)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img {{ $project->img_image }} class="wid-40 rounded border-2 border border-primary me-3">
                                        <p class="mb-0"><a href="{{ route('projects.show',$project) }}" class="name mb-0 h6 text-sm">{{ $project->project_name }}</a></p>
                                    </div>
                                </td>
                                <td class="">
                                    <span class="status_badge badge bg-{{\App\Models\Project::$status_color[$project->status]}} p-2 px-3 rounded">{{ __(\App\Models\Project::$project_status[$project->status]) }}</span>
                                </td>
                                <td class="">
                                    <div class="avatar-group" id="project_{{ $project->id }}">
                                        @if(isset($project->users) && !empty($project->users) && count($project->users) > 0)
                                            @foreach($project->users as $key => $user)
                                                {{-- @if($key < 3) --}}
                                                    <a href="#" class="avatar rounded-circle">
                                                        <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif title="{{ $user->name }}" style="height:36px;width:36px;">
                                                    </a>
                                                {{-- @else
                                                    @break
                                                @endif --}}
                                            @endforeach
                                            {{-- @if(count($project->users) > 3)
                                                <a href="#" class="avatar rounded-circle avatar-sm">
                                                    <img avatar="+ {{ count($project->users)-3 }}" style="height:36px;width:36px;">
                                                </a>
                                            @endif --}}
                                        @else
                                            {{ __('-') }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-1 text-success">{{ $project->project_progress($project , $last_task->id)['percentage'] }}</h5>
                                    <div class="progress mb-0">
                                        <div class="progress-bar bg-{{ $project->project_progress($project , $last_task->id)['color'] }}" style="width: {{ $project->project_progress($project , $last_task->id)['percentage'] }};"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span>
                                        @can('edit project')
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm bg-warning align-items-center" data-url="{{ route('invite.project.member.view', $project->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Invite User')}}"  data-toggle="tooltip" title="{{__('Invite User')}}">
                                                    <i class="ti ti-send text-white"></i>
                                                </a>
                                            </div>
                                        @endcan
                                        @can('edit project')
                                            <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 bg-info btn btn-sm  align-items-center" data-url="{{ URL::to('projects/'.$project->id.'/edit') }}" data-ajax-popup="true" data-size="lg" data-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Project')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                        @endcan
                                        @can('delete project')
                                            <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['projects.user.destroy', [$project->id,$user->id]]]) !!}
                                                    <a href="#" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                        @endcan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th scope="col" colspan="7"><h6 class="text-center">{{__('No Projects Found.')}}</h6></th>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

