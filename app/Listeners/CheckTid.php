<?php

namespace App\Listeners;

use App\Events\MiddlewareBackend;
use App\Models\Product;
use App\Models\Tid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckTid
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param MiddlewareBackend $event
     * @return void
     */
    public function handle( MiddlewareBackend $event )
    {
        $products = Product::withCount( [ 'tids' => function ( $query ) {
            $query->where( 'used', 0 );
        } ] )->get();

        $products = $products->where( 'tids_count', '<', 20 );

        if ( $products->isNotEmpty() ) {
            $errorMessage = [];
            foreach ( $products as $product ) {
                $tids_count = $product->tids_count;
                $product_id = $product->id;
                $name       = $product->name;

                $errorMessage[] = "$product_id: $name - $tids_count";
            }

            session()->flash( 'notificationMessage', implode( '<br/>', $errorMessage ) );
        }
    }

    public static function getSqlWithBindings( $query )
    {
        return vsprintf( str_replace( '?', '%s', $query->toSql() ), collect( $query->getBindings() )->map( function ( $binding ) {
            return is_numeric( $binding ) ? $binding : "'{$binding}'";
        } )->toArray() );
    }
}
