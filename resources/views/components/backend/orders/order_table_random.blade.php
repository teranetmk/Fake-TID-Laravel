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
                                <th>{{ __('backend/orders.table.tracking_number') }}</th>
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
                                    <td>
                                        {{ $order->tracking_number }}
                                    </td>
                                    {{--																	<td>--}}
                                    {{--																		@if(strlen($order->drop_info) > 0)--}}
                                    {{--																			{!! nl2br(e($order->drop_info)) !!}--}}
                                    {{--																		@endif--}}
                                    {{--																	</td>--}}
                                    <td>
                                        @if($order->getStatus() == 'cancelled')
                                            {{ __('backend/orders.status.cancelled') }}
                                        @elseif($order->getStatus() == 'completed')
                                            {{ __('backend/orders.status.completed') }}
                                        @elseif($order->getStatus() == 'pending')
                                            {{ __('backend/orders.status.pending') }}
                                        @endif
                                    </td>
                                    <td style="font-size: 20px;">
                                        <?php
                                            $edit_btn = $order->random_shipping_address;
                                        ?>
                                        @if($edit_btn && ! is_null($order->random_tid) && Storage::disk('public')->exists("order/{$order->id}/{$order->random_tid}"))
                                        <a href="{{ route('backend-orders-downloadTidFile-random', $order->id) }}"
                                           data-toggle="tooltip {{ $order->random_tid }}"
                                           data-original-title="{{ __('backend/orders.download') }}"><i
                                                class="la la-download"></i></a>
                                        @endif

                                        <a href="{{ route('backend-order-id', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.view') }}"><i
                                                class="la la-eye"></i></a>
                                        <a href="{{ route('backend-order-complete', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.complete') }}"><i
                                                class="la la-check"></i></a>
                                        <a href="{{ route('backend-order-edit-random', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.edit') }}"><i
                                                class="la la-edit @if(! is_null($edit_btn) && $edit_btn->recipient_first_name) text-success @else text-danger @endif"></i></a>
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
