
<?php
$percentage = '10% Gebühr';
if ($product->category->slug=='lalo-nachnahme'){
    $percentage = '7% Gebühr';
}
elseif($product->category->slug=='morty-nachnahme' || $product->category->slug=='welt-nachnahme'){
    $percentage = '30€ Gebühr';
}
?>
<div class="col-md-4">
    <div class="card mb-15 shadow-none" id="card-shop">
        <div class="card-header shop-card-header">
            <span class="f-17 text-bold">{{ ($product->name === 'Nachnahme Boxing') ? $percentage : $product->getFormattedPrice() }}</span>
            @if($product->isSale() && $product->isDigitalGoods())
                <div class="product-tag product-tag-sale" @if($product->isDigitalGoods()) style="position: unset !important;border-radius: 15px !important;color: #d40511 !important;background: #fc0 !important;background: -moz-linear-gradient(45deg, #fc0 0%, #fc0 100%) !important;background: -webkit-linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;background: linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fc0', endColorstr='#fc0',GradientType=1 );"@endif>
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
            <div class="text-center">
                <button class="btn btn-default btn-circle btn-exclaimation"
                        onclick="showdescription({{ $product->id }})">
                    @if(is_null($product->getIcon()))
                    <ion-icon ios="ios-information-circle-outline" md="ios-information-circle-outline"
                              role="img" class="hydrated"
                              aria-label="information circle outline"></ion-icon>
                    @else
                    <img src="{{ asset('icons/' . $product->getIcon()) }}"
                        style="position: relative;width: 90%;height: auto;"/>
                    @endif
                </button>
            </div>
        </div>

        <div class="card-body bg-light-gray shadow-none">

            <div class="row mt-2 mb-1">
                <div class="col-md-12 text-center">
                    <span class="f-17 text-bold">{{ $product->name }}</span><br>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <ul class="product-ul-list">
                        @foreach($product->benifits as $benifit)
                        <li><i class="fa fa-check text-success"></i>&nbsp; {{ $benifit->getLabel() }}</li>
                        @endforeach

                        @if($product->isShowStockEnabled())
                        <li><i class="fa fa-database text-success"></i>&nbsp; {{ __('frontend/shop.amount_with_char', ['amount_with_char' => $product->getStock()]) }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">


                    <form class="mt-15"
                          action="{{ route('buy-product-form') }}"
                          method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <input type="hidden" value="{{ $product->id }}" name="product_id"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <div class="row">
                                    <div class=" col-8 col-lg-8 only-p-left mt-15-991">

                                        @if(($product->isDigitalGoods() ||  $product->asWeight()) && ($product->name !== 'Nachnahme Boxing'))
                                        <input type="{{ $product->isDigitalGoods() ? 'number' : 'text' }}" name="product_amount"
                                                class="br-outline-input form-control form-control-round input-sm"
                                                placeholder="{{ $product->isDigitalGoods() ? ($product->inStock() ? __('frontend/shop.product_amount') : __('frontend/shop.sold_out') ) : __('frontend/shop.weight_placeholder') }}"
                                                @if($product->isSoldOut() || ! $product->isAvailable())
                                                    value="{{ __('frontend/shop.sold_out') }}"
                                                    disabled
                                                @endif />

                                        @else

						@if(Auth::user() != null && Auth::user()->email == "admin@example.com")
							<select name="product_amount"
                                                    class="br-outline-input form-control form-control-round">

                                                    <option value="1">{{ __('frontend/shop.amount', ['amount' => 1]) }}</option>

                                            </select>


						@else

                                            <select name="product_amount"
                                                    class="br-outline-input form-control form-control-round"

                                                @if($product->isSoldOut() || (! $product->isAvailable() && ($product->name !== 'Nachnahme Boxing') && !in_array($product->id,[53,54,55]))  ) disabled @endif>

                                                @if($product->isSoldOut() || (! $product->isAvailable() && ($product->name !== 'Nachnahme Boxing') && !in_array($product->id,[53,54,55]))  )
                                                    <option value="" selected>{{ __('frontend/shop.sold_out') }} </option>
                                                @endif

                                                @if($product->isUnlimited() || ($product->name === 'Nachnahme Boxing'))
                                                    <option value="1">{{ __('frontend/shop.amount', ['amount' => 1]) }}</option>
                                                @elseif($product->getStock() > 10 )
                                                    @for($i = 1; $i < 11; $i++)
                                                        <option value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                    @endfor
                                                @elseif($product->getStock() > 0 )
                                                    @for($i = 1; $i < ($product->getStock() + 1 ); $i++)
                                                        <option value="{{ $i }}">{{ __('frontend/shop.amount', ['amount' => $i]) }}</option>
                                                    @endfor
                                                @else
                                                    <option value="0">{{ $product->isUnlimited()}} </option>
                                                @endif

                                            </select>

						@endif
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


                                    <div class="col-4 col-lg-4 only-p-right">

					@if(Auth::user() != null && Auth::user()->email == "admin@example.com")
					<button type="submit"
                                                class="btn btn-icon btn-block btn-red">
                                            <ion-icon name="cart"></ion-icon>
                                        </button>

					@else
                        @if(Auth::user() != null)
                                        <button type="submit"
                                                class="btn btn-icon btn-block btn-red @if($product->isSoldOut() || (! $product->isAvailable() && $product->name !== 'Nachnahme Boxing' && !in_array($product->id,[53,54,55]))) disabled @endif"
                                                @if($product->isSoldOut() || (! $product->isAvailable() && $product->name !== 'Nachnahme Boxing' && !in_array($product->id, [53,54,55]))) ) disabled="true" @endif>
                                            <ion-icon name="cart"></ion-icon>
                                        </button>
                        @else
                                        <a class="btn btn-icon btn-block btn-red @if($product->isSoldOut() || (! $product->isAvailable() && $product->name !== 'Nachnahme Boxing' && !in_array($product->id,[53,54,55]))) disabled @endif" href="javascript:void(0)" data-toggle="modal" data-target="#login-signup">
                                            <ion-icon name="cart"></ion-icon>
                                        </a>
                        @endif
					@endif
                                    </div>

                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


