{{Form::open(array('url'=>'company-policy','method'=>'post', 'enctype' => "multipart/form-data" , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['company policy']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('branch',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('branch',$branch,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('title',__('Title'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('title',null,array('class'=>'form-control','required'=>'required' , 'placeholder'=>__('Enter Title')))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'),['class'=>'form-label'])}}
                {{ Form::textarea('description',null, array('class' => 'form-control' , 'placeholder'=>__('Enter Description'))) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-0">
            {{Form::label('attachment',__('Attachment'),['class'=>'form-label'])}}
            <div class="choose-file">
                <label for="attachment" class="form-label">
                    <input type="file" class="form-control file-validate" name="attachment" id="attachment" data-filename="attachment_create">
                    <p id="" class="file-error text-danger"></p>
                    <img id="image" class="mt-3" style="width:25%;"/>
                </label>
            </div>
            </div>
        </div>


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


<script>
    document.getElementById('attachment').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>

