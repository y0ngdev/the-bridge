<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRedemptionWeekAttendanceRequest;
use App\Models\RedemptionWeekAttendance;
use App\Models\RedemptionWeekSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RedemptionWeekAttendanceController extends Controller
{
    /**
     * Mark attendance for multiple alumni on a specific day.
     */
    public function store(StoreRedemptionWeekAttendanceRequest $request, RedemptionWeekSession $session): RedirectResponse
    {
        $validated = $request->validated();
        $eventDay = $validated['event_day'];
        $alumnusIds = $validated['alumnus_ids'];

        $created = 0;
        foreach ($alumnusIds as $alumnusId) {
            // Use firstOrCreate to avoid duplicates
            $attendance = RedemptionWeekAttendance::firstOrCreate(
                [
                    'session_id' => $session->id,
                    'alumnus_id' => $alumnusId,
                    'event_day' => $eventDay,
                ],
                [
                    'marked_by' => Auth::id(),
                ]
            );

            if ($attendance->wasRecentlyCreated) {
                $created++;
            }
        }

        $dayLabel = \App\Enums\RedemptionWeekDay::from($eventDay)->label();

        return redirect()->back()
            ->with('success', "{$created} attendance(s) marked for {$dayLabel}.");
    }

    /**
     * Remove attendance record (admin only).
     */
    public function destroy(RedemptionWeekSession $session, RedemptionWeekAttendance $attendance): RedirectResponse
    {
        // Ensure attendance belongs to session
        if ($attendance->session_id !== $session->id) {
            abort(404);
        }

        $attendance->delete();

        return redirect()->back()
            ->with('success', 'Attendance removed.');
    }

    /**
     * Bulk remove attendance for a day.
     */
    public function bulkDestroy(RedemptionWeekSession $session): RedirectResponse
    {
        $eventDay = request()->input('event_day');
        $alumnusIds = request()->input('alumnus_ids', []);

        if (empty($alumnusIds) || ! $eventDay) {
            return redirect()->back()->with('error', 'No attendances selected.');
        }

        $deleted = RedemptionWeekAttendance::where('session_id', $session->id)
            ->where('event_day', $eventDay)
            ->whereIn('alumnus_id', $alumnusIds)
            ->delete();

        return redirect()->back()
            ->with('success', "{$deleted} attendance(s) removed.");
    }
}
