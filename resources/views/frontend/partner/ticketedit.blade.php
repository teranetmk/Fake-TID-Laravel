@extends('frontend.layouts.dashboard')

@section('content')
<div class="py-4 px-3 px-md-4">

	<div class="k-content__head	k-grid__item">
		<div class="k-content__head-main">
			<h3 class="k-content__head-title">{{ __('backend/management.tickets.edit.title') }}</h3>
		</div>
	</div>

	<div class="k-content__body	k-grid__item k-grid__item--fluid">
		<div class="row">
			<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
				@if(!$ticket->isClosed())
				<a href="{{ route('partner-ticket-close', $ticket->id) }}" class="btn btn-wide btn-bold btn-danger btn-upper" style="margin-bottom:15px">{{ __('backend/management.tickets.edit.close') }}</a>
				@else
				<a href="{{ route('partner-ticket-open', $ticket->id) }}" class="btn btn-wide btn-bold btn-success btn-upper" style="margin-bottom:15px">{{ __('backend/management.tickets.edit.open') }}</a>
				@endif

				<div class="k-portlet k-portlet--height-fluid">
					<div class="k-portlet__head">
						<div class="k-portlet__head-label">
							<h3 class="k-portlet__head-title">
								{{ $ticket->subject }}
							</h3>
						</div>
					</div>
					<div class="k-portlet__body k-portlet__body--fluid">
						<div style="width: 100%">
							<div class="card">
								<div class="card-body">
									<p>{!! nl2br(strlen($ticket->content) > 0 ? e($ticket->content) : '') !!}</p>
								</div>
								<div class="card-footer text-muted">
									{{ $ticket->getDateTime() }} | {{ $ticket->getUser()->name }} | <b>{{ __('backend/management.tickets.edit.category') }}</b> {{ $ticket->getCategory()->name }}
								</div>
							</div>
							
							<hr />

							@foreach($ticketReplies as $ticketReply)
							<div class="card">
								<div class="card-body" style="@if($ticketReply->user_id == Auth::user()->id) background-color: #f2f2f2 !important; @endif">
									<p>{!! nl2br(strlen($ticketReply->content) > 0 ? e($ticketReply->content) : '') !!}</p>
								</div>
								<div class="card-footer text-muted">
									{{ $ticketReply->getDateTime() }} | {{ $ticketReply->getUser()->name }}
								</div>
							</div>

							<hr />
							@endforeach

							

							<h5>{{ __('backend/management.tickets.edit.title_reply') }}</h5>

							<form method="POST" class="kt-form" action="{{ route('partner-ticket-reply-form') }}" style="width: 100%">
								@csrf

								<input type="hidden" name="ticket_reply_id" value="{{ $ticket->id}}" />

								<div class="form-group" style="width: 100%">
									<label for="ticket_reply_msg">{{ __('backend/management.tickets.edit.message') }}</label>
									<textarea style="width: 100%" class="form-control @if($errors->has('ticket_reply_msg')) is-invalid @endif" id="ticket_reply_msg" name="ticket_reply_msg" placeholder="{{ __('backend/management.tickets.edit.message') }}">{{ old('ticket_reply_msg') }}</textarea>

									@if($errors->has('ticket_reply_msg'))
										<span class="invalid-feedback" style="display:block" role="alert">
											<strong>{{ $errors->first('ticket_reply_msg') }}</strong>
										</span>
									@endif
								</div>

								<button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.tickets.edit.submit_button') }}</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('page_scripts')

@endsection