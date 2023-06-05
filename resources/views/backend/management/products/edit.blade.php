@extends('backend.layouts.default')

@section('content')
                            <div class="k-content__head	k-grid__item">
									<div class="k-content__head-main">
										<h3 class="k-content__head-title">{{ __('backend/management.products.edit.title') }}</h3>
										<div class="k-content__head-breadcrumbs">
											<a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
											<span class="k-content__head-breadcrumb-separator"></span>
											<a href="{{ route('backend-management-products') }}" class="k-content__head-breadcrumb-link">{{ __('backend/management.products.title') }}</a>
										</div>
									</div>
								</div>
								<div class="k-content__body	k-grid__item k-grid__item--fluid">
									<div class="row">
										<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
										@if (!Auth::user()->hasPermission('vendor'))
											@if(!$product->isUnlimited())
											<a href="{{ route('backend-management-product-database', $product->id) }}" class="btn btn-wide btn-bold btn-primary btn-upper" style="margin-bottom:15px">{{ __('backend/management.products.edit.database') }}</a>
											@endif
										@endif
											<a href="{{ route('product-page', $product->id) }}" target="_shop_product_{{ $product->id }}" class="btn btn-wide btn-bold btn-primary btn-upper" style="margin-bottom:15px">{{ __('backend/management.products.show_product') }}</a>
											
											<div class="kt-portlet">
												<div class="kt-portlet__head">
													<div class="kt-portlet__head-label">
														<h3 class="kt-portlet__head-title">{{ __('backend/management.products.edit.title') }}</h3>
													</div>
												</div>
												<form method="post" class="kt-form" action="{{ route('backend-management-product-edit-form') }}" enctype="multipart/form-data">
													@csrf

													<input type="hidden" name="product_edit_id" value="{{ $product->id }}" />
													
													<div class="kt-portlet__body">
														<div class="kt-section kt-section--first">
														<div class="form-group">
																<label for="product_edit_name">{{ __('backend/management.products.name') }}</label>
																<input type="text" class="form-control @if($errors->has('product_edit_name')) is-invalid @endif" id="product_edit_name" name="product_edit_name" placeholder="{{ __('backend/management.products.name') }}" value="{{ $product->name }}" />

																@if($errors->has('product_edit_name'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_name') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="product_edit_short_description">{{ __('backend/management.products.short_description') }}</label>
																<input type="text" class="form-control @if($errors->has('product_edit_short_description')) is-invalid @endif" id="product_edit_short_description" name="product_edit_short_description" placeholder="{{ __('backend/management.products.short_description') }}" value="{{ strlen($product->short_description) > 0 ? $product->short_description : '' }}" />

																@if($errors->has('product_edit_short_description'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_short_description') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="product_edit_category_id">{{ __('backend/management.products.category') }}</label>
																<select name="product_edit_category_id" id="product_edit_category_id" class="form-control @if($errors->has('product_edit_category_id')) is-invalid @endif">
																	<option value="0">{{ __('backend/main.please_choose') }}</option>
																	@foreach(App\Models\ProductCategory::all() as $productCategory)
																		@if (!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor'))
																			@if ($productCategory->slug=='accounts')
																				<option value="{{ $productCategory->id }}" selected>{{ $productCategory->name }}</option>
																			@endif
																		
																		@else
																		<option value="{{ $productCategory->id }}" @if($product->category_id == $productCategory->id) selected @endif>{{ $productCategory->name }}</option>
																		@endif
																	<!-- <option value="{{ $productCategory->id }}" @if($product->category_id == $productCategory->id) selected @endif>{{ $productCategory->name }}</option>	 -->
																	@endforeach
																</select>

																@if($errors->has('product_edit_category_id'))
																	<span class="invalid-feedback" style="display:block;" role="alert">
																		<strong>{{ $errors->first('product_edit_category_id') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="product_edit_description">{{ __('backend/management.products.description') }}</label>
																<textarea class="text-editor form-control @if($errors->has('product_edit_description')) is-invalid @endif" id="product_edit_description" name="product_edit_description" placeholder="{{ __('backend/management.products.description') }}">{{ strlen($product->description) > 0 ? $product->description : '' }}</textarea>

																@if($errors->has('product_edit_description'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_description') }}</strong>
																	</span>
																@endif
															</div>
															
															<div class="form-group">
																<label for="product_edit_price_in_cent">{{ __('backend/management.products.price_in_cent') }}</label>
																<input type="text" class="form-control @if($errors->has('product_edit_price_in_cent')) is-invalid @endif" id="product_edit_price_in_cent" name="product_edit_price_in_cent" placeholder="{{ __('backend/management.products.price_in_cent_example') }}" value="{{ $product->price_in_cent }}" />

																@if($errors->has('product_edit_price_in_cent'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_price_in_cent') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="product_edit_old_price_in_cent">{{ __('backend/management.products.old_price_in_cent') }}</label>
																<input type="text" class="form-control @if($errors->has('product_edit_old_price_in_cent')) is-invalid @endif" id="product_edit_old_price_in_cent" name="product_edit_old_price_in_cent" placeholder="{{ __('backend/management.products.old_price_in_cent_example') }}" value="{{ $product->old_price_in_cent }}" />

																@if($errors->has('product_edit_old_price_in_cent'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_old_price_in_cent') }}</strong>
																	</span>
																@endif
															</div>
															
															<div style="margin-bottom: 5px;">
																<label class="k-checkbox k-checkbox--all k-checkbox--solid">
																	<input type="checkbox" name="product_edit_drop_needed" value="1" data-content-visible="false" data-weight-visible="false" @if($product->dropNeeded()) checked @endif />
																	<span></span>
																	{{ __('backend/management.products.edit.drop_needed') }}
																</label>
															</div>

															<div style="margin-bottom: 5px;">
																<b>{{ __('backend/management.products.edit.options') }}</b>
															</div>
															
															<div class="form-group">
																<label class="k-radio k-radio--all k-radio--solid">
																	<input type="radio" name="product_edit_stock_management" value="normal" data-content-visible="false" data-weight-visible="false" @if(!$product->isUnlimited() && !$product->asWeight()) checked @endif />
																	<span></span>
																	{{ __('backend/management.products.edit.normal_stock_management') }}
																</label>
															</div>
															
															<div class="form-group">
																<label class="k-radio k-radio--all k-radio--solid">
																	<input type="radio" name="product_edit_stock_management" value="weight" data-content-visible="true" data-weight-visible="true" @if($product->asWeight()) checked @endif />
																	<span></span>
																	{{ __('backend/management.products.edit.as_weight') }}
																</label>
															</div>
															
															<div class="form-group">
																<label class="k-radio k-radio--all k-radio--solid">
																	<input type="radio" name="product_edit_stock_management" value="unlimited" data-content-visible="true" data-weight-visible="false" @if($product->isUnlimited()) checked @endif />
																	<span></span>
																	{{ __('backend/management.products.edit.unlimited_available') }}
																</label>
															</div>

															<div class="product_edit_weight_div form-group" style="@if(!$product->asWeight()) display: none; @endif">
																<label for="product_edit_weight">{{ __('backend/management.products.weight') }}</label>
																<input type="number" class="text-editor form-control @if($errors->has('product_edit_weight')) is-invalid @endif" id="product_edit_weight" name="product_edit_weight" placeholder="{{ __('backend/management.products.weight') }}" value="{{ $product->getWeightAvailable() }}" />

																@if($errors->has('product_edit_weight'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_weight') }}</strong>
																	</span>
																@endif
															</div>

															<div class="product_edit_weight_div form-group" style="@if(!$product->asWeight()) display: none; @endif">
																<label for="product_edit_weightchar">{{ __('backend/management.products.weightchar') }}</label>
																<input type="text" class="text-editor form-control @if($errors->has('product_edit_weightchar')) is-invalid @endif" id="product_edit_weightchar" name="product_edit_weightchar" placeholder="{{ __('backend/management.products.weightchar') }}" value="{{ $product->getWeightChar() }}" />

																@if($errors->has('product_edit_weightchar'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_weightchar') }}</strong>
																	</span>
																@endif
															</div>

															<div class="product_edit_content_div form-group" style="@if(!$product->isUnlimited() && !$product->asWeight()) display: none; @endif">
																<label for="product_edit_content">{{ __('backend/management.products.content') }}</label>
																<textarea class="text-editor form-control @if($errors->has('product_edit_content')) is-invalid @endif" id="product_edit_content" name="product_edit_content" placeholder="{{ __('backend/management.products.content') }}">{{ strlen($product->content) > 0 ? $product->content : '' }}</textarea>

																@if($errors->has('product_edit_content'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_content') }}</strong>
																	</span>
																@endif
															</div>

															<div class="form-group">
																<label for="product_edit_benifits">Product benifits</label>
																<textarea rows="10" class="form-control @if($errors->has('product_edit_benifits')) is-invalid @endif" id="product_edit_benifits" name="product_edit_benifits" placeholder="Enter product benifits eachc one in new line">{{ $benifits }}</textarea>

																@if($errors->has('product_edit_benifits'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_benifits') }}</strong>
																	</span>
																@endif
															</div>
                                                            
                                                            <div style="margin-bottom: 5px;">
																<label class="k-checkbox k-checkbox--all k-checkbox--solid">
																	<input type="checkbox" name="product_edit_show_stock" value="1" data-content-visible="false" data-weight-visible="false" @if($product->isShowStockEnabled()) checked @endif />
																	<span></span>
																	Show available stock
																</label>
															</div>

															<div class="product_edit_icon form-group">
																<label for="product_edit_icon">Icon</label>
																<input type="file" class="form-control mb-2 @if($errors->has('product_edit_icon')) is-invalid @endif" id="product_edit_icon" name="product_edit_icon"/>

																@if($errors->has('product_edit_icon'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_icon') }}</strong>
																	</span>
																@endif

																<div style="margin-bottom: 5px;">
																	<label class="k-checkbox k-checkbox--all k-checkbox--solid">
																		<input type="checkbox" name="product_edit_delete_icon" value="1" data-content-visible="false" data-weight-visible="false" @if(is_null($product->getIcon())) checked @endif />
																		<span></span>
																		Use default icon
																	</label>
																</div>
															</div>
															
															@if($product->isDigitalGoods())
															<div class="product_edit_order_minimum form-group">
																<label for="product_edit_order_minimum">Order minimum</label>
																<input type="number" class="form-control mb-2 @if($errors->has('product_edit_order_minimum')) is-invalid @endif" id="product_edit_order_minimum" name="product_edit_order_minimum" placeholder="Enter number of minimum order items" value="{{ $product->getOrderMinimum() }}" />

																@if($errors->has('product_edit_order_minimum'))
																	<span class="invalid-feedback" style="display:block" role="alert">
																		<strong>{{ $errors->first('product_edit_order_minimum') }}</strong>
																	</span>
																@endif
															</div>
															@endif
														</div>
													</div>
													<div class="kt-portlet__foot">
														<div class="kt-form__actions">
															<button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.products.edit.submit_button') }}</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
@endsection

@section('page_scripts')
<script type="text/javascript">
	$(function() {
		$('textarea.text-editor').froalaEditor({
			toolbarSticky: false,
			language: 'de',
      		theme: 'gray',
			toolbarButtons: ['undo', 'redo' , '|', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'align', '|', 'fontFamily', 'fontSize', 'color', '|', 'emoticons', '|', 'insertLink', 'insertImage', '|', 'outdent', 'indent', 'clearFormatting', 'insertTable', 'html']
		});

		$('input[data-content-visible]').change(function() {
			if($(this).attr('data-content-visible') == 'true' && $(this).is(':checked')) {
				$('.product_edit_content_div').show();
			} else {
				$('.product_edit_content_div').hide();
			}
		});

		$('input[data-weight-visible]').change(function() {
			if($(this).attr('data-weight-visible') == 'true' && $(this).is(':checked')) {
				$('.product_edit_weight_div').show();
			} else {
				$('.product_edit_weight_div').hide();
			}
		});
  	});
</script>
@endsection
