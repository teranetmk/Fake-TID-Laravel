<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Services\Employee;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeProduct;
use App\Models\Employee\EmployeeProfit;
use App\Services\Service;
use BADDIServices\Framework\Repositories\Employee\EmployeeRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class EmployeeService extends Service
{
    /** @var AuthManager */
    private $authManager;

    public function __construct(EmployeeRepository $employeeRepository, AuthManager $authManager) 
    {
        $this->repository = $employeeRepository;
        $this->authManager = $authManager;
    }

    public function paginate(?int $page = null): LengthAwarePaginator
    {
        return $this->repository->paginate($page, ['products']);
    }
    
    public function findById(int $id): ?Employee
    {
        return $this->repository->findById($id);
    }
    
    public function findByName(string $name, ?Employee $employee = null): ?Employee
    {
        $conditions = [];
        
        $conditions[] = [
            Employee::NAME_COLUMN, '=', $name
        ];

        if (! is_null($employee)) {
            $conditions[] = [
                Employee::ID_COLUMN, '!=', $employee->getId()
            ];
        }

        return $this->repository->first($conditions);
    }
    
    public function create(array $attributes): Employee
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                Employee::NAME_COLUMN,
            ]);

        $filteredAttributes->put(Employee::ADDED_BY_COLUMN, $this->authManager->id());

        return $this->repository->create($filteredAttributes->toArray());
    }
    
    public function update(Employee $employee, array $attributes): bool
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                Employee::NAME_COLUMN,
            ]);

        $filteredAttributes->put(Employee::ADDED_BY_COLUMN, $this->authManager->id());

        return $this->repository->update([Employee::ID_COLUMN => $employee->getId()], $filteredAttributes->toArray());
    }
    
    public function updateProducts(Employee $employee, array $attributes): void
    {
        $filteredAttributes = Arr::get($attributes, 'products', []);

        EmployeeProduct::query()
            ->where(EmployeeProduct::EMPLOYEE_ID_COLUMN, $employee->getId())
            ->whereNotIn(EmployeeProduct::PRODUCT_ID_COLUMN, $filteredAttributes)
            ->delete();

        foreach ($filteredAttributes as $productId) {
            EmployeeProduct::query()
                ->updateOrCreate(
                    [
                        EmployeeProduct::EMPLOYEE_ID_COLUMN => $employee->getId(),
                        EmployeeProduct::PRODUCT_ID_COLUMN  => $productId,
                    ]
                );
        }
    }
    
    public function checkAssignedProducts(array $productsIds): Collection
    {
        return EmployeeProduct::query()
            ->with(['product'])
            ->whereIn(EmployeeProduct::PRODUCT_ID_COLUMN, $productsIds)
            ->get();
    }
    
    public function storeEmployeeProfit(int $employeeId, int $orderId, float $labelCost = EmployeeProfit::DEFAULT_LABEL_COST_IN_CENT): EmployeeProfit
    {
        return EmployeeProfit::query()
            ->create([
                EmployeeProfit::EMPLOYEE_ID_COLUMN          => $employeeId,
                EmployeeProfit::ORDER_ID_COLUMN             => $orderId,
                EmployeeProfit::LABEL_COST_IN_CENT_COLUMN   => $labelCost,
            ]);
    }

    public function delete(Employee $employee): bool
    {
        return $this->repository->delete($employee->getId());
    }
}