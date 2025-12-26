<?php

namespace App\Console\Commands;

use App\Models\Alumnus;
use Illuminate\Console\Command;
use Spatie\GoogleCalendar\Event;

class SyncBirthdaysToCalendar extends Command
{
    protected $signature = 'app:sync-birthdays-calendar 
                            {--dry-run : Show what would be synced without creating events}
                            {--force : Force recreate all events (delete existing first)}';

    protected $description = 'Sync alumni birthdays to Google Calendar as recurring events';

    public function handle(): int
    {
        $alumni = Alumnus::whereNotNull('birth_date')->get();

        $this->info("Found {$alumni->count()} alumni with birth dates.");

        if ($alumni->isEmpty()) {
            $this->warn('No alumni with birth dates found.');

            return self::SUCCESS;
        }

        // Get existing birthday events to check for duplicates
        $existingEvents = $this->getExistingBirthdayEvents();
        $this->info("Found {$existingEvents->count()} existing birthday events in calendar.");

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $synced = 0;
        $skipped = 0;
        $year = now()->year;

        foreach ($alumni as $alumnus) {
            $birthDate = $alumnus->birth_date->copy()->year($year);

            // If birthday already passed this year, schedule for next year
            if ($birthDate->isPast()) {
                $birthDate->addYear();
            }

            $eventName = "🎂 {$alumnus->name}'s Birthday";

            // Check if event already exists
            $existingEvent = $existingEvents->first(fn ($e) => $e->name === $eventName);

            if ($existingEvent && ! $force) {
                if ($dryRun) {
                    $this->line("Skipping (exists): {$eventName}");
                }
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("Would create: {$eventName} on {$birthDate->format('M j, Y')} (yearly recurring)");
            } else {
                try {
                    // Delete existing if force mode
                    if ($existingEvent && $force) {
                        $existingEvent->delete();
                        $this->line("Deleted existing: {$eventName}");
                    }

                    $event = new Event;
                    $event->name = $eventName;
                    $event->startDate = $birthDate;
                    $event->endDate = $birthDate;

                    // Add yearly recurrence
                    $event->googleEvent->setRecurrence(['RRULE:FREQ=YEARLY']);

                    $event->save();

                    $this->info("Created: {$eventName}");
                    $synced++;
                } catch (\Exception $e) {
                    $this->error("Failed to create event for {$alumnus->name}: {$e->getMessage()}");
                }
            }
        }

        if ($dryRun) {
            $this->info("\nDry run complete. {$alumni->count()} alumni checked, {$skipped} already exist.");
        } else {
            $this->info("\nSync complete. {$synced} events created, {$skipped} skipped (already exist).");
        }

        return self::SUCCESS;
    }

    private function getExistingBirthdayEvents()
    {
        try {
            // Get events from now to 2 years from now (covers all possible birthdays)
            return Event::get(now()->subYear(), now()->addYears(2))
                ->filter(fn ($e) => str_contains($e->name, '🎂') && str_contains($e->name, 'Birthday'));
        } catch (\Exception $e) {
            $this->warn("Could not fetch existing events: {$e->getMessage()}");

            return collect();
        }
    }
}
