<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
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

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return back()->with('success', 'Department created successfully.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

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
