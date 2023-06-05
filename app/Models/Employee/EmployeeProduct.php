<?php

namespace App\Models\Employee;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeProduct extends Pivot
{
    public const EMPLOYEE_ID_COLUMN = 'employee_id';
    public const PRODUCT_ID_COLUMN = 'product_id';
    public const TABLE_NAME = 'employee_products';

    protected $table = self::TABLE_NAME;

    public function getEmployeeId(): int
    {
        return $this->getAttribute(self::EMPLOYEE_ID_COLUMN);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, self::EMPLOYEE_ID_COLUMN);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, self::PRODUCT_ID_COLUMN);
    }
}