<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'holder_name',
        'bank_name',
        'account_number',
        'chart_account_id',
        'opening_balance',
        'contact_number',
        'bank_address',
        'created_by',
    ];

    public function chartAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccount', 'id', 'chart_account_id');
    }

    public static function payments()
    {
        $payments = [
            'is_bank_transfer_enabled'        => 'Bank Transfer',
            'is_stripe_enabled'               => 'Stripe',
            'is_paypal_enabled'               => 'Paypal',
            'is_paystack_enabled'             => 'Paystack',
            'is_flutterwave_enabled'          => 'Flutterwave',
            'is_razorpay_enabled'             => 'Razorpay',
            'is_paytm_enabled'                => 'Paytm',
            'is_mercado_enabled'              => 'Mercado Pago',
            'is_mollie_enabled'               => 'Mollie',
            'is_skrill_enabled'               => 'Skrill',
            'is_coingate_enabled'             => 'CoinGate',
            'is_paymentwall_enabled'          => 'PaymentWall',
            'is_toyyibpay_enabled'            => 'Toyyibpay',
            'is_payfast_enabled'              => 'PayFast',
            'is_iyzipay_enabled'              => 'Iyzipay',
            'is_sspay_enabled'                => 'SSpay',
            'is_paytab_enabled'               => 'PayTab',
            'is_benefit_enabled'              => 'Benefit',
            'is_cashfree_enabled'             => 'Cashfree',
            'is_aamarpay_enabled'             => 'Aamarpay',
            'is_paytr_enabled'                => 'PayTR',
            'is_yookassa_enabled'             => 'Yookassa',
            'is_midtrans_enabled'             => 'Midtrans',
            'is_xendit_enabled'               => 'Xendit',
            'is_nepalste_enabled'             => 'Nepalste',
            'is_paiementpro_enabled'          => 'Paiement Pro',
            'is_cinetpay_enabled'             => 'Cinetpay',
            'is_fedapay_enabled'              => 'Fedapay',
            'is_payhere_enabled'              => 'PayHere',
            'tap_payment_is_on'               => 'Tap',
            'authorizenet_payment_is_on'      => 'AuthorizeNet',
            'khalti_payment_is_on'            => 'Khalti',
            'easebuzz_payment_is_on'          => 'Easebuzz',
            'company_ozow_payment_is_enabled' => 'Ozow',
        ];

        $company_payment_setting = Utility::getCompanyPaymentSetting(\Auth::user()->creatorId());
        $ignoreWords = ['is', 'on', 'enabled', 'payment' , 'company'];

        $finalKeys = [];

        foreach ($payments as $key => $value) {
            // Split by underscore
            if (isset($company_payment_setting[$key]) && $company_payment_setting[$key] === 'on') {
                $parts = explode('_', $key);

                // Filter out ignored words
                $filtered = array_filter($parts, function($word) use ($ignoreWords) {
                    return !in_array(strtolower($word), $ignoreWords);
                });

                // Join the remaining parts with space
                $finalKey = implode(' ', $filtered);

                $finalKeys[$finalKey] = $value;
            }
        }

        return $finalKeys;
    }

}

