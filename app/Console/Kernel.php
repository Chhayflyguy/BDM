<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // NEW: Schedule your commands
        $schedule->command('report:send-daily-summary')->dailyAt('21:00'); // 9 PM
        $schedule->command('vip:check-expiries')->daily();
        $schedule->command('vip:reset-expired-balances')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}