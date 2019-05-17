<?php
namespace App\Lib\DataVerify;

use Validator;
use App\Models\AbsentReason;
use App\Models\FailureReason;

class PlacementResultDataVerify extends DataHelper {
    
    protected $cellNameToDbCol = [
        'course_run_id' => 'course_run_id',
        'per_id'=>'personnel_number',
        'attendance' => 'attendance',
        'assessment_results' => 'assessment_results',
        'result_uploaded' =>'result_uploaded',
        'action' =>'action',
        
    ];    
    
    private $placements = [];
    protected $defaultValues = [
        'result_uploaded' =>'Yes',
        'action' => null,
    ];

    public function __construct(Array $data, Array $placements) {

        parent::__construct($data);
        $this->placements = $placements;
        $this->validator()
            ->findFailureReasonId()
            ->findAbsentReasonId();
    }

    /**
     * Validate the Create Course Run Data
     */
    protected function validator() {
        
        $original = $this->data['original'];
        $placements = $this->placements;

        $this->validate = Validator::make($original, [
            //'course_run_id_per_id' => 'required',
            'course_run_id' => 'required|integer',            
            'per_id' => 'required|integer',              
            'assessment_results' => 'nullable|in_enum:Pass,Fail', 
            'attendance' =>'required|in_enum:Present,Absent',
            'absent_reason' => 'required_if:attendance,Absent,absent,ABSENT',
            'action' => 'in_enum:Penalty,Waived',
            'failure_reason' => 'required_if:assessment_results,Fail,fail,FAIL'         
        ],[
            //'assessment_results.required' => 'Assessment Result is required.',
            'assessment_results.in_enum' => 'Assessment Result accepts only Pass or Fail.',
            'attendance.required' => 'Attendance is required.',
            'attendance.in_enum' => 'Attendance accepts only Present or Absent.',
            'action.in_enum' => 'Action accepts only Penalty or Waived.',
            'absent_reason.required_if' => 'Absent reason is required if user did not attend the exam.',
            'failure_reason.required_if' => 'Failure reason is required if user did not qualified in the exam.',
            //'course_run_id.required' =>'Course run id is required.',
            //'course_run_id.integer' =>'Course run id must be a number.',
            'per_id.integer' =>'Per id must be a number.',
            'per_id.required' => 'Per Id is required.'
        ]);

        $this->validate->after(function($validator) use($original, $placements) {

            $course_run_id  = isset($original['course_run_id']) ? $original['course_run_id'] : null;
            $per_id  = isset($original['per_id']) ? $original['per_id'] : null;

            if($course_run_id && $per_id) {
                
                $course_run_id_per_id = $course_run_id.'-'.$per_id;

                $placement = isset($placements[$course_run_id_per_id])  ? $placements[$course_run_id_per_id][0] : null;

                if(!$placement){
                    $validator->errors()->add('course_run_id', 'User placement\'s record does not exists.');
                }
                else if($placement['current_status'] != 'Confirmed') {

                    $validator->errors()->add('course_run_id', 'System does not allow to upload placement result data if placement status is not Confirmed and the current status is '. $placement['current_status']);
                }
            }


            $attendance = isset($original['attendance']) ? $original['attendance'] : null;
            $assessment_results = isset($original['assessment_results']) ? $original['assessment_results'] : null;
            $failure_reason = isset($original['failure_reason']) ? $original['failure_reason'] : null;
            $absent_reason = isset($original['absent_reason']) ? $original['absent_reason'] : null;

            //
            if($attendance == 'Absent' && $assessment_results =='Pass'){

                $validator->errors()->add('assessment_results', 'User can not qualify the exam without attending. Please check the data.');
            }

            if($assessment_results =='Pass' && !empty ( $failure_reason) ) {

                $validator->errors()->add('failure_reason','Failure reason should be empty if User passed the exam.');
            }

            if($attendance=='Present' && !empty ( $absent_reason)) {

                $validator->errors()->add('absent_reason','Absent Reason should be empty if User attended the exam.');
            }
        });

        return $this;
    }

    /**
     * Find Programme Type in Database
     */
    private function findFailureReasonId() {

        return $this->findInDB(
            AbsentReason::getCached(),
            'absent_reason',
            ['absent_reason'],
            'absent_reason_id',
            'Absent reason does not match.'
        );        
    }
    /**
     * Find Programme Type in Database
     */
    private function findAbsentReasonId() {

        return $this->findInDB(
            FailureReason::getCached(),
            'failure_reason',
            ['failure_reason'],
            'failure_reason_id',
            'Failure reason does not match.'
        );        
    }

    /**
     * Translate assessment_results,  attendance value according to database Enum Collection
     */
    protected function afterSuccessCallBack() {        
        
        $this->translateManytoOne(['Pass' => ['pass'], 'Fail' => ['fail'] ], 'assessment_results' );
        $this->translateManytoOne(['Absent' => ['absent'], 'Present' => ['present'] ], 'attendance');
        $this->translateManytoOne(['Penalty' => ['penalty'], 'Waived' => ['waived'] ], 'action');
    }
}
