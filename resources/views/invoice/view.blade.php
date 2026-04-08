@extends('layouts.admin')
@section('page-title')
    {{ __('License Detail') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('License') }}</a></li>
    <li class="breadcrumb-item">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</li>
@endsection
@php
    $settings = Utility::settings();

    // Resolve human-readable Origin / Destination for display.
    $originName = null;
    if (!empty($invoice->origin)) {
        if (is_numeric($invoice->origin)) {
            $originName = optional(\App\Models\ProductService::find($invoice->origin))->name ?? $invoice->origin;
        } else {
            $originName = $invoice->origin;
        }
    }

    $destinationName = null;
    if (!empty($invoice->destination)) {
        if (is_numeric($invoice->destination)) {
            $destinationName = optional(\App\Models\ProductService::find($invoice->destination))->name ?? $invoice->destination;
        } else {
            $destinationName = $invoice->destination;
        }
    }
@endphp
@push('css-page')
    <style>
        #card-element {
            border: 1px solid #a3afbb !important;
            border-radius: 10px !important;
            padding: 10px !important;
        }
    </style>
@endpush
@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script type="text/javascript">
        @if (
            $invoice->getDue() > 0 &&
                !empty($company_payment_setting) &&
                $company_payment_setting['is_stripe_enabled'] == 'on' &&
                !empty($company_payment_setting['stripe_key']) &&
                !empty($company_payment_setting['stripe_secret']))

            var stripe = Stripe('{{ $company_payment_setting['stripe_key'] }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {
                style: style
            });

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        show_toastr('error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        @endif

        @if (isset($company_payment_setting['paystack_public_key']))
            $(document).on("click", "#pay_with_paystack", function() {

                $('#paystack-payment-form').ajaxForm(function(res) {
                    var amount = res.total_price;
                    if (res.flag == 1) {
                        var paystack_callback = "{{ url('/invoice/paystack') }}";

                        var handler = PaystackPop.setup({
                            key: '{{ $company_payment_setting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {

                                window.location.href = paystack_callback + '/' + response
                                    .reference + '/' + '{{ encrypt($invoice->id) }}' +
                                    '?amount=' + amount;
                            },
                            onClose: function() {
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', res.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (isset($company_payment_setting['flutterwave_public_key']))
            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function() {
                $('#flaterwave-payment-form').ajaxForm(function(res) {

                    if (res.flag == 1) {
                        var amount = res.total_price;
                        var API_publicKey = '{{ $company_payment_setting['flutterwave_public_key'] }}';
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/invoice/flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{ Auth::user()->email }}',
                            amount: res.total_price,
                            currency: '{{ App\Models\Utility::getValByName('site_currency') }}',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' + '{{ date('Y-m-d') }}' + '?amount=' + amount,
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {
                                    window.location.href = flutter_callback + '/' + txref +
                                        '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}';
                                } else {
                                    // redirect to a failure page.
                                }
                                x
                            .close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (isset($company_payment_setting['razorpay_public_key']))
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var amount = res.total_price;
                        var razorPay_callback = '{{ url('/invoice/razorpay') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "{{ $company_payment_setting['razorpay_public_key'] }}", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": '{{ App\Models\Utility::getValByName('site_currency') }}',
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + response
                                    .razorpay_payment_id + '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' +
                                    '?amount=' + amount;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif


        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
@endpush


@section('content')

    @can('send invoice')
        @if ($invoice->status != 4)
            <div class="row">
                <div class="col-12">
                    <div class="bill-timeline-card mb-4">
                        <div class="row timeline-wrapper">
                            <div class="col-xl-4 col-md-5 col-sm-7 create-invoice invoice">
                                <div class="progress mb-3">
                                    <div class="progress-value"></div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_69_767)">
                                                <path
                                                    d="M9.15642 18C8.92315 18 8.69942 17.9074 8.53447 17.7426C8.36952 17.5777 8.27686 17.3542 8.27686 17.1211V0.878906C8.27686 0.645806 8.36952 0.422253 8.53447 0.257426C8.69942 0.0925988 8.92315 0 9.15642 0C9.3897 0 9.61342 0.0925988 9.77837 0.257426C9.94332 0.422253 10.036 0.645806 10.036 0.878906V17.1211C10.036 17.3542 9.94332 17.5777 9.77837 17.7426C9.61342 17.9074 9.3897 18 9.15642 18Z"
                                                    fill="white" />
                                                <path
                                                    d="M17.2838 9.87891H1.02947C0.796193 9.87891 0.572472 9.78631 0.407521 9.62148C0.242571 9.45665 0.149902 9.2331 0.149902 9C0.149902 8.7669 0.242571 8.54335 0.407521 8.37852C0.572472 8.21369 0.796193 8.12109 1.02947 8.12109H17.2838C17.5171 8.12109 17.7408 8.21369 17.9058 8.37852C18.0707 8.54335 18.1634 8.7669 18.1634 9C18.1634 9.2331 18.0707 9.45665 17.9058 9.62148C17.7408 9.78631 17.5171 9.87891 17.2838 9.87891Z"
                                                    fill="white" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_69_767">
                                                    <rect width="18.0135" height="18" fill="white"
                                                        transform="translate(0.149902)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{ __('Create License') }}</h5>
                                        <p class="text-muted mb-2">
                                            {{ __('Created on ') }}{{ \Auth::user()->dateFormat($invoice->issue_date) }}</p>
                                            @can('edit invoice')
                                                @if ($invoice->status != 3 && $invoice->status != 4)
                                                    <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                        class="btn btn-sm d-inline-flex align-items-center gap-2" data-bs-toggle="tooltip"
                                                        data-original-title="{{ __('Edit') }}"><i
                                                            class="ti ti-pencil"></i>{{ __('Edit') }}</a>
                                                @endif
                                            @endcan
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-5 col-sm-7 send-invoice invoice">
                                <div class="progress mb-3">
                                    <div class="progress mb-3">
                                        <div class="{{ $invoice->status !== 0 ? 'progress-value' : '' }}"></div>
                                    </div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.3797 5.12359L7.43524 9.24773L1.21757 7.17645C0.783563 7.03159 0.491171 6.62471 0.493668 6.16758C0.496198 5.71046 0.791942 5.30607 1.22762 5.16627L17.1241 0.0508648C17.502 -0.0705155 17.9167 0.0290971 18.1974 0.309582C18.4781 0.590066 18.5778 1.00447 18.4563 1.38208L13.337 17.2666C13.1971 17.702 12.7924 17.9975 12.335 18C11.8775 18.0025 11.4703 17.7103 11.3253 17.2767L9.24246 11.0335L13.3797 5.12359Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{ __('Approve License') }}</h5>
                                        <p class="text-muted mb-2">
                                            @if ($invoice->status != 0)
                                                {{ __('Sent on') }} {{ \Auth::user()->dateFormat($invoice->send_date) }}
                                            @else
                                                @can('send purchase')
                                                    <small>{{ __('Status') }} : {{ __('Not Sent') }}</small>
                                                @endcan
                                            @endif
                                        </p>
                                        @if ($invoice->status == 0)
                                        @can('send bill')
                                            <a href="{{ route('invoice.sent', $invoice->id) }}" class="btn btn-sm d-inline-flex align-items-center gap-2 btn-warning border-0"
                                                data-bs-toggle="tooltip" data-original-title="{{ __('Mark Sent') }}"><i
                                                    class="ti ti-send"></i>{{ __('Approve') }}</a>
                                        @endcan
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-5 col-sm-7 get-paid invoice">
                                <div class="progress mb-3">
                                    <div class="{{ $invoice->status == 5 ? 'progress-value' : '' }}"></div>
                                </div>
                                <div class="bill-timeline-inner d-flex gap-3">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.386719 5.30859H8.47266V6.36328H0.386719V5.30859Z" fill="white" />
                                            <path
                                                d="M8.47266 7.06641H0.386719V9.52734H8.47266V7.06641ZM3.72656 8.47266H1.79297C1.5988 8.47266 1.44141 8.31526 1.44141 8.12109C1.44141 7.92693 1.5988 7.76953 1.79297 7.76953H3.72656C3.92073 7.76953 4.07812 7.92693 4.07812 8.12109C4.07812 8.31526 3.92073 8.47266 3.72656 8.47266ZM7.06641 8.47266H5.13281C4.93864 8.47266 4.78125 8.31526 4.78125 8.12109C4.78125 7.92693 4.93864 7.76953 5.13281 7.76953H7.06641C7.26057 7.76953 7.41797 7.92693 7.41797 8.12109C7.41797 8.31526 7.26057 8.47266 7.06641 8.47266Z"
                                                fill="white" />
                                            <path
                                                d="M10.5981 3.55005C10.5708 3.55131 4.92694 3.55075 3.55078 3.55086V4.60555H9.17578V10.2305H3.55078V14.4493H14.4492V7.06648C12.4352 7.06648 10.776 5.51852 10.5981 3.55005Z"
                                                fill="white" />
                                            <path
                                                d="M10.598 2.84846C10.7029 1.68683 11.3235 0.671766 12.2291 0.0351562H4.95703C4.18148 0.0351562 3.55078 0.665859 3.55078 1.44141V2.84766C4.94831 2.84776 10.5702 2.8472 10.598 2.84846ZM9.52734 1.79297H7.76953C7.57536 1.79297 7.41797 1.63557 7.41797 1.44141C7.41797 1.24724 7.57536 1.08984 7.76953 1.08984H9.52734C9.72148 1.08984 9.87891 1.24724 9.87891 1.44141C9.87891 1.63557 9.72148 1.79297 9.52734 1.79297Z"
                                                fill="white" />
                                            <path
                                                d="M3.55078 16.5586C3.55078 17.3341 4.18148 17.9648 4.95703 17.9648H13.043C13.8185 17.9648 14.4492 17.3341 14.4492 16.5586V15.1523H3.55078V16.5586ZM8.12109 16.207H9.87891C10.073 16.207 10.2305 16.3645 10.2305 16.5586C10.2305 16.7527 10.073 16.9102 9.87891 16.9102H8.12109C7.92703 16.9102 7.76953 16.7527 7.76953 16.5586C7.76953 16.3645 7.92703 16.207 8.12109 16.207Z"
                                                fill="white" />
                                            <path
                                                d="M14.4492 0.0351562C12.7017 0.0351562 11.2852 1.45174 11.2852 3.19922C11.2852 4.9467 12.7017 6.36328 14.4492 6.36328C16.1967 6.36328 17.6133 4.9467 17.6133 3.19922C17.6133 1.45174 16.1967 0.0351562 14.4492 0.0351562ZM16.0162 2.52496L14.1705 4.37066C14.0331 4.50795 13.8106 4.50795 13.6733 4.37066L12.8822 3.57964C12.7449 3.44236 12.7449 3.21975 12.8822 3.08243C13.0196 2.94514 13.2421 2.94514 13.3794 3.08243L13.9219 3.62489L15.519 2.02778C15.6563 1.89049 15.8788 1.89049 16.0162 2.02778C16.1535 2.16506 16.1535 2.38767 16.0162 2.52496Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <div class="timeline-content text-start">
                                        <h5 class="mb-2">{{ __('Get Paid') }}</h5>
                                        <p class="text-muted mb-2">
                                            {{ __('Status') }} : {{ __('Awaiting payment') }}
                                        </p>
                                        @if ($invoice->status != 0)
                                        @can('create payment invoice')
                                            <a href="#" data-url="{{ route('invoice.payment', $invoice->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Add Payment') }}"
                                                class="btn btn-sm d-inline-flex align-items-center gap-2" data-original-title="{{ __('Add Payment') }}"><i
                                                    class="ti ti-report-money"></i>{{ __('Add Payment') }}</a> <br>
                                        @endcan
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @if (\Auth::user()->type == 'company')
        @if ($invoice->status != 0)
            <div class="row justify-content-between align-items-center mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                    @if ($invoice->status != 4)
                        <div class="all-button-box">
                            <a href="#" class="btn btn-sm btn-primary"
                                data-url="{{ route('invoice.credit.note', $invoice->id) }}" data-ajax-popup="true"
                                data-title="{{ __('Apply Credit Note') }}">
                                {{ __('Apply Credit Note') }}
                            </a>
                        </div>
                    @endif
                    @if ($invoice->status != 4)
                        <div class="all-button-box">
                            <a href="{{ route('invoice.payment.reminder', $invoice->id) }}"
                                class="btn btn-sm btn-primary">{{ __('Receipt Reminder') }}</a>
                        </div>
                    @endif
                    <div class="all-button-box">
                        <a href="{{ route('invoice.resent', $invoice->id) }}"
                            class="btn btn-sm btn-primary">{{ __('Reapprove Invoice') }}</a>
                    </div>
                    <div class="all-button-box">
                        <a href="{{ route('invoice.pdf', Crypt::encrypt($invoice->id)) }}" target="_blank"
                            class="btn btn-sm btn-primary">{{ __('Print') }}</a>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h4>{{ __('License') }}</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                    <h4 class="invoice-number">
                                        {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="me-4">
                                            <small>
                                                <strong>{{ __('Issue Date') }} :</strong><br>
                                                {{ \Auth::user()->dateFormat($invoice->issue_date) }}<br><br>
                                            </small>
                                        </div>
                                        <div>
                                            <small>
                                                <strong>{{ __('Due Date') }} :</strong><br>
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}<br><br>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if (!empty($customer->name))
                                    <div class="col">
                                        <small class="font-style">
                                            <strong>{{ __('Customer ') }} :</strong><br>
                                            @if (!empty($customer->name))
                                                Full Name : {{ !empty($customer->name) ? $customer->name : '' }}<br>
                                                Mother's Name : {{ !empty($customer->mother_name) ? $customer->mother_name : '' }}<br>
                                                Gender : {{ !empty($customer->gender) ? $customer->gender : '' }}<br>
                                                Date of Birth : {{ !empty($customer->dob) ? \Auth::user()->dateFormat($customer->dob) : '' }}<br>
                                                place of Birth : {{ !empty($customer->pob) ? $customer->pob : '' }}<br>
                                                Mobile : {{ !empty($customer->contact) ? $customer->contact : '' . ', ' }}<br>
                                                Email : {{ !empty($customer->email) ? $customer->email : '', ', ' }},<br>
                                                Address : {{ !empty($customer->billing_address) ? $customer->billing_address : '', ', ' }},<br>
                                                City : {{ !empty($customer->billing_city) ? $customer->billing_city : '' }}<br>
                                                State : {{ !empty($customer->billing_state) ? $customer->billing_state : '' }}<br>
                                                country : {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}<br>
                                                @if ($settings['vat_gst_number_switch'] == 'on')
                                                    @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                                        {{ $settings['tax_type'] . ' ' . __('Number') }} :
                                                        {{ $settings['vat_number'] }} <br>
                                                    @endif

                                                    <strong>{{ __('Tax Number ') }} :
                                                    </strong>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}
                                                @endif
                                            @else
                                                -
                                            @endif

                                        </small>
                                    </div>
                                @endif
                                @if (App\Models\Utility::getValByName('shipping_display') == 'on')
                                    <div class="col ">
                                        <small>
                                            <strong>{{ __('Shipped To') }} :</strong><br>
                                            @if (!empty($customer->shipping_name))
                                                {{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}<br>
                                                {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}<br>
                                                {{ !empty($customer->shipping_city) ? $customer->shipping_city : '' . ', ' }}<br>
                                                {{ !empty($customer->shipping_state) ? $customer->shipping_state : '' . ', ' }},
                                                {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}<br>
                                                {{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}<br>
                                                {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}<br>
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </div>
                                @endif
                                <div class="col">
                                    <div class="float-end mt-3">
                                        @if ($settings['invoice_qr_display'] == 'on')
                                            {!! DNS2D::getBarcodeHTML(
                                                route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <small>
                                        <strong>{{ __('Status') }} :</strong><br>
                                        @if ($invoice->status == 0)
                                            <span
                                                class="badge p-2 px-3 rounded bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span
                                                class="badge p-2 px-3 rounded bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span
                                                class="badge p-2 px-3 rounded bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 3)
                                            <span
                                                class="badge p-2 px-3 rounded bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 4)
                                            <span
                                                class="badge p-2 px-3 rounded bg-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @endif
                                    </small>
                                </div>

                                <div class="col-md-4">
                                    <small>
                                        <strong>{{ __('Document Type') }} :</strong><br>
                                        {{ $customer->type ?? '-' }}
                                    </small>
                                </div>

                                <div class="col-md-4">
                                    <small>
                                        <strong>{{ __('Serial No') }} :</strong><br>
                                        {{ $customer->serial_no ?? '-' }}
                                    </small>
                                </div>

                                @if (!empty($customFields) && count($invoice->customField) > 0)
                                    @foreach ($customFields as $field)
                                        <div class="col text-md-right">
                                            <small>
                                                <strong>{{ $field->name }} :</strong><br>
                                                {{ !empty($invoice->customField) ? $invoice->customField[$field->id] : '-' }}
                                                <br><br>
                                            </small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="font-weight-bold">{{ __('Product Summary') }}</div>
                                    <small>{{ __('All items here cannot be deleted.') }}</small>
                                    <div class="table-responsive mt-2">
                                        <table class="table mb-0 table-striped">
                                            <thead>
                                                <tr>
                                                    <th data-width="40" class="text-dark">#</th>
                                                    <th class="text-dark">{{ __('Nooca Ruqsada') }}</th>
                                                    <th class="text-dark">{{ __('Grade') }}</th>
                                                    <th class="text-dark">{{ __('Diiwangalinta') }}</th>
                                                    <th class="text-dark">{{ __('B-Tabeelaha') }}</th>
                                                    <th class="text-dark">{{ __('Shahado') }}</th>
                                                    <th class="text-dark">{{ __('Buugga') }}</th>
                                                    <th class="text-dark">{{ __('Tijaabo Qadka') }}</th>
                                                    <th class="text-dark">{{ __('Discount') }}</th>

                                                    <th class="text-end text-dark" width="12%">{{ __('Amount') }}<br>
                                                        <small class="text-danger font-weight-bold">{{ __('after discount') }}</small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            @php
                                                $totals = [
                                                    'net' => 0,
                                                    'fare' => 0,
                                                    'tax' => 0,
                                                    'refund' => 0,
                                                    'commission' => 0,
                                                    'discount' => 0,
                                                    'before_discount' => 0,
                                                    'after_discount' => 0,
                                                    'tax' => 0,
                                                ];
                                                $taxesData = [];
                                                $taxReference = \App\Models\Utility::getTaxData();
                                            @endphp
                                            @foreach ($iteams as $key => $iteam)
                                                @php
                                                    $net = (float) ($iteam->net ?? 0);
                                                    $fareAmount = (float) ($iteam->fare ?? 0);
                                                    $tax = (float) ($iteam->tax ?? 0);
                                                    $refund = (float) ($iteam->refund ?? 0);
                                                    $commission = (float) ($iteam->commission ?? 0);
                                                    $discount = (float) ($iteam->discount ?? 0);
                                                    $lineAmount = $net + $fareAmount + $tax + $commission + $refund;
                                                    $lineSubtotal = $lineAmount  - $discount;

                                                    $totals['net'] += $net;
                                                    $totals['fare'] += $fareAmount;
                                                    $totals['tax'] += $tax;
                                                    $totals['refund'] += $refund;
                                                    $totals['commission'] += $commission;
                                                    $totals['discount'] += $discount;
                                                    $totals['before_discount'] += $lineSubtotal;
                                                    $totals['after_discount'] += $lineAmount;

                                                    $itemTaxes = [];
                                                    $itemTaxPrice = 0;
                                                    if (!empty($iteam->tax)) {
                                                        foreach (explode(',', $iteam->tax) as $taxId) {
                                                            if (empty($taxReference[$taxId])) {
                                                                continue;
                                                            }
                                                            $taxRate = $taxReference[$taxId]['rate'];
                                                            $taxPrice = max(0, $lineAmount) * ($taxRate / 100);
                                                            $itemTaxes[] = [
                                                                'name' => $taxReference[$taxId]['name'],
                                                                'rate' => $taxRate,
                                                                'price' => $taxPrice,
                                                            ];
                                                            $itemTaxPrice += $taxPrice;
                                                            $taxesData[$taxReference[$taxId]['name']] = ($taxesData[$taxReference[$taxId]['name']] ?? 0) + $taxPrice;
                                                        }
                                                    }
                                                    $totals['tax'] += $itemTaxPrice;


                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $iteam->type ?? '-' }}</td>
                                                    <td>{{ $iteam->grade ?? '-' }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($net) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($fareAmount) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($tax) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($refund) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($commission) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($discount) }}</td>
                                                    {{-- <td>
                                                        @if (!empty($itemTaxes))
                                                            <table>
                                                                @foreach ($itemTaxes as $tax)
                                                                    <tr>
                                                                        <td>{{ $tax['name'] . ' (' . $tax['rate'] . '%)' }}</td>
                                                                        <td>{{ \Auth::user()->priceFormat($tax['price']) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>

                                                        @if (!empty($iteam->description))
                                                            <div><strong>{{ __('Remarks') }}:</strong> {{ $iteam->description }}</div>
                                                        @endif
                                                        @if (empty($iteam->destination) && empty($iteam->description))
                                                            -
                                                        @endif
                                                    </td> --}}
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($lineAmount) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tfoot>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Sub Total') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($totals['before_discount']) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Discount') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($totals['discount']) }}
                                                    </td>
                                                </tr>

                                                @if (!empty($taxesData))
                                                    @foreach ($taxesData as $taxName => $taxPrice)
                                                        <tr>
                                                            <td colspan="8"></td>
                                                            <td class="text-end"><b>{{ $taxName }}</b></td>
                                                            <td class="text-end">
                                                                {{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="blue-text text-end"><b>{{ __('Total') }}</b></td>
                                                    <td class="blue-text text-end">
                                                        {{ \Auth::user()->priceFormat($totals['after_discount']) }}</td>
                                                </tr>
                                                @php
                                                    $creditNote = $invoice->creditNote->sum('amount');
                                                    $customerCreditNote = $invoice->invoiceTotalCustomerCreditNote();
                                                    $paymentsTotal = $invoice->payments->sum('amount');
                                                    $grandTotal = $totals['after_discount'];
                                                    $paidAmount = $paymentsTotal;
                                                    $dueAmount = max($grandTotal - $paidAmount - $creditNote, 0);
                                                @endphp
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Paid') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($paidAmount) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Credit Note Applied') }}</b></td>
                                                    <td class="text-end">{{ \Auth::user()->priceFormat($creditNote) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Credit Note Issued') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($customerCreditNote) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Due') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($dueAmount) }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class=" d-inline-block  mb-5">{{ __('Receipt Summary') }}</h5>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    {{-- <th class="text-dark">{{ __('Payment Receipt') }}</th> --}}
                                    <th class="text-dark">{{ __('Date') }}</th>
                                    <th class="text-dark">{{ __('Amount') }}</th>
                                    <th class="text-dark">{{ __('Payment Type') }}</th>
                                    <th class="text-dark">{{ __('Account') }}</th>
                                    <th class="text-dark">{{ __('Reference') }}</th>
                                    <th class="text-dark">{{ __('Description') }}</th>
                                    {{-- <th class="text-dark">{{ __('Receipt') }}</th>
                                    <th class="text-dark">{{ __('OrderId') }}</th>--}}
                                    @can('delete payment invoice')
                                        <th class="text-dark">{{ __('Action') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            @if (!empty($invoice->payments) && $invoice->bankPayments)
                                @php
                                    $path = \App\Models\Utility::get_file('uploads/order');
                                @endphp

                                @foreach ($invoice->payments as $key => $payment)
                                    <tr>
                                        {{-- <td>
                                            @if (!empty($payment->add_receipt))
                                                <a href="{{ asset(Storage::url('uploads/payment')) . '/' . $payment->add_receipt }}"
                                                    download="" class="btn btn-sm btn-secondary btn-icon"
                                                    target="_blank"><span class="btn-inner--icon"><i
                                                            class="ti ti-download"></i></span></a>
                                            @else
                                                -
                                            @endif
                                        </td> --}}
                                        <td>{{ \Auth::user()->dateFormat($payment->date) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($payment->amount) }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ !empty($payment->bankAccount) ? $payment->bankAccount->bank_name . ' ' . $payment->bankAccount->holder_name : '--' }}
                                        </td>
                                        <td>{{ !empty($payment->reference) ? $payment->reference : '--' }}</td>
                                        <td>{{ !empty($payment->description) ? $payment->description : '--' }}</td>
                                        {{-- <td>
                                            @if (!empty($payment->receipt))
                                                @if ($payment->payment_type == 'STRIPE')
                                                    <a href="{{ $payment->receipt }}" target="_blank">
                                                        <i class="ti ti-file"></i>{{ __('Receipt') }}</a>
                                                @else
                                                    <a href="{{ $path . '/' . $payment->receipt }}" target="_blank">
                                                        <i class="ti ti-file"></i>{{ __('Receipt') }}</a>
                                                @endif
                                             @elseif(!empty($payment->add_receipt))
                                                <a href="{{ asset(Storage::url('uploads/payment')) . '/' . $payment->add_receipt }}"
                                                    target="_blank">
                                                    <i class="ti ti-file"></i>{{ __('Receipt') }}</a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>{{ !empty($payment->order_id) ? $payment->order_id : '--' }}</td>--}}
                                        @can('delete invoice product')
                                            <td>
                                                <div class="action-btn">
                                                    {!! Form::open([
                                                        'method' => 'post',
                                                        'route' => ['invoice.payment.destroy', $invoice->id, $payment->id],
                                                        'id' => 'delete-form-' . $payment->id,
                                                    ]) !!}

                                                    <a href="#"
                                                        class="mx-3 bg-danger btn btn-sm align-items-center bs-pass-para"
                                                        data-bs-toggle="tooltip" title="Delete"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $payment->id }}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach

                                {{--  start for bank transfer --}}

                                @foreach ($invoice->bankPayments as $key => $bankPayment)
                                    <tr>
                                        <td>-</td>
                                        <td>{{ \Auth::user()->dateFormat($bankPayment->date) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($bankPayment->amount) }}</td>
                                        <td>{{ __('Bank Transfer') }}<br>

                                        </td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>
                                            @if (!empty($bankPayment->receipt))
                                                <a href="{{ $path . '/' . $bankPayment->receipt }}" target="_blank">
                                                    <i class="ti ti-file"></i> {{ __('Receipt') }}
                                                </a>
                                            @endif

                                        </td>
                                        <td>{{ !empty($bankPayment->order_id) ? $bankPayment->order_id : '--' }}</td>
                                        @can('delete invoice product')
                                            <td>
                                                @if ($bankPayment->status == 'Pending')
                                                    <div class="action-btn">
                                                        <a href="#"
                                                            data-url="{{ URL::to('invoice/' . $bankPayment->id . '/action') }}"
                                                            data-size="lg" data-ajax-popup="true"
                                                            data-title="{{ __('Payment Status') }}"
                                                            class="mx-3 btn btn-sm align-items-center bg-warning"
                                                            data-bs-toggle="tooltip" title="{{ __('Payment Status') }}"
                                                            data-original-title="{{ __('Payment Status') }}">
                                                            <i class="ti ti-caret-right text-white"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="action-btn ms-2">
                                                    {!! Form::open([
                                                        'method' => 'post',
                                                        'route' => ['invoice.payment.destroy', $invoice->id, $bankPayment->id],
                                                        'id' => 'delete-form-' . $bankPayment->id,
                                                    ]) !!}

                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-bs-toggle="tooltip" title="Delete"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $bankPayment->id }}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach

                                {{--  end for bank transfer --}}
                            @else
                                <tr>
                                    <td colspan="{{ Gate::check('delete invoice product') ? '10' : '9' }}"
                                        class="text-center text-dark">
                                        <p>{{ __('No Data Found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="d-inline-block mb-5">{{ __('Credit Note Summary') }}</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-dark">{{ __('Credit Note') }}</th>
                                    <th class="text-dark">{{ __('Date') }}</th>
                                    <th class="text-dark" class="">{{ __('Amount') }}</th>
                                    <th class="text-dark" class="">{{ __('Description') }}</th>
                                    @if (Gate::check('edit credit note') || Gate::check('delete credit note'))
                                        <th class="text-dark">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            @forelse($invoice->creditNote as $key =>$creditNote)
                                <tr>
                                    <td><span class="btn btn-outline-primary">{{ !empty($creditNote->creditNote) ? \App\Models\CustomerCreditNotes::creditNumberFormat($creditNote->creditNote->credit_id) : '---'}}</span></td>
                                    <td>{{ \Auth::user()->dateFormat($creditNote->date) }}</td>
                                    <td class="">{{ \Auth::user()->priceFormat($creditNote->amount) }}</td>
                                    <td class="">{{ $creditNote->description }}</td>
                                    <td>
                                        @can('edit credit note')
                                            <div class="action-btn me-2">
                                                <a data-url="{{ route('invoice.edit.credit.note', [$creditNote->invoice, $creditNote->id]) }}"
                                                    data-ajax-popup="true" title="{{ __('Edit') }}"
                                                    data-original-title="{{ __('Credit Note') }}" href="#"
                                                    class="mx-3 btn btn-sm align-items-center bg-info"
                                                    data-bs-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                        @endcan
                                        @can('delete credit note')
                                            <div class="action-btn">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['invoice.delete.credit.note', $creditNote->invoice, $creditNote->id],
                                                    'id' => 'delete-form-' . $creditNote->id,
                                                ]) !!}
                                                <a href="#"
                                                    class="mx-3 bg-danger btn btn-sm align-items-center bs-pass-para "
                                                    data-bs-toggle="tooltip" title="Delete"
                                                    data-original-title="{{ __('Delete') }}"
                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="document.getElementById('delete-form-{{ $creditNote->id }}').submit();">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <p class="text-dark">{{ __('No Data Found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
