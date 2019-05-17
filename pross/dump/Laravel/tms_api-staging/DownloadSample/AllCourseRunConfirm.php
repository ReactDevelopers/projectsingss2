<?php 
namespace DownloadSample;

use Illuminate\Console\Command;
use  App\Models\CourseRun;

class AllCourseRunConfirm extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course_run:confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all course run status as confirmed';

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
        CourseRun::where('id','>=', 1)->update(['current_status'=>'Confirmed']);
        $this->info('Course Run Status has been changed.');
    }
     
}
