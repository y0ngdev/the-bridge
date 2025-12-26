<?php

namespace App\Jobs;

use App\Models\Alumnus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class SyncBirthdaysToCalendarJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300; // 5 minutes

    public function __construct() {}

    public function handle(): void
    {
        // Mark as in progress
        Cache::put('calendar_sync_status', [
            'status' => 'running',
            'message' => 'Sync in progress...',
            'started_at' => now()->toDateTimeString(),
        ], 3600);

        // Get existing birthday events to check for duplicates
        $existingEvents = $this->getExistingBirthdayEvents();

        $alumni = Alumnus::whereNotNull('birth_date')->get();
        $year = now()->year;
        $synced = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($alumni as $alumnus) {
            $birthDate = $alumnus->birth_date->copy()->year($year);

            // If birthday already passed this year, schedule for next year
            if ($birthDate->isPast()) {
                $birthDate->addYear();
            }

            $eventName = "🎂 {$alumnus->name}'s Birthday";

            // Check if event already exists
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

                // Add yearly recurrence
                $event->googleEvent->setRecurrence(['RRULE:FREQ=YEARLY']);

                $event->save();

                $synced++;
            } catch (\Exception $e) {
                Log::error("Failed to create calendar event for {$alumnus->name}: {$e->getMessage()}");
                $failed++;
            }
        }

        // Mark as complete
        Cache::put('calendar_sync_status', [
            'status' => 'complete',
            'message' => "Synced {$synced} events, {$skipped} skipped (exist)".($failed > 0 ? ", {$failed} failed" : ''),
            'synced' => $synced,
            'skipped' => $skipped,
            'failed' => $failed,
            'total' => $alumni->count(),
            'completed_at' => now()->toDateTimeString(),
        ], 86400); // Keep for 24 hours

        Log::info("Calendar sync complete: {$synced} created, {$skipped} skipped, {$failed} failed.");
    }

    private function getExistingBirthdayEvents()
    {
        try {
            return Event::get(now()->subYear(), now()->addYears(2))
                ->filter(fn ($e) => str_contains($e->name, '🎂') && str_contains($e->name, 'Birthday'));
        } catch (\Exception $e) {
            Log::warning("Could not fetch existing events: {$e->getMessage()}");

            return collect();
        }
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put('calendar_sync_status', [
            'status' => 'failed',
            'message' => 'Sync failed: '.$exception->getMessage(),
            'failed_at' => now()->toDateTimeString(),
        ], 86400);

        Log::error('Calendar sync job failed: '.$exception->getMessage());
    }
}
