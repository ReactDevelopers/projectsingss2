<?php
namespace App\Lib\DataVerify;

class DataHelper {

    protected $cellNameToDbCol = [];
    protected $data = [];
    protected $validate;
    protected $defaultValues =[];

    protected $addExtrafields = ['creator_id','updater_id','deleted_at','created_at','updated_at'];

    public function __construct(Array $data) {

        $this->data = ['original' => $data];
    }

    public function run() {

        $validate = $this->validate;
        $this->translateData();

        if( $validate->fails()) {

            return [
                'status'=> false,
                'errors' => $validate->errors()->toArray(),
                'data' => $this->data,
            ];

        } else {

            
            
            return [
                'status'=> true,
                'errors' => [],
                'data' => $this->data,
            ];
        }
    }

    protected function translateData() {
        
        $original = $this->data['original'];

        $translate = isset($this->data['translate']) ? $this->data['translate'] : [];

        foreach($this->cellNameToDbCol as $cell_name => $db_column) {

        $val = isset($original[$cell_name]) && $original[$cell_name] ? $original[$cell_name] :  ( isset($this->defaultValues[$cell_name]) ? $this->defaultValues[$cell_name] : null );
        $this->setTranslateData($val, $db_column);
        }
        if(in_array('created_at', $this->addExtrafields)){

        $this->setTranslateData(date('Y-m-d H:i:s'), 'created_at');
        }
        if(in_array('updated_at', $this->addExtrafields)){
            
            $this->setTranslateData(date('Y-m-d H:i:s'), 'updated_at');
        }

        if(in_array('creator_id', $this->addExtrafields)){
            $this->setTranslateData(\Auth::user()->id, 'creator_id');
        }
        if(in_array('updater_id', $this->addExtrafields)){
            $this->setTranslateData( \Auth::user()->id,'updater_id');
        }

        if(in_array('deleted_at', $this->addExtrafields) ){
        $this->setTranslateData(null,'deleted_at');
        }

        $this->afterSuccessCallBack();
    }

    protected function setTranslateData($value, $key) {

        $this->data['translate'][$key] = $value;
    }

    /**
     * Translate Yes, No if excel value value is in [Y,y,Yes, N,n,No]
     */
    protected function transformYesNo($excel_column) {

        $this->translateManytoOne(['Yes' =>['yes','y'], 'No' => ['no','n'] ], $excel_column);        
    }

    /**
     * Translate many to One
     * [one_value] => [many value]
     */
    protected function translateManytoOne(Array $translate_in, $excel_column) {
         
         $value = strtolower(isset($this->data['original'][$excel_column]) ? $this->data['original'][$excel_column] : '');

        foreach ($translate_in as   $value_should => $match_in) {

             if(in_array($value, $match_in)) {

                $this->data['translate'][$this->cellNameToDbCol[$excel_column]] = $value_should;
             }
        }
    }
    protected function afterSuccessCallBack(){}

    protected function findInDB(Array $all, String $excel_cell_name, Array $match_in_keys, String $db_column_name, String $error_message) {
        
        $original = $this->data['original'];
        $this->validate->after(function ($validator) use ($original, $all, $excel_cell_name, $match_in_keys, $db_column_name, $error_message) {

            $match_with = isset($original[$excel_cell_name]) ? $original[$excel_cell_name] : null;
            
            if($match_with) {

                $all = collect($all);

                $all = $all->filter(function ($value)  use($match_with, $match_in_keys) { 
                    
                    $isMatch = false;
                    foreach($match_in_keys as $db_key) {

                        if(strtolower($value[$db_key]) == strtolower($match_with)){
                            $isMatch = true;
                        }
                    }
                    
                    return $isMatch;
                });
                $matched = $all->first();
                if($matched) {
                    $this->setTranslateData($matched['id'], $db_column_name);

                } else {
                    $validator->errors()->add($excel_cell_name, $error_message);    
                }

            } else {

                $this->setTranslateData(null, $db_column_name);
            }

        });

        return $this;
    }
}