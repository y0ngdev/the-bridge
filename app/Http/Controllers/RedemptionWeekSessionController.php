<?php

namespace App\Http\Controllers;

use App\Enums\RedemptionWeekDay;
use App\Http\Requests\StoreRedemptionWeekSessionRequest;
use App\Http\Requests\UpdateRedemptionWeekSessionRequest;
use App\Models\Alumnus;
use App\Models\RedemptionWeekSession;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RedemptionWeekSessionController extends Controller
{
    /**
     * Display a listing of sessions with analytics.
     */
    public function index(): Response
    {
        $sessions = RedemptionWeekSession::query()
            ->orderByDesc('year')
            ->orderByDesc('is_active')
            ->withCount('attendances')
            ->simplePaginate(15);

        // Add analytics to each session
        $sessions->through(function ($session) {
            $session->setAttribute('attendance_stats', $session->getAttendanceStats());
            $session->setAttribute('unique_attendees', $session->total_attendees);
            $session->setAttribute('perfect_count', $session->perfect_attendance_count);

            return $session;
        });

        $totalAlumni = Alumnus::count();

        return Inertia::render('redemption-week/Index', [
            'sessions' => $sessions,
            'totalAlumni' => $totalAlumni,
            'eventDays' => collect(RedemptionWeekDay::ordered())->map(fn($day) => [
                'value' => $day->value,
                'label' => $day->label(),
            ]),
        ]);
    }

    /**
     * Store a newly created session.
     */
    public function store(StoreRedemptionWeekSessionRequest $request): RedirectResponse
    {
        RedemptionWeekSession::create($request->validated());

        return redirect()->route('redemption-week.index')
            ->with('success', 'Redemption Week session created successfully.');
    }

    /**
     * Display a session with attendance details.
     */
    public function show(RedemptionWeekSession $session): Response
    {
        $session->load(['attendances.alumnus', 'attendances.markedBy']);

        $attendanceByDay = [];
        foreach (RedemptionWeekDay::ordered() as $day) {
            $dayAttendances = $session->attendances
                ->where('event_day', $day)
                ->values();

            $attendanceByDay[$day->value] = [
                'label' => $day->label(),
                'attendances' => $dayAttendances,
                'count' => $dayAttendances->count(),
            ];
        }

        // Get all alumni for attendance marking
        $alumni = Alumnus::query()
            ->orderBy('name')
            ->select(['id', 'name', 'email'])
            ->get();

        // Get attendance stats
        $stats = $session->getAttendanceStats();
        $totalAlumni = Alumnus::count();

        return Inertia::render('redemption-week/Show', [
            'session' => $session,
            'attendanceByDay' => $attendanceByDay,
            'alumni' => $alumni,
            'stats' => $stats,
            'totalAlumni' => $totalAlumni,
            'totalAttendees' => $session->total_attendees,
            'perfectAttendance' => $session->perfect_attendance_count,
            'eventDays' => collect(RedemptionWeekDay::ordered())->map(fn($day) => [
                'value' => $day->value,
                'label' => $day->label(),
            ]),
        ]);
    }

    /**
     * Update the session.
     */
    public function update(UpdateRedemptionWeekSessionRequest $request, RedemptionWeekSession $session): RedirectResponse
    {
        $session->update($request->validated());

        return redirect()->route('redemption-week.index')
            ->with('success', 'Redemption Week session updated successfully.');
    }

    /**
     * Remove the session and all its attendances.
     */
    public function destroy(RedemptionWeekSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('redemption-week.index')
            ->with('success', 'Redemption Week session deleted successfully.');
    }
}
