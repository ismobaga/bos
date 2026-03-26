<?php

namespace App\Providers;

use App\Contracts\AuditLoggerInterface;
use App\Contracts\LicensingManagerInterface;
use App\Contracts\NotificationDispatcherInterface;
use App\Contracts\TenantResolverInterface;
use App\Modules\Audit\Application\Services\AuditLogger;
use App\Modules\Licensing\Application\Services\LicensingManager;
use App\Modules\Notifications\Application\Services\NotificationDispatcher;
use App\Modules\Tenancy\Application\Services\TenantResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LicensingManagerInterface::class, LicensingManager::class);
        $this->app->bind(AuditLoggerInterface::class, AuditLogger::class);
        $this->app->bind(TenantResolverInterface::class, TenantResolver::class);
        $this->app->bind(NotificationDispatcherInterface::class, NotificationDispatcher::class);
    }

    public function boot(): void
    {
        //
    }
}
