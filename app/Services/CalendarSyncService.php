<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class CalendarSyncService
{
    /**
     * Get existing birthday events from Google Calendar.
     */
    public function getExistingBirthdayEvents(): Collection
    {
        try {
            return Event::get(now()->subYear(), now()->addYears(2))
                ->filter(fn ($e) => str_contains($e->name, '🎂') && str_contains($e->name, 'Birthday'));
        } catch (\Exception $e) {
            Log::warning("Could not fetch existing events: {$e->getMessage()}");

            return collect();
        }
    }

    /**
     * Set sync status in cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function setSyncStatus(array $data, int $ttl = 3600): void
    {
        Cache::put('calendar_sync_status', $data, $ttl);
    }

    /**
     * Set unsync status in cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function setUnsyncStatus(array $data, int $ttl = 3600): void
    {
        Cache::put('calendar_unsync_status', $data, $ttl);
    }

    /**
     * Mark sync as running.
     */
    public function markSyncRunning(): void
    {
        $this->setSyncStatus([
            'status' => 'running',
            'message' => 'Sync in progress...',
            'started_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Mark sync as complete.
     */
    public function markSyncComplete(int $synced, int $skipped, int $failed, int $total): void
    {
        $this->setSyncStatus([
            'status' => 'complete',
            'message' => "Synced {$synced} events, {$skipped} skipped (exist)".($failed > 0 ? ", {$failed} failed" : ''),
            'synced' => $synced,
            'skipped' => $skipped,
            'failed' => $failed,
            'total' => $total,
            'completed_at' => now()->toDateTimeString(),
        ], 86400);
    }

    /**
     * Mark sync as failed.
     */
    public function markSyncFailed(string $message): void
    {
        $this->setSyncStatus([
            'status' => 'failed',
            'message' => 'Sync failed: '.$message,
            'failed_at' => now()->toDateTimeString(),
        ], 86400);
    }

    /**
     * Mark unsync as running.
     */
    public function markUnsyncRunning(): void
    {
        $this->setUnsyncStatus([
            'status' => 'running',
            'message' => 'Removing birthday events...',
            'started_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Mark unsync as complete.
     */
    public function markUnsyncComplete(int $deleted, int $failed): void
    {
        $this->setUnsyncStatus([
            'status' => 'complete',
            'message' => "Removed {$deleted} birthday events".($failed > 0 ? ", {$failed} failed" : ''),
            'deleted' => $deleted,
            'failed' => $failed,
            'completed_at' => now()->toDateTimeString(),
        ], 86400);

        Cache::forget('calendar_sync_status');
    }

    /**
     * Mark unsync as failed.
     */
    public function markUnsyncFailed(string $message): void
    {
        $this->setUnsyncStatus([
            'status' => 'failed',
            'message' => 'Unsync failed: '.$message,
            'failed_at' => now()->toDateTimeString(),
        ], 86400);
    }
}
