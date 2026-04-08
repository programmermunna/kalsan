@foreach($project->users as $user)
    <li class="list-group-item px-0">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-auto mb-3 mb-sm-0">
                <div class="d-flex align-items-center">
                    <div class="avatar  avatar-sm me-3">
                        @php
                        $avatar = \App\Models\Utility::get_file('uploads/avatar/');

                        @endphp
                        <img @if($user->avatar) src="{{$avatar.$user->avatar}}" @else src="{{$avatar. 'avatar.png'}}" @endif  alt="image" class="rounded border-2 border border-primary">

                    </div>
                    <div class="div">
                        <h5 class="m-0">{{ $user->name }}</h5>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-auto text-sm-end d-flex align-items-center">
                {{-- @if (!empty($user) && $user->type != 'company') --}}
                    <div class="action-btn  ms-2">
                        {!! Form::open(['method' => 'DELETE', 'route' => ['projects.user.destroy',  [$project->id,$user->id]]]) !!}
                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                        {!! Form::close() !!}
                    </div>
                {{-- @endif --}}
            </div>
        </div>
    </li>
@endforeach
