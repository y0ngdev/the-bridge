<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
use App\Models\Department;
use App\Models\PendingAlumnusUpdate;
use App\Models\Tenure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlumnusPortalController extends Controller
{
    /**
     * Display the portal landing page.
     */
    public function index(): Response
    {
        return Inertia::render('public/Portal', [
            'tenures' => Tenure::orderBy('start_date')->get(),
            'departments' => Department::orderBy('name')->get(),
            'units' => collect(\App\Enums\Unit::cases())->map(fn ($case) => [
                'value' => $case->value,
                'label' => $case->value === "President's Unit" ? "President's Unit (Non Worker)" : $case->value,
            ]),
            'states' => collect(\App\Enums\NigerianState::cases())->map(fn ($case) => ['value' => $case->value, 'label' => $case->value]),
            'pastExcoOffices' => collect(\App\Enums\PastExcoOffice::cases())->map(fn ($case) => ['value' => $case->value, 'label' => $case->value]),
        ]);
    }

    /**
     * Look up an alumnus record.
     */
    public function lookup(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        $query = Alumnus::notMerged();

        // 1. Try exact email match first (strongest signal)
        if ($request->filled('email')) {
            $match = (clone $query)->where('email', $request->email)->first();
            if ($match) {
                return back()->with('match', $match);
            }
        }

        // 2. Try phone match
        if ($request->filled('phone')) {
            // This would ideally use normalized phone matching, but for now simple query
            // In a real app we'd normalize before query or use JSON contains if stored appropriately
        }

        // 3. Try name match (fuzzy)
        $name = $request->name;
        // Simple case-insensitive match for now
        $matches = (clone $query)
            ->where('name', 'like', "%{$name}%")
            ->with(['department', 'tenure']) // Load useful context for disambiguation
            ->limit(5)
            ->get();

        if ($matches->count() === 1) {
            return back()->with('match', $matches->first());
        }

        if ($matches->count() > 1) {
            return back()->with('possible_matches', $matches);
        }

        return back()->with('no_match', true)->withInput();
    }

    /**
     * Submit a new alumnus record (auto-create).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:alumni,email',
            'phones' => 'required|array|min:1',
            'phones.*' => 'string',
            'tenure_id' => 'required|exists:tenures,id',
            'department_id' => 'nullable|exists:departments,id',
            'address' => 'nullable|string|max:1000',
            'current_employer' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'gender' => 'required|string|in:M,F',
            'birth_date' => 'required|date',
            'past_exco_office' => 'nullable|string|max:255',
            'current_exco_office' => 'nullable|string|max:255',
            'is_futa_staff' => 'boolean',
            'marital_status' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('alumni-photos', 'public');
        }

        Alumnus::create($validated);

        return back()->with('success', 'Your record has been created successfully!');
    }

    /**
     * Submit an update for an existing record.
     */
    public function update(Request $request, Alumnus $alumnus): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => 'string',
            'tenure_id' => 'required|exists:tenures,id',
            'department_id' => 'nullable|exists:departments,id',
            'address' => 'nullable|string|max:1000',
            'current_employer' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'gender' => 'required|string|in:M,F',
            'birth_date' => 'required|date',
            'past_exco_office' => 'nullable|string|max:255',
            'current_exco_office' => 'nullable|string|max:255',
            'is_futa_staff' => 'boolean',
            'marital_status' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload - only include if a file was actually uploaded
        // This prevents null from overwriting existing photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('alumni-photos-pending', 'public');
        } else {
            // Remove photo key if no new file uploaded - preserves existing photo
            unset($validated['photo']);
        }

        // Calculate changes
        $changes = [];
        foreach ($validated as $key => $value) {
            $oldValue = $alumnus->$key;
            // Handle array comparison for phones
            if ($key === 'phones') {
                if (json_encode($oldValue) !== json_encode($value)) {
                    $changes[$key] = $value;
                }
            } elseif ($oldValue != $value) {
                $changes[$key] = $value;
            }
        }

        if (empty($changes)) {
            return back()->with('info', 'No changes were detected.');
        }

        PendingAlumnusUpdate::create([
            'alumnus_id' => $alumnus->id,
            'changes' => $changes,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Your update request has been submitted for review.');
    }
}
