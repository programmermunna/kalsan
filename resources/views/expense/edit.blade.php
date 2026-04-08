@extends('layouts.admin')
@section('page-title')
    {{__('Expense Edit')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('expense.index')}}">{{__('Expense')}}</a></li>
    <li class="breadcrumb-item">{{__('Expense Edit')}}</li>
@endsection

@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: true,
                defaultValues: {
                    'status': 1
                },
                show: function () {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }

                    // for item SearchBox ( this function is  custom Js )
                    JsSearchBox();


                },
                hide: function (deleteElement) {

                    if (confirm('Are you sure you want to delete this element?')) {
                        var el = $(this);
                        var id = $(el.find('.id')).val();
                        var amount = $(el.find('.amount')).html();
                        var type = $('input[name="type"]:checked').val();
                        var account_id = $('select[name="account_id"]').val();

                        $(".price").change();
                        $(".discount").change();
                        $('.item option').prop('hidden', false);
                        $('.item :selected').each(function () {
                            var ids = $(this).val();
                            if (ids) {
                                $('.item').not(this).find("option[value=" + ids + "]").prop('hidden', true);
                            }
                        });

                        if (id != undefined && id != null && id != '') {
                            $.ajax({
                                url: '{{route('expense.product.destroy')}}',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('#token').val()
                                },
                                data: {
                                    'id': id,
                                    'amount': amount,
                                    'type': type,
                                    'account_id': account_id,
                                },
                                cache: false,
                                success: function (data) {
                                    $('.item option').prop('hidden', false);
                                    $('.item :selected').each(function () {
                                        var id = $(this).val();
                                        $(".item option[value=" + id + "]").prop("hidden", true);
                                    });

                                    if (data.status) {
                                        show_toastr('success', data.message);
                                    } else {
                                        show_toastr('error', data.message);
                                    }
                                },
                                error: function (data) {
                                    data = data.responseJSON;
                                    show_toastr('error', data.error, 'error')
                                }

                            });
                        }


                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        var totalAmount= (parseFloat(subTotal));
                        $('.totalAmount').html(totalAmount.toFixed(2));
                    }

                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    changeItem(tr.find('.item'));
                }
                // Remove delete button for first row
                $('.repeater [data-repeater-item]').first().find('[data-repeater-delete]').remove();
            }

        }

        $(document).on('change', '#vender', function () {
            $('#vender_detail').removeClass('d-none');
            $('#vender_detail').addClass('d-block');
            $('#vender-box').removeClass('d-block');
            $('#vender-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data != '') {
                        $('#vender_detail').html(data);
                    } else {
                        $('#vender-box').removeClass('d-none');
                        $('#vender-box').addClass('d-block');
                        $('#vender_detail').removeClass('d-block');
                        $('#vender_detail').addClass('d-none');
                    }
                },

            });
        });
        $(document).on('click', '#remove', function () {
            $('#vender-box').removeClass('d-none');
            $('#vender-box').addClass('d-block');
            $('#vender_detail').removeClass('d-block');
            $('#vender_detail').addClass('d-none');
        });

        $(document).on('change', '.item', function () {

            changeItem($(this));
        });

        var bill_id = '{{$expense->id}}';

        function changeItem(element) {

            var iteams_id = element.val();
            var url = element.data('url');
            var el = element;

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id,
                },
                cache: false,
                success: function (data) {
                    var item = JSON.parse(data);

                    $.ajax({
                        url: '{{route('expense.items')}}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'bill_id': bill_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function (data) {
                            var billItems = JSON.parse(data);

                            if (billItems != null) {
                                var amount = (billItems.price * billItems.quantity);
                                $(el.parent().parent().parent().find('.quantity')).val(billItems.quantity);
                                $(el.parent().parent().parent().find('.price')).val(billItems.price);
                                $(el.parent().parent().parent().find('.discount')).val(billItems.discount);
                                $(el.parent().parent().parent().parent().find('.pro_description')).val(billItems.description);

                            } else {
                                $(el.parent().parent().parent().find('.quantity')).val(1);
                                $(el.parent().parent().parent().find('.price')).val(item.product.purchase_price);
                                $(el.parent().parent().parent().find('.discount')).val(0);
                                $(el.parent().parent().parent().parent().find('.pro_description')).val(item.product.description);
                            }


                            var taxes = '';
                            var tax = [];

                            var totalItemTaxRate = 0;
                            for (var i = 0; i < item.taxes.length; i++) {

                                taxes += '<span class="badge bg-primary p-2 px-3 rounded mt-1 me-1">' + item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' + '</span>';
                                tax.push(item.taxes[i].id);
                                totalItemTaxRate += parseFloat(item.taxes[i].rate);

                            }

                            var discount=$(el.parent().parent().parent().find('.discount')).val();

                            if (billItems != null) {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) * parseFloat((billItems.price * billItems.quantity)- discount);
                            } else {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) * parseFloat((item.product.purchase_price * 1)- discount);
                            }


                            $(el.parent().parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                            $(el.parent().parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                            $(el.parent().parent().parent().find('.taxes')).html(taxes);
                            $(el.parent().parent().parent().find('.tax')).val(tax);
                            $(el.parent().parent().parent().find('.unit')).html(item.unit);

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                            }

                            var totalItemPrice = 0;
                            var inputs_quantity = $(".quantity");
                            var priceInput = $('.price');
                            for (var j = 0; j < priceInput.length; j++) {

                                if (!isNaN(parseFloat(priceInput[j].value))) {
                                    var itemTotal = (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));

                                    totalItemPrice += itemTotal;
                                }
                            }

                            var totalItemTaxPrice = 0;
                            var itemTaxPriceInput = $('.itemTaxPrice');
                            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                                    if (billItems != null) {
                                        $(el.parent().parent().parent().find('.amount')).html(parseFloat(amount)+parseFloat(itemTaxPrice)-parseFloat(discount));
                                    } else {
                                        $(el.parent().parent().parent().find('.amount')).html(parseFloat(item.totalAmount)+parseFloat(itemTaxPrice));
                                    }
                                }
                            }


                            var totalItemDiscountPrice = 0;
                            var itemDiscountPriceInput = $('.discount');

                            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                                if (!isNaN(parseFloat(itemDiscountPriceInput[k].value))) {
                                    totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                                }
                            }



                            $('.subTotal').html(totalItemPrice.toFixed(2));
                            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                            $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice)).toFixed(2));
                            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));


                            var totalAmount= parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice);
                            $('.totalAmount').val(totalAmount.toFixed(2));


                        }
                    });


                },
            });
        }


        $(document).on('keyup', '.quantity', function () {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();

            var quantity = $(this).val();
            var price = $(el.find('.price')).val();
            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalInputItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalInputItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            var sumAmount = totalInputItemPrice;


            $('.subTotal').html(sumAmount.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

            //get hidden value of totalAmount
            var totalAmount= (parseFloat(subTotal));
            $('.totalAmount').val(totalAmount.toFixed(2));

        })

        $(document).on('keyup change', '.price', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();
            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var totalItemPrice = (quantity * price)-discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            var sumAmount = totalItemPrice;


            $('.subTotal').html(sumAmount.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

            //get hidden value of totalAmount
            var totalAmount= (parseFloat(subTotal));
            $('.totalAmount').val(totalAmount.toFixed(2));


        })

        $(document).on('keyup change', '.discount', function () {
            var el = $(this).parent().parent().parent();
            var discount = $(this).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }
            var price = $(el.find('.price')).val();

            var quantity = $(el.find('.quantity')).val();
            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));


            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }


            var totalItemDiscountPrice = 0;
            var itemDiscountPriceInput = $('.discount');
            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                if (!isNaN(parseFloat(itemDiscountPriceInput[k].value))) {
                    totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                }
            }

            var sumAmount = totalItemPrice;

            $('.subTotal').html(sumAmount.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));

            //get hidden value of totalAmount
            var totalAmount= (parseFloat(subTotal));
            $('.totalAmount').val(totalAmount.toFixed(2));

        })

        $(document).on('change', '.item', function () {
            $('.item option').prop('hidden', false);
            $('.item :selected').each(function () {
                var id = $(this).val();
                if (id) {
                    $(".item option[value=" + id + "]").prop("hidden", true);
                }
            });
        });

        $(document).on('click', '[data-repeater-create]', function () {
            $('.item option').prop('hidden', false);
            $('.item :selected').each(function () {
                var id = $(this).val();
                if (id) {
                    $(".item option[value=" + id + "]").prop("hidden", true);
                }
            });
        })

    </script>



    {{--  start for user select--}}
    <script>



        $('input[name="type"]:radio').on('change', function (e) {
            var type = $(this).val();

            if (type == 'employee') {
                $('.employee').addClass('d-block');
                $('.employee').removeClass('d-none');
                $('.customer').addClass('d-none');
                $('.customer').removeClass('d-block');
                $('.vendor').addClass('d-none');
                $('.vendor').removeClass('d-block');
                $('#customer').removeAttr('required');
                $('#vender').removeAttr('required');
            }
            else if (type == 'customer') {

                $('.customer').addClass('d-block');
                $('.customer').removeClass('d-none');
                $('.employee').addClass('d-none');
                $('.employee').removeClass('d-block');
                $('.vendor').addClass('d-none');
                $('.vendor').removeClass('d-block');
                $('#employee').removeAttr('required');
                $('#vender').removeAttr('required');
            }
            else {
                $('.vendor').addClass('d-block');
                $('.vendor').removeClass('d-none');
                $('.employee').addClass('d-none');
                $('.employee').removeClass('d-block');
                $('.customer').addClass('d-none');
                $('.customer').removeClass('d-block');
                $('#employee').removeAttr('required');
                $('#customer').removeAttr('required');
            }





        });



        $(document).on('change', '#employee', function () {

            $('#employee_detail').removeClass('d-none');
            $('#employee_detail').addClass('d-block');
            $('#employee-box').removeClass('d-block');
            $('#employee-box').addClass('d-none');

            var cId = $(this).val();
            var url = $('#employee').data('url');

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': cId
                },
                cache: false,
                success: function (data) {

                    if (data != '') {
                        $('#employee_detail').html(data);

                    } else {
                        $('#employee-box').removeClass('d-none');
                        $('#employee-box').addClass('d-block');
                        $('#employee_detail').removeClass('d-block');
                        $('#employee_detail').addClass('d-none');
                    }

                },
            });
        });

        $(document).on('change', '#customer', function () {

            $('#customer_detail').removeClass('d-none');
            $('#customer_detail').addClass('d-block');
            $('#customer-box').removeClass('d-block');
            $('#customer-box').addClass('d-none');
            var id = $(this).val();
            var url = $('#customer').data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {

                    if (data != '') {
                        $('#customer_detail').html(data);
                    } else {
                        $('#customer-box').removeClass('d-none');
                        $('#customer-box').addClass('d-block');
                        $('#customer_detail').removeClass('d-block');
                        $('#customer_detail').addClass('d-none');
                    }
                },
            });
        });

        $(document).on('change', '#vender', function () {
            $('#vender_detail').removeClass('d-none');
            $('#vender_detail').addClass('d-block');
            $('#vender-box').removeClass('d-block');
            $('#vender-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data != '') {
                        $('#vender_detail').html(data);
                    } else {
                        $('#vender-box').removeClass('d-none');
                        $('#vender-box').addClass('d-block');
                        $('#vender_detail').removeClass('d-block');
                        $('#vender_detail').addClass('d-none');
                    }
                },
            });
        });

        $(document).on('click', '#remove', function () {
            $('#vender-box').removeClass('d-none');
            $('#vender-box').addClass('d-block');
            $('#vender_detail').removeClass('d-block');
            $('#vender_detail').addClass('d-none');

            $('#customer-box').removeClass('d-none');
            $('#customer-box').addClass('d-block');
            $('#customer_detail').removeClass('d-block');
            $('#customer_detail').addClass('d-none');

            $('#employee-box').removeClass('d-none');
            $('#employee-box').addClass('d-block');
            $('#employee_detail').removeClass('d-block');
            $('#employee_detail').addClass('d-none');

        })


    </script>

    {{--   end for user select--}}
