<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ValidUser;
use App\Http\Middleware\ValidTeacher;
use App\Http\Middleware\ValidCounselor;
use App\Http\Middleware\ValidAdmin;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'Admin' => ValidAdmin::class,
            'Teacher' => ValidTeacher::class,
            'Counselor' => ValidCounselor::class,
            'User' => ValidUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
  