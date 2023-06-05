@extends('frontend.layouts.app')

@section('content')


<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card bg-light-darkgray p-3">
                    <div class="card-body bg-light-darkgray">
                        <form method="POST" action="{{ route('recipthtml') }}" id="recipthtmlform">
                           
                            {{--
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <h3 class="page-title">{{ __('frontend/shop.product_confirm_buy') }}</h3>
                                </div>
                            </div>
                            --}}
                            
                           
                            
                            
                            
                            <div class="card p-3">
                                @if($product->isSale() && ! $product->isDigitalGoods())
                                    <div class="product-tag product-tag-sale" @if($product->isDigitalGoods()) style="border-radius: 15px !important;color: #d40511 !important;background: #fc0 !important;background: -moz-linear-gradient(45deg, #fc0 0%, #fc0 100%) !important;background: -webkit-linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;background: linear-gradient(45deg, #fc0 0%,#fc0 100%) !important;filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fc0', endColorstr='#fc0',GradientType=1 );"@endif>
                                    @if(! $product->isDigitalGoods())
                                    <span class="product-tag-percent">
                                        {{ __('frontend/shop.sale', ['percent' => $product->getSalePercent()]) }}
                                    </span>
                                    @endif
                                        {{ __('frontend/shop.tags.sale') }}
                                        <span class="product-tag-old-price">
                                        <s>{{ $product->getFormattedOldPrice() }}</s>
                                    </span>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="ml-3 mt-2"><b>{{ $product->name }}</b></h6>
                                    </div>
                                </div>
                                
                                {{-- 
                                @if(strlen($product->short_description) > 0)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            {!! nl2br($product->short_description) !!}
                                        </li>
                                    </ul>
                                @endif --}}


                                @if(strlen($product->description) > 0)
                                    <div class="card-body">
                                        {!! nl2br($product->description) !!}
                                    </div>
                                @endif

                                @if( false )

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <b>{{ __('frontend/shop.category').'>>>>' }}</b>
                                            <a href="{{ route('product-category', [$product->getCategory()->slug]) }}">
                                                {{ $product->getCategory()->name }}
                                            </a>
                                        </li>
                                    </ul>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <b>{{ __('frontend/shop.price') }}</b> {{ $product->getFormattedPrice() }}
                                        </li>
                                    </ul>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            @if(!$product->asWeight())
                                                <b>{{ __('frontend/shop.product_amount') }}</b> {{ $amount  }}
                                            @else
                                                <b>{{ __('frontend/shop.product_weight') }}</b> {{ $amount  }}{{ $product->getWeightChar() }}
                                            @endif
                                        </li>
                                    </ul>

                                @endif
                                
                                
                                
                                
                              

                               
                               
                                    <div class="row mt-2 mb-2">
                                        <div class="col-md-7">
                                            <div class="form-outline  ml-3">
                                                <input type="text" id="datetimepicker1" name="datetime" class="br-outline-input form-control" value="{{ \Carbon\Carbon::now()->addDay()->format('d/m/Y H:i') }}"
                                                                   value="" required/>
                                                <label
                                                    class="form-label f-sm bg-white-imp">Date Time</label>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row mt-2 mb-2">
                                        <div class="col-md-7">
                                            <div class="form-outline  ml-3">
                                                <input type="text" name="track_number" class="br-outline-input form-control" required/>
                                                <label
                                                    class="form-label f-sm bg-white-imp">Tracking Number</label>
                                            </div>
                                        </div>
                                    </div>
									<div class="row mt-2 mb-2">
                                        <div class="col-md-7">
                                            <div class="form-outline  ml-3">
                                                <input type="text" id="search_address" name="search_address" class="br-outline-input form-control" required/>
                                                <label
                                                    class="form-label f-sm bg-white-imp">Search Address</label>
                                            </div>
                                        </div>
                                    </div>
									
									<div class="row mt-2 mb-2">
                                        <div class="col-md-7">
                                            <div class="form-outline  ml-3" id="resulted_address">
                                               
                                            </div>
                                        </div>
                                    </div>
									
                                    <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                                    <input type="hidden" name="product_amount" value="{{ $amount }}"/>
                                    <input type="hidden" name="city" id="city" value=""/>
                                     <input type="hidden" name="postel" id="postel" value=""/>
                               

                                
                                    <input name="receipt" type="hidden" id="receipt-hidden" value="no"/>
                                    
                                    <ul class="list-group list-group-flush">
                                        <li id="total_price" class="list-group-item" style="font-size: 16px">
                                            <b>{{ __('frontend/shop.total_price') }}</b> {{ $totalPriceHtml }}
                                        </li>
                                    </ul>

                                    <br/>
                                   <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="text-right">
                                            @csrf

                                            

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <button id="conform_btn" class="btn btn-red btn-block"
                                                            @if(!$product->isAvailable() && !in_array($product->id,[53,54,55]) ) disabled @endif>{{ __('frontend/shop.confirm') }}</button>
                                                </div>
                                                <div class="col-md-4">
                                                    <a href="{{ route('product-page', $product->id) }}"
                                                       class="btn btn-default btn-block">{{ __('frontend/shop.cancel_order') }}</a>
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            
            
            localStorage.clear();
            var totalPrice = {{ $totalPrice }},
                currency = '{{ App\Models\Setting::getShopCurrency() }}';
                
            window.localStorage.setItem('totalPrice', JSON.stringify({
                totalPrice: totalPrice,
                deliveryPrice: 0,
                timePrice: 0,
                receiptPrice: 0,
                @if($isBoxingProduct && ($category=='morty-nachnahme' || $category=='welt-nachnahme' || $category=='lalo-nachnahme' || $category=='nachnahme'))
                serviceFee: 3000
                @endif
            }));

            function changeTotalPrice(me, key, dataAttr) {
                var sum = 0,
                    totalPriceArray = Object.values({
                        totalPrice: totalPrice,
                        deliveryPrice: 0,
                        timePrice: 0,
                        receiptPrice: 0,
                        @if($isBoxingProduct && ($category=='morty-nachnahme' || $category=='welt-nachnahme' || $category=='lalo-nachnahme' || $category=='nachnahme'))
                        serviceFee: 3000
                        @endif
                    });

                try {
                    totalPriceArray = JSON.parse(window.localStorage.getItem('totalPrice'));
                    if (! totalPriceArray) {
                        totalPriceArray = Object.values({
                        totalPrice: totalPrice,
                        deliveryPrice: 0,
                        timePrice: 0,
                        receiptPrice: 0,
                        @if($isBoxingProduct && ($category=='morty-nachnahme' || $category=='welt-nachnahme' || $category=='lalo-nachnahme' || $category=='nachnahme'))
                        serviceFee: 3000
                        @endif
                      
                    });
                    }
                } catch (e) {
                    totalPriceArray = Object.values({
                        totalPrice: totalPrice,
                        deliveryPrice: 0,
                        timePrice: 0,
                        receiptPrice: 0,
                        @if($isBoxingProduct && ($category=='morty-nachnahme' || $category=='welt-nachnahme' || $category=='lalo-nachnahme' || $category=='nachnahme'))
                        serviceFee: 3000
                       
                        @endif
                    });
                }

                totalPriceArray[key] = $(me).data(dataAttr) || 0;

                window.localStorage.setItem('totalPrice', JSON.stringify(totalPriceArray));
                console.log(totalPriceArray);
                $.each(totalPriceArray, function (index, value) {
                    sum = sum + value;
                });

                var totalPrice_new = sum / 100;
                totalPrice_new = totalPrice_new.toFixed(2).replace(".", ",");
                totalPrice_new = '<b>{{ __('frontend/shop.total_price') }}</b> ' + totalPrice_new + ' ' + (currency || 'EUR');

                $('#total_price').html(totalPrice_new);
            }

	        $('#shipping_addresses_button').on('click', function () {
                $('#shipping_addresses_block').toggle();
            });

            $('#import_shipping_addresses_button').on('click', function () {
                try {
                    var option = $('#shipping_addresses_sender').val().split(/\r?\n/);
                    
                    if (Array.isArray(option)) {
                        console.log(option);

                        Object.values(option).map(function (value, key) {
                            switch(key) {
                                case 0: 
                                    var names = value.split(' ');
                                    $('input[name="sender_first_name"]').val(names[0] || '');
                                    $('input[name="sender_last_name"]').val(names[1] || '');
                                break;
                                case 1: 
                                    $('input[name="sender_street"]').val(value);
                                break;
                                case 2: 
                                    var values = value.split(' ');
                                    $('input[name="sender_zip"]').val(values[0] || '');
                                    $('input[name="sender_city"]').val(values[1] || '');
                                break;
                                case 3: 
                                    if (typeof value === "string" && value !== ""){
                                        $('input[name="sender_country"]').val(value);
                                    }
                                break;
                            }
                        });
                    } 
                } catch (e) {}

                try {
                    var option = $('#shipping_addresses_receiver').val().split(/\r?\n/);
                    
                    if (Array.isArray(option)) {
                        console.log(option);

                        Object.values(option).map(function (value, key) {
                            switch(key) {
                                case 0: 
                                    var names = value.split(' ');
                                    $('input[name="first_name"]').val(names[0] || '');
                                    $('input[name="last_name"]').val(names[1] || '');
                                break;
                                case 1: 
                                    $('input[name="street"]').val(value);
                                break;
                                case 2: 
                                    var values = value.split(' ');
                                    $('input[name="zip"]').val(values[0] || '');
                                    $('input[name="city"]').val(values[1] || '');
                                break;
                                case 3: 
                                    if (typeof value === "string" && value !== ""){
                                        $('input[name="country"]').val(value);
                                    }
                                break;
                            }
                        });
                    } 
                } catch (e) {}
            });

            @if(! $isBoxingProduct)
            $('#receipt-hidden').val('no');
            $('#receipt-btn').css('background-color', '#fff');
            $('#receipt-btn').css('border-color', '#cccccc');
            $('#receipt').data('data-price', 0);
            changeTotalPrice($('#receipt'), 'receiptPrice', 'data-price');
            @else
            let price = $('#product_payment_amount').val();
            @if($category!='morty-nachnahme' && $category!='welt-nachnahme')
            if (price < 500 ) {
                price = 500;
            }
            @endif
            @if($category=='lalo-nachnahme')
            let percentage = 7;
            @elseif($category=='morty-nachnahme' || $category=='welt-nachnahme')
            percentage = 0;
            @else
            percentage = 10;
            @endif
            $('#product_payment_amount').data('data-price', price * percentage);

            changeTotalPrice($('#product_payment_amount'), 'totalPrice', 'data-price');
            @endif

            $('#product_payment_amount').on('change', function () {
                let price = $('#product_payment_amount').val();
                @if($category!='morty-nachnahme' && $category!='welt-nachnahme')
                if (price < 500) {
                    price = 500;
                }
                @endif
                $('#product_payment_amount').data('data-price', price * percentage);

                changeTotalPrice($('#product_payment_amount'), 'totalPrice', 'data-price');
            });

            function chargeReceipt() 
            {
                if ($('#receipt-hidden').val() === 'yes') {
                    $('#receipt-hidden').val('no');
                    $('#receipt-btn').css('background-color', '#fff');
                    $('#receipt-btn').css('border-color', '#cccccc');
                    $('#receipt').data('data-price', 0);
                    changeTotalPrice($('#receipt'), 'receiptPrice', 'data-price');
                } else {
                    $('#receipt-hidden').val('yes');
                    $('#receipt-btn').css('background-color', '#48b451');
                    $('#receipt-btn').css('border-color', '#48b451');
                    $('#receipt-btn').css('text-align', 'center');
                    $('#receipt').data('data-price', 500); // TODO: change value to 500 = 5EURO
                    changeTotalPrice($('#receipt'), 'receiptPrice', 'data-price');
                }
            }

            function receiptClick() 
            {
                $(this).toggleClass('receipt-option-checked');

                chargeReceipt();
            }

            $('#receipt-option label').on('click', receiptClick);

            $('#receipt-info-btn').on('click', function () {
                $('#receipt-option label').off('click');
                alert('here');
                $('#receipt-option label').on('click');
            });

            $('.send_at').hide();
            $('input[type=radio][name=shipping_time]').change(function () {
                changeTotalPrice(this, 'timePrice', 'time-price');

                if (this.value == 'desired_date') {
                    $('.send_at').show();
                    $('.send_at').find('input').prop('required', true).prop('disabled', false);
                } else {
                    $('.send_at').hide();
                    $('.send_at').find('input').prop('required', false).prop('disabled', true);
                }
            });

            $('input[type=radio][name=product_delivery_method]').change(function () {
                changeTotalPrice(this, 'deliveryPrice', 'delivery-price');

                if ($(this).val() == 4) {
                    $('#receipt-option').hide();

                    $('#receipt-hidden').val('no');
                    $('#receipt-btn').css('background-color', '#fff');
                    $('#receipt-btn').css('border-color', '#cccccc');
                    $('#receipt').data('data-price', 0);
                    changeTotalPrice($('#receipt'), 'receiptPrice', 'data-price');
                } else {
                    $('#receipt-option').show();
                }
            });
            
            
            
			 
			$("#search_address").keyup(function(){
			  
				let searchval =$(this).val();
				var html = '';
				$.ajax({
					url: "{{route('buy-product-form-search-address')}}", 
					data:{searchval:searchval},
					dataType : 'json',
					success: function(result){
						$.each(result.data,function(key,value){
						     let address=value.name + '<br>' +value.address_one+'<br>'+value.zip+' '+value.address_two;
						     //alert(address);
							html = html+'<label class="custom-radio k-radio k-radio--all k-radio--solid"><input data-city="'+value.address_two+'" data-zip="'+value.zip+'" type="radio" name="address_selected" value="'+address+'" data-content-visible="false" data-weight-visible="false" data-delivery-price="0"><span class="radio" id="product-delivery-btn-1"></span>'+  value.name + '<br>' +value.address_one+'<br>'+value.zip+' '+value.address_two+'</label>';
							//html = html+"<input type='radio' name='address_selected' class='' value='"+address+"'>"+  value.name + "<br>" +value.address_one+"<br>"+value.zip+" "+value.address_two+"<br><br>";
						});
						
						$("#resulted_address").html(html);
						
							$('input[type=radio][name=address_selected]').change(function() {
							    
							    $("#city").val($(this).data('city'));
							    $("#postel").val($(this).data('zip'));
							   
                        });
					}
				});
			});
			
			
              $('#recipthtmlform').submit(function(e) {
                //e.preventDefault(); // prevent form submission
               if (!$("input[name='address_selected']:checked").val()) {
                   $('#message').removeClass('alert-success').addClass('alert-danger').html('Address Field is required');
                   return false;

                   //$("input[name='address_selected']").prop('required',true);
               }
               else{
                   return true;
                    //$('#recipthtmlform').submit();
               }
              });
            
           
        });
        
        
		
			
    </script>


    @if(isset($replaceEntry) && $replaceEntry != null)
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="container tets">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                {{ __('frontend/shop.replace_rules_alert') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div id="faqAccordion" class="mb-15 accordion-with-icon">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-12 mb-15">
                                <div class="card bg-shahzad-lightgray">
                                    <div class="card-header header-colour-none" id="faqHeading">
                                    <span data-toggle="collapse" data-target="#faqCollapse" aria-expanded="true"
                                          aria-controls="faqCollapse" class="pt-3">
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                    @if(! is_null($product->getCategory()) && ! is_null($product->getCategory()->slug) && strtolower($product->getCategory()->slug) === 'lit')
                                                    <span class="letter-spacing-shahzad"> Was wenn mein Paket nicht verloren geht?</span>
                                                    @elseif($isBoxingProduct)
                                                    <span class="letter-spacing-shahzad"> Replace Regeln für Nachnahme</span>
                                                    @elseif (! $product->isDigitalGoods())
                                                    <span class="letter-spacing-shahzad"> {{ $replaceEntry->question }}</span>
                                                    @else
                                                    <span class="letter-spacing-shahzad"> Replace Regeln bei Accounts.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </span>
                                    </div>

                                    <div id="faqCollapse" class="collapse show" aria-labelledby="faqHeading"
                                         data-parent="#faqAccordion">
                                         <div class="card-body">
                                            @if(! is_null($product->getCategory()) && ! is_null($product->getCategory()->slug) && strtolower($product->getCategory()->slug) === 'lit')
                                            <p>Mit einer extrem kleinen Wahrscheinlichkeit geht das Paket nicht verloren oder die Sendungsnummer wird nicht aktualisiert.</p><p>In diesem Fall eröffne bitte ein Ticket oder melde dich per PN.</p><p>Wir werden das Geld in Form von Guthaben replacen.</p><p>Wir haften außerdem nicht für gescheiterte Refunds.</p>
                                            @elseif($isBoxingProduct && $product->category->slug!='nachnahme')
											
											 <p>Ein Replace des BTC Betrags erfolgt, sollte der Vic das Paket nicht angenommen haben innerhalb 48h. <p> <p>
Der EUR Betrag wird bei der Bestellung zu dem aktuellen BTC Kurs umgerechnet, als Replace erfolgt der exakte BTC Betrag! <p> <p>
Meldet euch bitte per PN oder per Ticket mit eurer Wallet. <p> <p>
Die 20€ Service-Gebühr wird nicht zurückerstattet. <p> <p>
                                            @elseif(! $product->isDigitalGoods() && $product->category->slug!='nachnahme')
                                            {!! strlen($replaceEntry->answer) > 0 ? nl2br($replaceEntry->answer) : '' !!}
                                            @elseif ($product->category->slug=='nachnahme')
                                            <p> Sollte das Paket nicht angenommen, oder abgeholt werden erstatten wir die 10% ab sofort nurnoch als Shop-Guthaben zurück.</p>
                                            <p>Da einige meinen unsere Mitarbeiter mit Ihren Schrott Vics auszulasten, können wir das Problem nur so beheben.</p>
                                            <p>Stammkunden mit guter Annahmequote erhalten selbstverständlich Ihr Geld in BTC zurück. Diese Regel ist seit dem 11.02.2023 in Kraft. Bestellungen vor dem Datum sind nicht betroffen.</p>
                                            <p>Die Servicepauschale ist nicht erstattbar. Erstellt für die erstattung bitte ein Ticket oder schreibt eine PN.</p>
                                            @else
                                            <p>- Im Falle eines Replaces erfolgt ausschließlich eine Gutschrift in unserem Shop</p><p>- Shop Guthaben KANN NICHT Ausgezahlt werden, zahlt soviel ein wieviel ihr benötigt.</p><p><br></p><p>Generell gilt, dass Replace Video ist nur gültig wenn folgende Kriterien erfüllt werden:</p><p>- die aktuelle Systemzeit sowie die URL unseres Stores bzw den Kauf auf unserer Seite zeigt,</p><p>- ein NonBL VicSocks über whoer.net zu sehen ist,</p><p>- vor dem Erhalt der Ware aufgenommen undungeschnitten ist.</p><p>- Keine Handy Aufnahmen erlaubt.</p><p><br></p><p>Wir empfehlen für den Upload bei einem Replace Anspruch streamable.com!</p><p>Bitte versteht das aufgrund von Kulanzmissbrauch kein Replace ohne Video möglich ist.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
