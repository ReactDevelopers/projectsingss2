<?php
namespace App\Lib\DataVerify;

use Validator;

class UpdateCourseRunDataVerify extends DataHelper {
    
    protected $cellNameToDbCol = [
        'course_run_id' => 'id',
        'course_start_date' => 'start_date',
        'course_end_date' => 'end_date',
        'assessment_start_date' => 'assessment_start_date',
        'assessment_end_date' => 'assessment_end_date',
        //'no_of_attendees' => 'no_of_attendees',
        'no_of_trainees' => 'no_of_trainees',
        //'no_of_absentees' => 'no_of_absentees',
        'remarks'=>'remarks',
        'deconflict' => 'should_check_deconflict'
    ];

    protected $defaultValues = [
        'no_of_attendees' => null,
        'deconflict' => 'Yes',
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
            'course_start_date'=>'required|regex:/^\d{4}\-\d{2}\-\d{2}$/',
            'course_end_date' => 'required|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:course_start_date',
            'assessment_start_date'=>'nullable|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:course_end_date',
            'assessment_end_date' => 'nullable|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:assessment_start_date',
            //'no_of_attendees'=>'nullable|integer|max:4294967295',
            //'no_of_absentees'=>'nullable|integer|max:4294967295',
            'no_of_trainees'=>'required|integer|max:4294967295',
            'remarks'=>'nullable|max:255',
            'deconflict' =>'nullable|in_enum:Yes,No',
        ],[
            'course_run_id.required' =>'Course run id is required.',
            'course_run_id.integer' =>'Course run id must be a number.',
            'course_start_date.required'=> 'Course Start Date is required.',
            'course_start_date.regex' => 'Course Start Date format is not valid.',
            'course_end_date.required' => 'Course End Date is required.',
            'course_end_date.regex' => 'Course End Date format is not valid.',
            'course_end_date.in_arr_after_equal_date'=> 'Course End Date should be greater than or equal to from the Course Start Date.',

            'assessment_start_date.required'=> 'Assessment Start Date is required.',
            'assessment_start_date.regex' => 'Assessment Start Date format is not valid.',
            'assessment_start_date.in_arr_after_equal_date' => 'Assessment Start Date should be greater than or equal to from the Course End Date.',

            'assessment_end_date.required' => 'Assessment End Date is required.',
            'assessment_end_date.regex' => 'Assessment End Date format is not valid.',
            'assessment_end_date.in_arr_after_equal_date' => 'Assessment End Date should be greater than or equal to from the Assessment Start Date.',
            
            'no_of_attendees.integer' => 'No of attendees must be a number.',
            'no_of_attendees.max' => 'No of attendees must be less than or equal to 4294967295.',
            'no_of_trainees.required' => 'No of Tarinees is required.',
            'no_of_trainees.integer' => 'No of trainees must be a number.',
            'no_of_trainees.max' => 'No of trainees must be less than or equal to 4294967295.',
            //'no_of_absentees.integer' => 'No of absentees must be a number.',            
            //'no_of_absentees.max' => 'No of absentees must be less than or equal to 4294967295.',

            'remarks.max' => 'Remark must be less than 255 characters.',
            
            'deconflict.required' => 'Deconflict is required.',
            'deconflict.in_enum' => 'Deconflict value can be Yes or No only.',
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

    protected function afterSuccessCallBack() {

        $this->translateManytoOne(['Yes' => ['yes'], 'No' => ['no'] ], 'deconflict');
    }
}