@extends('frontend.layouts.dashboard')

@section('content')
<div class="py-4 px-3 px-md-4">
                            
	<nav class="d-none d-md-block" aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="#">{{ __('backend/management.tickets.title') }}</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">{{ __('backend/management.title') }}</li>
		</ol>
	</nav>
	<div class="k-content__body	k-grid__item k-grid__item--fluid">
		<div class="row">
			<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
				<div class="kt-portlet">
					<div class="kt-portlet__body">
						<div class="kt-section kt-section--first">
							@if(count($tickets))
							<table class="table table-head-noborder">
								<thead>
									<tr>
										<th>{{ __('backend/management.tickets.id') }}</th>
										<th>{{ __('backend/management.tickets.user') }}</th>
										<th>{{ __('backend/management.tickets.subject') }}</th>
										<th>{{ __('backend/management.tickets.category') }}</th>
										<th>{{ __('backend/management.tickets.status') }}</th>
										<th>{{ __('backend/management.tickets.date') }}</th>
										<th>{{ __('backend/management.tickets.actions') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($tickets as $ticket)
									<tr>
										<th scope="row">{{ $ticket->id }}</th>
										<td>
											{{ $ticket->getUser()->username }}
										</td>
										<td>{{ $ticket->subject }}</td>
										<td>{{ $ticket->getCategory()->name }}</td>
										<td>
											@if($ticket->isClosed())
											<span class="kt-badge kt-badge--danger kt-badge--dot kt-badge--md"></span>
											<span class="kt-label-font-color-2 kt-font-bold">{{ __('backend/management.tickets.status_data.closed') }}</span>
											@elseif($ticket->isAnswered())
											<span class="kt-badge kt-badge--brand kt-badge--dot kt-badge--md"></span>
											<span class="kt-label-font-color-2 kt-font-bold">{{ __('backend/management.tickets.status_data.replied') }}</span>
											@else
											<span class="kt-badge kt-badge--success kt-badge--dot kt-badge--md"></span>
											<span class="kt-label-font-color-2 kt-font-bold">{{ __('backend/management.tickets.status_data.open') }}</span>
											@endif
										</td>
										<td>
											{{ $ticket->created_at->format('d.m.Y H:i') }}
										</td>
										<td style="font-size: 20px;">
											<a href="{{ route('backend-management-ticket-edit', $ticket->id) }}"><i  class="gd-pencil-alt"></i></a>
											<a href="{{ route('backend-management-ticket-delete', $ticket->id) }}"><i  class="gd-trash" style="color:red;"></i></a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>

							{!! preg_replace('/' . $tickets->currentPage() . '\?page=/', '', $tickets->links()) !!}
							@else
							<i>{{ __('backend/main.no_entries') }}</i>
							@endif
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