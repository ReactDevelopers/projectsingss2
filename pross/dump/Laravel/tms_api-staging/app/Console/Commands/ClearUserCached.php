<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearUserCached extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:cached_clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To clear the user cached data, This command should be execute after update the User data by HRDU Software.';

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
        \Cache::forget('User:all');
        \Cache::forget('Department:staff');
        
        $this->info('User/Department cached data has been cleared.');
    }
     
}
