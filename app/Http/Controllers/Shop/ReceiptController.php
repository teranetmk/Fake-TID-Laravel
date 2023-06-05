<?php

namespace App\Http\Controllers\Shop;

use Carbon\Carbon;
use App\Models\Tid;
use App\Models\Setting;
use App\Models\UserOrder;
use App\Models\Packstation;
use Illuminate\Http\Response;
use App\Models\TidPackStation;
use App\Models\ShippingAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ReceiptController extends Controller
{
    public function __construct()
    {
        if (Setting::get('app.access_only_for_users', false)) {
            $this->middleware('auth');
        }
    }

    public function generateReceipt($tidId)
    {
        try {
            abort_unless(Auth::check(), Response::HTTP_UNAUTHORIZED);

            $tid = Tid::query()
                ->where('id', $tidId)
                ->first();

            abort_unless($tid instanceof Tid, Response::HTTP_NOT_FOUND);

            $orderQuery = UserOrder::query()
                ->where('tid_id', $tid->id);

            if (! Auth::user()->hasAnyPermissionFromArray(['access_backend', 'manage_orders'])) {
                $orderQuery = $orderQuery->where('user_id', Auth::id());
            }
                
            $order = $orderQuery->first();

            abort_unless($order instanceof UserOrder, Response::HTTP_NOT_FOUND);
            
            $tidPackstation = TidPackStation::query()
                ->where('tid_id', $tid->id)
                ->first();

            if (! $tidPackstation instanceof TidPackStation) {
                $address = ShippingAddress::query()
                    ->where('order_id', $order->id)
                    ->first();

                abort_unless($address instanceof ShippingAddress, Response::HTTP_NOT_FOUND);

                $packstation = Packstation::query()
                    ->where('zip', $address->zip)
                    ->first();

                abort_unless($packstation instanceof Packstation, Response::HTTP_NOT_FOUND);

                $tidPackstation = TidPackStation::query()
                    ->create([
                        'tid_id'            => $tid->id,
                        'packstation_id'    => $packstation->id,
                    ]);

                abort_unless($tidPackstation instanceof TidPackStation, Response::HTTP_NOT_FOUND);
            }
            
            $packstation = Packstation::query()
                ->where('id', $tidPackstation->id)
                ->inRandomOrder()
                ->first();

            abort_unless($packstation instanceof Packstation, Response::HTTP_NOT_FOUND);

            return view(
                'print.receipt', 
                [
                    'tid'           => pathinfo($tid->tid, PATHINFO_FILENAME),
                    'packstation'   => $packstation->name,
                    'addressOne'    => $packstation->address_one,
                    'addressTwo'    => $packstation->address_two,
                    'date'          => Carbon::parse($order->created_at)->format('d.m.Y'),
                    'hour'          => Carbon::parse($order->created_at)->format('H:i'),
                    'zip'           => $packstation->zip,
                ]
            );
        } catch (NotFoundHttpException $e) {
            return redirect()->route('not-found');
        } catch (UnauthorizedHttpException $e) {
            return redirect()->route('no-permissions');
        }
    }
}