{{Form::model($user,array('route' => array('user.password.update', $user->id), 'method' => 'post' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('password', __('Password')) }}<x-required></x-required>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{__('Enter Password')}}">
            @error('password')
            <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
           </span>
            @enderror
        </div>
        <div class="form-group">
            {{ Form::label('password_confirmation', __('Confirm Password')) }}<x-required></x-required>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{__('Enter Confirm Password')}}">
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Reset')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

