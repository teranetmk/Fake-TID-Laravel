<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RandomShippingAddress extends Model
{
    protected $table = 'random_shipping_address';

    /** @var array */
    protected $guarded = [];

    /**
     * @return bool
     */
    public function getSenderAttribute()
    {
        return (
            is_null( $this->sender_first_name ) ||
            is_null( $this->sender_last_name ) ||
            is_null( $this->sender_street ) ||
            is_null( $this->sender_zip ) ||
            is_null( $this->sender_city ) ||
            is_null( $this->sender_country )
        ) ? false : true;
    }
}