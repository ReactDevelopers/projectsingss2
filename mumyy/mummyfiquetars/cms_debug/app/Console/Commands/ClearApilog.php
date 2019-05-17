<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Dashboard\Entities\APILog;
use Carbon\Carbon;

class ClearApilog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear:apilog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear apilog 2 weeks older';

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
        $today = Carbon::today();
        $todaySub2Week  = $today->copy()->subWeeks(2);
        $dateForm = strtotime($todaySub2Week);
        $apiLog = APILog::where(\DB::raw("UNIX_TIMESTAMP(created_at)"),'<=',$dateForm)->orWhere('created_at',NULL)->get();
        if(count($apiLog) > 0){
            foreach ($apiLog as $key => $item) {
                $item->delete();
            }
        }
        $this->info("Clear API Log Success");
    }
}
