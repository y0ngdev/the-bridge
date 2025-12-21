<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenureRequest;
use App\Http\Requests\UpdateTenureRequest;
use App\Models\Tenure;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TenureController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('tenures/Index', [
            'tenures' => Tenure::latest()->paginate(),
        ]);
    }

    public function store(StoreTenureRequest $request): RedirectResponse
    {
        Tenure::create($request->validated());

        return redirect()->route('tenures.index')
            ->with('success', 'Tenure created successfully.');
    }

    public function update(UpdateTenureRequest $request, Tenure $tenure): RedirectResponse
    {
        $tenure->update($request->validated());

        return redirect()->route('tenures.index')
            ->with('success', 'Tenure updated successfully.');
    }
}
