{{ Form::open(array('url' => 'support','enctype'=>"multipart/form-data" , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
    <div class="text-end mb-3">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['support']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('subject', '', array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Suject'))) }}
        </div>
        @if(\Auth::user()->type !='client')
            <div class="form-group col-md-6">
                {{Form::label('user',__('Support for User'),['class'=>'form-label'])}}
                {{Form::select('user',$users,null,array('class'=>'form-control select'))}}
                <div class="text-xs mt-1">
                    {{ __('Create user here.') }} <a href="{{ route('users.index') }}"><b>{{ __('Create user') }}</b></a>
                </div>
            </div>
        @endif
        <div class="form-group col-md-6">
            {{Form::label('priority',__('Priority'),['class'=>'form-label'])}}
            {{Form::select('priority',$priority,null,array('class'=>'form-control select'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('status',__('Status'),['class'=>'form-label'])}}
            {{Form::select('status',$status,null,array('class'=>'form-control select'))}}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('end_date', __('End Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::date('end_date', '', array('class' => 'form-control','required'=>'required')) }}
        </div>


    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3', 'placeholder'=>__('Enter Description')]) !!}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{Form::label('attachment',__('Attachment'),['class'=>'form-label'])}}
        <label for="document" class="form-label">
            <input type="file" class="form-control file-validate" name="attachment" id="attachment" data-filename="attachment_create">
            <p id="" class="file-error text-danger"></p>
        </label>
        <img id="image" class="mt-2" style="width:25%;"/>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
    {{ Form::close() }}




<script>
    document.getElementById('attachment').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>
