@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Print-Settings') }}</li>
@endsection

@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_logo = Utility::getValByName('company_logo');
    $company_favicon = Utility::getValByName('company_favicon');
    $lang = Utility::getValByName('default_language');
@endphp
@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
          $(document).on("change", "select[name='purchase_template'], input[name='purchase_color']", function() {
            var template = $("select[name='purchase_template']").val();
            var color = $("input[name='purchase_color']:checked").val();
            $('#purchase_frame').attr('src', '{{ url('/purchase/preview') }}/' + template + '/' + color);
            });

            $(document).on("change", "select[name='quotation_template'], input[name='quotation_color']", function() {
            var template = $("select[name='quotation_template']").val();
            var color = $("input[name='quotation_color']:checked").val();
            $('#quotation_frame').attr('src', '{{ url('/quotation/preview') }}/' + template + '/' + color);
            });

            $(document).on("change", "select[name='pos_template'], input[name='pos_color']", function() {
            var template = $("select[name='pos_template']").val();
            var color = $("input[name='pos_color']:checked").val();
            $('#pos_frame').attr('src', '{{ url('/pos/preview') }}/' + template + '/' + color);
            });

    document.getElementById('pos_logo').onchange = function () {
    var src = URL.createObjectURL(this.files[0])
    document.getElementById('pos_image').src = src
    }

    </script>
