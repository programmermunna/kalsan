{{Form::open(array('url'=>'holiday','method'=>'post' , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['holiday']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('occasion',__('Occasion'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('occasion',null,array('class'=>'form-control' , 'placeholder' => __('Enter Occasion'), 'required' => 'required'))}}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('date',__('Start Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control ', 'required' => 'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('end_date',__('End Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('end_date',null,array('class'=>'form-control', 'required' => 'required'))}}
        </div>
    </div>
    @if (isset($settings['google_calendar_enable']) && $settings['google_calendar_enable'] == 'on')
        <div class="form-group col-md-6">
            {{Form::label('synchronize_type',__('Synchronize in Google Calendar ?'),array('class'=>'form-label')) }}
            <div class=" form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow" value="google_calender">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    @endif
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

