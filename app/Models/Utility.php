<?php

namespace App\Models;

use App\Mail\CommonEmailTemplate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Twilio\Rest\Client;
use App\Models\AddTransactionLine;

class Utility extends Model
{
    private static $getsettings = null;
    private static $getsettingsid = null;
    private static $ratingData = null;
    private static $langData = null;

    public static function getSetting()
    {
        if (self::$getsettings == null) {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', 2)->get();
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
            self::$getsettings = $data;
        }
        return self::$getsettings;
    }

    public static function getSettingById($id)
    {
        // if (self::$getsettingsid == null) {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', $id)->get();
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
            self::$getsettingsid = $data;
        // }
        return self::$getsettingsid;
    }

    public static function settings()
    {

        if (\Auth::check())
        {$data = Utility::getSetting();

        } else {
            $data = Utility::getSetting();
        }

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "invoice_prefix" => "#INVO",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "ffffff",
            "bill_prefix" => "#BILL",
            "expense_prefix" => "#EXP",
            "bill_color" => "ffffff",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "",
            "employee_prefix" => "#EMP",
            'leave_status' => '1',
            "bug_prefix" => "#ISSUE",
            'title_text' => '',
            'footer_text' => '',
            "company_start_time" => "09:00",
            "company_end_time" => "18:00",
            'gdpr_cookie' => 'off',
            "interval_time" => "",
            "zoom_apikey" => "",
            "zoom_apisecret" => "",
            "slack_webhook" => "",
            "telegram_accestoken" => "",
            "telegram_chatid" => "",
            "enable_signup" => "on",
            'cookie_text' => 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.
',
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "favicon.png",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "color" => "",
            "SITE_RTL" => "off",
            "purchase_prefix" => "#PUR",
            "purchase_color" => "ffffff",
            "purchase_template" => "template1",
            "pos_color" => "ffffff",
            "pos_template" => "template1",
            "pos_prefix" => "#POS",
            "quotation_prefix" => "#QUO",
            "quotation_logo"=>'',
            "quotation_color" => "ffffff",
            "quotation_template" => "template1",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",

            "purchase_logo" => "",
            "proposal_logo" => "",
            "invoice_logo" => "",
            "pos_logo" => "",
            "contract_prefix" => "#CON",

            "barcode_type" => "code128",
            "barcode_format" => "css",

            'new_user' => '1',
            'new_client' => '1',
            'new_support_ticket' => '1',
            'lead_assigned' => '1',
            'deal_assigned' => '1',
            'new_award' => '1',
            'customer_invoice_sent' => '1',
            'customer_purchase_sent' => '1',
            'new_invoice_payment' => '1',
            'new_payment_reminder' => '1',
            'new_bill_payment' => '1',
            'bill_resent' => '1',
            'proposal_sent' => '1',
            'complaint_resent' => '1',
            'leave_action_sent' => '1',
            'payslip_sent' => '1',
            'promotion_sent' => '1',
            'resignation_sent' => '1',
            'termination_sent' => '1',
            'transfer_sent' => '1',
            'trip_sent' => '1',
            'vender_bill_sent' => '1',
            'warning_sent' => '1',
            'new_contract' => '1',
            'new_project' => '1',
            'new_task' => '1',
            'task_status_updated' => '1',
            'new_leave' => '1',
            'project_assign_member' => '1',

            'vat_gst_number_switch' => 'off',
            'google_calendar_enable' => 'off',
            'google_calender_json_file' => '',

            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'contactus_url' => '#',

            'twilio_sid' => '',
            'twilio_token' => '',
            'twilio_from' => '',
            'ai_chatgpt_enable' => 'off',
            'chat_gpt_key' => '',
            'chat_gpt_model'=> '',
            "ip_restrict" => "off",

            "mail_driver" => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',
            'timezone' => '',

            'recaptcha_module' => '',
            'google_recaptcha_key' => '',
            'google_recaptcha_secret' => '',
            'google_recaptcha_version' => '',

            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'pusher_app_cluster' => '',

            'color_flag'=>'false',
            'currency_space'=>'withspace',
            'decimal_separator'=>'dot',
            'thousand_separator'=>'dot',
            'currency_symbol' => 'withcurrencysymbol',
            'float_number' => 'comma',

            'zkteco_api_url' => '',
            'username' => '',
            'user_password' => '',
            'auth_token' => '',

            "qr_display" => 'on',
            "bill_qr_display" => 'on',
            "invoice_qr_display" => 'on',
            "pos_purchase_qr_display" => 'on',
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        config(
            [
                'captcha.secret' => $settings['google_recaptcha_secret'],
                'captcha.sitekey' => $settings['google_recaptcha_key'],
                'options' => [
                    'timeout' => 30,
                ],
            ]
        );
        return $settings;
    }

    public static function settingsById($user_id)
    {

        $data = Utility::getSettingById($user_id);
        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "invoice_prefix" => "#INVO",
            "journal_prefix" => "#JUR",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "ffffff",
            "bill_prefix" => "#BILL",
            "expense_prefix" => "#EXP",
            "bill_color" => "ffffff",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "qr_display" => 'on',
            "bill_qr_display" => 'on',
            "invoice_qr_display" => 'on',
            "pos_purchase_qr_display" => 'on',
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "quotation_prefix" => "#QUO",
            "quotation_logo"=>'',
            "pos_color" => "ffffff",
            "quotation_template" => "template1",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "",
            "employee_prefix" => "#EMP",
            "contract_prefix" => "#CON",
            'leave_status' => '1',
            "bug_prefix" => "#ISSUE",
            'title_text' => '',
            'footer_text' => '',
            "company_start_time" => "09:00",
            "company_end_time" => "18:00",
            'gdpr_cookie' => 'off',
            "interval_time" => "",
            "zoom_apikey" => "",
            "zoom_apisecret" => "",
            "slack_webhook" => "",
            "telegram_accestoken" => "",
            "telegram_chatid" => "",
            "enable_signup" => "on",
            'cookie_text' => 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.
',
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "favicon.png",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "color" => "",
            "SITE_RTL" => "off",
            "purchase_prefix" => "#PUR",
            "purchase_color" => "ffffff",
            "purchase_template" => "template1",
            "proposal_logo" => "",
            "purchase_logo" => "",
            "invoice_logo" => "",
            "pos_logo" => "",
            "pos_color" => "ffffff",
            "pos_template" => "template1",
            "bill_logo" => '',

            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",

            "barcode_type" => "code128",
            "barcode_format" => "css",

            'new_user' => '1',
            'new_client' => '1',
            'new_support_ticket' => '1',
            'lead_assigned' => '1',
            'deal_assigned' => '1',
            'new_award' => '1',
            'customer_invoice_sent' => '1',
            'customer_purchase_sent' => '1',
            'new_invoice_payment' => '1',
            'new_payment_reminder' => '1',
            'new_bill_payment' => '1',
            'bill_resent' => '1',
            'proposal_sent' => '1',
            'complaint_resent' => '1',
            'leave_action_sent' => '1',
            'payslip_sent' => '1',
            'promotion_sent' => '1',
            'resignation_sent' => '1',
            'termination_sent' => '1',
            'transfer_sent' => '1',
            'trip_sent' => '1',
            'vender_bill_sent' => '1',
            'warning_sent' => '1',
            'new_contract' => '1',
            'new_project' => '1',
            'new_task' => '1',
            'task_status_updated' => '1',
            'new_leave' => '1',
            'project_assign_member' => '1',

            'vat_gst_number_switch' => 'off',
            'google_calendar_enable' => '',
            'google_calender_json_file' => '',

            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'contactus_url' => '#',

            'twilio_sid' => '',
            'twilio_token' => '',
            'twilio_from' => '',
            'ai_chatgpt_enable' => 'off',
            'chat_gpt_key' => '',
            'chat_gpt_model'=> '',
            "ip_restrict" => "off",

            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'pusher_app_cluster' => '',

            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',

            'currency_space'=>'withpace',
            'decimal_separator'=>'dot',
            'thousand_separator'=>'dot',
            'currency_symbol' => 'withcurrencysymbol',
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static $emailStatus = [
        'new_user' => 'New User',
        'new_client' => 'New Client',
        'new_support_ticket' => 'New Support Ticket',
        'lead_assigned' => 'Lead Assigned',
        'deal_assigned' => 'Deal Assigned',
        'new_award' => 'New Award',
        'customer_invoice_sent' => 'Customer Invoice Sent',
        'customer_purchase_sent' => 'Customer Purchase Sent',
        'new_invoice_payment' => 'New Invoice Payment',
        'new_payment_reminder' => 'New Payment Reminder',
        'new_bill_payment' => 'New Bill Payment',
        'bill_resent' => 'Bill Resent',
        'proposal_sent' => 'Proposal Sent',
        'complaint_resent' => 'Complaint Resent',
        'leave_action_sent' => 'Leave Action Sent',
        'payslip_sent' => 'Payslip Sent',
        'promotion_sent' => 'Promotion Sent',
        'resignation_sent' => 'Resignation Sent',
        'termination_sent' => 'Termination Sent',
        'transfer_sent' => 'Transfer Sent',
        'trip_sent' => 'Trip Sent',
        'vender_bill_sent' => 'Vendor Bill Sent',
        'warning_sent' => 'Warning Sent',
        'new_contract' => 'New Contract',
        'project_assign_member' => 'Project Assign Member',
    ];

    public static function languages()
    {
        if (self::$langData == null) {
            $languages = Utility::langList();

            if (\Schema::hasTable('languages')) {
                $settings = Utility::settings();
                if (!empty($settings['disable_lang'])) {
                    $disabledlang = explode(',', $settings['disable_lang']);
                    $languages = Language::whereNotIn('code', $disabledlang)->pluck('full_name', 'code');
                } else {
                    $languages = Language::pluck('full_name', 'code');
                }
            }

            self::$langData = $languages;
        }

        return self::$langData;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr = [];
        $arr['colors'] = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function priceFormat($settings, $price)
    {

        $number = explode('.', $price);
        $length = strlen(trim($number[0]));
        if($length > 3)
        {
            $decimal_separator = $settings['decimal_separator'] == 'dot' ? ',' : ',';
            $thousand_separator = $settings['thousand_separator'] == 'dot' ? '.' : ',';
        }
        else
        {
            $decimal_separator = $settings['decimal_separator'] == 'dot' ? '.' : ',';
            $thousand_separator = $settings['thousand_separator'] == 'dot' ? '.' : ',';
        }

        $currency = $settings['currency_symbol'] == 'withcurrencysymbol' ? $settings['site_currency_symbol']: $settings['site_currency'];

        $decimal_number = $settings['decimal_number'] ? $settings['decimal_number'] : 0;
        $currency_space = $settings['currency_space'];

        return (($settings['site_currency_symbol_position'] == "pre") ? $currency : '') . ($currency_space == 'withspace' ? ' ' : '') . number_format($price, $decimal_number , $decimal_separator, $thousand_separator) . ($currency_space == 'withspace' ? ' ' : '') . (($settings['site_currency_symbol_position'] == "post") ? $currency : '');

    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function dateFormat($settings, $date)
    {
        return date($settings['site_date_format'], strtotime($date));
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_time_format'], strtotime($time));
    }
    public static function purchaseNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["purchase_prefix"] . sprintf("%05d", $number);
    }
    public static function quotationNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["quotation_prefix"] . sprintf("%05d", $number);
    }

    public static function posNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["pos_prefix"] . sprintf("%05d", $number);
    }

    public static function contractNumberFormat($number)
    {

        $settings = self::settings();
        return $settings["contract_prefix"] . sprintf("%05d", $number);
    }

