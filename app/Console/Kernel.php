<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CloseAnsweredTickets;
use App\Console\Commands\BitcoinCheck;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

    class Kernel extends ConsoleKernel
    {
        protected $commands = [
            BitcoinCheck::class,
            CloseAnsweredTickets::class,
        ];
        
        protected function schedule(Schedule $schedule)
        {
            // $schedule->command('tickets-closed:delete')->weekly();
           $schedule->command('bitcoin:check')->everyMinute()->withoutOverlapping();
            $schedule->command('tickets:close-answered')->dailyAt('00:00');
        }

        protected function commands()
        {
            $this->load(__DIR__ . '/Commands');

            require base_path('routes/console.php');
        }

        protected function bootstrappers()
        {
            return array_merge(
                [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
                parent::bootstrappers()
            );
        }
    }
