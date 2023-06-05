@extends('backend.layouts.default')

@section('content')
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-4 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">diese Woche Provision</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $currentweekorder.' €' }} </div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-4 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">Vorwoche Provision</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $lastweekorder. ' €' }}</div>
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
            <a href="javascript:;" class="k-content__head-breadcrumb-link">wöchentliche Provision</a>
        </div>
    </div>
</div>
<div class="k-content__body	k-grid__item k-grid__item--fluid">
   
    <div class="row mt-3 mb-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header align-items-center">
                  <div class="col-auto ms-auto">
                    <h4 class="card-title text-muted">diese Woche Provision {{ \Carbon\Carbon::parse($currentweekstart)->format('d F') }} - {{ \Carbon\Carbon::parse($currentweekend)->format('d F Y') }}</h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-lg mt-4" id="curent-week-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header align-items-center">
                  <div class="col-auto ms-auto">
                    <h4 class="card-title text-muted">Vorwoche Provision {{ \Carbon\Carbon::parse($startOfLastWeek)->format('d F') }} - {{ \Carbon\Carbon::parse($endOfLastWeek)->format('d F Y') }} </h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-lg mt-4" id="last-week-chart"></div>
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
          name: "Beteiligung",
          data: {!! json_encode($currentcommissionchart['commission']) !!}
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
          categories: {!! json_encode($currentcommissionchart['names']) !!},
        }
        };

        var options2 = {
          series: [{
            name: "Beteiligung",
          data: {!! json_encode($lastcommissionchart['commission']) !!}
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
          categories:  {!! json_encode($lastcommissionchart['names']) !!},
        }
        };

        var chart = new ApexCharts(document.querySelector("#curent-week-chart"), options);
        chart.render();
        var chart = new ApexCharts(document.querySelector("#last-week-chart"), options2);
        chart.render();
      
    });
</script>
@endsection