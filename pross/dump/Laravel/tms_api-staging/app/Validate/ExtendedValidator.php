<?php 

namespace App\Validate;
 
use Illuminate\Validation\Validator;
use Illuminate\Http\UploadedFile;
 
class ExtendedValidator extends Validator {
 	  
    /**
     * validate the field , the field should only contains the emails seperated  by comma
     */
    protected function validateEmails($attribute, $values, $parameters) {

        $value = explode(',', $values);
        $rules = [
            'email' => 'required|email',
        ];
        if (!empty($values) && $value) {

            foreach ($value as $email) {
                $data = [
                    'email' => $email
                ];
                $validator = \Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        }

        return true;
    }

    /**
     * Validate the field, the field should contains only the given words [Here, we are ignoring the case sensitive]
     */
    protected function validateInEnum($attribute, $value, $parameters) {

        $this->requireParameterCount(1, $parameters, 'in_enum');
        
        if($value === '' || $value === null) {
            return true;
        }

        $should_match_with_arr = $parameters;       
        $is_match = false;

        foreach($should_match_with_arr as $in) {

            if(strtolower($value) == strtolower($in)) {
                $is_match = true;
            }
        }

        return $is_match;
    }
    /**
     * Validate for date shuold after the given input date
     * @param  string $attribute  element name
     * @param  string $value      Date
     * @param  array $parameters 
     * @return boolen 
     */
    protected function validateInArrAfterDate($attribute, $value, $parameters){
        
        $value = $this->verifyDateFormat($value);
        
        if(empty($value)) {
            
            return true;

        } else if($value == 'InvalidDate') {

            return false;
        }

        $this->requireParameterCount(1, $parameters, 'in_arr_after_date');
        preg_match_all('/(?<=\.)[0-9]+/',$attribute,$indexs);
        $indexs = isset($indexs[0]) ? $indexs[0] : [];

        $anotherElement = $parameters[0];
        $anotherElement = str_replace_array('*',$indexs,$anotherElement);
        $anotherValue = $this->getValue($anotherElement);
        $anotherValue = $this->verifyDateFormat($anotherValue);
        
        if($anotherValue == 'InvalidDate') {
            return true;
        }

        $anotherDate = \Carbon\Carbon::parse($anotherValue);

        $date = \Carbon\Carbon::parse($value);
        return ($anotherDate->diffInMinutes($date,false) >1);

    }

    /**
     * Validate for date shuold after or equal to given input date
     * @param  string $attribute  element name
     * @param  string $value      Date
     * @param  array $parameters 
     * @return boolen 
     */
    protected function validateInArrAfterEqualDate($attribute, $value, $parameters){

        $value = $this->verifyDateFormat($value);

        if(empty($value)) {
            
            return true;

        } else if($value == 'InvalidDate') {

            return false;
        }

        $this->requireParameterCount(1, $parameters, 'in_arr_after_equal_date');
        preg_match_all('/(?<=\.)[0-9]+/',$attribute,$indexs);
        $indexs = isset($indexs[0]) ? $indexs[0] : [];

        $anotherElement = $parameters[0];
        $anotherElement = str_replace_array('*',$indexs,$anotherElement);
        $anotherValue = $this->getValue($anotherElement);
        $anotherValue = $this->verifyDateFormat($anotherValue);

        if($anotherValue == 'InvalidDate') {
            return true;
        }
        $anotherDate = \Carbon\Carbon::parse($anotherValue);
        $date = \Carbon\Carbon::parse($value);
        return ($anotherDate->diffInMinutes($date,false) >=0);

    }

    private function verifyDateFormat($date) {

        return $date ? (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date) ? $date : 'InvalidDate') : $date;
    }

    
    /**
     * TO check file Extension
     */
    protected function validateExtIn($attribute, $value, $parameters) {

        $this->requireParameterCount(1, $parameters, 'ext_in');
        
        if($value && $value instanceof UploadedFile && $value->isValid() ){

            $file_name  = $value->getClientOriginalName();
            $file_name_arr = explode('.', $file_name);
            $ext = strtolower(end($file_name_arr));
            return in_array($ext, $parameters);
        }

        return true;
    }
}