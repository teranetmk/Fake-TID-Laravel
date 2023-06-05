<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UserTransaction;

class BitcoinCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcoin:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks bitcoin transactions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transactions = UserTransaction::where('status', 'pending')->get();

		foreach($transactions as $transaction) {
			try {
				$transaction->updateWhenPaidBtc();
			} catch(\Exception $ex) {}
		}
    }
}
