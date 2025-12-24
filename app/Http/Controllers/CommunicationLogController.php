<?php

namespace App\Http\Controllers;

use App\Enums\CommunicationOutcome;
use App\Enums\CommunicationType;
use App\Models\Alumnus;
use App\Models\CommunicationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunicationLogController extends Controller
{
    public function store(Request $request, Alumnus $alumnus): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(CommunicationType::class)],
            'outcome' => ['required', Rule::enum(CommunicationOutcome::class)],
            'notes' => ['nullable', 'string'],
            'occurred_at' => ['required', 'date'],
        ]);

        $alumnus->communicationLogs()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Communication log added.');
    }

    public function destroy(CommunicationLog $log): RedirectResponse
    {
        $log->delete();

        return back()->with('success', 'Communication log deleted.');
    }
}
