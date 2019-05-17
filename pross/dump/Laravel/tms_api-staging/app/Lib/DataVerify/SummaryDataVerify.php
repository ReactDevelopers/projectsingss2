<?php
namespace App\Lib\DataVerify;

use Validator;

class SummaryDataVerify extends DataHelper {
    
    protected $cellNameToDbCol = [
        'course_run_id' => 'id',
        'overall'=>'overall',
        'trainers_delivery'=>'trainer_delivery',
        'content_relevance' =>'content_relevance',
        'site_visits' => 'site_visits',
        'facilities' => 'facilities',
        'admin' =>'admin',
        'response_rate' => 'response_rate',
        'summary_uploaded' =>'summary_uploaded',
        'current_status' => 'current_status'
    ];
    protected $defaultValues = [

       'summary_uploaded' => 'Yes',
       'current_status' => 'Closed'
    ];
    
    private $availableCourseRun = [];

    public function __construct(Array $data, Array $availableCourseRun) {

        parent::__construct($data);
        $this->availableCourseRun = $availableCourseRun;
        $this->validator();
    }

    /**
     * Validate the Create Course Run Data
     */
    protected function validator() {
        
        $original = $this->data['original'];
        $course_run_data = $this->availableCourseRun;

        $this->validate = Validator::make($original, [
            'course_run_id' => 'required|integer',            
            'overall'=>'nullable|numeric|max:100',
            'trainers_delivery'=>'nullable|numeric|max:100',
            'content_relevance'=>'nullable|numeric|max:100',
            'site_visits'=>'nullable|numeric|max:100',
            'facilities'=>'nullable|numeric|max:100',
            'admin'=>'nullable|numeric|max:100',
            'response_rate'=>'nullable|numeric|max:100',
        ],[
           'course_run_id.required' =>  'Course Run Id is required',
           'course_run_id.integer' =>'Course run id must be a number.',
           'overall.numeric' => 'Overall value must be a numeric value.',
           'overall.max' => 'Overall value must be less than 100.',

           'trainers_delivery.numeric' => 'Trainers Delivery value must be a numeric value.',
           'trainers_delivery.max' => 'Trainers Delivery value must be less than 100.',

           'content_relevance.numeric' => 'Content Relevance value must be a numeric value.',
           'content_relevance.max' => 'Content Relevance must be less than 100.',

           'site_visits.numeric' => 'Site Visits value must be a numeric value.',
           'site_visits.max' => 'Site Visits must be less than 100.',

           'facilities.numeric' => 'Facilities value must be a numeric value.',
           'facilities.max' => 'Facilities must be less than 100.',

           'admin.numeric' => 'Admin value must be a numeric value.',
           'admin.max' => 'Admin must be less than 100.',

           'response_rate.numeric' => 'Response Rate value must be a numeric value.',
           'response_rate.max' => 'Response Rate must be less than 100.',

        ]);

        $this->validate->after(function($validator) use($original, $course_run_data) {

            $course_run_id = isset($original['course_run_id']) ? $original['course_run_id'] : null;

            if($course_run_id) {

                $course_run =  isset($course_run_data[$course_run_id]) ? $course_run_data[$course_run_id][0] : null;

                if(!$course_run) {

                    $validator->errors()->add('course_run_id', 'Course run does not exists into the database.');

                } else if($course_run && $course_run['current_status'] == 'Closed') {
                      
                    $validator->errors()->add('course_run_id', 'Course run has been closed.');
                }

            }
        });

        return $this;
    }
}