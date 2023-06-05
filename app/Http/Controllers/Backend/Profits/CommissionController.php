<?php

namespace App\Http\Controllers\Backend\Profits;

use Carbon\Carbon;
use App\Services\AnalyticsService;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserOrder;
use Auth;
class CommissionController extends Controller
{
    /** @var AnalyticsService */
    private $analyticsService;
    private $currentweekstart;
    private $currentweekend;
    private $startOfLastWeek;
    private $endOfLastWeek;
    public function __construct(AnalyticsService $analyticsService) 
    {
        $this->analyticsService = $analyticsService;
        $this->currentweekstart = Carbon::now()->startOfWeek();
        $this->currentweekend = Carbon::now()->endOfWeek();
        $this->startOfLastWeek  = Carbon::now()->subDays(7)->startOfWeek();
        $this->endOfLastWeek  = Carbon::now()->subDays(7)->endOfWeek();
    }
    
    public function show_commision(){

        if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
           
            return view(
                'frontend.vendor.chart',
                [
                    'currentweekstart' =>  $this->currentweekstart ,
                    'currentweekend'=> $this->currentweekend,
                    'startOfLastWeek' =>  $this->startOfLastWeek ,
                    'endOfLastWeek'=> $this->endOfLastWeek,
                    'currentcommissionchart'=> $this->analyticsService->getCommissionChart($this->currentweekstart,$this->currentweekend),
                    'lastcommissionchart'    => $this->analyticsService->getCommissionChart($this->startOfLastWeek,$this->endOfLastWeek),
                    'currentweekorder'    => $this->analyticsService->commissionamount($this->currentweekstart,$this->currentweekend),
                    'lastweekorder'    => $this->analyticsService->commissionamount($this->startOfLastWeek,$this->endOfLastWeek),
                ]
            );
        }
        else{
            return view(
                'backend.chart',
                [
                    'currentweekstart' =>  $this->currentweekstart ,
                    'currentweekend'=> $this->currentweekend,
                    'startOfLastWeek' =>  $this->startOfLastWeek ,
                    'endOfLastWeek'=> $this->endOfLastWeek,
                    'currentcommissionchart'=> $this->analyticsService->getCommissionChart($this->currentweekstart,$this->currentweekend),
                    'lastcommissionchart'    => $this->analyticsService->getCommissionChart($this->startOfLastWeek,$this->endOfLastWeek),
                    'currentweekorder'    => $this->analyticsService->commissionamount($this->currentweekstart,$this->currentweekend),
                    'lastweekorder'    => $this->analyticsService->commissionamount($this->startOfLastWeek,$this->endOfLastWeek),
                ]
            );
        }
        
    }

    public function cashonDeliveryChart(){
        $thisweekpackagecost = $lastweekpackagecost = $thisweektotalpayout = $lastweektotalPayout = 0;
        //dd($this->currentweekstart.'>>>>>>.'.$this->currentweekend);
        $productids = Product::whereIn('category_id',[11,12,13,7])->pluck('id')->all();
     
        $thisweektotalorders = UserOrder::WhereIn('product_id',$productids)->whereBetween('created_at', [$this->currentweekstart, $this->currentweekend])->count();

        $lastweektotalorders = UserOrder::WhereIn('product_id',$productids)->whereBetween('created_at', [$this->startOfLastWeek, $this->endOfLastWeek])->count();

        $thisweektotalpayout = UserOrder::WhereIn('product_id',$productids)->where('status','package_was_accepted')->whereBetween('created_at', [$this->currentweekstart, $this->currentweekend])->sum('totalprice')/100;

        $lastweektotalPayout = UserOrder::WhereIn('product_id',$productids)->where('status','package_was_accepted')->whereBetween('created_at', [$this->startOfLastWeek, $this->endOfLastWeek])->sum('totalprice')/100;
        if($thisweektotalorders>0){
            $thisweekpackagecost = $thisweekpackagecost+ ($thisweektotalorders*30);
        }
        if($lastweektotalorders>0){
            $thisweekpackagecost = $thisweekpackagecost+ ($lastweektotalorders*30);
        }
        return view('backend.cod',[
            'currentweekstart' =>  $this->currentweekstart ,
            'currentweekend'=> $this->currentweekend,
            'startOfLastWeek' =>  $this->startOfLastWeek ,
            'endOfLastWeek'=> $this->endOfLastWeek,
            'thisweekpackagecost' => $thisweekpackagecost,
            'lastweekpackagecost' => $lastweekpackagecost,
            'thisweektotalpayout' => $thisweektotalpayout*0.033,
            'lastweektotalpayout' => $lastweektotalPayout* 0.033
        ]

        );
    }
}