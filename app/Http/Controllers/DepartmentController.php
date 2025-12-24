<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('school', 'like', "%{$search}%");
            });
        }

        if ($request->filled('school')) {
            $query->where('school', $request->school);
        }

        $departments = $query->orderBy('school')->orderBy('name')->get();

        // Get unique schools for filter
        $schools = Department::distinct()->orderBy('school')->pluck('school')->filter()->values();

        return Inertia::render('departments/Index', [
            'departments' => $departments,
            'departmentsBySchool' => $departments->groupBy('school'),
            'schools' => $schools,
            'filters' => $request->only(['search', 'school']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:departments,code',
            'name' => 'required|string|max:255',
            'school' => 'nullable|string|max:50',
        ]);

        Department::create($validated);

        return back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:departments,code,'.$department->id,
            'name' => 'required|string|max:255',
            'school' => 'nullable|string|max:50',
        ]);

        $department->update($validated);

        return back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // Check if department has alumni
        if ($department->alumni()->exists()) {
            return back()->with('error', 'Cannot delete department with assigned alumni.');
        }

        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}
