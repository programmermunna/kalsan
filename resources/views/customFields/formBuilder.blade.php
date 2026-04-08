@if($customFields)
    @foreach($customFields as $customField)
        @if($customField->type == 'text')
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::text('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>
        @elseif($customField->type == 'email')
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::email('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>
        @elseif($customField->type == 'number')
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::number('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>
        @elseif($customField->type == 'date')
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::date('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>
        @elseif($customField->type == 'textarea')
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::textarea('customField['.$customField->id.']', null, array('class' => 'form-control', 'rows' => 1)) }}
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif


