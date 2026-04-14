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
        // Send rent reminders 3 times a day (Morning, Afternoon, Evening)
        $schedule->command('tenant:due-reminder')->dailyAt('08:00');
        $schedule->command('tenant:due-reminder')->dailyAt('13:00');
        $schedule->command('tenant:due-reminder')->dailyAt('18:00');

        // Otomatis keluarkan penyewa mati (lewat 3 hari) setiap hari jam 1 malam
        $schedule->command('app:evict-dead-accounts')->dailyAt('01:00');
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
