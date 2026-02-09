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
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AlumnusController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Alumnus::with(['tenure', 'department'])
            ->search($request->search)
            ->byTenure($request->tenure_id)
            ->byUnit($request->unit)
            ->byState($request->state)
            ->byGender($request->gender);

        return Inertia::render('alumni/Index', [
            'alumni' => $query->latest()->paginate()->withQueryString(),
            'tenures' => Tenure::orderBy('year', 'desc')->get(['id', 'year']),
            'units' => collect(Unit::cases())->map(fn($u) => [
                'value' => $u->value,
                'label' => $u->value === "President's Unit" ? "President's Unit (Non Worker)" : $u->value,
            ]),
            'states' => collect(NigerianState::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->value]),
            'pastExcoOffices' => collect(PastExcoOffice::cases())->map(fn($p) => ['value' => $p->value, 'label' => $p->value]),
            'departments' => Department::options(),
            'filters' => $request->only(['search', 'tenure_id', 'unit', 'state', 'gender']),
        ]);
    }

    public function show(Alumnus $alumnus): Response
    {
        return Inertia::render('alumni/Show', [
            'alumnus' => $alumnus->load(['tenure', 'department', 'communicationLogs.user']),
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

    public function destroy(Request $request, Alumnus $alumnus): RedirectResponse
    {
        // Verify password
        if (!Hash::check($request->input('password'), $request->user()->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

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
        $isSqlite = config('database.default') === 'sqlite';

        return Inertia::render('alumni/Birthdays', [
            'today' => Inertia::defer(function () {
                $today = now();

                return Alumnus::select(['id', 'name', 'email', 'phones', 'birth_date', 'photo'])
                    ->whereNotNull('birth_date')
                    ->whereMonth('birth_date', $today->month)
                    ->whereDay('birth_date', $today->day)
                    ->get();
            }),
            'thisWeek' => Inertia::defer(function () use ($isSqlite) {
                $today = now();
                $startOfWeek = $today->copy()->startOfWeek();
                $endOfWeek = $today->copy()->endOfWeek();

                $dates = collect(\Carbon\CarbonPeriod::create($startOfWeek, $endOfWeek))
                    ->map(fn($date) => $date->format('m-d'))
                    ->values()
                    ->all();

                $placeholders = implode(',', array_fill(0, count($dates), '?'));
                $dateExpr = $isSqlite
                    ? "strftime('%m-%d', birth_date)"
                    : "DATE_FORMAT(birth_date, '%m-%d')";

                return Alumnus::select(['id', 'name', 'email', 'phones', 'birth_date', 'photo'])
                    ->whereNotNull('birth_date')
                    ->whereRaw("$dateExpr IN ($placeholders)", $dates)
                    ->get();
            }),
            'thisMonth' => Inertia::defer(function () {
                $today = now();

                return Alumnus::select(['id', 'name', 'email', 'phones', 'birth_date', 'photo'])
                    ->whereNotNull('birth_date')
                    ->whereMonth('birth_date', $today->month)
                    ->get();
            }),
            'allByMonth' => Inertia::defer(function () use ($isSqlite) {
                $orderExpr = $isSqlite
                    ? "cast(strftime('%m', birth_date) as integer), cast(strftime('%d', birth_date) as integer)"
                    : 'MONTH(birth_date), DAYOFMONTH(birth_date)';

                $rows = \Illuminate\Support\Facades\DB::table('alumni')
                    ->select(['id', 'name', 'email', 'phones', 'birth_date', 'photo'])
                    ->whereNotNull('birth_date')
                    ->orderByRaw($orderExpr)
                    ->get();

                $months = [
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December',
                ];

                $grouped = [];
                foreach ($rows as $row) {
                    $row->phones = json_decode($row->phones, true);
                    $monthNum = (int) date('n', strtotime($row->birth_date));
                    $monthName = $months[$monthNum];
                    $grouped[$monthName][] = $row;
                }

                return $grouped;
            }),
        ]);
    }

    public function distribution(Request $request): Response
    {
        $isOverseas = $request->boolean('overseas');

        return Inertia::render('alumni/Distribution', [
            'alumni' => Inertia::defer(fn() => Alumnus::with('tenure')
                ->search($request->search)
                ->byTenure($request->tenure_id)
                ->byUnit($request->unit)
                ->when($isOverseas, fn($q) => $q->where('state', 'Overseas'))
                ->when(!$isOverseas && $request->state, fn($q) => $q->byState($request->state))
                ->latest()
                ->paginate(20)
                ->withQueryString()),
            'stateDistribution' => Inertia::defer(fn() => Alumnus::selectRaw('state, COUNT(*) as count')
                ->whereNotNull('state')
                ->where('state', '!=', 'Overseas')
                ->groupBy('state')
                ->orderBy('state')
                ->get()
                ->mapWithKeys(fn($item) => [$item->state->value => $item->count])),
            'overseasCount' => Inertia::defer(fn() => Alumnus::where('state', 'Overseas')->count()),
            'overseasAlumni' => Inertia::defer(fn() => Alumnus::with('tenure')
                ->where('state', 'Overseas')
                ->get()),
            'units' => collect(Unit::cases())->map(fn($u) => [
                'value' => $u->value,
                'label' => $u->value === "President's Unit" ? "President's Unit (Non Worker)" : $u->value,
            ]),
            'states' => collect(NigerianState::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->value]),
            'tenures' => Tenure::orderBy('year', 'desc')->get()->map(fn($t) => ['value' => $t->id, 'label' => $t->year]),
            'filters' => $request->only(['state', 'unit', 'tenure_id', 'search', 'overseas']),
        ]);
    }

    public function executives(): Response
    {
        return Inertia::render('alumni/Executives', [
            'centralExco' => Inertia::defer(fn() => Alumnus::with('tenure')
                ->whereNotNull('current_exco_office')
                ->whereIn('current_exco_office', [
                    'President',
                    'Vice President',
                    'General Secretary',
                    'Financial Secretary',
                    'Prayer Secretary',
                    'Bible Study Secretary',
                ])
                ->orderBy('current_exco_office')
                ->get()),
            'coordinators' => Inertia::defer(fn() => Alumnus::with('tenure')
                ->whereNotNull('current_exco_office')
                ->where('current_exco_office', 'LIKE', '%Coordinator%')
                ->orderBy('current_exco_office')
                ->get()),
            'otherPositions' => Inertia::defer(fn() => Alumnus::with('tenure')
                ->whereNotNull('current_exco_office')
                ->whereNotIn('current_exco_office', [
                    'President',
                    'Vice President',
                    'General Secretary',
                    'Financial Secretary',
                    'Prayer Secretary',
                    'Bible Study Secretary',
                ])
                ->where('current_exco_office', 'NOT LIKE', '%Coordinator%')
                ->orderBy('current_exco_office')
                ->get()),
            'totalCount' => Inertia::defer(fn() => Alumnus::whereNotNull('current_exco_office')->count()),
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
