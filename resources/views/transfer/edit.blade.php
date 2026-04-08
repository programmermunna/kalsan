{{Form::model($transfer,array('route' => array('transfer.update', $transfer->id), 'method' => 'PUT'  , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['transfer']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('branch_id',__('Branch'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('branch_id',$branches, $transfer->branch_id,array('class'=>'form-control select', 'id' => 'branch_id', 'required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('department_id',__('Department'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('department_id',$departments,null,array('class'=>'form-control select', 'id' => 'department_id', 'required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6 ">
            {{ Form::label('employee_id', __('Employee'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('employee_id', $employees,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('transfer_date',__('Transfer Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('transfer_date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>

        <div class="form-group col-lg-12">
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


    <script type="text/javascript">
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            var d_id = $('#department_id').val();
            getDepartment(b_id);
            getEmployee(d_id);
        });

        $(document).on('change', '#branch_id', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getEmployee(department_id);
        });

        function getDepartment(branch_id) {
            $.ajax({
                url: '{{ route('employee.getdepartment') }}',
                method: 'POST',
                data: {
                    "branch_id": branch_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append(
                        '<option value="" disabled>{{ __('Select Department') }}</option>');

                    $.each(data, function(key, value) {
                        var selected = '';
                        if (key == '{{ $transfer->department_id }}') {
                            selected = 'selected';
                        }

                        $('#department_id').append('<option value="' + key + '"  ' + selected + '>' + value + '</option>');
                    });
                }
            });
        }

        function getEmployee(did) {
            $.ajax({
                url: '{{ route('department.getemployee') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#employee_id').empty();
                    $('#employee_id').append('<option value="">Select Employee</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $transfer->employee_id }}') {
                            select = 'selected';
                        }

                        $('#employee_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }
    </script>
