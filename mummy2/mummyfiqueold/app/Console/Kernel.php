<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
		// p\Console\Commands\UpdatePortfolioMedia',
		// p\Console\Commands\UpdateVendorPhone',
		// App\Console\Commands\UpdateVendorPlan',
		// 'App\Console\Commands\UpdateMediaThumb',
		// 'App\Console\Commands\UpdateVendorSocialLink',
		'App\Console\Commands\UpdateVendorPackage',
		'App\Console\Commands\ClearApilog',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();
	}

}
