<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    
    use Denpa\Bitcoin\Client as BitcoinClient;

    use App\Models\Setting;
    use App\Models\User;
    use App\Models\Bonus;
    use App\Classes\BitcoinAPI;
    use App\Services\BtcpayApiService;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

    class UserTransaction extends Model
    {
        protected $table = 'users_transactions';

        protected $fillable = [
            'user_id', 'wallet', 'txid', 'confirmations', 'status', 'amount', 'amount_cent', 'payment_method'
        ];

        public static function getById($id) {
            return self::where('id', $id)->first();
        }

        public function getFormattedAmount() {
            if(strtolower($this->getPaymentMethod()) == 'btc') {
                return $this->getFormattedBTC() . ' (' . $this->getFormattedPrice() . ')';
            }

            return $this->getFormattedPrice();
        }

        public function getFormattedBTC() {
            return number_format($this->amount / 100000, 5, ',', '.') . ' BTC';
        }
        
        public function getFormattedPrice() {
            return number_format($this->amount_cent / 100, 2, ',', '.') . ' ' . Setting::getShopCurrency();
        }

        public function isPaid() {
            return strtolower($this->status) == 'paid';
        }

        public function isPending() {
            return strtolower($this->status) == 'pending';
        }

        public function isWaiting() {
            return strtolower($this->status) == 'waiting';
        }

        public function getPaymentMethod() {
            return strtolower($this->payment_method);
        }

        public function getUsername() {
            $name = '-/-';

            $user = User::where('id', $this->user_id)->get()->first();

            if($user != null) {
                $name = $user->username;
            }

            return $name;
        }

        public function getDate() {
            return $this->created_at->format('d.m.Y');
        }
        
        public function updateWhenPaidBtc() 
        {
            
            $btcpay = new BtcpayApiService();
            $result = $btcpay->getInvoice($this->txid);
            
            if(count($result)>0){
                
                if($result['status']=='Expired'){
                    
                    $this->update(['status' => 'expired']);
                }
                elseif($result['status']=='Settled'){
                    if($this->status == 'paid'){
                        return false;
                    }
                    $this->update(['status' => 'paid']);
                    $user = User::where('id', $this->user_id)->get()->first();

                        if($user != null) {
                            $balance_in_cent = $user->balance_in_cent;

                            $user->update([
                                'balance_in_cent' => $balance_in_cent + $this->amount_cent
                            ]);
                        }
                        
                     
                }
                return true;
            }
			
            // try {
            //     if(strlen($this->wallet) && $this->status=='waiting') {
                   
            //         $bitcoind = BitcoinAPI::getBitcoinClient();
                    
            //         $receivedInfo = $bitcoind->listreceivedbyaddress(0, true, true, (string) $this->wallet)[0];
                  
            //         $rAmount = $receivedInfo['amount'];
            //         if($rAmount > 0) {
            //             $txIDs = $receivedInfo['txids'];

            //             $amountCent = intval(BitcoinAPI::convertBtc($rAmount) * floatval(Setting::get('shop.bonus_in_percent', "1")));
                        
            //             $bonuses = Bonus::orderByDESC('min_amount')->get();
                        
            //             foreach($bonuses as $bonus) {
            //                 if($amountCent >= $bonus->min_amount) {
            //                     $amountCent = $amountCent * floatval($bonus->percent);
            //                     break;
            //                 }
            //             }
                        
            //             $this->update([
            //                 'status' => 'pending',
            //                 'amount' => intval($rAmount * 100000),
            //                 'amount_cent' => intval($amountCent),
            //                 'txid' => implode(',', $txIDs)
            //             ]);
                        
            //             return true;
            //         }
            //     } else if(strlen($this->wallet) > 0 && $this->status=='pending') {
                    
            //         $bitcoind = BitcoinAPI::getBitcoinClient();
                    
            //         $receivedInfo = $bitcoind->listreceivedbyaddress(Setting::get('shop.btc_confirms_needed'), true, true, (string) $this->wallet)[0];

            //         $rAmount = $receivedInfo['amount'];
			// 		//dd($rAmount);
            //         if($rAmount > 0) {
            //             $this->update([
            //                 'status' => 'paid'
            //             ]);

            //             $user = User::where('id', $this->user_id)->get()->first();

            //             if($user != null) {
            //                 $balance_in_cent = $user->balance_in_cent;

            //                 $user->update([
            //                     'balance_in_cent' => $balance_in_cent + $this->amount_cent
            //                 ]);
            //             }
                        
            //             return true;
            //         }
            //     }
                
            //     return false;
            // } catch (Throwable $e) {
            //     dd($e);
            //     Log::error($e);
             
            //     return false;
            // }
        }
    }