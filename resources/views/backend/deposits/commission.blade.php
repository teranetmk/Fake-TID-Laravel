@extends('backend.layouts.default')

@section('content')
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/dashboard.today_profit') }}</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $todaySales }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/profits.yesterday_sales') }}</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $yesterdaySales }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/profits.week_sales') }}</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $currentWeekSales }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/profits.month_sales') }}</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $currentMonthSales }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/profits.year_sales') }}</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $currentYearSales }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="k-content__head	k-grid__item">
    <div class="k-content__head-main">
        <h3 class="k-content__head-title">{{ __('backend/profits.title') }}</h3>
        <div class="k-content__head-breadcrumbs">
            <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
            <span class="k-content__head-breadcrumb-separator"></span>
            <a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
        </div>
    </div>
</div>
<div class="k-content__body	k-grid__item k-grid__item--fluid">
   
    <div class="row mt-3 mb-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header align-items-center">
                  <div class="col-auto ms-auto">
                    <h4 class="card-title text-muted">{{ \Carbon\Carbon::parse($startDate)->format('d F') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-lg mt-4" id="earnings-chart"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page_scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
          series: [{
          data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
            'United States', 'China', 'Germany'
          ],
        }
        };

        var chart = new ApexCharts(document.querySelector("#earnings-chart"), options);
        chart.render();
      

        
      
    });
</script>
@endsection