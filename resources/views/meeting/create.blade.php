{{Form::open(array('url'=>'meeting','method'=>'post'  , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['meeting']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('branch_id',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
                <select class="form-control select" name="branch_id" id="branch_id" placeholder="Select Branch" required>
                    <option value="">{{__('Select Branch')}}</option>
                    <option value="0">{{__('All Branch')}}</option>
                    @foreach($branch as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('department_id',__('Department'),['class'=>'form-label'])}}<x-required></x-required>
                <span id="department_div">
                    <select class="form-control select" name="department_id[]" id="department_id" placeholder="Select Department" required>
                    </select>
                    <div class="text-xs mt-1">
                        {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                    </div>
                </span>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('employee_id',__('Employee'),['class'=>'form-label'])}}<x-required></x-required>
                <span id="employee_div">
                    <select class="form-control select" name="employee_id[]" id="employee_id" placeholder="Select Employee" required>
                    </select>
                    <div class="text-xs mt-1">
                        {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                    </div>
                </span>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('title',__('Meeting Title'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Meeting Title'), 'required' => 'required'))}}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('date',__('Meeting Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('date',null,array('class'=>'form-control ', 'required' => 'required'))}}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{Form::label('time',__('Meeting Time'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::time('time',null,array('class'=>'form-control timepicker', 'required' => 'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('note',__('Meeting Note'),['class'=>'form-label'])}}
                {{Form::textarea('note',null,array('class'=>'form-control','placeholder'=>__('Enter Meeting Note')))}}
            </div>
        </div>

        @if(isset($settings['google_calendar_enable']) && $settings['google_calendar_enable'] == 'on')
            <div class="form-group col-md-6 mb-0">
                {{Form::label('synchronize_type',__('Synchronize in Google Calendar ?'),array('class'=>'form-label')) }}
                <div class="form-switch">
                    <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow" value="google_calender">
                    <label class="form-check-label" for="switch-shadow"></label>
                </div>
            </div>
        @endif

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}

<script>
    $(document).ready(function() {
        $('#department_id').empty();
        $('#department_id').append('<option value="">{{__('Select Department')}}</option>');
        $('#employee_id').empty();
        $('#employee_id').append('<option value="">{{__('Select Employee')}}</option>');
    });
</script>
