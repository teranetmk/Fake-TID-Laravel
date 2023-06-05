@extends('backend.layouts.default')

@section('content')
<div class="k-content__head	k-grid__item">
    <div class="k-content__head-main">
        <h3 class="k-content__head-title">{{ __('backend/employees.title') }}</h3>
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
            <a href="{{ route('admin.employees.create') }}" class="btn btn-wide btn-bold btn-primary btn-upper" style="margin-bottom:15px">{{ __('backend/main.add') }}</a>

            <div class="kt-portlet">
                <div class="kt-portlet__body">
                    <div class="kt-section kt-section--first">
                        @if($employees->count() > 0)
                        <table class="table table-head-noborder">
                            <thead>
                                <tr>
                                    <th>{{ __('backend/employees.id') }}</th>
                                    <th>{{ __('backend/employees.name') }}</th>
                                    <th>{{ __('backend/employees.products') }}</th>
                                    {{-- <th>{{ __('backend/employees.full_link') }}</th> --}}
                                    {{-- <th>{{ __('backend/employees.last7days_visits') }}</th> --}}
                                    {{-- <th>{{ __('backend/employees.last30days_visits') }}</th> --}}
                                    {{-- <th>{{ __('backend/employees.visits') }}</th> --}}
                                    <th>{{ __('backend/employees.date') }}</th>
                                    <th>{{ __('backend/employees.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td scope="row">{{ $employee->getId() }}</td>
                                    <td>{{ $employee->getName() }}</td>
                                    <td>
                                        <ul>
                                        @foreach ($employee->products as $product)
                                            <li>{{ $product->name }}</li>
                                        @endforeach
                                        </ul>
                                    </td>
                                    {{-- <td>
                                        <a href="{{ $employee->forHomePage() ? route('home_page') : route('shortlink', ['code' => $employee->getCode()])  }}" target="_blank">{{ $employee->forHomePage() ? config('app.name') : $employee->getLink() }}</a>
                                    </td> --}}
                                    {{-- <td>{{ $employee->analytics->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))->sum('views') }}</td> --}}
                                    {{-- <td>{{ $employee->analytics->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->sum('views') }}</td> --}}
                                    {{-- <td>{{ $employee->analytics->sum('views') }}</td> --}}
                                    <td>{{ $employee->getCreatedAt()->format('d.m.Y H:i') }}</td>
                                    <td style="font-size: 20px;">
                                        <a href="{{ route('admin.employees.edit', ['id' => $employee->getId()]) }}"><i class="la la-pen"></i></a>
                                        <a href="{{ route('admin.employees.delete', ['id' => $employee->getId()]) }}" onclick="return confirm('Are you sure you want to delete this employee?')"><i class="la la-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! preg_replace('/' . $employees->currentPage() . '\?page=/', '', $employees->links()) !!}
                        @else
                        <i>{{ __('backend/main.no_entries') }}</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection