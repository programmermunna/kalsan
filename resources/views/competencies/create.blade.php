
    {{Form::open(array('url'=>'competencies','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
    <div class="modal-body">

    <div class="row ">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Competencies Name'),'required'=> 'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('type',__('Type'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('type',$performance,null,array('class'=>'form-control select'  , 'placeholder' =>__('Enter Competencies Name'),'required'=> 'required'))}}
            </div>
        </div>

    </div>
</div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
    {{Form::close()}}

