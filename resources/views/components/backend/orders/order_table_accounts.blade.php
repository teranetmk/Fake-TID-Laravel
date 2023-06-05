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
                                    <td style="font-size: 20px;">
                                        <?php
                                            $edit_btn = $order->shipping_address;
                                        ?>
                                        @if(! is_null($order->tids) && $edit_btn && Storage::disk('public')->exists("order/{$order->id}/{$order->tids->tid}"))
                                        <a href="{{ route('backend-orders-downloadTidFile', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.download') }}"><i
                                                class="la la-download"></i></a>
                                        @endif

                                        <a href="{{ route('backend-order-id', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.view') }}"><i
                                                class="la la-eye"></i></a>
                                        {{--
                                        <a href="{{ route('backend-order-cancel', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.cancel') }}"><i
                                                class="la la-close"></i></a>
                                        <a href="{{ route('backend-order-delete', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.delete') }}"
                                           onClick="return confirm('Delete ?')"><i class="la la-trash"></i></a>
                                           --}}
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
