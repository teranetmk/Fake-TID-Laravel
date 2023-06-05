<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UserTicket;

class DeleteClosedTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets-closed:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all closed tickets.';

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
        $tickets = UserTicket::where('status', 'closed')->delete();
    }
}
