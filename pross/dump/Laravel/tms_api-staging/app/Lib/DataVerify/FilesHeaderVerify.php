<?php
namespace App\Lib\DataVerify;
use App\Exceptions\FileHeaderNotMatchException;

class FilesHeaderVerify {

    private $header = [];
    private $errorCode = 'FHNM';

    public $courseCell = [

        'course_code'=>                              'Course Code',
        'course_title'=>                             'Course Title',
        'duration_no_of_days'=>                      'Duration (No. of Days)',
        'programme_category'=>                       'Programme Category',
        'programme_type'=>                           'Programme Type',
        'competency_level_if_applicable'=>           'Competency Level (if applicable)',
        'assessment_type'=>                          'Assessment Type',
        'mandatory_yn'=>                             'Mandatory Y/N',
        'compulsory_yn'=>                            'Compulsory Y/N',
        'delivery_method'=>                          'Delivery Method',
        'training_location'=>                        'Training Location',
        'course_provider'=>                          'Course Provider',
        'costpax_without_gst'=>             'Cost/Pax (without GST)',
        'grant'=>                            'Grant',
        'funding_type'=>                     'Funding Type',
        'placement_criteria'=>                        'Placement Criteria',
        'cts_to_approve_future_placements'=>          'CTS to approve Future Placements'
    ];

    public $createCourseRunCell = [

        'course_code'                =>             'Course Code',
        'course_start_date'          =>             'Course Start Date',
        'course_end_date'            =>             'Course End Date',
        'assessment_start_date'      =>             'Assessment Start Date',
        'assessment_end_date'        =>             'Assessment End Date',
        'no_of_trainees'             =>             'No. of Trainees',
        'deconflict'                 =>             'Deconflict',
        'remarks'                    =>             'Remarks'
    ];

    public $updateCourseRunCell = [

        'course_run_id'             =>            'Course Run ID',
        'course_start_date'         =>            'Course Start Date',
        'course_end_date'           =>            'Course End Date',
        'assessment_start_date'     =>            'Assessment Start Date',
        'assessment_end_date'       =>            'Assessment End Date',
        'no_of_trainees'             =>             'No. of Trainees',
        //'no_of_attendees'           =>            'No. of Attendees',
        //'no_of_absentees'           =>            'No. of Absentees',
        'deconflict'                =>            'Deconflict',
        'remarks'                   =>            'Remarks'
    ];

    public $placementCell = [
        'course_run_id' =>            'Course Run ID',
        'per_id'        =>            'Per ID'  
    ];

    public $courseRunSummaryCell =[
        'course_run_id'                =>            'Course Run ID',
        'overall'                      =>            'Overall (%)',
        'trainers_delivery'            =>            'Trainer\'s Delivery (%)',
        'content_relevance'            =>            'Content Relevance (%)',
        'site_visits'                  =>            'Site Visits (%)',
        'facilities'                   =>            'Facilities (%)',
        'admin'                        =>            'Admin (%)',
        'response_rate'                =>            'Response Rate (%)'
    ];

    public $placementResultCell = [
        'course_run_id'         =>              'Course Run ID',
        'per_id'                =>             'Per ID',
        'attendance'            =>             'Attendance',
        'assessment_results'    =>             'Assessment Results',
        'absent_reason'         =>             'Absent Reason',
        'action'                =>             'Action',     
        'failure_reason'        =>             'Failure Reason'
    ];

    public $supervisorCell = [
        'per_id' => 'Per ID',
        'sup_id' => 'Sup ID'
    ];

    public function __construct(Array $header) {
        
        $this->header = $header;
    }

    /**
     * TO Verify the Supervusor File header
     */
    public function supervisor() {

        $this->verifyHeader( array_keys($this->supervisorCell), array_values($this->supervisorCell));
    }   

    /**
     * TO Verify the Couse File Header
     */
    public function course() {
        
        $this->verifyHeader( array_keys($this->courseCell), array_values($this->courseCell) );
    }
    /**
     * To Verify the header of create run course
     */
    public function createCourseRun() {

        $this->verifyHeader(array_keys($this->createCourseRunCell), array_values($this->createCourseRunCell));
    }
    /**
     * To Verify the header of Update Course Run File.
     */
    public function updateCourseRun() {

        $this->verifyHeader( array_keys($this->updateCourseRunCell) , array_values($this->updateCourseRunCell));
    }

    /**
     * TO Verify the header of Placement File.
     */
    public function placement() {

        $this->verifyHeader( array_keys($this->placementCell), array_values($this->placementCell));
    }
    /**
     * TO Verify the header of Course Summary File.
     */
    public function courseRunSummary() {

        $this->verifyHeader( array_keys($this->courseRunSummaryCell),  array_values($this->courseRunSummaryCell));
    }

    /**
     * TO Verify the header of Placement Result File.
     */
    public function placementResult() {

        $this->verifyHeader( array_keys($this->placementResultCell), array_values($this->placementResultCell));
    }

    /**
     * TO Verify the File Header
     */
    private function verifyHeader(array $columns, array $accepted_header) {
    
        $matched = true;
        foreach($columns as $key => $column) {
            
            if(!isset($this->header[$key]) ||  strtolower($this->header[$key]) !=  strtolower($column) ) {
                $matched = false;
            }
        }
        
        if(!$matched) {
            
            throw new FileHeaderNotMatchException( $accepted_header);
        }
    }
}