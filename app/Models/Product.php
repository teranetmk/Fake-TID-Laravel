<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ProductCategory;
use App\Models\Setting;
use App\Models\Product\ProductBenifit;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $appends = [
        'tid_exists'
    ];

    protected $fillable = [
        'sells',
        'name',
        'description',
        'short_description',
        'drop_needed',
        'content',
        'old_price_in_cent',
        'price_in_cent',
        'category_id',
        'stock_management',
        'as_weight',
        'weight_available',
        'weight_char',
        'created_at',
        'sold_out',
        'show_stock',
        'icon',
        'order_minimum',
    ];

    protected $hidden = [
        'content'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tids()
    {
        return $this->hasMany(Tid::class);
    }

    /**
     * @return Model|HasMany|object|null
     */
    public function lastTid()
    {
        return $this->tids->last();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }


    /**
     * @return bool
     */
    protected function getTidExistsAttribute()
    {
        return ( $this->tids()->where( 'used', 0 )->count() > 0 ) ? true : false;
    }

    public function isDigitalGoods()
    {
       
        return ($this->getCategory() instanceof ProductCategory && $this->getCategory()->isDigitalGoods());
       
       
    }

    public function isSoldOut() 
    {
        return (bool)$this->getAttribute('sold_out') === true;
    }

    public function isAvailable()
    {
        
        if ($this->isSoldOut()) {
            
            return false;
        }

        if (($this->name === 'Nachnahme Boxing')) {
            return true;
        }

        if ($this->isDigitalGoods()) {
            return $this->isUnlimited() || $this->inStock();
        }

        if (! $this->getTidExistsAttribute()) {
            return false;
        } else if ( $this->isUnlimited() ) {
            return true;
        } else if ( $this->asWeight() && $this->getWeightAvailable() > 0 ) {
            return true;
        } else if ( $this->inStock() ) {
            return true;
        }

        return 'false';
    }

    public static function getById( $id )
    {
        return self::where( 'id', $id )->first();
    }


    public static function getUncategorizedProducts()
    {
        $products_arr = [];

        $products = self::where( 'category_id', 0 )->get();

        foreach ( $products as $product ) {
            $products_arr[] = $product->id;
        }

        foreach ( self::all() as $product ) {
            if ( ProductCategory::getById( $product->category_id ) == null && !in_array( $product->id, $products_arr ) ) {
                $products[] = $product;
            }
        }

        return $products;
    }

    public static function formatPrice( $cent )
    {
        return number_format( $cent / 100, 2, ',', '.' ) . ' ' . Setting::getShopCurrency();
    }

    public function getWeightChar()
    {
        return $this->weight_char;
    }

    public function getFormattedPrice()
    {
        $after = '';

        if ( $this->asWeight() ) {
            $after = '/' . $this->getWeightChar();
        }

        return $this->getFormattedPriceWithoutCurrency() . ' ' . Setting::getShopCurrency() . $after;
    }

    public function getFormattedPriceWithoutCurrency()
    {
        return number_format( $this->price_in_cent / 100, 2, ',', '.' );
    }

    public function getFormattedOldPrice()
    {
        $after = '';

        if ( $this->asWeight() ) {
            $after = '/' . $this->getWeightChar();
        }

        return $this->getFormattedOldPriceWithoutCurrency() . ' ' . Setting::getShopCurrency() . $after;
    }

    public function getFormattedOldPriceWithoutCurrency()
    {
        return number_format( $this->old_price_in_cent / 100, 2, ',', '.' );
    }

    public function getCategory()
    {
        $productCategory = $this->relationLoaded('category') ?? ProductCategory::where( 'id', $this->category_id )->first();

        if ($productCategory instanceof ProductCategory) {
            return $productCategory;
        }

        return (object)[
            'name' => __('frontend/shop.uncategorized'),
            'slug' => 'uncategorized',
            'is_digital_goods' => false,
        ];
    }

    public function dropNeeded()
    {
        return $this->drop_needed == 1;
    }

    public function asWeight()
    {
        if ($this->isDigitalGoods()) {
            return false;
        }

        return $this->as_weight == 1;
    }

    public function getWeightAvailable()
    {
        return $this->weight_available;
    }

    public function isAvailableAmount( $amount )
    {
        if ( $this->isAvailable() ) {
            if ( $this->asWeight() && $this->getWeightAvailable() >= $amount ) {
                return true;
            } else if ( $this->inStock() && $this->getStock() >= $amount ) {
                return true;
            } else if ($this->isUnlimited()) {
                return true;
            }
        }

        return false;
    }


    public function isUnlimited()
    {
        if ($this->isSoldOut()) {
            return false;
        }

        return ($this->name === 'Nachnahme Boxing') || ($this->stock_management == 0 && !$this->asWeight());
    }

    public function isSale()
    {
        return $this->old_price_in_cent != null && $this->old_price_in_cent > 0 && $this->old_price_in_cent > $this->price_in_cent;
    }

    public function getSalePercent()
    {
        if ( $this->isSale() ) {
            return round( ( ( $this->old_price_in_cent - $this->price_in_cent ) * 100 ) / $this->old_price_in_cent );
        }

        return 0;
    }

    public function inStock()
    {
        if ($this->isSoldOut()) {
            return false;
        }

        return ( $this->getStock() > 0 || $this->getStock() == -1 ) && !$this->asWeight();
    }

    public function getSells()
    {
        return $this->sells;
    }

    public function getStock()
    {
        if ($this->isSoldOut()) {
            return 0;
        }

        if ( $this->stock_management <= 0 ) {
            return -1;
        }

        $stock = ProductItem::where( 'product_id', $this->id )->count();

        return $stock;
    }

    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function benifits(): HasMany
    {
        return $this->hasMany(ProductBenifit::class, 'product_id', 'id');
    }
    
    public function isShowStockEnabled(): bool
    {
        return (bool)$this->getAttribute('show_stock') === true;
    }

    public function getIcon(): ?string
    {
        return $this->getAttribute('icon');
    }
    
    public function getOrderMinimum(): int
    {
        return $this->getAttribute('order_minimum') ?? 1;
    }
}
