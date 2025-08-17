<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api', // Pastikan ini ada
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude Midtrans webhook dari CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/midtrans-webhook',
            '/api/midtrans-webhook',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        $middleware->trustProxies(
            at: '*', // Atau bisa diatur sesuai kebutuhan
        );

        // Untuk Laravel 11, throttling diatur berbeda
        // Kita akan handle di route level jika perlu
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
