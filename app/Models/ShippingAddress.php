<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shipping_address';

    protected $fillable = [
        'order_id',
        'first_name',
        'last_name',
        'street',
        'zip',
        'city',
        'country',
        'recipient_first_name',
        'recipient_last_name',
        'recipient_street',
        'recipient_zip',
        'recipient_city',
        'recipient_country',
        'sender_first_name',
        'sender_last_name',
        'sender_street',
        'sender_zip',
        'sender_city',
        'sender_country',
    ];

//    protected $attributes = [
//        'sender'
//    ];


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
