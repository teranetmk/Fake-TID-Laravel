<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Services\Product;

use App\Models\Product\ProductBenifit;
use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\Framework\Repositories\Product\ProductBenifitRepository;

class ProductBenifitService extends Service
{
    public function __construct(ProductBenifitRepository $productBenifitRepository) 
    {
        $this->repository = $productBenifitRepository;
    }
    
    public function findById(int $id): ?ProductBenifit
    {
        return $this->repository->findById($id);
    }
    
    public function findByProductId(string $productId): Collection
    {
        return $this->repository->where([ProductBenifit::PRODUCT_ID_COLUMN => $code]);
    }
    
    public function create(array $attributes): ProductBenifit
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                ProductBenifit::LABEL_COLUMN,
                ProductBenifit::PRODUCT_ID_COLUMN,
                ProductBenifit::TYPE_COLUMN,
                ProductBenifit::VALUE_COLUMN,
            ]);

        return $this->repository->create($filteredAttributes->toArray());
    }
    
    public function update(ProductBenifit $productBenifit, array $attributes): bool
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                ProductBenifit::LABEL_COLUMN,
                ProductBenifit::PRODUCT_ID_COLUMN,
                ProductBenifit::TYPE_COLUMN,
                ProductBenifit::VALUE_COLUMN,
            ]);

        return $this->repository->update([ProductBenifit::ID_COLUMN => $productBenifit->getId()], $filteredAttributes->toArray());
    }

    public function delete(ProductBenifit $productBenifit): bool
    {
        return $this->repository->delete($productBenifit->getId());
    }
}