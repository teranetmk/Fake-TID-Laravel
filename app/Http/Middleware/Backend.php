<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Product;
use App\Models\ProductItem;
use Auth;
use function foo\func;

class Backend
{
    public function handle($request, Closure $next)
    {
        if ($request->routeIs('backend-login')) {
            if ( Auth::user() && Auth::user()->hasPermission('access_backend')) {
                if (Auth::user()->hasPermission('manage_orders')) {
                    $this->getProductsTidsCount();
                }
		
		        return redirect()->route('backend-dashboard');
            } else {
                return $next($request);
            }
        } elseif (Auth::user() && Auth::user()->hasPermission('access_backend')) {
            if (Auth::user()->hasPermission('manage_orders')) {
            	$this->getProductsTidsCount();
            }

		    return $next($request);
        }

        return redirect()->route('backend-logout');
    }

    public function getProductsTidsCount()
    {
        $products = Product::select(['id', 'name', 'category_id'])
            ->withCount(['items','tids' => function($q) {
                $q->where('used', 0);
            }])
            ->get();

        if ($products->isNotEmpty()) {
            $errorMessage = [];
            foreach ($products as $product) {
                $unusedTidsCount = $product->tids_count;
                $unusedAccountsCount = $product->items_count;

                $errorMessage[] = sprintf("%s: %s - %s", $product->id, $product->name, ($product->isDigitalGoods() ? $unusedAccountsCount : $unusedTidsCount));
            }

            session()->flash( 'notificationMessage', implode( '<br/>', $errorMessage ) );
        }
    }
}
