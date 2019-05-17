<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
        protected $table = 'coupon';
        protected $primaryKey = 'id';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        /**
         * [This method is for listing all coupon code] 
         * @return Boolean
         */
        public static function listCoupon(){
            $prefix         = \DB::getTablePrefix();

           \DB::statement(\DB::raw('set @row_number=0'));

            $table_coupon = \DB::table('coupon');

            $table_coupon->select([\DB::raw('@row_number  := @row_number  + 1 AS row_number'),'id','code','start_date','expiration_date','status','created','updated', \DB::raw('(SELECT COUNT(*) FROM cb_api_coupon_response as acr WHERE acr.coupon_id = cb_coupon.id) AS redeem_coupon'),
            ]);
            $table_coupon->where('coupon.status','active');
            $table_coupon->orderBy('coupon.id','desc');

            return $table_coupon->get();
        }

        /**
         * [This method is for listing all coupon code] 
         * @return Boolean
         */
        public static function verifyCoupon($user_id, $project_id){
            $coupon_code = \DB::table('talent_proposals')
                            ->select('coupon_id')
                            ->where('project_id','=',$project_id)
                            ->where('user_id','=',$user_id)
                            ->first();

            if($coupon_code->coupon_id != 0){
                return true;
            }else{
                return false;
            }

            /*$coupon_code = \DB::table('users')
                                ->select('coupon.expiration_date as coupon_expire_date')
                                ->leftJoin('coupon','coupon.id','=','users.coupon_id')
                                ->where('users.id_user','=',$user_id)
                                ->first();

            if(!empty($coupon_code) && date('Y-m-d') <= date('Y-m-d',strtotime($coupon_code->coupon_expire_date))){
                return true;
            }else{
                return false;
            }*/

        }

        public static function getAppliedCouponDiscount($user_id, $project_id){
            $coupon_code = \DB::table('talent_proposals')
                            ->select('*')
                            ->where('project_id','=',$project_id)
                            ->where('user_id','=',$user_id)
                            ->first();

            if(!empty($coupon_code)){
                $discount = \DB::table('coupon')
                            ->select('discount')
                            ->where('id','=',$coupon_code->coupon_id)
                            ->first();

                return json_decode(json_encode($discount),true);            
            }else{
                return 0;
            }
        }

        /**
         * [This method is for getting coupon code] 
         * @return Boolean
         */
        public static function getCouponCodeById($id_code){

            $coupon_code = \DB::table('coupon')
                                ->select('code')
                                ->where('id','=',$id_code)
                                ->first();

            return json_decode(json_encode($coupon_code),true);

        }

        /**
         * [This method is for getting coupon code] 
         * @return Boolean
         */
        public static function deleteCouponCodeById($id_code){

            $delete_coupon_code = \DB::table('coupon')
                                ->where('id','=',$id_code)
                                ->delete();

            return (bool)$delete_coupon_code;

        }

        /**
         * [This method is for getting coupon code] 
         * @return Boolean
         */
        public static function updateStatusCouponCodeById($id_code){

            $delete_coupon_code = \DB::table('coupon')
                                ->where('id','=',$id_code)
                                ->update(['status'=>'deleted']);

            return (bool)$delete_coupon_code;

        }

        public static function validateCoupon($id_coupon, $id_user){
            $usedCoupon = \DB::table('talent_proposals')
                                ->where('user_id','=',$id_user)
                                ->where('coupon_id','=',$id_coupon)
                                ->first();

            $usedCoupon = json_decode(json_encode($usedCoupon),true);

            if(!empty($usedCoupon)){
                return true;
            }
            else{
                return false;
            }
        }
}
