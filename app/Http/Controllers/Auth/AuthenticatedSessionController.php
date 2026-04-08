<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyReCaptchaToken;
use App\Models\Customer;
use App\Models\LoginDetail;
use App\Models\Vender;
use  App\Models\Utility;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */


    public function __construct()
    {
        // if(!file_exists(storage_path() . "/installed"))
        // {
        //     header('location:install');
        //     die;
        // }
        // $this->middleware('guest')->except('logout');
    }

    public function create()
    {
        // return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */


    // protected function authenticated(Request $request)
    //    {


    //             $user = Auth::user();
    //        if($user->delete_status == 0)
    //        {
    //            auth()->logout();
    //        }

    //        if($user->is_active == 0)
    //        {
    //            auth()->logout();
    //        }
    //    }


    public function store(LoginRequest $request)
    {
        $user = User::where('email',$request->email)->first();
        $customer = null;

        // If user not found, check Customer table (Travel Agency type)
        if(!$user) {
            $customer = Customer::where('email', $request->email)
                ->where('type', 'Travel Agency')
                ->first();
            
            if($customer != null) {
                // Check if login is enabled and password is set
                if($customer->is_enable_login == 0 || empty($customer->password)) {
                    return redirect()->back()->with('status', __('Login is not enabled for this account.'));
                }
                
                if($customer->is_active == 0) {
                    return redirect()->back()->with('status', __('Your account is disabled.'));
                }
                
                // Verify password
                if(!\Hash::check($request->password, $customer->password)) {
                    return redirect()->back()->with('status', __('These credentials do not match our records.'));
                }
                
                // Login as Customer using customer guard
                // Store customer info in session to identify it's a Customer login
                Auth::guard('customer')->login($customer, $request->boolean('remember'));
                $request->session()->put('is_customer_login', true);
                $request->session()->put('customer_id', $customer->id);
                $request->session()->regenerate();
                
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        }

        if($user != null && $user->is_enable_login == 0 && $user->type != 'company')
        {
            return redirect()->back()->with('status', __('Your Account is disable from company.'));
        }

        $settings = Utility::settings();
        \App::setLocale($settings['default_language']);
        
        //ReCpatcha
        $validation = [];

        if(isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
        {
            if($settings['google_recaptcha_version'] == 'v2-checkbox'){
                $validation['g-recaptcha-response'] = 'required|captcha';
            }
            elseif($settings['google_recaptcha_version'] == 'v3'){
                $result = event(new VerifyReCaptchaToken($request));
                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]); // Set the key to null
                    $validation['g-recaptcha-response'] = 'required';
                }
            }else{
                $validation = [];
            }
        }else{
            $validation = [];
        }
        if($user){
            $this->validate($request, $validation);
            User::defaultEmail();
            $user->userDefaultDataRegister($user->id);
            $request->authenticate();

            $request->session()->regenerate();

            $user = Auth::user();

            if($user->delete_status == 0)
            {
                auth()->logout();
            }

            if($user->is_active == 0)
            {
                auth()->logout();
            }
            // Update Last Login Time
            $user->update(
                [
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                ]
            );

            if(isset($user->type) && $user->type == 'company') {
                Utility::migrationEntryDelete();
            }

            //start for user log
            if ($user->type != 'company' && $user->type != 'super admin')
            {
                $ip = $_SERVER['REMOTE_ADDR']; // your ip address here

    //     $ip = '49.36.83.154'; // This is static ip address

                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));

                if (isset($query['status']) && $query['status'] != 'fail') {
                    $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                    if ($whichbrowser->device->type == 'bot') {
                        return;
                    }
                    $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

                    /* Detect extra details about the user */
                    $query['browser_name'] = $whichbrowser->browser->name ?? null;
                    $query['os_name'] = $whichbrowser->os->name ?? null;
                    $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                    $query['device_type'] = get_device_type($_SERVER['HTTP_USER_AGENT']);
                    $query['referrer_host'] = !empty($referrer['host']);
                    $query['referrer_path'] = !empty($referrer['path']);

                    isset($query['timezone']) ? date_default_timezone_set($query['timezone']) : '';

                    $json = json_encode($query);

                    $login_detail = new LoginDetail();
                    $login_detail->user_id = Auth::user()->id;
                    $login_detail->ip = $ip;
                    $login_detail->date = date('Y-m-d H:i:s');
                    $login_detail->Details = $json;
                    $login_detail->created_by = \Auth::user()->creatorId();
                    $login_detail->save();

                }
            }
            //end for user log

            if($user->type =='company' || $user->type =='client')
            {
                return redirect()->intended(RouteServiceProvider::HOME);

            }
            else
            {
                return redirect()->intended(RouteServiceProvider::EMPHOME);
            }
        }else{
            return redirect()->back()->with('status', __('These credentials do not match our records.'));
        }

    }
    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function showLoginForm($lang = '')
    {

        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        $langList = Utility::languages()->toArray();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';

        \App::setLocale($lang);

        $settings = Utility::settings();

        return view('auth.login', compact('lang','settings'));
    }

    public function showLinkRequestForm($lang = '')
    {

        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        $langList = Utility::languages()->toArray();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';

        \App::setLocale($lang);

        return view('auth.forgot-password', compact('lang'));
    }


    public function showResetForm(Request $request, $token = null)
    {

        $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
        $lang             = !empty($default_language) ? $default_language->value : 'en';

        \App::setLocale($lang);

        return view('auth.passwords.reset')->with(
            [
                'token' => $token,
                'email' => $request->email,
                'lang' => $lang,
            ]
        );
    }

}

//for user log
if (!function_exists('get_device_type')) {
    function get_device_type($user_agent)
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
}
