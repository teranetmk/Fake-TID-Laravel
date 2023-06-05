<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Models\Product;
use App\Http\Controllers\Controller;

class CreateEmployeeController extends Controller
{
    public function __invoke()
    {
        return view(
            'backend.employees.create',
            [
                'products'  => Product::query()->select(['id', 'name'])->get()
            ]
        );
    }
}