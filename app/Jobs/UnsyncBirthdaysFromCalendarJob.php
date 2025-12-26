<?php

namespace App\Jobs;

use App\Services\CalendarSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UnsyncBirthdaysFromCalendarJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public function handle(CalendarSyncService $calendarService): void
    {
        $calendarService->markUnsyncRunning();

        $deleted = 0;
        $failed = 0;

        try {
            $events = $calendarService->getExistingBirthdayEvents();

            foreach ($events as $event) {
                try {
                    $event->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    Log::error("Failed to delete calendar event '{$event->name}': {$e->getMessage()}");
                    $failed++;
                }
            }

            $calendarService->markUnsyncComplete($deleted, $failed);

            Log::info("Calendar unsync complete: {$deleted} deleted, {$failed} failed.");

        } catch (\Exception $e) {
            $calendarService->markUnsyncFailed($e->getMessage());

            Log::error('Calendar unsync failed: '.$e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        app(CalendarSyncService::class)->markUnsyncFailed($exception->getMessage());

        Log::error('Calendar unsync job failed: '.$exception->getMessage());
    }
}
