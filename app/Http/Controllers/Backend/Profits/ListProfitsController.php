<?php

namespace App\Http\Controllers\Backend\Profits;

use Carbon\Carbon;
use App\Services\AnalyticsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyticsRequest;
use Auth;
class ListProfitsController extends Controller
{
    /** @var AnalyticsService */
    private $analyticsService;

    public function __construct(AnalyticsService $analyticsService) 
    {
        $this->analyticsService = $analyticsService;
    }

    public function __invoke(AnalyticsRequest $request)
    {
        $currentMonth = $this->analyticsService->getCurrentMonthPeriod();
        $currentYear = $this->analyticsService->getCurrentYearPeriod();

        $startDate = $request->input('start-date', $currentMonth->getStartDate()->format('Y/m/d'));
        $endDate = $request->input('end-date', $currentMonth->getEndDate()->format('Y/m/d'));
        //$startDate = '2022-06-01';
        //$endDate = '2022-06-30';

        $period = $this->analyticsService->getPeriod(Carbon::parse($startDate . ' 00:00:00'), Carbon::parse($endDate . ' 23:59:59'));
        if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){

            return view(
                'frontend.vendor.profit',
                [
                    'currentMonth'          => $currentMonth,
                    'startDate'             => $startDate,
                    'endDate'               => $endDate,
                    'yearStartDate'         => $currentYear->getStartDate(),
                    'yearEndDate'           => $currentYear->getEndDate(),
                    'accountsSalesChart'    => $this->analyticsService->getAccountsSalesChart($period),
                    'tidsSalesChart'        => $this->analyticsService->getTidsSalesChart($period),
                    'yearAccountsSalesChart'=> $this->analyticsService->getAccountsSalesChart($currentYear, true),
                    'yearTidsSalesChart'    => $this->analyticsService->getTidsSalesChart($currentYear, true),
                    'todaySales'            => $this->analyticsService->getTodaySales(),
                    'yesterdaySales'        => $this->analyticsService->getYesterdaySales(),
                    'currentWeekSales'      => $this->analyticsService->getCurrentWeekSales(),
                    'currentMonthSales'     => $this->analyticsService->getCurrentMonthSales(),
                    'currentYearSales'      => $this->analyticsService->getCurrentYearSales(),
                ]
            );
        }


        return view(
            'backend.profits.index',
            [
                'currentMonth'          => $currentMonth,
                'startDate'             => $startDate,
                'endDate'               => $endDate,
                'yearStartDate'         => $currentYear->getStartDate(),
                'yearEndDate'           => $currentYear->getEndDate(),
                'accountsSalesChart'    => $this->analyticsService->getAccountsSalesChart($period),
                'tidsSalesChart'        => $this->analyticsService->getTidsSalesChart($period),
                'yearAccountsSalesChart'=> $this->analyticsService->getAccountsSalesChart($currentYear, true),
                'yearTidsSalesChart'    => $this->analyticsService->getTidsSalesChart($currentYear, true),
                'todaySales'            => $this->analyticsService->getTodaySales(),
                'yesterdaySales'        => $this->analyticsService->getYesterdaySales(),
                'currentWeekSales'      => $this->analyticsService->getCurrentWeekSales(),
                'currentMonthSales'     => $this->analyticsService->getCurrentMonthSales(),
                'currentYearSales'      => $this->analyticsService->getCurrentYearSales(),
            ]
        );
    }
}
