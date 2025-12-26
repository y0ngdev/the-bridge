<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule email backup every 4 months (on the 1st of every 4th month at 2 AM)
Schedule::command('app:backup-database')->cron('0 2 1 */4 *')->onOneServer();

// Birthday notifications
Schedule::command('app:send-birthday-notifications --daily')->dailyAt('08:00')->onOneServer();
Schedule::command('app:send-birthday-notifications --weekly')->weeklyOn(1, '08:00')->onOneServer();
Schedule::command('app:send-birthday-notifications --monthly')->monthlyOn(1, '08:00')->onOneServer();

// Sync birthdays to Google Calendar weekly
Schedule::command('app:sync-birthdays-calendar')->weeklyOn(1, '09:00')->onOneServer();
