<?php

use App\Http\Middleware\ApiTenancyMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\WebTenancyMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(fn () => route('workspaces'));
        $middleware->redirectGuestsTo(fn () => route('site.home'));

        $middleware->web(append: [
            WebTenancyMiddleware::class,
            HandleInertiaRequests::class,
        ]);

        $middleware->api(append: [
            ApiTenancyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
