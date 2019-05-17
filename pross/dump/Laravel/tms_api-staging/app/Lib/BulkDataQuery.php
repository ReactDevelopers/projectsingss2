<?php

namespace App\Lib;


Trait BulkDataQuery 
{
	/**
	* Build the values statement for Mysql Bulk insert query from Array
	* @param Array
	* @return string
	**/

	public function buildValueStatement(array $data)
	{	
		 $values = [];

		 array_walk($data, function($v) use (&$values){
	        
	        $columns_values = [];
	        array_walk($v, function($v1) use (&$values,&$columns_values){
	            	
	           	$slash_str =  addslashes($v1);

	           	switch (true) {
	           		case is_null($v1):
	           			$column_value = 'null';
	           			break;

	           		case is_numeric($v1):
	           			$column_value  = $v1;
	           			break;

	           		case $this->isAQuery($v1):
	           			$column_value = '('.rtrim($v1,';').')';
	           			break;

	           		default:
	           			$column_value = "'{$slash_str}'";
	           			break;
	           	}

	            $columns_values[] = $column_value;

	        });
	        $columns_values = implode(',', $columns_values);

	        $values[] = $columns_values;          
	    });

		return '('.implode('),(', $values).')';
	}

	/**
	* Function is checking the string is in mysql query format or not.
	* @param String
	* @return Bool
	**/

	private function isAQuery($str)
	{
		return preg_match("/^(\s*?)select\s*?.*?\s*?from([\s]|[^;]|(['\"].*;.*['\"]))*?;\s*?$/i", $str);
	}

	/**
	 * Function builds the Insert and ignore statement.
	 * @param Array - Bulk data 
	 * @param String - $table - Table name.
	 * @return String
	 **/

	public function insertIgnoreQuery(array $data,$table)
	{

		if(!isset($data[0]) || !is_array($data[0])){
			return false;
		}

		$keys = array_keys($data[0]);
	    $keys = '`'.implode('`,`', $keys).'`';

	    $values = $this->buildValueStatement($data);

	    $query = "INSERT IGNORE INTO `{$table}` ({$keys}) values {$values}";
	    return  $query;
	}

	/**
	 * Function builds the Insert and Update statement.
	 * @param Array - Bulk data 
	 * @param String - $table - Table name.
	 * @param Array - Column name , which do you want to update.
	 * @return String
	 **/

	public function insertUpdateQuery(array $data,$table, array $update_columns)
	{

		if(!isset($data[0]) || !is_array($data[0])){
			return false;
		}

		$keys = array_keys($data[0]);
	    $keys = '`'.implode('`,`', $keys).'`';

	    $values = $this->buildValueStatement($data);
	    
	    $update_columnss  =[];
        array_walk($update_columns, function($v) use(&$update_columnss){

        	$update_columnss[] = '`'.$v.'`=  values(`'.$v.'`)';
        });

        $update_columnssStr = implode(',', $update_columnss);


	    $query = "INSERT INTO `{$table}` ({$keys}) values {$values} ON DUPLICATE KEY UPDATE {$update_columnssStr}";
	    return  $query;
	}

	/**
	 * Function executes the Insert the Update query.
	 * @param Array - Bulk data - $data
	 * @param Array - Column name , which do you want to update.
	 * @return Array
	 **/

	public static function batchInsertUpdate(array $data,array $update_columns,$size=10000)
	{	
		$data_count = count($data);

		if(!$data_count)
			return NULL;

		$self = new static;

		$table_name = $self->getTable();
		
		$next_id = $self->tableNextIncrement();

		$chunk_data = array_chunk($data, $size);

		foreach($chunk_data as $row_data){

			$query = $self->insertUpdateQuery($row_data,$table_name,$update_columns);
			if($query) {
				$self->getConnection()->statement($query);			
			}
		}
		
		return $self->batchResponse($next_id,$data_count,false);
	}

	/**
	 * Function executes the Insert the Ingore query.
	 * @param Array - Bulk data - $data
	 * @param Array - Column name , which do you want to update.
	 * @return Array
	 **/

	public static function batchInsertIgnore(array $data,$size=10000)
	{		

		$data_count = count($data);
		if(!$data_count)
			return NULL;

		$self = new static;
		$table_name = $self->getTable();

		$next_id = $self->tableNextIncrement();

		$chunk_data = array_chunk($data, $size);

		foreach($chunk_data as $row_data){

			$query = $self->insertIgnoreQuery($row_data,$table_name);
			
			if($query) {
				$self->getConnection()->statement($query);
			}
		}

		return $self->batchResponse($next_id,$data_count);		

	}

	/**
	 * For getting Table Status
	 * @return [Null|integer]
	 */
	private function tableNextIncrement()
	{
		$table_name = $this->getTable();
		$table_status = $this->getConnection()->select("SHOW TABLE STATUS LIKE '{$table_name}'");
		return $table_status[0]->Auto_increment;
	}

	/**
	 * For repairing the Table max number.
	 * @return   integer
	 */
	
	private function repairPrimaryNumber()
	{	
		$max_id = $this->max($this->getKeyName());
		$max_id = ($max_id+1);
		$table_name = $this->getTable();
		$this->getConnection()->statement('ALTER TABLE '.$table_name.' AUTO_INCREMENT='.$max_id);
		return $max_id;
	}

	/**
	 * Response the batch insert and update data.
	 * @return   Array|TRUE
	 */
	
	private function batchResponse($next_id,$data_count,$for_insert=true)
	{

		return ($next_id !== null ) ? $this->getInfo($next_id,$data_count,$for_insert) : true;
	}

	private function getInfo($next_id,$data_count,$for_insert=true) {

		$new_max_id = $this->repairPrimaryNumber();
		$inserted = ($new_max_id-$next_id);
		$inserted = ($inserted > $data_count)?$data_count:$inserted;
		$inserted = $inserted < 0 ? 0 : $inserted;

		$ignored = ($data_count-$inserted);
		$iu_column = ($for_insert)?'ignored':'updated';
		return ['total'=>$data_count, $iu_column=>$ignored, 'inserted'=>$inserted];
	}
}