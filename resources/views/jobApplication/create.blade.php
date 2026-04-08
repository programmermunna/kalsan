{{Form::open(array('url'=>'job-application','method'=>'post', 'enctype' => "multipart/form-data" , 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('job',__('Job'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('job',$jobs,null,array('class'=>'form-control','id'=>'jobs','required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create job here.') }} <a href="{{ route('job.index') }}"><b>{{ __('Create job') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control name' , 'placeholder' => __('Enter Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('email',null,array('class'=>'form-control' , 'placeholder' => __('Enter email'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            <x-Mobile label="{{__('Phone')}}" placeholder="{{__('Enter Phone')}}" name="phone" required></x-Mobile>
        </div>
        <div class="form-group col-md-6 dob d-none">
            {!! Form::label('dob', __('Date of Birth'),['class'=>'form-label']) !!}
            {!! Form::date('dob', old('dob'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group col-md-6 gender d-none">
            {!! Form::label('gender', __('Gender'),['class'=>'form-label']) !!}
            <div class="d-flex radio-check">
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_male" value="Male" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_male">{{__('Male')}}</label>
                </div>
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_female" value="Female" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_female">{{__('Female')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 country d-none">
            {{Form::label('country',__('Country'),['class'=>'form-label'])}}
            {{Form::text('country',null,array('class'=>'form-control', 'placeholder' => __('Enter Country')))}}
        </div>
        <div class="form-group col-md-6 country d-none">
            {{Form::label('state',__('State'),['class'=>'form-label'])}}
            {{Form::text('state',null,array('class'=>'form-control', 'placeholder' => __('Enter State')))}}
        </div>
        <div class="form-group col-md-6 country d-none">
            {{Form::label('city',__('City'),['class'=>'form-label'])}}
            {{Form::text('city',null,array('class'=>'form-control', 'placeholder' => __('Enter City')))}}
        </div>

        <div class="form-group col-md-6 profile d-none">
            {{Form::label('profile',__('Profile'),['class'=>'form-label'])}}
            <div class="choose-file form-group">
                <label for="profile" class="form-label">
                    <div>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="profile" id="profile" data-filename="profile_create">
                </label>
                <p class="profile_create"></p>
            </div>
        </div>
        <div class="form-group col-md-6 resume d-none">
            {{Form::label('resume',__('CV / Resume'),['class'=>'form-label'])}}
            <div class="choose-file form-group">
                <label for="resume" class="form-label">
                    <div>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="resume" id="resume" data-filename="resume_create">
                </label>
                <p class="resume_create"></p>
            </div>
        </div>
        <div class="form-group col-md-12 letter d-none">
            {{Form::label('cover_letter',__('Cover Letter'),['class'=>'form-label'])}}
            {{Form::textarea('cover_letter',null,array('class'=>'form-control', 'placeholder' => __('Enter Cover Letter')))}}
        </div>
        @foreach($questions as $question)
            <div class="form-group col-md-12  question question_{{$question->id}} d-none">
                {{Form::label($question->question,$question->question,['class'=>'form-label'])}}
                <input type="text" placeholder="Write Here..." class="form-control" name="question[{{$question->question}}]" {{($question->is_required=='yes')?'':''}}>
            </div>
        @endforeach

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


