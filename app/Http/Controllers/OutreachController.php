<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
use App\Models\CommunicationLog;
use App\Models\Tenure;
use Inertia\Inertia;
use Inertia\Response;

class OutreachController extends Controller
{
    public function index(): Response
    {
        $activeSession = Tenure::active()->first();

        $stats = [
            'total_reached' => 0,
            'total_logs' => 0,
            'success_rate' => 0,
            'total_alumni' => 0,
            'response_rate' => 0,
        ];

        $tenureBreakdown = collect();
        $typeBreakdown = collect();
        $outcomeBreakdown = collect();
        $genderBreakdown = collect();

        if ($activeSession) {
            $logsQuery = CommunicationLog::where('session_id', $activeSession->id);

            $stats['total_logs'] = $logsQuery->count();
            $stats['total_reached'] = $logsQuery->clone()->distinct('alumnus_id')->count('alumnus_id');
            $stats['total_alumni'] = Alumnus::count();
            $stats['response_rate'] = $stats['total_alumni'] > 0
                ? round(($stats['total_reached'] / $stats['total_alumni']) * 100)
                : 0;

            $successfulLogs = $logsQuery->clone()->where('outcome', 'successful')->count();
            $stats['success_rate'] = $stats['total_logs'] > 0
                ? round(($successfulLogs / $stats['total_logs']) * 100)
                : 0;

            // Breakdown by Tenure (Alumni Class Set)
            $tenureBreakdown = CommunicationLog::query()
                ->join('alumni', 'communication_logs.alumnus_id', '=', 'alumni.id')
                ->join('tenures', 'alumni.tenure_id', '=', 'tenures.id')
                ->where('communication_logs.session_id', $activeSession->id)
                ->selectRaw('tenures.name as tenure_name, tenures.year as tenure_year, COUNT(DISTINCT communication_logs.alumnus_id) as reached, COUNT(*) as total_logs')
                ->groupBy('tenures.id', 'tenures.name', 'tenures.year')
                ->orderByDesc('reached')
                ->get();

            // Breakdown by Type
            $typeBreakdown = $logsQuery->clone()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->map(fn ($item) => ['type' => $item->type->label(), 'count' => $item->count]);

            // Breakdown by Outcome
            $outcomeBreakdown = $logsQuery->clone()
                ->selectRaw('outcome, COUNT(*) as count')
                ->groupBy('outcome')
                ->get()
                ->map(fn ($item) => ['outcome' => $item->outcome->label(), 'count' => $item->count]);

            // Breakdown by Gender (of reached alumni)
            $genderBreakdown = CommunicationLog::query()
                ->join('alumni', 'communication_logs.alumnus_id', '=', 'alumni.id')
                ->where('communication_logs.session_id', $activeSession->id)
                ->selectRaw("COALESCE(alumni.gender, 'unspecified') as gender, COUNT(DISTINCT communication_logs.alumnus_id) as count")
                ->groupBy('alumni.gender')
                ->get()
                ->map(fn ($item) => ['gender' => ucfirst($item->gender ?? 'Unspecified'), 'count' => $item->count]);
        }

        return Inertia::render('analytics/Outreach', [
            'active_session' => $activeSession,
            'stats' => $stats,
            'tenure_breakdown' => $tenureBreakdown,
            'type_breakdown' => $typeBreakdown,
            'outcome_breakdown' => $outcomeBreakdown,
            'gender_breakdown' => $genderBreakdown,
        ]);
    }
}
