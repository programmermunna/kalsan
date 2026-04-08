{{Form::open(array('url'=>'designation','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
    <div class="modal-body">

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('branch_id', __('Branch'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::select('branch_id', $branchs,null, array('class' => 'form-control select','required'=>'required','placeholder'=>'Select Branch')) }}
            </div>
            <div class="form-group">
                {{ Form::label('department_id', __('Department'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::select('department_id', $departments,null, array('class' => 'form-control select','required'=>'required')) }}
            </div>
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Designation Name'),'required'=> 'required'))}}
                @error('name')
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
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
    {{Form::close()}}

