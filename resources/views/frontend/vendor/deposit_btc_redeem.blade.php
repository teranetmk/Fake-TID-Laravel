@extends('frontend.layouts.dashboard')

@section('content')
<div class="container mt-3 deposit-page">
    <div class="row justify-content-center"> 
        <div class="col-md-12">
            @component('components.frontend.box')
                @slot('title'){{ __('frontend/user.coupon_redeem.title') }}@endslot

                <form action="{{ route('deposit-btc-redeem-coupon') }}" method="POST" class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="coupon_redeem_code" placeholder="{{ __('frontend/user.coupon_redeem.label') }}"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-danger btn-lg">{{ __('frontend/user.coupon_redeem.title') }}</button>
                        </div>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
</div>
@endsection
