<?php

namespace App\Models\Employee;

use App\Models\Product;
use App\Models\User;
use BADDIServices\Framework\Entities\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Entity
{
    public const ADDED_BY_COLUMN = 'added_by';
    public const NAME_COLUMN = 'name';

    protected $table = 'employees';

    public function getName(): string
    {
        return $this->getAttribute(self::NAME_COLUMN);
    }
    
    public function getAddedBy(): int
    {
        return $this->getAttribute(self::ADDED_BY_COLUMN);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, self::ADDED_BY_COLUMN);
    }
    
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, EmployeeProduct::TABLE_NAME);
    }
}