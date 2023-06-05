@extends('frontend.layouts.app')


@section('content')

    <div class="container">

        <div class="custom-banner">
            <img src="{{ asset('assets/img/banner-1.jpg') }}" alt="pic" class="custom-banner--img">
            <div class="custom-banner--container">
                <p class="custom-banner--text">
                    Die #1 in Deutschland
                </p>
                <p class="custom-banner--title">
                    für Fake-TIDs.<br>
                    zuverlässig,<br>
                    schnell und fair.
                </p>
                <a href="/deutschland_tids" class="custom-banner--link custom--btn">Jetzt bestellen</a>
            </div>
        </div>

        <div class="custom-search">
            <p class="custom-search--title">
                Sendung verfolgen
            </p>
            <div class="custom-search--container">
                <form action="https://www.dhl.de/de/privatkunden.html" target="_blank">
                    <input type="text"
                           name="piececode"
                           class="br-outline-input custom-search--input"
                           placeholder="Sendungsnummer eingeben">

                    <button class="custom-search--btn custom--btn">
                        <svg role="img" xmlns="http://www.w3.org/2000/svg" version="1.1" class="search">
                            <path xmlns="http://www.w3.org/2000/svg" id="bga"
                                  d="M21 19.794l-6.063-5.938c.947-1.16 1.563-2.598 1.563-4.222C16.5 5.969 13.468 3 9.774 3 6.032 3 3 5.97 3 9.634c0 3.665 3.032 6.588 6.774 6.588 1.42 0 2.7-.418 3.79-1.16L19.673 21 21 19.794zM4.516 9.68c0-2.83 2.32-5.103 5.21-5.103s5.21 2.274 5.21 5.103c0 2.83-2.32 5.104-5.21 5.104-2.842 0-5.21-2.274-5.21-5.104z"/>
                        </svg>
                        Suchen
                    </button>
                </form>
            </div>
        </div>

        <div class="custom-block">
            <div class="custom-wrapper">
                <h2 class="custom-block--title">
                    Unsere Vorteile
                </h2>
                <p class="custom-block--subtitle">
                    Auf einem Blick:
                </p>

                <div class="custom-block--items">


                    <div class="item--wrapper">
                        <div class="item">
                            <img src="{{ asset('assets/img/img.sly.1573119005316.3200.medium.jpg') }}" alt="pic" class="item--pic">
                            <div class="item--inner">
                                <p class="item--title">
                                    Zuverlässige Einlieferung.
                                </p>
                                <p class="item--text">
                                    Alle Bestellung werden zuverlässig täglich eingeliefert.
                                </p>
                                <a href="/deutschland_tids" class="item--link">
                                    Jetzt bestellen
                                    <svg tabindex="-1" role="image" class="arrow-link-right">
                                        <use tabindex="-1" xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="/etc.clientlibs/redesign/clientlibs/static/resources/icons/sprite.svg#arrow-link-right"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="item--wrapper">
                        <div class="item">
                            <img src="{{ asset('assets/img/img.sly.1573119051632.3200.medium.jpg') }}" alt="pic" class="item--pic">
                            <div class="item--inner">
                                <p class="item--title">
                                    Sofortigen Erhalt der TID.
                                </p>
                                <p class="item--text">
                                    Erhalte die TID sofort nach Abschluss deiner Bestellung.
                                </p>
                                <a href="/deutschland_tids" class="item--link">
                                    Jetzt bestellen
                                    <svg tabindex="-1" role="image" class="arrow-link-right">
                                        <use tabindex="-1" xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="/etc.clientlibs/redesign/clientlibs/static/resources/icons/sprite.svg#arrow-link-right"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item--wrapper">
                        <div class="item">
                            <img src="{{ asset('assets/img/img.sly.1602672742265.3200.medium1.png') }}" alt="pic" class="item--pic">
                            <div class="item--inner">
                                <p class="item--title">
                                    Filial-Einlieferung.
                                </p>
                                <p class="item--text">
                                    Großes Paket? Kein Problem! Jetzt auch Filialeinlieferungen möglich.
                                </p>
                                <a href="/deutschland_tids" class="item--link">
                                    Jetzt bestellen
                                    <svg tabindex="-1" role="image" class="arrow-link-right">
                                        <use tabindex="-1" xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="/etc.clientlibs/redesign/clientlibs/static/resources/icons/sprite.svg#arrow-link-right"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('shop') }}" class="custom-calculator">
                    <p class="custom-calculator--title">
                        Versand berechnen und online sparen
                    </p>
                    <div class="custom-calculator--container">
                        <div class="custom-calculator--group">
                            <p class="custom-calculator--label">Zielland / Zielregion:</p>
                            <select name="target_place" class="custom-calculator--select calculate">
                                <option value="0">Deutschland</option>
                                <option value="15">Europa</option>
                            </select>
                        </div>

                        <div class="custom-calculator--group">
                            <p class="custom-calculator--label">Gewicht</p>
                            <select name="weight" class="custom-calculator--select calculate">
                                <option value="25">5kg</option>
                                <option value="30">10kg</option>
                                <option value="35">31.5kg</option>
                            </select>
                        </div>

                        <div class="custom-calculator--group">
                            <p class="custom-calculator--label">Versandart:</p>
                            <select name="shipping_method" class="custom-calculator--select calculate">
                                <option value="0">schnellstmöglich</option>
                                <option value="5">Wunschtermin</option>
                            </select>
                        </div>
                        <div class="custom-calculator--group">
                            <p class="custom-calculator--label" style="text-align: center">Online-Preis</p>
                            <p class="custom-calculator--total">
                                ab 100 EUR
                            </p>
                        </div>
                    </div>

                    <a href="/deutschland_tids" class="custom-calculator--btn custom--btn">Weiter</a>
                </form>
            </div>
        </div>


    </div>

   @include('frontend/partials/news-modal')
@endsection
