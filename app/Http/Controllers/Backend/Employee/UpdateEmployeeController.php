<?php

namespace App\Http\Controllers\Backend\Employee;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeProduct;
use App\Services\Employee\EmployeeService;

class UpdateEmployeeController extends Controller
{
    /** @var EmployeeService */
    private $employeeService;

    public function __construct(EmployeeService $employeeService) 
    {
        $this->employeeService = $employeeService;
    }

    public function __invoke(int $id, Request $request)
    {
        $employee = $this->employeeService->findById($id);
        abort_unless($employee instanceof Employee, Response::HTTP_NOT_FOUND);

        $validator = Validator::make( 
            $request->input(), 
            [
                Employee::NAME_COLUMN   => ['required', 'string'],
                'products'              => ['nullable'],
                'products.*'            => ['integer'],
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $existsEmployeeByName = $this->employeeService->findByName($request->input(Employee::NAME_COLUMN), $employee);
        if ($existsEmployeeByName instanceof Employee) {
            return back()
                ->withInput()
                ->with('errorMessage', 'Employee name already exists!');
        }

        $assignedProducts = $this->employeeService->checkAssignedProducts($request->input('products'));
        if ($assignedProducts->count() > 0) {
            $message = "Following products already assigned:";

            $assignedProducts->map(function (EmployeeProduct $employeeProduct) use (&$message) {
                $message .= ' ' . $employeeProduct->product->name;
            });

            return back()
                ->withInput()
                ->with('errorMessage', $message . " !");
        }

        $this->employeeService->update($employee, $request->input());
        $this->employeeService->updateProducts($employee, $request->input());

        return redirect()
            ->route('admin.employees')
            ->with('successMessage', 'Employee successfully updated');
    }
}