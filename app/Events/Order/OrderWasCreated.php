<?php

/**
 * Fake TIDs
 *
 * @copyright   Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\FakeTIDs\Events\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OrderWasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /** @var string */
    public $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}