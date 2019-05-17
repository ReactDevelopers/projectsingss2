<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\CourseDataVerify;

abstract class ColumnTestCase extends TestCase {
    
    use DatabaseTransactions;
    
    protected $row = [];
    protected $dataVerifyClass = null;
    protected $columnName = '';
    protected $translate = [];
    protected $original = [];

    protected function makeBlank() {

        $this->row[$this->columnName] = null;
        return $this;
    }
    protected function setColumn($column) {
        $this->columnName = $column;
        return $this;
    }
    protected function setValue($val, $column =null) {

        $column = $column ? $column : $this->columnName;
        $this->row[$column] = $val;
        return $this;
    }

    protected function makeSomeChar($length) {
        
        $faker = \Faker\Factory::create();
        $str = $faker->words($length, true);
        $str = substr($str, 0, $length);
        $this->row[$this->columnName] = $faker->shuffle($str);
        return $this;
    }

    protected function makeDigit($min =1000 ,$max = 9999) {
        
        $faker = \Faker\Factory::create();
        $this->row[$this->columnName] = $faker->numberBetween($min, $max);
        return $this;
    }

    protected function printRow() {

        print_r($this->row);
        exit;
        return $this;
    }

    protected function makeAlpha($length){

        $faker = \Faker\Factory::create();
        $this->row[$this->columnName] = $faker->regexify('[a-z]{'.$length.'}');
        return $this;
    }

    protected function makeSpecialChar($length) {
        
        $faker = \Faker\Factory::create();
        $str = '%$#@!//{[]})(*&^_-+=.,?"\':;';
        if(strlen($str) < $length) {
            $how_much_less = $length - strlen($str);
            $str .= $faker->words($how_much_less, true);
        }

        $str = substr($str, 0, $length);        
        $this->row[$this->columnName] = $faker->shuffle($str);

        return $this;
    }

    protected function checkColumn($valid = false, $column= null, $print_result =false) {
        
        $column = $column ? $column : $this->columnName;
        
        if($this->dataVerifyClass){

            $data_verifier_inst = $this->ins();
            $result = $data_verifier_inst->run();
            //print_r($result); exit;
            $this->translate = $result['data']['translate'];
            $this->original = $result['data']['original'];
            if($print_result) {
                print_r($result); exit;
            }
            ($valid) ? $this->assertTrue(!isset( $result['errors'][$column])) : $this->assertTrue(isset( $result['errors'][$column]));            

            
        }

        return $this;
    }

    protected function ins() {

        return  new $this->dataVerifyClass($this->row);
    }

    protected function checkTraslateValue($val, $column= null) {
        
        $column = $column ? $column : $this->columnName;
        
        $this->assertTrue(($this->translate[$column] == $val));
        return $this;
    }
}