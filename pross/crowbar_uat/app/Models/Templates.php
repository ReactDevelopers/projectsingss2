<?php

	namespace App\Models;
	use DB;
	use Illuminate\Database\Eloquent\Model;

	class Templates extends Model{
	    protected $table = 'templates';

        /**
         * [This method is used for rows] 
         * @param [Varchar]$key [Used for keys]
         * @return Data Response
         */ 
        
        public static function rows($keys = array()){
            $handel = DB::table((new static)->getTable());

            if(!empty($keys)){
                $handel->select($keys);
            }
            
            return $handel->where('status', 'active');    
        }	    
	}
