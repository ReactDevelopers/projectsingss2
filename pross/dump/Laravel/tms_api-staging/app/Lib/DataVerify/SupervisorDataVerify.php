<?php
namespace App\Lib\DataVerify;

use Validator;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\TrainingLocation;

class SupervisorDataVerify extends DataHelper {

    protected $cellNameToDbCol = [
        'per_id' => 'personnel_number',
        'sup_id' => 'supervisor_personnel_number',
    ];

    private $pubnetId =[];

    protected $addExtrafields = ['created_at','updated_at'];

    public function __construct(Array $data, Array $pubnetId) {

        parent::__construct($data);
        $this->pubnetId = $pubnetId;
        $this->validator();
    }

    /**
     * Validate the Course Data
     */
    protected function validator() {
        
        $original = $this->data['original'];
        $per_ids = $this->pubnetId;

        $validate = Validator::make($original, [
            'per_id' => 'required|integer',
            'sup_id' => 'nullable|integer',
        ],[
            'per_id.required' => 'Personnel Number is required.',
            //'per_id.in' => 'Personnel Number does not exist in database.',
            'per_id.integer' =>'Per id must be a number.',
            'per_id.required' => 'Supervisor Personnel Number is required.',
            //'sup_id.in' => 'Supervisor Personnel Number does not exist in database.',
            'sup_id.integer' =>'Sup id must be a number.',
        ]);
        $this->validate = $validate;

        $this->validate->after(function($validator) use($original, $per_ids) {

            if( isset($original['per_id']) && $original['per_id'] && !in_array($original['per_id'], $per_ids)){

                $validator->errors()->add('per_id', 'Personnel Number does not exist in database.');
            }

            if( isset($original['sup_id']) && $original['sup_id'] && !in_array($original['sup_id'], $per_ids)){

                $validator->errors()->add('sup_id', 'Supervisor Personnel Number does not exist in database.');
            }
        });
        return $this;
    }
}