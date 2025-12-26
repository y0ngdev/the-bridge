<?php

namespace App\Jobs;

use App\Models\Alumnus;
use App\Services\CalendarSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class SyncBirthdaysToCalendarJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public function handle(CalendarSyncService $calendarService): void
    {
        $calendarService->markSyncRunning();

        $existingEvents = $calendarService->getExistingBirthdayEvents();

        $alumni = Alumnus::whereNotNull('birth_date')->get();
        $year = now()->year;
        $synced = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($alumni as $alumnus) {
            $birthDate = $alumnus->birth_date->copy()->year($year);

            if ($birthDate->isPast()) {
                $birthDate->addYear();
            }

            $eventName = "🎂 {$alumnus->name}'s Birthday";

            $existingEvent = $existingEvents->first(fn ($e) => $e->name === $eventName);

            if ($existingEvent) {
                $skipped++;

                continue;
            }

            try {
                $event = new Event;
                $event->name = $eventName;
                $event->startDate = $birthDate;
                $event->endDate = $birthDate;

                $event->googleEvent->setRecurrence(['RRULE:FREQ=YEARLY']);

                $event->save();

                $synced++;
            } catch (\Exception $e) {
                Log::error("Failed to create calendar event for {$alumnus->name}: {$e->getMessage()}");
                $failed++;
            }
        }

        $calendarService->markSyncComplete($synced, $skipped, $failed, $alumni->count());

        Log::info("Calendar sync complete: {$synced} created, {$skipped} skipped, {$failed} failed.");
    }

    public function failed(\Throwable $exception): void
    {
        app(CalendarSyncService::class)->markSyncFailed($exception->getMessage());

        Log::error('Calendar sync job failed: '.$exception->getMessage());
    }
}
