@extends('backend.layouts.default')

@section('content')

    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/orders.show.title', ['id' => $recipient_address ? $recipient_address->order_id : $orderId ]) }}</h3>
            <div class="k-content__head-breadcrumbs">
                <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
                <span class="k-content__head-breadcrumb-separator"></span>
                <a href="{{ route('backend-orders') }}"
                   class="k-content__head-breadcrumb-link">{{ __('backend/orders.title') }}</a>
            </div>
        </div>
    </div>

    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
                <div class="k-portlet k-portlet--height-fluid">

                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{ __('backend/orders.show.form.receiver') }}</h3>
                        </div>
                    </div>
                    <br>
                    <form method="POST" class="kt-form" action="{{ route('backend-order-update',[ (isset($recipient_address) && ! is_null($recipient_address->order_id)) ? $recipient_address->order_id : $orderId ]) }}">
													@csrf
													
													<div class="kt-portlet__body">
														<div class="kt-section kt-section--first">
															<div class="form-group">
																<label for="recipient_first_name">{{ __('backend/orders.show.form.first_name') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_first_name')) is-invalid @endif" id="recipient_first_name" name="recipient_first_name" placeholder="{{ __('backend/orders.show.form.first_name') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_first_name}} @else {{$recipient_address->first_name}} @endif" />

																@if($errors->has('recipient_first_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_first_name') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="recipient_last_name">{{ __('backend/orders.show.form.last_name') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_last_name')) is-invalid @endif" id="recipient_last_name" name="recipient_last_name" placeholder="{{ __('backend/orders.show.form.last_name') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_last_name}} @else {{$recipient_address->last_name}} @endif" />

																@if($errors->has('recipient_last_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_last_name') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="recipient_street">{{ __('backend/orders.show.form.street') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_street')) is-invalid @endif" id="recipient_street" name="recipient_street" placeholder="{{ __('backend/orders.show.form.street') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_street}} @else {{$recipient_address->street}} @endif" />

																@if($errors->has('recipient_street'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_street') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="recipient_zip">{{ __('backend/orders.show.form.zip_code') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_zip')) is-invalid @endif" id="recipient_zip" name="recipient_zip" placeholder="{{ __('backend/orders.show.form.zip_code') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_zip}} @else {{$recipient_address->zip}} @endif" />

																@if($errors->has('recipient_zip'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_zip') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="recipient_city">{{ __('backend/orders.show.form.place') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_city')) is-invalid @endif" id="recipient_city" name="recipient_city" placeholder="{{ __('backend/orders.show.form.place') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_city}} @else {{$recipient_address->city}} @endif" />

																@if($errors->has('recipient_city'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_city') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="recipient_address">{{ __('backend/orders.show.form.address') }}</label>
																<input id="recipient_address" type="text" class="form-control" readonly/>
															</div>
															
															<div class="form-group">
																<label for="recipient_country">{{ __('backend/orders.show.form.country') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_country')) is-invalid @endif" id="recipient_country" name="recipient_country" placeholder="{{ __('backend/orders.show.form.country') }}" value="@if($recipient_address->recipient_first_name) {{$recipient_address->recipient_country}} @else {{$recipient_address->country}} @endif" />

																@if($errors->has('recipient_country'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_country') }}</strong>
																	</span>
																@endif
															</div>
														</div>
													</div>
													<div class="kt-portlet__foot k-container">
														<div class="kt-form__actions">
															<button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/orders.show.form.update') }}</button>
														</div>
													</div>
												</form>


                    

                </div>
            </div>
        </div>


        
    </div>
@endsection

@section('page_scripts')
<script>
(function() {
	var address = document.getElementById('recipient_address');
	var street = document.getElementById('recipient_street');
	var zip = document.getElementById('recipient_zip');
	var city = document.getElementById('recipient_city');
	var addressContent = "";
	
	var updateAddress = function () {
		if (typeof address !== "undefined") {
			addressContent = "";

			if (typeof street !== "undefined" && street.value) {
				addressContent = addressContent.concat(street.value);
			}
			
			if (typeof zip !== "undefined" && zip.value) {
				addressContent = addressContent.concat(`, ${zip.value}`);
			}
			
			if (typeof city !== "undefined" && city.value) {
				addressContent = addressContent.concat(`, ${city.value}`);
			}
	
			address.value = addressContent;
		}
	};

	updateAddress();
})();
</script>
@endsection
