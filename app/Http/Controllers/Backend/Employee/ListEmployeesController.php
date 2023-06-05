<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyticsRequest;
use App\Services\Employee\EmployeeService;

class ListEmployeesController extends Controller
{
    /** @var EmployeeService */
    private $employeeService;

    public function __construct(EmployeeService $employeeService) 
    {
        $this->employeeService = $employeeService;
    }

    public function __invoke(AnalyticsRequest $request)
    {
        $employees = $this->employeeService->paginate($request->query('page'));

        return view(
            'backend.employees.index',
            [
                'managementPage'        => true,
                'employees'             => $employees,
            ]
        );
    }
}