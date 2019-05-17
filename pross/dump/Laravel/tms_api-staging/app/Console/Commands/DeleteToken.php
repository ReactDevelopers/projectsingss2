<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteToken extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:delete_old_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Clear the token which are 30 days older.';

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
        $deleted = \DB::table('oauth_access_tokens')->whereRaw('created_at < NOW() - INTERVAL 30 DAY')->delete();
        $this->info('Successfully deleted expired tokens: '. $deleted);
    }
     
}
