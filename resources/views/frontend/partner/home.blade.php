@extends('frontend.layouts.dashboard')

@section('content')
<div class="py-4 px-3 px-md-4">

    <div class="mb-3 mb-md-4 d-flex justify-content-between">
        <div class="h3 mb-0">Dashboard</div>
    </div>
    <p>
        {!! __('frontend/user.panel.welcome_message', [ 'name' => e(Auth::user()->username) ]) !!}
    </p>

    <p>
        {!! __('frontend/user.panel.member_since', [ 'date' => e(Auth::user()->created_at->format('d.m.Y')) ]) !!}
    </p>
    


</div>

@endsection
