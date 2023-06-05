@extends('backend.layouts.default')

@section('page_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection

@section('content')
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">{{ __('backend/dashboard.today_deposit') }}</h3>
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
                        <h3 class="k-portlet__head-title">{{ __('backend/deposits.yesterday_sales') }}</h3>
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
                        <h3 class="k-portlet__head-title">{{ __('backend/deposits.week_sales') }}</h3>
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
                        <h3 class="k-portlet__head-title">{{ __('backend/deposits.month_sales') }}</h3>
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
                        <h3 class="k-portlet__head-title">{{ __('backend/deposits.year_sales') }}</h3>
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
        <h3 class="k-content__head-title">{{ __('backend/deposits.title') }}</h3>
        <div class="k-content__head-breadcrumbs">
            <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
            <span class="k-content__head-breadcrumb-separator"></span>
            <a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
        </div>
    </div>
</div>
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row mt-3 mb-2">
        <div class="col-md-7" style="padding: 0 !important;">
          <form style="padding: 0 !important;" id="periodForm" action="{{ route('admin.deposits.in-period') }}" method="POST">
            @csrf
            <input type="hidden" id="startDate" name="start-date"/>
            <input type="hidden" id="endDate" name="end-date"/>
            <div class="form-outline ml-3">
                <div class="input-group mb-3 euro-amount">
                    <input type="text" id="period" name="period" class="form-control " placeholder="Select a date"/>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="4" y="5" width="16" height="16" rx="2"></rect><line x1="16" y1="3" x2="16" y2="7"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="4" y1="11" x2="20" y2="11"></line><line x1="11" y1="15" x2="12" y2="15"></line><line x1="12" y1="15" x2="12" y2="18"></line></svg>
                        </span>
                    </div>
                </div>
            </div>
          </form>
        </div>
    </div>
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
    {{-- <div class="row mt-3 mb-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header align-items-center">
                  <div class="col-auto ms-auto">
                    <h4 class="card-title text-muted">{{ \Carbon\Carbon::parse($yearStartDate)->format('d F') }} - {{ \Carbon\Carbon::parse($yearEndDate)->format('d F Y') }}</h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-lg mt-4" id="year-earnings-chart"></div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
@endsection

@section('page_scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        $('#period').daterangepicker({
            "startDate": "{{ $startDate }}",
            "endDate": "{{ $endDate }}",
            locale: {
                format: 'YYYY/MM/DD'
            }
        }); 

        $('#period').on('apply.daterangepicker', function(ev, picker) {
            $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
            $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
            $('#periodForm').submit();
        });

        window.ApexCharts && (new ApexCharts(document.getElementById('earnings-chart'), {
            chart: {
                type: "bar",
                fontFamily: 'inherit',
                height: 340,
                parentHeightOffset: 0,
                stacked: true,
                redrawOnParentResize: true,
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return val + '€';
                }
            },
            stroke: {
                curve: 'smooth',
            },
            series: [{
                name: "{{ __('backend/deposits.accounts') }}",
                data: {!! json_encode($accountsSalesChart) !!}
            }],
            markers: {
                size: 0
            },
            tooltip: {
                enabled: true,
                shared: true,
                followCursor: true,
                intersect: false,
                inverseOrder: false,
                fillSeriesColor: false,
                x: {
                    format: 'dd MMMM yyyy',
                },
                y: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                        return (series[seriesIndex][dataPointIndex] || value) + '€';
                    },
                    title: {
                        formatter: (seriesName) => seriesName,
                    }
                }
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            colors: ["#fd397a", "#3c1cdd"],
            xaxis: {
                type: 'datetime',
                min: new Date("{{ $startDate }}").getTime(),
                max: new Date("{{ $endDate }}").getTime(),
                labels: {
                    format: 'dd MMMM yyyy',
                    show: true,
                    hideOverlappingLabels: true,
                    showDuplicates: false,
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
        })).render();
        
        // window.ApexCharts && (new ApexCharts(document.getElementById('year-earnings-chart'), {
        //     chart: {
        //         type: "bar",
        //         fontFamily: 'inherit',
        //         height: 340,
        //         parentHeightOffset: 0,
        //         stacked: true,
        //         redrawOnParentResize: true,
        //         zoom: {
        //             enabled: false
        //         },
        //         toolbar: {
        //             show: false
        //         },
        //     },
        //     dataLabels: {
        //         enabled: true,
        //         formatter: function (val, opts) {
        //             return val + '€';
        //         }
        //     },
        //     stroke: {
        //         curve: 'smooth',
        //     },
        //     series: [{
        //         name: "{{ __('backend/deposits.tids') }}",
        //         data: {!! json_encode($yearTidsSalesChart) !!}
        //     }, {
        //         name: "{{ __('backend/deposits.accounts') }}",
        //         data: {!! json_encode($yearAccountsSalesChart) !!}
        //     }],
        //     markers: {
        //         size: 0
        //     },
        //     tooltip: {
        //         enabled: true,
        //         shared: true,
        //         followCursor: true,
        //         intersect: false,
        //         inverseOrder: false,
        //         fillSeriesColor: false,
        //         x: {
        //             format: 'dd MMMM yyyy',
        //         },
        //         y: {
        //             formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
        //                 return (series[seriesIndex][dataPointIndex] || value) + '€';
        //             },
        //             title: {
        //                 formatter: (seriesName) => seriesName,
        //             }
        //         }
        //     },
        //     grid: {
        //         row: {
        //             colors: ['#f3f3f3', 'transparent'],
        //             opacity: 0.5
        //         },
        //     },
        //     colors: ["#fd397a", "#3c1cdd"],
        //     xaxis: {
        //         type: 'datetime',
        //         min: new Date("{{ $yearStartDate }}").getTime(),
        //         max: new Date("{{ $yearEndDate }}").getTime(),
        //         labels: {
        //             format: 'MMMM',
        //             show: true,
        //             hideOverlappingLabels: true,
        //             showDuplicates: false,
        //         },
        //     },
        //     legend: {
        //         position: 'top',
        //         horizontalAlign: 'right'
        //     },
        // })).render();
    });
</script>
@endsection