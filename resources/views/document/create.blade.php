    {{Form::open(array('url'=>'document','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
    <div class="modal-body">

    <div class="row">
        <div class="form-group col-12">
            {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Document Name') , 'required' => 'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12">
            {{ Form::label('is_required', __('Required Field'),['class'=>'form-label']) }}<x-required></x-required>
            <select class="form-control" required name="is_required">
                <option value="0">{{__('Not Required')}}</option>
                <option value="1">{{__('Is Required')}}</option>
            </select>
        </div>

    </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
    {{Form::close()}}

