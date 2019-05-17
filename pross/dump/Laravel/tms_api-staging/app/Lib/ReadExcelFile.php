<?php 

namespace App\Lib;

use PhpOffice\PhpSpreadsheet\IOFactory as ExcelReader;

class ReadExcelFile {

    private $dateCell  ='';

    public function __construct(Array $dateCell =[]) {

        $this->dateCell = $dateCell;
    }

    public static function getCollection(String $path, String $lastCell) {

        $sheet = ExcelReader::load($path)->getActiveSheet();        
        $total = $sheet->getHighestDataRow();
        $i = 1;
        $header = [];
        $data = [];
        $_this = new static;
        
        while($i <= $total ) {

            if($i ==1) {
                              
                $header = $sheet->rangeToArray('A'.$i.':'.$lastCell.$i);

                $header = $header[0];
                array_walk($header, function(&$v) {
                    $v = str_slug($v,'_');
                });

            } else {

                $row = $sheet->rangeToArray('A'.$i.':'.$lastCell.$i,null, false, false);
                $row = array_combine($header, $row[0]);

                foreach($row as $key => $value) {
                    
                    $methedName = 'set'.ucfirst(camel_case($key));
                    if( method_exists($_this, $methedName) ) {
                        $row[$key] = self::$methedName(trim($value));
                    } else {
                        
                        $row[$key] = trim($value);
                    }
                }

                $data[] = collect($row);                
            }
            $i++;
        }

        return collect($data);
    }
    
    public static function setCourseStartDate($value){

        return  self::dateFormat($value);
    }

    public static function setCourseEndDate($value){

        return self::dateFormat($value);
    }

    public static function setAssessmentStartDate($value){
        return self::dateFormat($value);
    }

    public static function setAssessmentEndDate($value){

        return self::dateFormat($value);
    }

    /**
     * TO Convert the Excel Date into Php Date Format
     */
    protected static function dateFormat($value){

        return $value && is_numeric($value) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d') : (!is_numeric($value) && !empty($value) ? 'InvalidDate': null);
    }
}