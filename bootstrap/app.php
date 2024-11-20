<?php

use App\Http\Middleware\CheckForAuthToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $authCookie = env('AUTH_COOKIE_NAME');

        $middleware->encryptCookies(except: [
            $authCookie,
            'app-token',
        ]);

        $middleware->statefulApi();
        $middleware->api('throttle:api');
        $middleware->prependToGroup('api', CheckForAuthToken::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
