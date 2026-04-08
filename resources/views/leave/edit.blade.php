{{Form::model($leave,array('route' => array('leave.update', $leave->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['leave']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}

    @if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('employee_id',__('Employee') ,['class'=>'form-label'])}}<x-required></x-required>
                    {{Form::select('employee_id',$employees,null,array('class'=>'form-control select','placeholder'=>__('Select Employee') ,'required' =>'required'))}}
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('leave_type_id',__('Leave Type'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('leave_type_id',$leavetypes,null,array('class'=>'form-control select','placeholder'=>__('Select Leave Type'),'required' =>'required'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('start_date',__('Start Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('start_date',null,array('class'=>'form-control ','required' =>'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('end_date',__('End Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('end_date',null,array('class'=>'form-control ','required' =>'required'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('leave_reason',__('Leave Reason'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::textarea('leave_reason',null,array('class'=>'form-control','placeholder'=>__('Leave Reason'),'required' =>'required'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm text-right" data-ajax-popup-over="true" id="grammarCheck" data-url="{{ route('grammar',['grammar']) }}"
               data-bs-placement="top" data-title="{{ __('Grammar check with AI') }}">
                <i class="ti ti-rotate"></i> <span>{{__('Grammar check with AI')}}</span>
            </a>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('remark',__('Remark'),['class'=>'form-label'])}}
                {{Form::textarea('remark',null,array('class'=>'form-control grammer_textarea','placeholder'=>__('Leave Remark'),'required' =>'required'))}}
            </div>
        </div>
    </div>
    @role('Company')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('status',__('Status'))}}
                <select name="status" id="" class="form-control select2">
                    <option value="">{{__('Select Status')}}</option>
                    <option value="pending" @if($leave->status=='Pending') selected="" @endif>{{__('Pending')}}</option>
                    <option value="approval" @if($leave->status=='Approval') selected="" @endif>{{__('Approval')}}</option>
                    <option value="reject" @if($leave->status=='Reject') selected="" @endif>{{__('Reject')}}</option>
                </select>
            </div>
        </div>
    </div>
    @endrole

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

<script>
    var employee_id = "{{$employee_id}}";
    var leave_type_id = "{{isset($leave) ? $leave->leave_type_id : null}}";
    leaveCount(employee_id, leave_type_id)
</script>
