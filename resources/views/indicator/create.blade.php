{{Form::open(array('url'=>'indicator','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    <div class="row row-gap-1   `">
        <div class="col-md-12">
            <div class="form-group mb-0">
                {{Form::label('branch',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('branch',$brances,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                {{Form::label('department',__('Department'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('department',$departments,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                {{Form::label('designation',__('Designation'),['class'=>'form-label'])}}<x-required></x-required>
                <select class="select form-control select2-multiple" id="designation_id" name="designation" data-toggle="select2" data-placeholder="{{ __('Select Designation ...') }}" required>
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create designation here.') }} <a href="{{ route('designation.index') }}"><b>{{ __('Create designation') }}</b></a>
                </div>
            </div>
        </div>

    </div>
    @foreach($performance as $performances)
    <div class="row">
        <div class="col-md-12 mt-3">
            <h6>{{$performances->name}}</h6>
            <hr class="mt-0">
        </div>
        @foreach($performances->types as $types )
            <div class="col-6">
                    {{$types->name}}
            </div>
            <div class="col-6">
                <fieldset id='demo1' class="rating">
                    <input class="stars" type="radio" id="technical-5-{{$types->id}}" name="rating[{{$types->id}}]" value="5"/>
                    <label class="full" for="technical-5-{{$types->id}}" title="Awesome - 5 stars"></label>
                    <input class="stars" type="radio" id="technical-4-{{$types->id}}" name="rating[{{$types->id}}]" value="4"/>
                    <label class="full" for="technical-4-{{$types->id}}" title="Pretty good - 4 stars"></label>
                    <input class="stars" type="radio" id="technical-3-{{$types->id}}" name="rating[{{$types->id}}]" value="3"/>
                    <label class="full" for="technical-3-{{$types->id}}" title="Meh - 3 stars"></label>
                    <input class="stars" type="radio" id="technical-2-{{$types->id}}" name="rating[{{$types->id}}]" value="2"/>
                    <label class="full" for="technical-2-{{$types->id}}" title="Kinda bad - 2 stars"></label>
                    <input class="stars" type="radio" id="technical-1-{{$types->id}}" name="rating[{{$types->id}}]" value="1"/>
                    <label class="full" for="technical-1-{{$types->id}}" title="Sucks big time - 1 star"></label>
                </fieldset>
            </div>
        @endforeach
    </div>
    @endforeach
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
<script>
    $(document).ready(function() {
        $('#designation_id').empty();
        $('#designation_id').append('<option value="">{{__('Select Designation')}}</option>');
    });
</script>
