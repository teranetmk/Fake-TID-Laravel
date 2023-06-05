@extends('backend.layouts.default')

@section('content')
                            	<div class="k-content__head	k-grid__item">
									<div class="k-content__head-main">
										<h3 class="k-content__head-title">{{ __('backend/management.tickets.edit.title') }}</h3>
									</div>
								</div>

								<div class="k-content__body	k-grid__item k-grid__item--fluid">
									<div class="row">
										<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
											@if(!$ticket->isClosed())
											<a href="{{ route('backend-management-ticket-close', $ticket->id) }}" class="btn btn-wide btn-bold btn-danger btn-upper" style="margin-bottom:15px">{{ __('backend/management.tickets.edit.close') }}</a>
											@else
											<a href="{{ route('backend-management-ticket-open', $ticket->id) }}" class="btn btn-wide btn-bold btn-success btn-upper" style="margin-bottom:15px">{{ __('backend/management.tickets.edit.open') }}</a>
											@endif

<div class="k-portlet">
    <form method="post" class="kt-form" action="{{ route('backend-management-ticket-ballance', $ticket->id) }}">
        @csrf
        <div class="form-group pt-3">
            <label for="user_edit_balance">{{ __('backend/management.users.balance_in_cent') }}</label>
            <div class="d-flex">
                <div class="flex-grow-1">
                    <input type="number" class="form-control @if($errors->has('user_edit_balance')) is-invalid @endif" id="user_edit_balance" name="user_edit_balance" placeholder="{{ __('backend/management.users.balance') }}" value="{{ $ticket->user->balance_in_cent }}" />
                    @if($errors->has('user_edit_balance'))
                        <span class="invalid-feedback" style="display:block" role="alert">
                            <strong>{{ $errors->first('user_edit_balance') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="ml-3">
                    <button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.users.edit.submit_button') }}</button>
                </div>
            </div>
        </div>
    </form>
</div>

<h3 class="pt-3">{{ __('frontend/user.transactions_history') }}</h3>
<div class="table-responsive pb-3 k-portlet">
    <table class="table table-transactions table-striped" id="transactionTable">
        <tr>
            <th scope="col">{{ __('frontend/user.transactions_page.id') }}</th>
            <th scope="col">{{ __('frontend/user.transactions_page.txid_label') }}</th>
            <th scope="col">{{ __('frontend/user.transactions_page.status') }}</th>
            <th scope="col">{{ __('frontend/user.transactions_page.amount') }}</th>
            <th scope="col">{{ __('frontend/user.transactions_page.date') }}</th>
        </tr>
        @foreach($user_transactions as $transaction)
        <tr class="@if($transaction->isPending()) bg-light @elseif($transaction->isWaiting()) bg-light-2 @endif">
            <th scope="row">#{{ $transaction->id }}</th>							
                <td><span id="transactionId" style="display:none;">{{ $transaction->id }}</span>{{ strlen($transaction->txid) ? $transaction->txid : '' }}</td>
                <td>
                    @if($transaction->isPaid())
                        <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.confirmed') }}</span>											
                        <span class="label label-success" id="show-data">
                        {{ __('frontend/user.transactions_page.confirmed') }}
                    </span>
                    @elseif($transaction->isPending()) 
                     <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.confirmations') }}</span>
                        <span class="label label-warning show-data" id="show-data"> 
						
                        {{ __('frontend/user.transactions_page.confirmations', [
                            'confirms' => $transaction->confirmations,
                            'confirms_needed' => App\Models\Setting::get('shop.btc_confirms_needed')
                        ]) }}
                    </span>
                    @else 
                        <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.waiting') }}</span>
                        <span class="label label-secondary" id="show-data"> 
                        {{ __('frontend/user.transactions_page.waiting') }}
                    </span>
                    @endif
                </td>
                <td>
                    {{ $transaction->getFormattedAmount() }}
                </td>
                <td>
                    {{ $transaction->getDate() }}
                </td>
            </tr>
        @endforeach
    </table>
</div>

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
				
														<h5>{{ __('backend/management.tickets.edit.move_category') }}</h5>
														
														<form method="POST" class="kt-form" action="{{ route('backend-management-ticket-move-form') }}" style="width: 100%">
															@csrf

															<input type="hidden" name="ticket_id" value="{{ $ticket->id}}" />

															<div class="form-group" style="width: 100%">
																<label for="ticket_move_category">{{ __('backend/management.tickets.edit.move_category') }}</label>
																<select style="width: 100%" class="form-control @if($errors->has('ticket_move_category')) is-invalid @endif" id="ticket_move_category" name="ticket_move_category">
																	<option value="0">{{ __('frontend/main.please_choose') }}</option>
																	@foreach(\App\Models\UserTicketCategory::orderBy('name')->get() as $userTicketCategory)
																		<option value="{{ $userTicketCategory->id }}" @if($ticket->category_id == $userTicketCategory->id) selected @endif>{{ $userTicketCategory->name }}</option>
																	@endforeach
																</select>

																@if($errors->has('ticket_move_category'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('ticket_move_category') }}</strong>
																	</span>
																@endif
															</div>

															<button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.tickets.edit.move') }}</button>
														</form>

														<hr />

														<h5>{{ __('backend/management.tickets.edit.title_reply') }}</h5>

														<form method="POST" class="kt-form" action="{{ route('backend-management-ticket-reply-form') }}" style="width: 100%">
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
@endsection

@section('page_scripts')

@endsection
