<?php

namespace App\Jobs\Confirmations;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\ConfirmationsExpiringToday;

class NotifyConfirmationsExpiring implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection
     */
    public $users;

    /**
     * @var Collection
     */
    public $confirmations;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $users, Collection $confirmations)
    {
        $this->users = $users;
        $this->confirmations = $confirmations;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->users->each(function ($user) {
            $user->notify(new ConfirmationsExpiringToday($this->confirmations));
        });
    }
}
