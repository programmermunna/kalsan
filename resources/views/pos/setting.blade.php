<form method="post" action="{{ route('barcode.setting') }}" class="needs-validation" novalidate = ''>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('barcode_type', __('Barcode Type'), ['class' => 'form-label text-dark']) }}
                {{ Form::select('barcode_type', ['code128' => 'Code 128', 'code39' => 'Code 39', 'code93' => 'Code 93'], $settings['barcode_type'], ['class' => 'form-control', 'data-toggle' => 'select']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('barcode_format', __('Barcode Format'), ['class' => 'form-label text-dark']) }}
                {{ Form::select('barcode_format', ['css' => 'CSS', 'bmp' => 'BMP'], $settings['barcode_format'], ['class' => 'form-control', 'data-toggle' => 'select']) }}
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="submit" value="{{__('Save')}}" class="btn btn-primary">
    </div>
</form>
