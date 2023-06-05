<?php

namespace App\Http\Controllers\Shop;


use Throwable;
use Validator;
use Carbon\Carbon;

use App\Models\FAQ;

use App\Models\Tid;

use App\Models\Product;
use App\Models\Setting;
use setasign\Fpdi\Fpdi;
use App\Models\UserOrder;
use App\Models\Packstation;
use App\Models\ProductItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\DeliveryMethod;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Models\TidPackStation;
use App\Models\User;

use App\Services\Domain\TidGenerationService;
use App\Services\Domain\BitcoinConverterService;
use BADDIServices\FakeTIDs\Events\Order\OrderWasCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

class ShopController extends Controller
{
    /** @var \App\Services\Domain\TidGenerationService */
    private $tidGenerationService;

    /** @var \App\Services\Domain\BitcoinConverterService */
    private $bitcoinConverterService;

    public function __construct(TidGenerationService $tidGenerationService, BitcoinConverterService $bitcoinConverterService)
    {
        if ( Setting::get( 'app.access_only_for_users', false ) ) {
            if (Route::current()->getName() == "product-category" && in_array(Route::current()->parameters()['slug'], ['deutschland_tids', 'europa_tids', 'lit'])) {
                // no auth required
            } else {
                $this->middleware('auth');
            }
        }

        $this->tidGenerationService = $tidGenerationService;
        $this->bitcoinConverterService = $bitcoinConverterService;
    }
    
    /**** search address ***/
    public function searchAddresses(Request $req){
        
        $packstations = Packstation::where("zip","LIKE","%".$req->searchval."%")->orWhere("address_two","LIKE","%".$req->searchval."%")->limit(5)->get();
        echo json_encode(array("data"=>$packstations));
        exit;
    }
    
    public function recipthtml(Request $request){
        
       
        if (! Auth::check()) {
            return redirect()
                ->route('shop')
                ->with(
                    [
                        'errorMessage' => __( 'frontend/shop.must_logged_in' )
                    ]
                );
        }
		
		
        $validated_address = Validator::make( $request->all(), [
            'datetime'  => 'required',
            'track_number'  => 'required',
            'address_selected'  => 'required'
        ]);
        $productId = $request->product_id;
		
		
		if ($request->getMethod() == 'POST' && ! is_null($productId)) {
			$product   = Product::where('id', $productId)->first();
            if (! $product instanceof Product) {
                return redirect()
                    ->route( 'shop' )
                    ->with([
                        'errorMessage' => __( 'frontend/shop.product_not_found' )
                    ]);
            }
			$datetime=$request->datetime;
			$track_number=$request->track_number;
			$address_selected=$request->address_selected;
			
			
			//$date=date_create($request->datetime);
                        $date=date_create_from_format('d/m/Y H:i', $request->datetime);
			
		   $datetime=date_format($date,"d.m.Y") ." um ".date_format($date,"H:i");
			$amount = intval($request->get('product_amount'));

            $isRefundingProduct = in_array($product->name, ['LIT für Refund','Return To Sender (RTS)','Special Amazon RTS','Express Scans (LIT/REFUSED/DELIVERY)']);
            $isRandomProduct = in_array($product->name, ['LIT für Refund', 'LIT für Filling','Return To Sender (RTS)','Special Amazon RTS','Express Scans (LIT/REFUSED/DELIVERY)']);
            $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);
			
			
            if (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) {

                $tid = Tid::where( 'product_id', $product->id )->where(['used' => '0'])->firstOrFail();
            }
			
			$priceInCent = $product->price_in_cent*$amount;
			
			
			 if ( Auth::user()->balance_in_cent >= $priceInCent ) {
				$newBalance = Auth::user()->balance_in_cent - $priceInCent;

				Auth::user()->update( [
					'balance_in_cent' => $newBalance
				] );
				
				
				if ( $product->isUnlimited() || $product->isDigitalGoods() ) {
					
					
			
					$productItems = ProductItem::where('product_id', $product->id)->limit($amount ?? 5)->get();
					
					
					
					$productItemsContent = implode('', $productItems->pluck(['content'])->toArray());
					$orderDetails = [
						'user_id'        => Auth::user()->id,
						'product_id'     => $product->id,
						'tid_id'         => (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) ? $tid->id : 0,
						'name'           => $product->name,
						'content'        => ! $product->isDigitalGoods() ? $product->content : $productItemsContent,
						'price_in_cent'  => $product->price_in_cent,
						'totalprice'     => $priceInCent,
						// 'drop_info'      => $dropInfo,
						'delivery_price' => '',
						'delivery_name'  => '',
						'status'         => 'nothing',
						'weight'         => ! $product->isDigitalGoods() ? 0 : $amount,
						'weight_char'    => '',
						'include_receipt'=> 0,
						'tracking_number'   => $track_number,
						'qrcode'   => '',
					];
					
					
					$order = UserOrder::create($orderDetails);
					
					event(new OrderWasCreated($order->id));

					/* if (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) {
						$tid->update([ 'used' => 1 ]);
					} */

					
					if (! $product->isDigitalGoods()) {
						$product->update( [
							'sells' => $product->sells + 1
						] );
					} else {
						foreach($productItems as $item) {
							$item->delete();
						}

						$product->update( [
							'sells'            => $product->sells + $amount
						] );
					}

					if ( $request->has( 'send_at' ) ) {
						$order->update( [
							'type_deliver' => 'desired_date',
							'deliver_at'   => $request->input( 'send_at' )
						] );
						$errorMessage =  '';
					} else {
						if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
							$order->update( [
								'deliver_at' => Carbon::tomorrow()
							] );

							$errorMessage = '';
						} else {
							$order->update( [
								'deliver_at' => Carbon::now()
							] );
							$errorMessage =  '';
						}
					}

					Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

					/* if (! $isBoxingProduct && ! $isRefundingProduct && ! $product->isDigitalGoods()) {
						$this->tidGenerationService->generateTidPDF($order->id);
					} */

					Notification::create( [
						'custom_id' => Auth::user()->id,
						'type'      => 'order'
					] );

					$html = <<<EOD
					<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
					<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
						<head>
							<meta content="text/html; charset=utf-8" http-equiv="content-type">
							<meta content="width=device-width, initial-scale=1.0" name="viewport">
							<link rel="icon" type="image/vnd.microsoft.icon" href="https://imgs.elainemedia.de/6209/favicon.ico">
					<!--[if !mso]><!-- -->
						<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<!--[endif]-->


					 <title>DHL Information</title>
					<style type="text/css">
					body{margin:0;padding:0;background-color:#f3f3f3;}
					img {display:block; border:0;}
					body, td, font, p, span, a, strong, li {-webkit-text-size-adjust: none;}
					table{mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse; border:0;}
					* {-webkit-text-size-adjust: none}

					@font-face {
					  font-family: "DeliveryRegular";
					  font-style: normal;
					  font-weight: normal;
					  src:
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.eot") format("embedded-opentype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.ttf") format("truetype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.woff2") format("woff2"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.woff") format("woff");
					mso-font-alt: 'Arial';
					}

					@font-face {
					  font-family: "DeliveryConsensedBlack";
					  font-style: normal;
					  font-weight: bold;
					  src:
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.eot") format("embedded-opentype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.ttf") format("truetype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.woff2") format("woff2"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.woff") format("woff");
					mso-font-alt: 'Arial Black';
					}

					@font-face {
					  font-family: "DeliveryConsensedLight";
					  font-style: normal;
					  font-weight: normal;
					  src:
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.eot") format("embedded-opentype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.ttf") format("truetype"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.woff2") format("woff2"),
					  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.woff") format("woff");
					mso-font-alt: 'Arial';
					}

