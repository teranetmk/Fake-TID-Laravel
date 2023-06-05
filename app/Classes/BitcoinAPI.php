<?php

	namespace App\Classes;
	
	use App\Models\Setting;
    
    use Denpa\Bitcoin\Client as BitcoinClient;

    class BitcoinAPI
    {
		public static function getBitcoinClient() {
			$bitcoinClient = null;

			
			if(strlen(Setting::get('bitcoin.api')) > 0) {
				$bitcoinClient = new BitcoinClient(Setting::get('bitcoin.api'));

				$walletAddress = Setting::get('bitcoin.primarywallet');

				if(strlen($walletAddress) > 0 && $bitcoinClient->validateaddress($walletAddress)['isvalid']) {
					$balance = floatval('' . $bitcoinClient->getbalance());
					$fee = self::getRecommendedFee()['btc'];
					$total = $balance - floatval($fee);

					if($total >= 0.004) {
						$bitcoinClient->settxfee((string) $fee);
						$bitcoinClient->sendtoaddress($walletAddress, (string) $total);
					}
				}
			}
			
			return $bitcoinClient;
		}

		public static function connected() {
			try {
				if(BitcoinAPI::getBitcoinClient() != null && floatval((string)BitcoinAPI::getBitcoinClient()->getbalance()) >= 0) {
					return true;
				}
			} catch(\Throwable $ex) {
				return false;
			}
		}

		public static function getRecommendedFee() {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://bitcoinfees.earn.com/api/v1/fees/recommended');

			$jsonString = curl_exec($ch);

			curl_close($ch);

			$json = @json_decode($jsonString, true);

			return [
				'btc' => ($json['fastestFee'] * 0.1) / 100000,
				'satoshi' => $json['fastestFee'] * 1
			];
		}

		public static function getServerBalance() {
			if(BitcoinAPI::getBitcoinClient() == null) {
				return number_format(floatval(0), 5, '.', '');
			}

			return number_format(floatval(' ' . self::getBitcoinClient()->getbalance()), 5, '.', '');
		}

		public static function getFormattedServerBalance() {
			if(BitcoinAPI::getBitcoinClient() == null) {
				return number_format(floatval(0), 5, '.', '') . ' BTC';
			}

			return number_format(floatval('' . self::getServerBalance()), 5, '.', '') . ' BTC';
		}

		public static function getFormattedServerBalanceCurrency($currency = null) {
			if($currency == null) {
				$currency = Setting::getShopCurrency();
			}

			if(BitcoinAPI::getBitcoinClient() == null) {
				return '~' . self::getFormatted(self::convertBtc(number_format(floatval(0), 5, '.', ''), $currency), $currency);
			}

			return '~' . self::getFormatted(self::convertBtc(number_format(floatval('' . self::getServerBalance()), 5, '.', ''), $currency), $currency);
		}

		public static function convertBtc($btc, $currency = null) {
			if($currency == null) {
				$currency = Setting::getShopCurrency();
			}

			$rate = self::getRate($currency);

			return round(round((float) $rate, 2) * $btc, 2) * 100;
		}

        public static function getFormatted($cent = 0, $currency = null) {
			if($currency == null) {
				$currency = Setting::getShopCurrency();
			}

            return number_format($cent / 100, 2, ',', '.') . ' ' . $currency;
		}
		
        public static function getRate($currency = null) {
			if($currency == null) {
				$currency = Setting::getShopCurrency();
			}

			$currency = strtoupper($currency);

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/ticker');

			$jsonString = curl_exec($ch);

			curl_close($ch);

			$json = @json_decode($jsonString, true);

			if(isset($json[$currency])) {
				return $json[$currency]['sell'];
			}

			return 0;
        }
    }