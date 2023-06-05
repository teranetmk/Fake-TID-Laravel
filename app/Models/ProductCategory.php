<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class ProductCategory extends Model
{
    protected $table = 'products_categories';

    protected $fillable = [
        'name',
        'slug',
        'is_digital_goods',
        'is_show'
    ];


    public static function getById( $id )
    {
        return self::where( 'id', $id )->first();
    }


    public function getProducts()
    {
        return Product::where( 'category_id', $this->id )->get();
    }

    public function isDigitalGoods(): bool
    {
        return (bool)$this->getAttribute('is_digital_goods') === true;
    }
}
