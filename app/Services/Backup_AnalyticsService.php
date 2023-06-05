<?php

/**
 * FakeTids.su
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace App\Services;

use App\Models\Employee\Employee;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use App\Models\UserOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Auth;

class Backup_AnalyticsService
{
    public function getPeriod(Carbon $startDate, Carbon $endDate): CarbonPeriod
    {
        return CarbonPeriod::create($startDate, $endDate);
    }

    public function getLast7DaysPeriod(): CarbonPeriod
    {
        $now = Carbon::now();
        $queryInterval = new CarbonPeriod();
        $startDate = $now->copy()->subDays(7)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        return $queryInterval
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }

    public function getCurrentMonthPeriod(): CarbonPeriod
    {
        $now = Carbon::now();
        $queryInterval = new CarbonPeriod();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfDay();

        return $queryInterval
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }

    public function getCurrentYearPeriod(): CarbonPeriod
    {
        $now = Carbon::now();
        $queryInterval = new CarbonPeriod();
        $startDate = $now->copy()->startOfYear();
        $endDate = $now->copy()->endOfDay();

        return $queryInterval
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }

    public function getProductOrdersCount(string $productName, bool $onlyToday = true): int
    {
        $query = UserOrder::query()
            ->whereHas('products', function ($product) use ($productName) {
                return $product->where('name', $productName);
            });

        if ($onlyToday === true) {
            $from = Carbon::now()->startOfDay();
            $to = Carbon::now()->endOfDay();

            $query = $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->count();
    }

    public function getOrdersCountByDeliveryMethod(string $deliveryMethod, bool $onlyToday = true): int
    {
        $query = UserOrder::query()
            ->where('delivery_name', $deliveryMethod);

        if ($onlyToday === true) {
            $from = Carbon::now()->startOfDay();
            $to = Carbon::now()->endOfDay();

            $query = $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->count();
    }

    public function getTodayEarningsOfProducts(bool $onlyDigitalProducts = false): float
    {
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product) use ($onlyDigitalProducts) {
                return $product->whereHas('category', function ($category) use ($onlyDigitalProducts) {
                    $category->where('is_digital_goods', ($onlyDigitalProducts === true ? '=' : '!='), 1);
                });
            })
            ->sum('totalprice');

        return $todayEarnings;
    }

    public function getTodayEarningsOfDigitalProducts(): string
    {
        return sprintf('%s %s', number_format($this->getTodayEarningsOfProducts(true) / 100, 2, ',', '.'), Setting::getShopCurrency());
    }

    public function getTodayEarningsOfNonDigitalProducts(): string
    {
        return sprintf('%s %s', number_format($this->getTodayEarningsOfProducts() / 100, 2, ',', '.'), Setting::getShopCurrency());
    }

    public function getSalesChart(CarbonPeriod $period, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): array
    {
        $orders = $this->whereInPeriod($period->getStartDate(), $period->getEndDate(), $onlyDigitalProducts, $groupByMonths);


        $filteredOrders = $orders->map(function (UserOrder $order) use ($groupByMonths) {

            return [
                'x' => ($groupByMonths === false) ? $order->created_at->toDateString() : $order->created_at->format('F'),
                'y' => round($order->total / 100)
            ];
        });

        return $filteredOrders->toArray();
    }

    public function getCommissionChart($startDate, $endDate): array
    {
        //DB::enableQueryLog();
        $cartdata = array('names'=>array(),'commission'=>array());
        $employeename = Employee::pluck('name')->first();
        $totalcommission = 0;
        $firstpool = UserOrder::WhereIn('product_id',[1,2,6,15,16])->whereBetween('created_at', [$startDate, $endDate])->sum('totalprice');

        $secondpool = UserOrder::WhereIn('product_id',[7,8,9])->whereBetween('created_at', [$startDate, $endDate])->sum('totalprice');

        $totalcommission = round((($firstpool/100)*0.33)+(($secondpool/100)*0.33));

        if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
            $commission = UserOrder::select(DB::raw('sum(totalprice/100) as quantity_sum'))->join('products','products.id','=','users_orders.product_id')->where('products.category_id',10);
            $totalcommission = $commission->quantity_sum*0.30;

        }
        $cartdata['names'][] = $employeename;
        $cartdata['commission'][] = round($totalcommission);
        // $profits  = DB::table('employee_profits')->select('employees.name',DB::raw('sum(totalprice/100) as quantity_sum'))->join('employees','employee_profits.employee_id','=','employees.id')->join('users_orders','employee_profits.order_id','=','users_orders.id');
        // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
        //     $alluserproductsIds = Product::select('id')->where('category_id',10)->get();
        //     $profits = $profits->whereIn('users_orders.product_id',$alluserproductsIds);
        // }
        // $profits = $profits->whereBetween('employee_profits.created_at', [$startDate, $endDate])->groupby('employee_profits.employee_id')->get();
        //dd(DB::getQueryLog());
        // if(!$profits->isEmpty()){

        //     foreach($profits as $profit){
        //         $cartdata['names'][] = $profit->name;
        //         $cartdata['commission'][] = round($profit->quantity_sum*0.30);
        //     }
        // }

           //dd($cartdata);
        return $cartdata;

    }

    public function commissionamount($startDate, $endDate): string
    {
        $totalcommission = 0;
        $firstpool = UserOrder::WhereIn('product_id',[1,2,6,15,16])->whereBetween('created_at', [$startDate, $endDate])->sum('totalprice');

        $secondpool = UserOrder::WhereIn('product_id',[7,8,9])->whereBetween('created_at', [$startDate, $endDate])->sum('totalprice');

        $totalcommission = round((($firstpool/100)*0.33)+(($secondpool/100)*0.33));
        // $profits  = DB::table('employee_profits')->select(DB::raw('sum(totalprice/100) as quantity_sum'))->join('users_orders','employee_profits.order_id','=','users_orders.id');
        if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
            $commission = UserOrder::select(DB::raw('sum(totalprice/100) as quantity_sum'))->join('products','products.id','=','users_orders.product_id')->where('products.category_id',10);
            $totalcommission = round($commission->quantity_sum*0.30);

        }
        // $profits = $profits->whereBetween('employee_profits.created_at', [$startDate, $endDate])->groupby('employee_profits.employee_id')->first();


        return $totalcommission;


    }

    public function getAccountsSalesChart(CarbonPeriod $period, bool $groupByMonths = false): array
    {
        return $this->getSalesChart($period, true, $groupByMonths);
    }

    public function getTidsSalesChart(CarbonPeriod $period, bool $groupByMonths = false): array
    {
        return $this->getSalesChart($period, false, $groupByMonths);
    }

    public function getTodaySales(): string
    {
        $totalSales = 0;
        $orders = $this->whereInPeriod(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserOrder $order) use (&$totalSales) {
            $totalSales += $order->total;
        });

        return round($totalSales / 100);
    }

    public function getYesterdaySales(): string
    {
        $totalSales = 0;
        $yesterday = Carbon::now()->subDay();
        $orders = $this->whereInPeriod($yesterday->copy()->startOfDay(), $yesterday->copy()->endOfDay());

        $orders->map(function (UserOrder $order) use (&$totalSales) {
            $totalSales += $order->total;
        });

        return round($totalSales / 100);
    }

    public function getEmployeeWeeklyOrders($startDate,$endDate){
       $totalOrders =  DB::table('employee_profits')->whereBetween('created_at', [$startDate, $endDate])->groupby('employee_profits.employee_id')->count();
       return $totalOrders;
    }
    public function getCurrentWeekSales(): string
    {
        $totalSales = 0;
        $startOfWeek = Carbon::now()->startOfWeek();
        $orders = $this->whereInPeriod($startOfWeek->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserOrder $order) use (&$totalSales) {
            $totalSales += $order->total;
        });

        return round($totalSales / 100);
    }

    public function getCurrentMonthSales(): string
    {
        $totalSales = 0;
        $startOfMonth = Carbon::now()->startOfMonth();
        $orders = $this->whereInPeriod($startOfMonth->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserOrder $order) use (&$totalSales) {
            $totalSales += $order->total;
        });

        return round($totalSales / 100);
    }

    public function getCurrentYearSales(): string
    {
        $totalSales = 0;
        $startOfYear = Carbon::now()->startOfYear();
        $orders = $this->whereInPeriod($startOfYear->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserOrder $order) use (&$totalSales) {
            $totalSales += $order->total;
        });

        return round($totalSales / 100);
    }

    public function whereInPeriod(Carbon $startDate, Carbon $endDate, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): Collection
    {
        $query = UserOrder::query();
        if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){

            $query =  $query->whereHas('products', function ($product) {

                return $product->where('category_id', 10);

            });
        }
        if (! is_null($onlyDigitalProducts)) {
           // dd('asdjlfjsadf');
            $query = $query->whereHas('products', function ($product) use ($onlyDigitalProducts) {
                return $product->whereHas('category', function ($category) use ($onlyDigitalProducts) {
                    $category->where('is_digital_goods', ($onlyDigitalProducts === true ? '=' : '!='), 1);
                });
            });
        }
        //dd($query);
        return $query->select(DB::raw('SUM(totalprice) as total'), UserOrder::CREATED_AT, DB::raw(sprintf('%s(created_at) as date', ($groupByMonths === false) ? 'DATE' : 'MONTH')))
            ->where(UserOrder::CREATED_AT, '>=', $startDate)
            ->where(UserOrder::CREATED_AT, '<=', $endDate)
            ->groupBy('date')
            ->orderBy(UserOrder::CREATED_AT, 'ASC')
            ->get();
    }
}
