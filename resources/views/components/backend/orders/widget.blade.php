<div class="col-lg-12 col-xl-2 order-lg-1 order-xl-1">
    <div class="k-portlet k-portlet--height-fluid">
        <div class="k-portlet__head  k-portlet__head--noborder">
            <div class="k-portlet__head-label">
                <h3 class="k-portlet__head-title">{{ $title }}</h3>
            </div>
        </div>
        <div class="k-portlet__body k-portlet__body--fluid">
            <div class="k-widget-20">
                <div class="k-widget-20__title">
                    <div class="k-widget-20__label">{{ $slot }}</div>
                    <img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}"
                         alt="bg"/>
                </div>
            </div>
        </div>
    </div>
</div>
