<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;

>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
=======
        $middleware->alias([
            'player.session' => \App\Http\Middleware\CheckPlayerSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
