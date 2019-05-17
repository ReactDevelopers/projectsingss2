<?php 
	namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Messages extends Model{

        protected $table = 'messages';
        protected $primaryKey = 'id_message';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        /**
         * [This method is used for compose] 
         * @param [Varchar]$sender_name[Used for sender_name]
         * @param [Varchar]$sender_email[Used for sender_email]
         * @param [Varchar]$message_content[Used for message_content]
         * @param [Varchar]$message_subject[message_subject]
         * @param [Varchar]$message_type[message_type]
         * @return Data Response
         */	

        public static function compose($sender_name,$sender_email,$message_content,$message_subject,$message_type){
			$message_status = 'approved';
			$message_approved_date = NULL;
			$message_approved_by = NULL;

			$receiver_type = 'admin';
			$receiver_id = 1;
			$message_status = 'approved';
			$message_approved_date = date('Y-m-d H:i:s');
			$message_approved_by = -1;

			$insert_data = [
				'sender_name' 			=> $sender_name,
				'sender_email' 			=> $sender_email,
				'id_receiver' 			=> $receiver_id,
				'receiver_type' 		=> $receiver_type,
				'message_subject' 		=> $message_subject,
				'message_content' 		=> $message_content,
				'message_comment' 		=> $message_content,
				'message_status' 		=> $message_status,
				'message_approved_date'	=> $message_approved_date,
				'message_type'			=> $message_type,
				'message_approved_by'	=> $message_approved_by,
				'created' 				=> date('Y-m-d H:i:s'),
				'updated' 				=> date('Y-m-d H:i:s')
			];
			return self::insertGetId($insert_data);
		}

		public static function composeSecond($sender_name,$sender_email,$message_content,$message_subject,$message_type,$country_code,$phone_number){
			
			$message_status = 'approved';
			$message_approved_date = NULL;
			$message_approved_by = NULL;

			$receiver_type = 'admin';
			$receiver_id = 1;
			$message_status = 'approved';
			$message_approved_date = date('Y-m-d H:i:s');
			$message_approved_by = -1;

			$insert_data = [
				'sender_name' 			=> $sender_name,
				'sender_email' 			=> $sender_email,
				'country_code' 			=> $country_code,
				'phone_number' 			=> $phone_number,
				'id_receiver' 			=> $receiver_id,
				'receiver_type' 		=> $receiver_type,
				'message_subject' 		=> $message_subject,
				'message_content' 		=> $message_content,
				'message_comment' 		=> $message_content,
				'message_status' 		=> $message_status,
				'message_approved_date'	=> $message_approved_date,
				'message_type'			=> $message_type,
				'message_approved_by'	=> $message_approved_by,
				'created' 				=> date('Y-m-d H:i:s'),
				'updated' 				=> date('Y-m-d H:i:s')
			];
			return self::insertGetId($insert_data);
		}
	}
