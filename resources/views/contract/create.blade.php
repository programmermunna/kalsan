{{ Form::open(array('url' => 'contract' , 'class'=>'needs-validation', 'novalidate') )}}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['contract']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('subject', '', array('class' => 'form-control','required'=>'required', 'placeholder' => __('Enter Suject'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('client_name', __('Client'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('client_name', $clients, null, ['class' => 'form-control select client_select', 'id' => 'client_select' , 'required' => 'required']) }}
            <div class="text-xs mt-1">
                {{ __('Create client here.') }} <a href="{{ route('clients.index') }}"><b>{{ __('Create client') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6" >
            {{ Form::label('projects', __('Projects'),['class'=>'form-label'])}}
            <select class="form-control select project_select" id="project_id" name="project_id" >
                <option value="">{{__('Select Project')}}</option>
            </select>
            <div class="text-xs mt-1">
                {{ __('Create project here.') }} <a href="{{ route('projects.index') }}"><b>{{ __('Create project') }}</b></a>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('type', __('Contract Type'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('type', $contractTypes,null, array('class' => 'form-control','data-toggle="select"','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create contract type here.') }} <a href="{{ route('contractType.index') }}"><b>{{ __('Create contract type') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('value', __('Contract Value'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('value', '', array('class' => 'form-control','required'=>'required','stage'=>'0.01', 'placeholder' => __('Enter Value'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::date('start_date', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('end_date', __('End Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::date('end_date', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3', 'placeholder' => __('Enter Description')]) !!}
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script>
    if ($(".multi-select").length > 0) {
        $( $(".multi-select") ).each(function( index,element ) {
            var id = $(element).attr('id');
            var multipleCancelButton = new Choices(
                '#'+id, {
                    removeItemButton: true,
                }
            );
        });
    }
</script>

<script type="text/javascript">

    $( ".client_select" ).change(function() {

        var client_id = $(this).val();
        getparent(client_id);
    });

    function getparent(bid) {

        $.ajax({
            url: `{{ url('contract/clients/select')}}/${bid}`,
            type: 'GET',
            success: function (data) {
                $("#project_id").html('');
                $('#project_id').append('<select class="form-control" id="project_id" name="project_id[]"  ></select>');
                $.each(data, function (i, item) {
                    $('#project_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                });


                if (data == '') {
                    $('#project_id').empty();
                }
            }
        });
    }

</script>

