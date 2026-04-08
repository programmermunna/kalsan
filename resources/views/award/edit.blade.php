{{Form::model($award,array('route' => array('award.update', $award->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['award']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('employee_id', __('Employee'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('employee_id', $employees,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('award_type', __('Award Type'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('award_type', $awardtypes,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create award type here.') }} <a href="{{ route('awardtype.index') }}"><b>{{ __('Create award type') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{Form::label('date',__('Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control ','required'=>'required'))}}
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{Form::label('gift',__('Gift'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('gift',null,array('class'=>'form-control','placeholder'=>__('Enter Gift'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('description',__('Description'),['class'=>'form-label'])}}
            {{Form::textarea('description',null,array('class'=>'form-control','placeholder'=>__('Enter Description')))}}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
