<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        // commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware
        $middleware->append([
            // \App\Http\Middleware\TrustHosts::class,
            \App\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \App\Http\Middleware\TrimStrings::class,
        ]);

        // RouteMiddleware / Alias
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'XSS' => \App\Http\Middleware\XSS::class,
            'revalidate' => \App\Http\Middleware\RevalidateBackHistory::class,
            'pusher' => \App\Http\Middleware\pusherConfig::class,
        ]);

        // middlewareGroups / Group Middleware
        // Append middleware to the 'web' group
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \App\Http\Middleware\FilterRequest::class,
            // \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Append middleware to the 'api' group
        $middleware->appendToGroup('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Exclude specific routes from CSRF protection
        $middleware->validateCsrfTokens(
            except: [
                'plan/paytm/*',
                '/customer/paytm/*',
                'plan-pay-with-paymentwall/*',
                'invoice-pay-with-paymentwall/*',
                'iyzipay/callback/*',
                'paytab-success/*',
                '/aamarpay/*',
                'invoice-easebuzz-payment-notify*',
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
