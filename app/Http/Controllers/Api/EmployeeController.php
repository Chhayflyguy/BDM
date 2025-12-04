<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Public route - return all employees
        $employees = Employee::latest()->get();
        
        // Append profile_image_url accessor to each employee
        $employees->each(function ($employee) {
            $employee->append('profile_image_url');
        });
        
        return response()->json(['data' => $employees]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Public route - return employee details
        // Append profile_image_url accessor
        $employee->append('profile_image_url');
        
        return response()->json(['data' => $employee]);
    }
}
