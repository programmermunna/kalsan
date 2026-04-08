{{ Form::model($deal, array('route' => array('deals.update', $deal->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['deal']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('name', __('Deal Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required', 'placeholder' => __('Enter Name'))) }}
        </div>
        <div class="col-6">
            <x-Mobile name="phone" label="{{__('Phone')}}" required placeholder="Enter Phone"></x-Mobile>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('price', __('Price'),['class'=>'form-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control', 'placeholder' => __('Enter Price'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control ','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create pipeline here.') }} <a href="{{ route('pipelines.index') }}"><b>{{ __('Create pipeline') }}</b></a>
            </div>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control ','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create stage here.') }} <a href="{{ route('stages.index') }}"><b>{{ __('Create stage') }}</b></a>
            </div>
        </div>
        <div class="col-12 form-group">
            {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}
            {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','multiple'=>'','id'=>'choices-multiple3')) }}
            <div class="text-xs mt-1">
                {{ __('Create source here.') }} <a href="{{ route('sources.index') }}"><b>{{ __('Create source') }}</b></a>
            </div>
        </div>
        <div class="col-12 form-group">
            {{ Form::label('products', __('Products'),['class'=>'form-label']) }}
            {{ Form::select('products[]', $products,null, array('class' => 'form-control select2','multiple'=>'','id'=>'choices-multiple4')) }}
            <div class="text-xs mt-1">
                {{ __('Create product here.') }} <a href="{{ route('productservice.index') }}"><b>{{ __('Create product') }}</b></a>
            </div>
        </div>
        <div class="col-12 form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'form-label']) }}
            {{ Form::textarea('notes',null, array('class' => 'summernote-simple')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}



<script>
    var stage_id = '{{$deal->stage_id}}';

    $(document).ready(function () {
        $("#commonModal select[name=pipeline_id]").trigger('change');
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        $.ajax({
            url: '{{route('stages.json')}}',
            data: {pipeline_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $('#stage_id').empty();
                $("#stage_id").append('<option value="" selected="selected">{{__('Select Stage')}}</option>');
                $.each(data, function (key, data) {
                    var select = '';
                    if (key == '{{ $deal->stage_id }}') {
                        select = 'selected';
                    }
                    $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data + '</option>');
                });
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    });
</script>
