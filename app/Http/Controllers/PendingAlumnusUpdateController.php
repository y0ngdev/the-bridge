<?php

namespace App\Http\Controllers;

use App\Models\PendingAlumnusUpdate;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PendingAlumnusUpdateController extends Controller
{
    /**
     * Display a listing of pending updates.
     */
    public function index(): Response
    {
        $updates = PendingAlumnusUpdate::query()
            ->with(['alumnus' => function ($query) {
                // Ensure we select necessary fields to display/compare
                $query->select('*'); 
            }])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return Inertia::render('admin/PendingUpdates', [
            'updates' => $updates,
        ]);
    }

    /**
     * Approve a pending update.
     */
    public function approve(PendingAlumnusUpdate $update): RedirectResponse
    {
        $update->approve(auth()->user());

        return back()->with('success', 'Update approved and applied to alumnus record.');
    }

    /**
     * Reject a pending update.
     */
    public function reject(PendingAlumnusUpdate $update): RedirectResponse
    {
        $update->reject(auth()->user());

        return back()->with('success', 'Update rejected.');
    }
}
