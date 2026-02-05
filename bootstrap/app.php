<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // 'is.timer.stop' => IsTimerStop::class,
            'check' => \App\Http\Middleware\CheckMePlease::class,
            // ... any other aliases you have ...
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
