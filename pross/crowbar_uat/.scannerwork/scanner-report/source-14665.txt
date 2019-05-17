<?php 
    namespace App\Http\Controllers\Front;

    use App\Http\Requests;
    use Illuminate\Support\Facades\DB;
    use App\Models\Proposals;
    use App\Models\File;
    use App\Http\Controllers\Controller;
    
    use Illuminate\Support\Facades\Cookie;
    use Illuminate\Validation\Rule;
    use Illuminate\Http\Request;
    use Crypt;
    
    class ChatController extends Controller {

        private $jsondata;
        private $redirect;
        private $message;
        private $status; 
        private $prefix;

        public function __construct(Request $request){
            $this->jsondata     = (object)[];
            $this->message      = "M0000";
            $this->error_code   = "no_error_found";
            $this->status       = false;
            $this->status_code  = 200;
            $this->prefix       = \DB::getTablePrefix();

            \View::share ( 'footer_settings', \Cache::get('configuration') );

            $json = json_decode(file_get_contents('php://input'),true);
            if(!empty($json)){
                $request->replace($json);
            }

            /*RECORDING API REQUEST IN TABLE*/
            /*
            \Models\Listings::record_api_request([
                'url' => $request->url(),
                'request' => json_encode($request->all()),
                'type' => 'webservice',
                'created' => date('Y-m-d H:i:s')
            ],$request);
            */
        }

        private function populateresponse($data){
            $data['message'] = (!empty($data['message']))?"":$this->message;
            
            if(empty($this->error)){
                $data['error'] = trans(sprintf("general.%s",$data['message']));     
            }else{
                $data['error'] = $this->error;
            }

            $data['error_code'] = "";

            if(empty($data['status'])){
                $data['status'] = $this->status;
                $data['error_code'] = $this->message;
            }
            
            $data['status_code'] = $this->status_code;
            
            $data = json_decode(json_encode($data),true);

            array_walk_recursive($data, function(&$item){
                if (gettype($item) == 'integer' || gettype($item) == 'float' || gettype($item) == 'NULL'){
                    $item = trim($item);
                }
            });

            if(empty($data['data'])){
                $data['data'] = (object) $data['data'];
            }

            return $data;
        }

        /**
         * [This method is used for Chat Save]
         * @param  Request
         * @return Json\Response
         */

        public function chat_save(Request $request){
            $isSaved = \Models\Chats::addmessage($request->all());
            if(!empty($isSaved)){
                $this->jsondata = $isSaved;
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for Update Chat]
         * @param  Request
         * @return Json\Response
         */
        
        public function chat_update(Request $request){
            $isSaved = \Models\Chats::updatemessage($request->all());
            if(!empty($isSaved)){
                $this->jsondata = $isSaved;
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for ReadAll Chat]
         * @param  Request
         * @return Json\Response
         */

        public function chat_readall(Request $request){
            $isSaved = \Models\Chats::readall([
                'group_id'      => $request->group_id,
                'receiver_id'   => $request->sender_id,
                'sender_id'     => $request->receiver_id,
                'seen_status'   => 'read',
            ]);

            if(!empty($isSaved)){
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for listing of chat]
         * @param  Request
         * @return Json\Response
         */

        public function chat_list(Request $request){
            $user = \Models\Users::findById($request->user_id,['id_user','type']);

            if($user['type'] == 'employer'){
                $this->jsondata         = \Models\Employers::get_my_chat_list($user['id_user'],$request->search);
            }else if($user['type'] == 'talent'){
                $this->jsondata         = \Models\Talents::get_my_chat_list($user['id_user'],$request->search);
            }else if($request->user_id == SUPPORT_CHAT_USER_ID){
                $this->jsondata         = \Models\Users::get_my_chat_list(SUPPORT_CHAT_USER_ID,$request->search);
            }
            
            $this->status = true;

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for History of chat]
         * @param  Request
         * @return Json\Response
         */

        public function chat_history(Request $request){
            $user_details   = \Models\Users::findById($request->sender_id,['id_user','type']);

            if($user_details['type'] == 'employer'){
                $column = 'delete_receiver_status';
            }else{
                $column = 'delete_sender_status';
            }
            
            $this->jsondata = \Models\Chats::getmessages($request->group_id,$request->sender_id,$request->receiver_id,$request->page,$request->chat_id,$request->direction,$column);
            
            if(!empty($this->jsondata)){
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for Offline Chat Messages]
         * @param  Request
         * @return Json\Response
         */
        
        public function chat_offline_messages(Request $request){
            $user_details   = \Models\Users::findById($request->sender_id,['id_user','type']);

            if($user_details['type'] == 'employer'){
                $column = 'delete_receiver_status';
            }else{
                $column = 'delete_sender_status';
            }
            
            $this->jsondata = \Models\Chats::getofflinemessages($request->sender_id,$request->page,$request->chat_id,$request->direction,$column,'offline');
            
            if(!empty($this->jsondata)){
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for Acceptance of Chat]
         * @param  Request
         * @return Json\Response
         */

        public function chat_accept(Request $request){
            $isAccepted = \Models\Chats::accept($request->group_id,$request->sender_id,$request->receiver_id);
            
            if(!empty($isAccepted)){
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }
        /**
         * [This method is used for Rejection of Chat]
         * @param  Request
         * @return Json\Response
         */

        public function chat_reject(Request $request){
            $isRejected = \Models\Chats::reject($request->group_id,$request->sender_id,$request->receiver_id);
            
            if(!empty($isRejected)){
                $this->status = true;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for chat image uploading ]
         * @param  Request
         * @return Json\Response
         */

        public function chat_upload_image(Request $request){
            $folder = 'uploads/chat/';

            $isImageUploaded = upload_file($request,'file',$folder,true);

            if(!empty($this->jsondata)){
                $this->status = true;
                $this->jsondata = $isImageUploaded;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for all chat deletion]
         * @param  Request
         * @return Json\Response
         */
        
        public function chat_terminate(Request $request){
            if(!empty($request->sender_id) && !empty($request->receiver_id) && !empty($request->group_id)){
                $isTerminated = \Models\Chats::terminate($request->sender_id,$request->receiver_id,$request->group_id);
                
                if(!empty($isTerminated)){
                    $this->status = true;
                }else{
                    $this->message = 'M0022';
                }
            }else{
                $this->message = 'M0121';
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for all chat deletion]
         * @param  Request
         * @return Json\Response
         */
        
        public function chat_deleteall(Request $request){
            if(!empty($request->sender_id) && !empty($request->receiver_id) && !empty($request->group_id)){
                $user_details   = \Models\Users::findById($request->sender_id,['id_user','type']);

                if($user_details['type'] == 'employer'){
                    $column = 'delete_receiver_status';
                }else{
                    $column = 'delete_sender_status';
                }

                $isDeleted     = \Models\Chats::delete_all($request->sender_id,$request->receiver_id,$column,$request->group_id);
                if(!empty($isDeleted) && $isDeleted['message'] == 'deleted_all'){
                    $this->status = true;
                }elseif(!empty($isDeleted) && $isDeleted['message'] == 'already_deleted_messages'){
                    $this->message = trans('general.M0482');
                }else{
                    $this->message = $isDeleted['message'];
                }
            }else{
                $this->message = 'M0121';
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for chat report abuse ]
         * @param  Request
         * @return Json\Response
         */

        public function chat_report_abuse(Request $request){
            if(!empty($request->message)){
                $request->message = trim($request->message);
            }
            
            $validate = \Validator::make($request->all(), [
                "message"             => validation('report_abuse_reason'),
            ],[
                'message.required'    => trans('general.M0475'),
                'message.string'      => trans('general.M0476'),
                'message.regex'       => trans('general.M0476'),
                'message.regex'       => trans('general.M0478'),
                'message.regex'       => trans('general.M0477'),
            ]);

            $validate->after(function ($validate) use($request){
                $isConversationStarted  = \Models\Chats::is_conversation_started($request->sender_id,$request->receiver_id);
                $isAlreadyReported      = \Models\Abuse::is_already_reported($request->sender_id,$request->receiver_id);

                if (!$isConversationStarted) {
                    $validate->errors()->add('message', trans('general.M0479'));
                }else if(!empty($isAlreadyReported)){
                    $validate->errors()->add('message', trans('general.M0480'));
                }
            });

            if($validate->passes()){
                $isSaved = \Models\Chats::addmessage($request->all());
                if(!empty($isSaved)){
                    $isReported = \Models\Abuse::report($request->sender_id,$request->receiver_id,trim($request->message), 'report-abused');
                    $this->jsondata = $isSaved;
                    $this->status = true;
                }
            }else{
                $this->jsondata = ___error_sanatizer($validate->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        /**
         * [This method is used for User's status of chat ]
         * @param  Request
         * @return Json\Response
         */

        public function chat_user_status(Request $request){
            if(!empty($request->sender_id)){
                $isUpdated = \Models\Users::change($request->sender_id,[
                    'chat_status' => $request->status,
                    'updated' => date('Y-m-d H:i:s'), 
                ]);

                if(!empty($isUpdated)){
                    $this->status = true;
                }
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }
    }
