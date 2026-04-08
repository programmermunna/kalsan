{{Form::model($training,array('route' => array('training.update', $training->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['training']) }}"
               data-bs-placement="top"  data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('branch',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('branch',$branches,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('trainer_option',__('Trainer Option'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('trainer_option',$options,null,array('class'=>'form-control select','required'=>'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('training_type',__('Training Type'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('training_type',$trainingTypes,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create training type here.') }} <a href="{{ route('trainingtype.index') }}"><b>{{ __('Create training type') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('trainer',__('Trainer'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('trainer',$trainers,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create trainer here.') }} <a href="{{ route('trainer.index') }}"><b>{{ __('Create trainer') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('training_cost',__('Training Cost'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::number('training_cost',null,array('class'=>'form-control','step'=>'0.01','required'=>'required', 'placeholder'=>__('Training Cost')))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('employee',__('Employee'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('employee',$employees,null,array('class'=>'form-control select','required'=>'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('start_date',__('Start Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('start_date',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('end_date',__('End Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('end_date',null,array('class'=>'form-control ','required'=>'required'))}}
            </div>
        </div>
        <div class="form-group col-lg-12">
            {{Form::label('description',__('Description'),['class'=>'form-label'])}}
            {{Form::textarea('description',null,array('class'=>'form-control','placeholder'=>__('Description')))}}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
    {{Form::close()}}
