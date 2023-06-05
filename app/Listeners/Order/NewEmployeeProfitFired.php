<?php

/**
 * Fake TIDs
 *
 * @copyright   Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\FakeTIDs\Listeners\Order;

use App\Models\Employee\EmployeeProduct;
use App\Models\Product;
use App\Models\UserOrder;
use App\Services\Employee\EmployeeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use BADDIServices\FakeTIDs\Events\Order\OrderWasCreated;

class NewEmployeeProfitFired implements ShouldQueue
{
    public function handle(OrderWasCreated $event)
    {
        /** @var UserOrder */
        $order = UserOrder::query()->with(['products'])->find($event->orderId);

        if (! $order instanceof UserOrder || ! $order->products instanceof Product) {
            return;
        }

        $employeeProduct = EmployeeProduct::query()
            ->where(EmployeeProduct::PRODUCT_ID_COLUMN, $order->products->id)
            ->first();

        if (! $employeeProduct instanceof EmployeeProduct) {
            return;
        }

        /** @var EmployeeService */
        $employeeService = app(EmployeeService::class);

        $employeeService->storeEmployeeProfit($employeeProduct->getEmployeeId(), $order->id);
    }
}