@extends('backend.layouts.default')

@section('content')
                            	<div class="k-content__head	k-grid__item">
					<div class="k-content__head-main" style="display:flex;flex-direction:row;width:100%;justify-content:space-between">
					  <div style="display:flex;">
						<h3 class="k-content__head-title">{{ __('backend/management.tickets.title') }}</h3>
						<div class="k-content__head-breadcrumbs">
							<a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
							<span class="k-content__head-breadcrumb-separator"></span>
							<a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
						</div>

					   </div>
        <form class="inline-form mb-4">
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" name="term" value="{{ $term }}" class="form-control" placeholder="Schlüsselwörter: Benutzername, Bestell-ID, DHL-Tracking-Nummer"/>
                </div>
                <div class="col-lg-6">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;Suche</button>
                </div>
            </div>
        </form>

									</div>
								</div>
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
																		<a href="{{ route('backend-management-ticket-edit', $ticket->id) }}"><i class="la la-edit"></i></a>
																		<a href="{{ route('backend-management-ticket-delete', $ticket->id) }}"><i class="la la-trash"></i></a>
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
@endsection

@section('page_scripts')

@endsection