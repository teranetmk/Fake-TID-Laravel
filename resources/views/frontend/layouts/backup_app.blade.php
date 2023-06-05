<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name') }}</title>

        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css" />

        <link rel="icon" href="@if(strlen(App\Models\Setting::get('theme.favicon')) > 0){{ App\Models\Setting::get('theme.favicon') }}@else{{ asset('favicon.svg') }}@endif" sizes="any" />

        <link href="{{ asset_dir('vendor/bootstrap-4.6.0/css/bootstrap.min.css') }}" rel="stylesheet" />

        <link href="{{ asset_dir('css/app.css') }}?v={{mt_rand(10000, 99999)}}" rel="stylesheet" />
        <link href="{{ asset_dir('css/style.css') }}?v={{mt_rand(10000, 99999)}}" rel="stylesheet" />
        <link href="{{ asset_dir('css/mdb.min.css') }}?v={{mt_rand(10000, 99999)}}" rel="stylesheet" />

        @if(App\Models\Setting::get('theme.color.enable', 0))
        <link href="{{ route('custom-colors') }}" rel="stylesheet" />
        @endif

        @if(strlen(App\Models\Setting::get('theme.background')) > 0)
        <style type="text/css">
            body {
                background-image: url('{{ App\Models\Setting::get('theme.background') }}');
            }
        </style>
        @endif
        <script src="{{ asset_dir('vendor/jquery-3.3.1/js/jquery-3.3.1.min.js') }}" ></script>
        <link href="{{ route('custom-css') }}" rel="stylesheet" />
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

