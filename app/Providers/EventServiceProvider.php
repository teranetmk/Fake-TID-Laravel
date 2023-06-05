<?php

namespace App\Providers;

use App\Listeners\CheckTid;
use App\Events\MiddlewareBackend;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use BADDIServices\FakeTIDs\Events\Order\OrderWasCreated;
use BADDIServices\FakeTIDs\Listeners\Order\NewEmployeeProfitFired;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class        => [
            SendEmailVerificationNotification::class,
        ],
        MiddlewareBackend::class => [
            // CheckTid::class
        ],
        
        OrderWasCreated::class  => [
            NewEmployeeProfitFired::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}
