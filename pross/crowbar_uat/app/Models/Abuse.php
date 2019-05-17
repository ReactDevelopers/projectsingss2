<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Abuse extends Model{

        /**
         * [This method is used for report] 
         * @param [Integer]$sender_id [Used for sender id]
         * @param [Integer]$receiver_id[Used for receiver id]
         */
        
        public static function  report($sender_id, $receiver_id, $reason, $type){
            $table_report_abuse = \DB::table('report_abuse');
            
            $isReported = $table_report_abuse->insert([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $reason,
                'type' => $type,
                'updated' => date('Y-m-d H:i:s'),
                'created' => date('Y-m-d H:i:s'),
            ]);

            return $isReported;
        }

        /**
         * [This method is used to get all abuses report] 
         * @param  null
         * @return Data Response
         */

        public static function  get_all_report_abuses(){
            $prefix = \DB::getTablePrefix();

            \DB::statement(\DB::raw('set @row_number=0'));
            $table_report_abuse = \DB::table('report_abuse')
            ->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'report_abuse.id_report',
                'report_abuse.sender_id',
                'report_abuse.receiver_id',
                'report_abuse.message',
                'report_abuse.type',
                'report_abuse.status',
                'report_abuse.created',
                'sender.type as sender_type',
                'receiver.type as receiver_type',
                \DB::raw("(SELECT COUNT(id_report) FROM {$prefix}report_abuse WHERE receiver_id = {$prefix}receiver.id_user) AS no_reported_abuse"),
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',IFNULL({$prefix}sender.last_name,''))) as sender_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',IFNULL({$prefix}receiver.last_name,''))) as receiver_name"),
            ])
            ->leftJoin('users as sender','sender.id_user','=','report_abuse.sender_id')
            ->leftJoin('users as receiver','receiver.id_user','=','report_abuse.receiver_id')
            ->where('sender.status','!=','trashed')
            ->where('receiver.status','!=','trashed');

            return $table_report_abuse->orderBy('created','desc')->get();
        }

        /**
         * [This method is used to get one abuse report] 
         * @param  null
         * @return Data Response
         */

        public static function  get_all_report_abuse_by_Id($receiver_id){
            $prefix = \DB::getTablePrefix();

            \DB::statement(\DB::raw('set @row_number=0'));
            $report_abuse = \DB::table('report_abuse')
            ->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'report_abuse.id_report',
                'report_abuse.sender_id',
                'report_abuse.receiver_id',
                'report_abuse.message',
                'report_abuse.type',
                'report_abuse.status',
                'report_abuse.created',
                'sender.type as sender_type',
                'receiver.type as receiver_type',
                \DB::raw("(SELECT COUNT(id_report) FROM {$prefix}report_abuse WHERE receiver_id = {$prefix}receiver.id_user) AS no_reported_abuse"),
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',IFNULL({$prefix}sender.last_name,''))) as sender_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',IFNULL({$prefix}receiver.last_name,''))) as receiver_name"),
            ])
            ->leftJoin('users as sender','sender.id_user','=','report_abuse.sender_id')
            ->leftJoin('users as receiver','receiver.id_user','=','report_abuse.receiver_id')
            ->where('receiver_id','=',$receiver_id);

            return $report_abuse->orderBy('created','desc')->get();

        }

        /**
         * [This method is used to resolve all abuse report] 
         * @param [Integer]$sender_id$id_report [Used for sender id]
         * @param [Varchar]$closeConnection [Used for close connection]
         * @return Data response
         */

        public static function resolve_report_abuse($id_report, $user_id, $closeConnection = false){

            $message            = trans('admin.report_abuse_unlink_message');
            $isUpdated          = \DB::table('users')->where('id_user', $user_id)->update(['status' => 'inactive']);
            $isStatusUpdated    = \DB::table('report_abuse')->where('id_report', $id_report)->update(['status' => 'disputed']);

            return true;
        }

        /**
         * [This method is used for already reported abuse] 
         * @param [Integer]$sender_id [Used for sender id]
         * @param [Integer]$receiver_id[Used for receiver id]
         * @return Data Response
         */

        public static function  is_already_reported($sender_id,$receiver_id){
            return \DB::table('report_abuse')
            ->where('sender_id','=',$sender_id)
            ->where('receiver_id','=',$receiver_id)
            ->where('status','=','open')
            ->get()->count();
        }

        /**
         * [This method is used to get report abuse details]
         * @param [Integer]$receiver_id[Used for receiver id]
         * @return Data Response
         */

        public static function  getAbuseById($report_id){
            $reportDetails = \DB::table('report_abuse')
            ->where('id_report','=',$report_id)
            ->get()->first();

            return json_decode(json_encode($reportDetails),true);
        }
    }