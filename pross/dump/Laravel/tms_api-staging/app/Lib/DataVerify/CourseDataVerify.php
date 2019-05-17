<?php
namespace App\Lib\DataVerify;

use Validator;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\TrainingLocation;
use App\Models\AssessmentType;
use App\Models\Department;
use App\Models\DeliveryMethod;

class CourseDataVerify extends DataHelper {

    protected $cellNameToDbCol = [
        'course_code' => 'course_code',
        'course_title' => 'title',
        'duration_no_of_days' =>'duration_in_days',
        'mandatory_yn' => 'mandatory',
        //'delivery_method' => 'delivery_method',
        'costpax_without_gst' => 'cost_per_pax',
        'grant' => 'type_of_grant',
        'funding_type' => 'vendor_email',
        'placement_criteria' => 'placement_criteria',
        'cts_to_approve_future_placements' => 'cts_approve_future_placement',
        'course_provider' => 'course_provider',
        'compulsory_yn' =>  'compulsory'
    ];

    protected $defaultValues = [        
        'mandatory_yn' => 'Yes',
        'compulsory_yn' => 'No',
        //'cts_to_approve_future_placements' => 'No',
    ];
    

    public function __construct(Array $data) {

        parent::__construct($data);

        $this->validator()
            ->findCategory()
            ->findProgrammeType()
            //->findCourseProvider()
            ->findTrainingLocation()
            ->findAssessmentType()
            ->findDepartment()
            ->findDeliveryMethod();
    }

    /**
     * Validate the Course Data
     */
    protected function validator() {
        
        $original = $this->data['original'];

        $validate = Validator::make($original, [
            'course_code' => 'required|max:20|regex:/[a-z0-9]+$/i',
            'course_title' => 'required|max:255',
            'duration_no_of_days' => 'required|numeric|max:999',
            'programme_category' => 'required',
            'programme_type' => 'required',
            'mandatory_yn' => 'required|in_enum:Yes,No,Y,N',
            'cts_to_approve_future_placements' => 'required|in_enum:Yes,No,Y,N,NA',
            'delivery_method' =>'nullable|max:255',
            'placement_criteria' =>'nullable',
            'training_location' => 'required',
            'course_provider' =>'required|max:255',
            'costpax_without_gst' => ['nullable','regex:/^\d{0,6}(\.\d{1,2})?$/'],
            'grant'=> 'nullable|in_enum:Y,N,Yes,No',
            'funding_type'=> 'nullable|max:255'
        ],[
            'costpax_without_gst.required' => 'Cost Per Pax is required.',
            'costpax_without_gst.regex' => 'Cost Per Pax should not be greater than 999999.99',
            //'if_yes_provide_value.regex' => '"If Yes Provide Value" should not be greater than 999999.99',
            //'if_yes_provide_value.required_if' => '"If Yes Provide Value"  is required if Grant Subsidy value is "Yes"',
            'funding_type.max' => 'Course Provider/Vendor may not be greater than 255 characters.',
            //'grant.max' => 'Type of Grant Value may not be greater than 255 characters.',
            //'placement_criteria.max' => 'Placement Criteria Value may not be greater than 255 characters.',
            "course_provider" => 'Course provider is required.',
            'training_location' => 'Training Location is required.',
            'delivery_method'=> 'Delivery Method is required.',
            'mandatory_yn.in_enum'=> 'Mandatory value can be Yes, Y, N, or No only.',
            'grant.in_enum'=> 'Grant can be Yes, Y, N, or No only.',
            'cts_to_approve_future_placements.in_enum'=> 'CTS to approve Future Placements can be Yes, Y, N,No, or NA only.',
            'programme_type.required' => 'Programme Type is required.',
            'programme_category.required' => 'Programme Category is required.',
            'duration_no_of_days.required' => 'Duration is required.',
            'duration_no_of_days.numeric' => 'Duration value should only be a number.',
            'duration_no_of_days.max'=>'Duration value should not be greater than 999.',
            'course_title.required' => 'Course Title is required.',
            'course_title.max' => 'Course Title may not be greater than 255 characters.',
            'course_code.required' => 'Course Code is required.',
            'course_code.regex' => 'Course Code must only be contain the alphabets or numbers.',
            'course_code.max' => 'Course Title may not be greater than 20 characters.',
        ]);
        
   
        $this->validate = $validate;

        return $this;
    }

    /**
     * Find Programme Category in database
     */
    private function findCategory() {
        return $this->findInDB(
            ProgrammeCategory::getCached(), 
            'programme_category', 
            ['prog_category_code', 'prog_category_name'], 
            'programme_category_id', 
            'Programme Category name does not match in database.'
        );
    }


    /**
     * Find Department in database
     */
    private function findDepartment() {
        
        return $this->findInDB(
            Department::getCached(), 
            'competency_level_if_applicable', 
            ['dept_code', 'dept_name'], 
            'department_id', 
            'Competency Level does not match in database.'
        );
    }

    /**
     * Find Programme Type in Database
     */
    private function findProgrammeType() {

        return $this->findInDB(
            ProgrammeType::getCached(),
            'programme_type',
            ['prog_type_code', 'prog_type_name'],
            'programme_type_id',
            'Programme Type does not match in database.'
        );
        
    }

    /**
     * Find Training Location in Database 
     */
    private function findTrainingLocation() {

        return $this->findInDB(
            TrainingLocation::getCached(),
            'training_location',
            ['location'],
            'training_location_id',
            'Training Location does not match in database.'
        );
    }
    
     /**
     * Find Training Location in Database 
     */
    // private function findCourseProvider() {

    //     return $this->findInDB(
    //         CourseProvider::getCached(),
    //         'course_provider',
    //         ['provider_name'],
    //         'course_provider_id',
    //         'Course Provider does not match in database.'
    //     );
    // }

    private function findAssessmentType() {

        return $this->findInDB(
            AssessmentType::getCached(),
            'assessment_type',
            ['assessment_type_name'],
            'assessment_type_id',
            'Assessment Type  does not match in database.'
        );
    }

    private function findDeliveryMethod() {

        return $this->findInDB(
            DeliveryMethod::getCached(),
            'delivery_method',
            ['name'],
            'delivery_method_id',
            'Delivery Method does not match in database.'
        );
    }

    /**
     * Translate mandatory_yn value according to database Enum Collection
     */
    protected function afterSuccessCallBack() {        
        
        $this->transformYesNo('mandatory_yn');
        $this->transformYesNo('compulsory_yn');        
        //$this->transformYesNo('cts_to_approve_future_placements');  
        $this->translateManytoOne(['Yes' =>['yes','y'], 'No' => ['no','n'] , 'NA' => ['na'] ], 'cts_to_approve_future_placements' );
        $this->transformYesNo('grant');        
    }    
    
}