<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
use App\Models\DismissedDuplicate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlumnusDuplicateController extends Controller
{
    /**
     * Display the duplicates detection page.
     */
    public function index(): Response
    {
        $duplicateGroups = Alumnus::findDuplicates();

        // Filter out dismissed pairs
        $filteredGroups = array_filter($duplicateGroups, function ($group) {
            if (count($group) < 2) {
                return false;
            }
            // Check if this pair has been dismissed
            $ids = array_map(fn ($a) => $a->id, $group);
            for ($i = 0; $i < count($ids); $i++) {
                for ($j = $i + 1; $j < count($ids); $j++) {
                    if (DismissedDuplicate::isPairDismissed($ids[$i], $ids[$j])) {
                        return false;
                    }
                }
            }
            return true;
        });

        return Inertia::render('alumni/Duplicates', [
            'duplicateGroups' => array_values($filteredGroups),
        ]);
    }

    /**
     * Dismiss a group as not duplicates.
     */
    public function dismiss(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array|min:2',
            'ids.*' => 'exists:alumni,id',
        ]);

        $ids = $request->input('ids');

        // Store all pairs
        for ($i = 0; $i < count($ids); $i++) {
            for ($j = $i + 1; $j < count($ids); $j++) {
                DismissedDuplicate::dismissPair($ids[$i], $ids[$j], auth()->id());
            }
        }

        return redirect()->route('alumni.duplicates')->with('success', 'Marked as not duplicates.');
    }

    /**
     * Merge two alumni records.
     */
    public function merge(Request $request, Alumnus $alumnus, Alumnus $target): RedirectResponse
    {
        $request->validate([
            'primary_id' => 'required|exists:alumni,id',
        ]);

        $primaryId = $request->input('primary_id');

        // Determine which is primary and which is secondary
        if ($alumnus->id == $primaryId) {
            $primary = $alumnus;
            $secondary = $target;
        } else {
            $primary = $target;
            $secondary = $alumnus;
        }

        // Prevent merging if either is already merged
        if ($primary->merged_into || $secondary->merged_into) {
            return back()->withErrors(['error' => 'One of these records has already been merged.']);
        }

        // Prevent self-merge
        if ($primary->id === $secondary->id) {
            return back()->withErrors(['error' => 'Cannot merge a record with itself.']);
        }

        // Merge phone numbers (combine and deduplicate)
        $primaryPhones = $primary->phones ?? [];
        $secondaryPhones = $secondary->phones ?? [];
        $mergedPhones = array_unique(array_merge($primaryPhones, $secondaryPhones));

        $primary->update(['phones' => $mergedPhones]);

        // Transfer communication logs from secondary to primary
        $secondary->communicationLogs()->update(['alumnus_id' => $primary->id]);

        // Mark secondary as merged
        $secondary->update(['merged_into' => $primary->id]);

        return redirect()->route('alumni.duplicates')->with('success', 'Alumni records merged successfully.');
    }
}

