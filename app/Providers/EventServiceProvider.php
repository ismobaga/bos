<?php

namespace App\Providers;

use App\Modules\Invoice\Domain\Events\InvoiceCreated;
use App\Modules\Invoice\Domain\Events\InvoiceOverdue;
use App\Modules\Invoice\Domain\Events\InvoicePaid;
use App\Modules\Invoice\Domain\Events\InvoiceSent;
use App\Modules\Invoice\Infrastructure\Listeners\IncrementInvoiceUsage;
use App\Modules\Invoice\Infrastructure\Listeners\ScheduleOverdueReminder;
use App\Modules\LMS\Domain\Events\LearnerEnrolled;
use App\Modules\LMS\Infrastructure\Listeners\SendEnrollmentWelcomeEmail;
use App\Modules\Licensing\Domain\Events\SubscriptionActivated;
use App\Modules\Licensing\Domain\Events\SubscriptionChanged;
use App\Modules\Licensing\Infrastructure\Listeners\RecomputeTenantEntitlements;
use App\Modules\Tenancy\Domain\Events\TenantCreated;
use App\Modules\Tenancy\Domain\Events\TenantSuspended;
use App\Modules\Helpdesk\Domain\Events\TicketOpened;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceCreated::class => [
            IncrementInvoiceUsage::class,
        ],
        InvoiceOverdue::class => [
            ScheduleOverdueReminder::class,
        ],
        LearnerEnrolled::class => [
            SendEnrollmentWelcomeEmail::class,
        ],
        SubscriptionChanged::class => [
            RecomputeTenantEntitlements::class,
        ],
        SubscriptionActivated::class => [
            RecomputeTenantEntitlements::class,
        ],
    ];
}
