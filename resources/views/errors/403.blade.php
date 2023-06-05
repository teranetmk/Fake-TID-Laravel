@extends('frontend.layouts.app')


@section('content')

    <div class="container">
        <div class="container_404">
            <h3 class="custom-block--title mt-4">
                Fehlermeldung - Wir bitten um Entschuldigung 
            </h3>
            <p class="custom-block--subtitle">
                Leider kann die gewünschte Seite nicht angezeigt werden. Möglicherweise ist die von Ihnen aufgerufene Seite nicht verfügbar oder der Inhalt ist auf eine andere Seite umgezogen.
            </p>
            <img src="/assets/img/404.svg" alt="pic" class="">
        </div>
    </div>

@endsection
