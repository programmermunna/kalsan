@extends('layouts.admin')
@section('page-title')
    {{__('Cargo Edit')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('purchase.index')}}">{{__('Cargo')}}</a></li>
    <li class="breadcrumb-item">{{__('Cargo Edit')}}</li>
@endsection
@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
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
                },
                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        var el = $(this);
                        var id = $(el.find('.id')).val();

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
                                url: '{{route('purchase.product.destroy')}}',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('#token').val()
                                },
                                data: {
                                    'id': id
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
                            });
                        }

                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var totalItemTaxPrice = 0;
                        var itemTaxPriceInput = $('.itemTaxPrice');
                        for (var j = 0; j < itemTaxPriceInput.length; j++) {
                            if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                            }
                        }

                        var totalItemPrice = 0;
                        var inputs_quantity = $(".quantity");
                        var priceInput = $('.price');
                        var otherInput = $('.other_frieght');
                        var agentInput = $('.agent_fee');
                        for (var j = 0; j < priceInput.length; j++) {
                            if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value)) + (parseFloat(otherInput[j].value) || 0) + (parseFloat(agentInput[j].value) || 0);
                            }
                        }

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
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
                        $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
                        $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
                    }
                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });

            // Load existing items for edit
            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                // Format the data for repeater
                var formattedValue = [];
                for (var i = 0; i < value.length; i++) {
                    formattedValue.push({
                        id: value[i].id || '',
                        item: value[i].product_id || '',
                        quantity: value[i].quantity || 0,
                        price: value[i].price || 0,
                        other_frieght: value[i].other_frieght || 0,
                        agent_fee: value[i].agent_fee || 0,
                        discount: value[i].discount || 0,
                        description: value[i].description || '',
                        tax: value[i].tax || ''
                    });
                }
                $repeater.setList(formattedValue);

                // Wait for repeater to render, then populate fields and trigger calculations
                setTimeout(function() {
                    $('tbody[data-repeater-item]').each(function(index) {
                        var rowWrapper = $(this);
                        var row = rowWrapper.find('tr').first();
                        var itemData = formattedValue[index] || {};

                        if (row.length && itemData) {
                            row.find('.id').val(itemData.id || '');
                            row.find('.quantity').val(itemData.quantity || 0);
                            row.find('.price').val(itemData.price || 0);
                            row.find('.other_frieght').val(itemData.other_frieght || 0);
                            row.find('.agent_fee').val(itemData.agent_fee || 0);
                            row.find('.discount').val(itemData.discount || 0);
                            rowWrapper.find('.pro_description').val(itemData.description || '');

                            // Calculate amount for this item immediately
                            var quantity = parseFloat(itemData.quantity) || 0;
                            var price = parseFloat(itemData.price) || 0;
                            var other_frieght = parseFloat(itemData.other_frieght) || 0;
                            var agent_fee = parseFloat(itemData.agent_fee) || 0;
                            var discount = parseFloat(itemData.discount) || 0;
                            var amount = (quantity * price) + other_frieght + agent_fee - discount;
                            row.find('.amount').html(amount.toFixed(2));

                            // Set initial tax values to 0 if not set
                            if (!row.find('.itemTaxPrice').val()) {
                                row.find('.itemTaxPrice').val('0.00');
                            }
                            if (!row.find('.itemTaxRate').val()) {
                                row.find('.itemTaxRate').val('0.00');
                            }

                            // Trigger change on item to load product details (taxes, unit, etc.)
                            if (itemData.item) {
                                row.find('.item').val(itemData.item || '');
                                setTimeout(function() {
                                    row.find('.item').trigger('change');
                                }, 100 * (index + 1));
                            }
                        }
                    });

                    // Immediate calculation of totals after setting initial values
                    setTimeout(function() {
                        var totalItemPrice = 0;
                        var inputs_quantity = $(".quantity");
                        var priceInput = $('.price');
                        var otherInput = $('.other_frieght');
                        var agentInput = $('.agent_fee');
                        for (var j = 0; j < priceInput.length; j++) {
                            var qty = parseFloat(inputs_quantity[j].value);
                            var prc = parseFloat(priceInput[j].value);
                            var other = parseFloat(otherInput[j].value);
                            var agent = parseFloat(agentInput[j].value);
                            if (!isNaN(qty) && !isNaN(prc)) {
                                totalItemPrice += (prc * qty)+ (other || 0) + (agent || 0);
                            }
                        }

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            var amtText = $(inputs[i]).html();
                            var amt = parseFloat(amtText) || 0;
                            if (!isNaN(amt)) {
                                subTotal += amt;
                            }
                        }

                        var totalItemDiscountPrice = 0;
                        var itemDiscountPriceInput = $('.discount');
                        for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                            var discVal = parseFloat(itemDiscountPriceInput[k].value);
                            if (!isNaN(discVal)) {
                                totalItemDiscountPrice += discVal;
                            }
                        }

                        $('.subTotal').html(totalItemPrice.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                        $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
                    }, 300);

                    // Recalculate totals after all items are loaded and product details are fetched
                    setTimeout(function() {
                        // First, recalculate amounts for all items in case taxes were updated
                        $('tbody[data-repeater-item]').each(function() {
                            var row = $(this).find('tr').first();
                            var quantity = parseFloat(row.find('.quantity').val()) || 0;
                            var price = parseFloat(row.find('.price').val()) || 0;
                            var other_frieght = parseFloat(row.find('.other_frieght').val()) || 0;
                            var agent_fee = parseFloat(row.find('.agent_fee').val()) || 0;
                            var discount = parseFloat(row.find('.discount').val()) || 0;
                            var amount = (quantity * price) + other_frieght + agent_fee - discount;
                            row.find('.amount').html(amount.toFixed(2));
                        });

                        // Now calculate totals
                        var totalItemTaxPrice = 0;
                        var itemTaxPriceInput = $('.itemTaxPrice');
                        for (var j = 0; j < itemTaxPriceInput.length; j++) {
                            if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                            }
                        }

                        var totalItemPrice = 0;
                        var inputs_quantity = $(".quantity");
                        var priceInput = $('.price');
                        for (var j = 0; j < priceInput.length; j++) {
                            if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                            }
                        }

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
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
                        $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
                        $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
                    }, 1000);

                    // Final recalculation after all async operations complete - ensure amounts are set and totals are displayed
                    setTimeout(function() {
                        // Recalculate all item amounts first
                        $('tbody[data-repeater-item]').each(function() {
                            var row = $(this).find('tr').first();
                            var quantity = parseFloat(row.find('.quantity').val()) || 0;
                            var price = parseFloat(row.find('.price').val()) || 0;
                            var other_frieght = parseFloat(row.find('.other_frieght').val()) || 0;
                            var agent_fee = parseFloat(row.find('.agent_fee').val()) || 0;
                            var discount = parseFloat(row.find('.discount').val()) || 0;
                            var amount = (quantity * price) + other_frieght + agent_fee - discount;
                            row.find('.amount').html(amount.toFixed(2));
                        });

                        // Now calculate and display totals
                        var totalItemTaxPrice = 0;
                        var itemTaxPriceInput = $('.itemTaxPrice');
                        for (var j = 0; j < itemTaxPriceInput.length; j++) {
                            var taxVal = parseFloat(itemTaxPriceInput[j].value);
                            if (!isNaN(taxVal)) {
                                totalItemTaxPrice += taxVal;
                            }
                        }

                        var totalItemPrice = 0;
                        var inputs_quantity = $(".quantity");
                        var priceInput = $('.price');
                        for (var j = 0; j < priceInput.length; j++) {
                            var qty = parseFloat(inputs_quantity[j].value);
                            var prc = parseFloat(priceInput[j].value);
                            if (!isNaN(qty) && !isNaN(prc) && qty > 0 && prc > 0) {
                                totalItemPrice += (prc * qty);
                            }
                        }

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            var amtText = $(inputs[i]).html();
                            var amt = parseFloat(amtText) || 0;
                            if (!isNaN(amt)) {
                                subTotal += amt;
                            }
                        }

                        var totalItemDiscountPrice = 0;
                        var itemDiscountPriceInput = $('.discount');
                        for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                            var discVal = parseFloat(itemDiscountPriceInput[k].value);
                            if (!isNaN(discVal)) {
                                totalItemDiscountPrice += discVal;
                            }
                        }

                        // Update display elements
                        $('.subTotal').html(totalItemPrice.toFixed(2));
                        $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                        $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
                    }, 2000);
                }, 200);
            }
        }

        $(document).on('change', '#vender_id', function () {
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

        var purchase_id = '{{$purchase->id}}';

        // Function to recalculate all totals
        function recalculateTotals() {
            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value) || 0;
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
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
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        }

        $(document).on('change', '.item', function () {
            var iteams_id = $(this).val();
            var url = $(this).data('url');
            var el = $(this);

            if (!iteams_id) {
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function (data) {
                    var item = JSON.parse(data);

                    // Check if this is an existing item in the purchase
                    $.ajax({
                        url: '{{route('purchase.items')}}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'purchase_id': purchase_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function (purchaseData) {
                            var purchaseItems = JSON.parse(purchaseData);

                            // Get current form values
                            var currentQty = parseFloat($(el.parent().parent().find('.quantity')).val()) || 0;
                            var currentPrice = parseFloat($(el.parent().parent().find('.price')).val()) || 0;

                            // If purchaseItems exists and we have current values, use them; otherwise use defaults
                            if (purchaseItems != null && purchaseItems.id && currentQty > 0) {
                                // Existing item with values already set - don't override
                                var quantity = currentQty;
                                var price = currentPrice > 0 ? currentPrice : (purchaseItems.price || item.product.purchase_price || 0);
                                var other_frieght = parseFloat($(el.parent().parent().find('.other_frieght')).val()) || (purchaseItems.other_frieght || item.product.other_frieght || 0);
                                var agent_fee = parseFloat($(el.parent().parent().find('.agent_fee')).val()) || (purchaseItems.agent_fee || item.product.agent_fee || 0);
                                var discount = parseFloat($(el.parent().parent().find('.discount')).val()) || (purchaseItems.discount || 0);

                                // Only set if not already set
                                if (currentPrice == 0) {
                                    $(el.parent().parent().find('.price')).val(price);
                                }
                                if (parseFloat($(el.parent().parent().find('.other_frieght')).val()) == 0) {
                                    $(el.parent().parent().find('.other_frieght')).val(other_frieght);
                                }
                                if (parseFloat($(el.parent().parent().find('.agent_fee')).val()) == 0) {
                                    $(el.parent().parent().find('.agent_fee')).val(agent_fee);
                                }
                                $(el.parent().parent().parent().find('.pro_description')).val(purchaseItems.description || item.product.description || '');
                            } else {
                                // New item or no existing data - use product defaults
                                $(el.parent().parent().find('.quantity')).val(1);
                                $(el.parent().parent().find('.price')).val(item.product.purchase_price || 0);
                                $(el.parent().parent().find('.other_frieght')).val(item.product.other_frieght || 0);
                                $(el.parent().parent().find('.agent_fee')).val(item.product.agent_fee || 0);
                                $(el.parent().parent().parent().find('.pro_description')).val(item.product.description || '');
                            }

                            var taxes = '';
                            var tax = [];

                            var totalItemTaxRate = 0;
                            if (item.taxes == 0 || !item.taxes || item.taxes.length == 0) {
                                taxes += '-';
                            } else {
                                for (var i = 0; i < item.taxes.length; i++) {
                                    taxes += '<span class="badge bg-primary mt-1 mr-2">' + item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' + '</span>';
                                    tax.push(item.taxes[i].id);
                                    totalItemTaxRate += parseFloat(item.taxes[i].rate);
                                }
                            }

                            // Get final values from form
                            var quantity = parseFloat($(el.parent().parent().find('.quantity')).val()) || 0;
                            var price = parseFloat($(el.parent().parent().find('.price')).val()) || 0;
                            var other_frieght = parseFloat($(el.parent().parent().find('.other_frieght')).val()) || 0;
                            var agent_fee = parseFloat($(el.parent().parent().find('.agent_fee')).val()) || 0;
                            var discount = parseFloat($(el.parent().parent().find('.discount')).val()) || 0;

                            // Amount = (quantity * price) + other_frieght + agent_fee - discount
                            var amount = (quantity * price) + other_frieght + agent_fee - discount;

                            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));

                            $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                            $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                            $(el.parent().parent().find('.taxes')).html(taxes);
                            $(el.parent().parent().find('.tax')).val(tax);
                            $(el.parent().parent().find('.unit')).html(item.unit);
                            $(el.parent().parent().find('.discount')).val(discount);
                            $(el.parent().parent().find('.amount')).html(amount.toFixed(2));

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
                            }

                            var totalItemPrice = 0;
                            var inputs_quantity = $(".quantity");
                            var priceInput = $('.price');
                            var otherInput = $('.other_frieght');
                            var agentInput = $('.agent_fee');
                            for (var j = 0; j < priceInput.length; j++) {
                                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value)) + (parseFloat(otherInput[j].value) || 0) + (parseFloat(agentInput[j].value) || 0);
                                }
                            }

                            var totalItemTaxPrice = 0;
                            var itemTaxPriceInput = $('.itemTaxPrice');
                            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                                }
                            }

                            $('.subTotal').html(totalItemPrice.toFixed(2));
                            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
                        }
                    });
                },
            });
        });
        $(document).on('change', '.item', function () {
            $('.item option').prop('hidden', false);
            $('.item :selected').each(function () {
                var id = $(this).val();
                if (id) {
                    $('.item').not(this).find("option[value=" + id + "]").prop('hidden', true);
                }
            });
        });

        $(document).on('keyup', '.quantity', function () {
            var el = $(this).parent().parent().parent().parent();
            var quantity = parseFloat($(this).val()) || 0;
            var price = parseFloat($(el.find('.price')).val()) || 0;
            var other_frieght = parseFloat($(el.find('.other_frieght')).val()) || 0;
            var agent_fee = parseFloat($(el.find('.agent_fee')).val()) || 0;
            var discount = parseFloat($(el.find('.discount')).val()) || 0;

            // Amount = (quantity * price) + other_frieght + agent_fee - discount
            var amount = (quantity * price) + other_frieght + agent_fee - discount;

            var totalItemTaxRate = parseFloat($(el.find('.itemTaxRate')).val()) || 0;
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(amount.toFixed(2));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
        })

        $(document).on('keyup change', '.price', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = parseFloat($(this).val()) || 0;
            var quantity = parseFloat($(el.find('.quantity')).val()) || 0;
            var other_frieght = parseFloat($(el.find('.other_frieght')).val()) || 0;
            var agent_fee = parseFloat($(el.find('.agent_fee')).val()) || 0;
            var discount = parseFloat($(el.find('.discount')).val()) || 0;

            // Amount = (quantity * price) + other_frieght + agent_fee - discount
            var amount = (quantity * price) + other_frieght + agent_fee - discount;

            var totalItemTaxRate = parseFloat($(el.find('.itemTaxRate')).val()) || 0;
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(amount.toFixed(2));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
        })

        $(document).on('keyup change', '.other_frieght', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = parseFloat($(el.find('.price')).val()) || 0;
            var quantity = parseFloat($(el.find('.quantity')).val()) || 0;
            var other_frieght = parseFloat($(this).val()) || 0;
            var agent_fee = parseFloat($(el.find('.agent_fee')).val()) || 0;
            var discount = parseFloat($(el.find('.discount')).val()) || 0;

            // Amount = (quantity * price) + other_frieght + agent_fee - discount
            var amount = (quantity * price) + other_frieght + agent_fee - discount;

            var totalItemTaxRate = parseFloat($(el.find('.itemTaxRate')).val()) || 0;
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(amount.toFixed(2));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
        })

        $(document).on('keyup change', '.agent_fee', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = parseFloat($(el.find('.price')).val()) || 0;
            var quantity = parseFloat($(el.find('.quantity')).val()) || 0;
            var other_frieght = parseFloat($(el.find('.other_frieght')).val()) || 0;
            var agent_fee = parseFloat($(this).val()) || 0;
            var discount = parseFloat($(el.find('.discount')).val()) || 0;

            // Amount = (quantity * price) + other_frieght + agent_fee - discount
            var amount = (quantity * price) + other_frieght + agent_fee - discount;

            var totalItemTaxRate = parseFloat($(el.find('.itemTaxRate')).val()) || 0;
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(amount.toFixed(2));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
        })

        $(document).on('keyup change', '.discount', function () {
            var el = $(this).parent().parent().parent();
            var discount = parseFloat($(this).val()) || 0;
            var price = parseFloat($(el.find('.price')).val()) || 0;
            var quantity = parseFloat($(el.find('.quantity')).val()) || 0;
            var other_frieght = parseFloat($(el.find('.other_frieght')).val()) || 0;
            var agent_fee = parseFloat($(el.find('.agent_fee')).val()) || 0;

            // Amount = (quantity * price) + other_frieght + agent_fee - discount
            var amount = (quantity * price) + other_frieght + agent_fee - discount;

            var totalItemTaxRate = parseFloat($(el.find('.itemTaxRate')).val()) || 0;
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (quantity * price));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(amount.toFixed(2));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                if (!isNaN(parseFloat(itemTaxPriceInput[j].value))) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (!isNaN(parseFloat(priceInput[j].value)) && !isNaN(parseFloat(inputs_quantity[j].value))) {
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + (parseFloat($(inputs[i]).html()) || 0);
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
            $('.totalAmount').html(parseFloat(subTotal).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        })

        $(document).on('click', '[data-repeater-create]', function () {
            $('.item option').prop('hidden', false);
            $('.item :selected').each(function () {
                var id = $(this).val();
                if (id) {
                    $('.item').not(this).find("option[value=" + id + "]").prop('hidden', true);
                }
            });
        })

        // Form validation
        $('form').on('submit', function(e) {
            // Remove any hidden items before submission
            $('#sortable-table tbody[data-repeater-item]:hidden').remove();

            // Count visible items after cleanup
            var visibleItems = $('#sortable-table tbody[data-repeater-item]:visible').length;

            if(visibleItems === 0) {
                e.preventDefault();
                e.stopPropagation();
                show_toastr('error', 'Please add at least one item.', 'error');
                return false;
            }

            // Ensure all visible items have required fields
            var hasValidItem = false;
            $('#sortable-table tbody[data-repeater-item]:visible').each(function() {
                var $tbody = $(this);
                var $row = $tbody.find('tr').first();
                var item = $row.find('.item').val();
                var quantity = $row.find('.quantity').val();
                var price = $row.find('.price').val();

                if(item && item !== '' && quantity && quantity !== '' && price && price !== '') {
                    hasValidItem = true;
                    return false; // break
                }
            });

            if(!hasValidItem) {
                e.preventDefault();
                e.stopPropagation();
                show_toastr('error', 'Please add at least one item with product, quantity, and price.', 'error');
                return false;
            }

            return true;
        });

    </script>

@endpush

@section('content')
    <div class="row">
        {{ Form::model($purchase, array('route' => array('purchase.update', $purchase->id), 'method' => 'PUT', 'class' => 'w-100', 'class' => 'needs-validation', 'novalidate')) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('customer_id', $customers, old('customer_id', $purchase->customer_id), array('class' => 'form-control select', 'id' => 'customer_id', 'data-url' => route('invoice.customer'), 'required' => 'required')) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('origin', __('Origin'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('origin', $origin, old('origin', $purchase->origin), array('class' => 'form-control', 'id' => 'origin', 'required' => 'required')) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('destination', __('Destination'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('destination', $destination, old('destination', $purchase->destination), array('class' => 'form-control', 'id' => 'destination', 'required' => 'required')) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('purchase_date', __('Issue Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{Form::date('purchase_date', old('purchase_date', $purchase->purchase_date), array('class' => 'form-control', 'required' => 'required'))}}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('purchase_number', __('Cargo Ref Number'), ['class' => 'form-label']) }}
                                            <input type="text" class="form-control" value="{{$purchase_number}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('vender_id', __('Company'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('vender_id', $topup, old('vender_id', $purchase->vender_id), array('class' => 'form-control select', 'id' => 'vender_id', 'data-url' => route('invoice.vender'), 'required' => 'required')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h5 class="d-inline-block mb-4">{{__('List of Cargo')}}</h5>
            <div class="card repeater" data-value='{{ json_encode($purchase->items) }}'>
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal" data-target="#add-bank">
                                    <i class="ti ti-plus"></i> {{__('Add Cargo')}}
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
                                <th>{{__('Cargo Type')}}</th>
                                <th>{{__('Cargo Detail')}}</th>
                                <th>{{__('Gross Weight')}} </th>
                                <th>{{__('Rate')}}</th>
                                <th>{{__('Other Frieght')}} </th>
                                <th>{{__('Agent Fee')}} </th>
                                <th>{{__('Discount')}}</th>
                                <th class="text-end">{{__('Amount')}} <br><small class="text-danger font-weight-bold">{{__('after tax & discount')}}</small></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody class="ui-sortable" data-repeater-item>
                            <tr>
                                <td width="15%" class="form-group pt-1">
                                    {{ Form::select('item', $product_services, '', array('class' => 'form-control select2 item', 'data-url' => route('purchase.product'), 'required' => 'required')) }}
                                </td>
                                <td colspan="0">
                                    <div class="form-group">
                                        {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Description')]) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('quantity', '', array('class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Weight'), 'step' => 'any')) }}
                                        <span class="unit input-group-text bg-transparent">KG</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('price', '', array('class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Rate'), 'step' => 'any')) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('other_frieght', '', array('class' => 'form-control other_frieght', 'required' => 'required', 'placeholder' => __('Other Frieght'), 'step' => 'any')) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('agent_fee', '', array('class' => 'form-control agent_fee', 'required' => 'required', 'placeholder' => __('Agent Fee'), 'step' => 'any')) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::number('discount', '', array('class' => 'form-control discount', 'placeholder' => __('Discount'), 'step' => 'any')) }}
                                    </div>
                                </td>
                                {{ Form::hidden('id', '', array('class' => 'form-control id')) }}
                                {{ Form::hidden('tax', '', array('class' => 'form-control tax')) }}
                                {{ Form::hidden('itemTaxPrice', '', array('class' => 'form-control itemTaxPrice')) }}
                                {{ Form::hidden('itemTaxRate', '', array('class' => 'form-control itemTaxRate')) }}
                                <td class="text-end amount">
                                    0.00
                                </td>
                                <td>
                                    <div class="action-btn me-2">
                                        <a href="#" class="ti ti-trash text-white btn btn-sm repeater-action-btn bg-danger ms-2" data-repeater-delete data-bs-toggle="tooltip" title="{{__('Delete')}}"></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="blue-text"><strong>{{__('Total Amount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                <td class="blue-text text-end totalAmount">0.00</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("purchase.index")}}';" class="btn btn-secondary me-2">
            <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection
