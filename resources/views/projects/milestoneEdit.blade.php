
{{ Form::model($milestone, array('route' => array('project.milestone.update', $milestone->id), 'method' => 'POST')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['project milestone']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class' => 'form-label']) }}
            {{ Form::text('title', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Enter Title'))) }}
            @error('title')
            <span class="invalid-title" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('status', __('Status'),['class' => 'form-label']) }}
            {!! Form::select('status',\App\Models\Project::$project_status, null,array('class' => 'form-control selectric select','required'=>'required')) !!}
            @error('client')
            <span class="invalid-client" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class' => 'col-form-label']) }}
            {{ Form::date('start_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class' => 'col-form-label']) }}
            {{ Form::date('due_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('cost', __('Cost'),['class' => 'col-form-label']) }}
            {{ Form::number('cost', null, array('class' => 'form-control','required'=>'required','stage'=>'0.01','placeholder'=>__('Enter Cost'))) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2','placeholder'=>__('Enter Description')]) !!}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="task-summary" class="col-form-label">{{ __('Progress')}}</label>
            <input type="range" class="slider w-100 mb-0 " name="progress" id="myRange" value="{{($milestone->progress)?$milestone->progress:'0'}}" min="0" max="100" oninput="ageOutputId.value = myRange.value">
            <output name="ageOutputName" id="ageOutputId">{{($milestone->progress)?$milestone->progress:"0"}}</output>
            %
        </div>
    </div>


    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Edit')}}" class="btn btn-primary">
    </div>

{{ Form::close() }}

