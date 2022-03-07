<?php

namespace App\Console;

use App\Console\Commands\NotifyStateComplaint;
use App\Console\Commands\NotifyStateComplaintFunt;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        NotifyStateComplaint::class,
        NotifyStateComplaintFunt::class,
    ];
    protected function scheduleTimezone()
    {
        return 'America/Bogota';
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:notifystatecomplainty')->twiceDaily(1, 13);
        $schedule->command('command:notifystatecomplaintyfunt')->twiceDaily(1, 13);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    /* protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    } */
}