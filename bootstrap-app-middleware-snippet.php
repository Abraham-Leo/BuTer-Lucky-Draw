<?php
/*
 * Tambahkan blok berikut ke bootstrap/app.php (Laravel 12) di dalam
 * ->withMiddleware(function (Middleware $middleware) { ... })
 * agar alias middleware "can.manage.draw" terdaftar.
 */

use App\Http\Middleware\EnsureCanManageDraw;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'can.manage.draw' => EnsureCanManageDraw::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();
