<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'experience' => 'nullable|string',
            'working_status' => 'nullable|in:Active,Inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        do {
            $employeeGid = random_int(100000, 999999);
        } while (Employee::where('employee_gid', $employeeGid)->exists());

        $validated['user_id'] = Auth::id();
        $validated['employee_gid'] = $employeeGid;
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $employeeGid . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('employees', $filename, 'public');
            $validated['profile_image'] = $path;
        }
        
        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'New employee added successfully!');
    }

    public function edit(Employee $employee)
    {
        // Add a policy check later if needed
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'experience' => 'nullable|string',
            'working_status' => 'nullable|in:Active,Inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($employee->profile_image && \Storage::disk('public')->exists($employee->profile_image)) {
                \Storage::disk('public')->delete($employee->profile_image);
            }
            
            $file = $request->file('profile_image');
            $filename = time() . '_' . $employee->employee_gid . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('employees', $filename, 'public');
            $validated['profile_image'] = $path;
        }
        
        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee information updated successfully!');
    }
}