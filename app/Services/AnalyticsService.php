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
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Auth;

class AnalyticsService
{
    private $currency;

    public function __construct()
    {
        $this->currency = Setting::getShopCurrency();
    }

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
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product)  {
                return $product->whereHas('category', function ($category) {
                    $category->where('is_digital_goods',  '=' , 1);
                });
            })
            ->sum('totalprice');

        return sprintf('%s %s', number_format($todayEarnings / 100, 2, ',', '.'), $this->currency);
    }
    
    public function getTodayEarningsOfDigitalProductsBelege(){
        
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product) {
                return $product->where('name', [
                    'Digitaler Einlieferungsbeleg'
                ]);
            })
            ->sum('totalprice');

        return sprintf('%s %s', number_format($todayEarnings / 100, 2, ',', '.'), $this->currency);
        
    }

    public function getTodayEarningsOfNonDigitalProducts(): string
    {
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product) {
                return $product->whereNotIn('name', [
                    '[DE] 100% Fake-TID',
                    '[DE] Originale 80% Fake-TID',
                    'Digitaler Einlieferungsbeleg',
                ])->whereHas('category', function ($category) {
                    $category->where('is_digital_goods',  '!=', 1);
                });
            })
            ->sum('totalprice');

        return sprintf('%s %s', number_format($todayEarnings / 100, 2, ',', '.'), $this->currency);
    }

    /**
     * @return string
     */
    public function getTodayEarningsOf100PercentTids(): string
    {
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product) {
                return $product->where('name', '[DE] 100% Fake-TID');
            })
            ->sum('totalprice');
        return sprintf('%s %s', number_format($todayEarnings / 100, 2, ',', '.'), $this->currency);
    }

    public function getTodayEarningsOfOriginaleTids()
    {
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();

        $todayEarnings = UserOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('products', function ($product) {
                return $product->where('name','[DE] Originale 80% Fake-TID');
            })
            ->sum('totalprice');
        return sprintf('%s %s', number_format($todayEarnings / 100, 2, ',', '.'), $this->currency);

    }

    public function getSalesChart(CarbonPeriod $period, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): array
    {
        $orders = $this->whereInPeriod($period->getStartDate(), $period->getEndDate(), $onlyDigitalProducts, $groupByMonths);

        $profits = (Auth::user()->is_partner)?2:1;
        $filteredOrders = $orders->map(function (UserOrder $order) use ($groupByMonths,$profits) {

            return [
                'x' => ($groupByMonths === false) ? $order->created_at->toDateString() : $order->created_at->format('F'),
                'y' => round(($order->total / 100)/$profits),
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
        if(Auth::user()->is_partner){
            $query = $query->whereIn('product_id',[53,54,55]);
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


    //-----------------------------------------
    // Deposit Analytics Start
    //-----------------------------------------
    public function getAccountsDepositsChart(CarbonPeriod $period, bool $groupByMonths = false): array
    {
        return $this->getDepositsChart($period, true, $groupByMonths);
    }
    public function getTidsDepositsChart(CarbonPeriod $period, bool $groupByMonths = false): array
    {
        return $this->getDepositsChartEmpty($period, false, $groupByMonths);
    }
    public function getTodayDeposits(): string
    {
        $totalDeposits = 0;
        $orders = $this->depositsWhereInPeriod(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserTransaction $order) use (&$totalDeposits) {
            $totalDeposits += $order->total;
        });

        return round($totalDeposits / 100);
    }

    public function getYesterdayDeposits(): string
    {
        $totalDeposits = 0;
        $yesterday = Carbon::now()->subDay();
        $orders = $this->depositsWhereInPeriod($yesterday->copy()->startOfDay(), $yesterday->copy()->endOfDay());

        $orders->map(function (UserTransaction $order) use (&$totalDeposits) {
            $totalDeposits += $order->total;
        });

        return round($totalDeposits / 100);
    }
     public function getCurrentWeekDeposits(): string
    {
        $totalDeposits = 0;
        $startOfWeek = Carbon::now()->startOfWeek();
        $orders = $this->depositsWhereInPeriod($startOfWeek->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserTransaction $order) use (&$totalDeposits) {
            $totalDeposits += $order->total;
        });

        return round($totalDeposits / 100);
    }

    public function getCurrentMonthDeposits(): string
    {
        $totalDeposits = 0;
        $startOfMonth = Carbon::now()->startOfMonth();
        $orders = $this->depositsWhereInPeriod($startOfMonth->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserTransaction $order) use (&$totalDeposits) {
            $totalDeposits += $order->total;
        });

        return round($totalDeposits / 100);
    }

    public function getCurrentYearDeposits(): string
    {
        $totalDeposits = 0;
        $startOfYear = Carbon::now()->startOfYear();
        $orders = $this->depositsWhereInPeriod($startOfYear->copy()->startOfDay(), Carbon::now()->endOfDay());

        $orders->map(function (UserTransaction $order) use (&$totalDeposits) {
            $totalDeposits += $order->total;
        });

        return round($totalDeposits / 100);
    }

    public function getDepositsChart(CarbonPeriod $period, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): array
    {
        $orders = $this->depositsWhereInPeriod($period->getStartDate(), $period->getEndDate(), $onlyDigitalProducts, $groupByMonths);

        $profits = (Auth::user()->is_partner)?2:1;
        $filteredOrders = $orders->map(function (UserTransaction $order) use ($groupByMonths,$profits) {

            return [
                'x' => ($groupByMonths === false) ? $order->created_at->toDateString() : $order->created_at->format('F'),
                'y' => round(($order->total / 100)/$profits),
            ];
        });

        return $filteredOrders->toArray();
    }
    public function getDepositsChartEmpty(CarbonPeriod $period, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): array
    {
        $orders = $this->depositsWhereInPeriod($period->getStartDate(), $period->getEndDate(), $onlyDigitalProducts, $groupByMonths);

        $profits = (Auth::user()->is_partner)?2:1;
        $filteredOrders = $orders->map(function (UserTransaction $order) use ($groupByMonths,$profits) {

            return [
                'x' => ($groupByMonths === false) ? $order->created_at->toDateString() : $order->created_at->format('F'),
                'y' => 0,
            ];
        });

        return $filteredOrders->toArray();
    }
    public function depositsWhereInPeriod(Carbon $startDate, Carbon $endDate, ?bool $onlyDigitalProducts = null, bool $groupByMonths = false): Collection
    {
        $query = UserTransaction::query();
        // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){

        //     $query =  $query->whereHas('products', function ($product) {

        //         return $product->where('category_id', 10);

        //     });
        // // }
        // if(Auth::user()->is_partner){
        //     $query = $query->whereIn('product_id',[53,54,55]);
        // }
        // if (! is_null($onlyDigitalProducts)) {
        //    // dd('asdjlfjsadf');
        //     $query = $query->whereHas('products', function ($product) use ($onlyDigitalProducts) {
        //         return $product->whereHas('category', function ($category) use ($onlyDigitalProducts) {
        //             $category->where('is_digital_goods', ($onlyDigitalProducts === true ? '=' : '!='), 1);
        //         });
        //     });
        // }
        //dd($query);
        return $query->select(DB::raw('SUM(amount_cent) as total'), UserTransaction::CREATED_AT, DB::raw(sprintf('%s(created_at) as date', ($groupByMonths === false) ? 'DATE' : 'MONTH')))
            ->where(UserTransaction::CREATED_AT, '>=', $startDate)
            ->where(UserTransaction::CREATED_AT, '<=', $endDate)
            ->groupBy('date')
            ->orderBy(UserTransaction::CREATED_AT, 'ASC')
            ->get();
    }
    //-----------------------------------------
    // Deposit Analytics End
    //-----------------------------------------
    
}