					/* Formatierung Links */
					body[data-outlook-cycle] .articleText a {outline:none; color:rgb(168, 195, 61); text-decoration:none; font-weight: bold;}
					.articleText a .articleText a {color:#d40511;}
					.footerText a {color:#d40511;}
					a{outline:none; color:#d40511;}
					a[x-apple-data-detectors]{color:inherit !important; text-decoration:none !important;}
					a[href^="tel"]:hover{text-decoration:none !important;}
					#MessageViewBody a {
						color: inherit;
						text-decoration: none;
						font-size: inherit;
						font-family: inherit;
						font-weight: inherit;
						line-height: inherit;
					}

					@media all and ( max-width: 799px ){
					.nomobile {display:none !important;}
					.mobileTable {display:table !important;width:100% !important;}
					.mobileTableApp {display:block ruby !important;width:100% !important;}
					.wrapperContent, .wrapperHeader, .wrapperFooter, .twoColumns, .oneColumn, .oneColumnImg, .oneColumnImg img {width: 100% !important;max-width:800px !important; }

					.twoColumnsImg, .twoColumnsImg img {width: 100% !important;max-width:100% !important; height: auto !important;}
					.oneColumnImg .notFull, .twoColumnsImg .notFull, .notFull img  {width: auto !important; max-width:100% !important; height: auto !important; }
					.mobileTableApp img {width: 100% !important;max-width:210px !important; height: auto !important;}
					.articleSeperator td {height:20px !important;}
					.articleHeadline {padding-top:10px !important;}
					.articleHeadlineHeader {padding-top:10px !important;line-height:26px !important;}
					.articleHeadline31 {padding-top:10px !important;font-size:24px !important;line-height:22px !important;}
					.articleHeadline42 {padding-top:10px !important;font-size:28px !important;line-height:28px !important;}
					.footerText{text-align:left !important;line-height:22px !important;}
					.articleText, .articleText a {font-size:17px!important;line-height:22px !important;}
					.articleText_28 {font-size:20px!important;line-height:22px !important;}
					.mobilePadding, .layoutMobileTableIcon, .layoutMobileTableIcon{padding: 0 15px 0 15px !important;}
					}

					@media all and ( max-width: 400px ){
					.yellowBg {padding: 0px!important;}

					}

					</style>

					<meta name="title" content="DHL Newsletter" />
					<!--[if gte mso 9]><xml>
					<o:OfficeDocumentSettings>
					<o:AllowPNG/>
					<o:PixelsPerInch>96/o:PixelsPerInch
					/o:OfficeDocumentSettings
					</xml><![endif]-->
					</head>
					<body style="background-color: #f3f3f3; margin: 0; padding: 0;" yahoo="fix"><img src="https://mailing3.dhl.de/action/view/127/20wp2knx/7?t_id=3087552695&static=1" border="0" width="1" height="1" alt="" />
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="center">
					<!-- open page wrapper header -->
					<table width="800" border="0" cellspacing="0" cellpadding="0" class="wrapperHeader" style="width:800px;">
					<tr>
					<td style="padding: 0px 0px 0px 0px;">

					<!-- Preheadertext -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td style="color: #f3f3f3; padding: 0px 0px 0px 0px; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 1px; text-align: center; line-height: 1px;" align="center">
					Wichtige Informationen zu Ihrem Paket
					</td>
					</tr>
					</table>
					<!-- Onlinelink -->
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
					<td class="headerOnlineversion" style="color: #666666; padding: 10px 0px 10px 0px; font-family: DeliveryRegular, Calibri, DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 15px; text-align: center;line-height:19px;" align="center">
					 <a href="https://mailing3.dhl.de/go/wgr20wp2knxryaetl4fdy11rcktyrajdzwg00040o5r4/7?t_id=3087552695" target="_blank" style="color: #d40511; ">Onlineversion</a>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>
					<!-- close page wrapper header -->
					</td>
					</tr>
					<tr>
					<td align="center">
					<!-- open page wrapper content -->
					<table cellpadding="0" cellspacing="0" width="800" class="wrapperContent" style="width:800px;">
					<tr>
					<td>

					<!-- Image max. 800px-->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td class="oneColumnImg notFull" align="center">
					<a href="https://mailing3.dhl.de/go/26z20wp2knx01b3j269haq5y22rm867m3sm80sk8k5x8/7?t_id=3087552695" target="_blank"><img src="https://lforr.elainelfo.net/nr243/go/mxn20wp2knxyqznxto19gkn0787pev49ulk848cso62q/7?t_id=3087552695" alt="DHL" style="display:block; border:0;" /></a>

					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="15"> </td>
					</tr>

					</table>

					<!-- Layoutartikel einspaltig Hintergrund editierbar -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					Hallo,<br /><br />Sie haben Ihre Sendung $datetime Uhr eingeliefert.
					</td>
					</tr>
					</table>

					<!-- Editorial -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					<br>
					Ein DHL Zusteller wird sie spätestens am nächsten Werktag abholen und auf den Versandweg bringen.<br /><br />Beste Grüße
					</td>
					</tr>
					<tr>
					<td>
					<img src="http://imgs.elainemedia.de/e56b/7457b06f1a8c3ebf82575777fd6dc48d.png" alt="DHL Team" style="border:0;" />
					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="25"> </td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					<!-- Layoutartikel zweispaltig Hintergrund editierbar -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">

					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">


					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
					<tr>
					<td style="font-size: 1px;" height="15"> </td>
					</tr>

					</table>

					<!-- Headline Versalien -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
					<tr>
					<td class="articleHeadline" align="left" style="color: #d40511; font-family: DeliveryConsensedBlack, 'Arial Black', 'Calibri', 'Arial', Helvetica, sans-serif;font-weight:bold; font-size: 28px; text-transform:uppercase; line-height:32px;">
					Ihr Versand
					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
					<tr>
					<td style="font-size: 1px;" height="20"> </td>
					</tr>

					</table>

					</td>
					</tr>
					<tr>
					<td  align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

					<!--[if gte mso 9]>
					<table border="0" cellspacing="0" cellpadding="0" width="730" style="width:730px;"><tr><td valign="top">
					<![endif]-->

					<table class="twoColumnsImg mobileTable" width="300" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
					<tr>
					<td align="left" width="300">
					<!-- Composingbox 300px -->

					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					<strong>Ihre Sendung</strong>
					</td>
					</tr>
					</table>

					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					<strong><a href="https://mailing3.dhl.de/go/ye320wp2knx4xvw9vvdouscp9m7z8pmb86jkgs8ggsta/7?t_id=3087552695&get_identcode0=JJD1405465045742" target="_blank" rel="noopener" style="color: #d40511; text-decoration: none;" >$track_number</a><br /></strong>
					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>
					<!--[if gte mso 9]>
					</td><td valign="top">
					<![endif]-->
					<table width="35" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
					<tr>
					<td width="35">
					 
					</td>
					</tr>
					</table>

					<!--[if gte mso 9]>
					</td><td valign="top">
					<![endif]-->
					<table class="mobileTable"  width="395" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
					<tr>



					<td align="left" style="padding: 0px 0px 0px 0px;" width="395">
					<!-- Composingbox 395px -->

					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					<strong>Adresse</strong><br>$address_selected
					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					<!--[if gte mso 9]>
					</td></tr></table>
					<![endif]-->

					</td>
					</tr>
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">


					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
					<tr>
					<td style="font-size: 1px;" height="25"> </td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					<!-- Layoutartikel einspaltig Hintergrund editierbar -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="40"> </td>
					</tr>
					</table>

					<!-- Headline Versalien -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td class="articleHeadline" align="left" style="color: #d40511; font-family: DeliveryConsensedBlack, 'Arial Black', 'Calibri', 'Arial', Helvetica, sans-serif; font-weight:bold; font-size: 28px; text-transform:uppercase; line-height:32px;">
					Nützliche Information
					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="15"> </td>
					</tr>

					</table>

					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
					Hier sehen Sie, <a href="https://mailing3.dhl.de/go/2u320wp2knx32yz2ejik9r9g1cad1qd6yflcs80oc6ho/7?t_id=3087552695" target="_blank" rel="noopener" style="color: #d40511; text-decoration: none;" >wie Sie sich für die Packstation anmelden</a>, um sie im vollen Umfang nutzen zu können.
					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>



					<!-- close page wrapper 2 -->
					</td>
					</tr>
					</table>
					<!-- close page wrapper content -->
					</td>
					</tr>
					<tr>
					<td align="center">
					<!-- open page wrapper footer -->

					<table width="800" border="0" cellspacing="0" cellpadding="0" class="wrapperFooter" style="background-color: #ffffff;width:800px;" bgcolor="#ffffff">
					<tr>
					<td>

					<!-- Trenner mit Linie Footer -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="35"> </td>
					</tr>
					<tr>
					<td height="50" style="font-size: 1px; border-top: 1px solid #dddddd;padding-left:20px;"> </td>
					</tr>
					</table>
					</td>
					</tr>
					</table>

					<!-- Layoutartikel einspaltig Hintergrund editierbar -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

					<!-- App -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="35"> </td>
					</tr>
					</table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td class="articleHeadline" align="center" style="color: #d40511; font-family: DeliveryConsensedBlack, 'Arial Black', 'Calibri', 'Arial', Helvetica, sans-serif;font-weight:bold; font-size: 28px; line-height:32px;text-transform:uppercase;">
					Post & DHL App
					</td>
					</tr>
					</table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="35"> </td>
					</tr>
					</table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td  align="center" style="padding:0px 20px 0px 20px;">


					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="center">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left" class="mobileTable notFull" style="color: #d40511; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;text-align:center;">
					<center>
					<a href="https://mailing3.dhl.de/go/l7f20wp2knxc7a2zdvk9ulf6w9q2gdu20sk088ccw6or/7?t_id=3087552695" target="_blank"><img src="https://lforr.elainelfo.net/nr243/go/tzv20wp2knxyiskdhu1gr4n2a26bhe7w3f3tw0wcs6ug/7?t_id=3087552695" alt="Post & DHL App" style="display:block; border:0;" /></a>

					</center>
					</td>
					<td width="30" height="30" align="left" class="mobileTable notFull"  style="width:30px;"></td>
					<td align="left" class="mobileTable" style="color: #d40511; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;text-align:center;">
					<center>
					<a href="https://mailing3.dhl.de/go/ot720wp2knx591e2fskrt11ppk14gq7kocaowkwsc704/7?t_id=3087552695" target="_blank"><img src="https://lforr.elainelfo.net/nr243/go/rff20wp2knxr4o7af8ck3y7mnp6lnc8xt1hwosc0075l/7?t_id=3087552695" alt="Post & DHL App" style="display:block; border:0;" /></a>

					</center>
					</td>
					</tr>
					</table>


					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>


					</td>
					</tr>
					</table>


					<!-- Trenner mit Linie Footer -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="40"> </td>
					</tr>
					<tr>
					<td height="65" style="font-size: 1px; border-top: 1px solid #dddddd;padding-left:20px;"> </td>
					</tr>
					</table>
					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					<!-- Layoutartikel einspaltig Hintergrund editierbar -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

					<!-- Impressum -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td  align="center" style="padding:0px 35px 0px 35px;width:345px;" width="345">


					<table class="mobileTable" width="315" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;width:315px;">
					<tr>
					<td align="left" style="padding: 0px 0px 0px 0px;width:315px;" width="315">

					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left" class="footerText" style="color: #d40511; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 25px;">
					<a title="Datenschutzerklärung" style="color: #d40511" href="https://www.dhl.de/de/toolbar/footer/datenschutz.html" target="_blank">Datenschutzerklärung</a>
					</td>
					</tr>
					<tr>
					<td align="left" class="footerText" style="color: #d40511; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 25px;">
					<a title="Impressum" style="color: #d40511" href="https://www.dhl.de/de/toolbar/footer/impressum/vertragspartner-impressum.html" target="_blank">Impressum</a>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>


					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="15"> </td>
					</tr>

					</table>

					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td  align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding" width="100%">


					<!-- Text -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td align="left" class="footerText" style="color: #666666; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 25px;">
					Auftragnehmer (Frachtführer) ist die Deutsche Post AG. Es gelten für Päckchen die AGB der Deutsche Post Brief National bzw. International und für Pakete die AGB der DHL Paket/Express National bzw. Paket International in der zum Zeitpunkt der Einlieferung gültigen Fassung. Der Absender versichert, dass keine in den AGB ausgeschlossenen Güter in der von ihm eingelieferten Sendung enthalten sind.<br /> <br /> <strong>Datenschutzhinweis</strong>: Die DHL Paket GmbH, Sträßchensweg 10, 53113 Bonn verarbeitet Ihre E-Mail-Adresse, um Ihnen den Einlieferungsbeleg per E-Mail zusenden zu können. Die Rechtsgrundlage für die Datenverarbeitung ist Art. 6 Abs.1 lit. b DSGVO, da die Verarbeitung für die Vertragserfüllung erforderlich ist. Die E-Mail-Adresse wird nicht an Dritte weitergegeben und nach 7 Tagen gelöscht. Ihnen steht das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Widerspruch und Datenübertragbarkeit zu. Bezüglich der Geltendmachung Ihrer Rechte nutzen Sie bitte unser <strong><a href="https://mailing3.dhl.de/go/skb20wp2knxtrdgz8uxv7hmb3eza83v8p72800kck7ct/7?t_id=3087552695" target="_blank" rel="noopener" style="color: #666666; text-decoration: underlined;" >Kontaktformular</a></strong>. Wenn Sie der Ansicht sind, dass die Verarbeitung Ihrer personenbezogenen Daten gegen Datenschutzrecht verstößt, können Sie sich bei einer Datenschutzaufsichtsbehörde beschweren. Bei datenschutzrechtlichen Fragen können Sie sich ebenfalls unter Deutsche Post AG, Konzerndatenschutz 53250 Bonn, datenschutz@dpdhl.com an unsere Datenschutzbeauftragte wenden. Weitere Informationen zum Datenschutz unter <a href="https://mailing3.dhl.de/go/asb20wp2knx6kxyvadg5sd0e37iva8fra4jcwksk07ir/7?t_id=3087552695" target="_blank" rel="noopener" style="color: #666666; text-decoration: underlined;" >www.dhl.de/datenschutz</a>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="25"> </td>
					</tr>
					</table>

					<!-- Social Bookmarks -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
					<td style="padding: 0px 35px 10px 35px;color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 18px; line-height: 24px;" align="left" class="footerText">
					Folgen Sie uns
					</td>
					</tr>
					<tr>
					<td style="padding: 0px 35px 0px 35px;" align="left">

					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td style="padding-right:15px;">
					<a title="Instagram" href="https://mailing3.dhl.de/go/m4920wp2knxm1o9ls3l0ru0wu4lkmwun9e46cc44s3ge/7?t_id=3087552695" target="_blank"><img src="https://imgs.elainemedia.de/e56b/00f596e9f89358da0904bb58ae5d6e30.png" alt="Instagram" height="37" style="display:block; border:0;" /></a>
					</td>
					<td style="padding-right:15px;">
					<a title="Facebook" href="https://mailing3.dhl.de/go/qkl20wp2knx9f6lx2o0i5lfri4q495ku8eykgo4889h1/7?t_id=3087552695" target="_blank"><img src="https://imgs.elainemedia.de/e56b/b6a8c39d25b8a641c9192a2589fc5673.png" alt="Facebook" height="37" style="display:block; border:0;" /></a>
					</td>
					<td style="padding-right:15px;">
					<a title="Twitter" href="https://mailing3.dhl.de/go/b0v20wp2knxn51v7ti2cb0m6xtt4kenxxwao48gskfg6/7?t_id=3087552695" target="_blank"><img src="https://imgs.elainemedia.de/e56b/9347265292645543084bad9caa859200.png" alt="Twitter" height="37" style="display:block; border:0;" /></a>
					</td>
					<td style="padding-right:15px;">
					<a title="Linked In" href="https://mailing3.dhl.de/go/zgt20wp2knxb1at0xhc3okkdqk3jju1jdakgwwwgwlby/7?t_id=3087552695" target="_blank"><img src="https://imgs.elainemedia.de/e56b/f25035f00916a7ae6c2cfc555931f497.png" alt="Linked In" height="37" style="display:block; border:0;" /></a>
					</td>
					<td style="padding-right:15px;">
					<a title="YouTube" href="https://mailing3.dhl.de/go/qku20wp2knxcxeuq7sqoxh99732mcdl7tgtkwos84r9h/7?t_id=3087552695" target="_blank"><img src="https://imgs.elainemedia.de/e56b/6961c502c913937a17ee326fce9beb3d.png" alt="YouTube" height="37" style="display:block; border:0;" /></a>
					</td>
					<td style="padding-right:15px;">

					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator"  bgcolor="#ffffff">
					<tr>
					<td style="font-size: 1px;" height="30"> </td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator"  bgcolor="#f3f3f3">
					<tr>
					<td style="font-size: 1px;" height="30"> </td>
					</tr>
					</table>


					<!-- Abschluss -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f3f3f3">
					<tr>
					<td  align="center" style="padding:0px 35px 0px 35px;width:345px;" class="mobilePadding" width="345">


					<!--[if gte mso 9]>
					<table border="0" cellspacing="0" cellpadding="0" width="730" ><tr><td valign="top">
					<![endif]-->

					<table class="twoColumnsImg mobileTable" width="345" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;width:345px;"><tr>
					<td align="left" width="345" style="width:345px;">

					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left" class="footerText" style="color: #000000; font-family: DeliveryConsensedBlack, 'Arial Black', 'Calibri', 'Arial', Helvetica, sans-serif; font-weight:bold; font-size: 15px; line-height: 27px;">
					Deutsche Post DHL Group
					</td>
					</tr>
					</table>


					</td>
					</tr>
					</table>
					<!--[if gte mso 9]>
					</td><td valign="top">
					<![endif]-->
					<table class="mobileTable" width="35" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;width:35px;"><tr>
					<td  width="35" style="width:35px;">
					 
					</td>
					</tr>
					</table>

					<!--[if gte mso 9]>
					</td><td valign="top">
					<![endif]-->
					<table class="mobileTable" width="315" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;width:315px;">
					<tr>
					<td align="left" style="padding: 0px 0px 0px 0px;width:315px;" width="315">

					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left" class="footerText" style="color: #666666; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 27px;text-align:right;">
					2023 © DHL Paket GmbH. All rights reserved.
					</td>
					</tr>

					</table>
					</td>
					</tr>
					</table>

					<!--[if gte mso 9]>
					</td></tr></table>
					<![endif]-->

					</td>
					</tr>
					</table>

					<!-- Trenner -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator"  bgcolor="#f3f3f3">
					<tr>
					<td style="font-size: 1px;" height="30"> </td>
					</tr>
					</table>

					</td>
					</tr>
					</table>
					<!-- close page wrapper footer -->
					</td>
					</tr>
					</table>
					</body>
					</html>
					EOD;



							$google_fonts = "Roboto";
							

							$data = array('html'=>$html,'google_fonts'=>$google_fonts);
							
							
							$ch = curl_init();
							
							curl_setopt($ch, CURLOPT_URL, "https://hcti.io/v1/image");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							
							curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
							
							curl_setopt($ch, CURLOPT_POST, 1);
							// Retrieve your user_id and api_key from https://htmlcsstoimage.com/dashboard
							curl_setopt($ch, CURLOPT_USERPWD, env('htmlcsstoimage_user_id') . ":" . env('htmlcsstoimage_aapi_key'));
							
							$headers = array();
							
							$headers[] = "Content-Type: application/x-www-form-urlencoded";
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
							
							$result = curl_exec($ch);
							if (curl_errno($ch)) {
							  echo 'Error:' . curl_error($ch);
							}
							curl_close ($ch);
							$res = json_decode($result,true);
							
							$url= $res['url'];
							
							$html1 = <<<EOD
								<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
													<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
														<head>
															<meta content="text/html; charset=utf-8" http-equiv="content-type">
															<meta content="width=device-width, initial-scale=1.0" name="viewport">
															<link rel="icon" type="image/vnd.microsoft.icon" href="https://imgs.elainemedia.de/6209/favicon.ico">
													<!--[if !mso]><!-- -->
														<meta http-equiv="X-UA-Compatible" content="IE=edge" />
													<!--[endif]-->


													 <title>DHL Information</title>
													<style type="text/css">
													body{margin:0;padding:0;background-color:#f3f3f3;}
													img {display:block; border:0;}
													body, td, font, p, span, a, strong, li {-webkit-text-size-adjust: none;}
													table{mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse; border:0;}
													* {-webkit-text-size-adjust: none}

													@font-face {
													  font-family: "DeliveryRegular";
													  font-style: normal;
													  font-weight: normal;
													  src:
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.eot") format("embedded-opentype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.ttf") format("truetype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.woff2") format("woff2"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_rg.woff") format("woff");
													mso-font-alt: 'Arial';
													}

													@font-face {
													  font-family: "DeliveryConsensedBlack";
													  font-style: normal;
													  font-weight: bold;
													  src:
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.eot") format("embedded-opentype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.ttf") format("truetype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.woff2") format("woff2"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_cdblk.woff") format("woff");
													mso-font-alt: 'Arial Black';
													}

													@font-face {
													  font-family: "DeliveryConsensedLight";
													  font-style: normal;
													  font-weight: normal;
													  src:
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.eot") format("embedded-opentype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.ttf") format("truetype"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.woff2") format("woff2"),
													  url("https://imgs.elainemedia.de/6209/cdn/delivery_lt.woff") format("woff");
													mso-font-alt: 'Arial';
													}

