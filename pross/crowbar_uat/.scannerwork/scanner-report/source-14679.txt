<?php 
    namespace App\Http\Controllers\Admin;

    use App\Http\Requests;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Controller;
    
    use Illuminate\Support\Facades\Cookie;
    use Yajra\Datatables\Datatables;
    use Illuminate\Http\Request;
    use App\Models\Interview as Interview;
    use Auth;
    use Crypt;
    
    class AjaxController extends Controller {

        private $URI_PLACEHOLDER;

        public function __construct(){
            $this->URI_PLACEHOLDER = \Config::get('constants.URI_PLACEHOLDER');    
        }

        /**
         * [This method is used for randering view of message] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function messages(){
            $messages = \Models\Listings::apimessages('object');
            return Datatables::of($messages)->make(true);
        }

        /**
         * [This method is used for adding message] 
         * @param  Request
         * @return Json Response
         */

        public function addmessage(Request $request){
            $validator = \Validator::make($request->all(), [
                'message_code' => 'required',
                'message_description' =>'required',
                'message_section' =>'required'
            ]);

            if ($validator->passes()) {
                $isInserted = \Models\Listings::add_api_message(['code' => $request->message_code, 'message' => $request->message_description, 'section' => $request->message_section, 'created' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')]);

                if(!empty($isInserted)){
                    return response()->json([
                        'status' => true,
                        'message' => sprintf(ALERT_SUCCESS,'New message has been saved successfully.'),
                        'errors' => []
                    ]); 
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.'),
                        'errors' => json_decode(json_encode($validator->errors()),true)
                    ]); 
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Please fix the following error(s).'),
                    'errors' => json_decode(json_encode($validator->errors()),true)
                ]); 
            }
        }

        /**
         * [This method is used for randering view of country] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function countries(){
            $countries = \Models\Listings::countries(
                'object',
                [
                    'id_country',
                    'iso_code',
                    'phone_country_code',
                    'en as country_name',
                    'status'
                ],
                "status != 'trashed'"
            );
            return Datatables::of($countries)->make(true);
        }

        /**
         * [This method is used for country status] 
         * @param  Request
         * @return Jyson Response
         */

        public function countrystatus(Request $request) {
            $country_id = $request->country_id;
            if(empty($country_id)){return false;}
            $status = strtolower($request->status);

            if($status == 'trashed'){
                $isUpdated = \Models\Listings::update_country($country_id,array('status' => $status,'updated' => date('Y-m-d H:i:s')));                 
            }else{
                $isUpdated = \Models\Listings::update_country($country_id,array('status' => $status,'updated' => date('Y-m-d H:i:s')));                 
            }

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]); 
            }
        }

        /**
         * [This method is used for state status] 
         * @param  Request
         * @return Json Response
         */

        public function statestatus(Request $request) {
            $state_id = $request->id_state;
            if(empty($state_id)){return false;}

            $status     = strtolower($request->status);
            $isUpdated  = \Models\Listings::update_state($state_id,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]); 
            }
        }

        /**
         * [This method is used for city status] 
         * @param  Request
         * @return Json Response
         */

        public function citystatus(Request $request) {
            $city_id = $request->id_city;
            if(empty($city_id)){return false;}

            $status     = strtolower($request->status);
            $isUpdated  = \Models\Listings::update_city($city_id,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]); 
            }
        }


        /**
         * [This method is used for city status] 
         * @param  Request
         * @return Json Response
         */

        public function dispute_concern_status(Request $request) {
            $concern_id = $request->id_concern;
            if(empty($concern_id)){return false;}

            $status     = strtolower($request->status);
            $isUpdated  = \Models\DisputeConcern::where('id_concern',$concern_id)->update(array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]); 
            }
        }   

        /**
         * [This method is used for update setting] 
         * @param  Request
         * @return Json Response
         */             

        public function updatesetting(Request $request) {
            $isUpdated = \Models\Listings::update_setting('site_environment',$request->site_environment);
            if($isUpdated){
                if($request->site_environment == 'development'){
                    \Cache::forget('site_environment');
                    \Cache::forever('site_environment',$request->site_environment);
                    return response()->json([
                        'status' => true,
                        'target' => ucfirst(($request->site_environment)),
                        'html' => '<a href="javascript:;" data-url="'.url($this->URI_PLACEHOLDER.'/ajax/setting/update?site_environment=production').'" data-request="html" data-ask="Do you really want to continue with this action?" data-target="#site_environment" class="btn btn-primary btn-block">Switch <u><b>Site Mode</b></u></a>'
                    ]); 
                }else{
                    \Cache::forget('site_environment');
                    \Cache::forever('site_environment',$request->site_environment);

                    return response()->json([
                        'status' => true,
                        'target' => ucfirst(($request->site_environment)),
                        'html' => '<a href="javascript:;" data-url="'.url($this->URI_PLACEHOLDER.'/ajax/setting/update?site_environment=development').'" data-request="html" data-ask="Do you really want to continue with this action?" data-target="#site_environment" class="btn btn-primary btn-block">Switch <u><b>Site Mode</b></u></a>'
                    ]); 
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]); 
            }
        }

        /**
         * [This method is used for randering view of email] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function emails(Request $request){

            $emails = \Models\Listings::emails(
                'object',
                [
                    'id_email',
                    'language',
                    'subject',
                    'alias',
                ],
                "status = 'active' "
            );
            
            return Datatables::of($emails)->filter(function ($instance) use($request){
                if ($request->has('search')) {
                    if(!empty($request->search['value'])){
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return (\Str::contains(strtolower($row->subject), strtolower($request->search['value'])) || \Str::contains(strtolower($row->alias), strtolower($request->search['value'])) || \Str::contains(strtolower(\Cache::get('languages')[$row->language]), strtolower($request->search['value']))) ? true : false;
                        });
                    } 
                }
            })->editColumn('language',function($item){
                return $item->language = \Cache::get('languages')[$item->language];
            })->make(true);
        }

        /**
         * [This method is used for user listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function user_list(Request $request){

            switch ($request->page) {
                case 'talent':
                    $where = "type = 'talent' AND status != 'trashed'";
                    break;
                case 'employer':
                    $where = "type = 'employer' AND status != 'trashed'";
                    break;
                case 'sub-admin':
                    $where = "type = 'sub-admin' AND status != 'trashed'";
                    break;
                default:
                    # code...
                    break;
            }

            $users = \Models\Listings::users(
                'object',
                [
                    'id_user',
                    'name',
                    'email',
                    'status',
                ],
                $where
            );

            return Datatables::of($users)->make(true);
        }

        /**
         * [This method is used for question status] 
         * @param  Request
         * @return Json Response
         */

        public function questionstatus(Request $request) {
            $id_question = $request->id_question;
            if(empty($id_question)){return false;}

            $status = strtolower($request->status);
            $isUpdated = Interview::update_question($id_question,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for question type status] 
         * @param  Request
         * @return Json Response
         */

        public function questionTypestatus(Request $request) {
            $id_question_type = ___decrypt($request->id_question_type);
            if(empty($id_question_type)){return false;}

            $status = strtolower($request->status);
            $isUpdated = Interview::update_question_type($id_question_type,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for industry status] 
         * @param  Request
         * @return Json Response
         */

        public function industrystatus(Request $request) {
            $id_industry = ___decrypt($request->id_industry);
            if(empty($id_industry)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Industries::update_industry($id_industry,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for abusive word status] 
         * @param  Request
         * @return Json Response
         */

        public function abusive_word_status(Request $request) {
            $id_words = ___decrypt($request->id_words);
            if(empty($id_words)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Listings::update_abusive_words($id_words,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for degree status] 
         * @param  Request
         * @return Json Response
         */

        public function degreestatus(Request $request) {
            $id_degree = ___decrypt($request->id_degree);
            if(empty($id_degree)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Listings::update_degree($id_degree,array('degree_status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for certificate status] 
         * @param  Request
         * @return json Response
         */

        public function certificatestatus(Request $request) {
            $id_certificate = ___decrypt($request->id_certificate);
            if(empty($id_certificate)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Listings::update_certificate($id_certificate,array('certificate_status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for college status] 
         * @param  Request
         * @return Json Response
         */

        public function collegestatus(Request $request) {
            $id_college = ___decrypt($request->id_college);
            if(empty($id_college)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Listings::update_college($id_college,array('college_status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for skill status] 
         * @param  Request
         * @return Json Response
         */

        public function skillstatus(Request $request) {
            $id_skill = ___decrypt($request->id_skill);
            if(empty($id_skill)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Listings::update_skill($id_skill,array('skill_status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        /**
         * [This method is used for feature status] 
         * @param  Request
         * @return Json Response
         */

        public function featurestatus(Request $request) {
            $id_feature = ___decrypt($request->id_feature);
            if(empty($id_feature)){return false;}

            $status = strtolower($request->status);
            $isUpdated = \Models\Plan::update_feature($id_feature,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        }

        public function faqstatus(Request $request) {
            $id_faq = ___decrypt($request->id_faq);
            if(empty($id_faq)){
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }

            $status     = strtolower($request->status);

            $faq_record = \Models\Faqs::select([
                'type'                
            ])->where('id_faq',$id_faq)->first();
            $faq_record = json_decode(json_encode($faq_record),true);
            if(!empty($faq_record)){
                if($faq_record['type'] == 'topic'){
                    $isUpdated = self::deleteFaqTopic($id_faq,$status);
                }else if($faq_record['type'] == 'category'){
                    $isUpdated = self::deleteFaqCategory($id_faq,$status);
                }else if($faq_record['type'] == 'post'){
                    $isUpdated = self::deleteFaqPost($id_faq,$status);
                }
            }

            if(!empty($isUpdated)){
                return response()->json([
                    'status' => true,
                    'message' => sprintf(ALERT_SUCCESS,'Status has been updated successfully.')
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
                ]);
            }
        } 

        /**
         * [This method is used for randering view of page] 
         * @param  null
         * @return \Illuminate\Http\Response
         */                

        public function pages(){
            $page = \Models\Listings::pages(
                'object',
                [
                    'id',
                    'title'
                ],
                "status != 'trashed'"
            );

            return Datatables::of($page)->make(true);
        }

        /**
         * [This method is used for message listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function message_list(Request $request){

            switch ($request->messages_status) {
                case 'inbox':
                    $where = "message_status = 'approved' AND message_ticket_status = 'open' AND (sender_type='talent' OR sender_type='employer' OR sender_type='guest')";
                    break;
                case 'closed':
                    $where = "message_status = 'approved' AND message_ticket_status = 'closed' AND (sender_type='talent' OR sender_type='employer' OR sender_type='guest')";
                    break;
                case 'trashed':
                    $where = "message_status = 'trashed' AND (sender_type='talent' OR sender_type='employer' OR sender_type='guest')";
                    break;
                default:
                    # code...
                    break;
            }

            $messages = \Models\Listings::messages(
                'object',
                [
                    'id_message',
                    'id_message as id_ticket',
                    'message_subject',
                    'created',
                    'message_status',
                ],
                $where
            );

            return Datatables::of($messages)
            ->editColumn('created',function($item){
                return $item->created = ___ago($item->created);
            })
            ->editColumn('id_ticket',function($item){
                return $item->id_ticket = _ticketid($item->id_ticket);
            })->make(true);
        }

        public function deleteFaqTopic($faq_id,$status){
            $faq_record = \Models\Faqs::select([
                'id_faq'
            ])->where('parent',$faq_id)->get();
            
            $faq_record = json_decode(json_encode($faq_record),true);
            if(!empty($faq_record)){
                foreach ($faq_record as $key => $value) {
                    $topicStatus = self::deleteFaqCategory($value['id_faq'],$status);
                }
            }

            if(!empty($topicStatus)){
                \Models\Faq_language::where('faq_id',$faq_id)->delete();
                return $isDeleted = \Models\Faqs::where('id_faq',$faq_id)->delete();            
            }else{
                return false;
            }
        }

        public function deleteFaqCategory($faq_id,$status){
            $faq_record = \Models\Faqs::select([
                'id_faq'
            ])->where('parent',$faq_id)->get();
            
            $faq_record = json_decode(json_encode($faq_record),true);
           
            if(!empty($faq_record)){
                foreach ($faq_record as $key => $value) {
                    $categoryStatus = self::deleteFaqPost($value['id_faq'],$status);
                }
            }

            if(!empty($categoryStatus)){
                \Models\Faq_language::where('faq_id',$faq_id)->delete();
                return $isUpdated  =  \Models\Faqs::where('id_faq',$faq_id)->delete();
            }else{
                return false;
            }
        }

        public function deleteFaqPost($faq_id,$status){
            \Models\Faq_language::where('faq_id',$faq_id)->delete();
            \Models\Faq_response::where('faq_id',$faq_id)->delete();
            return $isUpdated  =  \Models\Faqs::where('id_faq',$faq_id)->delete();
        }
    }
