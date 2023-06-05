@extends('frontend.layouts.app')

@section('content')
<div class="container mt-3 deposit-page">
    <div class="row justify-content-center"> 
        <div class="col-md-12">

            @component('components.frontend.box')

                @slot('title'){{ __('frontend/user.btc_cashin_title') }}@endslot
                    <div id="btc-cashin" class="card-body">
                            <div class="btc-calculator">
                                <div class="btc-car">
                                    <h1 id="from-basis"></h1>
                                    <ul id="from-menu">
                                        <li class="from-currency">BTC</li>
                                    </ul>
                                </div>

                                <div class="line"><h1>=</h1></div>

                                <div class="car-car">
                                    <h1 id="to-currency-price"></h1>
                                    <ul id="to-menu">
                                    <li class="to-currency">AUD</li>
                                    <li class="to-currency">BRL</li>
                                    <li class="to-currency">CAD</li>
                                    <li class="to-currency">CHF</li>
                                    <li class="to-currency">CLP</li>
                                    <li class="to-currency">CNY</li>
                                    <li class="to-currency">CZK</li>
                                    <li class="to-currency">DKK</li>
                                    <li class="to-currency">EUR</li>
                                    <li class="to-currency">GBP</li>
                                    <li class="to-currency">HKD</li>
                                    <li class="to-currency">INR</li>
                                    <li class="to-currency">ISK</li>
                                    <li class="to-currency">JPY</li>
                                    <li class="to-currency">KRW</li>
                                    <li class="to-currency">NZD</li>
                                    <li class="to-currency">PLN</li>
                                    <li class="to-currency">RUB</li>
                                    <li class="to-currency">SEK</li>
                                    <li class="to-currency">SGD</li>
                                    <li class="to-currency">THB</li>
                                    <li class="to-currency">TWD</li>
                                    <li class="to-currency">USD</li>
                                    </ul>
                                </div>
                            </div>

                    <div class="row">
                        <div class="col-sm-4 text-center btc-cashin-bg">
                            <img class="btc-cashin-img" src="https://chart.googleapis.com/chart?chs=180x180&chld=L|0&cht=qr&chl=bitcoin:{{ $btcWallet }}" />
                        </div>
                        <div class="col-sm-8">
                            
                            <div class="input-group mb-3 btcwall">
                                <input type="text" class="form-control" id="btc-cashin-wallet" onClick="this.select();" value="{{ $btcWallet }}" readonly placeholder="BTC Wallet" aria-label="BTC Wallet" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><a href="javascript:void(0);" class="btc-cashin-copy-btn" data-clipboard-target="#btc-cashin-wallet">
                                <!-- {{ __('frontend/user.copy') }} -->
                                <ion-icon name="copy"></ion-icon>
                            </a>
                            <span class="btc-cashin-divider">|</span>
                            <a href="bitcoin:{{ $btcWallet }}">
                                <!-- {{ __('frontend/user.open_in_wallet') }} -->
                                <ion-icon name="open"></ion-icon>
                            </a></span>
                                </div>
                            </div>
                            <!-- <input id="btc-cashin-wallet" type="text" onClick="this.select();" class="br-outline-input btc-cashin-input" value="{{ $btcWallet }}" readonly /> -->

                            

                            <span class="btc-cashin-copy-info">{{ __('frontend/user.wallet_copied') }}</span>
                            <div class="btc-value row">
                                <div class="col-sm-1">
                                    <img class="img-mdeposit" src="assets/img/alert.png" width="40">
                                </div>
                                <div class="col-sm-11">
                                    <h4 class="minimum-deposit-title">{{ __('frontend/user.minimum_deposit') }}:</h4>
                                    <p class="minimum-deposit-desc">1 mBTC = 1,000 μBTC</h4>
                                </div>
                            </div>

                            <hr />

                            <form method="POST" action="{{ route('deposit-btc-post', $userTransactionID) }}">
                                @csrf

                                <button type="submit" class="btn btn-red ml-2">{{ __('frontend/user.i_paid_button') }}</button>
                            </form>
                        </div>
                    </div>
                    </div>

            @endcomponent
            
        </div>
    </div>
</div>

<script>
    var outside = document.getElementById("body-container");
var toPrice = document.getElementById("to-currency-price");
var fromBasis = document.getElementById("from-basis");
var toMenu = document.getElementById("to-menu");
var fromMenu = document.getElementById("from-menu");
var toSelect = document.getElementsByClassName("to-currency");
var fromSelect = document.getElementsByClassName("from-currency");

var toCurrency = "USD";
var fromCurrency = "BTC";

var retrievePrice = function() {
    var XHR = new XMLHttpRequest();
    
    XHR.onreadystatechange = function(){
      if(XHR.readyState == 4 && XHR.status == 200) {
        var val = JSON.parse(XHR.responseText)[fromCurrency][toCurrency];
        var price = val.toLocaleString('en');
        toPrice.textContent = price + " " + toCurrency;
        fromBasis.textContent = fromCurrency;
      }
    }
    
    XHR.open("GET","https://min-api.cryptocompare.com/data/pricemulti?fsyms=" + fromCurrency + "&tsyms=" + toCurrency);
    XHR.send();
}

for(var i = 0; i < toSelect.length; i++) {
    toSelect[i].addEventListener("click", function() {
        toMenu.classList.remove("expand");
        toCurrency = this.textContent;
        retrievePrice();
    });
}

for(var i = 0; i < fromSelect.length; i++) {
    fromSelect[i].addEventListener("click", function() {
        fromMenu.classList.remove("expand");
        fromCurrency = this.textContent;
        retrievePrice();
    });
}

toPrice.addEventListener("click", function() {
    if(toMenu.classList.contains("expand")) {
        toMenu.classList.remove("expand");
    } else {
        toMenu.classList.add("expand");
    }
});

fromBasis.addEventListener("click", function() {
    if(fromMenu.classList.contains("expand")) {
        fromMenu.classList.remove("expand");
    } else {
        fromMenu.classList.add("expand");
    }
});

//Execute
setInterval(function() {
    retrievePrice();
}, 10000);

retrievePrice();
</script>
@endsection
