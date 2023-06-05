<?php

namespace App\Http\Controllers\Backend\Employee;

use Illuminate\Http\Response;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Employee\EmployeeService;

class EditEmployeeController extends Controller
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

        return view(
            'backend.employees.edit',
            [
                'employee'      => $employee,
                'productsIds'   => $employee->products->pluck('id')->toArray(),
                'products'      => Product::query()->select(['id', 'name'])->get()
            ]
        );
    }
}