<!-- Modal -->
<div id="login-signup"
     class="modal fade"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">{{ __('frontend/user.login.title') }}</h4>

                <button type="button" class="close btn-close-icon" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="login_form sq-price">
                    <form method="POST" action="{{route('login')}}">
                        @csrf
                        {{-- {{ csrf_field() }} --}}

{{--                        <input type='hidden' name='recaptcha_token' class='recaptcha_token'>--}}


                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input type="text"
                                           name="email"
                                           id="email"
                                           class="br-outline-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           value="{{ old('email') }}" required autofocus>

                                    <label class="form-label f-sm"
                                           for="form1">{{ __('frontend/user.username') }}</label>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input id="password" type="password"
                                           class="br-outline-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" required>

                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/user.login.password') }}</label>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="captcha-img">
                                    {{ $captcha_img }}
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="form-outline">
                                    <input type="text"
                                           class="br-outline-input form-control {{ $errors->has('captcha') ? ' is-invalid' : '' }}"
                                           name="captcha" id="captcha" required/>

                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/main.captcha') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('captcha'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        --}}

                        <div class="form-group row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('frontend/user.login.remember') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                              
                                <div class="h-captcha" data-sitekey="98d95bb4-f93a-4508-a4c3-097bf30ceca2"></div>
                                
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-dark-red btn-block">
                                    {{ __('frontend/user.login.submit') }} 
                                </button>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('frontend/user.login.forgot_password') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <a href="javascript:void(0)"
                                   class="btn btn-outline-secondary-red btn-block sing_up change-price">
                                    {{ __('frontend/user.login.create_account') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="none-sq-price">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        {{-- {{ csrf_field() }} --}}

{{--                        <input type='hidden' name='recaptcha_token' class='recaptcha_token'>--}}

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input id="username" type="text"
                                           class="br-outline-input form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                                           name="username" value="{{ old('username') }}" required autofocus>
                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/user.username') }}</label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        @if ($errors->has('username'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input id="jabber_id" type="email"
                                           class="br-outline-input form-control{{ $errors->has('jabber_id') ? ' is-invalid' : '' }}"
                                           name="jabber_id" value="{{ old('jabber_id') }}" required>
                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/user.jabber_id') }}</label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        @if ($errors->has('jabber_id'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('jabber_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input id="password" type="password"
                                           class="br-outline-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" required>
                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/user.register.password') }}</label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-outline">
                                    <input id="password-confirm" type="password" class="br-outline-input form-control"
                                           name="password_confirmation" required>
                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/user.register.confirm_password') }}</label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="captcha-img">
                                    {{ $captcha_img }}
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="form-outline">
                                    <input type="text"
                                           class="br-outline-input form-control {{ $errors->has('captcha') ? ' is-invalid' : '' }}"
                                           name="captcha" id="captcha" required/>
                                    <label class="form-label  f-sm"
                                           for="form1">{{ __('frontend/main.captcha') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('captcha'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('captcha') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        --}}

                        <div class="form-group row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="newsletter_enabled"
                                           id="newsletter_enabled"
                                        {{--                                        {{ old('newsletter_enabled') ? 'checked' : App\Models\Setting::get('register.newsletter_enabled') ? 'checked' : '' }}--}}
                                     checked>

                                    <label class="form-check-label" for="newsletter_enabled">
                                        {{ __('frontend/user.register.newsletter_enabled') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="h-captcha" data-sitekey="98d95bb4-f93a-4508-a4c3-097bf30ceca2"></div>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-dark-red btn-block">
                                    {{ __('frontend/user.register.submit') }}
                                </button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <a href="javascript:void(0)"
                                   class="btn btn-outline-secondary-red btn-block sing_up change-price">
                                    {{ __('frontend/user.login.submit') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- script for Switching the signup -->

<script type="text/javascript">
    $(document).ready(function () {

        $('.none-sq-price').hide();
        $('.change-price').on('click',
            function () {
                $('.sq-price, .none-sq-price').toggle(600);
            }
        );
    });
</script>

