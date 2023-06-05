@extends('frontend.layouts.app')

@section('content')
<div class="container mt-3 deposit-page">
    <div class="row justify-content-center"> 
        <div class="col-md-12">

            @component('components.frontend.box')

                @slot('title'){{ __('frontend/user.btc_cashin_title') }}@endslot
                <div class="card-body" id="bitcoin-wallet">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bitcoin">Währung</label>
                                <select class="form-control btc-currency">
                                    <option value="btc" data-thumbnail="/assets/img/btc.png" selected>Bitcoin</option>
                                </select>
                                <div class="car-select">
                                    <button class="car-btn-select" value=""></button>
                                    <div class="car-b">
                                    <ul id="car-a"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                             <div class="form-group">
                                <label for="bitcoin">Einzahlung in</label>
                                <select class="form-control">
                                    <option selected>Mein Guthaben</option>
                                    <!--<option>Euro</option>-->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                 
                <form method="POST" action="{{route('create-btc-invoice')}}">
                @csrf
                    <div id="btc-cashin" class="card-body">
                        <div class="input-group mb-3 euro-amount">
                            <input type="text" name="euro_amount" class="form-control euro_input" placeholder="Betrag eingeben" aria-label="Enter Amount" aria-describedby="euro-btn" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="euro-btn">Euro</span>
                            </div>
                        </div>
                        <div class="equal-sign">=</div>
                        <div class="input-group mb-3 btc-amount">
                            <input type="text" class="form-control btc_input" name="btc_amount" value="0" placeholder="Enter Amount" aria-label="Enter Amount" aria-describedby="euro-btn">
                            <div class="input-group-append">
                                <span class="input-group-text" id="euro-btn">BTC</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body" id="bitcoin-wallet-btn">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>WICHTIG: Bitte für jede Zahlung eine neue Rechnung erstellen und nur den genauen Betrag einzahlen! Zahlung wird nach einer Bestätigung gutgeschrieben. Falls nicht klickt auf "NOCHMAL PRÜFEN" auf eurer "Einzahlungen" Seite.</p>
                            
                                
                            <button type="submit" class="btn btn-red btn-lg">RECHNUNG ERSTELLEN</button>
                          
                        </div>
                    </div>
                </form>
                </div>
<hr>
                   
                    </div>

            @endcomponent
           
        </div>
    </div>
</div>




@endsection
