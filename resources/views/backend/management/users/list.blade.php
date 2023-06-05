@extends('backend.layouts.default')

@section('content')
                            <div class="k-content__head	k-grid__item">
									<div class="k-content__head-main">
										<h3 class="k-content__head-title">{{ __('backend/management.users.title') }}</h3>
										<div class="k-content__head-breadcrumbs">
											<a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
										</div>
									</div>
								</div>
								
								@if(Session::has('successMessageSettingsPassword'))
								<div class="alert alert-success alert-dismissible fade show" role="alert">
									<button type="button" class="close" data-dismiss="alert"
											aria-label="{{ __('frontend/main.close') }}">
										<span aria-hidden="true">×</span>
									</button>

									{{ Session::get('successMessageSettingsPassword') }}
								</div>
							@endif
							
							@if($errors->any())
							<div class="alert alert-success alert-dismissible fade show" role="alert">
									<button type="button" class="close" data-dismiss="alert"
											aria-label="{{ __('frontend/main.close') }}">
										<span aria-hidden="true">×</span>
									</button>

									@foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
								</div>
                                    @endif
                                
								<div class="k-content__body	k-grid__item k-grid__item--fluid">
									<form class="inline-form mb-4">
										<div class="row">
											<div class="col-lg-6">
												<input type="text" name="term" value="{{ $term }}" class="form-control" placeholder="Schlüsselwörter: Benutzer-ID, Benutzername"/>
											</div>
											<div class="col-lg-3">
												<select name="order_by" class="form-control" >
													<option value="created_at" <?= $order_by == 'created_at' ? ' selected' : '' ?>>Datum</option>
													<option value="balance_in_cent" <?= $order_by == 'balance_in_cent' ? ' selected' : '' ?>>Guthaben</option>
												</select>
											</div>
											<div class="col-lg-1">
												<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;Suche</button>
											</div>
										</div>
									</form>
								</div>
								<div class="k-content__body	k-grid__item k-grid__item--fluid">
									<div class="row">
										<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
											<div class="kt-portlet">
												<div class="kt-portlet__body">
													<!-- show switch filter to order by date or amount -->

													<div class="kt-section kt-section--first">
														@if(count($users))
														<table class="table table-head-noborder">
															<thead>
																<tr>
																	<th>{{ __('backend/management.users.id') }}</th>
																	<!--
																	<th>{{ __('backend/management.users.name') }}</th>
																	-->	
																	<th>{{ __('backend/management.users.username') }}</th>
																	<th>{{ __('backend/management.users.jabber') }}</th>
																	<th>{{ __('backend/management.users.newsletter_enabled') }}</th>
																	<th>{{ __('backend/management.users.balance') }}</th>
																	<th>{{ __('backend/management.users.date') }}</th>
																	<!-- <th>{{ __('backend/management.users.transactions_count') }}</th> -->
																	<th>{{ __('backend/management.users.actions') }}</th>
																</tr>
															</thead>
															<tbody>
																@foreach($users as $user)
																<tr>
																	<th scope="row">{{ $user->id }}</th>
																	<!--
																	<td>{{ $user->name }}</td>
																	-->
																	<td>{{ $user->username }}</td>
																	<td>{{ $user->jabber_id }}</td>
																	<td>{{ $user->newsletter_enabled == 1 ? __('backend/management.users.enabled') : __('backend/management.users.disabled') }}</td>
																	<td>{{ $user->getFormattedBalance() }}</td>
																	<td>
																		{{ $user->created_at->format('d.m.Y H:i') }}
																	</td>
																	<!-- <td>{{$user->getTransactionCount()}}</td> -->
																	<td style="font-size: 20px;">
																		<a href="{{ route('backend-management-user-edit', $user->id) }}"><i class="la la-edit"></i></a>
																		<a href="{{ route('backend-management-user-delete', $user->id) }}"><i class="la la-trash"></i></a>
																		<a href="{{ route('backend-management-user-login', $user->id) }}"><i class="la la-sign-in"></i></a>
																		<a href="javascript:void(0);" onclick="showModal('{{ $user->id }}')"><i class="las la-lock-open"></i></a>
																	</td>
																</tr>
																@endforeach
															</tbody>
														</table>

														{!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.su", preg_replace('/' . $users->currentPage() . '\?page=/', '', $users->links())) !!}
														@else
														<i>{{ __('backend/main.no_entries') }}</i>
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal -->

@endsection
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
	  <form method="post" onsubmit="validateForm();" action="{{route('backend-change-user-password')}}">
      <div class="modal-body">
	  
		@csrf
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">New Password:</label>
            <input type="password" id="password" name="password" class="form-control" id="recipient-name" required>
          </div>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirmpassword" class="form-control" id="recipient-name" required>
          </div>
		  <p id="validate-status"></p>
		  <input type="hidden" id="userid" name="userid" value="">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
	  </form>
    </div>
  </div>
</div>
@section('page_scripts')
<script>
	function showModal(id){
     $(".modal-body #userid").val( id );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
     $('#exampleModalCenter').modal('show');
};

</script>
@endsection