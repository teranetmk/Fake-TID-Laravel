<div class="row">
    <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="kt-section kt-section--first">

                    @if(count($orders))
                        <table class="table table-head-noborder">

                            <tr>
                                <th>{{ __('backend/orders.table.id') }}</th>
                                <th>{{ __('backend/orders.table.product') }}</th>
                                <th>{{ __('backend/orders.table.user') }}</th>
                                <th>{{ __('backend/orders.table.date_of_order') }}</th> 
                                <th>{{ __('backend/orders.table.date_of_delivery') }}</th>
                                <th>Payment Betrag</th>
                                <th>{{ __('backend/orders.table.status') }}</th>
                                <th>{{ __('backend/orders.table.actions') }}</th>
                            </tr>

                            @foreach($orders as $order)
                                <tr>
                                    <th scope="row">{{ $order->id }}</th>
                                    <td>{{ $order->name }}</td>
                                    <td>
                                        {{ $order->getUser()->username }}
                                    </td>
                                    <td>
                                        {{ date('d.m.Y', strtotime($order->created_at)) }}
                                    </td>
                                    <td>
                                        {{ date('d.m.Y', strtotime($order->deliver_at)) }}
                                    </td>
                                    <td>{{ $order->product_payment_amount ?? 0 }}€</td>
                                    <td>
                                        {{ __(sprintf('backend/orders.status.%s', $order->getStatus() ?? 'pending')) }}
                                    </td>
                                    <td style="font-size: 20px;">
                                        <a href="{{ route('backend-order-id', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.view') }}"><i
                                                class="la la-eye"></i></a>
                                        <a href="{{ route('backend-order-complete', $order->id) }}"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('backend/orders.complete') }}"><i
                                                    class="la la-check"></i></a>
                                        @if(! is_null($order->tid) )
                                        <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->tid }}"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('backend/orders.show.track') }}"
                                            target="_blank"><i class="las la-box"></i></a>
                                        @endif

                                        @if($order->boxing_status == 'package_was_accepted')
                                        <i class="fa-solid fa-box text-success"></i>
                                        @elseif($order->boxing_status == 'package_was_refused')
                                        <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                        @endif
                                        
                                        @if($order->replace_status == 'replace_has_been_paid')
                                        <i class="fa-brands fa-btc text-success"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </table>

                        {!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.su",  $orders->links()) !!}


{{--                    {{ dd( $orders->currentPage(), $orders->links(), preg_replace('/' . $orders->currentPage() . '\?page=/', '', $orders->links()) ) }}--}}


{{--                        {!! preg_replace('/' . $orders->currentPage() . '\?page=/', '', $orders->links()) !!}--}}
                    @else
                        <i>{{ __('backend/main.no_entries') }}</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>