<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBirthdaysToCalendarJob;
use App\Jobs\UnsyncBirthdaysFromCalendarJob;
use App\Models\Alumnus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    /**
     * Display calendar settings page.
     */
    public function index(): Response
    {
        $isConfigured = $this->isGoogleCalendarConfigured();
        $alumniCount = Alumnus::whereNotNull('birth_date')->count();
        $syncStatus = Cache::get('calendar_sync_status');
        $unsyncStatus = Cache::get('calendar_unsync_status');

        return Inertia::render('settings/Calendar', [
            'isConfigured' => $isConfigured,
            'alumniCount' => $alumniCount,
            'calendarId' => config('services.google.calendar_id', ''),
            'syncStatus' => $syncStatus,
            'unsyncStatus' => $unsyncStatus,
        ]);
    }

    /**
     * Sync birthdays to Google Calendar (dispatches async job).
     */
    public function sync(Request $request): RedirectResponse
    {
        if (! $this->isGoogleCalendarConfigured()) {
            return back()->with('error', 'Google Calendar is not configured. Please add credentials.');
        }

        // Check if a sync is already running
        $status = Cache::get('calendar_sync_status');
        if ($status && $status['status'] === 'running') {
            return back()->with('warning', 'A sync is already in progress. Please wait.');
        }

        // Dispatch the job to run asynchronously
        SyncBirthdaysToCalendarJob::dispatch();

        return back()->with('success', 'Calendar sync started! This runs in the background. Refresh to see progress.');
    }

    /**
     * Remove all birthday events from Google Calendar (dispatches async job).
     */
    public function unsync(Request $request): RedirectResponse
    {
        if (! $this->isGoogleCalendarConfigured()) {
            return back()->with('error', 'Google Calendar is not configured. Please add credentials.');
        }

        // Check if an unsync is already running
        $status = Cache::get('calendar_unsync_status');
        if ($status && $status['status'] === 'running') {
            return back()->with('warning', 'An unsync is already in progress. Please wait.');
        }

        // Dispatch the job to run asynchronously
        UnsyncBirthdaysFromCalendarJob::dispatch();

        return back()->with('success', 'Removing birthday events started! This runs in the background. Refresh to see progress.');
    }

    /**
     * Check if Google Calendar is configured.
     */
    private function isGoogleCalendarConfigured(): bool
    {
        $credentialsPath = storage_path('app/google-calendar/service-account-credentials.json');

        return file_exists($credentialsPath) && ! empty(config('services.google.calendar_id'));
    }
}
