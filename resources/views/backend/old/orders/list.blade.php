@extends('backend.layouts.default')

@section('content')
    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/orders.title') }}</h3>
        </div>
    </div>


    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <div class="row">

            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_packstation'))
            @component('components.backend.orders.widget')
                @slot('title')
                    Packstation
                @endslot 

                {{ $packing_station_count }}
            @endcomponent
            @endif

            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_filialeinlieferung'))
            @component('components.backend.orders.widget')
                @slot('title')
                    Filialeinlieferung
                @endslot
                {{ $branch_delivery_count }}
            @endcomponent
            @endif

            @if(Auth::user()->hasPermission('manage_orders'))
            <?php $not_changed = App\Models\ShippingAddress::where('recipient_first_name', null)->count(); ?>
            @component('components.backend.orders.widget')
                @slot('title')
                    Adresse nicht geändert
                @endslot
                {{ $not_changed }}
            @endcomponent
            @endif

            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_filling'))
            @component('components.backend.orders.widget')
                @slot('title')
                    LIT Filling
                @endslot
                {{ $ordersLitFillingCount ?? 0 }}
            @endcomponent
            @endif

            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_refund'))
            @component('components.backend.orders.widget')
                @slot('title')
                    LIT Refund
                @endslot
                {{ $ordersLitRefundCount ?? 0 }}
            @endcomponent
            @endif

            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_nachnahme'))
            @component('components.backend.orders.widget')
                @slot('title')
                    Nachnahme
                @endslot
                {{ $ordersBoxingCount ?? 0 }}
            @endcomponent
            @endif
        </div>
    </div>


    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <form class="inline-form mb-4">
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" name="term" value="{{ $term }}" class="form-control" placeholder="Schlüsselwörter: Benutzername, Bestell-ID, DHL-Tracking-Nummer"/>
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;Suche</button>
                </div>
            </div>
        </form>


        <div class="navbar navbar-expand-lg">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <form action="{{ route( 'backend-orders-with-pageNumber', 1 ) }}" method="get" class="row">

                        <div class="col">
                            <input type="date"
                                   name="dateFilter[from]"
                                   class="form-control daterangepicker_from"
                                   value="@if(request()->has('dateFilter')){{ \Carbon\Carbon::parse(request()->input('dateFilter')['from'])->format('Y-m-d') }}@endif">
                        </div>

                        <div class="col">
                            <input type="date"
                                   name="dateFilter[to]"
                                   class="form-control daterangepicker_to col"
                                   value="@if(request()->has('dateFilter')){{ \Carbon\Carbon::parse(request()->input('dateFilter')['to'])->format('Y-m-d') }}@endif">
                        </div>

                        <div class="col">
                            <select name="statusFilter" class="form-control col">
                                <option value="" selected>all</option>
                                <option value="cancelled"
                                        @if(request()->input('statusFilter') == 'cancelled') selected @endif>
                                    {{ __('backend/orders.status.cancelled') }}
                                </option>
                                <option value="completed"
                                        @if(request()->input('statusFilter') == 'completed') selected @endif>
                                    {{ __('backend/orders.status.completed') }}
                                </option>
                                <option value="pending"
                                        @if(request()->input('statusFilter') == 'pending') selected @endif>
                                    {{ __('backend/orders.status.pending') }}
                                </option>
                            </select>
                        </div>

                        {{--                        <div class="col">--}}
                        {{--                            <select name="typeFilter" id="" class="form-control col">--}}
                        {{--                                <option value="" selected>all</option>--}}
                        {{--                                <option value="branch_delivery"--}}
                        {{--                                        @if(request()->input('typeFilter') == 'branch_delivery') selected @endif>--}}
                        {{--                                    Filialeinlieferung--}}
                        {{--                                </option>--}}
                        {{--                                <option value="packing_station"--}}
                        {{--                                        @if(request()->input('typeFilter') == 'packing_station') selected @endif>--}}
                        {{--                                    Packstation--}}
                        {{--                                </option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}

                        <button type="submit" class="btn btn-brand mx-4">{{ __('backend/orders.fiters.fiter') }}</button>
                    </form>
                </li>


                <li class="nav-item">
                    <form action="{{ route( 'backend-orders-clearFilter' ) }}" method="post" class="row">
                        @csrf
                        <button type="submit" class="btn btn-brand mx-4">{{ __('backend/orders.fiters.clear_filter') }}</button>
                    </form>
                </li>

                <li class="nav-item">
                    <form action="{{ route( 'backend-orders-with-pageNumber', 1 ) }}" method="get" class="row">
                        <input type="hidden" name="dateFilter[from]"
                               value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <input type="hidden" name="dateFilter[to]"
                               value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

                        <button type="submit" class="btn btn-brand mx-4">{{ __('backend/orders.fiters.today_oders') }}</button>
                    </form>
                </li>

                <li class="nav-item">
                    <form action="{{ route( 'address-not-changed', 1 ) }}" method="get" class="row">
                        <input type="hidden" name="address_not_changed"
                               value="null">

                        <button type="submit" class="btn btn-brand mx-4">{{ __('backend/orders.fiters.address_not_changed') }}</button>
                    </form>
                </li>
                
            </ul>
        </div>


        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_packstation'))
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#packing_station">Packstation</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_filialeinlieferung'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#branch_delivery">Filialeinlieferung</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_filling'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#lit_filling">LIT Filling</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_refund'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#lit_refund">LIT Refund</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_nachnahme'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#nachnahme">Nachnahme</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_random'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#random">Random</a>
            </li>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_accounts'))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#accounts">Accounts</a>
            </li>
            @endif
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_packstation'))
            <div class="tab-pane container active" id="packing_station" style="max-width: 100%">
                @component('components.backend.orders.order_table', [ 'orders' => $orders_packing_station ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_filialeinlieferung'))
            <div class="tab-pane container" id="branch_delivery" style="max-width: 100%">
                @component('components.backend.orders.order_table', [ 'orders' => $orders_branch_delivery ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_filling'))
            <div class="tab-pane container" id="lit_filling" style="max-width: 100%">
                @component('components.backend.orders.order_table', [ 'orders' => $ordersLitFilling ?? [] ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_lit_refund'))
            <div class="tab-pane container" id="lit_refund" style="max-width: 100%">
                @component('components.backend.orders.order_table_refund', [ 'orders' => $ordersLitRefund ?? [] ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_nachnahme'))
            <div class="tab-pane container" id="nachnahme" style="max-width: 100%">
                @component('components.backend.orders.order_table_nachnahme', [ 'orders' => $ordersBoxing ?? [] ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_random'))
            <div class="tab-pane container" id="random" style="max-width: 100%">
                @component('components.backend.orders.order_table_random', [ 'orders' => $ordersRandom ?? [] ])@endcomponent
            </div>
            @endif
            @if(Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('manage_orders_accounts'))
            <div class="tab-pane container" id="accounts" style="max-width: 100%">
                @component('components.backend.orders.order_table_accounts', [ 'orders' => $ordersAccounts ?? [] ])@endcomponent
            </div>
            @endif
        </div>

    </div>
@endsection

@section('page_scripts')


    <script>

        $(document).ready(function () {

            $('.daterangepicker_from').change(function () {
                var from = new Date($(this).val());
                var to = $('.daterangepicker_to').val();

                if (to !== '') {
                    from = new Date($('.daterangepicker_to').val());
                    if (from > to) $(this).val('');
                }

                var dd = from.getDate();
                var mm = from.getMonth() + 1; //January is 0!
                var yyyy = from.getFullYear();

                if (dd < 50) dd = '0' + dd;
                if (mm < 50) mm = '0' + mm;
                from = yyyy + '-' + mm + '-' + dd;

                $('.daterangepicker_to').prop('min', from);
            });


            $('.daterangepicker_to').change(function () {
                var from = $('.daterangepicker_from').val();
                var to = new Date($(this).val());

                if (from !== '') {
                    from = new Date($('.daterangepicker_from').val());

                    if (from > to)
                        $(this).val('');

                }

                var dd = to.getDate();
                var mm = to.getMonth() + 1; //January is 0!
                var yyyy = to.getFullYear();

                if (dd < 50) dd = '0' + dd;
                if (mm < 50) mm = '0' + mm;

                to = yyyy + '-' + mm + '-' + dd;

                $('.daterangepicker_from').prop('max', to);
            });



            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
            var activeTab = localStorage.getItem('activeTab');

            if (activeTab) {
                $('.nav-item a[href="' + activeTab + '"]').tab('show');
            }

            // $('.nav-tabs a').click(function () {
            $('a[data-toggle="tab"]').click(function () {
                $(this).tab('show');
            });


        });




    </script>

@endsection
