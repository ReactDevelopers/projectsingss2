<?php

namespace App\Console\Commands;

use App\User;
use App\Confirmation;
use Illuminate\Console\Command;
use App\Jobs\Confirmations\NotifyConfirmationsExpiring as NotifyConfirmationsExpiringJob;

class NotifyConfirmationsExpiring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:confirmations-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify all users of confirmations expiring today';

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
        // Users who want notifications
        // $notifiable_users = User::where('notify_of_hotel_payment_schedule', 1)->get();
        $notifiable_users = User::all();

        if ($notifiable_users->isEmpty()) {
            $this->info('No users exist to be notified.');
            return false;
        }

        $expires_on = date('D, M j, Y', strtotime('today'));
        $confirmations = Confirmation::getExpiringConfirmations();

        if ($confirmations->isEmpty()) {
            $this->info('No confirmations are expiring on '.$expires_on);
            return false;
        }

        return dispatch_now(new NotifyConfirmationsExpiringJob($notifiable_users, $confirmations));
    }
}