@endpush
@section('content')
    <div class="row">

        {{ Form::model($expense, array('route' => array('expense.update', $expense->id), 'method' => 'PUT','class'=>'w-100'  , 'class'=>'needs-validation', 'novalidate')) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col">
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="employee_radio" value="employee" name="type" class="form-check-input" {{ $expense->user_type == 'employee' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="employee">{{__('Employee')}}</label>
                                </div>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="customer_radio" value="customer" name="type" class="form-check-input" {{ $expense->user_type == 'customer' ? 'checked' : '' }} >
                                    <label class="form-check-label" for="customer">{{__('Customer')}}</label>
                                </div>

                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="vendor_radio" value="vendor" name="type" class="form-check-input" {{ $expense->user_type == 'vendor' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vendor">{{__('Vendor')}}</label>
                                </div>
                            </div>


                            <div class="col employee">
                                <div class="form-group" id="employee-box">
                                    {{ Form::label('employee_id', __('Employee'),['class'=>'form-label']) }}<x-required></x-required>
                                    {{ Form::select('employee_id', $employees,$expense->vender_id, array('class' => 'form-control select','id'=>'employee','data-url'=>route('expense.employee') , 'required' => 'required')) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                                    </div>
                                </div>
                                <div id="employee_detail" class="d-none">
                                </div>
                            </div>
                            <div class="col customer d-none">
                                <div class="form-group" id="customer-box">
                                    {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}<x-required></x-required>
                                    {{ Form::select('customer_id', $customers,$expense->vender_id, array('class' => 'form-control select','id'=>'customer','data-url'=>route('expense.customer'), 'required' => 'required')) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create customer here.') }} <a href="{{ route('customer.index') }}"><b>{{ __('Create customer') }}</b></a>
                                    </div>
                                </div>
                                <div id="customer_detail" class="d-none">
                                </div>
                            </div>
                            <div class="col vendor d-none">
                                <div class="form-group" id="vender-box">
                                    {{ Form::label('vender_id', __('Vendor'),['class'=>'form-label']) }}<x-required></x-required>
                                    {{ Form::select('vender_id', $venders,$expense->vender_id, array('class' => 'form-control select','id'=>'vender','data-url'=>route('expense.vender') , 'required' => 'required')) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create vender here.') }} <a href="{{ route('vender.index') }}"><b>{{ __('Create vender') }}</b></a>
                                    </div>
                                </div>
                                <div id="vender_detail" class="d-none">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('bill_date', __('Payment Date'),['class'=>'form-label']) }}<x-required></x-required>
                                        {{Form::date('bill_date',null,array('class'=>'form-control','required'=>'required'))}}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
                                        {{ Form::select('category_id', $category,null, array('class' => 'form-control select' , 'required' => 'required')) }}
                                        <div class="text-xs mt-1">
                                            {{ __('Create category here.') }} <a href="{{ route('product-category.index') }}"><b>{{ __('Create category') }}</b></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}<x-required></x-required>
                                        <select class="form-control select" name="account_id" required>
                                            @foreach($bank_Account as $key => $account)
                                                <option value="{{$key}}" @if(isset($bankAccount) && $bankAccount->id == $key) selected @endif>{{__($account)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('bill_number', __('Expense Number'),['class'=>'form-label']) }}
                                        <input type="text" class="form-control" value="{{$expense_number}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12">
            <h5 class="d-inline-block mb-4">{{__('Product & Services')}}</h5>
            <div class="card repeater" data-value='{{ json_encode($items) }}'>
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal" data-target="#add-bank">
                                    <i class="ti ti-plus"></i> {{__('Add item')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0" data-repeater-list="items" id="sortable-table">
                            <thead>
                                <tr>
                                <th width="20%">{{__('Items')}}</th>
                                <th>{{__('Quantity')}}</th>
                                <th>{{__('Price')}} </th>
                                <th>{{__('Discount')}}</th>
                                <th>{{__('Tax')}} (%)</th>
                                <th class="text-end">{{__('Amount')}}
                                    <br><small class="text-danger font-bold">{{__('after tax & discount')}}</small>
                                </th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                    {{ Form::hidden('id',null, array('class' => 'form-control id')) }}
                                    {{ Form::hidden('account_id',null, array('class' => 'form-control account_id')) }}
                                    <td width="25%" class="form-group pt-0">
                                        {{ Form::select('items', $product_services,null, array('class' => 'form-control select item','data-url'=>route('expense.product') , 'required' => 'required')) }}
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form">
                                            {{ Form::number('quantity',null, array('class' => 'form-control quantity','placeholder'=>__('Qty') , 'required' => 'required')) }}
                                            <span class="unit input-group-text bg-transparent"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form">
                                            {{ Form::number('price',null, array('class' => 'form-control price','placeholder'=>__('Price') , 'required' => 'required')) }}
                                            <span class="input-group-text bg-transparent">{{\Auth::user()->currencySymbol()}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form">
                                            {{ Form::number('discount',null, array('class' => 'form-control discount','placeholder'=>__('Discount') , 'required' => 'required')) }}
                                            <span class="input-group-text bg-transparent">{{\Auth::user()->currencySymbol()}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="taxes"></div>
                                                {{ Form::hidden('tax','', array('class' => 'form-control tax')) }}
                                                {{ Form::hidden('itemTaxPrice','', array('class' => 'form-control itemTaxPrice')) }}
                                                {{ Form::hidden('itemTaxRate','', array('class' => 'form-control itemTaxRate')) }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-end amount">
                                        0.00
                                    </td>

                                    <td>
                                        @can('delete bill product')
                                        <div class="action-btn me-2">
                                            <a href="#" class="ti ti-trash text-white btn btn-sm repeater-action-btn bg-danger ms-2" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-repeater-delete></a>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="form-group">
                                            {{ Form::textarea('description', null, ['class'=>'form-control pro_description','rows'=>'1','placeholder'=>__('Description')]) }}
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{__('Sub Total')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                    <td class="text-end subTotal">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{__('Discount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                    <td class="text-end totalDiscount">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{__('Tax')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                    <td class="text-end totalTax">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="blue-text"><strong>{{__('Total Amount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>

                                    <td class="blue-text text-end totalAmount">0.00</td>
                                    {{ Form::hidden('totalAmount',null, array('class' => 'form-control totalAmount')) }}

                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("expense.index")}}';" class="btn btn-secondary me-2">
            <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection

