<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\EnsureTenantActive;
use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\CheckUsage;
use App\Http\Middleware\EnsurePlatformAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant.resolve' => ResolveTenant::class,
            'tenant.active' => EnsureTenantActive::class,
            'module' => EnsureModuleEnabled::class,
            'usage' => CheckUsage::class,
            'platform.admin' => EnsurePlatformAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\App\Modules\Licensing\Domain\Exceptions\ModuleNotLicensedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'module_not_licensed',
            ], 403);
        });

        $exceptions->render(function (\App\Modules\Licensing\Domain\Exceptions\UsageLimitExceededException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'usage_limit_exceeded',
            ], 422);
        });
    })->create();
