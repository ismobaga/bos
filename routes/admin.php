<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform Admin Routes
|--------------------------------------------------------------------------
| Routes for CROMMIX staff/admin panel
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'platform.admin'])->group(function () {
    Route::get('/tenants', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'index']);
    Route::post('/tenants', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'store']);
    Route::get('/tenants/{tenant}', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'show']);
    Route::put('/tenants/{tenant}', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'update']);
    Route::post('/tenants/{tenant}/suspend', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'suspend']);
    Route::post('/tenants/{tenant}/activate', [\App\Modules\Tenancy\Http\Controllers\Admin\TenantAdminController::class, 'activate']);

    Route::get('/plans', [\App\Modules\Licensing\Http\Controllers\Admin\PlanAdminController::class, 'index']);
    Route::post('/plans', [\App\Modules\Licensing\Http\Controllers\Admin\PlanAdminController::class, 'store']);
    Route::put('/plans/{plan}', [\App\Modules\Licensing\Http\Controllers\Admin\PlanAdminController::class, 'update']);

    Route::get('/modules', [\App\Modules\Licensing\Http\Controllers\Admin\ModuleAdminController::class, 'index']);

    Route::get('/subscriptions', [\App\Modules\Licensing\Http\Controllers\Admin\SubscriptionAdminController::class, 'index']);

    Route::get('/users', [\App\Modules\Identity\Http\Controllers\Admin\UserAdminController::class, 'index']);

    Route::get('/audit-logs', [\App\Modules\Audit\Http\Controllers\AuditLogController::class, 'index']);
});
