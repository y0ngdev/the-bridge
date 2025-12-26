<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
use App\Models\CommunicationLog;
use App\Models\Tenure;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        $today = Carbon::today();

        return Inertia::render('Dashboard', [
            // Core Stats
            'stats' => Inertia::defer(fn () => [
                'total_alumni' => Alumnus::count(),
                'total_tenures' => Tenure::count(),
                'birthdays_today' => Alumnus::whereMonth('birth_date', $today->month)
                    ->whereDay('birth_date', $today->day)
                    ->count(),
                'new_this_month' => Alumnus::whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->count(),
                'futa_staff' => Alumnus::where('is_futa_staff', true)->count(),
                'with_contact' => Alumnus::where(function ($q) {
                    $q->whereNotNull('email')
                        ->orWhereJsonLength('phones', '>', 0);
                })->count(),
            ]),

            // Gender Distribution (simple counts)
            'gender_distribution' => Inertia::defer(fn () => [
                'male' => Alumnus::where('gender', 'M')->count(),
                'female' => Alumnus::where('gender', 'F')->count(),
                'unspecified' => Alumnus::whereNull('gender')->count(),
            ]),

            // Top Units (only top 5)
            'unit_distribution' => Inertia::defer(
                fn () => Alumnus::query()
                    ->selectRaw('unit, count(*) as total')
                    ->whereNotNull('unit')
                    ->groupBy('unit')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get()
            ),

            // Recent Alumni
            'recent_alumni' => Inertia::defer(
                fn () => Alumnus::with('tenure')
                    ->orderBy('id', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn ($a) => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'email' => $a->email,
                        'tenure' => $a->tenure?->year ?? 'N/A',
                        'initials' => $this->getInitials($a->name),
                    ])
            ),

            // Upcoming Birthdays (next 14 days)
            'upcoming_birthdays' => Inertia::defer(fn () => $this->getUpcomingBirthdays(5)),

            // Current Executives
            'current_executives' => Inertia::defer(
                fn () => Alumnus::whereNotNull('current_exco_office')
                    ->where('current_exco_office', '!=', '')
                    ->orderBy('current_exco_office')
                    ->take(6)
                    ->get()
                    ->map(fn ($a) => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'office' => $a->current_exco_office,
                        'initials' => $this->getInitials($a->name),
                    ])
            ),

            // Department Distribution (top 5) - uses department relationship
            'department_distribution' => Inertia::defer(
                fn () => Alumnus::query()
                    ->join('departments', 'alumni.department_id', '=', 'departments.id')
                    ->selectRaw('departments.name as department, departments.code, count(*) as total')
                    ->groupBy('departments.id', 'departments.name', 'departments.code')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get()
            ),

            // Activity Summary (current session)
            'activity_summary' => Inertia::defer(fn () => $this->getActivitySummary()),
        ]);
    }

    /**
     * Get upcoming birthdays.
     */
    private function getUpcomingBirthdays(int $limit): array
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays(14);

        return Alumnus::whereNotNull('birth_date')
            ->get()
            ->map(function ($alumnus) use ($today) {
                $birthDate = $alumnus->birth_date->copy()->year($today->year);
                if ($birthDate->lt($today)) {
                    $birthDate->addYear();
                }
                $daysUntil = $today->diffInDays($birthDate, false);

                return [
                    'id' => $alumnus->id,
                    'name' => $alumnus->name,
                    'birth_date' => $birthDate->format('M j'),
                    'days_until' => $daysUntil,
                    'initials' => $this->getInitials($alumnus->name),
                ];
            })
            ->filter(fn ($a) => $a['days_until'] >= 0 && $a['days_until'] <= 14)
            ->sortBy('days_until')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Get activity summary for current session.
     */
    private function getActivitySummary(): array
    {
        $activeSession = Tenure::active()->first();

        if (! $activeSession) {
            return [
                'session' => null,
                'total_logs' => 0,
                'alumni_reached' => 0,
            ];
        }

        $logs = CommunicationLog::where('session_id', $activeSession->id);

        return [
            'session' => $activeSession->name.' ('.$activeSession->year.')',
            'total_logs' => $logs->count(),
            'alumni_reached' => $logs->distinct('alumnus_id')->count('alumnus_id'),
        ];
    }

    /**
     * Get initials from name.
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= strtoupper($w[0] ?? '');
        }

        return substr($initials, 0, 2);
    }
}
