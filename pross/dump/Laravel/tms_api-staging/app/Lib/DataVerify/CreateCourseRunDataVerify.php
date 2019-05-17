<?php
namespace App\Lib\DataVerify;

use Validator;

class CreateCourseRunDataVerify extends DataHelper {
    
    protected $cellNameToDbCol = [
        'course_code' => 'course_code',
        'course_start_date' => 'start_date',
        'course_end_date' => 'end_date',
        'assessment_start_date' => 'assessment_start_date',
        'assessment_end_date' => 'assessment_end_date',
        'no_of_trainees' => 'no_of_trainees',
        'remarks'=>'remarks',
        'deconflict' => 'should_check_deconflict'
    ];
  
    private $availableCourseCodes = [];

    protected $defaultValues = [
        'deconflict' => 'Yes',
    ];

    public function __construct(Array $data, Array $availableCourseCodes) {

        parent::__construct($data);
        $this->availableCourseCodes = $availableCourseCodes;
        $this->validator();
    }

    /**
     * Validate the Create Course Run Data
     */
    protected function validator() {
        
        $original = $this->data['original'];
        $course_codes = $this->availableCourseCodes;

        $this->validate = Validator::make($original, [
            'course_code' => 'required|string|max:20',
            'course_start_date'=>'required|regex:/^\d{4}\-\d{2}\-\d{2}$/',
            'course_end_date' => 'required|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:course_start_date',
            'assessment_start_date'=>'nullable|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:course_end_date',
            'assessment_end_date' => 'nullable|regex:/^\d{4}\-\d{2}\-\d{2}$/|in_arr_after_equal_date:assessment_start_date',
            'no_of_trainees'=>'required|integer|max:4294967295',
            'remarks'=>'nullable|max:255',
            'deconflict' =>'nullable|in_enum:Yes,No',
        ],[
            'course_code.required' => 'Course code is required.',
            'course_code.string' => 'Course code should contain at least one alphabet.',
            'course_code.max'=>'Course code must be less than 20 characters.',
            //'course_code.in'=>'Course code does not exist in database.',
            
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
            
            'no_of_trainees.required' => 'No of Tarinees is required.',
            'no_of_trainees.integer' => 'No of trainees must be a number.',
            'no_of_trainees.max' => 'No of trainees must be less than or equal to 4294967295.',
            'remarks.max' => 'Remark must be less than 255 characters.',
            'deconflict.required' => 'Deconflict is required.',
            'deconflict.in_enum' => 'Deconflict value can be Yes or No only.',
        ]);

        $this->validate->after(function($validator) use($original, $course_codes) {

            if( isset($original['course_code']) && $original['course_code'] && !in_array($original['course_code'], $course_codes)){

                $validator->errors()->add('course_code', 'Course code does not exist in database.');
            }
        });

        return $this;
    }

    protected function afterSuccessCallBack() {

        $this->translateManytoOne(['Yes' => ['yes'], 'No' => ['no'] ], 'deconflict');
    }
}