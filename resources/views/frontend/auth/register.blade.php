@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{--
            <h3 class="page-title">{{ __('frontend/user.register.title') }}</h3>
            --}}
            <div class="card">
                <div class="card-header">{{ __('frontend/user.register.title') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!--
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        -->

                        <div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.username') }}</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="br-outline-input form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        -->

                        <div class="form-group row">
                            <label for="jabber_id" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.jabber_id') }}</label>

                            <div class="col-md-6">
                                <input id="jabber_id" type="email" class="br-outline-input form-control{{ $errors->has('jabber_id') ? ' is-invalid' : '' }}" name="jabber_id" value="{{ old('jabber_id') }}" required>

                                @if ($errors->has('jabber_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('jabber_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.register.password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="br-outline-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('frontend/user.register.confirm_password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="fbr-outline-input orm-control" name="password_confirmation" required>
                            </div>
                        </div>

                            {{--
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('frontend/main.captcha') }}</label>
                                <div class="col-md-6">
                                    <div class="captcha-img">
                                        {{ $captcha_img }}
                                    </div>
                                    <input type="text" class="br-outline-input form-control {{ $errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha" id="captcha" required />

                                    @if ($errors->has('captcha'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            --}}
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="newsletter_enabled" id="newsletter_enabled"
{{--                                        {{ old('newsletter_enabled') ? 'checked' : App\Models\Setting::get('register.newsletter_enabled') ? 'checked' : '' }}--}}
                                    >

                                    <label class="form-check-label" for="newsletter_enabled">
                                        {{ __('frontend/user.register.newsletter_enabled') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('frontend/user.register.submit') }}
                                </button>
                                <a href="{{ route('index') }}" class="btn btn-outline-secondary">
                                    {{ __('frontend/user.register.cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
