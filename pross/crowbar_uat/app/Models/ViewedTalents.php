<?php 
    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class ViewedTalents extends Model{
    	protected $table  = 'viewed_talent';

    	const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        /**
         * [This method is used to add all viewed user's] 
         * @param [Varchar]$data [Used for data]
         * @return Boolean
         */	

        public static function add_viewed_talent($data){
            if(empty($data)){
                return (bool) false;
            }else{
            	$viewed_record = json_decode(json_encode(self::where([
            		'employer_id' 	=> $data['employer_id'],
            		'talent_id' 	=> $data['talent_id']
            	])
            	->first()),true);
	            if(!empty($viewed_record)){
            		if(strtotime($viewed_record['updated']) < strtotime(date('Y-m-d'))){
	            		$isUpdated = self::where([
		            		'employer_id' 	=> $data['employer_id'],
		            		'talent_id' 	=> $data['talent_id']
	            		])->update([
	            			'updated' => $data['updated']
	            		]);
	            		if($isUpdated){
	            			return $viewed_record['updated'];
	            		}else{
	            			return (bool)false;
	            		}
	            	}else{
	            		return $viewed_record['updated'];
	            	}
	            }else{
	                if(self::insert($data)){
	                    return (bool)true;
	                }else{
	                    return (bool) false;
	                }
	            }
            }
        }    	
    }
?>