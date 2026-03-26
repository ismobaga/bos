<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks as per blueprint
Schedule::command('crommix:reset-monthly-counters')->monthlyOn(1, '00:00');
Schedule::command('crommix:detect-overdue-invoices')->dailyAt('08:00');
Schedule::command('crommix:expire-trials')->dailyAt('06:00');
Schedule::command('crommix:suspend-unpaid-subscriptions')->dailyAt('07:00');
Schedule::command('crommix:retry-failed-webhooks')->everyThirtyMinutes();
Schedule::command('crommix:clean-temp-files')->weekly();
