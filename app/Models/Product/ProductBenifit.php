<?php

namespace App\Models\Product;

use App\Models\Product;
use BADDIServices\Framework\Entities\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBenifit extends Entity
{
    public const PRODUCT_ID_COLUMN = 'product_id';
    public const LABEL_COLUMN = 'label';
    public const VALUE_COLUMN = 'value';
    public const TYPE_COLUMN = 'type';

    protected $table = 'product_benifits';

    public function getLabel(): string
    {
        return $this->getAttribute(self::LABEL_COLUMN);
    }
    
    public function getValue(): ?string
    {
        return $this->getAttribute(self::VALUE_COLUMN);
    }
    
    public function getType(): ?int
    {
        return $this->getAttribute(self::TYPE_COLUMN);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}