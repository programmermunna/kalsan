
{{ Form::open(['url' => 'appraisal', 'method' => 'post' , 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>

                <select name="branch" id="branch" required class="form-control ">
                    <option selected disabled value="">{{__('Select Branch')}}</option>

                    @foreach ($brances as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                </div>
            </div>
        </div>


        <div class="col-md-6 mt-2">
            <div class="form-group">
                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}

                <div class="employee_div">

                    <select name="employee" id="employee" class="form-control " required>
                    </select>
                    <div class="text-xs mt-1">
                        {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('appraisal_date', __('Select Month*'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::month('appraisal_date', '', ['class' => 'form-control ','autocomplete'=>'off' ,'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('remark', __('Remarks'), ['class' => 'col-form-label']) }}
                {{ Form::textarea('remark', null, ['class' => 'form-control', 'rows' => '3','placeholder'=>'Enter remark']) }}
            </div>
        </div>
    </div>
    <div class="row" id="stares">
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}



<script>
 $(document).ready(function() {
        $('#employee').empty();
        $('#employee').append('<option value="">{{__('Select Employee')}}</option>');
    });
    $('#employee').change(function(){

        var emp_id = $('#employee').val();
        $.ajax({
            url: "{{ route('empByStar') }}",
            type: "post",
            data:{
                "employee": emp_id,
                "_token": "{{ csrf_token() }}",
            },

            cache: false,
            success: function(data) {
                $('#stares').html(data.html);
            }
        })
    });
</script>

<script>
    $('#branch').on('change', function() {
        var branch_id = this.value;

        $.ajax({
            url: "{{ route('getemployee') }}",
            type: "post",
            data:{
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            },

            cache: false,
            success: function(data) {

                $('#employee').html('<option value="">Select Employee</option>');
                $.each(data.employee, function (key, value) {
                    $("#employee").append('<option value="' + value.id + '">' + value.name + '</option>');
                });

            }
        })


    });
</script>







