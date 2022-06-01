<?php

namespace App\Console;

use App\Console\Commands\CleanUploads;
use App\Console\Commands\DailyDigest;
use App\Console\Commands\FetchConvertionStatus;
use App\Console\Commands\GenerateDataExport;
use App\Console\Commands\RunUnlockTimeouts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(RunUnlockTimeouts::class)->everyMinute();
        $schedule->command(CleanUploads::class)->everyMinute();
        $schedule->command(FetchConvertionStatus::class)->everyMinute();
        $schedule->command(GenerateDataExport::class)->daily();
        $schedule->command(DailyDigest::class)->dailyAt('19:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
