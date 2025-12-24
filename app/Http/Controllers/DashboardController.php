<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
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

        $stats = [
            'total_alumni' => Alumnus::count(),
            'total_tenures' => Tenure::count(),
            'birthdays_today' => Alumnus::whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->count(),
            'new_this_month' => Alumnus::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->count(),
        ];

        $gender_distribution = [
            'male' => Alumnus::where('gender', 'M')->count(),
            'female' => Alumnus::where('gender', 'F')->count(),
            'unspecified' => Alumnus::whereNull('gender')->count(),
        ];

        $state_distribution = Alumnus::query()
            ->selectRaw('state, count(*) as total')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderByDesc('total')
            ->get();

        $unit_distribution = Alumnus::query()
            ->selectRaw('unit, count(*) as total')
            ->whereNotNull('unit')
            ->groupBy('unit')
            ->orderByDesc('total')
            ->get();

        $state_unit_breakdown = Alumnus::query()
            ->selectRaw('state, unit, count(*) as total')
            ->whereNotNull('state')
            ->whereNotNull('unit')
            ->groupBy('state', 'unit')
            ->orderBy('state')
            ->orderBy('unit')
            ->get();

        $recent_alumni = Alumnus::with('tenure')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->map(function ($alumnus) {
                return [
                    'id' => $alumnus->id,
                    'name' => $alumnus->name,
                    'email' => $alumnus->email,
                    'tenure' => $alumnus->tenure ? $alumnus->tenure->year : 'N/A',
                    'initials' => $this->getInitials($alumnus->name),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => Inertia::defer(fn () => $stats),
            'gender_distribution' => Inertia::defer(fn () => $gender_distribution),
            'state_distribution' => Inertia::defer(fn () => $state_distribution),
            'unit_distribution' => Inertia::defer(fn () => $unit_distribution),
            'state_unit_breakdown' => Inertia::defer(fn () => $state_unit_breakdown),
            'recent_alumni' => Inertia::defer(fn () => $recent_alumni),
        ]);
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
