<?php

namespace App\Http\Middleware;

use App\Models\LandingPageSection;
use App\Models\User;
use App\Models\Utility;
use Closure;
use Config;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {



        if(\Auth::check())
        {
                    $settings = Utility::settingsById(\Auth::user()->creatorId());
            if (!empty($settings['timezone'])) {
                Config::set('app.timezone', $settings['timezone']);
                date_default_timezone_set(Config::get('app.timezone', 'UTC'));
            }

            \App::setLocale(\Auth::user()->lang);

            if(\Auth::user()->type == 'company')
            {
                $migrations             = $this->getMigrations();
                //$messengerMigration     = Utility::get_messenger_packages_migration();
                $dbMigrations           = $this->getExecutedMigrations();
                $Modulemigrations = glob(base_path().'/Modules/LandingPage/Database'.DIRECTORY_SEPARATOR.'Migrations'.DIRECTORY_SEPARATOR.'*.php');
                $numberOfUpdatesPending = (count($migrations)  + count($Modulemigrations)) - count($dbMigrations);

                if($numberOfUpdatesPending > 0)
                {
                    Utility::addNewData();
                    User::defaultEmail();
                    Utility::addNewAccountData();
                    
                    $users = User::where('type', 'company')->get();
                    foreach($users as $user)
                    {
                        $user->userDefaultDataRegister($user->id);
                    }
                    
                    return redirect()->route('LaravelUpdater::welcome');
                }


            }
        }

        $input = $request->all();
//        array_walk_recursive(
//            $input, function (&$input){
//            $input = strip_tags($input);
//        }
//        );
        $request->merge($input);

        return $next($request);
    }
}
