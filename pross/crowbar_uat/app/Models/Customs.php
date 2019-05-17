<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class Customs extends Model{

		/**
         * [This method is used to insertIgnoreQuery] 
         * @param [Varchar] $data [Used for data]
         * @param [String] $table[Used for table]
         * @return Data Response
         */

		public static function insertIgnoreQuery(array $data,$table){
			if(!isset($data[0]) || !is_array($data[0])){
				return false;
			}

			$keys = array_keys($data[0]);
			$keys = '`'.implode('`,`', $keys).'`';

			$values = self::buildValueSrting($data);

			$query = "INSERT IGNORE INTO `{$table}` ({$keys}) values {$values}";
			return  $query;
		}

		/**
         * [This method is used for build value string] 
         * @param [Varchar] $data [Used for data]
         * @return Data Response
         */

		public static function buildValueSrting($data){
		    $values = [];
		    array_walk($data, function($v) use (&$values){
		       $vin = [];
		       array_walk($v, function($v1) use (&$values,&$vin){
		           $vm = (is_integer($v1)?$v1:((strpos(strtolower($v1), 'select ') !==false)?'('.$v1.')':"'{$v1}'"));
		           $vin[] = $vm;
		       });
		       $vin = implode(',', $vin);
		       $values[] = $vin;
		    });

		    return $values = '('.implode('),(', $values).')';
	  	}
	}