													/* Formatierung Links */
													body[data-outlook-cycle] .articleText a {outline:none; color:rgb(168, 195, 61); text-decoration:none; font-weight: bold;}
													.articleText a .articleText a {color:#d40511;}
													.footerText a {color:#d40511;}
													a{outline:none; color:#d40511;}
													a[x-apple-data-detectors]{color:inherit !important; text-decoration:none !important;}
													a[href^="tel"]:hover{text-decoration:none !important;}
													#MessageViewBody a {
														color: inherit;
														text-decoration: none;
														font-size: inherit;
														font-family: inherit;
														font-weight: inherit;
														line-height: inherit;
													}

													@media all and ( max-width: 799px ){
													.nomobile {display:none !important;}
													.mobileTable {display:table !important;width:100% !important;}
													.mobileTableApp {display:block ruby !important;width:100% !important;}
													.wrapperContent, .wrapperHeader, .wrapperFooter, .twoColumns, .oneColumn, .oneColumnImg, .oneColumnImg img {width: 100% !important;max-width:800px !important; }

													.twoColumnsImg, .twoColumnsImg img {width: 100% !important;max-width:100% !important; height: auto !important;}
													.oneColumnImg .notFull, .twoColumnsImg .notFull, .notFull img  {width: auto !important; max-width:100% !important; height: auto !important; }
													.mobileTableApp img {width: 100% !important;max-width:210px !important; height: auto !important;}
													.articleSeperator td {height:20px !important;}
													.articleHeadline {padding-top:10px !important;}
													.articleHeadlineHeader {padding-top:10px !important;line-height:26px !important;}
													.articleHeadline31 {padding-top:10px !important;font-size:24px !important;line-height:22px !important;}
													.articleHeadline42 {padding-top:10px !important;font-size:28px !important;line-height:28px !important;}
													.footerText{text-align:left !important;line-height:22px !important;}
													.articleText, .articleText a {font-size:17px!important;line-height:22px !important;}
													.articleText_28 {font-size:20px!important;line-height:22px !important;}
													.mobilePadding, .layoutMobileTableIcon, .layoutMobileTableIcon{padding: 0 15px 0 15px !important;}
													}

													@media all and ( max-width: 400px ){
													.yellowBg {padding: 0px!important;}

													}

													</style>

													<meta name="title" content="DHL Newsletter" />
													<!--[if gte mso 9]><xml>
													<o:OfficeDocumentSettings>
													<o:AllowPNG/>
													<o:PixelsPerInch>96/o:PixelsPerInch
													/o:OfficeDocumentSettings
													</xml><![endif]-->
													</head>
													<body style="background-color: #f3f3f3; margin: 0; padding: 0;" yahoo="fix">
													<img src="https://mailing3.dhl.de/action/view/127/20wp2knx/7?t_id=3087552695&static=1" border="0" width="100%" height="1" alt="" />
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
													
													<tr>
													<td align="center">
													<!-- open page wrapper content -->
													<table cellpadding="0" cellspacing="0" width="" class="wrapperContent" style="width:100%">
													<tr>
													<td>

													<!-- Image max. 800px-->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
													<tr>
													<td class="oneColumnImg notFull" align="center">
													<a href="https://mailing3.dhl.de/go/26z20wp2knx01b3j269haq5y22rm867m3sm80sk8k5x8/7?t_id=3087552695" target="_blank"><img src="https://lforr.elainelfo.net/nr243/go/mxn20wp2knxyqznxto19gkn0787pev49ulk848cso62q/7?t_id=3087552695" alt="DHL" style="display:block; border:0;width:100%" /></a>

													</td>
													</tr>
													</table>

													<!-- Trenner -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
													<tr>
													<td style="font-size: 1px;" height="15"> </td>
													</tr>

													</table>

													<!-- Layoutartikel einspaltig Hintergrund editierbar -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
													<tr>
													<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

													<!-- Text -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
													<tr>
													<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
													Hallo,<br /><br />Sie haben Ihre Sendung $datetime Uhr eingeliefert.
													</td>
													</tr>
													</table>

													<!-- Editorial -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
													<tr>
													<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
													<br>
													Ein DHL Zusteller wird sie spätestens am nächsten Werktag abholen und auf den Versandweg bringen.<br /><br />Beste Grüße
													</td>
													</tr>
													<tr>
													<td>
													<img src="http://imgs.elainemedia.de/e56b/7457b06f1a8c3ebf82575777fd6dc48d.png" alt="DHL Team" style="border:0;" />
													</td>
													</tr>
													</table>

													<!-- Trenner -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#ffffff">
													<tr>
													<td style="font-size: 1px;" height="25"> </td>
													</tr>
													</table>

													</td>
													</tr>
													</table>

													<!-- Layoutartikel zweispaltig Hintergrund editierbar -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">

													<tr>
													<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">


													<!-- Trenner -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
													<tr>
													<td style="font-size: 1px;" height="15"> </td>
													</tr>

													</table>

													<!-- Headline Versalien -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
													<tr>
													<td class="articleHeadline" align="left" style="color: #d40511; font-family: DeliveryConsensedBlack, 'Arial Black', 'Calibri', 'Arial', Helvetica, sans-serif;font-weight:bold; font-size: 28px; text-transform:uppercase; line-height:32px;">
													Ihr Versand
													</td>
													</tr>
													</table>

													<!-- Trenner -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
													<tr>
													<td style="font-size: 1px;" height="20"> </td>
													</tr>

													</table>

													</td>
													</tr>
													<tr>
													<td  align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">

													<!--[if gte mso 9]>
													<table border="0" cellspacing="0" cellpadding="0" width="730" style="width:730px;"><tr><td valign="top">
													<![endif]-->

													<table class="twoColumnsImg mobileTable" width="300" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
													<tr>
													<td align="left" width="300">
													<!-- Composingbox 300px -->

													<!-- Text -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
													<tr>
													<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
													<strong>Ihre Sendung</strong>
													</td>
													</tr>
													</table>

													<!-- Text -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
													<tr>
													<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
													<strong><a href="https://mailing3.dhl.de/go/ye320wp2knx4xvw9vvdouscp9m7z8pmb86jkgs8ggsta/7?t_id=3087552695&get_identcode0=JJD1405465045742" target="_blank" rel="noopener" style="color: #d40511; text-decoration: none;" >$track_number</a><br /></strong>
													</td>
													</tr>
													</table>

													</td>
													</tr>
													</table>
													<!--[if gte mso 9]>
													</td><td valign="top">
													<![endif]-->
													<table width="35" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
													<tr>
													<td width="35">
													 
													</td>
													</tr>
													</table>

													<!--[if gte mso 9]>
													</td><td valign="top">
													<![endif]-->
													<table class="mobileTable"  width="395" border="0" cellspacing="0" cellpadding="0" align="left" style="float:left; display:inline-block;">
													<tr>



													<td align="left" style="padding: 0px 0px 0px 0px;" width="395">
													<!-- Composingbox 395px -->

													<!-- Text -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
													<tr>
													<td align="left" class="articleText" style="color: #000000; font-family: DeliveryRegular, Calibri, Arial, Helvetica, sans-serif; font-size: 20px; line-height: 30px;">
													<strong>Adresse</strong><br>$address_selected
													</td>
													</tr>
													</table>

													</td>
													</tr>
													</table>

													<!--[if gte mso 9]>
													</td></tr></table>
													<![endif]-->

													</td>
													</tr>
													<tr>
													<td align="center" style="padding:0px 35px 0px 35px;" class="mobilePadding">


													<!-- Trenner -->
													<table width="100%" border="0" cellspacing="0" cellpadding="0" class="articleSeperator" bgcolor="#EBEBEB">
													<tr>
													<td style="font-size: 1px;" height="25"> </td>
													</tr>
													</table>

													</td>
													</tr>
													</table>

													



													<!-- close page wrapper 2 -->
													</td>
													</tr>
													</table>
													<!-- close page wrapper content -->
													</td>
													</tr>
													
													</table>
													</body>
													</html>
								EOD;


								$google_fonts = "Roboto";

								$data = array('html'=>$html,
											  
											  'google_fonts'=>$google_fonts);

								$ch1 = curl_init();

								curl_setopt($ch1, CURLOPT_URL, "https://hcti.io/v1/image");
								curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);

								curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($data));

