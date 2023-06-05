<?php

namespace App\Models\Employee;

use App\Models\UserOrder;
use BADDIServices\Framework\Entities\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfit extends Entity
{
    public const EMPLOYEE_ID_COLUMN = 'employee_id';
    public const ORDER_ID_COLUMN = 'order_id';
    public const LABEL_COST_IN_CENT_COLUMN = 'label_cost_in_cent';

    public const DEFAULT_LABEL_COST_IN_CENT = 300;

    protected $table = 'employee_profits';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, self::EMPLOYEE_ID_COLUMN);
    }
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(UserOrder::class, self::ORDER_ID_COLUMN);
    }
}