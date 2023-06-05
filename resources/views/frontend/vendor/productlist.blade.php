@extends('frontend.layouts.dashboard')

@section('content')

   
    <nav class="d-none d-md-block" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('backend/management.title') }}</li>
        </ol>
    </nav>
    <div class="mb-3 mb-md-4 d-flex justify-content-between">
        <div class="h3 mb-0">{{ __('backend/management.products.title') }}</div>
    </div>
    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
                <a href="{{ route('backend-management-product-add') }}"
                   class="btn btn-wide btn-bold btn-primary btn-upper"
                   style="margin-bottom:15px">{{ __('backend/main.add') }}</a>

                <div class="kt-portlet">
                    <div class="kt-portlet__body">
                        <div class="kt-section kt-section--first">
                            @if(count($products))
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ __('backend/management.products.id') }}</th>
                                        <th>{{ __('backend/management.products.name') }}</th>
                                        <th>{{ __('backend/management.products.category') }}</th>
                                        <th>{{ __('backend/management.products.price') }}</th>
                                        <th>{{ __('backend/management.products.stock_status') }}</th>
                                        <th>{{ __('backend/management.products.sells') }}</th>

                                        <!-- <th>{{ __('backend/management.products.tids_count') }}</th> -->

                                        <th>{{ __('backend/management.products.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <th scope="row">{{ $product->id }}</th>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                @if($product->getCategory()->slug == 'uncategorized')
                                                    {{ $product->getCategory()->name }}
                                                @else
                                                  {{ $product->getCategory()->name }}
                                                @endif
                                            </td>
                                            <td>{{ $product->getFormattedPrice() }}</td>

                                            <td>
                                                @if($product->isUnlimited())
                                                    {{ __('backend/management.products.unlimited') }}
                                                @elseif($product->asWeight())
                                                    {{ __('backend/management.products.weight_available', [
                                                        'weight' => $product->getWeightAvailable(),
                                                        'char' => $product->getWeightChar()
                                                    ]) }}
                                                @else
                                                    @if($product->inStock())
                                                        {{ $product->getStock() }}
                                                    @else
                                                        {{ __('backend/management.products.sold_out') }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $product->getSells() }}@if($product->asWeight()){{ $product->getWeightChar() }}@endif
                                            </td>
                                            <!-- <td>{{ $product->tids_count }}</td> -->
                                            <td style="font-size: 20px;">
                                                
                                                @if(!$product->isUnlimited() && !$product->asWeight() )
                                                    <a href="{{ route('backend-management-product-database', $product->id) }}"
                                                       data-toggle="tooltip"
                                                       data-original-title="{{ __('backend/main.tooltips.database') }}"><i
                                                            class="gd-layout-menu-v"></i></a>
                                                @endif
                                                <a href="{{ route('backend-management-product-edit', $product->id) }}"
                                                   data-toggle="tooltip"
                                                   data-original-title="{{ __('backend/main.tooltips.edit') }}"><i
                                                        class="gd-pencil-alt"></i></a>
                                                <a href="{{ route('backend-management-product-delete', $product->id) }}"
                                                   data-toggle="tooltip"
                                                   data-original-title="{{ __('backend/main.tooltips.delete') }}"><i
                                                        class="gd-trash" style="color:red;"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <i>{{ __('backend/main.no_entries') }}</i>
                            @endif

                            {!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.su", $products->links()) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')

@endsection
