<?php

namespace App\Models;

use App\Filters\OrderFilters;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrderNote;
use App\Models\Product;
use App\Models\ShippingAddress;

use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserOrder extends Model
{
    protected $table = 'users_orders';

    protected $appends = [
        'tid'
    ];

    protected $guarded = [];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->belongsTo( Product::class, 'product_id', 'id' );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tids()
    {
        return $this->belongsTo( Tid::class, 'tid_id', 'id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shipping_address()
    {
        return $this->hasOne(ShippingAddress::class, 'order_id', 'id');
    }

    public function random_shipping_address()
    {
        return $this->hasOne(RandomShippingAddress::class, 'order_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne( ShippingAddress::class, 'order_id', 'id' );
    }


    /**
     * @param Builder $builder
     * @param OrderFilters $filters
     * @return Builder
     */
    public function scopeFilter( Builder $builder, OrderFilters $filters )
    {
        return $filters->apply( $builder );
    }


    /**
     * @param $type
     * @return mixed
     */
    public static function getCount( $type)
    {
        $from = Carbon::now()->startOfDay();
        $to   = Carbon::now()->setTime( 19, 00, 00 );

        return self::where( 'delivery_name', $type )
            ->whereBetween( 'deliver_at', [ $from, $to ] )
            ->count();
    }




    /**
     * @return string
     */
    public function getDeliverAttribute()
    {
        if ( !is_null( $this->deliver_at ) ) {
            return Carbon::parse( $this->deliver_at )->format( 'd.m.Y' );
        } else {
            return Carbon::parse( $this->created_at )->format( 'd.m.Y' );
        }
    }


    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        switch ( $this->type ) {
            case 'packing_station':
                return 'Packstation';
            case 'branch_delivery':
                return 'Filialeinlieferung';
        }
    }


    /**
     * @return string|string[]
     */
    public function getTidAttribute()
    {
        $uploadService = new UploadService();

        return (! is_null($this->tids) && ! is_null($this->tids->tid)) ? $uploadService->getTid( $this->tids->tid ) : '';
    }


    public static function getById( $id )
    {
        return self::where( 'id', $id )->first();
    }

    public static function getTodayWin()
    {
        $todayOrders = self::whereDate( 'created_at', DB::raw( 'CURDATE()' ) )->get();

        $cent = 0;

        foreach ( $todayOrders as $order ) {
            $cent += $order->totalprice;
        }

        return $cent;
    }

    public static function getFormattedTodayWin()
    {
        return number_format( self::getTodayWin() / 100, 2, ',', '.' ) . ' ' . Setting::getShopCurrency();
    }

    public function hasNotes()
    {
        return UserOrderNote::where( 'order_id', $this->id )->count() > 0;
    }

    public function getNotes()
    {
        return UserOrderNote::orderByDesc( 'created_at' )->where( 'order_id', $this->id )->get();
    }

    public function getDrop()
    {
        return $this->drop_info;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getWeightChar()
    {
        return $this->weight_char;
    }

    public function asWeight()
    {
        return strlen( $this->weight_char ) > 0 && intval( $this->weight ) > 0;
    }

    public function getStatus()
    {
        return strlen( $this->status ) > 0 ? strtolower( $this->status ) : 'nothing';
    }

    public function isManual()
    {
        return !is_null($this->products) && Str::contains($this->products->name, ['100%','Originale 80%']);
    }
    public function hasPdf()
    {
       return Storage::disk('public')->exists("order_tid/{$this->id}.pdf");
    }

    public function getUser()
    {
        $name = '-/-';

        $user = $this->relationLoaded('user') ? $this->getRelation('user') : User::where( 'id', $this->user_id )->first();

        if ( $user != null ) {
            $name = $user->username;
        }

        return (object)[
            'name'     => $name,
            'username' => $name
        ];
    }

    public function getFormattedDeliveryPrice()
    {
        return $this->getFormattedDeliveryPriceWithoutCurrency() . ' ' . Setting::getShopCurrency();
    }

    public function getFormattedDeliveryPriceWithoutCurrency()
    {
        return number_format( $this->delivery_price / 100, 2, ',', '.' );
    }

    public function getFormattedTotalPrice()
    {
        return number_format( $this->totalprice / 100, 2, ',', '.' ) . ' ' . Setting::getShopCurrency();
    }

    public function getFormattedPrice()
    {
        $after = '';

        if ( $this->asWeight() ) {
            $after = '/' . $this->getWeightChar();
        }

        return number_format( $this->price_in_cent / 100, 2, ',', '.' ) . ' ' . Setting::getShopCurrency() . $after;
    }
}
