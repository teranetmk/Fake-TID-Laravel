@extends('backend.layouts.default')

@section('content')
    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/orders.show.title', ['id' => ($recipientAddress ? $recipientAddress->order_id : $orderId)]) }}</h3>
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
                    <div class="row mt-2 mb-4">
                        <div class="col-4">
                            <form action="{{ route('backend-order-random-address',[ (isset($recipientAddress) && ! is_null($recipientAddress->order_id)) ? $recipientAddress->order_id : $orderId ]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm ml-3">Randomize</button>
                            </form>
                        </div>
                    </div>
                    <form method="POST" class="kt-form" action="{{ route('backend-order-update-random',[ (isset($recipientAddress) && ! is_null($recipientAddress->order_id)) ? $recipientAddress->order_id : $orderId ]) }}">
													@csrf
													
													<div class="kt-portlet__body">
														<div class="kt-section kt-section--first">
															<div class="form-group">
																<label for="sender_first_name">{{ __('backend/orders.show.form.first_name') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_first_name')) is-invalid @endif" id="sender_first_name" name="sender_first_name" placeholder="{{ __('backend/orders.show.form.first_name') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_first_name) ? $recipientAddress->sender_first_name : old('sender_first_name') }}" />

																@if($errors->has('sender_first_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_first_name') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="sender_last_name">{{ __('backend/orders.show.form.last_name') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_last_name')) is-invalid @endif" id="sender_last_name" name="sender_last_name" placeholder="{{ __('backend/orders.show.form.last_name') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_last_name) ? $recipientAddress->sender_last_name : old('sender_last_name') }}" />

																@if($errors->has('sender_last_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_last_name') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="sender_street">{{ __('backend/orders.show.form.street') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_street')) is-invalid @endif" id="sender_street" name="sender_street" placeholder="{{ __('backend/orders.show.form.street') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_street) ? $recipientAddress->sender_street : old('sender_street') }}" />

																@if($errors->has('sender_street'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_street') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="sender_zip">{{ __('backend/orders.show.form.zip_code') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_zip')) is-invalid @endif" id="sender_zip" name="sender_zip" placeholder="{{ __('backend/orders.show.form.zip_code') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_zip) ? $recipientAddress->sender_zip : old('sender_zip') }}" />

																@if($errors->has('sender_zip'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_zip') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="sender_city">{{ __('backend/orders.show.form.place') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_city')) is-invalid @endif" id="sender_city" name="sender_city" placeholder="{{ __('backend/orders.show.form.place') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_city) ? $recipientAddress->sender_city : old('sender_city') }}"  />

																@if($errors->has('sender_city'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_city') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="sender_country">{{ __('backend/orders.show.form.country') }}</label>
																<input type="text" class="form-control @if($errors->has('sender_country')) is-invalid @endif" id="sender_country" name="sender_country" placeholder="{{ __('backend/orders.show.form.country') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->sender_country) ? $recipientAddress->sender_country : old('sender_country') }}" />

																@if($errors->has('sender_country'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('sender_country') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="recipient_first_name">{{ __('backend/orders.show.form.first_name') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_first_name')) is-invalid @endif" id="recipient_first_name" name="recipient_first_name" placeholder="{{ __('backend/orders.show.form.first_name') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_first_name) ? $recipientAddress->recipient_first_name : old('recipient_first_name') }}" />

																@if($errors->has('recipient_first_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_first_name') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="recipient_last_name">{{ __('backend/orders.show.form.last_name') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_last_name')) is-invalid @endif" id="recipient_last_name" name="recipient_last_name" placeholder="{{ __('backend/orders.show.form.last_name') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_last_name) ? $recipientAddress->recipient_last_name : old('recipient_last_name') }}" />

																@if($errors->has('recipient_last_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_last_name') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="recipient_street">{{ __('backend/orders.show.form.street') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_street')) is-invalid @endif" id="recipient_street" name="recipient_street" placeholder="{{ __('backend/orders.show.form.street') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_street) ? $recipientAddress->recipient_street : old('recipient_street') }}" />

																@if($errors->has('recipient_street'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_street') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="recipient_zip">{{ __('backend/orders.show.form.zip_code') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_zip')) is-invalid @endif" id="recipient_zip" name="recipient_zip" placeholder="{{ __('backend/orders.show.form.zip_code') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_zip) ? $recipientAddress->recipient_zip : old('recipient_zip') }}" />

																@if($errors->has('recipient_zip'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_zip') }}</strong>
																	</span>
																@endif
															</div>

                                                            <div class="form-group">
																<label for="recipient_city">{{ __('backend/orders.show.form.place') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_city')) is-invalid @endif" id="recipient_city" name="recipient_city" placeholder="{{ __('backend/orders.show.form.place') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_city) ? $recipientAddress->recipient_city : old('recipient_city') }}"  />

																@if($errors->has('recipient_city'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('recipient_city') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="recipientAddress">{{ __('backend/orders.show.form.address') }}</label>
																<input id="recipientAddress" type="text" class="form-control" readonly/>
															</div>
															
															<div class="form-group">
																<label for="recipient_country">{{ __('backend/orders.show.form.country') }}</label>
																<input type="text" class="form-control @if($errors->has('recipient_country')) is-invalid @endif" id="recipient_country" name="recipient_country" placeholder="{{ __('backend/orders.show.form.country') }}" value="{{ (! is_null($recipientAddress) && $recipientAddress->recipient_country) ? $recipientAddress->recipient_country : old('recipient_country') }}" />

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
	var address = document.getElementById('recipientAddress');
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