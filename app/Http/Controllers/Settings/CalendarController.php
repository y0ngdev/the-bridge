<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Alumnus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

        return Inertia::render('settings/Calendar', [
            'isConfigured' => $isConfigured,
            'alumniCount' => $alumniCount,
            'calendarId' => env('GOOGLE_CALENDAR_ID', ''),
            'syncStatus' => $syncStatus,
        ]);
    }

    /**
     * Sync birthdays to Google Calendar.
     */
    public function sync(Request $request)
    {
        if (! $this->isGoogleCalendarConfigured()) {
            return back()->with('error', 'Google Calendar is not configured. Please add credentials.');
        }

        try {
            // Run the sync command directly
            $exitCode = Artisan::call('app:sync-birthdays-calendar');
            $output = Artisan::output();

            if ($exitCode === 0) {
                // Parse results from output
                preg_match('/(\d+) events created, (\d+) skipped/', $output, $matches);
                $created = $matches[1] ?? 0;
                $skipped = $matches[2] ?? 0;

                return back()->with('success', "Synced {$created} events, {$skipped} skipped (already exist).");
            }

            return back()->with('error', 'Sync failed. Check logs for details.');
        } catch (\Exception $e) {
            return back()->with('error', 'Sync failed: '.$e->getMessage());
        }
    }

    /**
     * Check if Google Calendar is configured.
     */
    private function isGoogleCalendarConfigured(): bool
    {
        $credentialsPath = storage_path('app/google-calendar/service-account-credentials.json');

        return file_exists($credentialsPath) && ! empty(env('GOOGLE_CALENDAR_ID'));
    }
}
