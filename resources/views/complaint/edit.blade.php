{{Form::model($complaint,array('route' => array('complaint.update', $complaint->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['complaint']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        @if(\Auth::user()->type !='Employee')
            <div class="form-group col-md-6 col-lg-6">
                {{ Form::label('complaint_from', __('Complaint From'),['class'=>'form-label'])}}<x-required></x-required>
                {{ Form::select('complaint_from', $employees,null, array('class' => 'form-control  select', 'id' => 'complaint_from', 'required'=>'required')) }}
                <div class="text-xs mt-1">
                    {{ __('Create complaint from here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create complaint from') }}</b></a>
                </div>
            </div>
        @endif
        <div class="form-group col-md-6 col-lg-6">
            {{Form::label('complaint_against',__('Complaint Against'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('complaint_against',$employees,null,array('class'=>'form-control select', 'id' => 'complaint_against', 'required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create complaint against here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create complaint against') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{Form::label('title',__('Title'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('title',null,array('class'=>'form-control','required'=>'required' , 'placeholder'=>__('Enter Title')))}}
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{Form::label('complaint_date',__('Complaint Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('complaint_date',null,array('class'=>'form-control','required'=>'required'))}}
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

<script>
    $(document).ready(function() {
        updateDropdowns();
    });
</script>