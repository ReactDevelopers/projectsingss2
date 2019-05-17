<?php

namespace App\Jobs\Races;

use Auth;
use App\Race;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Races\RacesStoreRequest;

class RacesUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * RacesStoreRequest
     *
     * @var Request
     */
    private $request;

    /**
     * Race id
     * @var integer
     */
    private $race_id;

    /**
     * Create a new job instance.
     *
     * @param App\Http\Requests\Races\RacesStoreRequest $request
     * @param int $race_id
     * @return void
     */
    public function __construct(RacesStoreRequest $request, int $race_id)
    {
        $this->request = $request;
        $this->race_id = $race_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $race = Race::find($this->race_id);
        $race->year = $this->request->year;
        $race->name = $this->request->name;
        $race->start_on = format_input_date_to_system($this->request->start_on);
        $race->end_on = format_input_date_to_system($this->request->end_on);
        $race->race_code = $this->request->race_code;
        $race->currency_id = $this->request->currency_id;
        $race->created_by = auth()->user()->id;

        return $race->save();
    }
}
