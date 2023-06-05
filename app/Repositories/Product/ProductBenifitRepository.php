<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Repositories\Product;

use App\Models\Product\ProductBenifit;
use BADDIServices\Framework\Repositories\EloquentRepository;

class ProductBenifitRepository extends EloquentRepository
{
    /** @var ProductBenifit */
    protected $model = ProductBenifit::class;
}