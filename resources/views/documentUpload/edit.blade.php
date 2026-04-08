{{Form::model($ducumentUpload,array('route' => array('document-upload.update', $ducumentUpload->id), 'method' => 'PUT','enctype' => "multipart/form-data" , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['document']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Name')))}}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('role',__('Role'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('role',$roles,null,array('class'=>'form-control select'))}}
                <div class="text-xs mt-1">
                    {{ __('Create role here.') }} <a href="{{ route('roles.index') }}"><b>{{ __('Create role') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                {{ Form::textarea('description',null, array('class' => 'form-control','rows'=> 3 , 'placeholder'=>__('Enter Description'))) }}
            </div>
        </div>
        <div class="col-md-6 form-group">
            {{Form::label('document',__('Document'),['class'=>'form-label'])}}<x-required></x-required>
            <div class="choose-file">
                <label for="document" class="form-label">
                    <input type="file" class="form-control file-validate" name="document" id="document" data-filename="document_create" required>
                    <p id="" class="file-error text-danger"></p>
                    <img id="image" src="{{asset(Storage::url('uploads/documentUpload')).'/'.$ducumentUpload->document}}" class="mt-3" style="width:25%;"/>
                </label>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}


<script>
    document.getElementById('document').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>

