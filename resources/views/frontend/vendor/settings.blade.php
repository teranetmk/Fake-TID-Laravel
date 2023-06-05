@extends('frontend.layouts.dashboard')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @component('components.frontend.box')

                    @slot('title'){{ __('frontend/user.settings_change_password') }}@endslot

                    @if(Session::has('successMessageSettingsPassword'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert"
                                    aria-label="{{ __('frontend/main.close') }}">
                                <span aria-hidden="true">×</span>
                            </button>

                            {{ Session::get('successMessageSettingsPassword') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('settings-password-change') }}"
                          class="d-flex flex-column align-items-center">
                        @csrf

                        <div class="row mt-2 mb-3 col-md-7">

                            <div class="">

                                <div class="form-outline ml-3">

                                    <input type="password"
                                           id="settings_current_password"
                                           class="br-outline-input form-control{{ $errors->has('settings_current_password') ? ' is-invalid' : '' }}"
                                           name="settings_current_password"
                                           value="{{ old('settings_current_password') }}"
                                           required
                                           autofocus>

                                    <label for="settings_current_password"
                                           class="form-label f-sm bg-white-imp">{{ __('frontend/user.settings_current_password') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('settings_current_password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('settings_current_password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row mt-2 mb-3 col-md-7">

                            <div class="">

                                <div class="form-outline ml-3">
                                    <input type="password"
                                           id="settings_new_password"
                                           class="br-outline-input form-control{{ $errors->has('settings_new_password') ? ' is-invalid' : '' }}"
                                           name="settings_new_password"
                                           value="{{ old('settings_new_password') }}"
                                           required
                                           autofocus/>

                                    <label for="settings_new_password"
                                           class="form-label f-sm bg-white-imp">{{ __('frontend/user.settings_new_password') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('settings_new_password'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('settings_new_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                      


                        <div class="row mt-2 mb-3 col-md-7">

                            <div class="">

                                <div class="form-outline ml-3">
                                    <input id="settings_new_password_confirm" type="password"
                                           class="br-outline-input form-control{{ $errors->has('settings_new_password_confirm') ? ' is-invalid' : '' }}"
                                           name="settings_new_password_confirm"
                                           value="{{ old('settings_new_password_confirm') }}" required autofocus>

                                    <label for="settings_new_password_confirm"
                                           class="form-label f-sm bg-white-imp">{{ __('frontend/user.settings_new_password_confirm') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('settings_new_password_confirm'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('settings_new_password_confirm') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        

                        <div class="row mt-2 mb-3 col-md-7">
                            <div class="">
                                <div class="form-outline ml-3">
                                    <button type="submit"
                                            class="btn btn-danger d-block ml-auto mr-0">
                                        {{ __('frontend/user.settings_save_submit') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                      

                    </form>

                @endcomponent

            </div>


            <div class="col-md-8 mt-15">

               
            </div>
        </div>
    </div>
@endsection
