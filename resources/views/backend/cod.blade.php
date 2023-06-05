@extends('backend.layouts.default')

@section('content')
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-4 order-lg-1 order-xl-1">
            <div class="k-portlet k-portlet--height-fluid">
                <div class="k-portlet__head  k-portlet__head--noborder">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">DieseWoche Verpackungskosten</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $thisweekpackagecost.' €' }} </div>
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
                        <h3 class="k-portlet__head-title">Vorwoche Verpackungskosten</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $lastweekpackagecost. ' €' }}</div>
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
                        <h3 class="k-portlet__head-title">DieseWoche Auszahlungsgewinne</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $thisweektotalpayout.' €' }} </div>
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
                        <h3 class="k-portlet__head-title">Vorwoche Auszahlungsgewinne</h3>
                    </div>
                </div>
                <div class="k-portlet__body k-portlet__body--fluid">
                    <div class="k-widget-20">
                        <div class="k-widget-20__title">
                            <div class="k-widget-20__label">{{ $lastweektotalpayout. ' €' }}</div>
                            <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
