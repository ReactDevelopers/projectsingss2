<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CurrencyConversion::class,
        \App\Console\Commands\TransferAmount::class,
        \App\Console\Commands\RefundPayment::class,
        \App\Console\Commands\RaiseDisputeStatusUpdate::class,
        \App\Console\Commands\JobStartReminder::class,
        \App\Console\Commands\FollowUsers::class,
        \App\Console\Commands\FollowQuestion::class,
        \App\Console\Commands\TransferJob::class,
    ];

    /**
     * Define the application's command schedule.
     * * * * * php /var/www/html/crowbar/artisan schedule:run >> /dev/null 2>&1
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('currencyconversion')->daily();
        $schedule->command('transferamount')->daily();
        $schedule->command('refundpayment')->daily();
        $schedule->command('RaiseDisputeStatusUpdate')->everyTenMinutes();
        $schedule->command('TransferJob:transferjob')->dailyAt('2:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
