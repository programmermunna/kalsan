    {{Form::model($document,array('route' => array('document.update', $document->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name',['class'=>'form-label']))}}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Department Name') ,'required' => 'required'))}}
                @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('is_required', __('Required Field'),['class'=>'form-label']) }}
                <select class="form-control" required name="is_required">
                    <option value="0" @if($document->is_required == 0) selected @endif>{{__('Not Required')}}</option>
                    <option value="1" @if($document->is_required == 1) selected @endif>{{__('Is Required')}}</option>
                </select>
            </div>
        </div>

    </div>
    </div>

    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
    </div>
    {{Form::close()}}
