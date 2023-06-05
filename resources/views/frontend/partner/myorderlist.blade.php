@extends('frontend.layouts.dashboard')

@section('content')
    
    <div class="py-4 px-3 px-md-4">
	<nav class="d-none d-md-block" aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="#">Dashboard</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">{{ __('backend/orders.title') }}</li>
		</ol>
	</nav>
    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/orders.title') }}</h3>
        </div>
    </div>


    <div class="k-content__body	k-grid__item k-grid__item--fluid">


        

        <!-- Tab panes -->
        <div class="row">
    <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="kt-section kt-section--first">

                    @if(count($ordersAccounts))
                        <table class="table table-head-noborder">

                            <tr>
                                <th>{{ __('backend/orders.table.id') }}</th>
                                <th>{{ __('backend/orders.table.product') }}</th>
                                <th>{{ __('backend/management.products.price') }}</th>
                                <th>{{ __('backend/orders.table.user') }}</th>
                                <th>{{ __('backend/orders.table.date_of_order') }}</th> 
                                <th>VERSANDLABEL</th>
                                <th>{{ __('backend/orders.table.actions') }}</th>
                            </tr>

                            @foreach($ordersAccounts as $order)
                                <tr>
                                    <th scope="row">{{ $order->id }}</th>
                                    <td>{{ $order->name }}</td>
                                    <td>{{$order->getFormattedTotalPrice()}}</td>
                                    <td>
                                        {{ $order->getUser()->username }}
                                    </td>
                                    <td>
                                        {{ date('d.m.Y', strtotime($order->created_at)) }}
                                    </td>
                                    <td>{{$order->qrcode}}</td>
                                    <td style="font-size: 20px;">
                                        

                                        <a href="{{ route('myorder-detail', $order->id) }}"
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('backend/orders.view') }}"><i
                                                class="gd-eye"></i></a>
                                        
                                    </td>
                                </tr>
                            @endforeach

                        </table>

                        {!! str_replace(request()->server('SERVER_ADDR'), "fake-tids.su",  $ordersAccounts->links()) !!}


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

</div>
                                        </div>

   

@endsection

@section('page_scripts')


    <script>

        $(document).ready(function () {

            $('.daterangepicker_from').change(function () {
                var from = new Date($(this).val());
                var to = $('.daterangepicker_to').val();

                if (to !== '') {
                    from = new Date($('.daterangepicker_to').val());
                    if (from > to) $(this).val('');
                }

                var dd = from.getDate();
                var mm = from.getMonth() + 1; //January is 0!
                var yyyy = from.getFullYear();

                if (dd < 50) dd = '0' + dd;
                if (mm < 50) mm = '0' + mm;
                from = yyyy + '-' + mm + '-' + dd;

                $('.daterangepicker_to').prop('min', from);
            });


            $('.daterangepicker_to').change(function () {
                var from = $('.daterangepicker_from').val();
                var to = new Date($(this).val());

                if (from !== '') {
                    from = new Date($('.daterangepicker_from').val());

                    if (from > to)
                        $(this).val('');

                }

                var dd = to.getDate();
                var mm = to.getMonth() + 1; //January is 0!
                var yyyy = to.getFullYear();

                if (dd < 50) dd = '0' + dd;
                if (mm < 50) mm = '0' + mm;

                to = yyyy + '-' + mm + '-' + dd;

                $('.daterangepicker_from').prop('max', to);
            });



            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
            var activeTab = localStorage.getItem('activeTab');

            if (activeTab) {
                $('.nav-item a[href="' + activeTab + '"]').tab('show');
            }

            // $('.nav-tabs a').click(function () {
            $('a[data-toggle="tab"]').click(function () {
                $(this).tab('show');
            });


        });

    $("#checkbox-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
        (this.checked) ? $("#bulk-action-btn").show():$("#bulk-action-btn").hide();

    });
    $(".checkArray").click(function () {
        var checkedNum = $('input[name="order-ids[]"]:checked').length;
        (!checkedNum) ? $("#bulk-action-btn").hide():$("#bulk-action-btn").show();

    });   
    function submitForm() {
        var checkedNum = $('input[name="order-ids[]"]:checked').length;
        if (!checkedNum) {
            alert('Please check at least one');
        } else {
            $("#statusChangeForm").modal('toggle');
            var val = [];
            $('input[type="checkbox"]:checked').each(function(i) {
                val[i] = $(this).val();
            });
           
            $("#order_ids_input").val(val);
        }
    }

    </script>

@endsection
