@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{--
            <h3 class="page-title mt-3">{{ __('frontend/shop.product_details') }}</h3>
            --}}

            <div class="card mb-15">
                @if($product->isSale() && ! $product->isDigitalGoods())
                    <div class="product-tag product-tag-sale" @if($product->isDigitalGoods()) style="border-radius: 15px !important;color: #d40511 !important;background: #fc0 !important;background: -moz-linear-gradient(45deg, #fc0 0%, #fc0 100%) !important;background: -webkit-linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;background: linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fc0', endColorstr='#fc0',GradientType=1 );"@endif>
                        @if(! $product->isDigitalGoods())
                        <span class="product-tag-percent">
                            {{ __('frontend/shop.sale', ['percent' => $product->getSalePercent()]) }}
                        </span>
                        @endif
                        {{ __('frontend/shop.tags.sale') }}
                        <span class="product-tag-old-price">
                            <s>{{ $product->getFormattedOldPrice() }}</s>
                        </span>
                    </div>
                @endif

                <div class="card-header p-3">{{ $product->name }}</div>

                @if(strlen($product->short_description) > 0)
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        {!! nl2br($product->short_description) !!}
                    </li>
                </ul>
                @endif

                @if(strlen($product->description) > 0)
                <div class="card-body">
                    {!! nl2br($product->description) !!}
                </div>
                @endif

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <b>{{ __('frontend/shop.category') }}</b>
                        <a href="{{ route('product-category', [$product->getCategory()->slug]) }}">
                            {{ $product->getCategory()->name }}
                        </a>
                    </li>
                </ul>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="container no-padding">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('product-category', [$product->getCategory()->slug]) }}" class="btn btn-default d-none d-lg-inline-block">{{ __('frontend/shop.to_shop') }}</a>
                                </div>

                                <div class="col-md-6 text-right">
                                    <form method="POST" class="mt-15" action="{{ route('buy-product-form') }}">
                                        @csrf

                                        <input type="hidden" value="{{ $product->id }}" name="product_id" />
                                        <div class="row">
                                            <div class="col-xs-6 col-lg-6 only-p-right">
                                                <div class="br-outline-input form-control form-control-round text-left price-control">
                                                    {{ $product->getFormattedPrice() }}
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-lg-6 only-p-left mt-15-991">
                                                @if($product->isDigitalGoods() || $product->asWeight())
                                                <input type="{{ $product->isDigitalGoods() ? 'number' : 'text' }}" name="product_amount" class="br-outline-input form-control form-control-round" placeholder="{{ $product->isDigitalGoods() ? __('frontend/shop.product_amount') : __('frontend/shop.weight_placeholder') }}" @if(!$product->isAvailable()) value="{{ __('frontend/shop.sold_out') }}" disabled @endif />
                                                @else
                                                <select name="product_amount" class="br-outline-input form-control form-control-round" @if(!$product->isAvailable()) disabled @endif>

                                                    @if(!$product->isAvailable())
                                                        <option value="" selected>{{ __('frontend/shop.sold_out') }}</option>
                                                    @endif

                                                    @if($product->isUnlimited())
                                                        <option value="1">{{ __('frontend/shop.amount', ['amount' => 1]) }}</option>
                                                    @elseif($product->getStock() > 10)
                                                        @for($i = 1; $i < 11; $i++)
                                                        <option value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                        @endfor
                                                    @elseif($product->getStock() > 0)
                                                        @for($i = 1; $i < ($product->getStock()+1); $i++)
                                                        <option value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                        @endfor
                                                    @else($product->getStock() > 0)
                                                        <option value="0">{{ __('frontend/shop.sold_out') }}</option>
                                                    @endif
                                                </select>
                                                @endif
                                                @if($product->asWeight())
                                                <div class="col-xs-12 col-lg-12 mt-15">
                                                    <span>
                                                        {{ __('frontend/shop.amount_with_char', [
                                                            'amount_with_char' => $product->getWeightAvailable() . $product->getWeightChar()
                                                        ]) }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mt-3 mb-3">
                                            <div class="col-xs-12 col-lg-12">
                                                <button type="submit" class="btn btn-icon btn-block btn-primary @if(!$product->isAvailable()) disabled @endif" @if(!$product->isAvailable()) disabled="true" @endif><ion-icon name="cart"></ion-icon></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <a href="{{ route('product-category', [$product->getCategory()->slug]) }}" class="btn btn-outline-secondary d-lg-none d-md-inline-block">{{ __('frontend/shop.to_shop') }}</a>
        </div>
    </div>
</div>
@endsection
