<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class companyConnectedTalent extends Model
{
    protected $table = 'company_connected_talent';
	protected $primaryKey = 'id_company_connected_talent';

	protected $fillable = ['id_talent_company','id_user','user_type','created','updated'];

	public function user(){
        return $this->hasOne('\Models\Talents','id_user','id_user');
    }

    public function company(){
        return $this->hasOne('\Models\TalentCompany','talent_company_id','id_talent_company');
    }

    public function getProfile(){
		return $this->hasOne('\Models\File','user_id','id_user')->where('type','profile');
    }

    public function juris(){
        // return $this->hasOne('\Models\FirmJurisdiction','company_id','id_talent_company');
        return $this->belongsTo('\Models\FirmJurisdiction','id_talent_company','company_id');
    }

	public static  function get_file($where = "",$fetch = 'all',$keys = ['*']){
        $table_files = \DB::table('files');
        $table_files->select($keys);

        if(!empty($where)){
            $table_files->whereRaw($where);
        }

        if($fetch == 'count'){
            return $table_files->get()->count();
        }else if($fetch == 'single'){
            return (array) $table_files->get()->first();
        }else if($fetch == 'all'){
            $result = json_decode(json_encode($table_files->get()),true);
            
            foreach ($result as &$item) {
                $item['file_url'] = asset(sprintf('%s%s',$item['folder'],$item['filename']));
                $item['filename'] = $item['filename'];
            }
            
            return $result;
        }else{
            return $table_files->get();
        }
    }

    public function companyDetails(){
        return $this->belongsToMany('\Models\TalentCompany','firm_jurisdiction','company_id','country_id');
    }

    

}