								curl_setopt($ch1, CURLOPT_POST, 1);
								// Retrieve your user_id and api_key from https://htmlcsstoimage.com/dashboard
								//curl_setopt($ch1, CURLOPT_USERPWD, "1af795f9-94a6-4c21-bc00-a4383409d36d" . ":" . "9b25c38a-da27-4049-9adb-285812e93178");
                                	curl_setopt($ch1, CURLOPT_USERPWD, env('htmlcsstoimage_user_id') . ":" . env('htmlcsstoimage_aapi_key'));
								$headers1 = array();
								$headers1[] = "Content-Type: application/x-www-form-urlencoded";
								curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);

								$result1 = curl_exec($ch1);
								if (curl_errno($ch1)) {
								  echo 'Error:' . curl_error($ch1);
								}
								curl_close ($ch1);
								$res1 = json_decode($result1,true);
								 $url1 = $res1['url'];
							
							$ch = curl_init($url);
							$dir = public_path().'/images/';
							$file_name = basename($url);
							$file_name1 = basename($url1);
							$save_file_loc = $dir . $file_name.".png";
							$save_file_loc1 = $dir . $file_name1.".png";
							
							
							
							if (file_put_contents($save_file_loc, file_get_contents($url)))
							{
								
							}
							else
							{
							   
							}
							
							if (file_put_contents($save_file_loc1, file_get_contents($url1)))
							{
								
							}
							else
							{
							   
							}
							
							
							
							
							$order->post_date_time=$datetime;
							$order->track_number=$track_number;
							$order->city=$request->city;
							$order->postal=$request->postel;
							$order->download_link = url('public').'/images/'.$file_name.".png";
							$order->download_link_compress = url('public').'/images/'.$file_name1.".png";
							$order->save();
							
							/* $order->update( [
								'download_link' => url('public').'/images/'.$file_name;
							] ); */
							if($errorMessage != '') {
								return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
									'successMessage' => __( 'frontend/shop.you_bought', [
										'name'  => $product->name,
										'price' => Product::formatPrice($priceInCent)
									] )
								] )->with('errorMessage', $errorMessage);
							}else {
								return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
									'successMessage' => __( 'frontend/shop.you_bought', [
										'name'  => $product->name,
										'price' => Product::formatPrice($priceInCent)
									] )
								] );

							}

				} else if ( $product->asWeight()) {

					$order = UserOrder::create( [
						'user_id'        => Auth::user()->id,
						'product_id'     => $product->id,
						'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
						'name'           => $product->name,
						'content'        => $product->content,
						'weight'         => $amount,
						'weight_char'    => $product->getWeightChar(),
						'price_in_cent'  => $product->price_in_cent,
						'totalprice'     => $priceInCent,
						// 'drop_info'      => $dropInfo,
						'delivery_price' => $deliveryMethodPrice,
						'delivery_name'  => $deliveryMethodName,
						'status'         => $status,
						'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
						'tracking_number'   => $request->input('tracking_number')
					] );

					event(new OrderWasCreated($order->id));

					if (! $product->isDigitalGoods()) {
						$tid->update( [ 'used' => 1 ] );
					}

					if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
						$this->saveShippingAddress( $order, $validated_address );
					}

					$product->update( [
						'sells'            => $product->sells + $amount,
						'weight_available' => $product->weight_available - $amount
					] );

					if ( $request->has( 'send_at' ) ) {
						$order->update( [
							'type_deliver' => 'desired_date',
							'deliver_at' => $request->input( 'send_at' )
						] );
						$errorMessage =  '';
					} else {
						if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
							$order->update( [
									'deliver_at' => Carbon::tomorrow()
								// 'deliver_at' => date('Y-m-d', strtotime(Carbon::tomorrow()))
							] );

							$errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
						} else {
							$order->update( [
									'deliver_at' => Carbon::now()
								// 'deliver_at' => date('Y-m-d', strtotime(Carbon::now()))
							] );
							$errorMessage =  '';
						}
					}

					Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

					if (! $isRefundingProduct && ! $product->isDigitalGoods() && !in_array($product->id,[53,54,55])) {
						$this->tidGenerationService->generateTidPDF($order->id);
					}

					Notification::create( [
						'custom_id' => Auth::user()->id,
						'type'      => 'order'
					] );

					if($errorMessage != '') {
						return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
							'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
								'name'             => $product->name,
								'amount_with_char' => $amount . $product->getWeightChar(),
								'totalprice'       => Product::formatPrice( $priceInCent ),
								'price'            => $product->getFormattedPrice()
							] )
						] )->with('errorMessage', $errorMessage);
					}else{
						return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
							'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
								'name'             => $product->name,
								'amount_with_char' => $amount . $product->getWeightChar(),
								'totalprice'       => Product::formatPrice( $priceInCent ),
								'price'            => $product->getFormattedPrice()
							] )
						] );
					}

				} else {
					for ( $i = 0; $i < $amount; $i++ ) {

						$productItem    = ProductItem::where( 'product_id', $product->id )->get()->first();
						$productContent = $productItem->content;
						$productItem->delete();

						$order = UserOrder::create( [
							'user_id'        => Auth::user()->id,
							'product_id'     => $product->id,
							'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
							'name'           => $product->name,
							'content'        => $productContent,
							'price_in_cent'  => $product->price_in_cent,
							'totalprice'     => $priceInCent,
							'weight'         => 0,
							'weight_char'    => '',
							'status'         => $status,
							'delivery_price' => $deliveryMethodPrice,
							'delivery_name'  => $deliveryMethodName,
							// 'drop_info'      => $dropInfo,
							'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
							'tracking_number'   => $request->input('tracking_number')
						] );

						event(new OrderWasCreated($order->id));

						if (! $product->isDigitalGoods() && !in_array($product->id,[53,54,55])) {
							$tid->update( [ 'used' => 1 ] );
						}

						if (! $isRefundingProduct && ! $product->isDigitalGoods() ) {
							$this->saveShippingAddress( $order, $validated_address );
						}

						$product->update( [
							'sells' => $product->sells + 1
						] );

						if ( $request->has( 'send_at' ) ) {
							$order->update( [
								'type_deliver' => 'desired_date',
								'deliver_at' => $request->input( 'send_at' )
							] );
							$errorMessage =  '';
						} else {
							if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
								$order->update( [
									'deliver_at' => Carbon::tomorrow()
								] );

								$errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
							} else {
								$order->update( [
									'deliver_at' => Carbon::now()
								] );
								$errorMessage =  '';
							}
						}

						Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

						if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
							$this->tidGenerationService->generateTidPDF($order->id);
						}

						Notification::create( [
							'custom_id' => Auth::user()->id,
							'type'      => 'order'
						] );


					}

					if($errorMessage != '') {
						return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
							'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
								'name'       => $product->name,
								'amount'     => $amount,
								'totalprice' => Product::formatPrice( $priceInCent ),
								'price'      => $product->getFormattedPrice()
							] )
						] )->with('errorMessage', $errorMessage);
					}else {
						return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
							'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
								'name'       => $product->name,
								'amount'     => $amount,
								'totalprice' => Product::formatPrice( $priceInCent ),
								'price'      => $product->getFormattedPrice()
							] )
						] );
					}
				} 
			 }else {

                return redirect()->route( 'shop' )->with( [
                    'errorMessage' => __( 'frontend/shop.not_enought_money' )
                ] );

            }
			
	
		
		
		
		}
        
    }
    public function showShopPage()
    {
       
        return redirect('/');
        $categories = ProductCategory::orderByDesc( 'created_at' )->get();

        return response()->view( 'frontend/shop.shop', [
            'categories' => $categories
        ], 200 )->header('Cache-Control', 'public, max-age: 900');
    }

    public function buyProductForm( Request $request, $pId = null, $pAmount = null )
    {
        if ( !Auth::check() ) {
            return redirect()->route( 'shop' )->with( [
                'errorMessage' => __( 'frontend/shop.must_logged_in' )
            ] );
        }

        $backAction = false;
        if ( $pId != null && $pAmount != null ) {
            $backAction = true;
        }

        if ( $request->getMethod() == 'POST' || $backAction ) {
            if ( $backAction ) {
                $productId = $pId;
            } else {
                $productId = $request->get( 'product_id' );
            }

            $product = Product::where( 'id', $productId )->get()->first();
            
           

            if ( $product == null ) {
                return redirect()->back()
                    ->with([
                        'errorMessage' => __( 'frontend/shop.product_not_found' )
                    ]);
            }
            $isRefundingProduct = in_array($product->name, ['LIT für Refund','Return To Sender (RTS)','Special Amazon RTS','Express Scans (LIT/REFUSED/DELIVERY)']);
            $isRandomProduct = in_array($product->name, ['LIT für Refund', 'LIT für Filling']);
            $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);

            if ( $backAction ) {
                $amount = $pAmount;
            } else {
                $amount = intval( $request->get( 'product_amount' ) );
            }

            if ($product->isDigitalGoods() && $amount < $product->getOrderMinimum()) {

                return redirect()->back()->with( [
                    'errorMessage' => sprintf('Die Mindestabnahme beträgt %d Accounts.', $product->getOrderMinimum())
                ] );
            } elseif ($product->isDigitalGoods() && ! $product->isAvailableAmount($amount)) {

                // return redirect()->back()->with( [
                //     'errorMessage' => 'Dieses Produkt ist ausverkauft oder die Bestellung überschreitet die vorhandene Menge.'
                // ] );
                return redirect()->back()->with( [
                    'errorMessage' => 'Ihre Bestellung überschreitet die Anzahl an verfügbaren Accounts.'
                ] );
            }

            if ($product->isUnlimited() && ! $product->isDigitalGoods()) {
                $amount = 1;
            } elseif ($product->asWeight() && $amount > $product->getWeightAvailable()) {
                $amount = $product->getWeightAvailable();
            } elseif (! $product->asWeight() && $amount > $product->getStock()) {
                $amount = $product->getStock();
            }

            if ( $amount <= 0 ) {
                return redirect()->route( 'shop' );
            }
            $totalPrice = $isBoxingProduct ? 0 : ($product->price_in_cent * $amount);
            $totalPriceHtml = Product::formatPrice($totalPrice);
            $replaceEntry = FAQ::where( 'id', Setting::get( 'shop.replace_rules' ) )->first();
            
           
            if($product->name=='Digitaler Einlieferungsbeleg'){
                //echo $productId;
                //die();
                return view( 'frontend/shop.product_confirm_buynew', [
                'product'        => $product,
                'amount'         => $amount,
                'totalPrice'     => $totalPrice,
                'totalPriceHtml' => $totalPriceHtml,
                'replaceEntry'   => $replaceEntry,
                'category' => $product->category->slug,
                'isRefundingProduct'    => $isRefundingProduct,
                'isBoxingProduct'       => $isBoxingProduct,
                'isRandomProduct'       => $isRandomProduct,
            ] );
            
            //die();
            }

            return view( 'frontend/shop.product_confirm_buy', [
                'product'        => $product,
                'amount'         => $amount,
                'totalPrice'     => $totalPrice,
                'totalPriceHtml' => $totalPriceHtml,
                'replaceEntry'   => $replaceEntry,
                'category' => $product->category->slug,
                'isRefundingProduct'    => $isRefundingProduct,
                'isBoxingProduct'       => $isBoxingProduct,
                'isRandomProduct'       => $isRandomProduct,
            ] );
        }

        return redirect()->route( 'shop' );
    }

    public function buyProductConfirmForm( Request $request )
    {
    
        if (! Auth::check()) {
            return redirect()
                ->route('shop')
                ->with(
                    [
                        'errorMessage' => __( 'frontend/shop.must_logged_in' )
                    ]
                );
        }

        $productId = $request->get('product_id');
        
        

        if ($request->getMethod() == 'POST' && ! is_null($productId)) {
            $product   = Product::where('id', $productId)->first();
            if (! $product instanceof Product) {
                return redirect()
                    ->route( 'shop' )
                    ->with([
                        'errorMessage' => __( 'frontend/shop.product_not_found' )
                    ]);
            }

            $amount = intval($request->get('product_amount'));

            $isRefundingProduct = in_array($product->name, ['LIT für Refund','Return To Sender (RTS)','Special Amazon RTS','Express Scans (LIT/REFUSED/DELIVERY)']);
            $isRandomProduct = in_array($product->name, ['LIT für Refund', 'LIT für Filling','Return To Sender (RTS)','Special Amazon RTS','Express Scans (LIT/REFUSED/DELIVERY)']);
            $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);
            if (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) {

                $tid = Tid::where( 'product_id', $product->id )->where(['used' => '0'])->firstOrFail();
            }

            $validated_address = Validator::make( $request->all(), [
                'first_name'        => 'bail|required|max:255',
                'last_name'         => 'bail|required|max:255',
                'street'            => 'bail|required|max:255',
                'zip'               => 'bail|required|max:255',
                'city'              => 'bail|required|max:255',
                'country'           => 'bail|required|max:255',
                'sender_first_name' => 'bail|required|max:255',
                'sender_last_name'  => 'bail|required|max:255',
                'sender_street'     => 'bail|required|max:255',
                'sender_zip'        => 'bail|required|max:255',
                'sender_city'       => 'bail|required|max:255',
                'sender_country'    => 'bail|required|max:255',
                'shipping_time'     => 'bail|required',
                'send_at'           => 'bail|required_if:shipping_time,desired_date',
                'receipt'           => 'nullable|in:yes,no'

            ], [

                'first_name.required'        => __('frontend/shop.validator.first_name' ),
                'last_name.required'         => __('frontend/shop.validator.last_name' ),
                'street.required'            => __('frontend/shop.validator.street' ),
                'zip.required'               => __('frontend/shop.validator.zip' ),
                'city.required'              => __('frontend/shop.validator.city' ),
                'country.required'           => __('frontend/shop.validator.country' ),
                'sender_first_name.required' => __('frontend/shop.validator.sender_first_name' ),
                'sender_last_name.required'  => __('frontend/shop.validator.sender_last_name' ),
                'sender_street.required'     => __('frontend/shop.validator.sender_street' ),
                'sender_zip.required'        => __('frontend/shop.validator.sender_zip' ),
                'sender_city.required'       => __('frontend/shop.validator.sender_city' ),
                'sender_country.required'    => __('frontend/shop.validator.sender_country' ),
                'shipping_time.required'     => __('frontend/shop.validator.shipping_time' ),
                'send_at.required'           => __('frontend/shop.validator.send_at' ),
            ]);


            if ($isRefundingProduct) {
                if(in_array($product->id,[53,54,55])){
                    $validated_address = Validator::make( $request->all(), [


                        'shipping_time'     => 'bail|required',
                        'send_at'           => 'bail|required_if:shipping_time,desired_date',
                        'receipt'           => 'nullable|in:yes,no'
                    ], [
                        'shipping_time.required'     => __('frontend/shop.validator.shipping_time' ),
                        'send_at.required'           => __('frontend/shop.validator.send_at' ),
                    ]);
                }
                else{
                    $validated_address = Validator::make( $request->all(), [

                        'tracking_number'   => 'bail|required|max:255',
                        'shipping_time'     => 'bail|required',
                        'send_at'           => 'bail|required_if:shipping_time,desired_date',
                        'receipt'           => 'nullable|in:yes,no'
                    ], [
                        'shipping_time.required'     => __('frontend/shop.validator.shipping_time' ),
                        'send_at.required'           => __('frontend/shop.validator.send_at' ),
                    ]);
                }

            }
            if ($isBoxingProduct & in_array($product->category->slug, ['welt-nachnahme','morty-nachnahme'])) {
                $validated_address = Validator::make( $request->all(), [
                    'product_name'                  => 'bail|required|max:255',
                    'product_size'                  => 'bail|required|max:255',
                    'product_weight'                => 'bail|required|max:255',
                    'product_payment_amount'        => 'bail|required|integer',
                    'product_package_labels_link'   => 'bail|required|max:255',
                    'amazon_product_link'           => 'bail|required|max:255',
                    'service_fee'                   => 'bail|required|integer',
                ]);
            }
            if ($isBoxingProduct & !in_array($product->category->slug, ['welt-nachnahme','morty-nachnahme'])) {
                $validated_address = Validator::make( $request->all(), [
                    'product_name'                  => 'bail|required|max:255',
                    'product_size'                  => 'bail|required|max:255',
                    'product_weight'                => 'bail|required|max:255',
                    'product_payment_amount'        => 'bail|required|integer|min:500',
                    'product_package_labels_link'   => 'bail|required|max:255',
                    'amazon_product_link'           => 'bail|required|max:255',
                    'service_fee'                   => 'bail|required|integer',
                ]);
            }

            if ($product->isDigitalGoods()) {

                $validated_address = Validator::make( $request->all(), [
                    'product_amount'    => 'integer|required|min:' . $product->getOrderMinimum(),
                ], [
                    'product_amount.*'  => sprintf('Die Mindestabnahme beträgt %d Accounts.', $product->getOrderMinimum())
                ]);
            }

            if ($validated_address->fails()) {

                return redirect()
                    ->route( 'buy-product', [ $productId, $amount ] )
                    ->withErrors( $validated_address)
                    ->withInput();
            }

            $dropInfo            = '';
            $status              = 'nothing';
            $deliveryMethodId    = 0;
            $deliveryMethodName  = "";
            $deliveryMethodPrice = 0;
            $extraCosts          = 0;


            if ( $product->dropNeeded() ) {

                $status           = 'pending';
                $deliveryMethodId = $request->get( 'product_delivery_method' ) ?? 0;
                $deliveryMethod   = DeliveryMethod::where( 'id', $deliveryMethodId )->get()->first();

                if ( $deliveryMethod == null && ! $product->isDigitalGoods() && ! $isBoxingProduct && ! $isRandomProduct  ) {

                    return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                        'errorMessage' => __( 'frontend/shop.delivery_method_needed' ),
                        'productDrop'  => $dropInfo
                    ] );

                } elseif (! $product->isDigitalGoods() && ! $isBoxingProduct && ! $isRandomProduct  ) {

                    $extraCosts          += $deliveryMethod->price;
                    $deliveryMethodName  = $deliveryMethod->name;
                    $deliveryMethodPrice = $deliveryMethod->price;
                }

                if ( $request->exists( 'shipping_time' ) && $request->shipping_time == 'desired_date' ) {
                    $extraCosts += 500;
                }

                // if ( $request->get( 'product_drop' ) == null ) {
                //
                //     return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                //         'errorMessage' => __( 'frontend/shop.order_note_needed' ),
                //         'productDrop'  => $dropInfo
                //     ] );
                //
                // } else if ( strlen( $request->get( 'product_drop' ) ) > 500 ) {
                //
                //     return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                //         'errorMessage' => __( 'frontend/shop.order_note_long', [
                //             'charallowed' => 500
                //         ] ),
                //         'productDrop'  => $dropInfo
                //     ] );
                //
                // } else {
                //
                //     $dropInfo = $request->get( 'product_drop' );
                //
                // }

            }

            try {
                if ($request->input('receipt') !== null && $request->input('receipt') === 'yes' && ! $product->isDigitalGoods()) {
                    $zip = str_pad($request->input('zip'), 5, '0', STR_PAD_LEFT);

                    $packstation = Packstation::query()
                        ->where('zip', 'LIKE', '%' . $zip . '%')
                        ->first();

                    if ($packstation instanceof Packstation) {
                        TidPackStation::query()
                            ->create([
                                'tid_id'            => $tid->id,
                                'packstation_id'    => $packstation->id,
                            ]);

                        // $extraCosts += 500; TODO: enable if after free period
                    }
                }
            } catch (Throwable $e) { }

            if ($isBoxingProduct) {
                $extraCosts += 3000;
                // if($product->category->slug=='morty-nachnahme' || $product->category->slug=='welt-nachnahme' || $product->category->slug=='lalo-nachnahme'){
                //     $extraCosts += 3000; // 0 EUR
                // }

                // else{
                //     $extraCosts += 2000; // 20 EUR
                // }


            }

            if ( in_array($product->id,[53,54,55]) || ($amount > 0 && $product->isAvailableAmount($amount)) ) {

                if ( $product->isUnlimited() ) {
                    $amount = 1;
                }
                if($isBoxingProduct){
                    if($product->category->slug=='morty-nachnahme' || $product->category->slug=='welt-nachnahme'){
                        $otheramount =  0;
                    }
                    elseif($product->category->slug=='lalo-nachnahme'){
                        $otheramount =  $request->input('product_payment_amount', 500) * 7;
                    }
                    else{
                        $otheramount =  $request->input('product_payment_amount', 500) * 10;
                    }

                }
                else{
                    $otheramount = $product->price_in_cent;
                }
                $priceInCent = $amount * $otheramount;
                $priceInCent += $extraCosts;

                if ( Auth::user()->balance_in_cent >= $priceInCent ) {

                    $newBalance = Auth::user()->balance_in_cent - $priceInCent;

                    Auth::user()->update( [
                        'balance_in_cent' => $newBalance
                    ] );

                    if ( $product->isUnlimited() || $product->isDigitalGoods() ) {

                        $productItems = ProductItem::where('product_id', $product->id)->limit($amount ?? 5)->get();
                        $productItemsContent = implode('', $productItems->pluck(['content'])->toArray());
                        $orderDetails = [
                            'user_id'        => Auth::user()->id,
                            'product_id'     => $product->id,
                            'tid_id'         => (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) ? $tid->id : 0,
                            'name'           => $product->name,
                            'content'        => ! $product->isDigitalGoods() ? $product->content : $productItemsContent,
                            'price_in_cent'  => $product->price_in_cent,
                            'totalprice'     => $priceInCent,
                            // 'drop_info'      => $dropInfo,
                            'delivery_price' => $deliveryMethodPrice,
                            'delivery_name'  => $deliveryMethodName,
                            'status'         => $status,
                            'weight'         => ! $product->isDigitalGoods() ? 0 : $amount,
                            'weight_char'    => '',
                            'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                            'tracking_number'   => !in_array($product->id,[53,54,55]) ? $request->input('tracking_number'):0,
                            'qrcode'   => $request->input('qrcode'),
                        ];

                        if ($isBoxingProduct) {
                            $orderDetails['product_name'] = $request->input('product_name');
                            $orderDetails['product_size'] = $request->input('product_size');
                            $orderDetails['product_weight'] = $request->input('product_weight', 0);
                            $orderDetails['product_payment_amount'] = $request->input('product_payment_amount', 5000);
                            //$orderDetails['product_payment_amount'] = $priceInCent/10;
                            $orderDetails['product_package_labels_link'] = $request->input('product_package_labels_link');
                            $orderDetails['amazon_product_link'] = $request->input('amazon_product_link');
                            $orderDetails['total_price_in_btc'] = $this->bitcoinConverterService->toBitcoin(($priceInCent/100), Setting::getShopCurrency());
                        }

                        $order = UserOrder::create($orderDetails);

                        event(new OrderWasCreated($order->id));

                        if (! $product->isDigitalGoods() && ! $isBoxingProduct && !in_array($product->id,[53,54,55])) {
                            $tid->update([ 'used' => 1 ]);
                        }

                        if (! $isBoxingProduct && ! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->saveShippingAddress( $order, $validated_address );
                        }

                        if (! $product->isDigitalGoods()) {
                            $product->update( [
                                'sells' => $product->sells + 1
                            ] );
                        } else {
                            foreach($productItems as $item) {
                                $item->delete();
                            }

                            $product->update( [
                                'sells'            => $product->sells + $amount
                            ] );
                        }

                        if ( $request->has( 'send_at' ) ) {
                            $order->update( [
                                'type_deliver' => 'desired_date',
                                'deliver_at'   => $request->input( 'send_at' )
                            ] );
                            $errorMessage =  '';
                        } else {
                            if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                $order->update( [
                                    'deliver_at' => Carbon::tomorrow()
                                ] );

                                $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                            } else {
                                $order->update( [
                                    'deliver_at' => Carbon::now()
                                ] );
                                $errorMessage =  '';
                            }
                        }

                        Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                        if (! $isBoxingProduct && ! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->tidGenerationService->generateTidPDF($order->id);
                        }

                        Notification::create( [
                            'custom_id' => Auth::user()->id,
                            'type'      => 'order'
                        ] );


                        if($errorMessage != '') {
                                return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                    'successMessage' => __( 'frontend/shop.you_bought', [
                                        'name'  => $product->name,
                                        'price' => Product::formatPrice($priceInCent)
                                    ] )
                                ] )->with('errorMessage', $errorMessage);
                            }else {
                                return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                    'successMessage' => __( 'frontend/shop.you_bought', [
                                        'name'  => $product->name,
                                        'price' => Product::formatPrice($priceInCent)
                                    ] )
                                ] );

                            }

                    } else if ( $product->asWeight()) {

                        $order = UserOrder::create( [
                            'user_id'        => Auth::user()->id,
                            'product_id'     => $product->id,
                            'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
                            'name'           => $product->name,
                            'content'        => $product->content,
                            'weight'         => $amount,
                            'weight_char'    => $product->getWeightChar(),
                            'price_in_cent'  => $product->price_in_cent,
                            'totalprice'     => $priceInCent,
                            // 'drop_info'      => $dropInfo,
                            'delivery_price' => $deliveryMethodPrice,
                            'delivery_name'  => $deliveryMethodName,
                            'status'         => $status,
                            'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                            'tracking_number'   => $request->input('tracking_number')
                        ] );

                        event(new OrderWasCreated($order->id));

                        if (! $product->isDigitalGoods()) {
                            $tid->update( [ 'used' => 1 ] );
                        }

                        if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->saveShippingAddress( $order, $validated_address );
                        }

                        $product->update( [
                            'sells'            => $product->sells + $amount,
                            'weight_available' => $product->weight_available - $amount
                        ] );

                        if ( $request->has( 'send_at' ) ) {
                            $order->update( [
                                'type_deliver' => 'desired_date',
                                'deliver_at' => $request->input( 'send_at' )
                            ] );
                            $errorMessage =  '';
                        } else {
                            if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                $order->update( [
                                        'deliver_at' => Carbon::tomorrow()
                                    // 'deliver_at' => date('Y-m-d', strtotime(Carbon::tomorrow()))
                                ] );

                                $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                            } else {
                                $order->update( [
                                        'deliver_at' => Carbon::now()
                                    // 'deliver_at' => date('Y-m-d', strtotime(Carbon::now()))
                                ] );
                                $errorMessage =  '';
                            }
                        }

                        Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                        if (! $isRefundingProduct && ! $product->isDigitalGoods() && !in_array($product->id,[53,54,55])) {
                            $this->tidGenerationService->generateTidPDF($order->id);
                        }

                        Notification::create( [
                            'custom_id' => Auth::user()->id,
                            'type'      => 'order'
                        ] );

                        if($errorMessage != '') {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
                                    'name'             => $product->name,
                                    'amount_with_char' => $amount . $product->getWeightChar(),
                                    'totalprice'       => Product::formatPrice( $priceInCent ),
                                    'price'            => $product->getFormattedPrice()
                                ] )
                            ] )->with('errorMessage', $errorMessage);
                        }else{
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
                                    'name'             => $product->name,
                                    'amount_with_char' => $amount . $product->getWeightChar(),
                                    'totalprice'       => Product::formatPrice( $priceInCent ),
                                    'price'            => $product->getFormattedPrice()
                                ] )
                            ] );
                        }

                    } else {
                        for ( $i = 0; $i < $amount; $i++ ) {

                            $productItem    = ProductItem::where( 'product_id', $product->id )->get()->first();
                            $productContent = $productItem->content;
                            $productItem->delete();

                            $order = UserOrder::create( [
                                'user_id'        => Auth::user()->id,
                                'product_id'     => $product->id,
                                'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
                                'name'           => $product->name,
                                'content'        => $productContent,
                                'price_in_cent'  => $product->price_in_cent,
                                'totalprice'     => $priceInCent,
                                'weight'         => 0,
                                'weight_char'    => '',
                                'status'         => $status,
                                'delivery_price' => $deliveryMethodPrice,
                                'delivery_name'  => $deliveryMethodName,
                                // 'drop_info'      => $dropInfo,
                                'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                                'tracking_number'   => $request->input('tracking_number')
                            ] );

                            event(new OrderWasCreated($order->id));

                            if (! $product->isDigitalGoods() && !in_array($product->id,[53,54,55])) {
                                $tid->update( [ 'used' => 1 ] );
                            }

                            if (! $isRefundingProduct && ! $product->isDigitalGoods() ) {
                                $this->saveShippingAddress( $order, $validated_address );
                            }

                            $product->update( [
                                'sells' => $product->sells + 1
                            ] );

                            if ( $request->has( 'send_at' ) ) {
                                $order->update( [
                                    'type_deliver' => 'desired_date',
                                    'deliver_at' => $request->input( 'send_at' )
                                ] );
                                $errorMessage =  '';
                            } else {
                                if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                    $order->update( [
                                        'deliver_at' => Carbon::tomorrow()
                                    ] );

                                    $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                                } else {
                                    $order->update( [
                                        'deliver_at' => Carbon::now()
                                    ] );
                                    $errorMessage =  '';
                                }
                            }

                            Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                            if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                                $this->tidGenerationService->generateTidPDF($order->id);
                            }

                            Notification::create( [
                                'custom_id' => Auth::user()->id,
                                'type'      => 'order'
                            ] );


                        }

                        if($errorMessage != '') {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
                                    'name'       => $product->name,
                                    'amount'     => $amount,
                                    'totalprice' => Product::formatPrice( $priceInCent ),
                                    'price'      => $product->getFormattedPrice()
                                ] )
                            ] )->with('errorMessage', $errorMessage);
                        }else {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
                                    'name'       => $product->name,
                                    'amount'     => $amount,
                                    'totalprice' => Product::formatPrice( $priceInCent ),
                                    'price'      => $product->getFormattedPrice()
                                ] )
                            ] );
                        }
                    }

                    /*if(UserOrder::create([
                        'user_id' => Auth::user()->id,
                        'name' => $product->name,
                        'content' => $productContent,
                        'status' => $status,
                        'price_in_cent' => $product->price_in_cent,
                        'drop_info' => $dropInfo
                    ])) {
                        Setting::set('shop.total_sells', Setting::get('shop.total_sells', 0) + 1);

                        $product->update([
                            'sells' => $product->sells + 1
                        ]);

                        Notification::create([
                            'custom_id' => Auth::user()->id,
                            'type' => 'order'
                        ]);

                        return redirect()->route('orders-with-pageNumber', 1)->with([
                            'successMessage' => __('frontend/shop.you_bought', [
                                'name' => $product->name,
                                'price' => $product->getFormattedPrice()
                            ])
                        ]);
                    } else {
                        return redirect()->route('buy-product', [
                            'id' => $productId,
                            'amount' => $amount
                        ])->with([
                            'errorMessage' => __('frontend/shop.buy_error')
                        ]);
                    }*/

                } else {

                    return redirect()->route( 'buy-product', [
                        'id'     => $productId,
                        'amount' => $amount
                    ] )->with( [
                        'errorMessage' => __( 'frontend/shop.not_enought_money' )
                    ] );

                }
            } else {

                return redirect()->route( 'shop' )->with( [
                    'errorMessage' => __( 'frontend/shop.product_not_available' )
                ] );

            }

        }

        return redirect()->route( 'shop' );
    }


    /**
     * @param $order
     * @param $validated_address
     */
    public function saveShippingAddress( $order, $validated_address )
    {
        $validated_address = $validated_address->valid();
        $order->address()->create( $validated_address );
    }


    public function showProductPage( $productId )
    {
        $product = Product::with(['benifits'])->where('id', $productId)->first();

        if ( $product != null ) {
            return view( 'frontend/shop.products_category', [
                'products'        => [$product],
                'productCategory' => $product->getCategory() ?? (object)[ 'name' => __( 'frontend/shop.uncategorized' ) ]
            ] );
        }

        return view( 'frontend/shop.product_not_found' );
    }

    public function showProductCategoryPage( $slug = null )
    {
        if ( $slug == null && strtolower( $slug ) != 'uncategorized' ) {
            return redirect()->route( 'shop' );
        }

        $productCategory = ProductCategory::where( 'slug', $slug )->get()->first();

        if ( $productCategory == null && $slug != 'uncategorized' ) {
            return redirect()->route( 'shop' );
        } else if ( $productCategory == null ) {
            $products = Product::with(['benifits'])->getUncategorizedProducts();
        } else {
            $products = Product::with(['benifits'])->where( 'category_id', $productCategory->id )->get()->all();
        }

        return view( 'frontend/shop.products_category', [
            'products'        => $products,
            'productCategory' => $productCategory ?? (object)[ 'name' => __( 'frontend/shop.uncategorized' ) ]
        ] );
    }

    public function createTidFile( $order_id )
    {


        $order = UserOrder::find( $order_id );

        $original_name = $order->tids->tid;
        $file_loc = $order->tids->loc;

        $offset_x = 170;
        $offset_y = 30;
        if ( $file_loc == 'eu' ) {
            $offset_x = 35;
            $offset_y = 22;
        }

        $path = Storage::disk( 'public' )->path( "tid/$order->product_id/$original_name" );

        $pdf = new Fpdi();
        $pdf->setSourceFile( $path );

        $tplIdx = $pdf->importPage( 1 );
        $specs  = $pdf->getTemplateSize( $tplIdx );
        $pdf->AddPage( $specs[ 'height' ] > $specs[ 'width' ] ? 'P' : 'L' );
        $pdf->useTemplate( $tplIdx );

        $pdf->SetFont( 'arial', '', '10' );
        $pdf->SetTextColor( 0, 0, 0 );

        $order = UserOrder::find( $order_id );

        setlocale( LC_ALL, 'de_DE' );

        $shipping = Setting::where( 'key', 'like', 'shipping%' )->get();

        $settings = [];

        foreach ( $shipping->pluck( 'value', 'key' )->toArray() as $key => $setting )
            $settings[ explode( '.', $key )[ 1 ] ] = $setting;


        // sender_first_name sender_last_name
        $pdf->SetXY( $offset_x, $offset_y );
        $pdf->Write( 0, $this->codeToISO(
            $order->address->sender_first_name . ' ' . $order->address->sender_last_name
        ) );

        // sender_street
        $pdf->SetXY( $offset_x, $offset_y + 5 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_street ) );

        // sender_zip
        $pdf->SetXY( $offset_x, $offset_y + 10 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_zip ) );

        // sender_city
        $pdf->SetXY( $offset_x, $offset_y + 15 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_city ) );

        // sender_country
        $pdf->SetXY( $offset_x, $offset_y + 20 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_country ) );


        // first_name last_name
        $pdf->SetXY( $offset_x, $offset_y + 30 );
        $pdf->Write( 0, $this->codeToISO( $order->address->first_name . ' ' . $order->address->last_name ) );

        // street
        $pdf->SetXY( $offset_x, $offset_y + 35 );
        $pdf->Write( 0, $this->codeToISO( $order->address->street ) );

        // zip
        $pdf->SetXY( $offset_x, $offset_y + 40 );
        $pdf->Write( 0, $this->codeToISO( $order->address->zip ) );

        // city
        $pdf->SetXY( $offset_x, $offset_y + 45 );
        $pdf->Write( 0, $this->codeToISO( $order->address->city ) );

        // country
        $pdf->SetXY( $offset_x, $offset_y + 50 );
        $pdf->Write( 0, $this->codeToISO( $order->address->country ) );


        $path = "order/$order_id";

        Storage::disk( 'public' )->makeDirectory( $path );

        $pdf->Output( public_path( "storage/order/$order_id/$original_name" ), 'F' );

        return back()->with( 'success', 'You have successfully upload file.' );
    }

    public function codeToISO( $str )
    {
        return iconv( 'UTF-8', 'ISO-8859-1', $str );
    }

    public function showdescription( $id )
    {
        $data = Product::where( 'id', $id )->get()->first();
        // print_r($data);exit;
        $name          = $data->name;
        $descrption    = $data->description;
        $decripteddesc = nl2br($descrption);
        return response()->json( [ 'name' => $name, 'descrption' => $decripteddesc ] );
        // print_r($data);
    }
}
