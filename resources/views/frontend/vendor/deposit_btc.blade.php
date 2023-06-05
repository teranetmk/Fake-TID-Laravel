@extends('frontend.layouts.dashboard')

@section('content')
<div class="py-4 px-3 px-md-4">
    <div class="card mb-3 mb-md-4">

        <div class="card-body">
            <!-- Breadcrumb -->
            <nav class="d-none d-md-block" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('frontend/user.btc_cashin_title') }}</li>
                </ol>
            </nav>
            <!-- End Breadcrumb -->

            <div class="mb-3 mb-md-4 d-flex justify-content-between">
                <div class="h3 mb-0">{{ __('frontend/user.btc_cashin_title') }}</div>
            </div>


            <!-- Form -->
            <div>
               
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6">
                            <label for="email">Währung</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <img class="icon-img" src="/assets/img/btc.png" alt="Bitcoin Cash">
                                </div>
                                <input class="form-control btc-currency form-control-lg form-control-icon-img" value="Bitcoin" type="text">
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="email">Einzahlung in</label>
                            <input type="text" class="form-control form-control-lg" value="Mein Guthaben"  name="email">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-12">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" id="btc-cashin-wallet" onClick="this.select();" value="{{ $btcWallet }}"  readonly="">

                                <div class="input-group-append input-group-append-simple">
                                <a class="input-group-text bg-transparent text-muted" href="#" data-target="#qrModal">
                                    <img class="btc-cashin-img" src="https://chart.googleapis.com/chart?chs=180x180&chld=L|0&cht=qr&chl=bitcoin:{{ $btcWallet }}" width="30"/>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- <div id="btc-cashin" class="card-body">
                        <div class="input-group mb-3 euro-amount">
                            <input type="text" class="form-control euro_input" placeholder="Betrag eingeben" aria-label="Enter Amount" aria-describedby="euro-btn">
                            <div class="input-group-append">
                                <span class="input-group-text" id="euro-btn">Euro</span>
                            </div>
                        </div>
                        <div class="equal-sign">=</div>
                        <div class="input-group mb-3 btc-amount">
                            <input type="text" class="form-control btc_input" value="0" placeholder="Enter Amount" aria-label="Enter Amount" aria-describedby="euro-btn">
                            <div class="input-group-append">
                                <span class="input-group-text" id="euro-btn">BTC</span>
                            </div>
                        </div>
                    </div> -->
                </div>
                    <div id="btc-cashin" class="form-row">
                        <div class="form-group col-12 col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg euro_input" placeholder="Betrag eingeben" aria-label="Enter Amount" aria-describedby="euro-btn">

                                <div class="input-group-append input-group-append-infinity">
                                    <a class="btn btn-sm " href="javascript:;">EURO</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <div class="input-group btc-amount">
                                <input type="text" class="form-control form-control-lg btc_input" value="0" placeholder="Betrag eingeben" aria-label="Enter Amount" aria-describedby="euro-btn">

                                <div class="input-group-append input-group-append-infinity">
                                    <a class="btn btn-sm" id="euro-btn" href="javascript:;">BTC</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="bitcoin-wallet-btn">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>WICHTIG: Guthaben wird nach einer Bestätigung gutgeschrieben. Sollte deine Einzahlung nicht automatisch gutgeschrieben werden, klicke bitte auf der Seite <a href="/meine-einzahlungen/">"Einzahlungen"</a> <a href="https://prnt.sc/rm25kNdZeHcl">Hier</a>. </p>
                            <form method="POST" action="{{ route('deposit-btc-post', $userTransactionID) }}">
                                @csrf
                            <button type="submit" class="btn btn-danger btn-lg">{{ __('frontend/user.i_paid_button') }}</button>
                            </form>
                        </div>
                    </div>
                    
                </div>
              
            </div>
            <!-- End Form -->
        </div>
    </div>
</div>


<!-- The Modal -->
  <div class="modal fade" id="qrModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <!--<div class="modal-header">-->
          <!--<h4 class="modal-title">Modal Heading</h4>-->
          <button type="button" class="close" data-dismiss="modal">×</button>
        <!--</div>-->
        
        <!-- Modal body -->
        <div class="modal-body p-0">
          <img class="btc-cashin-img" src="https://chart.googleapis.com/chart?chs=180x180&chld=L|0&cht=qr&chl=bitcoin:{{ $btcWallet }}" width="100%"/>
        </div>
        
        <!-- Modal footer -->
        <!--<div class="modal-footer">-->
        <!--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
        <!--</div>-->
        
      </div>
    </div>
  </div>

@endsection
