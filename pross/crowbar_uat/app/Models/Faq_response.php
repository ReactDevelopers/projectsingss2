<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class Faq_response extends Model{
	   	protected $table = 'faq_response';	
        protected $primaryKey = 'id_response';

        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = ['id_response','faq_id','ip_address','response','language','created','updated'];

        public static function add($data){
            if(!empty($data)){
                $faq_response = json_decode(json_encode(self::where([
                    'faq_id'        => $data['faq_id'],
                    'ip_address'    => $data['ip_address']
                ])->get()),true);
                
                if(!empty($faq_response)){
                    $isUpdated = self::where('ip_address',$data['ip_address'])->where('faq_id',$data['faq_id'])->update($data);
                }else{
                    $isInserted = self::insert($data);
                }
                $total_response = self::where('faq_id', $data['faq_id'] )->count();             
                $liked_response = self::where(['faq_id' => $data['faq_id'], 'response' => 'like'])->count();
                return [
                	'total_response' => $total_response,
                	'liked_response' => $liked_response
                ];
            }else{
                return bool(false);   
            }
        }
	}