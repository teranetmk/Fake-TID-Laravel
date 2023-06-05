<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Repositories\Employee;

use App\Models\Employee\Employee;
use BADDIServices\Framework\Repositories\EloquentRepository;

class EmployeeRepository extends EloquentRepository
{
    /** @var Employee */
    protected $model = Employee::class;
}