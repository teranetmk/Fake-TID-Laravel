@extends('backend.layouts.default')

@section('content')
	<div class="k-content__head	k-grid__item">
		<div class="k-content__head-main">
			<h3 class="k-content__head-title">{{ __('backend/management.products.database.title') }}</h3>
			<div class="k-content__head-breadcrumbs">
				<a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
				<span class="k-content__head-breadcrumb-separator"></span>
				<a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
				<span class="k-content__head-breadcrumb-separator"></span>
				<a href="{{ route('backend-management-address-database') }}" class="k-content__head-breadcrumb-link">{{ __('backend/management.adressen_generator.title') }}</a>
			</div>
		</div>
	</div>

	<div class="k-content__body	k-grid__item k-grid__item--fluid">
		<div class="row">
			<div class="col-lg-12 col-xl-4 order-lg-1 order-xl-1">
				<div class="k-portlet k-portlet--height-fluid">
					<div class="k-portlet__head  k-portlet__head--noborder">
						<div class="k-portlet__head-label">
							<h3 class="k-portlet__head-title">{{ __('backend/management.products.database.widget1.title') }}</h3>
						</div>
					</div>
					<div class="k-portlet__body k-portlet__body--fluid">
						<div class="k-widget-20">
							<div class="k-widget-20__title">
								<div class="k-widget-20__label">{{ $count }}</div>
								<img class="k-widget-20__bg" src="{{ asset_dir('admin/assets/media/misc/iconbox_bg.png') }}" alt="bg" />
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-12 col-xl-8 order-lg-1 order-xl-1">
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
				<div class="k-portlet k-portlet--height-fluid">
					<div class="k-portlet__head">
						<div class="k-portlet__head-label">
							<h3 class="k-portlet__head-title">{{ __('backend/management.products.database.title') }}</h3>
						</div>
					</div>
					<div class="k-portlet__body k-portlet__body--fluid">
						<form method="POST" action="{{ route('backend-management-address-database-import') }}" style="width: 100%;">
							@csrf
							<div class="form-group" style="width: 100%;">
								<label for="import_one_content">Adressen</label>
								<textarea rows="20" style="width: 100%;" class="form-control @if($errors->has('addresses')) is-invalid @endif" name="addresses" id="addresses" placeholder="Adressen">{{ old('addresses', $addressesInline) }}</textarea>

								@if($errors->has('addresses'))
									<span class="invalid-feedback" style="display:block" role="alert">
										<strong>{{ $errors->first('addresses') }}</strong>
									</span>
								@endif
							</div>

							<div class="form-group">
								<input type="submit" class="btn btn-wide btn-bold btn-danger" value="{{ __('backend/management.products.database.import.submit_button') }}" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('page_scripts')

@endsection