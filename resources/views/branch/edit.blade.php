{{Form::model($branch, array('route' => array('branch.update', $branch->id), 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate')) }}
<div class="modal-body">

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {{Form::label('name', __('Name'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::text('name', null, array('class' => 'form-control', 'placeholder' => __('Enter Branch Name'), 'required' => 'required'))}}
                @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{Form::label('manager', __('Manager'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::text('manager', null, array('class' => 'form-control', 'placeholder' => __('Enter Manager Name'), 'required' => 'required'))}}
                @error('manager')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{Form::label('address', __('Address'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::textarea('address', null, array('class' => 'form-control', 'rows' => '2', 'placeholder' => __('Enter Address'), 'required' => 'required'))}}
                @error('address')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{Form::label('mobile', __('Mobile'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::textarea('mobile', null, array('class' => 'form-control', 'rows' => '2', 'placeholder' => __('Enter Mobile'), 'required' => 'required'))}}
                @error('mobile')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{Form::label('email', __('Email'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::email('email', null, array('class' => 'form-control', 'rows' => '2', 'placeholder' => __('Enter Email'), 'required' => 'required'))}}
                @error('mobile')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{Form::label('type', __('Type'), ['class' => 'form-label'])}}<x-required></x-required>
                {{Form::select('type', ['Head Office' => 'Head Office', 'Branch' => 'Branch'], old('title', $user->title ?? ''), ['class' => 'form-control', 'style' => 'width: 200px;']) }}
                @error('type')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}
