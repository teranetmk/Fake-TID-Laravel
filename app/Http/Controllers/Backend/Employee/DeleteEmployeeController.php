<?php

namespace App\Http\Controllers\Backend\Employee;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Services\Employee\EmployeeService;

class DeleteEmployeeController extends Controller
{
    /** @var EmployeeService */
    private $employeeService;

    public function __construct(EmployeeService $employeeService) 
    {
        $this->employeeService = $employeeService;
    }

    public function __invoke(int $id)
    {
        $employee = $this->employeeService->findById($id);
        abort_unless($employee instanceof Employee, Response::HTTP_NOT_FOUND);

        $this->employeeService->delete($employee);

        return back()
            ->with('successMessage', 'Employee successfully deleted');
    }
}