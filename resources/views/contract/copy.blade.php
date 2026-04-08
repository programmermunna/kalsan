
{{ Form::model($contract, array('route' => array('contract.copy.store', $contract->id),  'method' => 'POST')) }}
<div class="modal-body">

    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('subject', __('Subject'),['class' => 'form-label']) }}
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required', 'placeholder' => __('Enter Subject'))) }}
        </div>
        <div class="form-group col-md-6">
                {{ Form::label('client', __('Client'),['class'=>'form-label'])}}
                {{ Form::select('client', $clients, null, ['class' => 'form-control select client_select', 'id' => 'client_select']) }}
                <div class="text-xs mt-1">
                    {{ __('Create client here.') }} <a href="{{ route('clients.index') }}"><b>{{ __('Create client') }}</b></a>
                </div>
            </div>

            <div class="col-md-6 form-group">
                {{ Form::label('project', __('Project'), ['class' => 'form-label']) }}
                <div class="project-div">
                {{ Form::select('project', $project, null, ['class' => 'form-control select project_select', 'id' => 'project_id', 'name' => 'project_id[]']) }}
                <div class="text-xs mt-1">
                    {{ __('Create project here.') }} <a href="{{ route('projects.index') }}"><b>{{ __('Create project') }}</b></a>
                </div>
                </div>
            </div>
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Contract Type'),['class' => 'form-label']) }}
            {{ Form::select('type', $contractTypes,null, array('class' => 'form-control ','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create contract type here.') }} <a href="{{ route('contractType.index') }}"><b>{{ __('Create contract type') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('value', __('Contract Value'),['class' => 'form-label']) }}
            {{ Form::number('value', null, array('class' => 'form-control','required'=>'required','stage'=>'0.01', 'placeholder' => __('Enter Value'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class' => 'form-label']) }}
            {{ Form::date('start_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('end_date', __('End Date'),['class' => 'form-label']) }}
            {{ Form::date('end_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3', 'placeholder' => __('Enter Description')]) !!}
        </div>
    </div>

<div class="modal-footer pr-0">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{Form::submit(__('Copy'),array('class'=>'btn  btn-primary'))}}
</div>
</div>
{{ Form::close() }}


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
