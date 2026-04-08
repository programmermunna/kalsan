{{Form::model($webhooksetting,array('route' => array('webhook.update', $webhooksetting->id), 'method' => 'POST')) }}
<div class="modal-body">

    <div class="row ">
        <div class="col-12 form-group">
            {{Form::label('module',__('Module'),['class'=>'form-label'])}}
            {{Form::select('module',$modules,null,array('class'=>'form-control select','placeholder'=>__('Select Module')))}}
        </div>
        <div class="col-12 form-group">
            {{Form::label('url',__('Url'),['class'=>'form-label'])}}
            {{Form::text('url',null,array('class'=>'form-control','placeholder'=>__('Enter Webhook Url')))}}
            @error('url')
            <span class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
        <div class="col-12 form-group">
            {{Form::label('method',__('Method'),['class'=>'form-label'])}}
            {{Form::select('method',$methods,null,array('class'=>'form-control select','placeholder'=>__('Select Method')))}}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
