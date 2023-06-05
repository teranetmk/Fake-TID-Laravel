<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Title -->
    <title>{{ config('app.name') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="@if(strlen(App\Models\Setting::get('theme.favicon')) > 0){{ App\Models\Setting::get('theme.favicon') }}@else{{ asset('favicon.svg') }}@endif" >


    <!-- Template -->
    <link rel="stylesheet" href="{{ asset('graindashboard/css/graindashboard.css') }}?v={{mt_rand(10000, 99999)}}">
    <link href="{{ asset_dir('admin/assets/vendors/general/summernote/dist/summernote.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('graindashboard/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset_dir('admin/assets/vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    	<link href="{{ asset_dir('admin/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="has-sidebar has-fixed-sidebar-and-header">
<!-- Header -->
<header class="header bg-body">
    <nav class="navbar flex-nowrap p-0">
        <div class="navbar-brand-wrapper d-flex align-items-center col-auto">
            <!-- Logo For Mobile View -->
            <a class="navbar-brand navbar-brand-mobile" href="{{ url('users-backend-dashboard') }}">
            @if(strlen(App\Models\Setting::get('theme.logo')) > 0)
                <img class="img-fluid w-100" src="{{ App\Models\Setting::get('theme.logo') }}" alt="{{ config('app.name') }}">
                @else
            {{ config('app.name') }}
            @endif
            </a>
            <!-- End Logo For Mobile View -->

            <!-- Logo For Desktop View -->
            <a class="navbar-brand navbar-brand-desktop" href="{{ url('users-backend-dashboard') }}">
                <img class="side-nav-show-on-closed" src="{{ App\Models\Setting::get('theme.logo') }}" alt="{{ config('app.name') }}" style="width: auto; height: 25px;">
                <img class="side-nav-hide-on-closed" src="{{ App\Models\Setting::get('theme.logo') }}" alt="{{ config('app.name') }}" style="width: auto; height: 60px;">
            </a>
            <!-- End Logo For Desktop View -->
        </div>

        <div class="header-content col px-md-3">
            <div class="d-flex align-items-center">
                <!-- Side Nav Toggle -->
                <a  class="js-side-nav header-invoker d-flex mr-md-2" href="#"
                    data-close-invoker="#sidebarClose"
                    data-target="#sidebar"
                    data-target-wrapper="body">
                    <i class="gd-align-left"></i>
                </a>
                <!-- End Side Nav Toggle -->

                <!-- User Notifications -->
                <div class="dropdown ml-auto">
                

                    
                </div>
                <!-- End User Notifications -->
                <!-- User Avatar -->
                <div class="dropdown mx-3 dropdown ml-2">
                    <a id="profileMenuInvoker" class="header-complex-invoker" href="#" aria-controls="profileMenu" aria-haspopup="true" aria-expanded="false" data-unfold-event="click" data-unfold-target="#profileMenu" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-animation-in="fadeIn" data-unfold-animation-out="fadeOut">
                        <!--img class="avatar rounded-circle mr-md-2" src="#" alt="John Doe"-->
                        <span class="mr-md-2 avatar-placeholder">{{ucwords(mb_substr(Auth::user()->username, 0, 1))}}</span>
                        <span class="d-none d-md-block">{{Auth::user()->username}}</span>
                        <i class="gd-angle-down d-none d-md-block ml-2"></i>
                    </a>

                    <ul id="profileMenu" class="unfold unfold-user unfold-light unfold-top unfold-centered position-absolute pt-2 pb-1 mt-4 unfold-css-animation unfold-hidden fadeOut" aria-labelledby="profileMenuInvoker" style="animation-duration: 300ms;">
                        <li class="unfold-item">
                            <a class="unfold-link d-flex align-items-center text-nowrap" href="{{ route('settings') }}">
                    <span class="unfold-item-icon mr-3">
                      <i class="gd-user"></i>
                    </span>
                    {{ __('frontend/user.settings') }}
                            </a>
                        </li>
                        <li class="unfold-item unfold-item-has-divider">
                            <a class="unfold-link d-flex align-items-center text-nowrap" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="unfold-item-icon mr-3">
                      <i class="gd-power-off"></i>
                    </span>
                    {{ __('frontend/main.logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                        </li>
                    </ul>
                </div>
                <!-- End User Avatar -->
            </div>
        </div>
    </nav>
</header>
<!-- End Header -->

<main class="main">
    <!-- Sidebar Nav -->
    <aside id="sidebar" class="js-custom-scroll side-nav">
        <ul id="sideNav" class="side-nav-menu side-nav-menu-top-level mb-0">
            <!-- Title -->
            <li class="sidebar-heading h6">Dashboard</li>
            <!-- End Title -->

            <!-- Dashboard -->
            <li class="side-nav-menu-item {{ request()->is('user/dashboard*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{route('users-backend-dashboard')}}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-dashboard"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">Dashboard</span>
                </a>
            </li>

            <li class="side-nav-menu-item">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('shop') }}" target="_blank">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-shopping-cart-full"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/header.go_to_shop') }}</span>
                </a>
            </li>
            <!-- End Dashboard -->

            <!-- End Documentation -->

            <!-- Title -->
            <li class="sidebar-heading h6">Activities</li>
            <!-- End Title -->
            @if(Auth::user()->is_partner)
            <li class="side-nav-menu-item {{ request()->is('partner/products*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('partner-management-products') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-package"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/management.products.title') }}</span>
                </a>
            </li>
           
       
            <li class="side-nav-menu-item {{ request()->is('partner/tickets*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{route('partner-tickets')}}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-layout-column-3"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/management.tickets.title') }}</span>
                </a>
            </li>

            <li class="side-nav-menu-item {{ request()->is('partner/profits*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('partner-profits') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-panel"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/profits.title') }}</span>
                </a>
            </li>
            <!-- <li class="side-nav-menu-item {{ request()->is('admin/commission*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('employee-commission') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-write"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">Mitarbeiterprovision</span>
                </a>
            </li> -->

            <li class="side-nav-menu-item {{ request()->is('partner/orders*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('partner-orders') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-dropbox"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/orders.title') }}</span>
                </a>
            </li>
            <div class="dropdown-divider"></div>
            @endif


            @if(Auth::user()->hasPermission('vendor'))
            <li class="side-nav-menu-item {{ request()->is('admin/management/products*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('backend-management-products') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-package"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/management.products.title') }}</span>
                </a>
            </li>
           
       
            <li class="side-nav-menu-item {{ request()->is('admin/management/tickets*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('backend-management-tickets') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-layout-column-3"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/management.tickets.title') }}</span>
                </a>
            </li>

            <li class="side-nav-menu-item {{ request()->is('admin/profits*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('admin.profits') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-panel"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/profits.title') }}</span>
                </a>
            </li>
            <li class="side-nav-menu-item {{ request()->is('admin/commission*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('employee-commission') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-write"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">Mitarbeiterprovision</span>
                </a>
            </li>

            <li class="side-nav-menu-item {{ request()->is('user/myorders*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('my-orders') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-dropbox"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('backend/orders.title') }}</span>
                </a>
            </li>
            <div class="dropdown-divider"></div>
            @endif

            <!-- Settings -->
            <!-- <li class="side-nav-menu-item {{ request()->is('meine-tids*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('orders') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-layout-grid-4-alt"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('frontend/user.orders') }}</span>
                </a>
            </li>
            
            <li class="side-nav-menu-item {{ request()->is('btc-einzahlung*')? 'active' : '' }}" >
                <a class="side-nav-menu-link media align-items-center" href="{{ route('deposit-btc') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-credit-card"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('frontend/user.deposit') }}</span>
                </a>
            </li>
            <li class="side-nav-menu-item  {{ request()->is('meine-einzahlungen*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('transactions') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-layout-tab"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('frontend/user.transactions') }}</span>
                </a>
            </li>
            <li class="side-nav-menu-item {{ request()->is('gutschein-einloesen*')? 'active' : '' }}">
                <a class="side-nav-menu-link media align-items-center" href="{{ route('deposit-btc-redeem') }}">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="gd-loop"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">{{ __('frontend/user.deposit_btc_redeem') }}</span>
                </a>
            </li> -->
            
            <!-- End Settings -->


        </ul>
    </aside>
    <!-- End Sidebar Nav -->

    <div class="content">
        @yield('content')

        <!-- Footer -->
        <footer class="small p-3 px-md-4 mt-auto">
            <!-- <div class="row justify-content-between">
                <div class="col-lg text-center text-lg-left mb-3 mb-lg-0">
                    <ul class="list-dot list-inline mb-0">
                        <li class="list-dot-item list-dot-item-not list-inline-item mr-lg-2"><a class="link-dark" href="#">FAQ</a></li>
                        <li class="list-dot-item list-inline-item mr-lg-2"><a class="link-dark" href="#">Support</a></li>
                        <li class="list-dot-item list-inline-item mr-lg-2"><a class="link-dark" href="#">Contact us</a></li>
                    </ul>
                </div>

                <div class="col-lg text-center mb-3 mb-lg-0">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item mx-2"><a class="link-muted" href="#"><i class="gd-twitter-alt"></i></a></li>
                        <li class="list-inline-item mx-2"><a class="link-muted" href="#"><i class="gd-facebook"></i></a></li>
                        <li class="list-inline-item mx-2"><a class="link-muted" href="#"><i class="gd-github"></i></a></li>
                    </ul>
                </div> -->

                <div class="col-lg text-center text-lg-right">
                &copy; {{ date('Y') }} {{ ucwords(parse_url(env('APP_URL', 'https://Fake-tids.su'), PHP_URL_HOST)) }}. All Rights Reserved.
                </div>
            </div>
        </footer>
        <!-- End Footer -->
    </div>
</main>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="{{ asset_dir('admin/assets/vendors/general/moment/min/moment.min.js') }}" type="text/javascript"></script>
<script src="{{asset('graindashboard/js/graindashboard.js')}}"></script>
<script src="{{asset('graindashboard/js/graindashboard.vendor.js')}}"></script>
<script src="{{ asset_dir('admin/assets/vendors/general/summernote/dist/summernote.js') }}" type="text/javascript"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>


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
        @section('page_scripts')
        @show
</body>
</html>