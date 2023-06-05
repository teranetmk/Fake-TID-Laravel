<?php

    namespace App\Http\Controllers\UserPanel;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    use App\Models\UserOrder;
    use App\Models\Coupon;
    use App\Models\UserTransaction;
    
    use App\Classes\BitcoinAPI;

    use App\Rules\RuleCouponRedeem;
    
    use Validator;
    use Hash;

    class UserPanelController extends Controller
    {
        public function __construct() {
            $this->middleware('auth');
        }

        public function showUserDashboard()
        {
            return view('frontend/userpanel.home');
        }

        public function showSettingsPage() {
            return view('frontend/userpanel.settings');
        }
        
        public function redirectToSettingsPage() 
        {
            return redirect()->route('settings');
        }

        public function redeemCoupon(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'coupon_redeem_code' => new RuleCouponRedeem()
                ]);

                if(!$validator->fails()) {
                    $coupon = Coupon::where('code', $request->get('coupon_redeem_code'))->get()->first();

                    if($coupon != null) {
                        if(!Auth::user()->hasCouponUsed($coupon)) {
                            if(Auth::user()->redeemCoupon($coupon)) {
                                return redirect()->route('deposit')->with(['successMessage' => __('frontend/user.coupon_redeem.success', [
                                    'amount' => $coupon->getFormattedAmount(),
                                    'code' => $coupon->code
                                ])]);
                            }
                        } else {
                            $request->flash();
                            return redirect()->route('deposit')->withErrors(['coupon_redeem_code' => __('frontend/user.coupon_redeem.error3')])->withInput();
                        }
                    }
                } else {
                    $request->flash();
                    return redirect()->route('deposit')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('deposit');
        }

       public function btcRedeemCoupon(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'coupon_redeem_code' => new RuleCouponRedeem()
                ]);

                if(!$validator->fails()) {
                    $coupon = Coupon::where('code', $request->get('coupon_redeem_code'))->get()->first();

                    if($coupon != null) {
                        if(!Auth::user()->hasCouponUsed($coupon)) {
                            if(Auth::user()->redeemCoupon($coupon)) {
                                return redirect()->route('deposit-btc')->with(['successMessage' => __('frontend/user.coupon_redeem.success', [
                                    'amount' => $coupon->getFormattedAmount(),
                                    'code' => $coupon->code
                                ])]);
                            }
                        } else {
                            $request->flash();
                            return redirect()->route('deposit-btc')->withErrors(['coupon_redeem_code' => __('frontend/user.coupon_redeem.error3')])->withInput();
                        }
                    }
                } else {
                    $request->flash();
                    return redirect()->route('deposit-btc')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('deposit-btc');
        }

	public function btcRedeemPage(Request $request) {
            $userTransaction = UserTransaction::where('user_id', Auth::user()->id)->where('status', 'waiting')->orderByDesc('created_at')->get()->first();

            if($userTransaction == null) {
                $bitcoind = BitcoinAPI::getBitcoinClient();
                $btcWallet = $bitcoind->getnewaddress();

                $userTransaction = UserTransaction::create([
                    'user_id' => Auth::user()->id,
                    'wallet' => $btcWallet,
                    'status' => 'waiting',
                    'amount' => 0,
                    'amount_cent' => 0,
                    'txid' => ''
                ]);
            } else {
                $btcWallet = $userTransaction->wallet;
            }

            return view('frontend/userpanel.deposit_btc_redeem', [
                'btcWallet' => $btcWallet,
                'clipboardJS' => (object) [
                    'element' => '.btc-cashin-copy-btn',
                    'fadeIn' => '.btc-cashin-copy-info'
                ],
                'userTransactionID' => $userTransaction->id
            ]);
        }

        public function passwordChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_current_password' => [
                        'required'
                    ],
                    'settings_new_password' => [
                        'required', 'min:6', 'max:255', 'same:settings_new_password_confirm'
                    ],
                    'settings_new_password_confirm' => [
                        'required', 'min:6', 'max:255'
                    ],
                ]);

                if(!$validator->fails()) {
                    if(Hash::check($request->input('settings_current_password'), Auth::user()->password)) {
                        Auth::user()->update([
                            'password' => bcrypt($request->input('settings_new_password_confirm'))
                        ]);
                        
                        return redirect()->route('settings')->with('successMessageSettingsPassword', __('frontend/user.success_password_changed'));
                    } else {
                        $validator->getMessageBag()->add('settings_current_password', __('frontend/user.settings_current_password_wrong'));

                        $request->flash();
                        return redirect()->route('settings')->withErrors($validator)->withInput();
                    }
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

        public function jabberIDChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_jabber_id' => [
                        'required', 'string', 'email', 'max:255', 'unique:users,jabber_id,' . Auth::user()->id
                    ]
                ]);

                if(!$validator->fails()) {
                    $jabberID = $request->input('settings_jabber_id');

                    Auth::user()->update([
                        'jabber_id' => $jabberID,
                        'newsletter_enabled' => $request->get('newsletter_enabled') ? 1 : 0
                    ]);
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

        public function mailAddressChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_mail_address' => [
                        'required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id
                    ],
                ]);

                if(!$validator->fails()) {
                    $mailAddress = $request->input('settings_mail_address');

                    Auth::user()->update([
                        'email' => $mailAddress
                    ]);
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

        public function showDepositPage() {
            return view('frontend/userpanel.deposit');
        }
		
		
		public function depositBtcPaidCheck($userTransactionID) {
            $userTransaction = UserTransaction::where('id', $userTransactionID)->where('user_id', Auth::user()->id)->orderByDesc('created_at')->get()->first();
            
            if($userTransaction == null) 
			{
				
				//return $userTransaction;
               return redirect()->route('deposit');
			   
            } else {
                
                // if (! $userTransaction->updateWhenPaidBtc()) {
                //     return redirect()->route('btc-connection');  
                // }
                if($userTransaction->updateWhenPaidBtc()==false)
				{
					return redirect()->route('transactions');
				}else{
					return redirect()->route('transactions');
				}
                //dd($userTransaction->updateWhenPaidBtc());
                //$userTransaction->updateWhenPaidBtc();
				
				//return $userTransaction;
				
                //return redirect()->route('transactions');
            }
        }

        /*public function depositBtcPaidCheck(Request $request) {
			$userTransactionID=$request->userTransactionID;
			//dd($userTransactionID);
            $userTransaction = UserTransaction::where('id', $userTransactionID)->where('user_id', Auth::user()->id)->first();
            
			
            if($userTransaction == null) {
				
                return 'null';//redirect()->route('deposit');
            } else {
                // if (! $userTransaction->updateWhenPaidBtc()) {
                //     return redirect()->route('btc-connection');  
                // }
				//dd($userTransaction->updateWhenPaidBtc());
				//dd($userTransaction->updateWhenPaidBtc());
                if($userTransaction->updateWhenPaidBtc()==false)
				{
					return 'false';
				}else{
					return 'true';
				}

                //return redirect()->route('transactions');
            }
        }*/

        public function showDepositBtcPage() {
            $userTransaction = UserTransaction::where('user_id', Auth::user()->id)->where('status', 'waiting')->orderByDesc('created_at')->get()->first();

            if($userTransaction == null) {
                $bitcoind = BitcoinAPI::getBitcoinClient();
                $btcWallet = $bitcoind->getnewaddress();

                $userTransaction = UserTransaction::create([
                    'user_id' => Auth::user()->id,
                    'wallet' => $btcWallet,
                    'status' => 'waiting',
                    'amount' => 0,
                    'amount_cent' => 0,
                    'txid' => ''
                ]);
            } else {
                $btcWallet = $userTransaction->wallet;
            }

            return view('frontend/userpanel.deposit_btc', [
                'btcWallet' => $btcWallet,
                'clipboardJS' => (object) [
                    'element' => '.btc-cashin-copy-btn',
                    'fadeIn' => '.btc-cashin-copy-info'
                ],
                'userTransactionID' => $userTransaction->id
            ]);
        }

        public function showOrdersPage(Request $request)
        {
            $user_orders = UserOrder::where( 'user_id', Auth::user()->id )->orderByDesc( 'id' )->paginate(5)->setPath('https://fake-tids.su/meine-tids');
			//
            /*if ( $pageNumber > $user_orders->lastPage() || $pageNumber <= 0 ) {
                return redirect()->route( 'orders-with-pageNumber', 1 );
            }
			*/
			

            return view( 'frontend/userpanel.orders', [
                'user_orders' => $user_orders
            ] );
        }

        public function showTransactionsPage($pageNumber = 0) {
            

            $user_transactions = UserTransaction::where('user_id', Auth::user()->id)
                        ->orderByDesc('created_at')->paginate(5, ['*'], 'page', $pageNumber);


			//dd($user_transactions);

            

            if($pageNumber > $user_transactions->lastPage() || $pageNumber <= 0) {
                return redirect()->route('transactions-with-pageNumber', 1);
            }

       
              
            return view('frontend/userpanel.transactions', [
                'user_transactions' => $user_transactions
            ]);
        }
    }
