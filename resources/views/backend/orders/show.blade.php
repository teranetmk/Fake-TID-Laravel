@extends('backend.layouts.default')

@section('content')

    @php
       $isManual = $order->isManual()
    @endphp
    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/orders.show.title', ['id' => $order->id]) }}</h3>
            <div class="k-content__head-breadcrumbs">
                <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
                <span class="k-content__head-breadcrumb-separator"></span>
                <a href="{{ route('backend-orders') }}"
                   class="k-content__head-breadcrumb-link">{{ __('backend/orders.title') }}</a>
            </div>
        </div>
    </div>

    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
                <div class="k-portlet k-portlet--height-fluid">

                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{ __('backend/orders.show.block_title') }}</h3>
                        </div>
                    </div>


                    <ul class="list-group">

                        @if($isManual && $order->label_code)
                            <li class="list-group-item">
                                    <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->label_code }}"
                                       target="_blank">{{ __('backend/orders.show.track') }}</a>
                            </li>
                        @elseif($product->name !== 'Nachnahme Boxing' && ! $order->products->isDigitalGoods())
                        <li class="list-group-item">
                            @if(! $order->products->isDigitalGoods() && ! is_null($order->tid) )
                                <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->tid }}"
                                   target="_blank">{{ __('backend/orders.show.track') }}</a>
                            @elseif($order->products->name === 'LIT für Refund')
                            <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->tracking_number }}"
                                target="_blank">{{ __('backend/orders.show.track') }}</a>
                            @endif
                        </li>
                        @endif

                        @if ($order->products->isDigitalGoods())
                        <li class="list-group-item">
                            <b>{{ __('frontend/shop.product_amount') }}</b> {{ $order->getWeight() }} Accounts
                        </li>
                        @endif

                        @if($isManual)
                            @if($order->hasPdf())
                                    <li class="list-group-item">
                                        <a href="{{ route( 'backend-orders-downloadTidFile-manual', $order->id ) }}">{{ __('backend/orders.show.download') }}</a>
                                    </li>
                            @endif
                        @elseif($product->name !== 'Nachnahme Boxing' && ! $order->products->isDigitalGoods())
                        <li class="list-group-item">
                            <a href="{{ route( 'backend-orders-downloadTidFile', $order->id ) }}">{{ __('backend/orders.show.download') }}</a>
                        </li>
                        @endif

                        @if($product->name === 'Nachnahme Boxing')
                        <li class="list-group-item">
                            <b>Price in EUR:</b> {{ ($order->product_payment_amount / 10) ?? 0 }}€
                        </li>
                        <li class="list-group-item">
                            <b>Price in BTC:</b> {{ $order->total_price_in_btc ?? 0 }}฿
                        </li>
                        @endif

                        @if ($order->products->isDigitalGoods())
                        @php
                            try {
                                $showContent = false;
                                $contentVal = '';
                                if (strlen($order->content)) {
                                    $showContent = true;

                                    $contentVal = nl2br( e( strlen($order->content) ? $order->content : '' ) );
                                }
                            } catch (\Exception $e) {}
                        @endphp
                        <li class="list-group-item">
                            <b>{{ $order->products->getCategory() ? $order->products->getCategory()->name : ''  }}:</b><br/>
                            {!! $contentVal !!}
                        </li>
                        @endif

                        @if(! $order->products->isDigitalGoods())
                        <li class="list-group-item">

                            <form action="{{ route( 'backend-order-setStatus', ['id' => $order->id] ) }}" method="post"
                                  class="input-group">

                                @csrf

                                <select name="status" class="form-control">
                                    <option value="cancelled"
                                            @if($order->status == 'cancelled') selected @endif>
                                        {{ __('backend/orders.status.cancelled') }}
                                    </option>
                                    <option value="completed"
                                            @if($order->status == 'completed') selected @endif>
                                        {{ __('backend/orders.status.completed') }}
                                    </option>
                                    <option value="pending"
                                            @if($order->status == 'pending') selected @endif>
                                        {{ __('backend/orders.status.pending') }}
                                    </option>
                                </select>

                                <div class="input-group-append">
                                    <input type="submit" class="btn btn-brand mx-3" value="Submit {{ __('backend/orders.show.save') }}"/>
                                </div>
                            </form>
                        </li>
                        @endif

                        @if($product->name === 'Nachnahme Boxing')
                        <li class="list-group-item">

                            <form action="{{ route( 'backend-order-setstatus-for-boxing', ['id' => $order->id] ) }}" method="post"
                                  class="input-group">

                                @csrf

                                <select name="status" class="form-control">
                                    <option value="pending"
                                            @if($order->boxing_status == 'pending') selected @endif>
                                        ---
                                    </option>
                                    <option value="package_was_accepted"
                                        @if($order->boxing_status == 'package_was_accepted') selected @endif>
                                        {{ __('backend/orders.status.package_was_accepted') }}
                                    </option>
                                    <option value="package_was_refused"
                                        @if($order->boxing_status == 'package_was_refused') selected @endif>
                                        {{ __('backend/orders.status.package_was_refused') }}
                                    </option>
                                </select>

                                <div class="input-group-append">
                                    <input type="submit" class="btn btn-brand mx-3" value="Submit {{ __('backend/orders.show.save') }}"/>
                                </div>
                            </form>
                        </li>

                        <li class="list-group-item">

                            <form action="{{ route( 'backend-order-set-replace-status-for-boxing', ['id' => $order->id] ) }}" method="post"
                                  class="input-group">

                                @csrf

                                <select name="status" class="form-control">
                                    <option value="pending"
                                            @if($order->replace_status == 'pending') selected @endif>
                                        ---
                                    </option>
                                    <option value="replace_has_been_paid"
                                        @if($order->replace_status == 'replace_has_been_paid') selected @endif>
                                        {{ __('backend/orders.status.replace_has_been_paid') }}
                                    </option>
                                </select>

                                <div class="input-group-append">
                                    <input type="submit" class="btn btn-brand mx-3" value="Submit {{ __('backend/orders.show.save') }}"/>
                                </div>
                            </form>
                        </li>
                        @endif

                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.product_name') }}</b>
                            <a href="{{ route('backend-management-product-edit', $product->id) }}" target="_blank">
                                {{ $product->name }}
                            </a>
                        </li>

                        {{-- <li class="list-group-item">
                            <b>{{ __('backend/orders.show.description') }}</b>
                            {!! nl2br($product->description) !!}
                        </li>

                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.short_description') }}</b>
                            {!! nl2br($product->short_description) !!}
                        </li> --}}

                       @if(!$isManual && $product->name !== 'Nachnahme Boxing' && ! $order->products->isDigitalGoods() && ! is_null($order->tid))
                            <li class="list-group-item">
                                <b>TID:</b> {{ $order->tid }}
                            </li>
                        @endif

                        @if(! $order->products->isDigitalGoods() && $product->name !== 'Nachnahme Boxing')
                        <li class="list-group-item">
                            <b>Versandart:</b> {{ $order->delivery_name }}
                        </li>
                        @endif

                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.created') }}</b> {{ $order->created_at->format('d.m.Y H:i') }}
                        </li>

                        @if(! $order->products->isDigitalGoods() && $product->name !== 'Nachnahme Boxing')
                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.deliver') }}</b> {{ $order->deliver }}
                        </li>
                        @endif

                        @if($product->name === 'Nachnahme Boxing')
                        <li class="list-group-item">
                            <b>Produktname:</b> {{ $order->product_name }}
                        </li>
                        <li class="list-group-item">
                            <b>Produktgröße (Länge x Breite x Höhe):</b> {{ $order->amazon_product_link }}
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
                            <b>Paketmarken Link (Auf Workupload):</b> {{ $order->product_package_labels_link }}
                        </li>
                        @endif

                        @if($order->label_code)
                                <li class="list-group-item">
                                    <b>TID:</b> {{ $order->label_code }}
                                </li>
                        @endif
                        @if(! $order->products->isDigitalGoods() && ! is_null($order->address))
                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.form.receiver') }}</b>
                            <ul>
                                <li>
                                    <b><b>{{ __('backend/orders.show.form.name') }}</b></b> {{ $order->address->first_name ?? '' }} {{ $order->address->last_name ?? '' }}
                                </li>
                                <li><b>{{ __('backend/orders.show.form.street') }}</b> {{ $order->address->street ?? '' }}</li>
                                <li><b>{{ __('backend/orders.show.form.zip_code') }}</b> {{ $order->address->zip ?? '' }}</li>
                                <li><b>{{ __('backend/orders.show.form.place') }}</b> {{ $order->address->city ?? '' }}</li>
                                <li><b>{{ __('backend/orders.show.form.country') }}</b> {{ $order->address->country ?? '' }}</li>
                            </ul>
                        </li>

                        <li class="list-group-item">
                            <b>{{ __('backend/orders.show.form.sender') }}</b>
                            <ul>
                                <li>
                                    <b>{{ __('backend/orders.show.form.name') }}</b> {{ $order->address->sender_first_name }} {{ $order->address->sender_last_name }}
                                </li>
                                <li><b>{{ __('backend/orders.show.form.street') }}</b> {{ $order->address->sender_street }}</li>
                                <li><b>{{ __('backend/orders.show.form.zip_code') }}</b> {{ $order->address->sender_zip }}</li>
                                <li><b>{{ __('backend/orders.show.form.place') }}</b> {{ $order->address->sender_city }}</li>
                                <li><b>{{ __('backend/orders.show.form.country') }}</b> {{ $order->address->sender_country }}</li>





                            </ul>
                        </li>
                        @endif
                    </ul>

                </div>
            </div>
        </div>
        @if($isManual)
            <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{ __('backend/orders.show.additional_fields.title') }}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <hr/>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <form method="POST" class="form-inline"
                                      action="{{ route('backend-orders-add-label-code', ['id' => $order->id]) }}">
                                    @csrf
                                    <div class="form-group col-6 flex-column align-items-start">
                                        <label for="label_code" >{{ __('backend/orders.show.additional_fields.form.label_code') }}</label>
                                        <input type="text"
                                               class="form-control @if($errors->has('label_code')) is-invalid @endif"
                                               name="label_code"
                                               id="label_code"
                                               value="{{ old('label_code') ?? $order->label_code }}">

                                        @if($errors->has('label_code'))
                                            <span class="invalid-feedback" style="display:block" role="alert">
										    	<strong>{{ $errors->first('label_code') }}</strong>
											</span>
                                        @endif
                                    </div>

                                    <div class="col-6 mt-4">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="la la-save"></i>
                                        </button>
                                    </div>
                                </form>
                                <form method="POST" enctype="multipart/form-data" class="form-inline mt-2"
                                      action="{{ route('order-manual-add-tid', $order->id) }}"
                                >
                                    @csrf
{{--                                    <input type="hidden" name="product_id" value="{{ $order->product_id }}">--}}
{{--                                    <input type="hidden" name="loc" value="de">--}}

                                    <div class="form-group col-6">
                                        <label for="pdf" >{{ __('backend/tids.file_input') }}</label>
                                        <input type="file"
                                               class="form-control-file ml-1 @if($errors->has('file')) is-invalid @endif"
                                               name="file" id="pdf"
                                               accept="application/pdf">

                                        @if($errors->has('file'))
                                            <span class="invalid-feedback" style="display:block" role="alert">
																	<strong>{{ $errors->first('file') }}</strong>
																</span>
                                        @endif
                                    </div>

                                    <div class="col-6 mt-4">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="la la-upload"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{ __('backend/orders.notes') }}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        @foreach($notes as $note)

                            <div class="user-order-note">
                                {{ strlen($note->note) > 0 ? $note->note : '' }}
                                <span>{{ $note->getDateTime() }}</span>
                            </div>

                        @endforeach

                        <hr/>

                        <form method="POST" action="{{ route('backend-orders-add-note-form', ['id' => $order->id]) }}"
                              style="width: 100%;">
                            @csrf

                            <div class="form-group" style="width: 100%;">
                                <label for="order_note">{{ __('backend/orders.note_input') }}</label>
                                <textarea style="width: 100%;"
                                          class="form-control @if($errors->has('order_note')) is-invalid @endif"
                                          name="order_note" id="order_note"
                                          placeholder="">{{ old('order_note') }}</textarea>

                                @if($errors->has('order_note'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
																	<strong>{{ $errors->first('order_note') }}</strong>
																</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="submit" class="btn btn-wide btn-bold btn-danger"
                                       value="{{ __('backend/orders.add_note') }}"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')

@endsection