    public static function invoiceNumberFormat($settings, $number)
    {

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function customerProposalNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function customerInvoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }
    public static function customerPosNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["pos_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function vendorBillNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    private static $taxes = null;

    public static function taxes($tax)
    {
        if (self::$taxes == null) {
            $tax = Tax::find($tax);
            self::$taxes = $tax;
        }
        return self::$taxes;
    }

    private static $taxData = null;

    public static function tax($taxes)
    {

        if (self::$taxData == null) {
            $taxArr = explode(',', $taxes);
            $taxes = [];
            foreach ($taxArr as $tax) {
                $taxes[] = self::taxes($tax);
            }
            self::$taxData = $taxes;
        }

        return self::$taxData;
    }

    public static $rates;
    public static $data;

    public static function getTaxData()
    {
        $data = [];
        if (self::$rates == null) {
            $rates = Tax::get();
            self::$rates = $rates;
            foreach (self::$rates as $rate) {
                $data[$rate->id]['id'] = $rate->id
                ;
                $data[$rate->id]['name'] = $rate->name;
                $data[$rate->id]['rate'] = $rate->rate;
                $data[$rate->id]['created_by'] = $rate->created_by;
            }
            self::$data = $data;
        }
        return self::$data;
    }

    public static function taxRate($taxRate, $price, $quantity, $discount = 0)
    {
        return (($price * $quantity) - $discount) * ($taxRate / 100);
    }

    private static $taxRateData = null;

    public static function totalTaxRate($taxes)
    {
        if (self::$taxRateData == null) {
            $taxArr = explode(',', $taxes);
            $taxRate = 0;

            foreach ($taxArr as $tax) {
                $tax = self::taxes($tax);
                $taxRate += !empty($tax->rate) ? $tax->rate : 0;
            }

            self::$taxRateData = $taxRate;
        }
        return self::$taxRateData;
    }

    //start for customer & vendor balance
    public static function userBalance($users, $id, $amount, $type)
    {
        if ($users == 'customer') {
            $user = Customer::find($id);
        } else {
            $user = Vender::find($id);
        }

        if (!empty($user)) {
            if ($type == 'credit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance + $amount;
                $user->balance = $userBalance;
                $user->save();
            } elseif ($type == 'debit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance - $amount;
                $user->balance = $userBalance;
                $user->save();
            }
        }
    }

    public static function updateUserBalance($users, $id, $amount, $type)
    {
        if ($users == 'customer') {
            $user = Customer::find($id);
        } else {
            $user = Vender::find($id);
        }

        if (!empty($user)) {
            if ($type == 'credit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance + $amount;
                $user->balance = $userBalance;
                $user->save();
            } elseif ($type == 'debit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance - $amount;
                $user->balance = $userBalance;
                $user->save();
            }
        }
    }

    //end for customer & vendor balance

    public static function bankAccountBalance($id, $amount, $type)
    {
        $bankAccount = BankAccount::find($id);
        if ($bankAccount) {
            if ($type == 'credit') {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $amount;
                $bankAccount->save();
            } elseif ($type == 'debit') {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $amount;
                $bankAccount->save();
            }
        }

    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for ($i = 0; $i < count($C); ++$i) {
            if ($C[$i] <= 0.03928) {
                $C[$i] = $C[$i] / 12.92;
            } else {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if ($L > 0.179) {
            $color = 'black';
        } else {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public static $chartOfAccountType = [
        'assets' => 'Assets',
        'liabilities' => 'Liabilities',
        'equity' => 'Equity',
        'income' => 'Income',
        'costs of goods sold' => 'Costs of Goods Sold',
        'expenses' => 'Expenses',

    ];

    public static $chartOfAccountSubType = array(
        "assets" => array(
            '1' => 'Accounts Receivable',
            '2' => 'Current Asset',
            '3' => 'Inventory Asset',
            '4' => 'Non-current Asset',
        ),
        "liabilities" => array(
            '1' => 'Accounts Payable',
            '2' => 'Current Liabilities',
            '3' => 'Long Term Liabilities',
            '4' => 'Share Capital',
            '5' => 'Retained Earnings',
        ),
        "equity" => array(
            '1' => 'Owners Equity',
        ),
        "income" => array(
            '1' => 'Sales Revenue',
            '2' => 'Other Revenue',
        ),
        "costs of goods sold" => array(
            '1' => 'Costs of Goods Sold',
        ),
        "expenses" => array(
            '1' => 'Payroll Expenses',
            '2' => 'General and Administrative expenses',
        ),

    );

    public static function chartOfAccountTypeData($company_id)
    {
        $chartOfAccountTypes = Self::$chartOfAccountType;
        foreach ($chartOfAccountTypes as $k => $type) {

            $accountType = ChartOfAccountType::create(
                [
                    'name' => $type,
                    'created_by' => $company_id,
                ]
            );

            $chartOfAccountSubTypes = Self::$chartOfAccountSubType;

            foreach ($chartOfAccountSubTypes[$k] as $subType) {
                ChartOfAccountSubType::create(
                    [
                        'name' => $subType,
                        'type' => $accountType->id,
                        'created_by' => $company_id,
                    ]
                );
            }
        }
    }

    public static $chartOfAccount = array(

        [
            'code' => '1050',
            'name' => 'Accounts Receivable',
            'type' => 1,
            'sub_type' => 1,
        ],
        [
            'code' => '1060',
            'name' => 'Checking Account',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1065',
            'name' => 'Petty Cash',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1205',
            'name' => 'Allowance for doubtful accounts',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1510',
            'name' => 'Inventory',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1520',
            'name' => 'Stock of Raw Materials',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1530',
            'name' => 'Stock of Work In Progress',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1540',
            'name' => 'Stock of Finished Goods',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1550',
            'name' => 'Goods Received Clearing account',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1810',
            'name' => 'Land and Buildings',
            'type' => 1,
            'sub_type' => 4,
        ],
        [
            'code' => '1820',
            'name' => 'Office Furniture and Equipement',
            'type' => 1,
            'sub_type' => 4,
        ],
        [
            'code' => '1825',
            'name' => 'Accum.depreciation-Furn. and Equip',
            'type' => 1,
            'sub_type' => 4,
        ],
        [
            'code' => '1840',
            'name' => 'Motor Vehicle',
            'type' => 1,
            'sub_type' => 4,
        ],
        [
            'code' => '1845',
            'name' => 'Accum.depreciation-Motor Vehicle',
            'type' => 1,
            'sub_type' => 4,
        ],
        [
            'code' => '2100',
            'name' => 'Accounts Payable',
            'type' => 2,
            'sub_type' => 5,
        ],
        [
            'code' => '2105',
            'name' => 'Deferred Income',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2110',
            'name' => 'Accrued Income Tax-Central',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2120',
            'name' => 'Income Tax Payable',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2130',
            'name' => 'Accrued Franchise Tax',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2140',
            'name' => 'Vat Provision',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2145',
            'name' => 'Purchase Tax',
            'type' => 2,
            'sub_type' => 6,
        ], [
            'code' => '2150',
            'name' => 'VAT Pay / Refund',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2151',
            'name' => 'Zero Rated',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2152',
            'name' => 'Capital import',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2153',
            'name' => 'Standard Import',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2154',
            'name' => 'Capital Standard',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2155',
            'name' => 'Vat Exempt',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2160',
            'name' => 'Accrued Use Tax Payable',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2210',
            'name' => 'Accrued Wages',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2220',
            'name' => 'Accrued Comp Time',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2230',
            'name' => 'Accrued Holiday Pay',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2240',
            'name' => 'Accrued Vacation Pay',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2310',
            'name' => 'Accr. Benefits - Central Provident Fund',
            'type' => 2,
            'sub_type' => 6,
        ], [
            'code' => '2320',
            'name' => 'Accr. Benefits - Stock Purchase',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2330',
            'name' => 'Accr. Benefits - Med, Den',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2340',
            'name' => 'Accr. Benefits - Payroll Taxes',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2350',
            'name' => 'Accr. Benefits - Credit Union',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2360',
            'name' => 'Accr. Benefits - Savings Bond',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2370',
            'name' => 'Accr. Benefits - Group Insurance',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2380',
            'name' => 'Accr. Benefits - Charity Cont.',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '2620',
            'name' => 'Bank Loans',
            'type' => 2,
            'sub_type' => 7,
        ],
        [
            'code' => '2680',
            'name' => 'Loans from Shareholders',
            'type' => 2,
            'sub_type' => 7,
        ],
        [
            'code' => '3350',
            'name' => 'Common Shares',
            'type' => 2,
            'sub_type' => 8,
        ],
        [
            'code' => '3590',
            'name' => 'Reserves and Surplus',
            'type' => 2,
            'sub_type' => 9,
        ],
        [
            'code' => '3595',
            'name' => 'Owners Drawings',
            'type' => 2,
            'sub_type' => 9,
        ],
        [
            'code' => '3020',
            'name' => 'Opening Balances and adjustments',
            'type' => 3,
            'sub_type' => 10,
        ],
        [
            'code' => '3025',
            'name' => 'Owners Contribution',
            'type' => 3,
            'sub_type' => 10,
        ],
        [
            'code' => '3030',
            'name' => 'Profit and Loss ( current Year)',
            'type' => 3,
            'sub_type' => 10,
        ],
        [
            'code' => '3035',
            'name' => 'Retained income',
            'type' => 3,
            'sub_type' => 10,
        ],
        [
            'code' => '4010',
            'name' => 'Sales Income',
            'type' => 4,
            'sub_type' => 11,
        ],
        [
            'code' => '4020',
            'name' => 'Service Income',
            'type' => 4,
            'sub_type' => 11,
        ],
        [
            'code' => '4430',
            'name' => 'Shipping and Handling',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '4435',
            'name' => 'Sundry Income',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '4440',
            'name' => 'Interest Received',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '4450',
            'name' => 'Foreign Exchange Gain',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '4500',
            'name' => 'Unallocated Income',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '4510',
            'name' => 'Discounts Received',
            'type' => 4,
            'sub_type' => 12,
        ],
        [
            'code' => '5005',
            'name' => 'Cost of Sales- On Services',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5010',
            'name' => 'Cost of Sales - Purchases',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5015',
            'name' => 'Operating Costs',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5020',
            'name' => 'Material Usage Varaiance',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5025',
            'name' => 'Breakage and Replacement Costs',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5030',
            'name' => 'Consumable Materials',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5035',
            'name' => 'Sub-contractor Costs',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5040',
            'name' => 'Purchase Price Variance',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5045',
            'name' => 'Direct Labour - COS',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5050',
            'name' => 'Purchases of Materials',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5060',
            'name' => 'Discounts Received',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5100',
            'name' => 'Freight Costs',
            'type' => 5,
            'sub_type' => 13,
        ],
        [
            'code' => '5410',
            'name' => 'Salaries and Wages',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5415',
            'name' => 'Directors Fees & Remuneration',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5420',
            'name' => 'Wages - Overtime',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5425',
            'name' => 'Members Salaries',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5430',
            'name' => 'UIF Payments',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5440',
            'name' => 'Payroll Taxes',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5450',
            'name' => 'Workers Compensation ( Coida )',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5460',
            'name' => 'Normal Taxation Paid',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5470',
            'name' => 'General Benefits',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5510',
            'name' => 'Provisional Tax Paid',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5520',
            'name' => 'Inc Tax Exp - State',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5530',
            'name' => 'Taxes - Real Estate',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5540',
            'name' => 'Taxes - Personal Property',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5550',
            'name' => 'Taxes - Franchise',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5560',
            'name' => 'Taxes - Foreign Withholding',
            'type' => 6,
            'sub_type' => 14,
        ],
        [
            'code' => '5610',
            'name' => 'Accounting Fees',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5615',
            'name' => 'Advertising and Promotions',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5620',
            'name' => 'Bad Debts',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5625',
            'name' => 'Courier and Postage',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5660',
            'name' => 'Depreciation Expense',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5685',
            'name' => 'Insurance Expense',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5690',
            'name' => 'Bank Charges',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5695',
            'name' => 'Interest Paid',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5700',
            'name' => 'Office Expenses - Consumables',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5705',
            'name' => 'Printing and Stationary',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5710',
            'name' => 'Security Expenses',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5715',
            'name' => 'Subscription - Membership Fees',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5755',
            'name' => 'Electricity, Gas and Water',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5760',
            'name' => 'Rent Paid',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5765',
            'name' => 'Repairs and Maintenance',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5770',
            'name' => 'Motor Vehicle Expenses',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5771',
            'name' => 'Petrol and Oil',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5775',
            'name' => 'Equipment Hire - Rental',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5780',
            'name' => 'Telephone and Internet',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5785',
            'name' => 'Travel and Accommodation',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5786',
            'name' => 'Meals and Entertainment',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5787',
            'name' => 'Staff Training',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5790',
            'name' => 'Utilities',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5791',
            'name' => 'Computer Expenses',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5795',
            'name' => 'Registrations',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5800',
            'name' => 'Licenses',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '5810',
            'name' => 'Foreign Exchange Loss',
            'type' => 6,
            'sub_type' => 15,
        ],
        [
            'code' => '9990',
            'name' => 'Profit and Loss',
            'type' => 6,
            'sub_type' => 15,
        ],

    );

    public static $chartOfAccount1 = array(
        [
            'code' => '1050',
            'name' => 'Accounts Receivable',
            'type' => 'Assets',
            'sub_type' => 'Accounts Receivable',
        ],
        [
            'code' => '1060',
            'name' => 'Checking Account',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1065',
            'name' => 'Petty Cash',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1205',
            'name' => 'Allowance for doubtful accounts',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1510',
            'name' => 'Inventory',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1520',
            'name' => 'Stock of Raw Materials',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1530',
            'name' => 'Stock of Work In Progress',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1540',
            'name' => 'Stock of Finished Goods',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1550',
            'name' => 'Goods Received Clearing account',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1810',
            'name' => 'Land and Buildings',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1820',
            'name' => 'Office Furniture and Equipement',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1825',
            'name' => 'Accum.depreciation-Furn. and Equip',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1840',
            'name' => 'Motor Vehicle',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1845',
            'name' => 'Accum.depreciation-Motor Vehicle',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '2100',
            'name' => 'Accounts Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Accounts Payable',
        ],
        [
            'code' => '2105',
            'name' => 'Deferred Income',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2110',
            'name' => 'Accrued Income Tax-Central',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2120',
            'name' => 'Income Tax Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2130',
            'name' => 'Accrued Franchise Tax',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2140',
            'name' => 'Vat Provision',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2145',
            'name' => 'Purchase Tax',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ], [
            'code' => '2150',
            'name' => 'VAT Pay / Refund',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2151',
            'name' => 'Zero Rated',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2152',
            'name' => 'Capital import',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2153',
            'name' => 'Standard Import',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2154',
            'name' => 'Capital Standard',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2155',
            'name' => 'Vat Exempt',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2160',
            'name' => 'Accrued Use Tax Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2210',
            'name' => 'Accrued Wages',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2220',
            'name' => 'Accrued Comp Time',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2230',
            'name' => 'Accrued Holiday Pay',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2240',
            'name' => 'Accrued Vacation Pay',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2310',
            'name' => 'Accr. Benefits - Central Provident Fund',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ], [
            'code' => '2320',
            'name' => 'Accr. Benefits - Stock Purchase',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2330',
            'name' => 'Accr. Benefits - Med, Den',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2340',
            'name' => 'Accr. Benefits - Payroll Taxes',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2350',
            'name' => 'Accr. Benefits - Credit Union',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2360',
            'name' => 'Accr. Benefits - Savings Bond',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2370',
            'name' => 'Accr. Benefits - Group Insurance',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2380',
            'name' => 'Accr. Benefits - Charity Cont.',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2620',
            'name' => 'Bank Loans',
            'type' => 'Liabilities',
            'sub_type' => 'Long Term Liabilities',
        ],
        [
            'code' => '2680',
            'name' => 'Loans from Shareholders',
            'type' => 'Liabilities',
            'sub_type' => 'Long Term Liabilities',
        ],
        [
            'code' => '3350',
            'name' => 'Common Shares',
            'type' => 'Liabilities',
            'sub_type' => 'Share Capital',
        ],
        [
            'code' => '3590',
            'name' => 'Reserves and Surplus',
            'type' => 'Liabilities',
            'sub_type' => 'Retained Earnings',
        ],
        [
            'code' => '3595',
            'name' => 'Owners Drawings',
            'type' => 'Liabilities',
            'sub_type' => 'Retained Earnings',
        ],
        [
            'code' => '3020',
            'name' => 'Opening Balances and adjustments',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3025',
            'name' => 'Owners Contribution',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3030',
            'name' => 'Profit and Loss ( current Year)',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3035',
            'name' => 'Retained income',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '4010',
            'name' => 'Sales Income',
            'type' => 'Income',
            'sub_type' => 'Sales Revenue',
        ],
        [
            'code' => '4020',
            'name' => 'Service Income',
            'type' => 'Income',
            'sub_type' => 'Sales Revenue',
        ],
        [
            'code' => '4430',
            'name' => 'Shipping and Handling',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4435',
            'name' => 'Sundry Income',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4440',
            'name' => 'Interest Received',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4450',
            'name' => 'Foreign Exchange Gain',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4500',
            'name' => 'Unallocated Income',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4510',
            'name' => 'Discounts Received',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '5005',
            'name' => 'Cost of Sales- On Services',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5010',
            'name' => 'Cost of Sales - Purchases',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5015',
            'name' => 'Operating Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5020',
            'name' => 'Material Usage Varaiance',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5025',
            'name' => 'Breakage and Replacement Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5030',
            'name' => 'Consumable Materials',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5035',
            'name' => 'Sub-contractor Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5040',
            'name' => 'Purchase Price Variance',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5045',
            'name' => 'Direct Labour - COS',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5050',
            'name' => 'Purchases of Materials',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5060',
            'name' => 'Discounts Received',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5100',
            'name' => 'Freight Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5410',
            'name' => 'Salaries and Wages',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5415',
            'name' => 'Directors Fees & Remuneration',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5420',
            'name' => 'Wages - Overtime',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5425',
            'name' => 'Members Salaries',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5430',
            'name' => 'UIF Payments',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5440',
            'name' => 'Payroll Taxes',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5450',
            'name' => 'Workers Compensation ( Coida )',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5460',
            'name' => 'Normal Taxation Paid',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5470',
            'name' => 'General Benefits',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5510',
            'name' => 'Provisional Tax Paid',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5520',
            'name' => 'Inc Tax Exp - State',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5530',
            'name' => 'Taxes - Real Estate',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5540',
            'name' => 'Taxes - Personal Property',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5550',
            'name' => 'Taxes - Franchise',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5560',
            'name' => 'Taxes - Foreign Withholding',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5610',
            'name' => 'Accounting Fees',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5615',
            'name' => 'Advertising and Promotions',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5620',
            'name' => 'Bad Debts',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5625',
            'name' => 'Courier and Postage',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5660',
            'name' => 'Depreciation Expense',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5685',
            'name' => 'Insurance Expense',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5690',
            'name' => 'Bank Charges',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5695',
            'name' => 'Interest Paid',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5700',
            'name' => 'Office Expenses - Consumables',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5705',
            'name' => 'Printing and Stationary',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5710',
            'name' => 'Security Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5715',
            'name' => 'Subscription - Membership Fees',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5755',
            'name' => 'Electricity, Gas and Water',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5760',
            'name' => 'Rent Paid',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5765',
            'name' => 'Repairs and Maintenance',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5770',
            'name' => 'Motor Vehicle Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5771',
            'name' => 'Petrol and Oil',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5775',
            'name' => 'Equipment Hire - Rental',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5780',
            'name' => 'Telephone and Internet',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5785',
            'name' => 'Travel and Accommodation',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5786',
            'name' => 'Meals and Entertainment',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5787',
            'name' => 'Staff Training',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5790',
            'name' => 'Utilities',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5791',
            'name' => 'Computer Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5795',
            'name' => 'Registrations',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5800',
            'name' => 'Licenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5810',
            'name' => 'Foreign Exchange Loss',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '9990',
            'name' => 'Profit and Loss',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],

    );

    public static function chartOfAccountData1($user)
    {
        $chartOfAccounts = Self::$chartOfAccount1;

        foreach ($chartOfAccounts as $account) {

            $type = ChartOfAccountType::where('created_by', $user)->where('name', $account['type'])->first();
            $sub_type = ChartOfAccountSubType::where('type', $type->id)->where('name', $account['sub_type'])->where('created_by' , $user)->first();

            $account_name = ChartOfAccount::where('type', $type->id)->where('name', $account['name'])->where('created_by' , $user)->first();

            if(empty($account_name)) {
                ChartOfAccount::create(
                    [
                        'code' => $account['code'],
                        'name' => $account['name'],
                        'type' => $type->id,
                        'sub_type' => $sub_type->id,
                        'is_enabled' => 1,
                        'created_by' => $user,
                    ]
                );
            }

        }
    }

    public static function chartOfAccountData($user)
    {
        $chartOfAccounts = Self::$chartOfAccount;
        foreach ($chartOfAccounts as $account) {
            ChartOfAccount::create(
                [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'sub_type' => $account['sub_type'],
                    'is_enabled' => 1,
                    'created_by' => $user->id,
                ]
            );

        }
    }

    public static function addNewAccountData()
    {
        $users = User::where('type','company')->get();

        foreach($users as $user)
        {
            $chartOfAccountTypes = Self::$chartOfAccountType;
            foreach($chartOfAccountTypes as $k => $type) {
                $check_type = ChartOfAccountType::where('created_by', $user->id)->where('name', $type)->first();

                $chartOfAccountSubTypes = Self::$chartOfAccountSubType;
                foreach ($chartOfAccountSubTypes[$k] as $subType) {
                    $check_subtype = ChartOfAccountSubType::where('created_by', $user->id)->where('type', $check_type->id)->where('name', $subType)->first();
                    if (empty($check_subtype)) {
                        $accountSubType = ChartOfAccountSubType::create(
                            [
                                'name' => $subType,
                                'type' => $check_type->id,
                                'created_by' => $user->id,
                            ]
                        );

                        $chartOfAccounts = Self::$chartOfAccount1;

                        foreach ($chartOfAccounts as $chartAccount) {
                            $type = ChartOfAccountType::where('created_by', $user->id)->where('name', $chartAccount['type'])->first();
                            $sub_type = ChartOfAccountSubType::where('type', $type->id)->where('name', $chartAccount['sub_type'])->where('created_by' , $user->id)->first();
                            $check_account = ChartOfAccount::where('name', $chartAccount['name'])->where('created_by' , $user->id)->first();
                            $receivableAccount = ChartOfAccount::where('created_by', $user->id)->where('type', $check_type->id)->where('name', 'Accounts Receivable')->first();
                            $payableAccount = ChartOfAccount::where('type', $check_type->id)->where('name', 'Account Payable')->first();
                                if(!empty($receivableAccount))
                                {
                                    $receivableAccount->delete();
                                }
                                if(!empty($payableAccount))
                                {
                                    $payableAccount->delete();
                                }
                            if (empty($check_account)) {
                                ChartOfAccount::create(
                                    [
                                        'name' => $chartAccount['name'],
                                        'code' => $chartAccount['code'],
                                        'type' => $type->id,
                                        'sub_type' => $sub_type->id,
                                        'is_enabled' => 1,
                                        'created_by' => $user->id,
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
    {
        $usr = Auth::user();

        //Remove Current Login user Email don't send mail to them
        // unset($mailTo[$usr->id]);

        $mailTo = array_values($mailTo);

        if ($usr->type != 'Super Admin') {
            // find template is exist or not in our record
            $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

            if (isset($template) && !empty($template)) {
                // check template is active or not by company
                $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->ownerId())->first();

                if ($is_active->is_active == 1) {
                    $settings = self::settings();

                    // get email content language base
                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();

                    $content->from = $template->from;
                    if (!empty($content->content)) {
                        $content->content = self::replaceVariable($content->content, $obj);

                        // send email
                        try
                        {
                            config(
                                [
                                    'mail.driver' => $settings['mail_driver'],
                                    'mail.host' => $settings['mail_host'],
                                    'mail.port' => $settings['mail_port'],
                                    'mail.encryption' => $settings['mail_encryption'],
                                    'mail.username' => $settings['mail_username'],
                                    'mail.password' => $settings['mail_password'],
                                    'mail.from.address' => $settings['mail_from_address'],
                                    'mail.from.name' => $settings['mail_from_name'],
                                ]
                            );
                            Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                        } catch (\Exception $e) {
                            $error = $e->getMessage();
                        }

                        if (isset($error)) {
                            $arReturn = [
                                'is_success' => false,
                                'error' => $error,
                            ];
                        } else {
                            $arReturn = [
                                'is_success' => true,
                                'error' => false,
                            ];
                        }
                    } else {
                        $arReturn = [
                            'is_success' => false,
                            'error' => __('Mail not send, email is empty'),
                        ];
                    }

                    return $arReturn;
                } else {
                    return [
                        'is_success' => true,
                        'error' => false,
                    ];
                }
            } else {
                return [
                    'is_success' => false,
                    'error' => __('Mail not send, email not found'),
                ];
            }
        }
    }

    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{app_name}',
            '{company_name}',
            '{app_url}',
            '{email}',
            '{password}',
            '{client_name}',
            '{client_email}',
            '{client_password}',
            '{support_name}',
            '{support_title}',
            '{support_priority}',
            '{support_end_date}',
            '{support_description}',
            '{lead_name}',
            '{lead_email}',
            '{lead_subject}',
            '{lead_pipeline}',
            '{lead_stage}',
            '{deal_name}',
            '{deal_pipeline}',
            '{deal_stage}',
            '{deal_status}',
            '{deal_price}',
            '{award_name}',
            '{award_email}',
            '{customer_name}',
            '{customer_email}',
            '{invoice_name}',
            '{purchase_name}',
            '{invoice_number}',
            '{invoice_url}',
            '{invoice_payment_name}',
            '{invoice_payment_amount}',
            '{invoice_payment_date}',
            '{payment_dueAmount}',
            '{payment_reminder_name}',
            '{invoice_payment_number}',
            '{invoice_payment_dueAmount}',
            '{payment_reminder_date}',
            '{payment_name}',
            '{payment_bill}',
            '{payment_amount}',
            '{payment_date}',
            '{payment_method}',
            '{vender_name}',
            '{vender_email}',
            '{bill_name}',
            '{bill_number}',
            '{bill_url}',
            '{proposal_name}',
            '{proposal_number}',
            '{proposal_url}',
            '{complaint_name}',
            '{complaint_title}',
            '{complaint_against}',
            '{complaint_date}',
            '{complaint_description}',
            '{leave_name}',
            '{leave_status}',
            '{leave_reason}',
            '{leave_start_date}',
            '{leave_end_date}',
            '{total_leave_days}',
            '{employee_name}',
            '{employee_email}',
            '{payslip_name}',
            '{payslip_salary_month}',
            '{payslip_url}',
            '{promotion_designation}',
            '{promotion_title}',
            '{promotion_date}',
            '{resignation_email}',
            '{assign_user}',
            '{resignation_date}',
            '{notice_date}',
            '{termination_name}',
            '{termination_email}',
            '{termination_date}',
            '{termination_type}',
            '{transfer_name}',
            '{transfer_email}',
            '{transfer_date}',
            '{transfer_department}',
            '{transfer_branch}',
            '{transfer_description}',
            '{trip_name}',
            '{purpose_of_visit}',
            '{start_date}',
            '{end_date}',
            '{place_of_visit}',
            '{trip_description}',
            '{vender_bill_name}',
            '{vender_bill_number}',
            '{vender_bill_url}',
            '{employee_warning_name}',
            '{warning_subject}',
            '{warning_description}',
            '{contract_client}',
            '{contract_subject}',
            '{contract_start_date}',
            '{contract_end_date}',
            '{user_name}',
            '{lead_user_name}',
            '{project_name}',
            '{payment_price}',
            '{invoice_payment_type}',
            '{task_name}',
            '{old_stage_name}',
            '{new_stage_name}',
            '{year}',
            '{announcement_title}',
            '{branch_name}',
            '{support_user_name}',
            '{meeting_title}',
            '{meeting_date}',
            '{meeting_time}',
            '{award_date}',
            '{holiday_title}',
            '{holiday_date}',
            '{event_title}',
            '{event_start_date}',
            '{event_end_date}',
            '{company_policy_name}',
            '{budget_period}',
            '{budget_year}',
            '{budget_name}',
            '{revenue_amount}',
            '{vendor_name}',
            '{payment_type}',
            '{bill_due_date}',
            '{bill_date}',
            '{project_user}',
            '{project_start_date}',
            '{project_end_date}',
            '{hours}',
            '{task_user}',
            '{task_start_date}',
            '{task_end_date}',
            '{invoice_payment_method}',

        ];
        $arrValue = [
            'app_name' => '-',
            'company_name' => '-',
            'app_url' => '-',
            'email' => '-',
            'password' => '-',
            'client_name' => '-',
            'client_email' => '-',
            'client_password' => '-',
            'support_name' => '-',
            'support_title' => '-',
            'support_priority' => '-',
            'support_end_date' => '-',
            'support_description' => '-',
            'lead_name' => '-',
            'lead_email' => '-',
            'lead_subject' => '-',
            'lead_pipeline' => '-',
            'lead_stage' => '-',
            'deal_name' => '-',
            'deal_pipeline' => '-',
            'deal_stage' => '-',
            'deal_status' => '-',
            'deal_price' => '-',
            'award_name' => '-',
            'award_email' => '-',
            'customer_name' => '-',
            'customer_email' => '-',
            'invoice_name' => '-',
            'invoice_number' => '-',
            'invoice_url' => '-',
            'invoice_payment_name' => '-',
            'invoice_payment_amount' => '-',
            'invoice_payment_date' => '-',
            'payment_dueAmount' => '-',
            'payment_reminder_name' => '-',
            'invoice_payment_number' => '-',
            'invoice_payment_dueAmount' => '-',
            'payment_reminder_date' => '-',
            'payment_name' => '-',
            'payment_bill' => '-',
            'payment_amount' => '-',
            'payment_date' => '-',
            'payment_method' => '-',
            'vender_name' => '-',
            'vender_email' => '-',
            'bill_name' => '-',
            'bill_number' => '-',
            'bill_url' => '-',
            'proposal_name' => '-',
            'proposal_number' => '-',
            'proposal_url' => '-',
            'complaint_name' => '-',
            'complaint_title' => '-',
            'complaint_against' => '-',
            'complaint_date' => '-',
            'complaint_description' => '-',
            'leave_name' => '-',
            'leave_status' => '-',
            'leave_reason' => '-',
            'leave_start_date' => '-',
            'leave_end_date' => '-',
            'total_leave_days' => '-',
            'employee_name' => '-',
            'employee_email' => '-',
            'payslip_name' => '-',
            'payslip_salary_month' => '-',
            'payslip_url' => '-',
            'promotion_designation' => '-',
            'promotion_title' => '-',
            'promotion_date' => '-',
            'resignation_email' => '-',
            'assign_user' => '-',
            'resignation_date' => '-',
            'notice_date' => '-',
            'termination_name' => '-',
            'termination_email' => '-',
            'termination_date' => '-',
            'termination_type' => '-',
            'transfer_name' => '-',
            'transfer_email' => '-',
            'transfer_date' => '-',
            'transfer_department' => '-',
            'transfer_branch' => '-',
            'transfer_description' => '-',
            'trip_name' => '-',
            'purpose_of_visit' => '-',
            'start_date' => '-',
            'end_date' => '-',
            'place_of_visit' => '-',
            'trip_description' => '-',
            'vender_bill_name' => '-',
            'vender_bill_number' => '-',
            'vender_bill_url' => '-',
            'employee_warning_name' => '-',
            'warning_subject' => '-',
            'warning_description' => '-',
            'contract_client' => '-',
            'contract_subject' => '-',
            'contract_start_date' => '-',
            'contract_end_date' => '-',
            'user_name' => '-',
            'lead_user_name' => '-',
            'project_name' => '-',
            'payment_price' => '-',
            'invoice_payment_type' => '-',
            'task_name' => '-',
            'old_stage_name' => '-',
            'new_stage_name' => '-',
            'year' => '-',
            'announcement_title' => '-',
            'branch_name' => '-',
            'support_user_name' => '-',
            'meeting_title' => '-',
            'meeting_date' => '-',
            'meeting_time' => '-',
            'award_date' => '-',
            'holiday_title' => '-',
            'holiday_date' => '-',
            'event_title' => '-',
            'event_start_date' => '-',
            'event_end_date' => '-',
            'company_policy_name' => '-',
            'budget_period' => '-',
            'budget_year' => '-',
            'budget_name' => '-',
            'revenue_amount' => '-',
            'vendor_name' => '-',
            'payment_type' => '-',
            'bill_due_date' => '-',
            'bill_date' => '-',
            'project_user' => '',
            'project_start_date' => '',
            'project_end_date' => '',
            'hours' => '',
            'task_user' => '',
            'task_start_date' => '',
            'task_end_date' => '',
            'invoice_payment_method' => '-',
         ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        $settings = Utility::settings();
        $company_name = $settings['company_name'];

        $arrValue['app_name'] = !empty($company_name) ? $company_name : env('APP_NAME');
        $arrValue['company_name'] = self::settings()['mail_from_name'];
        $arrValue['app_url'] = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    public static function pipeline_lead_deal_Stage($created_id)
    {
        $pipeline = Pipeline::create(
            [
                'name' => 'Sales',
                'created_by' => $created_id,
            ]
        );
        $stages = [
            'Draft',
            'Sent',
            'Open',
            'Revised',
            'Declined',
        ];
        foreach ($stages as $stage) {
            LeadStage::create(
                [
                    'name' => $stage,
                    'pipeline_id' => $pipeline->id,
                    'created_by' => $created_id,
                ]
            );
            Stage::create(
                [
                    'name' => $stage,
                    'pipeline_id' => $pipeline->id,
                    'created_by' => $created_id,
                ]
            );
        }

    }

    public static function project_task_stages($created_id)
    {
        $projectStages = [
            'To Do',
            'In Progress',
            'Review',
            'Done',
        ];
        foreach ($projectStages as $key => $stage) {
            TaskStage::create(
                [
                    'name' => $stage,
                    'order' => $key,
                    'created_by' => $created_id,
                ]
            );
        }
    }

    public static function labels($created_id)
    {
        $stages = [
            [
                'name' => 'On Hold',
                'color' => 'primary',
            ],
            [
                'name' => 'New',
                'color' => 'info',
            ],
            [
                'name' => 'Pending',
                'color' => 'warning',
            ],
            [
                'name' => 'Loss',
                'color' => 'danger',
            ],
            [
                'name' => 'Win',
                'color' => 'success',
            ],
        ];
        foreach ($stages as $stage) {
            Label::create(
                [
                    'name' => $stage['name'],
                    'color' => $stage['color'],
                    'pipeline_id' => 1,
                    'created_by' => $created_id,
                ]
            );
        }
        $bugStatus = [
            'Confirmed',
            'Resolved',
            'Unconfirmed',
            'In Progress',
            'Verified',
        ];
        foreach ($bugStatus as $status) {
            BugStatus::create(
                [
                    'title' => $status,
                    'created_by' => $created_id,
                ]
            );
        }
    }

    public static function sources($created_id)
    {
        $stages = [
            'Websites',
            'Facebook',
            'Naukari.com',
            'Phone',
            'LinkedIn',
        ];
        foreach ($stages as $stage) {
            Source::create(
                [
                    'name' => $stage,
                    'created_by' => $created_id,
                ]
            );
        }
    }

    public static function employeeNumber($user_id)
    {
        $latest = Employee::where('created_by', $user_id)->latest()->first();

        if (!$latest) {
            return 1;
        }

        return $latest->employee_id + 1;
    }

    public static function employeeDetails($user_id, $created_by)
    {
        $user = User::where('id', $user_id)->first();

        $employee = Employee::create(
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'employee_id' => Utility::employeeNumber($created_by),
                'created_by' => $created_by,
            ]
        );
    }

    public static function employeeDetailsUpdate($user_id, $created_by)
    {
        $user = User::where('id', $user_id)->first();

        $employee = Employee::where('user_id', $user->id)->update(
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );

    }

    public static function jobStage($id)
    {
        $stages = [
            'Applied',
            'Phone Screen',
            'Interview',
            'Hired',
            'Rejected',
        ];
        foreach ($stages as $stage) {

            JobStage::create(
                [
                    'title' => $stage,
                    'created_by' => $id,
                ]
            );
        }
    }

    public static function errorFormat($errors)
    {
        $err = '';

        foreach ($errors->all() as $msg) {
            $err .= $msg . '<br>';
        }

        return $err;
    }

    // get date formated
    public static function getDateFormated($date)
    {
        if (!empty($date) && $date != '0000-00-00') {
            return date("d M Y", strtotime($date));
        } else {
            return '';
        }
    }

    // get progress bar color
    public static function getProgressColor($percentage)
    {
        $color = '';

        if ($percentage <= 20) {
            $color = 'danger';
        } elseif ($percentage > 20 && $percentage <= 40) {
            $color = 'warning';
        } elseif ($percentage > 40 && $percentage <= 60) {
            $color = 'info';
        } elseif ($percentage > 60 && $percentage <= 80) {
            $color = 'primary';
        } elseif ($percentage >= 80) {
            $color = 'success';
        }

        return $color;
    }

    // Return Percentage from two value
    public static function getPercentage($val1 = 0, $val2 = 0)
    {
        $percentage = 0;
        if ($val1 > 0 && $val2 > 0) {
            $percentage = intval(($val1 / $val2) * 100);
        }

        return $percentage;
    }

    // For crm dashboard
    public static function getCrmPercentage($val1 = 0, $val2 = 0)
    {
        $percentage = 0;
        if ($val1 > 0 && $val2 > 0) {
            $percentage = ($val1 / $val2) * 100;
            $percentage = number_format($percentage, \Utility::getValByName('decimal_number'));
        }

        return $percentage;
    }

    public static function timeToHr($times)
    {
        $totaltime = self::calculateTimesheetHours($times);
        $timeArray = explode(':', $totaltime);
        if ($timeArray[1] <= '30') {
            $totaltime = $timeArray[0];
        }
        $totaltime = $totaltime != '00' ? $totaltime : '0';

        return $totaltime;
    }

    public static function calculateTimesheetHours($times)
    {
        $minutes = 0;
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // Return Last 7 Days with date & day name
    public static function getLastSevenDays()
    {
        $arrDuration = [];
        $previous_week = strtotime("-1 week +1 day");

        for ($i = 0; $i < 7; $i++) {
            $arrDuration[date('Y-m-d', $previous_week)] = date('D', $previous_week);
            $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
        }

        return $arrDuration;
    }

    // Check File is exist and delete these
    public static function checkFileExistsnDelete(array $files)
    {
        $status = false;
        foreach ($files as $key => $file) {
            if (Storage::exists($file)) {
                $status = Storage::delete($file);
            }
        }

        return $status;
    }

    // get project wise currency formatted amount
    public static function projectCurrencyFormat($project_id, $amount, $decimal = false)
    {
        $project = Project::find($project_id);
        if (empty($project)) {
            $settings = Utility::settings();

            return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
        }

    }

    // Return Week first day and last day
    public static function getFirstSeventhWeekDay($week = null)
    {
        $first_day = $seventh_day = null;
        if (isset($week)) {
            // $first_day = Carbon::now()->addWeeks($week)->startOfWeek();
            // $seventh_day = Carbon::now()->addWeeks($week)->endOfWeek();
            $week = (int) $week;
            if ($week >= 0) {
                $first_day = Carbon::now()->addWeeks($week)->startOfWeek();
                $seventh_day = Carbon::now()->addWeeks($week)->endOfWeek();
            } else {
                $first_day = Carbon::now()->subWeeks(abs($week))->startOfWeek();
                $seventh_day = Carbon::now()->subWeeks(abs($week))->endOfWeek();
            }
        }
        $dateCollection['first_day'] = $first_day;
        $dateCollection['seventh_day'] = $seventh_day;
        $period = CarbonPeriod::create($first_day, $seventh_day);
        foreach ($period as $key => $dateobj) {
            $dateCollection['datePeriod'][$key] = $dateobj;
        }

        return $dateCollection;
    }

    public static function employeePayslipDetail($employeeId, $month)
    {
        // allowance
        $earning['allowance'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalAllowance = 0;

        $arrayJson = json_decode($earning['allowance']);
        foreach ($arrayJson as $earn) {
            $allowancejson = json_decode($earn->allowance);
            foreach ($allowancejson as $allowances) {
                if ($allowances->type == 'percentage') {
                    $empall = $allowances->amount * $earn->basic_salary / 100;
                } else {
                    $empall = $allowances->amount;
                }
                $totalAllowance += $empall;
            }
        }

        // commission
        $earning['commission'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalCommission = 0;

        $arrayJson = json_decode($earning['commission']);

        foreach ($arrayJson as $earn) {
            $commissionjson = json_decode($earn->commission);

            foreach ($commissionjson as $commissions) {

                if ($commissions->type == 'percentage') {
                    $empcom = $commissions->amount * $earn->basic_salary / 100;
                } else {
                    $empcom = $commissions->amount;
                }
                $totalCommission += $empcom;
            }
        }

        // otherpayment
        $earning['otherPayment'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalotherpayment = 0;

        $arrayJson = json_decode($earning['otherPayment']);

        foreach ($arrayJson as $earn) {
            $otherpaymentjson = json_decode($earn->other_payment);

            foreach ($otherpaymentjson as $otherpay) {
                if ($otherpay->type == 'percentage') {
                    $empotherpay = $otherpay->amount * $earn->basic_salary / 100;
                } else {
                    $empotherpay = $otherpay->amount;
                }
                $totalotherpayment += $empotherpay;
            }
        }

        //overtime
        $earning['overTime'] = Payslip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $ot = 0;

        $arrayJson = json_decode($earning['overTime']);
        foreach ($arrayJson as $overtime) {
            $overtimes = json_decode($overtime->overtime);
            foreach ($overtimes as $overt) {
                $OverTime = $overt->number_of_days * $overt->hours * $overt->rate;
                $ot += $OverTime;
            }
        }

        // loan
        $deduction['loan'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalloan = 0;

        $arrayJson = json_decode($deduction['loan']);

        foreach ($arrayJson as $loan) {
            $loans = json_decode($loan->loan);

            foreach ($loans as $emploans) {

                if ($emploans->type == 'percentage') {
                    $emploan = $emploans->amount * $loan->basic_salary / 100;
                } else {
                    $emploan = $emploans->amount;
                }
                $totalloan += $emploan;
            }
        }

        // saturation_deduction
        $deduction['deduction'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totaldeduction = 0;

        $arrayJson = json_decode($deduction['deduction']);

        foreach ($arrayJson as $deductions) {
            $deduc = json_decode($deductions->saturation_deduction);
            foreach ($deduc as $deduction_option) {
                if ($deduction_option->type == 'percentage') {
                    $empdeduction = $deduction_option->amount * $deductions->basic_salary / 100;
                } else {
                    $empdeduction = $deduction_option->amount;
                }
                $totaldeduction += $empdeduction;
            }
        }

        $payslip['earning'] = $earning;
        $payslip['totalEarning'] = $totalAllowance + $totalCommission + $totalotherpayment + $ot;
        $payslip['deduction'] = $deduction;
        $payslip['totalDeduction'] = $totalloan + $totaldeduction;

        return $payslip;
    }

    public static function companyData($company_id, $string)
    {
        $setting = DB::table('settings')->where('created_by', $company_id)->where('name', $string)->first();
        if (!empty($setting)) {
            return $setting->value;
        } else {
            return '';
        }
    }

    public static function addNewData()
    {
        \Artisan::call('cache:forget spatie.permission.cache');
        \Artisan::call('cache:clear');
        $usr = \Auth::user();

        $arrPermissions = [
            'manage form builder',
            'create form builder',
            'edit form builder',
            'delete form builder',
            'manage form field',
            'create form field',
            'edit form field',
            'delete form field',
            'view form response',
            'manage performance type',
            'create performance type',
            'edit performance type',
            'delete performance type',
            'manage budget plan',
            'create budget plan',
            'edit budget plan',
            'delete budget plan',
            'view budget plan',
            'stock report',
            'manage warehouse',
            'create warehouse',
            'edit warehouse',
            'show warehouse',
            'delete warehouse',
            'manage purchase',
            'create purchase',
            'edit purchase',
            'show purchase',
            'delete purchase',
            'send purchase',
            'create payment purchase',
            'manage pos',
            'manage contract type',
            'create contract type',
            'edit contract type',
            'delete contract type',
            'create barcode',
            'show crm dashboard',
            'share project',
            'show pos dashboard',
            'create webhook',
            'edit webhook',
            'delete webhook',
            'manage quotation',
            'create quotation',
            'edit quotation',
            'delete quotation',
            'show quotation',
            'convert quotation',
            'show pos',
            'manage zoom meeting',
            'create zoom meeting',
            'show zoom meeting',
            'delete zoom meeting',
            'manage biometric attendance',
            'biometric attendance synchronize',
            'manage visa',
            'create visa',
            'edit visa',
            'delete visa',
            'show visa',

        ];
        foreach ($arrPermissions as $ap) {
            // check if permission is not created then create it.
            $permission = Permission::where('name', 'LIKE', $ap)->first();
            if (empty($permission)) {
                Permission::create(['name' => $ap]);
            }
        }
        $companyRole = Role::where('name', 'LIKE', 'company')->first();

        $companyPermissions = $companyRole->getPermissionNames()->toArray();
        $companyNewPermission = [
            'manage form builder',
            'create form builder',
            'edit form builder',
            'delete form builder',
            'manage form field',
            'create form field',
            'edit form field',
            'delete form field',
            'view form response',
            'manage performance type',
            'create performance type',
            'edit performance type',
            'delete performance type',
            'manage budget plan',
            'create budget plan',
            'edit budget plan',
            'delete budget plan',
            'view budget plan',
            'stock report',
            'manage warehouse',
            'create warehouse',
            'edit warehouse',
            'show warehouse',
            'delete warehouse',
            'manage purchase',
            'create purchase',
            'edit purchase',
            'show purchase',
            'delete purchase',
            'send purchase',
            'create payment purchase',
            'manage pos',
            'manage contract type',
            'create contract type',
            'edit contract type',
            'delete contract type',
            'create barcode',
            'show crm dashboard',
            'show pos dashboard',
            'share project',
            'manage biometric attendance',
            'biometric attendance synchronize',
            'manage visa',
            'create visa',
            'edit visa',
            'delete visa',
            'show visa',
        ];
        foreach ($companyNewPermission as $op) {
            // check if permission is not assign to owner then assign.
            if (!in_array($op, $companyPermissions)) {
                $permission = Permission::findByName($op);
                $companyRole->givePermissionTo($permission);
            }
        }

    }

    public static function getAdminPaymentSetting()
    {

        $data = \DB::table('admin_payment_settings');

        $settings = [];
        if (\Auth::check()) {

            $user_id = 1;
            $data = $data->where('created_by', '=', $user_id);

        }
        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getCompanyPaymentSetting($user_id)
    {

        $data = \DB::table('company_payment_settings');
        $settings = [];
        $data = $data->where('created_by', '=', $user_id);
        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getCompanyPayment()
    {

        $data = \DB::table('company_payment_settings');
        $settings = [];
        if (\Auth::check()) {
            $user_id = \Auth::user()->creatorId();
            $data = $data->where('created_by', '=', $user_id);

        }
        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function error_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "error" : $msg;
        $msg_id = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }

    public static function success_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "success" : $msg;
        $msg_id = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    public static function get_messenger_packages_migration()
    {
        $totalMigration = 0;
        $messengerPath = glob(base_path() . '/vendor/munafio/chatify/src/database/migrations' . DIRECTORY_SEPARATOR . '*.php');
        if (!empty($messengerPath)) {
            $messengerMigration = str_replace('.php', '', $messengerPath);
            $totalMigration = count($messengerMigration);
        }

        return $totalMigration;

    }

    public static function getselectedThemeColor()
    {
        $color = env('THEME_COLOR');
        if ($color == "" || $color == null) {
            $color = 'blue';
        }

        return $color;
    }

    public static function getAllThemeColors()
    {
        $colors = [
            'blue',
            'denim',
            'sapphire',
            'olympic',
            'violet',
            'black',
            'cyan',
            'dark-blue-natural',
            'gray-dark',
            'light-blue',
            'light-purple',
            'magenta',
            'orange-mute',
            'pale-green',
            'rich-magenta',
            'rich-red',
            'sky-gray',
        ];

        return $colors;
    }

    public static function diffance_to_time($start, $end)
    {
        $start = new Carbon($start);
        $end = new Carbon($end);
        $totalDuration = $start->diffInSeconds($end);

        return $totalDuration;
    }

    public static function second_to_time($seconds = 0)
    {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = $seconds % 60;

        $time = sprintf("%02d:%02d:%02d", $H, $i, $s);

        return $time;
    }

    //Slack notification
    public static function send_slack_msg($slug, $obj, $user_id = null)
    {

        $notification_template = NotificationTemplates::where('slug', $slug)->first();

        if (!empty($notification_template) && !empty($obj)) {
            if (!empty($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = \Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }

        if (isset($msg)) {
            $settings = Utility::settingsById($user->id);
            try {
                if (isset($settings['slack_webhook']) && !empty($settings['slack_webhook'])) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $settings['slack_webhook']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $msg]));

                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    }
                    curl_close($ch);
                }
            } catch (\Exception $e) {
            }
        }
    }

    //Telegram Notification
    public static function send_telegram_msg($slug, $obj, $user_id = null)
    {
        $notification_template = NotificationTemplates::where('slug', $slug)->first();

        if (!empty($notification_template) && !empty($obj)) {
            if (!empty($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = \Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }

        if (isset($msg)) {
            $settings = Utility::settingsById($user->id);
            try {
                if (isset($settings['telegram_accestoken']) && !empty($settings['telegram_accestoken'])) {
                    // Set your Bot ID and Chat ID.
                    $telegrambot = $settings['telegram_accestoken'];
                    $telegramchatid = $settings['telegram_chatid'];
                    // Function call with your own text or variable
                    $url = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
                    $data = array(
                        'chat_id' => $telegramchatid,
                        'text' => $msg,
                    );
                    $options = array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                            'content' => http_build_query($data),
                        ),
                    );
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $url = $url;
                }
            } catch (\Exception $e) {
            }
        }

    }

    //Twilio Notification
    public static function send_twilio_msg($to, $slug, $obj, $user_id = null)
    {

        $notification_template = NotificationTemplates::where('slug', $slug)->first();

        if (!empty($notification_template) && !empty($obj)) {
            if (!empty($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = \Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }

        if (isset($msg)) {
            $settings = Utility::settingsById($user->id);
            $account_sid = $settings['twilio_sid'];
            $auth_token = $settings['twilio_token'];
            $twilio_number = $settings['twilio_from'];
            try {
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($to, [
                    'from' => $twilio_number,
                    'body' => $msg,
                ]);
            } catch (\Exception $e) {

            }
        }

    }

    //inventory management (Quantity)
    public static function total_quantity($type, $quantity, $product_id)
    {

        $product = ProductService::find($product_id);
        if (($product->type == 'product')) {
            $pro_quantity = $product->quantity;

            if ($type == 'minus') {
                $product->quantity = $pro_quantity - $quantity;
            } else {
                $product->quantity = $pro_quantity + $quantity;

            }
            $product->save();
        }

    }

    //quantity update in warehouse details
    public static function warehouse_quantity($type, $quantity, $product_id, $warehouse_id)
    {

        $product = WarehouseProduct::where('warehouse_id', $warehouse_id)->where('product_id', $product_id)->first();

        $pro_quantity = (!empty($product) && !empty($product->quantity)) ? $product->quantity : 0;

        if ($type == 'minus') {
            $product->quantity = $pro_quantity != 0 ? $pro_quantity - $quantity : $quantity;
        } else {
            $product->quantity = $pro_quantity + $quantity;

        }
        $product->quantity = !empty($product->quantity) ? $product->quantity : 0;

        $product->save();

    }

    //warehouse transfer
    public static function warehouse_transfer_qty($from_warehouse, $to_warehouse, $product_id, $quantity)
    {

        $toWarehouse = WarehouseProduct::where('warehouse_id', $to_warehouse)->where('product_id', $product_id)->first();
        if (empty($toWarehouse)) {
            $transfer = new WarehouseProduct();
            $transfer->warehouse_id = $to_warehouse;
            $transfer->product_id = $product_id;
            $transfer->quantity = $quantity;
            $transfer->created_by = \Auth::user()->creatorId();
            $transfer->save();
        } else {
            $toWarehouse->quantity = $toWarehouse->quantity + $quantity;
            $toWarehouse->save();
        }
        $fromWarehouse = WarehouseProduct::where('warehouse_id', $from_warehouse)->where('product_id', $product_id)->first();
        $fromWarehouse->quantity = ($fromWarehouse->quantity) - ($quantity);
        if ($fromWarehouse->quantity <= 0) {
            $fromWarehouse->delete();
        } else {
            $fromWarehouse->save();
        }

    }

    //add quantity in product stock
    public static function addProductStock($product_id, $quantity, $type, $description, $type_id)
    {

        $stocks = new StockReport();
        $stocks->product_id = $product_id;
        $stocks->quantity = $quantity;
        $stocks->type = $type;
        $stocks->type_id = $type_id;
        $stocks->description = $description;
        $stocks->created_by = \Auth::user()->creatorId();
        $stocks->save();
    }

    public static function mode_layout()
    {
        $data = DB::table('settings');

        if (\Auth::check()) {

            $data = $data->where('created_by', '=', \Auth::user()->creatorId())->get();
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }

        } else {

            $data->where('created_by', '=', 1);
            $data = $data->get();
        }

        $settings = [
            "cust_darklayout" => "off",
            "cust_theme_bg" => "on",
            "color" => '',
        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function colorset()
    {
        if (\Auth::check()) {
            $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->pluck('value', 'name')->toArray();
        } else {
            // 로그인 되어있지 않을 때(예: 로그인/비밀번호 재설정 화면),
            // company 타입 사용자가 아직 없으면 $user 가 null 이 되어 에러가 발생할 수 있으므로
            // 안전하게 기본 settings 로 폴백한다.
            $user = User::where('type', 'company')->first();

            if ($user) {
                $setting = DB::table('settings')
                    ->where('created_by', $user->id)
                    ->pluck('value', 'name')
                    ->toArray();
            } else {
                // 설정 레코드가 없을 때는 전역 기본 설정으로 대체
                $setting = Utility::settings();
            }
        }

        if (!isset($setting['color'])) {
            $setting = Utility::settings();
        }
        return $setting;
    }

    public static function get_superadmin_logo()
    {
        $is_dark_mode = self::getValByName('cust_darklayout');
        $setting = DB::table('settings')->where('created_by', Auth::user()->id)->pluck('value', 'name')->toArray();
        if (!empty($setting['cust_darklayout'])) {
            $is_dark_mode = $setting['cust_darklayout'];
            if ($is_dark_mode == 'on') {
                return 'logo-light.png';
            } else {
                return 'logo-dark.png';
            }

        } else {
            return 'logo-dark.png';
        }

    }

    public static function GetLogo()
    {
        $setting = Utility::colorset();

        if (\Auth::user() && \Auth::user()->type != 'super admin') {

            if (Utility::getValByName('cust_darklayout') == 'on') {

                return Utility::getValByName('company_logo_light');
            } else {
                return Utility::getValByName('company_logo_dark');
            }
        } else {
            if (Utility::getValByName('cust_darklayout') == 'on') {

                return Utility::getValByName('light_logo');
            } else {
                return Utility::getValByName('dark_logo');
            }
        }
    }

    public static function getGdpr()
    {
        $data = DB::table('settings');
        if (\Auth::check()) {
            $data = $data->where('created_by', '=', 1);
        } else {
            $data = $data->where('created_by', '=', 1);
        }
        $data = $data->get();
        $settings = [
            "gdpr_cookie" => "",
            "cookie_text" => "",
        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function getValByName1($key)
    {
        $setting = Utility::getGdpr();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }
        return $setting[$key];
    }

    //add quantity in warehouse stock
    public static function addWarehouseStock($product_id, $quantity, $warehouse_id)
    {

        $product = WarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();
        if ($product) {
            $pro_quantity = $product->quantity;
            $product_quantity = $pro_quantity + $quantity;
        } else {
            $product_quantity = $quantity;
        }

        $data = WarehouseProduct::updateOrCreate(
            ['warehouse_id' => $warehouse_id, 'product_id' => $product_id, 'created_by' => \Auth::user()->id],
            ['warehouse_id' => $warehouse_id, 'product_id' => $product_id, 'quantity' => $product_quantity, 'created_by' => \Auth::user()->id])
        ;

    }

    public static function starting_number($id, $type)
    {

        if ($type == 'invoice') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'invoice_starting_number')->update(array('value' => $id));
        } elseif ($type == 'proposal') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'proposal_starting_number')->update(array('value' => $id));
        } elseif ($type == 'bill') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'bill_starting_number')->update(array('value' => $id));
        }

        return $data;
    }

    //  Start Storage Setting

    public static function upload_file($request, $key_name, $name, $path, $custom_validation = [])
    {
        try {
            $settings = Utility::getStorageSetting();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {

                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '20480000000';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $request->$key_name;

                if (count($custom_validation) > 0) {

                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];

                }

                $validator = \Validator::make($request->all(), [
                    $key_name => $validation,
                ]);

                if ($validator->fails()) {

                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];

                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {
                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path . $name;
                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );


                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {

            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    //only employee edit storage setting upload_coustom_file function

    public static function upload_coustom_file($request, $key_name, $name, $path, $data_key, $custom_validation = [])
    {

        try {
            $settings = Utility::getStorageSetting();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $request->$key_name;

                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];

                }
                $validator = \Validator::make($request->all(), [
                    $name => $validation,
                ]);

                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {

                        \Storage::disk()->putFileAs(
                            $path,
                            $request->file($key_name)[$data_key],
                            $name
                        );

                        $path = $name;
                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $request->file($key_name)[$data_key],
                            $name
                        );


                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $request->file($key_name)[$data_key],
                            $name
                        );

                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function get_file($path)
    {
        $settings = Utility::getStorageSetting();

        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                    ]
                );
            } elseif ($settings['storage_setting'] == 's3') {
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function getStorageSetting()
    {

        $data = Utility::getSetting();
        $settings = [
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",

        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    //  End Storage Setting

    public static function getTargetrating($designationid, $competencyCount)
    {
        if (self::$ratingData == null) {
            $indicator = Indicator::where('designation', $designationid)->first();

            if (!empty($indicator->rating) && ($competencyCount != 0)) {
                $rating = json_decode($indicator->rating, true);
                $starsum = array_sum($rating);

                $overallrating = $starsum / $competencyCount;
            } else {
                $overallrating = 0;
            }

            self::$ratingData = $overallrating;
        }

        return self::$ratingData;
    }

    //start Google Calendar
    public static function colorCodeData($type)
    {
        if ($type == 'event') {
            return 1;
        } elseif ($type == 'zoom_meeting') {
            return 2;
        } elseif ($type == 'task') {
            return 3;
        } elseif ($type == 'appointment') {
            return 11;
        } elseif ($type == 'rotas') {
            return 3;
        } elseif ($type == 'holiday') {
            return 4;
        } elseif ($type == 'call') {
            return 10;
        } elseif ($type == 'meeting') {
            return 5;
        } elseif ($type == 'leave') {
            return 6;
        } elseif ($type == 'work_order') {
            return 7;
        } elseif ($type == 'lead') {
            return 7;
        } elseif ($type == 'deal') {
            return 8;
        } elseif ($type == 'interview_schedule') {
            return 9;
        } else {
            return 11;
        }

    }

    public static $colorCode = [
        1 => 'event-warning',
        2 => 'event-secondary',
        3 => 'event-info',
        4 => 'event-warning',
        5 => 'event-danger',
        6 => 'event-dark',
        7 => 'event-black',
        8 => 'event-info',
        9 => 'event-dark',
        10 => 'event-success',
        11 => 'event-warning',

    ];

    public static function googleCalendarConfig()
    {
        $setting = Utility::settings();
        $path = storage_path($setting['google_calender_json_file']);
        config([
            'google-calendar.default_auth_profile' => 'service_account',
            'google-calendar.auth_profiles.service_account.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.token_json' => $path,
            'google-calendar.calendar_id' => isset($setting['google_clender_id']) ? $setting['google_clender_id'] : '',
            'google-calendar.user_to_impersonate' => '',

        ]);
    }

    public static function addCalendarData($request, $type)
    {
        Self::googleCalendarConfig();
        $event = new GoogleEvent();
        $event->name = $request->title;
        $event->startDateTime = Carbon::parse($request->start_date);
        $event->endDateTime = Carbon::parse($request->end_date);
        $event->colorId = Self::colorCodeData($type);
        $event->save();
    }

    public static function getCalendarData($type)
    {

        Self::googleCalendarConfig();
        $data = GoogleEvent::get();

        $type = Self::colorCodeData($type);
        $arrayJson = [];
        foreach ($data as $val) {
            $end_date = date_create($val->endDateTime);
            date_add($end_date, date_interval_create_from_date_string("1 days"));
            if ($val->colorId == "$type") {

                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->summary,
                    "start" => $val->startDateTime,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => Self::$colorCode[$type],
                    "allDay" => true,

                ];
            }
        }

        return $arrayJson;
    }

    //end Google Calendar

    public static function getSeoSetting()
    {
        $data = \DB::table('settings')->whereIn('name', ['meta_title', 'meta_desc', 'meta_image'])->get();
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;

    }

    public static function webhookSetting($module, $user_id = null)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
        } else {
            $user = \Auth::user();
        }
        $webhook = WebhookSetting::where('module', $module)->where('created_by', '=', $user->id)->first();
        if (!empty($webhook)) {
            $url = $webhook->url;
            $method = $webhook->method;
            $reference_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $data['method'] = $method;
            $data['reference_url'] = $reference_url;
            $data['url'] = $url;
            return $data;
        }
        return false;
    }
    public static function WebhookCall($url = null, $parameter = null, $method = 'POST')
    {
        if (!empty($url) && !empty($parameter)) {
            try {

                $curlHandle = curl_init($url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                $curlResponse = curl_exec($curlHandle);
                curl_close($curlHandle);
                if (empty($curlResponse)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }

    //start for cookie settings
    public static function getCookieSetting()
    {

        $data = \DB::table('settings')->whereIn('name', ['enable_cookie', 'cookie_logging', 'cookie_title',
            'cookie_description', 'necessary_cookies', 'strictly_cookie_title',
            'strictly_cookie_description', 'more_information_description', 'contactus_url'])->get();
        $settings = [

            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'contactus_url' => '#',

        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {
            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }

        }
    }

    //end for cookie settings

    //for AI module
    public static function flagOfCountry()
    {
        $arr = [
            'ar' => '🇦🇪 ar',
            'zh' => '🇨🇳 zh',
            'da' => '🇩🇰 da',
            'de' => '🇩🇪 de',
            'es' => '🇪🇸 es',
            'fr' => '🇫🇷 fr',
            'he' => '🇮🇱 he',
            'it' => '🇮🇹 it',
            'ja' => '🇯🇵 ja',
            'nl' => '🇳🇱 nl',
            'pl' => '🇵🇱 pl',
            'ru' => '🇷🇺 ru',
            'pt' => '🇵🇹 pt',
            'en' => '🇮🇳 en',
            'tr' => '🇹🇷 tr',
            'pt-br' => '🇵🇹 pt-br',
        ];
        return $arr;
    }

    public static function langList()
    {
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Turkish",
            "pt-br" => "Portuguese (Brazil)",
        ];
        return $languages;
    }

    public static function languagecreate()
    {
        $languages = Utility::langList();

        foreach ($languages as $key => $lang) {
            $languageExist = Language::where('code', $key)->first();
            if (empty($languageExist)) {
                $language = new Language();
                $language->code = $key;
                $language->full_name = $lang;
                $language->save();
            }
        }
    }

    public static function langSetting()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1)->get();
        if (count($data) == 0) {
            $data = DB::table('settings')->where('created_by', '=', 1)->get();
        }
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    //start for chartOfAccount data show

    public static function getAccountBalance($account_id, $start_date = null, $end_date = null)
    {

        if (!empty($start_date) && !empty($end_date)) {
            $start = $start_date;
            $end = $end_date;
        } else {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }


        // foreach ($types as $type) {
        $total = AddTransactionLine::
            select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name',
            \DB::raw('sum(debit) as totalDebit'),
            \DB::raw('sum(credit) as totalCredit'));
        $total->leftjoin('chart_of_accounts', 'add_transaction_lines.account_id', 'chart_of_accounts.id');
        $total->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');
        $total->where('add_transaction_lines.created_by', \Auth::user()->creatorId());
        $total->where('add_transaction_lines.account_id', $account_id);
        $total->where('add_transaction_lines.date', '>=', $start);
        $total->where('add_transaction_lines.date', '<=', $end);
        $total->groupBy('account_id');
        $total = $total->get()->toArray();

        $balance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($total as $key => $record) {
            $totalDebit = $record['totalDebit'];
            $totalCredit = $record['totalCredit'];

        }

        $balance += $totalCredit - $totalDebit;

        return $balance;

    }

    public static function getAccountData($account_id, $start_date = null, $end_date = null)
    {
        if (!empty($start_date) && !empty($end_date)) {
            $start = $start_date;
            $end = $end_date;
        } else {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }

        $transactionData = DB::table('add_transaction_lines')
            ->where('add_transaction_lines.created_by', \Auth::user()->creatorId())
            ->where('add_transaction_lines.account_id', $account_id)
            ->whereBetween('add_transaction_lines.date', [$start, $end])
            ->leftJoin('invoices', function ($join) {
                $join->on('add_transaction_lines.reference_id', '=', 'invoices.id')
                    ->whereIn('add_transaction_lines.reference', ['Invoice Payment', 'Invoice']);
            })
             ->leftJoin('purchases', function ($join) {
                $join->on('add_transaction_lines.reference_id', '=', 'purchases.id')
                    ->whereIn('add_transaction_lines.reference', ['Purchase Payment', 'Purchase']);
            })
            ->leftJoin('bills', function ($join) {
                $join->on('add_transaction_lines.reference_id', '=', 'bills.id')
                    ->whereIn('add_transaction_lines.reference', ['Bill', 'Bill Payment', 'Bill Account']);
            })
            ->leftJoin('revenues', function ($join) {
                $join->on('add_transaction_lines.reference_id', '=', 'revenues.id')
                    ->whereIn('add_transaction_lines.reference', ['Revenue']);
            })
            ->leftJoin('payments', function ($join) {
                $join->on('add_transaction_lines.reference_id', '=', 'payments.id')
                    ->whereIn('add_transaction_lines.reference', ['Payment']);
            })
            ->leftJoin('customers as invoice_customer', 'invoices.customer_id', '=', 'invoice_customer.id')
            ->leftJoin('customers as revenue_customer', 'revenues.customer_id', '=', 'revenue_customer.id')
            ->leftJoin('venders as bill_vendor', 'bills.vender_id', '=', 'bill_vendor.id')
            ->leftJoin('venders as payment_vendor', 'payments.vender_id', '=', 'payment_vendor.id')
            ->leftJoin('chart_of_accounts', 'add_transaction_lines.account_id', '=', 'chart_of_accounts.id')
            ->select(
                'add_transaction_lines.*',
                'invoice_customer.name as invoice_customer_name',
                'revenue_customer.name as revenue_customer_name',
                'bill_vendor.name as bill_vendor_name',
                'payment_vendor.name as payment_vendor_name',
                'chart_of_accounts.name as account_name',
                DB::raw("COALESCE(invoice_customer.name, revenue_customer.name,bill_vendor.name,payment_vendor.name) as user_name"),
            )->get();

        return $transactionData;

    }

    //end for chartOfAccount data show

    public static function getPusherSetting()
    {
        $settings = Utility::settings();
        if ($settings) {
            config([
                'chatify.pusher.key' => isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '',
                'chatify.pusher.secret' => isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '',
                'chatify.pusher.app_id' => isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '',
                'chatify.pusher.options.cluster' => isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '',
            ]);
            return $settings;
        }
    }

    public static function addOnlinePaymentData($payment , $invoice , $accountId , $payment_type)
    {
        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name',$payment_type)->first();

        $get_account = ChartOfAccount::find($account->chart_account_id);

        $data = [

            'account_id'         => !empty($get_account)? $get_account->id : 0,
            'bank_id'            => $payment->bank_id,
            'transaction_type'   => 'debit',
            'transaction_amount' => $payment->amount,
            'reference'          => 'Invoice-Payment-Bank',
            'reference_id'       => $invoice->id,
            'reference_sub_id'   => $payment->id,
            'customer_id'        => $invoice->customer_id,
            'vender_id'          => $invoice->vender_id,
            'date'               => $payment->date,
            'created_by'         => $invoice->created_by
        ];
        self::addTransactionLines($data);

        $account = ChartOfAccount::where('name','Accounts Receivable')->where('created_by' , $invoice->created_by)->first();
        $data    = [
            'account_id'         => !empty($account) ? $account->id : 0,
            'bank_id'            => $payment->bank_id,
            'transaction_type'   => 'credit',
            'transaction_amount' => $payment->amount,
            'reference'          => 'Invoice-Payment-AR',
            'reference_id'       => $invoice->id,
            'customer_id'        => $invoice->customer_id,
            'vender_id'          => $invoice->vender_id,
            'reference_sub_id'   => $payment->id,
            'date'               => $payment->date,
            'created_by'         => $invoice->created_by
        ];
        self::addTransactionLines($data);
    }

    public static function addTransactionLines($data , $action = '' , $type = '')
    {
        if($type == 'notes')
        {
            $existingTransaction = AddTransactionLine::where('reference', $data['reference'])
            ->where('reference_id', $data['reference_id'])
            ->where('reference_sub_id', $data['reference_sub_id'])
            ->first();
        }
        else
        {
            $existingTransaction = AddTransactionLine::where('account_id', $data['account_id'])
                ->where('reference', $data['reference'])
                ->where('reference_id', $data['reference_id'])
                ->where('reference_sub_id', $data['reference_sub_id'])
                ->first();
        }

        if ($existingTransaction && $action == 'edit') {
            $transactionLines = $existingTransaction;
        } else {
            $transactionLines = new  AddTransactionLine();
        }

        $transactionLines->account_id       = $data['account_id'];
        $transactionLines->bank_id          = $data['bank_id'];
        $transactionLines->reference        = $data['reference'];
        $transactionLines->reference_id     = $data['reference_id'];
        $transactionLines->reference_sub_id = $data['reference_sub_id'];
        $transactionLines->customer_id      = $data['customer_id'];
        $transactionLines->vender_id        = $data['vender_id'];
        $transactionLines->date             = $data['date'];

        if ($data['transaction_type'] == "credit") {
            $transactionLines->credit = $data['transaction_amount'];
            $transactionLines->debit  = 0;
        } else {
            $transactionLines->credit = 0;
            $transactionLines->debit  = $data['transaction_amount'];
        }

        $transactionLines->created_by = $data['created_by'] ?? \Auth::user()->creatorId();
        $transactionLines->save();
    }

    public static $invoiceProductsData = null;
    public static $billProductsData = null;

    public static function getInvoiceProductsData($fromDate, $toDate)
    {
        if (self::$invoiceProductsData === null) {
            $taxData = Utility::getTaxData();

            $InvoiceProducts = \DB::table('invoice_products')
                ->select('invoice_products.invoice_id as invoice',
                    \DB::raw('SUM(quantity) as total_quantity'),
                    \DB::raw('SUM(discount) as total_discount'),
                    \DB::raw('SUM(price + discount) as sub_total'))
                ->selectRaw('0 as tax_values')
                ->leftJoin('invoices', 'invoice_products.invoice_id', 'invoices.id')
                ->where('issue_date', '>=', $fromDate)->where('issue_date', '<=', $toDate)
                ->where('invoices.created_by', \Auth::user()->creatorId())
                ->groupBy('invoice')
                ->get()
                ->keyBy('invoice');

            $InvoiceProducts->map(function ($invoice) {
                $invoice->total = $invoice->sub_total + $invoice->tax_values - $invoice->total_discount;
                return $invoice;
            });

            $total = 0;
            foreach ($InvoiceProducts as $invoice) {
                $total += ($invoice->total);
            }
            self::$invoiceProductsData = $total;
        }

        return self::$invoiceProductsData;
    }

    public static function getBillProductsData($fromDate, $toDate)
    {
        if (self::$billProductsData === null) {
            $taxData = Utility::getTaxData();
            $BillProducts = \DB::table('bill_products')
                ->select('bill_products.bill_id as bill',
                    \DB::raw('SUM(quantity) as total_quantity'),
                    \DB::raw('SUM(discount) as total_discount'),
                    \DB::raw('SUM(bill_products.price * quantity)  as sub_total'))
                ->selectRaw('(SELECT SUM(bill_accounts.price) FROM bill_accounts
                    WHERE bill_accounts.ref_id = bills.id) as acc_price')
                ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
                                        LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
                                        WHERE bill_products.bill_id = bills.id) as tax_values')
                ->leftJoin('bills', 'bill_products.bill_id', 'bills.id')
                ->where('bill_date', '>=', $fromDate)->where('bill_date', '<=', $toDate)
                ->where('bills.created_by', \Auth::user()->creatorId())
                ->groupBy('bill')
                ->get()
                ->keyBy('bill');

            $BillProducts->map(function ($bill) {
                $bill->total = $bill->sub_total + $bill->acc_price + $bill->tax_values - $bill->total_discount;
                return $bill;
            });

            $total = 0;
            foreach ($BillProducts as $bill) {
                $total += ($bill->total);
            }
            self::$billProductsData = $total;
        }

        return self::$billProductsData;
    }

    public static function billInvoiceData($array, $request, $yearList)
    {
        $billsum = [];
        foreach ($array as $category => $categoryData) {
            $billchartArr = [];
            foreach ($yearList as $key => $value) {

                if ($request->period === 'quarterly') {
                    for ($i = 0; $i < 12; $i += 3) {
                        $invoicequarterArr = array_slice($categoryData[$key], $i, 3);
                        $billchartArr[] = array_sum($invoicequarterArr);
                    }
                } elseif ($request->period === 'half-yearly') {
                    for ($i = 0; $i < 12; $i += 6) {
                        $InvoicehalfYearArr = array_slice($categoryData[$key], $i, 6);
                        $billchartArr[] = array_sum($InvoicehalfYearArr);
                    }
                } elseif ($request->period === 'yearly') {
                    for ($i = 0; $i < 12; $i += 12) {
                        $invoiceyearArr = array_slice($categoryData[$key], $i, 12);
                        $billchartArr[] = array_sum($invoiceyearArr);
                    }
                } else {
                    // Monthly
                    $billchartArr = $categoryData[$key];
                }
            }

            $billdata = [
                "category" => $category,
                "data" => $billchartArr,
            ];

            $billsum[] = $billdata;
        }
        return $billsum;
    }

    public static function revenuePaymentData($category, $categoryData, $request, $yearList)
    {

        $chartArr = [];
        foreach ($yearList as $key => $value) {
            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterArr = array_slice($categoryData[$key], $i, 3);
                    $chartArr[] = array_sum($quarterArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearArr = array_slice($categoryData[$key], $i, 6);
                    $chartArr[] = array_sum($halfYearArr);
                }
            } elseif ($request->period === 'yearly') {

                for ($i = 0; $i < 12; $i += 12) {
                    $yearArr = array_slice($categoryData[$key], $i, 12);
                    $chartArr[] = array_sum($yearArr);
                }

            } else {
                $chartArr = $categoryData[$key];
                $billchartArr = $categoryData[$key];
            }
        }

        $chartdata = [
            "category" => $category,
            "data" => $chartArr,
        ];

        return $chartdata;
    }

    public static function totalData($billArr, $expenseArr, $request, $yearList)
    {

        $chartExpenseArr = [];
        foreach ($yearList as $year) {

            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterbillArr = array_slice($billArr[$year], $i, 3);
                    $quarterexpenseArr = array_slice($expenseArr[$year], $i, 3);
                    $chartbillArr[$year][$i] = array_sum($quarterbillArr);
                    $chartexpenseArr[$year][$i] = array_sum($quarterexpenseArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearBillArr = array_slice($billArr[$year], $i, 6);
                    $halfYearExpenseArr = array_slice($expenseArr[$year], $i, 6);
                    $chartbillArr[$year][$i] = array_sum($halfYearBillArr);
                    $chartexpenseArr[$year][$i] = array_sum($halfYearExpenseArr);
                }
            } elseif ($request->period === 'yearly') {
                for ($i = 0; $i < 12; $i += 12) {
                    $YearBillArr = array_slice($billArr[$year], $i, 12);

                    $YearExpenseArr = array_slice($expenseArr[$year], $i, 12);
                    $chartbillArr[$year][$i] = array_sum($YearBillArr);
                    $chartexpenseArr[$year][$i] = array_sum($YearExpenseArr);
                }
            } else {
                for ($i = 1; $i <= 12; $i++) {
                    $chartbillArr[$year][] = $billArr[$year][$i];
                    $chartexpenseArr[$year][] = $expenseArr[$year][$i];
                }
            }
        }

        if (isset($chartexpenseArr) && isset($chartbillArr)) {

            foreach ($chartexpenseArr as $year => $values) {
                if (isset($chartbillArr[$year])) {
                    $chartExpenseArr[] = array_map(function ($a, $b) {
                        return $a + $b;
                    }, $chartexpenseArr[$year], $chartbillArr[$year]);
                } else {
                    $chartExpenseArr[$year] = $values;
                }
            }
        }

        return $chartExpenseArr;
    }

    public static function totalSum($array, $request, $yearList)
    {

        $totalArr = [];
        foreach ($yearList as $year) {

            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterArr = array_slice($array[$year], $i, 3);
                    $totalArr[$year][$i] = array_sum($quarterArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearArr = array_slice($array[$year], $i, 6);
                    $totalArr[$year][$i] = array_sum($halfYearArr);
                }
            } elseif ($request->period === 'yearly') {
                for ($i = 0; $i < 12; $i += 12) {
                    $YearArr = array_slice($array[$year], $i, 12);
                    $totalArr[$year][$i] = array_sum($YearArr);
                }
            } else {
                for ($i = 1; $i <= 12; $i++) {
                    $totalArr[$year][] = $array[$year][$i];
                }
            }
        }
        return $totalArr;
    }

    public static function emailTemplateLang($lang)
    {

        $defaultTemplate = [
            'new_user' => [
                'subject' => 'New User',
                'lang' => [
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                ],
            ],
            'new_client' => [
                'subject' => 'New Client',
                'lang' => [
                    'en' => '<p><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">Hello {client_name},</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">You are now Client..</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><b data-stringify-type="bold" style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">Email&nbsp;</b><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">: {client_email}</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><b data-stringify-type="bold" style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">Password</b><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">&nbsp;: {client_password}</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">{app_url}</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">Thanks,</span><br style="box-sizing: inherit; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);"><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; background-color: rgb(248, 248, 248);">{app_name}</span><br></p>',
                ],
            ],
            'new_support_ticket' => [
                'subject' => 'New Support Ticket',
                'lang' => [
                    'en' => '<p><span style="font-size: 12pt;"><b>Hi</b>&nbsp;{support_name}</span><br><br><span style="font-size: 12pt;">New support ticket has been opened.</span><br><br><span style="font-size: 12pt;"><strong>Title:</strong>&nbsp;{support_title}</span><br><span style="font-size: 12pt;"><strong>Priority:</strong>&nbsp;{support_priority}</span><span style="font-size: 12pt;"><br></span><span style="font-size: 12pt;"><b>End Date</b>: {support_end_date}</span></p><p><br><span style="font-size: 12pt;"><strong>Support message:</strong></span><br><span style="font-size: 12pt;">{support_description}</span><span style="font-size: 12pt;"><br><br><b>Kind Regards</b>,</span><br>{app_name}</p>',
                ],
            ],
            'lead_assigned' => [
                'subject' => 'Lead Assigned',
                'lang' => [
                    'en' => '<p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="font-family: " open="" sans";"="">﻿</span><span style="font-family: " open="" sans";"="">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: " open="" sans";"="">New Lead has been Assign to you.</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="" open="" sans";"=""><b>Lead Name</b></span><span style="" open="" sans";"="">&nbsp;: {lead_name}</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span open="" sans";"="" style="font-size: 1rem;"><b>Lead Email</b></span><span open="" sans";"="" style="font-size: 1rem;">&nbsp;: {lead_email}</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="" open="" sans";"=""><b>Lead Pipeline</b></span><span style="" open="" sans";"="">&nbsp;: {lead_pipeline}</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="" open="" sans";"=""><b>Lead Stage</b></span><span style="" open="" sans";"="">&nbsp;: {lead_stage}</span></p><p style="line-height: 28px;"><span style="" open="" sans";"=""><b>Lead Subject</b>: {lead_subject}</span></p><p></p>',
                ],
            ],
            'deal_assigned' => [
                'subject' => 'Deal Assigned',
                'lang' => [
                    'en' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal has been Assign to you.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Deal Name</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal Status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal Price</span>&nbsp;: {deal_price}</span></p><p></p>',
                ],
            ],
            'new_award' => [
                'subject' => 'New Award',
                'lang' => [
                    'en' => '<p>Hi , <span style="font-family: var(--bs-body-font-family); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{award_name}</span></p><p>I am much pleased to nominate .</p><p>I am satisfied that he/she is the best employee for the award. </p><p>I have realized  that he/she is a goal-oriented person, efficient and very punctual .</p><p>Feel free to reach out if you have any question.<br></p><p>Thank You, </p><p>{app_name}</p><p>{app_url}</p>',
                ],
            ],
            'customer_invoice_sent' => [
                'subject' => 'Customer Invoice Sent',
                'lang' => [
                    'en' => '<p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="font-family: " open="" sans";"="">﻿</span><span style="text-align: var(--bs-body-text-align);">Hi ,{invoice_name}</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Welcome to {app_name}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Hope this email finds you well! Please see attached invoice number {invoice_number}<span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">} for product/service.</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Simply click on the button below: </p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{invoice_url}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Feel free to reach out if you have any questions.</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Thank You,</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Regards,</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{company_name}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{app_url}</p><p></p>',
                ],
            ],
            'customer_purchase_sent' => [
                'subject' => 'Customer Purchase Sent',
                'lang' => [
                    'en' => '<p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"=""><span style="font-family: " open="" sans";"="">﻿</span><span style="text-align: var(--bs-body-text-align);">Hi ,{purchase_name}</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Welcome to {app_name}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Hope this email finds you well! Please see attached invoice number {purchase_number}<span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">} for product/service.</span></p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Simply click on the button below: </p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{purchase_url}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Feel free to reach out if you have any questions.</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Thank You,</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">Regards,</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{company_name}</p><p style="line-height: 28px; font-family: Nunito, " segoe="" ui",="" arial;="" font-size:="" 14px;"="">{app_url}</p><p></p>',
                ],
            ],
            'new_invoice_payment' => [
                'subject' => 'New Invoice Payment',
                'lang' => [
                    'en' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Hi,</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Welcome to {app_name}</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Dear {invoice_payment_name}</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">We have recieved your amount {invoice_payment_amount} payment for {invoice_number} submited on date {invoice_payment_date}</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Your {invoice_number} Due amount is {payment_dueAmount}</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">We appreciate your prompt payment and look forward to continued business with you in the future.</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Thank you very much and have a good day!!</span></span></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Regards,</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">{company_name}</span></span></p>
                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif;">
                    <span style="font-size: 15px; font-variant-ligatures: common-ligatures;">{app_url}</span></span></p>',
                ],
            ],
            'new_payment_reminder' => [
                'subject' => 'New Payment Reminder',
                'lang' => [
                    'en' => '<p>Dear, {payment_reminder_name}</p>
                    <p>I hope you&rsquo;re well.This is just a reminder that payment on invoice {invoice_payment_number} total dueAmount {invoice_payment_dueAmount} , which we sent on {payment_reminder_date} is due today.</p>
                    <p>You can make payment to the bank account specified on the invoice.</p>
                    <p>I&rsquo;m sure you&rsquo;re busy, but I&rsquo;d appreciate if you could take a moment and look over the invoice when you get a chance.</p>
                    <p>If you have any questions whatever, please reply and I&rsquo;d be happy to clarify them.</p>
                    <p>&nbsp;</p>
                    <p>Thanks,&nbsp;</p>
                    <p>{company_name}</p>
                    <p>{app_url}</p>
                    <p>&nbsp;</p>',
                ],
            ],
            'new_bill_payment' => [
                'subject' => 'New Bill Payment',
                'lang' => [
                    'en' => '<p>Hi , {payment_name}</p><p>Welcome to {app_name}</p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">We are writing to inform you that we has sent your {payment_bill} payment.</span></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">We has sent your amount {payment_amount} payment for {payment_bill} submited&nbsp; on date {payment_date} via {payment_method}.</span></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Thank You very much and have a good day !!!!</span></p><p>{company_name}</p><p>{app_url}</p>',
                    'es' => '<p>Hola, {nombre_pago}</p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Bienvenido a {app_name}</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Estamos escribiendo para informarle que hemos enviado su pago {payment_bill}.</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Hemos enviado su importe {payment_amount} pago para {payment_bill} submitado en la fecha {payment_date} a través de {payment_method}.</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Thank You very much and have a good day! !!!</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{nombre_empresa}</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{app_url}</span><br></p>',
                ],
            ],
            'bill_resent' => [
                'subject' => 'Bill Resent',
                'lang' => [
                    'en' => '<p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Hi , {bill_name}</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Welcome to {app_name}</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Hope this email finds you well! Please see attached bill number {bill_bill} for product/service.</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Simply click on the button below .</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{bill_url}</span></p><p>Feel free to reach out if you have any questions.</p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Thank You for your business !!!!</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Regards,</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{company_name}</span><br></p><p><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{app_url}</span><br></p><div><br></div>',
                ],
            ],
            'proposal_sent' => [
                'subject' => 'Proposal Sent',
                'lang' => [
                    'en' => '<p>Hi, {proposal_name}</p>
                    <p>Hope this email ﬁnds you well! Please see attached proposal number {proposal_number} for product/service.</p>
                    <p>simply click on the button below</p>
                    <p>{proposal_url}</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you for your business!!</p>
                    <p>&nbsp;</p>
                    <p>Regards,</p>
                    <p>{company_name}</p>
                    <p>{app_url}</p>',
                ],
            ],
            'complaint_resent' => [
                'subject' => 'Complaint Resent',
                'lang' => [
                    'en' => '<p><font color="#1d1c1d" face="Slack-Lato, Slack-Fractions, appleLogo, sans-serif"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Hi ,</span></font></p><p><span style="font-size: 15px; font-variant-ligatures: common-ligatures; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Welcome to {app_name}</span><br></p><p><font color="#1d1c1d" face="Slack-Lato, Slack-Fractions, appleLogo, sans-serif"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">HR department/company to send complaints letter.<br></span></font></p><p><font color="#1d1c1d" face="Slack-Lato, Slack-Fractions, appleLogo, sans-serif"><span style="font-size: 15px; font-variant-ligatures: common-ligatures;">Dear {complaint_name}</span></font></p><p>I would like to report a conflict between you and the other person. There  have been several incidents over the last few days, and I feel that its is time to report a formal complaint against him/her.</p><p>Feel free to reach out if you have any questions.</p><p><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Thank You,</span></p><p><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">Regards,</span></p><p><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">HR Department.</span></p><p><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{company_name}</span><span style="color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; font-variant-ligatures: common-ligatures; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);"><br></span></p><p><span style="font-size: 15px; font-variant-ligatures: common-ligatures; color: rgb(29, 28, 29); font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{app_url}</span><br></p>',
                ],
            ],
            'leave_action_sent' => [
                'subject' => 'Leave Action Sent',
                'lang' => [
                    'en' => '<p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Subject : "HR department/company to send approval letter to {leave_status} a vacation or leave" .</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">﻿Hi ,{leave_name}</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;"><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; I have {leave_status} your leave request for&nbsp; {leave_reason} from {leave_start_date} to {leave_end_date}. {total_leave_days} days I have&nbsp; {leave_status} your leave request for {leave_reason}.</span><br></p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;"><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">We request you to complete all your pending work or any other important issue so that the company does not face any any loss or problem during your absence. We appreciate your thoughtfulness to inform us well in advance.</span></p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Feel free to reach out if you have any questions.</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Thank You,</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Regards,</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">HR Department,</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">{app_name}</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">{app_url}</p><p></p>',
                ],
            ],
            'payslip_sent' => [
                'subject' => 'Payslip Sent',
                'lang' => [
                    'en' => '<p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Subject :&nbsp; " HR&nbsp; Department / Company to send&nbsp; payslips by email at time of confirmation of payslip. "</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">﻿Dear ,{payslip_name}</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;"><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">&nbsp; &nbsp;&nbsp;</span>&nbsp; &nbsp; Hope this email finds you well! Please see attached payslip for {payslip_salary_month} . Simply click on the button below :&nbsp;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {payslip_url}</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Feel free to&nbsp; reach out if you have any questions.</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">Regards ,</p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;"><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">HR Department ,</span></p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;"><span style="font-family: var(--bs-body-font-family); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">{app_name}</span><br></p><p segoe="" ui",="" arial;="" font-size:="" 14px;"="" style="line-height: 28px;">{app_url}</p><p></p>',
                ],
            ],
            'promotion_sent' => [
                'subject' => 'Promotion Sent',
                'lang' => [
                    'en' => '<p>&nbsp;</p>
                    <p><strong>Subject:-HR department/Company to send job promotion congratulation letter.</strong></p>
                    <p><strong>Dear {employee_name},</strong></p>
                    <p>Congratulations on your promotion to {promotion_designation} {promotion_title} effective {promotion_date}.</p>
                    <p>We shall continue to expect consistency and great results from you in your new role. We hope that you will set an example for the other employees of the organization.</p>
                    <p>We wish you luck for your future performance, and congratulations!.</p>
                    <p>Again, congratulations on the new position.</p>
                    <p>&nbsp;</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><strong>Regards,</strong></p>
                    <p><strong>HR Department,</strong></p>
                    <p><strong>{app_name}</strong></p>',
                ],
            ],
            'resignation_sent' => [
                'subject' => 'Resignation Sent',
                'lang' => [
                    'en' => '<p ><b>Subject:-HR department/Company to send resignation letter .</b></p>
                    <p ><b>Dear {assign_user},</b></p>
                    <p >It is with great regret that I formally acknowledge receipt of your resignation notice on {notice_date} to {resignation_date} is your final day of work. </p>
                    <p >It has been a pleasure working with you, and on behalf of the team, I would like to wish you the very best in all your future endeavors. Included with this letter, please find an information packet with detailed information on the resignation process. </p>
                    <p>Thank you again for your positive attitude and hard work all these years.</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><b>Regards,</b></p>EmailTemplate
                    <p><b>HR Department,</b></p>
                    <p><b>{app_name}</b></p>',
                ],
            ],
            'termination_sent' => [
                'subject' => 'Termination Sent',
                'lang' => [
                    'en' => '<p><strong>Subject:-HR department/Company to send termination letter.</strong></p>
                    <p><strong>Dear {employee_termination_name},</strong></p>
                    <p>This letter is written to notify you that your employment with our company is terminated.</p>
                    <p>More detail about termination:</p>
                    <p>Notice Date :{notice_date}</p>
                    <p>Termination Date:{termination_date}</p>
                    <p>Termination Type:{termination_type}</p>
                    <p>&nbsp;</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><strong>Regards,</strong></p>
                    <p><strong>HR Department,</strong></p>
                    <p><strong>{app_name}</strong></p>',
                ],
            ],
            'transfer_sent' => [
                'subject' => 'Transfer Sent',
                'lang' => [
                    'en' => '<p ><b>Subject:-HR department/Company to send transfer letter to be issued to an employee from one location to another.</b></p>
                    <p ><b>Dear {transfer_name},</b></p>
                    <p >As per Management directives, your services are being transferred w.e.f.{transfer_date}. </p>
                    <p >Your new place of posting is {transfer_department} department of {transfer_branch} branch and date of transfer {transfer_date}. </p>
                    {transfer_description}.
                    <p>Feel free to reach out if you have any questions.</p>
                    <p><b>Thank you</b></p>
                    <p><b>Regards,</b></p>
                    <p><b>HR Department,</b></p>
                    <p><b>{app_name}</b></p>',
                ],
            ],
            'trip_sent' => [
                'subject' => 'Trip Sent',
                'lang' => [
                    'en' => '<p><strong>Subject:-HR department/Company to send trip letter .</strong></p>
                    <p><strong>Dear {employee_name},</strong></p>
                    <p>Top of the morning to you! I am writing to your department office with a humble request to travel for a {purpose_of_visit} abroad.</p>
                    <p>It would be the leading climate business forum of the year and have been lucky enough to be nominated to represent our company and the region during the seminar.</p>
                    <p>My three-year membership as part of the group and contributions I have made to the company, as a result, have been symbiotically beneficial. In that regard, I am requesting you as my immediate superior to permit me to attend.</p>
                    <p>More detail about trip:{start_date} to {end_date}</p>
                    <p>Trip Duration:{start_date} to {end_date}</p>
                    <p>Purpose of Visit:{purpose_of_visit}</p>
                    <p>Place of Visit:{place_of_visit}</p>
                    <p>Description:{trip_description}</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><strong>Regards,</strong></p>
                    <p><strong>HR Department,</strong></p>
                    <p><strong>{app_name}</strong></p>',
                ],
            ],
            'vender_bill_sent' => [
                'subject' => 'Vendor Bill Sent',
                'lang' => [
                    'en' => '<p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Hi, {bill_name}</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Welcome to {app_name}</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Hope this email finds you well!! Please see attached bill number {bill_number} for product/service.</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Simply click on the button below.</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">{bill_url}</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Feel free to reach out if you have any questions.</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Thank You,</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">Regards,</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">{company_name}</span></p>
                    <p style="line-height: 28px; font-family: Nunito,;"><span style="font-family: sans-serif;">{app_url}</span></p>',
                ],
            ],
            'warning_sent' => [
                'subject' => 'Warning Sent',
                'lang' => [
                    'en' => '<p><strong>Subject:-HR department/Company to send warning letter.</strong></p>
                    <p><strong>Dear {employee_warning_name},</strong></p>
                    <p>{warning_subject}</p>
                    <p>{warning_description}</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><strong>Regards,</strong></p>
                    <p><strong>HR Department,</strong></p>
                    <p><strong>{app_name}</strong></p>',
                ],
            ],
            'new_contract' => [
                'subject' => 'New Contract',
                'lang' => [
                    'en' => '<p>&nbsp;</p>
                    <p><strong>Hi</strong> {contract_client}</p>
                    <p><b>Contract Subject</b>&nbsp;: {contract_subject}</p>
                    <p><b>Contract Project</b>&nbsp;: {contract_project}</p>
                    <p><b>Start Date&nbsp;</b>: {contract_start_date}</p>
                    <p><b>End Date&nbsp;</b>: {contract_end_date}</p>
                    <p>Looking forward to hear from you.</p>
                    <p><strong>Kind Regards, </strong></p>
                    <p>{company_name}</p>',
                ],
            ],
            'project_assign_member' => [
                'subject' => 'Project Assign Member',
                'lang' => [
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                ],
            ],
            'new_project' => [
                'subject' => 'New Project',
                'lang' => [
                    'en' => '<p><strong>Hi</strong> {project_user}</p>
                    <p><b>Project Name</b>&nbsp;: {project_name}</p>
                    <p><b>Start Date&nbsp;</b>: {project_start_date}</p>
                    <p><b>End Date&nbsp;</b>: {project_end_date}</p>
                    <p><b>Estimated Hours&nbsp;</b>: {hours}</p>
                    <p>Looking forward to hear from you.</p>
                    <p><strong>Kind Regards, </strong></p>
                    <p>{company_name}</p>',
                ],
            ],
            'new_task' => [
                'subject' => 'New Task',
                'lang' => [
                    'en' => '<p><strong>Hi</strong> {task_user}</p>
                    <p><b>Task Name</b>&nbsp;: {task_name}</p>
                    <p><b>Project Name</b>&nbsp;: {project_name}</p>
                    <p><b>Start Date&nbsp;</b>: {task_start_date}</p>
                    <p><b>End Date&nbsp;</b>: {task_end_date}</p>
                    <p><b>Estimated Hours&nbsp;</b>: {hours}</p>
                    <p>Looking forward to hear from you.</p>
                    <p><strong>Kind Regards, </strong></p>
                    <p>{company_name}</p>',
                ],
            ],
            'task_status_updated' => [
                'subject' => 'Task Status Updated',
                'lang' => [
                    'en' => '<p>&nbsp;</p>
                    <p><strong>Hi</strong> {task_user}</p>
                    <p>{task_name} status changed from {old_stage_name} to {new_stage_name}</p>
                    <p><strong>Kind Regards, </strong></p>
                    <p>{company_name}</p>',
                ],
            ],
            'new_leave' => [
                'subject' => 'New Leave',
                'lang' => [
                    'en' => '<p><strong>Hi</strong> {user_name}</p>
                    <p>New Leave create from {start_date} to {end_date} for {leave_reason}</p>
                    <p><strong>Kind Regards, </strong></p>
                    <p>{employee_name}</p>',
                ],
            ],
            'promotion_sent' => [
                'subject' => 'Promotion Sent',
                'lang' => [
                    'en' => '<p>&nbsp;</p>
                    <p><strong>Subject:-HR department/Company to send job promotion congratulation letter.</strong></p>
                    <p><strong>Dear {employee_name},</strong></p>
                    <p>Congratulations on your promotion to {promotion_designation} {promotion_title} effective {promotion_date}.</p>
                    <p>We shall continue to expect consistency and great results from you in your new role. We hope that you will set an example for the other employees of the organization.</p>
                    <p>We wish you luck for your future performance, and congratulations!.</p>
                    <p>Again, congratulations on the new position.</p>
                    <p>&nbsp;</p>
                    <p>Feel free to reach out if you have any questions.</p>
                    <p>Thank you</p>
                    <p><strong>Regards,</strong></p>
                    <p><strong>HR Department,</strong></p>
                    <p><strong>{app_name}</strong></p>',
                ],
            ],
        ];

        $email = EmailTemplate::all();

        foreach ($email as $e) {
            foreach ($defaultTemplate[$e->slug]['lang'] as  $content) {
                $emailNoti = EmailTemplateLang::where('parent_id', $e->id)->where('lang', $lang)->count();
                if ($emailNoti == 0) {
                    EmailTemplateLang::create(
                        [
                            'parent_id' => $e->id,
                            'lang' => $lang,
                            'subject' => $defaultTemplate[$e->slug]['subject'],
                            'content' => $content,
                        ]
                    );
                }
            }
        }
    }

    public static function smtpDetail()
    {
        $settings = self::settings();
        $smtpDetail = config(
            [
                'mail.driver' => $settings['mail_driver'],
                'mail.host' => $settings['mail_host'],
                'mail.port' => $settings['mail_port'],
                'mail.encryption' => $settings['mail_encryption'],
                'mail.username' => $settings['mail_username'],
                'mail.password' => $settings['mail_password'],
                'mail.from.address' => $settings['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'],
            ]
        );

        return $smtpDetail;
    }
    public static function file_validate()
    {
        try {
            $settings = Utility::getStorageSetting();
            if (!empty($settings['storage_setting'])) {
                if ($settings['storage_setting'] == 'wasabi') {
                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes =  !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';
                } else if ($settings['storage_setting'] == 's3') {
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes =  !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';
                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';
                    $mimes =  !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }
                    $res = [
                        'types'  => $mimes,
                        'max_size'  => $max_size,
                    ];
                    return $res;
            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }
        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function getTableFields($input, $extraFields = [])
    {
        try {
            // if (class_exists($input)) {
            //     // If you pass model name
            //     $model = new $input();
            //     if (method_exists($model, 'getTable')) {
            //         $table = $model->getTable();
            //     } else {
            //         return [
            //             'status' => true,
            //             'message' => 'Table name not found',
            //             'data' => null,
            //         ];
            //     }
            // } else {
            //     // If you pass table name
                // $table = $input;
            // }

            if(Schema::hasTable($input)){
                $tableFields = Schema::getColumnListing($input);
            } else {
                return [
                    'status' => true,
                    'message' => 'Table name not found',
                    'data' => null,
                ];
            }

            $filteredFields = array_diff($tableFields, $extraFields);

            return [
                'status' => true,
                'message' => 'success',
                'data' => $filteredFields,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public static function migrationEntryDelete()
    {
        try {
            $migrations = [
                '2019_10_18_223259_add_avatar_to_users',
                '2019_10_20_211056_add_messenger_color_to_users',
                '2019_10_22_000539_add_dark_mode_to_users',
                '2019_10_25_214038_add_active_status_to_users',
            ];
            DB::table('migrations')->whereIn('migration', $migrations)->delete();

            return [
                'status' => true,
                'message' => 'Migration entry delete successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function getAiModelName()
    {
        return [
            'GPT-4 Series' => [
                'gpt-4o' => 'GPT-4o',
                'gpt-4-turbo' => 'GPT-4-Turbo',
                'gpt-4' => 'GPT-4',
                'gpt-4.1-nano' => 'GPT-4.1-Nano',
            ],
            'GPT-3.5 Series' => [
                'gpt-3.5-turbo' => 'GPT-3.5-Turbo',
                'gpt-3.5-turbo-instruct' => 'GPT-3.5-Turbo-Instruct',
            ],
        ];
    }

}
