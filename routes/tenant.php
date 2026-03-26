<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('tenant')->middleware(['auth:sanctum', 'tenant.resolve', 'tenant.active'])->group(function () {
    Route::get('/settings', [\App\Modules\Tenancy\Http\Controllers\TenantSettingController::class, 'index']);
    Route::put('/settings', [\App\Modules\Tenancy\Http\Controllers\TenantSettingController::class, 'update']);

    Route::get('/members', [\App\Modules\AccessControl\Http\Controllers\MembershipController::class, 'index']);
    Route::post('/members/invite', [\App\Modules\AccessControl\Http\Controllers\MembershipController::class, 'invite'])
        ->middleware('permission:tenant.users.invite');
    Route::delete('/members/{membership}', [\App\Modules\AccessControl\Http\Controllers\MembershipController::class, 'destroy'])
        ->middleware('permission:tenant.users.manage');

    Route::get('/roles', [\App\Modules\AccessControl\Http\Controllers\RoleController::class, 'index']);
    Route::post('/roles', [\App\Modules\AccessControl\Http\Controllers\RoleController::class, 'store'])
        ->middleware('permission:tenant.roles.manage');

    Route::get('/branding', [\App\Modules\Tenancy\Http\Controllers\BrandingController::class, 'show']);
    Route::put('/branding', [\App\Modules\Tenancy\Http\Controllers\BrandingController::class, 'update'])
        ->middleware('permission:tenant.settings.manage');

    Route::get('/subscription', [\App\Modules\Licensing\Http\Controllers\SubscriptionController::class, 'show']);
    Route::get('/integrations', [\App\Modules\Integrations\Http\Controllers\IntegrationController::class, 'index']);
});
