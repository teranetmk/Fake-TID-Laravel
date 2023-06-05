@extends('frontend.layouts.dashboard')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">

                @if(count($user_transactions))
                   
                    @component('components.frontend.box')

                        @slot('title'){{ __('frontend/user.transactions_history') }}@endslot

                        <div class="table-responsive">
                            <table class="table table-transactions table-striped" id="transactionTable">

                                <tr>
                                    <th scope="col">{{ __('frontend/user.transactions_page.id') }}</th>
                                    <th scope="col">{{ __('frontend/user.transactions_page.wallet') }}</th>
                                    <th scope="col">{{ __('frontend/user.transactions_page.status') }}</th>
                                    <th scope="col">{{ __('frontend/user.transactions_page.amount') }}</th>
                                    <th scope="col">{{ __('frontend/user.transactions_page.date') }}</th>
                                    <th scope="col">{{ __('frontend/user.transactions_page.actions') }}</th>
                                </tr>

                                @foreach($user_transactions as $transaction)
                                    <tr class="@if($transaction->isPending()) bg-light @elseif($transaction->isWaiting()) bg-light-2 @endif">
                                        <th scope="row">#{{ $transaction->id }}</th>
										
                                        <td><span id="transactionId" style="display:none;">{{ $transaction->id }}</span>{{ strlen($transaction->wallet) ? $transaction->wallet : '' }}</td>
                                     
                                        <td>
                                            @if($transaction->isPaid())
                                                <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.confirmed') }}</span>											
                                                <span class="label label-success" id="show-data">
                                                {{ __('frontend/user.transactions_page.confirmed') }}
                                            </span>
                                            @elseif($transaction->isPending()) 
                                             <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.confirmations') }}</span>
                                                <span class="label label-warning show-data" id="show-data"> 
												
                                                {{ __('frontend/user.transactions_page.confirmations', [
                                                    'confirms' => $transaction->confirmations,
                                                    'confirms_needed' => App\Models\Setting::get('shop.btc_confirms_needed')
                                                ]) }}
                                            </span>
                                            @else 
												 <span id="confirmId" style="display:none;">{{ __('frontend/user.transactions_page.waiting') }}</span>
                                                <span class="label label-secondary" id="show-data"> 
                                                {{ __('frontend/user.transactions_page.waiting') }}
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->getFormattedAmount() }}
                                        </td>
                                        <td>
                                            {{ $transaction->getDate() }}
                                        </td>
                                        <td>
										
                                            @if(!$transaction->isWaiting() && strlen($transaction->txid) > 0 )
                                                <a href="https://blockchain.info/tx/{{ $transaction->txid }}"
                                                   target="_blockchain_{{ $transaction->id }}">
                                                    Blockchain
                                                    <ion-icon name="open"></ion-icon>
                                                </a>
                                            @elseif($transaction->isWaiting())
                                                <form method="POST"
                                                      action="{{ route('deposit-btc-post', $transaction->id) }}">
                                                    @csrf

                                                    <button type="submit" class="btn btn-link"
                                                            style="margin: 0;padding: 0;">{{ __('frontend/user.i_paid_button') }}</button>
                                                </form>
                                            @elseif($transaction->isPending())
                                                <form method="POST"
                                                      action="{{ route('deposit-btc-post', $transaction->id) }}">
                                                    @csrf

                                                    <button type="submit" transaction_id={{ $transaction->id }} class="btn btn-link"
                                                            style="margin: 0;padding: 0;">{{ __('frontend/user.check_again') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach

                            </table>
                        </div>

                    @endcomponent

                @else
                <div class="container">
                    <div class="container_404">
                        <h3 class="custom-block--title mt-4">
                            {{ __('frontend/user.transactions_page.no_transactions_exists') }}
                        </h3>
                        <img src="/assets/img/404.svg" alt="pic" class="">
                    </div>
                </div>
                    <!--<div class="alert alert-warning">-->
                    <!--    {{ __('frontend/user.transactions_page.no_transactions_exists') }}-->
                    <!--</div>-->
                @endif
            </div>
        </div>
    </div>

	<script>
	setInterval(intevelUpdateBitCoin,  5000);
	
	
	
	function intevelUpdateBitCoin(){
		$('#transactionTable > tbody  > tr').each(function() {
		var thistable=$(this);
	    var status=$(this).find("td:eq(1) >#confirmId").text();
		var transactionId=$(this).find("td:eq(0) >span").text()
		//alert(transactionId);
		if(status==':confirms/:confirms_needed Bestätigungen')
		{
			checkBitCoinStatus(transactionId,thistable);
		}else if(status=='Zahlung ausstehend')
		{
			
			checkBitCoinStatus(transactionId,thistable);
		}		
		
	});
	}
	
		function checkBitCoinStatus(transactionId,thistable)
		{
			
			$.ajax
			({
				url:"/deposit-btc/"+transactionId+"?_token={{ csrf_token() }}",
				type:"POST",
				success:function(res)
				{ 
					if(res.status=="paid")
					{
						location.reload();
					}
				}
			});
		}
		
	</script>
	
	
	
@endsection
