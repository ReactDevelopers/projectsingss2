<?php

namespace App\Jobs\RoomingLists;

use App\RoomingList;
use App\Confirmation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignRoomingListToClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Confirmation object
     *
     * @var App\Confirmation
     */
    private $confirmation;

    /**
     * Create a new job instance.
     *
     * @param App\Confirmation $confirmation
     *
     * @return void
     */
    public function __construct(Confirmation $confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        RoomingList::assignClient($this->confirmation);
    }
}
