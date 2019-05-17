<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Payout_mgmt extends Model
{
    protected $table = 'payout_mgmt';
    protected $primaryKey = 'id';
    
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * [This method is for listing all payouts managements] 
     * @return Boolean
     */
    public static function listPayoutsMgmt(){

        $prefix         = \DB::getTablePrefix();
        \DB::statement(\DB::raw('set @row_number=0'));
        $payout_mgmt_list = \DB::table('payout_mgmt');
        $payout_mgmt_list->select([
        	\DB::raw('@row_number  := @row_number  + 1 AS row_number'),
        	'payout_mgmt.id',
            'countries.en as country',
        	'countries.id_country as country_id',
        	'industries.en as industry',
            \DB::raw("CONCAT({$prefix}payout_mgmt.pay_commision_percent,' %') as pay_commision_percent"),
        	\DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.accept_escrow,1,1)),MID({$prefix}payout_mgmt.accept_escrow,2)) as accept_escrow"),
        	\DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.pay_commision,1,1)),MID({$prefix}payout_mgmt.pay_commision,2)) as pay_commision"),
        	\DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.identification_number,1,1)),MID({$prefix}payout_mgmt.identification_number,2)) as identification_number"),
        	\DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.status,1,1)),MID({$prefix}payout_mgmt.status,2)) as status"),
        	'payout_mgmt.created',
        ]);
        $payout_mgmt_list->leftJoin('countries','countries.id_country','=','payout_mgmt.country');
        $payout_mgmt_list->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry');
        $payout_mgmt_list->where('payout_mgmt.status','active');
        $payout_mgmt_list->where('industries.id_industry','=','1');
        $payout_mgmt_list->orderBy('payout_mgmt.id','desc');

        return $payout_mgmt_list->get();
    }

    public static function validatePayoutsMgmt($country){

        $payout_valid = \DB::table('payout_mgmt')
        					->select('id')
        					->where('country',$country)
        					->where('status','active')
        					->first();
       
       	if(!empty($payout_valid)){
       		return true;
       	}else{
       		return false;
       	}
    }

    /*To check if Escrow is required or not*/
    public static function validateEscrowPayoutsMgmt($country,$industry){

        $payout_valid = \DB::table('payout_mgmt')
                            ->select('id')
                            ->where('country',$country)
                            ->where('industry',$industry)
                            ->where('accept_escrow','no')
                            ->where('status','active')
                            ->first();
       
        if(!empty($payout_valid)){
            return true;
        }else{
            return false;
        }
    }

    public static function deletePayoutsMgmt($country_id,$status = 'deleted'){

    	$payout_delete = 0;
        $payout_delete = \DB::table('payout_mgmt')
        					->where('country',$country_id)
        					->update(['status'=>$status]);

        return (bool)$payout_delete; 
    }

    /**
     * [This method is for getting professions(industry) which have been added by admin to ask for identification number, so that user can enter identification number in front end.] 
     * @return arr
     */
    public static function userCheckIdentificationNumber($country_id){

        $prefix         = \DB::getTablePrefix();
        \DB::statement(\DB::raw('set @row_number=0'));
        $payout_mgmt_list = \DB::table('payout_mgmt');
        $payout_mgmt_list->select([
            \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
            'payout_mgmt.id',
            'countries.en as country',
            'industries.en as industry',
            'payout_mgmt.industry as industry_id',
        ]);
        $payout_mgmt_list->leftJoin('countries','countries.id_country','=','payout_mgmt.country');
        $payout_mgmt_list->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry');
        $payout_mgmt_list->where('payout_mgmt.status','active');
        if($country_id != 0){
            $payout_mgmt_list->where('payout_mgmt.country',$country_id);
        }
        // $payout_mgmt_list->where('payout_mgmt.identification_number','yes'); 
        $payout_mgmt_list->where('payout_mgmt.is_registered_show','yes'); 
        $payout_mgmt_list->orderBy('payout_mgmt.id','desc');
        $payout_mgmt_list = $payout_mgmt_list->get();

        $payout_mgmt_list =  json_decode(json_encode($payout_mgmt_list),true);

        if(!empty($payout_mgmt_list)){
            $payout_mgmt_list = implode(',', array_column($payout_mgmt_list, 'industry_id'));
            return $payout_mgmt_list;
        }else{
            return '';
        }
    }

    /**
     * [This method is for getting professions(industry) which have been added by admin to ask for identification number, so that user can enter identification number in front end.] 
     * @return arr
     */
    public static function userCheckIsRegistered($country_id,$talent_industry_id){

        $prefix         = \DB::getTablePrefix();
        \DB::statement(\DB::raw('set @row_number=0'));
        $payout_mgmt_list = \DB::table('payout_mgmt');
        $payout_mgmt_list->select([
            \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
            'payout_mgmt.id',
            'countries.en as country',
            'industries.en as industry',
            'payout_mgmt.industry as industry_id',
            'payout_mgmt.is_registered_show',
        ]);
        $payout_mgmt_list->leftJoin('countries','countries.id_country','=','payout_mgmt.country');
        $payout_mgmt_list->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry');
        $payout_mgmt_list->where('payout_mgmt.status','active');
        if($country_id != 0){
            $payout_mgmt_list->where('payout_mgmt.country',$country_id);
        }
        if($talent_industry_id != 0){
            $payout_mgmt_list->where('payout_mgmt.industry',$talent_industry_id);
        }
        // $payout_mgmt_list->where('payout_mgmt.identification_number','yes'); 
        $payout_mgmt_list->orderBy('payout_mgmt.id','desc');
        $payout_mgmt_list = $payout_mgmt_list->get();

        $payout_mgmt_list =  json_decode(json_encode($payout_mgmt_list),true);
        if(!empty($payout_mgmt_list)){
            $payout_mgmt_list = implode(',', array_column($payout_mgmt_list, 'is_registered_show'));
            return $payout_mgmt_list;
        }else{
            return '';
        }
    }

    /*To check if Escrow is required or not*/
    public static function toCheckPayCommision($country,$industry){

        $pay_commission_valid = \DB::table('payout_mgmt')
                            ->select('pay_commision')
                            ->where('country',$country)
                            ->where('industry',$industry)
                            ->where('status','active')
                            ->first();
       
        if(!empty($pay_commission_valid) && $pay_commission_valid->pay_commision == 'yes'){
            return true;
        }elseif(empty($pay_commission_valid)){
            return true;
        }else{
            return false;
        }
    }

    public static function toPayCommisionPercent($country,$industry){

        $pay_commision_percent = \DB::table('payout_mgmt')
                            ->select('pay_commision_percent')
                            ->where('country',$country)
                            ->where('industry',$industry)
                            ->where('status','active')
                            ->first();
       
        if(!empty($pay_commision_percent) ){
            return $pay_commision_percent->pay_commision_percent;
        }
    }

    public static function toGetPayoutDetails($country,$industry){
        $payout_details = \DB::table('payout_mgmt')
                            ->select('*')
                            ->where('country',$country)
                            ->where('industry',$industry)
                            ->where('status','active')
                            ->first();
       
        return json_decode(json_encode($payout_details),true);
    }

    public static function getPayoutMgmtByCountry($country){

        $country_payout = \DB::table('payout_mgmt')
                            ->select('*')
                            ->where('country',$country)
                            ->where('status','active')
                            ->get();
       
        if(!empty($country_payout)){
            return json_decode(json_encode($country_payout),true);
        }
    }

    public static function getPayoutByCountryId($country_id){

        $payout_details = 0;
        $payout_details = \DB::table('payout_mgmt')
                            ->select('*')
                            ->where('country',$country_id)
                            ->get();

        $payout_details = json_decode(json_encode($payout_details),true);

        $return_arr = array();
        foreach ($payout_details as $key => $value) {
            $return_arr[$value['industry']] = $value;
        }
        return $return_arr;  
    }

    public static function showConfigurationByCountryId($country_id){

        $prefix         = \DB::getTablePrefix();
        $payout_details = 0;
        $payout_details = \DB::table('payout_mgmt')
                            ->select(['payout_mgmt.*',
                                      'industries.en as industry_name',
                                      \DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.accept_escrow,1,1)),MID({$prefix}payout_mgmt.accept_escrow,2)) as accept_escrow"),
                                      \DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.non_reg_accept_escrow,1,1)),MID({$prefix}payout_mgmt.non_reg_accept_escrow,2)) as non_reg_accept_escrow"),
                                      \DB::raw("CONCAT(UCASE(MID({$prefix}payout_mgmt.identification_number,1,1)),MID({$prefix}payout_mgmt.identification_number,2)) as identification_number"),
                                      ])
                            ->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry')
                            ->where('payout_mgmt.country',$country_id)
                            ->get();

        $payout_details = json_decode(json_encode($payout_details),true);

        $return_arr = array();
        foreach ($payout_details as $key => $value) {
            $return_arr[$value['industry']] = $value;
        }
        // dd($return_arr);
        return $return_arr;  
    }

    public static function getCountryNameById($country_id){

        $country_name = '';
        $country_name = \DB::table('countries')
                            ->select('en')
                            ->where('id_country',$country_id)
                            ->first();

        return $country_name->en;  
          
    }

    public static function talentCheckIdentificationNo($country,$industry_id){

        $identification_no = \DB::table('payout_mgmt')
                            ->select('identification_number')
                            ->where('country',$country)
                            ->where('industry',$industry_id)
                            ->where('status','active')
                            ->first();
       
        if(!empty($identification_no) && $identification_no->identification_number == 'yes'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * [This method is for getting professions(industry) which have been added by admin to ask for identification number, so that user can enter identification number in front end.] 
     * @return arr
     */
    public static function usertalentCheckIdentificationNo($country_id){

        $prefix         = \DB::getTablePrefix();
        \DB::statement(\DB::raw('set @row_number=0'));
        $payout_mgmt_list = \DB::table('payout_mgmt');
        $payout_mgmt_list->select([
            \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
            'payout_mgmt.id',
            'countries.en as country',
            'industries.en as industry',
            'payout_mgmt.industry as industry_id',
        ]);
        $payout_mgmt_list->leftJoin('countries','countries.id_country','=','payout_mgmt.country');
        $payout_mgmt_list->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry');
        $payout_mgmt_list->where('payout_mgmt.status','active');
        if($country_id != 0){
            $payout_mgmt_list->where('payout_mgmt.country',$country_id);
        }
        // $payout_mgmt_list->where('payout_mgmt.identification_number','yes'); 
        // $payout_mgmt_list->where('payout_mgmt.is_registered_show','yes'); 
        $payout_mgmt_list->where('payout_mgmt.identification_number','yes'); 
        $payout_mgmt_list->orderBy('payout_mgmt.id','desc');
        $payout_mgmt_list = $payout_mgmt_list->get();

        $payout_mgmt_list =  json_decode(json_encode($payout_mgmt_list),true);

        if(!empty($payout_mgmt_list)){
            $payout_mgmt_list = implode(',', array_column($payout_mgmt_list, 'industry_id'));
            return $payout_mgmt_list;
        }else{
            return '';
        }
    }

    /*Add Manual Payout for this New inserted Industry for all countries.*/
    public static function manualPayoutForNewIndustry(){

        $last_inserted_indus = \DB::table('industries');
        $last_inserted_indus->select('id_industry');
        $last_inserted_indus->orderBy('id_industry','DESC');
        $last_inserted_indus =  $last_inserted_indus->first();
        $last_inserted_indus = $last_inserted_indus->id_industry; 


        $payout_mgmt_list = \DB::table('payout_mgmt');
        $payout_mgmt_list->select('countries.id_country as country_id');
        $payout_mgmt_list->leftJoin('countries','countries.id_country','=','payout_mgmt.country');
        $payout_mgmt_list->leftJoin('industries','industries.id_industry','=','payout_mgmt.industry');
        $payout_mgmt_list->where('payout_mgmt.status','active');
        $payout_mgmt_list->where('industries.id_industry','=','1');
        $payout_mgmt_list =  $payout_mgmt_list->get();

        $payout_mgmt_list = json_decode(json_encode($payout_mgmt_list),true);
        $payout_mgmt_list = array_column($payout_mgmt_list, 'country_id');

        $resp =1;
        if(!empty($payout_mgmt_list)){
            foreach($payout_mgmt_list as $key => $value){
                
                $insertArr[$key] = [
                    'country'               => $value,
                    'industry'              => $last_inserted_indus,
                    'accept_escrow'         => 'yes',
                    'non_reg_accept_escrow' => 'no',
                    'pay_commision'         => 'no',
                    'pay_commision_percent' => '0.00',
                    'pay_commision'         => 'no',
                    'identification_number' => 'no',
                    'status'                => 'active',
                    'created'               => date('Y-m-d H:i:s'), 
                    'updated'               => date('Y-m-d H:i:s')
                ];
                
            }

            $resp = \DB::table('payout_mgmt')->insert($insertArr);
        }
        return $resp; 
    }

}