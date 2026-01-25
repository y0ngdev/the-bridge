<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
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

        return Inertia::render('alumni/Duplicates', [
            'duplicateGroups' => $duplicateGroups,
        ]);
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
