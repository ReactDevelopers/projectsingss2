<?php 
namespace DownloadSample;

use Illuminate\Console\Command;
use  App\Models\Placement;

class AllPlacementConfirm extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'placement:confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all Placements status as confirmed ,if does not conflict';

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
    	$date = date('Y-m-d H:i:s');
    	$placement = new Placement();
        $placement->timestamps = false;

		$placement->join('course_runs','course_runs.id','=','placements.course_run_id')
            ->where('placements.current_status','<>' ,'Confirmed')
            ->leftJoin(
                \DB::raw("(SELECT 
                    placements.id, 
                    placements.personnel_number, 
                    placements.current_status,
                    cr.id as course_run_id,
                    cr.course_code,
                    cr.start_date,
                    cr.end_date,
                    cr.assessment_start_date,
                    cr.assessment_end_date
                    FROM `placements` 
                    JOIN course_runs as cr ON cr.id = placements.course_run_id
                    where placements.deleted_at IS NULL
                    ) as p
                "), 
                function($q) {
                    $q->on('p.personnel_number','=','placements.personnel_number');
                    $q->whereRaw('(p.id  <> placements.id AND p.current_status ="Confirmed")');
                    $q->whereRaw('(
                    (
                        (course_runs.start_date <=  p.start_date AND course_runs.end_date >= p.start_date )
                        OR 
                        (  course_runs.start_date <=  p.end_date AND course_runs.end_date >= p.end_date )
                        OR 
                        ( p.start_date <= course_runs.start_date AND p.end_date >= course_runs.start_date )
                        OR 
                        (p.start_date <= course_runs.end_date AND  p.end_date >= course_runs.end_date )
                    )
                    OR
                    (
                        p.assessment_start_date IS NOT NULL 
                        AND p.assessment_end_date IS NOT NULL 
                        AND course_runs.assessment_start_date IS NOT NULL 
                        AND course_runs.assessment_end_date IS NOT NULL 
                        AND 
                        (
                            (course_runs.assessment_start_date <=  p.start_date AND course_runs.assessment_end_date >= p.start_date )
                                OR 
                            (  course_runs.assessment_start_date <=  p.end_date AND course_runs.assessment_end_date >= p.end_date )
                                OR 
                            ( p.start_date <= course_runs.assessment_start_date AND p.end_date >= course_runs.assessment_start_date )
                                OR 
                            (p.start_date <= course_runs.assessment_end_date AND  p.end_date >= course_runs.assessment_end_date )
                        )
                    )
                    OR 
                    (
                        (course_runs.start_date <=  p.assessment_start_date AND course_runs.end_date >= p.assessment_start_date )
                        OR 
                        (  course_runs.start_date <=  p.assessment_end_date AND course_runs.end_date >= p.assessment_end_date )
                        OR 
                        ( p.assessment_start_date <= course_runs.start_date AND p.assessment_end_date >= course_runs.start_date )
                        OR 
                        (p.assessment_start_date <= course_runs.end_date AND  p.assessment_end_date >= course_runs.end_date )
                    )
                    OR
                    (
                        p.assessment_start_date IS NOT NULL 
                        AND p.assessment_end_date IS NOT NULL 
                        AND course_runs.assessment_start_date IS NOT NULL 
                        AND course_runs.assessment_end_date IS NOT NULL 
                        AND 
                        (
                            (course_runs.assessment_start_date <=   p.assessment_start_date AND course_runs.assessment_end_date >=  p.assessment_start_date )
                                OR 
                            (  course_runs.assessment_start_date <=  p.assessment_end_date AND course_runs.assessment_end_date >= p.assessment_end_date )
                                OR 
                            (  p.assessment_start_date <= course_runs.assessment_start_date AND p.assessment_end_date >= course_runs.assessment_start_date )
                                OR 
                            ( p.assessment_start_date <= course_runs.assessment_end_date AND  p.assessment_end_date >= course_runs.assessment_end_date )
                        )
                    )
                    
                    )');   
            })
 			->whereRaw('(p.id is NUll OR course_runs.should_check_deconflict = "No")')
            ->groupBy('placements.id')            
            ->update(['placements.current_status' => 'Confirmed','placements.updated_at' => $date]);

            $data = Placement::where('updated_at', $date)
                        ->get();

        $this->info('Placement Status has been changed. '. $data->count());
    }
     
}
