<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AppCatalog\Http\Controllers\AppCatalogController;
use App\Modules\CRM\Http\Controllers\CustomerController;
use App\Modules\CRM\Http\Controllers\ContactController;
use App\Modules\Invoice\Http\Controllers\InvoiceController;
use App\Modules\Invoice\Http\Controllers\InvoicePaymentController;
use App\Modules\LMS\Http\Controllers\CourseController;
use App\Modules\LMS\Http\Controllers\EnrollmentController;
use App\Modules\Helpdesk\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Version 1 — All routes require authentication and tenant resolution
*/

Route::prefix('v1')->group(function () {

    // -------------------------------------------------------------------------
    // Platform routes — no tenant resolution needed for some
    // -------------------------------------------------------------------------
    Route::prefix('platform')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', [\App\Modules\Identity\Http\Controllers\MeController::class, 'show']);
        Route::get('/tenants', [\App\Modules\Tenancy\Http\Controllers\TenantController::class, 'index']);
        Route::post('/tenants', [\App\Modules\Tenancy\Http\Controllers\TenantController::class, 'store']);
    });

    Route::prefix('platform')->middleware(['auth:sanctum', 'tenant.resolve', 'tenant.active'])->group(function () {
        Route::get('/apps', [AppCatalogController::class, 'index']);
        Route::get('/license', [\App\Modules\Licensing\Http\Controllers\LicenseController::class, 'show']);
        Route::get('/usage', [\App\Modules\Licensing\Http\Controllers\UsageController::class, 'show']);
    });

    // -------------------------------------------------------------------------
    // CRM Module
    // -------------------------------------------------------------------------
    Route::prefix('crm')->middleware([
        'auth:sanctum',
        'tenant.resolve',
        'tenant.active',
        'module:crm',
    ])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store'])
            ->middleware('permission:crm.customer.create');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])
            ->middleware('permission:crm.customer.view');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])
            ->middleware('permission:crm.customer.edit');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
            ->middleware('permission:crm.customer.delete');

        Route::get('/customers/{customer}/contacts', [ContactController::class, 'index']);
        Route::post('/customers/{customer}/contacts', [ContactController::class, 'store']);
        Route::put('/contacts/{contact}', [ContactController::class, 'update']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
    });

    // -------------------------------------------------------------------------
    // Invoice Module
    // -------------------------------------------------------------------------
    Route::prefix('invoice')->middleware([
        'auth:sanctum',
        'tenant.resolve',
        'tenant.active',
        'module:invoice',
    ])->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::post('/invoices', [InvoiceController::class, 'store'])
            ->middleware(['permission:invoice.create', 'usage:invoice.monthly_limit']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
            ->middleware('permission:invoice.view');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])
            ->middleware('permission:invoice.edit');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
            ->middleware('permission:invoice.delete');

        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])
            ->middleware('permission:invoice.send');

        Route::post('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])
            ->middleware('permission:invoice.payment.create');
    });

    // -------------------------------------------------------------------------
    // LMS Module
    // -------------------------------------------------------------------------
    Route::prefix('lms')->middleware([
        'auth:sanctum',
        'tenant.resolve',
        'tenant.active',
        'module:lms',
    ])->group(function () {
        Route::get('/courses', [CourseController::class, 'index']);
        Route::post('/courses', [CourseController::class, 'store'])
            ->middleware('permission:lms.course.manage');
        Route::get('/courses/{course}', [CourseController::class, 'show']);
        Route::put('/courses/{course}', [CourseController::class, 'update'])
            ->middleware('permission:lms.course.manage');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
            ->middleware('permission:lms.course.manage');

        Route::post('/courses/{course}/enrollments', [EnrollmentController::class, 'store'])
            ->middleware(['permission:lms.enrollment.create', 'usage:lms.learners.limit']);
    });

    // -------------------------------------------------------------------------
    // Helpdesk Module
    // -------------------------------------------------------------------------
    Route::prefix('helpdesk')->middleware([
        'auth:sanctum',
        'tenant.resolve',
        'tenant.active',
        'module:helpdesk',
    ])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store'])
            ->middleware('permission:helpdesk.ticket.create');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->middleware('permission:helpdesk.ticket.view');
        Route::put('/tickets/{ticket}', [TicketController::class, 'update'])
            ->middleware('permission:helpdesk.ticket.edit');
    });
});
