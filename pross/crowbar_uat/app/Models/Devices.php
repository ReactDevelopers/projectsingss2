<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Devices extends Model{
        protected $table = 'devices';
        protected $primaryKey = 'id_device';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct() {
            
        }

        /**
         * [This method is used for adding] 
         * @param [Integer] $user_id [Used for user id]
         * @param [type] $device_token[Used for device tokin]
         * @param [Varchar] $device_name[Used for device name]
         * @param [Varchar] $device_type[Used for device type]
         * @return Boolean 
         */

        public static function add($user_id,$device_token ="", $device_name = "", $device_type = ""){
            $user                   = \Models\Users::findById($user_id,['id_user']);
            $all_devices            = ___devices();

            if(!empty($user) && !empty($device_token) && in_array(strtolower($device_type), $all_devices)){
                $table_device     = \DB::table('devices');
                $table_device->where('user_id','!=',$user_id);
                $table_device->where('device_token',$device_token);
                $table_device->delete();

                $table_device     = \DB::table('devices');
                $table_device->where('user_id',$user_id);
                $table_device->where('device_token','!=',$device_token);
                $table_device->update(['is_current_device' => 'no']);

                $table_device     = \DB::table('devices');
                $is_device_exists = $table_device->where([
                    'user_id' => $user_id,
                    'device_token' => $device_token
                ])->get()->count();

                if(empty($is_device_exists)){
                    $insert_data = [
                        'user_id' => $user_id,
                        'device_name' => ($device_name)?$device_name:ucfirst(strtolower($device_type)),
                        'device_type' => strtolower($device_type),
                        'device_token' => $device_token,
                        'is_current_device' => 'yes',
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ];

                    return (bool) $table_device->insert($insert_data);
                }else{
                    
                    $table_device     = \DB::table('devices');
                    $table_device->where('user_id',$user_id);
                    $table_device->where('device_token',$device_token);

                    return (bool) $table_device->update(['is_current_device' => 'yes','updated' => date('Y-m-d H:i:s')]);
                }
            }else{
                return false;
            }
        }

        /**
         * [This method is used for remove] 
         * @param [Integer] $user_id [Used for user id]
         * @param [Varchar]$device_token[Used for device token]
         * @return Boolean
         */

        public static function remove($user_id,$device_token){
            $table_device     = \DB::table('devices');

            $is_device_exists = $table_device->where([
                'device_token' => $device_token
            ])->get()->count();

            if(!empty($is_device_exists)){
                $table_device     = \DB::table('devices');
                $isDeviceDeleted = $table_device->where([
                    'device_token' => $device_token
                ])->delete();
            }

            $table_device       = \DB::table('devices');
            $lastDeviceUsed     = $table_device->select(['id_device'])->where('user_id',$user_id)->get()->first();
            
            if(!empty($lastDeviceUsed)){
                $table_device->where('user_id',$user_id);
                $table_device->where('id_device',$lastDeviceUsed->id_device);
                return (bool) $table_device->update(['is_current_device' => 'yes','updated' => date('Y-m-d H:i:s')]);
            }else{
                return (bool) false;
            }
        }

        /**
         * [This method is used for changing] 
         * @param [Integer] $user_id [Used for user id]
         * @param [Varchar] $data [Used for data]
         * @return Data Response
         */

        public static function change($user_id, $data){
            return \DB::table('devices')
            ->where('user_id', $user_id)
            ->update($data);
        }
        
    }
