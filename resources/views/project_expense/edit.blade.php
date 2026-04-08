{{ Form::model($expense, ['route' => ['projects.expenses.update',[$project->id,$expense->id]], 'id' => 'edit_expense', 'method' => 'POST','enctype' => 'multipart/form-data' , 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">

    <div class="row">
        <div class="col-12 col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'),['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control','required'=>'required','placeholder'=>__('Enter Name')]) }}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {{ Form::label('date', __('Date'),['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('date', null, ['class' => 'form-control ' ,'required'=>'required','placeholder'=>__('Enter Date')]) }}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {{Form::label('amount',__('Amount'),['class'=>'form-label'])}}<x-required></x-required>
                <div class="form-group price-input input-group search-form">
                    <span class="input-group-text bg-transparent">{{\Auth::user()->currencySymbol()}}</span>
                    {{Form::number('amount',null,array('class'=>'form-control','required' => 'required','min' => '0','placeholder'=>__('Enter Amount')))}}
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {{ Form::label('task_id', __('Task'),['class' => 'form-label']) }}
                <select class="form-control select" name="task_id" id="task_id">
                    <option class="text-muted" value="0" disabled selected> Choose Task </option>
                    @foreach($project->tasks as $task)
                        <option value="{{ $task->id }}" {{ ($task->id == $expense->task_id) ? 'selected' : '' }}>{{ $task->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create task here.') }} <a href="{{ route('projects.tasks.index', $project->id) }}"><b>{{ __('Create task') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'),['class' => 'form-label']) }}
                <small class="form-text text-muted mb-2 mt-0">{{__('This textarea will autosize while you type')}}</small>
                {{ Form::textarea('description', null, ['class' => 'form-control','rows' => '1','data-toggle' => 'autosize','placeholder'=>__('Enter Description')]) }}
            </div>
        </div>
        <div class="col-12 col-md-12">
            {{Form::label('attachment',__('Attachment'),['class'=>'form-label'])}}
            <div class="choose-file form-group">
                <label for="attachment" class="form-label">
                    <div>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="attachment" id="attachment" data-filename="attachment_create">
                </label>
                <p class="attachment_create"></p>
            </div>
        </div>

    </div>
</div>


<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}

