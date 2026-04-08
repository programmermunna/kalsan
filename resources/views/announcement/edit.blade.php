{{Form::model($announcement,array('route' => array('announcement.update', $announcement->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['announcement']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('title',__('Announcement Title'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Announcement Title') ,'required' => 'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('branch_id',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
                <select class="form-control select" name="branch_id" id="branch_id" placeholder="Select Branch" required>
                    <option value="">{{__('Select Branch')}}</option>
                    <option value="0" {{ isset($announcement) && $announcement->branch_id == 0 ? 'selected' : '' }}>{{__('All Branch')}}</option>
                    @foreach($branch as $branch)
                    <option value="{{ $branch->id }}" {{ isset($announcement) && ($announcement->branch_id == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('department_id',__('Department'),['class'=>'form-label'])}}<x-required></x-required>
                <select class="form-control select" name="department_id[]" id="department_id" placeholder="Select Department" required>
                    <option value="">{{__('Select Department')}}</option>
                    <option value="0" {{ isset($announcement) && in_array(0, json_decode($announcement->department_id)) ? 'selected' : '' }}>{{__('All Department')}}</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ isset($announcement) && in_array(json_decode($announcement->department_id)[0], json_decode($announcement->department_id)) ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('start_date',__('Announcement start Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('start_date',null,array('class'=>'form-control' ,'required' => 'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('end_date',__('Announcement End Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('end_date',null,array('class'=>'form-control' ,'required' => 'required'))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('description',__('Announcement Description'),['class'=>'form-label'])}}
                {{Form::textarea('description',null,array('class'=>'form-control','placeholder'=>__('Enter Announcement Title')))}}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

