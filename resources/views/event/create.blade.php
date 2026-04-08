{{ Form::open(['url' => 'event', 'method' => 'post'  , 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['event']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                <select class="form-control select" name="branch_id" id="branch_id"
                        placeholder="{{ __('Select Branch') }}" required>
                    <option value="">{{ __('Select Branch') }}</option>
                    <option value="0">{{ __('All Branch') }}</option>
                    @foreach ($branch as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="department_div">
                    <select class="form-control department_id" name="department_id[]" placeholder="Select Designation" required>
                        <option value="">{{ __('Select Designation') }}</option>
                    </select>
                    <div class="text-xs mt-1">
                        {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('employee_id', __('Employee'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="employee_div">
                    <select class="form-control employee_id" name="employee_id[]" placeholder="Select Employee" required>
                        <option value="">{{ __('Select Employee') }}</option>
                    </select>
                    <div class="text-xs mt-1">
                        {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
            <div class="form-group">
                {{ Form::label('title', __('Event Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control ', 'placeholder' => __('Enter Event Title') , 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 col-xl-6">
            <div class="form-group">
                {{ Form::label('start_date', __('Event start Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('start_date', null, ['class' => 'form-control datetime-local', 'autocomplete'=>'off' , 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 col-xl-6">
            <div class="form-group">
                {{ Form::label('end_date', __('Event End Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('end_date', null, ['class' => 'form-control datetime-local','autocomplete'=>'off' , 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
            <div class="form-group">
                {{ Form::label('color', __('Event Select Color'), ['class' => 'form-label d-block mb-3']) }}
                <div class="btn-group-toggle btn-group-colors event-tag" data-toggle="buttons">
                    <label class="btn bg-info active p-3"><input type="radio" name="color" value="event-info" checked class="d-none"></label>
                    <label class="btn bg-warning p-3"><input type="radio" name="color" value="event-warning" class="d-none"></label>
                    <label class="btn bg-danger p-3"><input type="radio" name="color" value="event-danger" class="d-none"></label>
                    <label class="btn bg-success p-3"><input type="radio" name="color" value="event-success" class="d-none"></label>
                    <label class="btn bg-custom p-3" style="background-color: #51459d !important"><input type="radio" name="color" class="d-none" value="event-primary"></label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {{ Form::label('description', __('Event Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Event Description'),'rows'=>'5']) }}
        </div>
        @if(isset($settings['google_calendar_enable']) && $settings['google_calendar_enable'] == 'on')
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
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
