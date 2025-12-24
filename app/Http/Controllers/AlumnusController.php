<?php

namespace App\Http\Controllers;

use App\Enums\NigerianState;
use App\Enums\PastExcoOffice;
use App\Enums\Unit;
use App\Exports\AlumnusExport;
use App\Imports\AlumnusImport;
use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AlumnusController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Alumnus::with('tenure');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by tenure
        if ($request->filled('tenure_id')) {
            $query->where('tenure_id', $request->tenure_id);
        }

        // Filter by unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->whereRaw('LOWER(gender) = ?', [strtolower($request->gender)]);
        }

        return Inertia::render('alumni/Index', [
            'alumni' => $query->latest()->paginate()->withQueryString(),
            'tenures' => Tenure::orderBy('year', 'desc')->get(['id', 'year']),
            'units' => collect(Unit::cases())->map(fn($u) => ['value' => $u->value, 'label' => $u->value]),
            'states' => collect(NigerianState::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->value]),
            'pastExcoOffices' => collect(PastExcoOffice::cases())->map(fn($p) => ['value' => $p->value, 'label' => $p->value]),
            'departments' => Department::options(),
            'filters' => $request->only(['search', 'tenure_id', 'unit', 'state', 'gender']),
        ]);
    }

    public function show(Alumnus $alumnus): Response
    {
        return Inertia::render('alumni/Show', [
            'alumnus' => $alumnus->load(['tenure', 'communicationLogs.user']),
            'departments' => Department::options(),
        ]);
    }

    public function store(\App\Http\Requests\StoreAlumnusRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Parse birth_date from various formats (e.g., "20-12", "20/12", "20 Dec")
        $validated['birth_date'] = $this->parseBirthDate($validated['birth_date'] ?? null);

        Alumnus::create($validated);

        return back()->with('success', 'Alumnus created successfully.');
    }

    public function update(\App\Http\Requests\UpdateAlumnusRequest $request, Alumnus $alumnus): RedirectResponse
    {
        $validated = $request->validated();

        // Parse birth_date from various formats (e.g., "20-12", "20/12", "20 Dec")
        $validated['birth_date'] = $this->parseBirthDate($validated['birth_date'] ?? null);

        $alumnus->update($validated);

        return back()->with('success', 'Alumnus updated successfully.');
    }

    public function destroy(Alumnus $alumnus): RedirectResponse
    {
        $alumnus->delete();

        return back()->with('success', 'Alumnus deleted successfully.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $fields = $request->input('fields', []);
        $filters = [
            'tenure_id' => $request->input('tenure_id'),
            'unit' => $request->input('unit'),
            'state' => $request->input('state'),
            'gender' => $request->input('gender'),
        ];

        // Generate dynamic filename based on filters
        $filename = 'alumni';
        if ($request->filled('state')) {
            $stateName = strtolower(str_replace(' ', '-', $request->state));
            $filename .= "-in-{$stateName}-state";
        }
        if ($request->filled('unit')) {
            $unitName = strtolower(str_replace(' ', '-', $request->unit));
            $filename .= "-{$unitName}";
        }
        if ($request->filled('tenure_id')) {
            $tenure = Tenure::find($request->tenure_id);
            if ($tenure) {
                $filename .= "-{$tenure->year}-tenure";
            }
        }
        $filename .= '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AlumnusExport($fields, $filters), $filename);
    }

    public function birthdays(): Response
    {
        $today = now();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        $allAlumni = Alumnus::whereNotNull('birth_date')
            ->orderByRaw("strftime('%m', birth_date), strftime('%d', birth_date)")
            ->get();

        // Filter for today's birthdays (comparing month and day only)
        $todayBirthdays = $allAlumni->filter(function ($alumnus) use ($today) {
            return $alumnus->birth_date->month === $today->month
                && $alumnus->birth_date->day === $today->day;
        })->values();

        // Filter for this week's birthdays (comparing month and day only)
        $thisWeek = $allAlumni->filter(function ($alumnus) use ($startOfWeek, $endOfWeek) {
            $birthDate = $alumnus->birth_date->copy()->year(now()->year);

            return $birthDate->between($startOfWeek, $endOfWeek);
        })->values();

        // Filter for this month's birthdays
        $thisMonth = $allAlumni->filter(function ($alumnus) use ($today) {
            return $alumnus->birth_date->month === $today->month;
        })->values();

        return Inertia::render('alumni/Birthdays', [
            'today' => $todayBirthdays,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'allByMonth' => $allAlumni->groupBy(fn($alumnus) => $alumnus->birth_date->format('F')),
        ]);
    }

    public function distribution(Request $request): Response
    {
        $query = Alumnus::with('tenure');

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Filter by unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Filter by tenure
        if ($request->filled('tenure_id')) {
            $query->where('tenure_id', $request->tenure_id);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Get alumni counts by state for sidebar
        $stateDistribution = Alumnus::selectRaw('state, COUNT(*) as count')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderBy('state')
            ->get()
            ->mapWithKeys(fn($item) => [$item->state->value => $item->count]);

        return Inertia::render('alumni/Distribution', [
            'alumni' => $query->latest()->paginate(20)->withQueryString(),
            'stateDistribution' => $stateDistribution,
            'units' => collect(Unit::cases())->map(fn($u) => ['value' => $u->value, 'label' => $u->value]),
            'states' => collect(NigerianState::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->value]),
            'tenures' => Tenure::orderBy('year', 'desc')->get()->map(fn($t) => ['value' => $t->id, 'label' => $t->year]),
            'filters' => $request->only(['state', 'unit', 'tenure_id', 'search']),
        ]);
    }

    public function executives(): Response
    {
        // Get all alumni with current exco office positions
        $executives = Alumnus::with('tenure')
            ->whereNotNull('current_exco_office')
            ->orderBy('current_exco_office')
            ->get();

        // Group executives by position category
        $centralExco = $executives->filter(fn($a) => in_array($a->current_exco_office, [
            'President',
            'Vice President',
            'General Secretary',
            'Financial Secretary',
            'Prayer Secretary',
            'Bible Study Secretary',
        ]));

        $coordinators = $executives->filter(fn($a) => str_contains($a->current_exco_office ?? '', 'Coordinator'));

        $otherPositions = $executives->filter(
            fn($a) => !$centralExco->contains($a) && !$coordinators->contains($a)
        );

        return Inertia::render('alumni/Executives', [
            'centralExco' => $centralExco->values(),
            'coordinators' => $coordinators->values(),
            'otherPositions' => $otherPositions->values(),
            'totalCount' => $executives->count(),
        ]);
    }

    public function importStore(\App\Http\Requests\ImportAlumnusRequest $request): RedirectResponse
    {
        Excel::import(new AlumnusImport($request->tenure_id), $request->file('file'));

        return back()->with('success', 'Alumni imported successfully.');
    }

    /**
     * Parse a birth date from various formats (day-month only).
     * Supports: "20-12", "20/12", "20 Dec", "20 December", or full dates.
     */
    private function parseBirthDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            $value = trim($value);

            // Handle "Day-Month" or "Day/Month" formats (e.g., "20-12", "20/12")
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $value, $matches)) {
                return Carbon::create(2000, $matches[2], $matches[1]);
            }

            // Handle "Day Month" formats (e.g., "20 Dec", "20 December")
            if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)$/', $value, $matches)) {
                return Carbon::parse("{$matches[1]} {$matches[2]} 2000");
            }

            // Try standard Carbon parsing for other formats (full dates)
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
