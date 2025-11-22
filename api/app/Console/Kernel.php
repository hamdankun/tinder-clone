<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendLikeThresholdNotification;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for users who have reached the like threshold (default 50 likes)
        // Run once per day at 2 AM
        $schedule->command('likes:check-high-count')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Likes check completed successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Likes check failed');
            });
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
