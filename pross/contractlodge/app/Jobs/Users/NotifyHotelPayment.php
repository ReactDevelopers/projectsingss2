<?php

namespace App\Jobs\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\HotelPaymentComingDue;

class NotifyHotelPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection
     */
    public $users;

    /**
     * @var Collection
     */
    public $bills;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $users, Collection $bills)
    {
        $this->users = $users;
        $this->bills = $bills;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->users->each(function ($user) {
            $user->notify(new HotelPaymentComingDue($this->bills));
        });
    }
}
