@extends('frontend.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">


                @component('components.frontend.box')

                    @slot('title'){{ __('frontend/user.tickets.ticket_create') }}@endslot

                    @if (\Session::has('errorMessageTicketForm'))
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-label="{{ __('frontend/main.close') }}">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                        {!! \Session::get('errorMessageTicketForm') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (\Session::has('successMessageTicketForm'))
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-label="{{ __('frontend/main.close') }}">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                        {!! \Session::get('successMessageTicketForm') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ticket-create-form') }}">

                        @csrf

                        <div class="row mt-2 mb-3">

                            <div class="col-md-7">

                                <div class="form-outline ml-3">

                                    <input id="subject"
                                           class="br-outline-input form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}"
                                           name="subject"
                                           value="{{ old('subject') }}"
                                           required
                                           autofocus/>

                                    <label class="form-label f-sm bg-white-imp"
                                           for="subject">{{ __('frontend/user.tickets.subject') }}</label>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('subject'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('subject') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="row mt-2 mb-3">
                            <div class="col-md-7">
                                <div class="form-outline ml-3">
                                    <select
                                        class="br-outline-input form-control{{ $errors->has('ticket_category') ? ' is-invalid' : '' }}"
                                        name="ticket_category" id="ticket_category">
                                        <option value="0">{{ __('frontend/main.please_choose') }}</option>
                                        @foreach(\App\Models\UserTicketCategory::orderBy('name')->get() as $userTicketCategory)
                                            <option value="{{ $userTicketCategory->id }}"
                                                    @if(old('ticket_category') == $userTicketCategory->id) selected @endif>{{ $userTicketCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label f-sm bg-white-imp"
                                           for="ticket_category">{{ __('frontend/user.tickets.category') }}</label>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('ticket_category'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('ticket_category') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3">
                            <div class="col-md-7">
                                <div class="form-outline ml-3">
                                    <textarea style="padding-top: 20px"
                                              class="br-outline-input form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                              name="message" id="message" rows="3"
                                              required>{{ old('message') }}</textarea>
                                    <label class="form-label f-sm bg-white-imp"
                                           for="message">{{ __('frontend/user.tickets.message') }}</label>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-12">
                                    @if ($errors->has('message'))
                                        <span class="invalid-feedback" role="alert">
												<strong>{{ $errors->first('message') }}</strong>
											</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3">
                            <div class="col-md-7">
                                <div class="ml-3">
                                    <!--<div class="h-captcha" data-sitekey="{{ env('HCAPTCHA_PUBLIC_KEY') }}"></div>-->
                                    <div class="h-captcha" data-sitekey="b885a687-18d7-4744-bab9-c32094313972"></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-left">
                            <input type="submit" value="{{ __('frontend/user.tickets.create_button') }}"
                                   class="btn btn-red"/>
                        </div>

                    </form>

                @endcomponent

            </div>
        </div>
    </div>
@endsection
