<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'middlewareByAccess' => \Idev\EasyAdmin\app\Http\Middleware\MiddlewareByAccess::class
        ]);
    })
    ->withCommands([
        Idev\EasyAdmin\app\Console\Commands\ControllerMaker::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
