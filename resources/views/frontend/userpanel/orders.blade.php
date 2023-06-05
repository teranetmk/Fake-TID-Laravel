@extends('frontend.layouts.app')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{--
                <h3 class="page-title mt-3">{{ __('frontend/user.orders') }}</h3>
                --}}
                @if(count($user_orders))
                    <div id="orderAccordion" class="mb-15 accordion-with-icon">
                        @foreach($user_orders as $order)
                            @php
                                $isManual = $order->isManual()
                            @endphp
                            <div class="card mb-15 bg-shahzad-lightgray" id="orderHeading-{{ $loop->iteration }}">
                                <div class="card-header header-colour-none p-3">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-decoration-none collapsed"
                                                data-toggle="collapse"
                                                data-target="#orderCollapse-{{ $loop->iteration }}"
                                                aria-expanded="@if($loop->iteration == 1) true @else false @endif"
                                                aria-controls="orderCollapse-{{ $loop->iteration }}">
                                            <strong class="text-dark"> ID: {{ $order->id }}</strong> {{ $order->name }}
                                        </button>
                                    </h5>
                                </div>

                                {{--
                                -   Заказу присваевается трекинговый номер.
                                    Этот номер доступен юзеру в истории заказа,
                                    и юзер должен мочь отследить заказ, наждав на ссылку и перешев на сайт DHL https://www.dhl.de/de/privatkunden.html?piececode=XXXXX

                                -   Показываем зеленую галочку, когда TID был вставлен.
                                    В противном случае красный X, настраивается вручную в бэкэнде или автоматически через DHL API.
                                    Если TID задан, то тогда можно отсдулить заказ.

                                -   Показываем значок рядом с заказом:
                                    будь то доставка через упаковочную станцию/Packstation
                                    или через филиал Filialeinlieferung

                                -   Показываем инфомрацию для заказа,
                                    когда будет дсотавлен заказ.
                                    Заказы, сделанные после 19:00, будут доставлены лишь на следующий день
                                --}}

                                <div id="orderCollapse-{{ $loop->iteration }}"
                                     class="collapse @if($loop->iteration == 1) show @endif"
                                     aria-labelledby="orderHeading-{{ $loop->iteration }}"
                                     data-parent="#orderAccordion">

                                    {{--@php
                                        try {
                                            $showContent = false;
                                            $contentVal = '';
                                            if (strlen($order->content)) {
                                                $showContent = true;

                                                $contentVal = nl2br( e( strlen($order->content) ? $order->content : '' ) );
                                            }
                                        } catch (\Exception $e) {}
                                    @endphp
                                    @if( $showContent && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                        <div class="card-body">
                                            {!! $contentVal !!}
                                        </div>
                                    @endif--}}

                                    <ul class="list-group list-group-flush">
                                        @if ((! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                            <li class="list-group-item">
                                                <b>{{ __('frontend/shop.price') }}</b> {{ ($order->products->name !== 'Nachnahme Boxing') ? $order->getFormattedPrice() : ((number_format($order->product_payment_amount / 10, 2, ',', '.') . ' ' . $shop_currency)) }}
                                            </li>
                                        @endif

                                        @if($order->delivery_price > 0 && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                            <li class="list-group-item">
                                                <b>{{ __('frontend/shop.delivery_price') }}</b> {{ $order->getFormattedDeliveryPrice() }}
                                            </li>
                                        @endif

                                        @if($order->asWeight() && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                            <li class="list-group-item">
                                                <b>{{ __('frontend/shop.bought_weight') }}</b> {{ $order->getWeight() . $order->getWeightChar() }}
                                            </li>
                                        @endif

                                        @if( $order->type_deliver == 'desired_date' && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                            <li class="list-group-item">
                                                <b>Versandzeit:</b>
                                                5,00 EUR
                                            </li>
                                        @endif

                                        @if (! is_null($order->products) && $order->products->isDigitalGoods())
                                            <li class="list-group-item">
                                                <b>{{ __('frontend/shop.product_amount') }}</b> {{ $order->getWeight() }}
                                                Accounts
                                            </li>
                                        @endif
                                        <li class="list-group-item">
                                            <b>{{ __('frontend/shop.totalprice') }}</b> {{ $order->getFormattedTotalPrice() }}
                                        </li>
                                        @if (! is_null($order->products) && $order->products->isDigitalGoods())
                                            <li class="list-group-item">
                                                <b>{{ $order->products->getCategory() ? $order->products->getCategory()->name : ''  }}
                                                    :</b><br/>
                                                {!! $contentVal !!}
                                            </li>
                                        @endif

                                        @if(! is_null($order->products) && $order->products->name !== 'Nachnahme Boxing' && strlen($order->delivery_method) > 0 && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                            <li class="list-group-item">
                                                <b>{{ __('frontend/shop.delivery_method.title') }}</b>
                                                {{ $order->delivery_method }}
                                            </li>
                                        @endif



                                        {{--                                    @if(strlen($order->getDrop()) > 0)--}}
                                        {{--                                    <li class="list-group-item">--}}
                                        {{--                                        <b>{{ __('frontend/shop.orders_order_note') }}</b><br />--}}
                                        {{--                                        <p style="margin-top: 8px">--}}
                                        {{--                                            {!! nl2br(e($order->getDrop())) !!}--}}
                                        {{--                                        </p>--}}
                                        {{--                                    </li>--}}
                                        {{--                                    @endif--}}



                                        @if(! is_null($order->address))
                                            <li class="list-group-item two-col">
                                                <ul class="list-group">
                                                    <li class="list-group-item"><b class="mb-1">Empfänger:</b></li>
                                                    <li class="list-group-item">{{ $order->address->first_name }} {{ $order->address->last_name }}</li>
                                                    <li class="list-group-item">{{ $order->address->street }}</li>
                                                    <li class="list-group-item">{{ $order->address->zip }}</li>
                                                    <li class="list-group-item">{{ $order->address->city }}</li>
                                                    <li class="list-group-item">{{ $order->address->country }}</li>
                                                </ul>

                                                <ul class="list-group">
                                                    <li class="list-group-item"><b class="mb-1">Absender:</b></li>
                                                    <li class="list-group-item">{{ $order->address->sender_first_name }} {{ $order->address->sender_last_name }}</li>
                                                    <li class="list-group-item">{{ $order->address->sender_street }}</li>
                                                    <li class="list-group-item">{{ $order->address->sender_zip }}</li>
                                                    <li class="list-group-item">{{ $order->address->sender_city }}</li>
                                                    <li class="list-group-item">{{ $order->address->sender_country }}</li>
                                                </ul>
                                            </li>
                                        @endif

                                        <style>
                                            .two-col {
                                                display: flex;
                                                justify-content: start;
                                            }

                                            .two-col > .list-group {

                                                min-width: 33%;
                                                min-device-width: 33%;
                                            }

                                            .two-col > .list-group > .list-group-item {
                                                padding-top: 0;
                                                padding-bottom: 0.5rem;
                                            }
                                        </style>


                                        @if(! is_null($order->products) && $order->products->name == 'Nachnahme Boxing')
                                            <li class="list-group-item">
                                                <b>Nachnahme Status:</b>
                                                @if($order->boxing_status == 'package_was_accepted')
                                                    Paket wurde erfolgreich zugestellt.
                                                @elseif($order->boxing_status == 'package_was_refused')
                                                    Paketannahme wurde verweigert, kontaktiere uns bitte für einen
                                                    Replace.
                                                @else
                                                    ---
                                                @endif
                                            </li>
                                        @endif
										@if($order->name!='Digitaler Einlieferungsbeleg')
                                            @if($isManual)
                                                <li class="list-group-item">
                                                    <b>TID:</b> {{ $order->label_code ? $order->label_code  : __('Wird hier angezeigt sobald das Label gekauft wurde. Bitte schaue später noch einmal nach.') }}
                                                    @if($order->label_code)
                                                    <li class="list-group-item">
                                                        <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->label_code }}"
                                                           target="_blank">Tracking</a>
                                                    </li>
                                                    @endif
                                                </li>
                                            @elseif(!is_null($order->products) && $order->products->name !== 'Nachnahme Boxing' && isset($order->tids))
                                            <li class="list-group-item">

                                                <b>TID:</b>
                                            @if(! is_null($order->tids->tid_name))
                                                {{ ($order->products->name !== 'LIT für Refund') ? $order->tids->tid_name : $order->tracking_number }}
                                                <li class="list-group-item">
                                                    <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ ($order->products->name !== 'LIT für Refund') ? $order->tids->tid_name : $order->tracking_number }}"
                                                       target="_blank">Tracking</a>
                                                </li>
                                            @else
                                                -
                                                @endif
                                                </li>

                                            @endif

                                            @if(! is_null($order->products) && ! in_array($order->products->name, ['LIT für Refund', 'LIT für Filling']) && $order->products->name !== 'Nachnahme Boxing' && !is_null($order->delivery_name) && (! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                                <li class="list-group-item">
                                                    <b>Versandart:</b>
                                                    @if(in_array($order->products->id,[53,54,55]))
                                                        {{ $order->qrcode }}
                                                    @else
                                                        {{ $order->delivery_name }}
                                                    @endif
                                                </li>
                                            @endif










                                            @if($order->hasNotes())
                                                <li class="list-group-item">
                                                    <b>{{ __('frontend/shop.orders_notes') }}</b>
                                                </li>

                                                @foreach($order->getNotes() as $note)
                                                    <li class="list-group-item list-group-order-note">
                                                        {!! strlen($note->note) > 0 ? nl2br($note->note) : ''; !!}
                                                        <span>{{ $note->getDateTime() }}</span>
                                                    </li>
                                                @endforeach
                                            @endif

                                            <li class="list-group-item">
                                                <b>{{ __('backend/orders.table.date_of_order') }}:</b>
                                                <!--{{ $order->created_at->format('d.m.Y') }}-->
                                                {{ date('d.m.Y', strtotime($order->created_at)) }}
                                            </li>
                                            @if($order->include_receipt == 1)
                                                <li class="list-group-item">
                                                    <b>{{ __('backend/orders.table.receipt') }}:</b>
                                                    @if($order->tids->packStation)
                                                        <a href="{{ route('receipt.jpg', ['tidId' => $order->tids->id]) }}"
                                                           target="_blank">{{ __('backend/orders.table.download') }}</a>
                                                    @else
                                                        {{ __('backend/orders.table.invalid_zip') }}
                                                    @endif
                                                </li>
                                            @endif
                                            @if ((! is_null($order->products) && ! $order->products->isDigitalGoods()))
                                                <li class="list-group-item">
                                                    <b>{{ __('backend/orders.table.date_of_delivery') }}:</b>
                                                    {{ date('d.m.Y', strtotime($order->deliver_at)) }}
                                                </li>
                                            @endif
                                            @if(! is_null($order->products) && $order->products->name === 'Nachnahme Boxing')
                                                <li class="list-group-item">
                                                    <b>Produktname:</b> {{ $order->product_name }}
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Produktgröße (Länge x Breite x
                                                        Höhe):</b> {{ $order->amazon_product_link }}
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Link vom Produkt:</b> {{ $order->product_size }}
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Gewicht in KG:</b> {{ $order->product_weight }}
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Payment-Betrag:</b> {{ $order->product_payment_amount ?? 0 }}€
                                                </li>
                                                <li class="list-group-item">
                                                    <b>BTC Wert bei Zeit der
                                                        Bestellung:</b> {{ $order->total_price_in_btc ?? 0 }}฿
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Paketmarken Link (Auf
                                                        Workupload):</b> {{ $order->product_package_labels_link }}
                                                </li>
                                            @endif
                                            @endif
											
											@if($order->name=='Digitaler Einlieferungsbeleg')
											<li class="list-group-item">
                                                    <b>Ausgewähltes Datum:</b> {{ $order->post_date_time }}
                                            </li>
                                            <li class="list-group-item">
                                                    <b>Sendungsnummer:</b> {{ $order->track_number }}
                                            </li>
                                            
                                             <li class="list-group-item">
                                                    <b>Ausgewählte Stadt:</b> {{ $order->city }} {{ $order->postal }}
                                            </li>
											
											<li class="list-group-item">
												<b>Download:</b> <a href="{{ $order->download_link }}" download>Download</a>
											</li>
    											@if($order->download_link_compress)
    											<li class="list-group-item">
    												<b>Download:</b> <a href="{{ $order->download_link_compress }}" download>Download Compress image</a>
    											</li>
    											@endif
											@endif
                                    </ul>
                                </div>
                            </div>

                        @endforeach
                    </div>

                    {!! $user_orders->links() !!}
                @else
                    <div class="container">
                        <div class="container_404">
                            <h3 class="custom-block--title mt-4">
                                {{ __('frontend/user.orders_page.no_orders_exists') }}
                            </h3>
                            <img src="{{ asset('/assets/img/404.svg') }}" alt="pic" class="">
                        </div>
                    </div>
                    <!--<div class="alert alert-warning">-->
                    <!--    {{ __('frontend/user.orders_page.no_orders_exists') }}-->
                    <!--</div>-->
                @endif
            </div>
        </div>
    </div>
@endsection
