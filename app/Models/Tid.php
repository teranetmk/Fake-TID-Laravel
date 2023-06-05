<?php

namespace App\Models;

use App\Services\UploadService;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Tid extends Model
{
    protected $table = 'tids';

    protected $appends = [
        'used_name',
        'tid_name'
    ];

    protected $fillable = [
        'tid',
        'used',
        'loc',
        'product_id',
        'used',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->belongsTo( Product::class, 'product_id', 'id' );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne( UserOrder::class, 'tid_id', 'id' );
    }

    public function packStation()
    {
        return $this->hasOne(TidPackStation::class, 'tid_id', 'id');
    }


    /**
     * @return array|string|null
     */
    public function getUsedNameAttribute()
    {
        if ( $this->used == 1 )
            return __( 'backend/tids.useds.already_assigned' );
        else
            return __( 'backend/tids.useds.not_assigned' );
    }


    /**
     * @return mixed
     */
    public function getTidNameAttribute()
    {
        return app( UploadService::class )->getTid( $this->tid );
    }
}