{{--        <script src="https://www.google.com/recaptcha/api.js?render={{ env('GOOGLE_CAPTCHA_PUBLIC_KEY') }}"></script>--}}
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
<style type="text/css">
            .form-control:focus, textarea:focus {
                border-color: #757575 !important;
                box-shadow: inset 0 0 0 1px #757575 !important;
            }

            .radio:before {
                content: "✓";
                color: #fff;
                font: normal normal normal 20px/1 FontAwesome;
            }
        </style>
    </head>
    <body>
        <div id="app">
                  <div class="">
                <div class="">
                    <div class="container">
                        <nav class="navbar navbar-expand-lg navbar-light nav-shop">
                            <div class="container">
                                <a class="" href="{{ url('/') }}">
                                    @if(strlen(App\Models\Setting::get('theme.logo')) > 0)
                                    <img src="{{ App\Models\Setting::get('theme.logo') }}" alt="logo" style="max-width: 140px;" />
                                    @else
                                    {{ config('app.name') }}
                                    @endif
                                </a>

                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('frontend/main.toggle_navigation') }}">
                                    <span class="navbar-toggler-icon"></span>
                                </button>

                                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                                    <ul class="navbar-nav">

                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/') }}">{{ __('frontend/main.home') }}</a>
                                        </li>

                                        @php
                                            $productCategories = \App\Models\ProductCategory::all()
                                        @endphp
                                        <li class="nav-item @if(count($productCategories) > 0) dropdown @endif">
                                            @if(count($productCategories) > 0 || App\Models\Setting::get('shop.creditcards.enabled'))
                                                <a id="navbarDropdownShop" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                    {{ __('frontend/main.shop') }}
                                                </a>
                                            @else
                                                <a class="nav-link" href="{{ route('shop') }}">
                                                    {{ __('frontend/main.shop') }}
                                                </a>
                                            @endif

                                            @if(count($productCategories) > 0 || App\Models\Setting::get('shop.creditcards.enabled'))
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <!-- <a class="dropdown-item" href="{{ route('shop') }}">
                                                    {{ __('frontend/shop.all_categories') }}
                                                </a> -->

                                                <div class="dropdown-divider"></div>

                                                @if(App\Models\Setting::get('shop.creditcards.enabled'))
                                                    <a class="dropdown-item" href="{{ route('creditcards') }}">
                                                        {{ __('frontend/shop.creditcards') }}
                                                    </a>

                                                    @if(count($productCategories) > 0)
                                                    <div class="dropdown-divider"></div>
                                                    @endif
                                                @endif

                                                @if(count($productCategories) > 0)
                                                    @foreach($productCategories as $productCategory)
                                                    <a class="dropdown-item" href="{{ route('product-category', [$productCategory->slug]) }}">
                                                        {{ $productCategory->name }}
                                                    </a>
                                                    @endforeach

                                                    {{-- @if(count(App\Models\Product::getUncategorizedProducts()))
                                                    <div class="dropdown-divider"></div>

                                                    <a class="dropdown-item" href="{{ route('product-category', ['uncategorized']) }}">
                                                        {{ __('frontend/shop.uncategorized') }}
                                                    </a>
                                                    @endif --}}
                                                @endif
                                            </div>
                                            @endif
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('faq') }}">{{ __('frontend/main.faq') }}</a>
                                        </li>

                                        @auth

                                        <li class="nav-item dropdown">
                                            <a id="navbarDropdownShop" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                {{ __('frontend/main.tickets') }}
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('tickets') }}">
                                                    {{ __('frontend/main.my_tickets') }}
                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <a class="dropdown-item" href="{{ route('ticket-create') }}">
                                                    {{ __('frontend/main.create_ticket') }}
                                                </a>
                                            </div>
                                        </li>
                                        @endauth
                                    </ul>

                                    <ul class="navbar-nav">
                                        @guest
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:void(0)" data-toggle="modal" data-target="#login-signup">
                                                    <svg class="user-icon">
                                                        <g xmlns="http://www.w3.org/2000/svg" id="login-Page-1" stroke="none" stroke-width="1" fill-rule="evenodd"> <g id="login-seitenrahmen_pk_1170" transform="translate(-1337.000000, -65.000000)"> <g id="login-Header-#2" transform="translate(215.000000, 8.000000)"> <g id="login-1.Ebene" transform="translate(634.000000, 56.000000)"> <path d="M497,2.5 C499.06775,2.5 500.75,4.18225 500.75,6.25075 C500.75,8.31775 499.06775,10.00075 497,10.00075 C494.93225,10.00075 493.25,8.31775 493.25,6.25075 C493.25,4.18225 494.93225,2.5 497,2.5 L497,2.5 Z M497,1 C494.1005,1 491.75,3.3505 491.75,6.25075 C491.75,9.1495 494.1005,11.50075 497,11.50075 C499.8995,11.50075 502.25,9.1495 502.25,6.25075 C502.25,3.3505 499.8995,1 497,1 L497,1 Z M501.77675,11.01475 C501.404,11.38825 500.984,11.713 500.53325,11.99125 C502.68725,13.39675 503.81675,15.8035 504.26225,17.50075 L489.722,17.50075 C490.151,15.78325 491.2655,13.3735 493.45175,11.98225 C492.9995,11.70175 492.58025,11.374 492.20825,10.99825 C489.01475,13.30225 488,17.37775 488,19 L506,19 C506,17.3935 504.92,13.33225 501.77675,11.01475 L501.77675,11.01475 Z" id="login-login"/> </g> </g> </g> </g>
                                                    </svg>
                                                    {{ __('frontend/main.login') }}
                                                </a>
                                            </li>

                                            @if ( false && Route::has('register') )
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('register') }}">{{ __('frontend/main.register') }}</a>
                                                </li>
                                            @endif
                                        @else

                                            
                                            <!-- <li class="nav-item">
                                                <a href="#" class="nav-link nav-link-btc">
                                                    <ion-icon name="logo-bitcoin"></ion-icon>
                                                    1 BTC = {{ App\Classes\BitcoinAPI::getFormatted(App\Classes\BitcoinAPI::convertBtc(1)) }}
                                                </a>
                                            </li> -->
                                         
                                            
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('deposit-btc') }}">
                                                    <i class="fa fa-eur"></i>
                                                    {{-- <ion-icon name="wallet"></ion-icon> --}}
                                                    <!-- {{ App\Classes\BitcoinAPI::getRate('EUR') }} -->
                                                    {{ Auth::user()->getFormattedBalance() }}
                                                </a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a id="navbarDropdownUser" class="nav-link dropdown-login dropdown-toggle btn btn-inline-block" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                    <svg class="user-icon">
                                                        <g xmlns="http://www.w3.org/2000/svg" id="login-Page-1" stroke="none" stroke-width="1" fill-rule="evenodd"> <g id="login-seitenrahmen_pk_1170" transform="translate(-1337.000000, -65.000000)"> <g id="login-Header-#2" transform="translate(215.000000, 8.000000)"> <g id="login-1.Ebene" transform="translate(634.000000, 56.000000)"> <path d="M497,2.5 C499.06775,2.5 500.75,4.18225 500.75,6.25075 C500.75,8.31775 499.06775,10.00075 497,10.00075 C494.93225,10.00075 493.25,8.31775 493.25,6.25075 C493.25,4.18225 494.93225,2.5 497,2.5 L497,2.5 Z M497,1 C494.1005,1 491.75,3.3505 491.75,6.25075 C491.75,9.1495 494.1005,11.50075 497,11.50075 C499.8995,11.50075 502.25,9.1495 502.25,6.25075 C502.25,3.3505 499.8995,1 497,1 L497,1 Z M501.77675,11.01475 C501.404,11.38825 500.984,11.713 500.53325,11.99125 C502.68725,13.39675 503.81675,15.8035 504.26225,17.50075 L489.722,17.50075 C490.151,15.78325 491.2655,13.3735 493.45175,11.98225 C492.9995,11.70175 492.58025,11.374 492.20825,10.99825 C489.01475,13.30225 488,17.37775 488,19 L506,19 C506,17.3935 504.92,13.33225 501.77675,11.01475 L501.77675,11.01475 Z" id="login-login"/> </g> </g> </g> </g>
                                                    </svg>
                                                    <span class="login-link--text">
                                                    {{ Auth::user()->username }}
                                                    </span>
                                                    <!-- <span class="caret"></span> -->
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                                                    {{-- <a class="dropdown-item" href="{{ route('home') }}"> {{ __('frontend/user.profile') }} </a> --}}
                                                    <a class="dropdown-item" href="{{ route('orders') }}">
                                                        {{ __('frontend/user.orders') }}
                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    <a class="dropdown-item" href="{{ route('deposit-btc') }}">
                                                        {{ __('frontend/user.deposit') }}
                                                    </a>

                                                    <a class="dropdown-item" href="{{ route('transactions') }}">
                                                        {{ __('frontend/user.transactions') }}
                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    <a class="dropdown-item" href="{{ route('settings') }}">
                                                        {{ __('frontend/user.settings') }}
                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    <a class="dropdown-item" href="{{ route('deposit-btc-redeem') }}">
                                                        {{ __('frontend/user.deposit_btc_redeem') }}
                                                    </a>

                                                    @if(Auth::user()->hasAnyPermissionFromArray(['access_backend', 'manage_orders', 'manage_orders_packstation', 'manage_orders_filialeinlieferung', 'manage_orders_lit_filling', 'manage_orders_lit_refund', 'manage_orders_nachnahme', 'manage_orders_random', 'manage_orders_accounts']))
						                            <div class="dropdown-divider"></div>

                                                    <a class="dropdown-item" href="{{ route('backend-dashboard') }}" target="_panel">
                                                        {{ __('frontend/user.admin_panel') }}
                                                        <ion-icon name="open"></ion-icon>
                                                    </a>

                                                    <div class="dropdown-divider"></div>
                                                    @endif

                                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        {{ __('frontend/main.logout') }}
                                                    </a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </div>
                                            </li>
                                        @endguest

                                        <li class="nav-item @if(count(App\Models\Setting::getAvailableLocales())) dropdown language-dropdown @else language-dropdown-hide-arrow @endif">
                                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle dropdown-lang btn btn-language btn-inline-block" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                <span style="color: #d40511 !important">{{ app()->getLocale() }}</span>
                                                <svg>
                                                    <polygon xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" points="8 9.241 15 5 15 6.759 8 11 1 6.759 1 5 8 9.241"/>
                                                </svg>
                                            </a>
                                            @if(count(App\Models\Setting::getAvailableLocales()))
                                            <div class="dropdown-menu dropdown-lang dropdown-menu-right" aria-labelledby="navbarDropdownLang" style="width: 70px;min-width: 70px;">
                                                @foreach(App\Models\Setting::getAvailableLocales() as $locale)
                                                @php 
                                                    $route = "language.".$locale;
                                                @endphp
                                                <a class="dropdown-item" href="{{ route($route) }}"><img class="germany-flag" src="{{ asset_dir('img/germany.jpg') }}" alt="Germany Flag" width="20"><img class="usa-flag" src="{{ asset_dir('img/usa.jpg') }}" alt="USA Flag" width="20">
                                                    {{$locale}}
                                                    <!-- <img class="flag-icon-img" src="{{ asset_dir('svg/flags/' . \Lang::get('locale.icon', [], $locale) . '.svg') }}" />
                                                    <span class="flag-icon-name">{{ \Lang::get('locale.name', [], $locale) }}</span> -->
                                                </a>
                                                @endforeach
                                                <a class="dropdown-item tor-menu" href="http://fpolrhfke2pgovjbrra4gvprbspuawuxzcssmhy4rlsyj5pbx44panad.onion"><img class="tor-icon" src="{{ asset_dir('svg/Tor_Browser_icon.svg') }}" alt="Tor Icon" width="15">
                                                    tor</a>
                                            </div>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>

            <main class="">
                @if( $errors->any() )

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('frontend/main.close') }}">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                    @foreach( $errors->all() as $error )

                                        {{ $error }} <br/>

                                    @endforeach

                                    @if($errors->has('recaptcha_token'))
                                        {{$errors->first('recaptcha_token')}}
                                    @endif

                                    {!! \Session::get('errorMessage') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif (\Session::has('errorMessage'))
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('frontend/main.close') }}">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                    {!! \Session::get('errorMessage') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (\Session::has('successMessage'))
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('frontend/main.close') }}">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                    {!! \Session::get('successMessage') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer id="footer">

                <span style="font-size:11px;">&copy; {{ date('Y') }} {{ ucwords(parse_url(env('APP_URL', 'https://Fake-tids.su'), PHP_URL_HOST)) }}. All rights reserved.</span>
                <!--<span style="font-size:11px;">&copy; 2019 {{ App\Models\Setting::get('app.name') }}. All rights reserved.</span>-->
            </footer>
        </div>

        <!-- Scripts -->
{{--        <script src="{{ asset('vendor/jquery-3.3.1/js/jquery-3.3.1.min.js') }}" ></script>--}}
        <script src="{{ asset_dir('vendor/bootstrap-4.6.0/js/bootstrap.min.js') }}" ></script>

        <script src="https://unpkg.com/ionicons@4.2.2/dist/ionicons.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>

        @include('frontend/layouts/app_modal')
        
        <script>
            $(document).ready(function() {
              $("#navbarDropdownLang span").html($("#navbarDropdownLang span").html().replace('de', 'de/tor'));
            });
        </script>

        <script type="text/javascript">
            (function ($) {

                $(document).ready(function () {

                    var calculate = function () {
                        var sum = 0;

                        $('select.calculate').each(function (index) {
                            sum += parseInt($(this).val());
                        });

                        $('.custom-calculator--total').text('ab ' + sum + ' EUR');
                    };

                    $('select.calculate').on('change', function () {
                        calculate();
                    });

                    calculate();
                });

            })(jQuery);
        </script>

        <script>
            //grecaptcha.ready(function() {
              //  grecaptcha.execute('{{ env('GOOGLE_CAPTCHA_PUBLIC_KEY') }}')    .then(function(token) {

                //    $('.recaptcha_token').val(token);
                    // document.getElementById("recaptcha_token").value = token;
                //}); });1
        </script>


        <script type="text/javascript">
            @if(isset($clipboardJS))
            var clipboard = new ClipboardJS('{{ $clipboardJS->element }}');

            clipboard.on('success', function(e) {
                $('{{ $clipboardJS->fadeIn }}').css('display', 'block').hide().fadeIn();
            });
            @endif
        </script>
        <script>
                        // api url
            const api_url = 
                "https://blockchain.info/ticker";
            
            // Defining async function
            async function getapi(url) {
                
                // Storing response
                const response = await fetch(url);
                
                // Storing data in form of JSON
                var data = await response.json();
                var euro = (1 / data['EUR']['sell']);
                console.log(euro);

                $('.euro_input').keyup(function () {
                    var euroamount = $('.euro_input').val();
                    
                    var btcamount = (euroamount.replace(/,/g, '.') * euro);
                    $(".btc_input").val(btcamount.toFixed(7));
                });
                
            //    show(data);
            }
            // Calling that async function
            getapi(api_url);

        </script>


            <script>

                var carArray = [];
                $('.btc-currency option').each(function(){
                var img = $(this).attr("data-thumbnail");
                var text = this.innerText;
                var value = $(this).val();
                var item = '<li><img src="'+ img +'" alt="" value="'+value+'"/><span>'+ text +'</span></li>';
                carArray.push(item);
                })

                $('#car-a').html(carArray);

                //Set the button value to the first el of the array
                $('.car-btn-select').html(carArray[0]);
                $('.car-btn-select').attr('value', 'btc');

                //change button stuff on click
                $('#car-a li').click(function(){
                var img = $(this).find('img').attr("src");
                var value = $(this).find('img').attr('value');
                var text = this.innerText;
                var item = '<li><img src="'+ img +'" alt="" /><span>'+ text +'</span></li>';
                $('.car-btn-select').html(item);
                $('.car-btn-select').attr('value', value);
                $(".car-b").toggle();
                //console.log(value);
                });

            </script>
    </body>
</html>
