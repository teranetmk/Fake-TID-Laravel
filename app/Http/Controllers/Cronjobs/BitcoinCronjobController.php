<?php

    namespace App\Http\Controllers\Cronjobs;

    use App\Http\Controllers\Controller;

    use App\Models\UserTransaction;

    class BitcoinCronjobController extends Controller
    {
        public function checkTransaction() {
			$transactions = UserTransaction::where(['status', '!=', 'paid'])->get();

			foreach($transactions as $transaction) {
				try {
					$transaction->updateWhenPaidBtc();
				} catch(\Exception $ex) {}
			}
        }
    }
