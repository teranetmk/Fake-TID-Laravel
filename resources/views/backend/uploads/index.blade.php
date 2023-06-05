@extends('backend.layouts.default')

@section('content')

    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/tids.title') }}</h3>
            <div class="k-content__head-breadcrumbs">
                <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
                <span class="k-content__head-breadcrumb-separator"></span>
                <a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/tids.title') }}</a>
            </div>
        </div>
    </div>

    <div class="k-content__body	k-grid__item k-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                <a href="{{ route('admin.uploads.create') }}"
                   class="btn btn-wide btn-bold btn-primary btn-upper"
                   style="margin-bottom:15px">{{ __('backend/tids.title') }}</a>

                <div class="kt-portlet">
                    <div class="kt-portlet__body">
                        <div class="kt-section kt-section--first">

                            @if( count($tids) )

                                <table class="table table-head-noborder">

                                    <tr>
                                        <th>{{ __('backend/tids.table.id') }}</th>
                                        <th>{{ __('backend/tids.table.product_id') }}</th>
                                        <th>{{ __('backend/tids.table.tid') }}</th>
                                        <th>{{ __('backend/tids.table.used') }}</th>
                                        <th>{{ __('backend/tids.table.actions') }}</th>
                                    </tr>

                                    @foreach( $tids as $tid )

                                        <tr>
                                            <th scope="row">{{ $tid->id }}</th>
                                            <td>{{ $tid->product_id }}</td>
                                            <td>{{ $tid->tid_name }}</td>
                                            <td>{{ $tid->used_name }}</td>
                                            <td style="font-size: 20px;">
                                                <a href="{{ route('admin.uploads.download', $tid->id) }}"
                                                   data-toggle="tooltip"
                                                   data-original-title="{{ __('backend/tids.tooltips.download') }}"><i
                                                        class="la la-download"></i></a>

                                                @if( $tid->used == 0 )
                                                    <a href="{{ route('admin.uploads.destroy', $tid->id) }}"
                                                       data-toggle="tooltip"
                                                       data-original-title="{{ __('backend/tids.tooltips.delete') }}"
                                                       onClick="return confirm('Delete ?')"><i class="la la-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>

                                    @endforeach

                                </table>

                            @else
                                <i>{{ __('backend/main.no_entries') }}</i>
                            @endif

                            {!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.su", preg_replace('/' . $tids->currentPage() . '\?page=/', '', $tids->links())) !!}

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
