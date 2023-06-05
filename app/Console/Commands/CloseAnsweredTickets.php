<?php

namespace App\Console\Commands;

use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class CloseAnsweredTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:close-answered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close answered tickets older for more than 3 days';

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
        try {
            /** @var Collection */
            $tickets = UserTicket::query()
                ->where('status', 'answered')
                ->whereDate('updated_at', '<=', Carbon::now()->subDays(3))
                ->get();

            $tickets->map(function (UserTicket $ticket) {
                $ticket->update([
                    'status'    => 'closed',
                ]);
            });
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
