<div class="{{ $divClass }}">
    <div class="form-group">
        {{Form::label($name,$label,['class'=>'form-label'])}}@if($required)<x-required></x-required> @endif
        {{Form::text($name,$value,array('class'=>$class,'placeholder'=>$placeholder,'pattern' => '^\+\d{1,3}\d{9,13}$','id'=>$id,'required'=>$required))}}
        <div class=" text-xs text-danger mt-1">
            {{ __('Please use with country code. (ex. +251)') }}
        </div>
    </div>
</div>
