{{Form::model($overtime,array('route' => array('overtime.update', $overtime->id), 'method' => 'PUT' , 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">

    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('title', __('Title'),['class'=>'form-label']) }}<x-required></x-required>
                    {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('number_of_days', __('Number Of Days'),['class'=>'form-label']) }}<x-required></x-required>
                    {{ Form::text('number_of_days',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Number of days'))) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('hours', __('Hours'),['class'=>'form-label']) }}<x-required></x-required>
                    {{ Form::text('hours',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Hours'))) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('rate', __('Rate'),['class'=>'form-label']) }}<x-required></x-required>
                    {{ Form::number('rate',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Rate'))) }}
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


