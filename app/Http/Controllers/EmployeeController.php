<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'profile')
            ->whereHas('profile') // Only show users with profile (employees)
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('nip', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $employees = $query->paginate(12)->withQueryString();

        $departments = ['Redaksi', 'IT', 'HRD', 'Management', 'Finance'];

        // Stats for Dashboard Header
        $totalEmployees = User::whereHas('profile')->count();
        $activeEmployees = User::whereHas('profile', function ($q) {
            $q->where('is_active', true);
        })->count();

        return view('employees.index', compact('employees', 'departments', 'totalEmployees', 'activeEmployees'));
    }

    public function show(User $employee)
    {
        $employee->load('profile', 'roles');
        return view('employees.show', compact('employee'));
    }
}
