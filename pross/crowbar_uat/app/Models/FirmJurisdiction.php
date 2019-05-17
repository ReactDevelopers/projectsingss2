<?php 
    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class FirmJurisdiction extends Model{
    	protected $table  = 'firm_jurisdiction';

    	const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        public static function get_firm_jurisdiction($user_id){
            $get_jurisdiction = \DB::table('firm_jurisdiction')
                                ->select('country_id')
                                ->where('company_id',$user_id)
                                ->get();

            $get_jurisdiction = json_decode(json_encode($get_jurisdiction),true);

            return $get_jurisdiction;
        } 

        /**
         * [This method is used to update jursisdiction] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$jursisdiction[Used for jursisdiction]
         * @param [VArchar]$subindustry[Used for subindustry]
         * @return Data Response
         */

        public static function update_jurisdiction($user_id,$jurisdiction){
            $table_talent_urisdiction = DB::table('firm_jurisdiction');
            $table_talent_urisdiction->where('company_id',$user_id);
            $table_talent_urisdiction->delete();
            if(!empty($jurisdiction) && is_array($jurisdiction)){
                $jurisdiction_list = array_map(
                    function($i) use($user_id){
                        return array(
                            'country_id'        => $i,
                            'created'           => date('Y-m-d H:i:s'),
                            'updated'           => date('Y-m-d H:i:s')
                        ); 
                    }, 
                    $jurisdiction
                );

                
                foreach ($jurisdiction['jurisdiction'] as $key => $v) {
                    \DB::table('firm_jurisdiction')->insert([
                        'company_id'  => $user_id,
                        'country_id' => $v,
                        'created'           => date('Y-m-d H:i:s'),
                        'updated'           => date('Y-m-d H:i:s')
                    ]); 
                }
                return true;
            }else{
                return false;
            }
        }
           	
    }
?>