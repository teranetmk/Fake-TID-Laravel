<?php

namespace App\Http\Controllers\UserPanel;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\UserTransaction;
 
    class BtcPayServerController extends Controller
    {
        public function __construct() {
            
        }

        public function btcpaymentcheck(Request $request){
           
            // $secret = config('app.btcpay_secret');
            // $signature = $request->header('BTCPay-Sig');
          
            // $payload = $request->getContent();
            // $computedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

            // if ($computedSignature === $signature) {
                $response = json_decode($request->getContent(), true);
                
                $userTransaction = UserTransaction::where('txid', $response['invoiceId'])->get()->first();
                if($userTransaction){
                    if($response['type']=='InvoiceExpired'){
                        $userTransaction->update([
                            'status' => 'expired',
                        ]);
                    }
                    elseif($response['type']=='InvoiceSettled' || $response['type']=='InvoicePaymentSettled' || $response['type']=='InvoiceReceivedPayment'){
                        $userTransaction->update([
                            'status' => 'paid',
                            'confirmations' =>1
                        ]);
                        $user = User::where('id', $userTransaction->user_id)->get()->first();
    
                            if($user != null) {
                                $balance_in_cent = $user->balance_in_cent;
    
                                $user->update([
                                    'balance_in_cent' => $balance_in_cent + $$userTransaction->amount_cent
                                ]);
                            }
                    }
                   
                }
                
           // }
            // else{
               
            //     // Return an error response, as the signature does not match.
            //     return response()->json(['error' => 'Invalid signature'], 400);
               
                
            // }

           

        }
    }
