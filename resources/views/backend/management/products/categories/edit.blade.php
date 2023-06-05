@extends('backend.layouts.default')

@section('content')
								<div class="k-content__head	k-grid__item">
									<div class="k-content__head-main">
										<h3 class="k-content__head-title">{{ __('backend/management.products.categories.edit.title') }}</h3>
										<div class="k-content__head-breadcrumbs">
											<a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="{{ route('backend-management-products') }}" class="k-content__head-breadcrumb-link">{{ __('backend/management.products.title') }}</a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="{{ route('backend-management-products-categories') }}" class="k-content__head-breadcrumb-link">{{ __('backend/management.products.categories.title') }}</a>
										</div>
									</div>
								</div>
								<div class="k-content__body	k-grid__item k-grid__item--fluid">
									<div class="row">
										<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
										<div class="kt-portlet">
												<div class="kt-portlet__head">
													<div class="kt-portlet__head-label">
														<h3 class="kt-portlet__head-title">{{ __('backend/management.products.categories.edit.title') }}</h3>
													</div>
												</div>
												<form method="post" class="kt-form" action="{{ route('backend-management-product-category-edit-form') }}">
													@csrf

													<input type="hidden" name="product_category_edit_id" value="{{ $productCategory->id }}" />
													
													<div class="kt-portlet__body">
														<div class="kt-section kt-section--first">
															<div class="form-group">
																<label for="product_category_edit_name">{{ __('backend/management.products.categories.name') }}</label>
																<input type="text" class="form-control @if($errors->has('product_category_edit_name')) is-invalid @endif" id="product_category_edit_name" name="product_category_edit_name" placeholder="{{ __('backend/management.products.categories.name') }}" value="{{ $productCategory->name }}" />

																@if($errors->has('product_category_edit_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_category_edit_name') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="product_category_edit_slug">{{ __('backend/management.products.categories.slug') }}</label>
																<input type="text" class="form-control @if($errors->has('product_category_edit_slug')) is-invalid @endif" id="product_category_edit_slug" name="product_category_edit_slug" placeholder="{{ __('backend/management.products.categories.slug') }}" value="{{ $productCategory->slug }}" />

																@if($errors->has('product_category_edit_slug'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_category_edit_slug') }}</strong>
																	</span>
																@endif
															</div>
															<div class="form-group">
																<label for="product_add_category_id">Show In Shop</label>
																<select name="is_show" class="form-control @if($errors->has('is_show')) is-invalid @endif">
																
																	
																		
																			
																	<option value="yes" @if($productCategory->is_show == 'yes') selected @endif>Yes</option>
																	<option value="no" @if($productCategory->is_show == 'no') selected @endif>No</option>	
																</select>

																
															</div>
															<div style="margin-bottom: 5px;">
																<label class="k-checkbox k-checkbox--all k-checkbox--solid">
																	<input type="checkbox" name="product_category_edit_is_digital_goods" value="1" data-content-visible="false" data-weight-visible="false" @if($productCategory->isDigitalGoods()) checked @endif/>
																	<span></span>
																	Set as digital goods category
																</label>

																@if($errors->has('product_category_edit_is_digital_goods'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_category_edit_is_digital_goods') }}</strong>
																	</span>
																@endif
															</div>
														</div>
													</div>
													<div class="kt-portlet__foot">
														<div class="kt-form__actions">
															<button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.products.categories.edit.submit_button') }}</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
@endsection

@section('page_scripts')

@endsection