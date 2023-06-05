@extends('backend.layouts.default')

@section('content')
<div class="k-content__head	k-grid__item">
    <div class="k-content__head-main">
        <h3 class="k-content__head-title">{{ __('backend/shortlinks.title') }}</h3>
        <div class="k-content__head-breadcrumbs">
            <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
            <span class="k-content__head-breadcrumb-separator"></span>
            <a href="javascript:;" class="k-content__head-breadcrumb-link">{{ __('backend/management.title') }}</a>
        </div>
    </div>
</div>
<div class="k-content__body	k-grid__item k-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
            <a href="{{ route('admin.shortlinks.create') }}" class="btn btn-wide btn-bold btn-primary btn-upper" style="margin-bottom:15px">{{ __('backend/main.add') }}</a>

            <div class="kt-portlet">
                <div class="kt-portlet__body">
                    <div class="kt-section kt-section--first">
                        @if($shortLinks->count() > 0)
                        <table class="table table-head-noborder">
                            <thead>
                                <tr>
                                    <th>{{ __('backend/shortlinks.id') }}</th>
                                    <th>{{ __('backend/shortlinks.name') }}</th>
                                    <th>{{ __('backend/shortlinks.short_link') }}</th>
                                    <th>{{ __('backend/shortlinks.full_link') }}</th>
                                    <th>{{ __('backend/shortlinks.last7days_visits') }}</th>
                                    <th>{{ __('backend/shortlinks.last30days_visits') }}</th>
                                    <th>{{ __('backend/shortlinks.visits') }}</th>
                                    <th>{{ __('backend/shortlinks.date') }}</th>
                                    <th>{{ __('backend/shortlinks.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shortLinks as $shortLink)
                                <tr>
                                    <td scope="row">{{ $shortLink->getId() }}</td>
                                    <td>{{ $shortLink->getName() }}</td>
                                    <td>
                                        <a href="{{ route('shortlink', ['code' => $shortLink->getCode()]) }}" target="_blank">{{ $shortLink->getCode() }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ $shortLink->forHomePage() ? route('home_page') : route('shortlink', ['code' => $shortLink->getCode()])  }}" target="_blank">{{ $shortLink->forHomePage() ? config('app.name') : $shortLink->getLink() }}</a>
                                    </td>
                                    <td>{{ $shortLink->analytics->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))->sum('views') }}</td>
                                    <td>{{ $shortLink->analytics->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->sum('views') }}</td>
                                    <td>{{ $shortLink->analytics->sum('views') }}</td>
                                    <td>{{ $shortLink->getCreatedAt()->format('d.m.Y H:i') }}</td>
                                    <td style="font-size: 20px;">
                                        <a href="#" onclick="copyShortLink('{{ route('shortlink', ['code' => $shortLink->getCode()]) }}')" title="Copy short link"><i class="la la-copy"></i></a>
                                        <a href="{{ route('admin.shortlinks.edit', ['id' => $shortLink->getId()]) }}"><i class="la la-pen"></i></a>
                                        <a href="{{ route('admin.shortlinks.delete', ['id' => $shortLink->getId()]) }}" onclick="return confirm('Are you sure you want to delete this short link?')"><i class="la la-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! preg_replace('/' . $shortLinks->currentPage() . '\?page=/', '', $shortLinks->links()) !!}
                        @else
                        <i>{{ __('backend/main.no_entries') }}</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function copyShortLink(link) {
        let el = document.createElement('textarea');
        el.value = link;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
</script>
@endsection