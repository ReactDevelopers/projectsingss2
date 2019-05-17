<?php

namespace App\Console\Commands;

use App\Bill;
use App\User;
use Illuminate\Console\Command;
use App\Jobs\Users\NotifyHotelPayment as NotifyHotelPaymentJob;

class NotifyHotelPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:hotel-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify opted-in users of an upcoming hotel payment';

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
     * Send notifications to users with a list of hotel payments due in 7 days
     *
     * @return mixed
     */
    public function handle()
    {
        // Users who want notifications
        $notifiable_users = User::where('notify_of_hotel_payment_schedule', 1)->get();

        if ($notifiable_users->isEmpty()) {
            $this->info('No users want to be notified of the hotel payment schedule.');
            return false;
        }

        // Hotel bills with payments due 7 days from today
        // (expectation is that this command is run once daily).
        $due_on = date('Y-m-d', strtotime('+7 days'));

        $bills = Bill::with(['currency', 'payments', 'race_hotel', 'race_hotel.hotel'])
            ->whereNull('deleted_at')
            ->whereHas('payments', function ($query) use ($due_on) {
                $query->whereNull('deleted_at')
                    ->whereDate('due_on', $due_on)
                    ->where('amount_due', '>', 0)
                    ->where(function ($query) {
                        $query->whereColumn('amount_paid', '<', 'amount_due')
                            ->orWhereNull('paid_on');
                    });
            })
            ->whereHas('race_hotel', function ($query) {
                $query->whereNull('deleted_at');
            })->get();

        if ($bills->isEmpty()) {
            $this->info('No hotel bills have payments due on '.$due_on);
            return false;
        }

        return dispatch_now(new NotifyHotelPaymentJob($notifiable_users, $bills));
    }
}
