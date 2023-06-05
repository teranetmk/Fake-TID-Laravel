@extends('backend.layouts.default')

@section('content')
<div class="k-content__head	k-grid__item">
    <div class="k-content__head-main">
        <h3 class="k-content__head-title">{{ __('backend/shortlinks.add.title') }}</h3>
        <div class="k-content__head-breadcrumbs">
            <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
            <span class="k-content__head-breadcrumb-separator"></span>
            <a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
            <span class="k-content__head-breadcrumb-separator"></span>
            <a href="{{ route('admin.shortlinks') }}" class="k-content__head-breadcrumb-link">{{ __('backend/shortlinks.title') }}</a>
        </div>
    </div>
</div>
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">{{ __('backend/shortlinks.add.title') }}</h3>
                    </div>
                </div>
                <form method="post" class="kt-form" action="{{ route('admin.shortlinks.post') }}">
                    @csrf
                    <div class="kt-portlet__body">
                        <div class="kt-section kt-section--first">
                            <div class="form-group">
                                <label for="name">{{ __('backend/shortlinks.name') }}</label>
                                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" id="name" name="name" placeholder="{{ __('backend/shortlinks.name') }}" value="{{ old('name') }}" />

                                @if($errors->has('name'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label for="link">{{ __('backend/shortlinks.full_link') }}</label>
                                <input type="url" class="form-control @if($errors->has('link')) is-invalid @endif" id="link" name="link" placeholder="{{ __('backend/shortlinks.full_link') }}" value="{{ old('link') }}" />
                                <span class="text-muted text-sm">{{ __('backend/shortlinks.to_home_page') }}</span>

                                @if($errors->has('link'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
                                        <strong>{{ $errors->first('link') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="code">{{ __('backend/shortlinks.code') }}</label>
                                <input type="text" class="form-control @if($errors->has('code')) is-invalid @endif" id="code" name="code" placeholder="{{ __('backend/shortlinks.code') }}" value="{{ old('code') }}" />
                                <span class="text-muted text-sm">{{ __('backend/shortlinks.code_empty') }}</span>

                                @if($errors->has('code'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/management.faqs.add.submit_button') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection