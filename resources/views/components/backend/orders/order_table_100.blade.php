<div class="row">
    <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="kt-section kt-section--first">

                    @if(count($orders))
                        <table class="table table-head-noborder">

                            <tr>
                                <th class="text-center pt-3">
                                    <div class="custom-checkbox custom-checkbox-table custom-control">
                                        <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
                                        <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                    </div>
                                </th>
                                <th>{{ __('backend/orders.table.id') }}</th>
                                <th>{{ __('backend/orders.table.product') }}</th>
                                <th>{{ __('backend/orders.table.user') }}</th>
                                <th>{{ __('backend/orders.table.date_of_order') }}</th>
                                <th>{{ __('backend/orders.table.date_of_delivery') }}</th>
                                <th>{{ __('backend/orders.table.delivery_method') }}</th>
                                <th>{{ __('backend/orders.table.tid') }}</th>
                                <th>{{ __('backend/orders.table.status') }}</th>
                                <th>{{ __('backend/orders.table.actions') }}</th>
                            </tr>

                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-center pt-2">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" name="order-ids[]" value="{{ $order->id }}" data-checkboxes="mygroup"
                                                class="custom-control-input checkArray" id="checkbox-{{ $order->id }}">
                                            <label for="checkbox-{{ $order->id }}" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <th scope="row">{{ $order->id }}</th>
                                    <td>{{ $order->products->name }}</td>
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
                                        @if($order->delivery_name)
                                            {{-- {{ $order->delivery_method }} --}}
                                            {{ $order->delivery_name }}
                                        @endif
                                    </td>
                                    {{--																	<td>--}}
                                    {{--																		@if(strlen($order->drop_info) > 0)--}}
                                    {{--																			{!! nl2br(e($order->drop_info)) !!}--}}
                                    {{--																		@endif--}}
                                    {{--																	</td>--}}
                                    <td>
                                        {{$order->label_code ? $order->label_code : '-'}}
                                    </td>
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

                                        @if ($order->hasPdf())
                                         <a href="{{ route( 'backend-orders-downloadTidFile-manual', $order->id ) }}"
                                            data-toggle="tooltip"
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
                                        @if($order->label_code )
                                           <a href="https://www.dhl.de/de/privatkunden.html?piececode={{ $order->label_code }}"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('backend/orders.show.track') }}"
                                            target="_blank"><i class="las la-box"></i></a>
                                        @endif
                                </tr>
                            @endforeach

                        </table>
                         {!! $orders->links() !!}
                        <!-- {!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.to",  $orders->links()) !!} -->

                    @else
                        <i>{{ __('backend/main.no_entries') }}</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


