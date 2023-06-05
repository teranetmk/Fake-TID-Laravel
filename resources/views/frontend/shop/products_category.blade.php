@extends('frontend.layouts.app')

@section('content')
    <div class="container">

        @if( count( $products ) )
            {{--
            <h3 class="page-title mt-4">{{ $productCategory->name }}</h3>
            --}}
            <div class="row mt-4">
                @foreach( $products as $product )
                    @include('frontend.shop.product_box')

                    {{-- OLD --}}
                    @if( false )
                        <div class="col-md-4">
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

                            <div class="card-header">{{ $product->name }}</div>

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

                            <ul class="list-group list-group-flush text-right">
                                <li class="list-group-item">
                                    <a href="{{ route('product-page', $product->id) }}"
                                       class="btn btn-outline-secondary">{{ __('frontend/shop.details_button') }}</a>

                                    <form method="POST" class="mt-15" action="{{ route('buy-product-form') }}">
                                        @csrf

                                        <input type="hidden" value="{{ $product->id }}" name="product_id"/>
                                        <div class="row">
                                            <div class="col-xs-6 col-lg-6 only-p-right">
                                                <div class="br-outline-input form-control form-control-round text-left price-control">
                                                    {{ $product->getFormattedPrice() }}
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-lg-6 only-p-left mt-15-991">
                                                @if($product->isDigitalGoods() ||  $product->asWeight())
                                                    <input type="{{ $product->isDigitalGoods() ? 'number' : 'text' }}" name="product_amount"
                                                           class="br-outline-input form-control form-control-round"
                                                           placeholder="{{ $product->isDigitalGoods() ? __('frontend/shop.product_amount') : __('frontend/shop.weight_placeholder') }}"
                                                           @if(!$product->isAvailable()) value="{{ __('frontend/shop.sold_out') }}"
                                                           disabled @endif />
                                                @else
                                                    <select name="product_amount"
                                                            class="br-outline-input form-control form-control-round"
                                                            @if(!$product->isAvailable()) disabled @endif>

                                                        @if(!$product->isAvailable())
                                                            <option value=""
                                                                    selected>{{ __('frontend/shop.sold_out') }}</option>
                                                        @endif

                                                        @if($product->isUnlimited())
                                                            <option
                                                                value="1">{{ __('frontend/shop.amount', ['amount' => 1]) }}</option>
                                                        @elseif($product->getStock() > 10)
                                                            @for($i = 1; $i < 11; $i++)
                                                                <option
                                                                    value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                            @endfor
                                                        @elseif($product->getStock() > 0)
                                                            @for($i = 1; $i < ($product->getStock()+1); $i++)
                                                                <option
                                                                    value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                            @endfor
                                                        @else($product->getStock() > 0)
                                                            <option
                                                                value="0">{{ __('frontend/shop.sold_out') }}</option>
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
                                        <div class="row mt-15">
                                            <div class="col-xs-12 col-lg-12">
                                                <button type="submit"
                                                        class="btn btn-icon btn-block btn-primary @if(!$product->isAvailable()) disabled @endif"
                                                        @if(!$product->isAvailable()) disabled="true" @endif>
                                                    <ion-icon name="cart"></ion-icon>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif
                    {{-- OLD:END --}}
                @endforeach
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-warning">
                        {{ __('frontend/shop.no_products_category_exists') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('frontend.shop.product_box_modal')

@endsection
