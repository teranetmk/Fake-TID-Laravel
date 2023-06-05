@extends('frontend.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">

                @component('components.frontend.box')

                    @slot('title'){{ $ticket->subject }}@endslot

                    <div class="ticket-reply">
                        <div class="ticket-content d-flex">
                            <div class="align-items-center d-flex flex-column image_block mr-3">
                                <img src="{{ asset('/assets/svg/missing_avatar.svg') }}" alt="" width="40" height="40" class="">

                                <span
                                    style="
                                    text-overflow: ellipsis;
                                    width: 60px;
                                    overflow: hidden;
                                    white-space: nowrap;
                                    text-align: center;"
                                >{{ Auth::user()->username }}</span>
                            </div>
                            <span>{!! nl2br(strlen($ticket->content) > 0 ? e($ticket->content) : '') !!}</span>
                        </div>
                        <span class="ticket-reply-span">{{ $ticket->getDateTime() }}</span>
                    </div>

                    @if(count($ticketReplies) > 0)
                        <hr/>@endif

                    @foreach( $ticketReplies as $ticketReply )


                        <div
                            class="ticket-reply @if( $ticketReply->user_id == Auth::user()->id )  @else ticket-reply-answer @endif">

                            <div class="ticket-content d-flex">
                                <div class="align-items-center d-flex flex-column image_block mr-3">
                                    @if( $ticketReply->user_id == Auth::user()->id )
                                        <img src="{{ asset('/assets/svg/missing_avatar.svg') }}" alt="" width="40" height="40"
                                             class="">
                                        <span style="text-overflow: ellipsis;
    width: 60px;
    overflow: hidden;
    white-space: nowrap;
text-align: center;">
                                            {{ Auth::user()->username }}
                                        </span>
                                    @else
                                        <img src="{{ asset('/assets/img/DHL_Support_icon.png') }}" alt="" width="40" height="40"
                                             class="">
                                        <span style="text-overflow: ellipsis;
    width: 60px;
    overflow: hidden;
    white-space: nowrap;
text-align: center;">Support</span>
                                    @endif
                                </div>
                                <span>{!! nl2br(strlen($ticketReply->content) > 0 ? e($ticketReply->content) : '') !!}</span>
                            </div>

                            <span class="ticket-reply-span">{{ $ticketReply->getDateTime() }}</span>
                        </div>
                    @endforeach

                    @if(!$ticket->isClosed())

                        <hr/>

                        <form method="POST" action="{{ route('ticket-reply', $ticket->id) }}">

                            @csrf

                            <div class="form-group">
                                <label for="message">{{ __('frontend/user.tickets.message') }}</label>
                                <textarea
                                    class="br-outline-input form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                    name="message" id="message" rows="3" required
                                    autofocus>{{ old('message') }}</textarea>

                                @if ($errors->has('message'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <!--<div class="h-captcha" data-sitekey="{{ env('HCAPTCHA_PUBLIC_KEY') }}"></div>-->
                                    <div class="h-captcha" data-sitekey="b885a687-18d7-4744-bab9-c32094313972"></div>
                                    
                                </div>
                            </div>

                            <div class="text-left">
                                <input type="submit" value="{{ __('frontend/user.tickets.reply_button') }}"
                                       class="btn btn-red"/>
                            </div>
                        </form>
                    @endif

                @endcomponent

            </div>
        </div>
    </div>
@endsection
