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

        return Inertia::render('analytics/Outreach', [
            'active_session' => $activeSession,
            'stats' => Inertia::defer(function () use ($activeSession) {
                if (! $activeSession) {
                    return [
                        'total_reached' => 0,
                        'total_logs' => 0,
                        'success_rate' => 0,
                        'total_alumni' => 0,
                        'response_rate' => 0,
                    ];
                }

                $logsQuery = CommunicationLog::where('session_id', $activeSession->id);
                $totalLogs = $logsQuery->count();
                $totalReached = $logsQuery->clone()->distinct('alumnus_id')->count('alumnus_id');
                $totalAlumni = Alumnus::count();
                $responseRate = $totalAlumni > 0 ? round(($totalReached / $totalAlumni) * 100) : 0;
                $successfulLogs = $logsQuery->clone()->where('outcome', 'successful')->count();
                $successRate = $totalLogs > 0 ? round(($successfulLogs / $totalLogs) * 100) : 0;

                
dd(round((30 / $totalAlumni) * 100) );
                return [
                    'total_reached' => $totalReached,
                    'total_logs' => $totalLogs,
                    'success_rate' => $successRate,
                    'total_alumni' => $totalAlumni,
                    'response_rate' => $responseRate,
                ];
            }),
            'tenure_breakdown' => Inertia::defer(function () use ($activeSession) {
                if (! $activeSession) {
                    return collect();
                }

                return CommunicationLog::query()
                    ->join('alumni', 'communication_logs.alumnus_id', '=', 'alumni.id')
                    ->join('tenures', 'alumni.tenure_id', '=', 'tenures.id')
                    ->where('communication_logs.session_id', $activeSession->id)
                    ->selectRaw('tenures.name as tenure_name, tenures.year as tenure_year, COUNT(DISTINCT communication_logs.alumnus_id) as reached, COUNT(*) as total_logs')
                    ->groupBy('tenures.id', 'tenures.name', 'tenures.year')
                    ->orderByDesc('reached')
                    ->get();
            }),
            'type_breakdown' => Inertia::defer(function () use ($activeSession) {
                if (! $activeSession) {
                    return collect();
                }

                return CommunicationLog::where('session_id', $activeSession->id)
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->map(fn ($item) => ['type' => $item->type->label(), 'count' => $item->count]);
            }),
            'outcome_breakdown' => Inertia::defer(function () use ($activeSession) {
                if (! $activeSession) {
                    return collect();
                }

                return CommunicationLog::where('session_id', $activeSession->id)
                    ->selectRaw('outcome, COUNT(*) as count')
                    ->groupBy('outcome')
                    ->get()
                    ->map(fn ($item) => ['outcome' => $item->outcome->label(), 'count' => $item->count]);
            }),
            'gender_breakdown' => Inertia::defer(function () use ($activeSession) {
                if (! $activeSession) {
                    return collect();
                }

                return CommunicationLog::query()
                    ->join('alumni', 'communication_logs.alumnus_id', '=', 'alumni.id')
                    ->where('communication_logs.session_id', $activeSession->id)
                    ->selectRaw("COALESCE(alumni.gender, 'unspecified') as gender, COUNT(DISTINCT communication_logs.alumnus_id) as count")
                    ->groupBy('alumni.gender')
                    ->get()
                    ->map(fn ($item) => ['gender' => ucfirst($item->gender ?? 'Unspecified'), 'count' => $item->count]);
            }),
        ]);
    }
}