@endpush
@section('content')
    <div class="col-sm-12 mt-4">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-purchase-tab" data-bs-toggle="pill" href="#pills-purchase"
                            role="tab" aria-controls="pills-purchase"
                            aria-selected="false">{{ __('Purchase Print Setting') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-quotation-tab" data-bs-toggle="pill" href="#pills-quotation"
                            role="tab" aria-controls="pills-pos"
                            aria-selected="false">{{ __('Quotation Print Setting') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-pos-tab" data-bs-toggle="pill" href="#pills-pos" role="tab"
                            aria-controls="pills-pos" aria-selected="false">{{ __('POS Print Setting') }}</a>
                    </li>

                </ul>

                <div class="tab-content" id="pills-tabContent">

                    <!--Purchase Setting-->
                    <div class="tab-pane fade  show active" id="pills-purchase" role="tabpanel"
                        aria-labelledby="pills-purchase-tab">

                        <div class="bg-none">
                            <div class="row company-setting">
                                <div class="col-md-3">
                                    <div class="card-body">
                                       
                                        <form id="setting-form" method="post"
                                            action="{{ route('purchase.template.setting') }}" enctype="multipart/form-data" class="needs-validation" novalidate = ''>
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="address"
                                                            class="form-label">{{ __('Purchase Template') }}</label>
                                                        <select class="form-control" name="purchase_template">
                                                            @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                                                <option value="{{ $key }}"
                                                                    {{ isset($settings['purchase_template']) && $settings['purchase_template'] == $key ? 'selected' : '' }}>
                                                                    {{ $template }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="address"
                                                            class="col-form-label">{{ __('QR Display?') }}</label>
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-check form-switch custom-switch-v1 mt-2">
                                                                <input type="hidden" name="pos_purchase_qr_display"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    id="customswitchv1-1 pos_purchase_qr_display"
                                                                    name="pos_purchase_qr_display"
                                                                    {{ isset($settings['pos_purchase_qr_display']) && $settings['pos_purchase_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('Color Input') }}</label>
                                                <div class="row gutters-xs">
                                                    @foreach (Utility::templateData()['colors'] as $key => $color)
                                                        <div class="col-auto">
                                                            <label class="colorinput">
                                                                <input name="purchase_color" type="radio"
                                                                    value="{{ $color }}" class="colorinput-input"
                                                                    {{ isset($settings['purchase_color']) && $settings['purchase_color'] == $color ? 'checked' : '' }}>
                                                                <span class="colorinput-color"
                                                                    style="background: #{{ $color }}"></span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('Purchase Logo') }}</label>
                                                <div class="choose-files mt-2 ">
                                                    <label for="purchase_logo">
                                                        <div class=" bg-primary purchase_logo_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="form-control file" name="purchase_logo"
                                                            id="purchase_logo" data-filename="purchase_logo_update">
                                                        <img id="purchase_image" class="mt-2" style="width:25%;" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2 text-end">
                                                <input type="submit" value="{{ __('Save') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    @if (isset($settings['purchase_template']) && isset($settings['purchase_color']))
                                        <iframe id="purchase_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('purchase.preview', [$settings['purchase_template'], $settings['purchase_color']]) }}"></iframe>
                                    @else
                                        <iframe id="purchase_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('purchase.preview', ['template1', 'fffff']) }}"></iframe>
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>

                    <!--Quotation Setting-->
                    <div class="tab-pane fade" id="pills-quotation" role="tabpanel"
                        aria-labelledby="pills-quotation-tab">
                        <div class="bg-none">
                            <div class="row company-setting">
                                <div class="col-md-3">
                                    <div class="card-body">
                                       
                                        <form id="setting-form" method="post"
                                            action="{{ route('quotation.template.setting') }}"
                                            enctype="multipart/form-data" class="needs-validation" novalidate>
                                            @csrf
                                            <div class="form-group">
                                                <label for="address"
                                                    class="form-label">{{ __('Quotation Template') }}</label>
                                                <select class="form-control" name="quotation_template">
                                                    @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                                        <option value="{{ $key }}"
                                                            {{ isset($settings['quotation_template']) && $settings['quotation_template'] == $key ? 'selected' : '' }}>
                                                            {{ $template }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('Color Input') }}</label>
                                                <div class="row gutters-xs">
                                                    @foreach (Utility::templateData()['colors'] as $key => $color)
                                                        <div class="col-auto">
                                                            <label class="colorinput">
                                                                <input name="quotation_color" type="radio"
                                                                    value="{{ $color }}" class="colorinput-input"
                                                                    {{ isset($settings['quotation_color']) && $settings['quotation_color'] == $color ? 'checked' : '' }}>
                                                                <span class="colorinput-color"
                                                                    style="background: #{{ $color }}"></span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('Quotation Logo') }}</label>
                                                <div class="choose-files mt-2 ">
                                                    <label for="quotation_logo">
                                                        <div class=" bg-primary quotation_logo_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="form-control file"
                                                            name="quotation_logo" id="quotation_logo"
                                                            data-filename="quotation_logo_update">
                                                        <img id="pos_image" class="mt-2" style="width:25%;" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2 text-end">
                                                <input type="submit" value="{{ __('Save') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    @if (isset($settings['quotation_template']) && isset($settings['quotation_color']))
                                        <iframe id="quotation_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('quotation.preview', [$settings['quotation_template'], $settings['quotation_color']]) }}"></iframe>
                                    @else
                                        <iframe id="quotation_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('quotation.preview', ['template1', 'fffff']) }}"></iframe>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--POS Setting-->
                    <div class="tab-pane fade" id="pills-pos" role="tabpanel" aria-labelledby="pills-pos-tab">
                        <div class="bg-none">
                            <div class="row company-setting">
                                <div class="col-md-3">
                                    <div class="card-body">
                                       
                                        <form id="setting-form" method="post"
                                            action="{{ route('pos.template.setting') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                                            @csrf
                                            <div class="form-group">
                                                <label for="address" class="form-label">{{ __('POS Template') }}</label>
                                                <select class="form-control" name="pos_template">
                                                    @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                                        <option value="{{ $key }}"
                                                            {{ isset($settings['pos_template']) && $settings['pos_template'] == $key ? 'selected' : '' }}>
                                                            {{ $template }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('Color Input') }}</label>
                                                <div class="row gutters-xs">
                                                    @foreach (Utility::templateData()['colors'] as $key => $color)
                                                        <div class="col-auto">
                                                            <label class="colorinput">
                                                                <input name="pos_color" type="radio"
                                                                    value="{{ $color }}" class="colorinput-input"
                                                                    {{ isset($settings['pos_color']) && $settings['pos_color'] == $color ? 'checked' : '' }}>
                                                                <span class="colorinput-color"
                                                                    style="background: #{{ $color }}"></span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">{{ __('POS Logo') }}</label>
                                                <div class="choose-files mt-2 ">
                                                    <label for="pos_logo">
                                                        <div class=" bg-primary pos_logo_update"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="form-control file" name="pos_logo"
                                                            id="pos_logo" data-filename="pos_logo_update">
                                                        <img id="pos_image" class="mt-2" style="width:25%;" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2 text-end">
                                                <input type="submit" value="{{ __('Save') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    @if (isset($settings['pos_template']) && isset($settings['pos_color']))
                                        <iframe id="pos_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('pos.preview', [$settings['pos_template'], $settings['pos_color']]) }}"></iframe>
                                    @else
                                        <iframe id="pos_frame" class="w-100 h-100" frameborder="0"
                                            src="{{ route('pos.preview', ['template1', 'fffff']) }}"></iframe>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
