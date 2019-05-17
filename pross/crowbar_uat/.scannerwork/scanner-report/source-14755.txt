<?php

    namespace Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Forum extends Model{
        protected $table = 'forum_question';
        protected $primaryKey = 'id_question';

        /**
         * [This method is used forchange] 
         * @param [Integer]$id_question [Used for question id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function change($id_question,$data){
            $isUpdated = false;
            $table_users = DB::table('forum_question');

            if(!empty($data)){
                $table_users->where('id_question','=',$id_question);
                $isUpdated = $table_users->update($data);
            }

            return (bool)$isUpdated;
        }

        /**
         * [This method is used to change reply] 
         * @param [Integer]$id_answer [Used for answer id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function changeReply($id_answer, $data){
            $isUpdated = false;
            $table_users = DB::table('forum_answer');

            if(!empty($data)){
                $table_users->where('id_answer','=',$id_answer);
                $isUpdated = $table_users->update($data);
            }

            return (bool)$isUpdated;
        }

        /**
         * [This method is used for question deletion] 
         * @param [Integer]$id_question [Used for question id]
         * @return Boolean
         */

        public static function delete_question($id_question){
            $isUpdated = false;
            $table_users = DB::table('forum_answer');

            $table_users->where('id_question','=',$id_question);
            $isUpdated = $table_users->update(['status' => 'trash']);

            $table_users = DB::table('forum_question');
            $table_users->where('id_question','=',$id_question);
            $isUpdated = $table_users->update(['status' => 'trash']);

            return (bool)$isUpdated;
        }

        /**
         * [This method is used to delete reply] 
         * @param [Integer]$id_answer [Used for answer id]
         * @return Boolean
         */

        public static function delete_reply($id_answer){
            $isUpdated = false;
            $table_users = DB::table('forum_answer');

            $table_users->where('id_answer','=',$id_answer);
            $table_users->orWhere('id_parent','=',$id_answer);
            $isUpdated = $table_users->update(['status' => 'trash']);

            return (bool)$isUpdated;
        }

        /**
         * [This method is used to getQuestionList] 
         * @param null
         * @return Data Response
         */

        public static function getQuestionList(){
            $prefix       = DB::getTablePrefix();
            $questionList = DB::table('forum_question')
            ->select([
                'forum_question.id_question',
                'forum_question.id_user',
                \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 80) AS question_description'),
                'forum_question.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_question.status, 1)),SUBSTRING('.$prefix.'forum_question.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
            ])
            ->leftJoin('users as users','users.id_user','=','forum_question.id_user')
            ->where('forum_question.status', '!=', 'trash')
            ->get();

            return $questionList;
        }

        /**
         * [This method is used to getQuestionFront] 
         * @param [Integer]$id_question[Used for question id]
         * @return Data Response
         */

        public static function getQuestionFront($id_question = 0){

            $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $prefix       = DB::getTablePrefix();
            $questionList = DB::table('forum_question')
            ->select([
                'forum_question.id_question',
                'forum_question.id_user',
                \DB::raw('(SELECT COUNT(id_answer) FROM '.$prefix.'forum_answer WHERE id_question = '.$prefix.'forum_question.id_question AND status = "approve") AS total_reply'),
                // \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 80) AS question_description'),
                \DB::raw(''.$prefix.'forum_question.question_description AS question_description'),
                'forum_question.approve_date',
                \DB::raw(''.$prefix.'forum_question.created AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_question.status, 1)),SUBSTRING('.$prefix.'forum_question.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                // \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                \DB::Raw($prefix."files.folder as folder"),
                \DB::Raw($prefix."files.filename as filename"),

                'forum_question.type',
                \DB::Raw("IF(({$prefix}forum_question.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_question.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='question') AS is_following"),

            ])
            ->leftJoin('users as users','users.id_user','=','forum_question.id_user')
            ->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            })
            ->leftJoin('forum_answer','forum_answer.id_question','=','forum_question.id_question')
            ->where('forum_question.status', '=', 'open')
            ->orderBy('forum_question.id_question', 'DESC');

            if($id_question > 0){
                $questionList->where('forum_question.id_question', $id_question);
                $questionList->addSelect([
                        \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = '".$id_question."' and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='question') AS is_ques_following"),
                        ]);
            }

            $questionList->groupBY('forum_question.id_question');

            /*if($type=='api'){
                return $questionList;   
            }*/
            $questionList = $questionList->get();

            if($id_question > 0){
                $questionList = $questionList->first();
                $questionList = json_decode(json_encode($questionList), true);
            }
            return $questionList;
        }

        /**
         * [This method is used to getQuestionFront] 
         * @param [Integer]$id_question[Used for question id]
         * @return Data Response
         */

        public static function getQuestionFrontApi($id_question = 0){

            $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $prefix       = DB::getTablePrefix();
            $base_url = ___image_base_url();
            $questionList = DB::table('forum_question')
            ->select([
                'forum_question.id_question',
                'forum_question.id_user',
                \DB::raw('(SELECT COUNT(id_answer) FROM '.$prefix.'forum_answer WHERE id_question = '.$prefix.'forum_question.id_question AND status = "approve") AS total_reply'),
                // \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 80) AS question_description'),
                \DB::raw(''.$prefix.'forum_question.question_description AS question_description'),
                'forum_question.approve_date',
                \DB::raw(''.$prefix.'forum_question.created AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_question.status, 1)),SUBSTRING('.$prefix.'forum_question.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                \DB::Raw("
                        {$prefix}files.filename as user_img
                    "),
                \DB::Raw("
                        {$prefix}files.folder as folder
                    "),

                'forum_question.type',
                \DB::Raw("IF(({$prefix}forum_question.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_question.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='question') AS is_following"),

            ])
            ->leftJoin('users as users','users.id_user','=','forum_question.id_user')
            ->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            })
            ->leftJoin('forum_answer','forum_answer.id_question','=','forum_question.id_question')
            ->where('forum_question.status', '=', 'open')
            ->orderBy('forum_question.id_question', 'DESC');

            if($id_question > 0){
                $questionList->where('forum_question.id_question', $id_question);
                $questionList->addSelect([
                        \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = '".$id_question."' and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='question') AS is_ques_following"),
                        ]);
            }

            $questionList->groupBY('forum_question.id_question');

            /*if($type=='api'){
                return $questionList;   
            }*/
            $questionList = $questionList->get();

            if($id_question > 0){
                $questionList = $questionList->first();
                $filedata['filename'] = $questionList->user_img;
                $filedata['folder'] = $questionList->folder;
                $questionList->filename = get_file_url($filedata);
                $questionList = json_decode(json_encode($questionList), true);
            }
            return $questionList;
        }

        public static function getHomeQuestionFront($id_question,$api = NULL){

            $user_id  = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $prefix   = DB::getTablePrefix();
            $base_url = ___image_base_url();

            $questionList = DB::table('forum_question')
            ->select([
                'forum_question.id_question',
                'forum_question.id_user',
                \DB::raw('(SELECT COUNT(id_answer) FROM '.$prefix.'forum_answer WHERE id_question = '.$prefix.'forum_question.id_question AND status = "approve" LIMIT 1) AS total_reply'),
                // \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 80) AS question_description'),
                \DB::raw(''.$prefix.'forum_question.question_description AS question_description'),
                'forum_question.approve_date',
                \DB::raw(''.$prefix.'forum_question.created AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_question.status, 1)),SUBSTRING('.$prefix.'forum_question.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),

                /*\DB::Raw("
                    IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
                        CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as filename
                "),*/
                 \DB::Raw("
                      {$prefix}files.filename as filename
                "),
                 \DB::Raw("
                      {$prefix}files.folder as folder
                "),

                'forum_question.type',
                \DB::Raw("IF(({$prefix}forum_question.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_question.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user' LIMIT 1) AS is_following"),

            ])
            ->leftJoin('users as users','users.id_user','=','forum_question.id_user')
            ->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            })
            ->leftJoin('forum_answer','forum_answer.id_question','=','forum_question.id_question')
            ->where('forum_question.status', '=', 'open');

            if($id_question > 0){
                $questionList->where('forum_question.id_question', $id_question);
                $questionList->addSelect([
                        \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = '".$id_question."' and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='question') AS is_ques_following"),
                        ]);
            }

            $questionList->groupBY('forum_question.id_question');

            /*if($type=='api'){
                return $questionList;   
            }*/
            $questionList = $questionList->get();

            if($id_question > 0){
                $questionList = $questionList->first();
                $questionList = json_decode(json_encode($questionList), true);

                if($api=='apidata'){
                    $questionList['created'] = ___ago($questionList['created']);
                    $questionList['share_link'] = url('/network/community/forum/question/'.$questionList['id_question']);
                }
            }
            return $questionList;
        }

        /**
         * [This method is used to getQuestionFront] 
         * @param [Integer]$id_question[Used for question id]
         * @return Data Response
         */

        public static function getQuestionApi($id_question = 0){

            $base_url       = ___image_base_url();
            $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $prefix       = DB::getTablePrefix();
            $questionList = DB::table('forum_question')
            ->select([
                'forum_question.id_question',
                'forum_question.id_user',
                \DB::raw('(SELECT COUNT(id_answer) FROM '.$prefix.'forum_answer WHERE id_question = '.$prefix.'forum_question.id_question AND status = "approve") AS total_reply'),
                // \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 80) AS question_description'),
                \DB::raw(''.$prefix.'forum_question.question_description AS question_description'),
                'forum_question.approve_date',
                \DB::raw(''.$prefix.'forum_question.created AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_question.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_question.status, 1)),SUBSTRING('.$prefix.'forum_question.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                \DB::Raw("
                        {$prefix}files.filename as filename
                    "),
                \DB::Raw("
                        {$prefix}files.folder as folder
                    "),

                'forum_question.type',
                \DB::Raw("IF(({$prefix}forum_question.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_question.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

            ])
            ->leftJoin('users as users','users.id_user','=','forum_question.id_user')
            ->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            })
            ->leftJoin('forum_answer','forum_answer.id_question','=','forum_question.id_question')
            ->where('forum_question.status', '=', 'open');

            if($id_question > 0){
                $questionList->where('forum_question.id_question', $id_question);
            }

            $questionList->orderBy('forum_question.id_question', 'DESC')->groupBY('forum_question.id_question');

            return $questionList;   
            
        }

        /**
         * [This method is used to getQuestionFrontById] 
         * @param [Integer]$id_question[Used for question id]
         * @return Data Response
         */

        public static function getQuestionFrontById($id_question){
            $questionList = DB::table('forum_question')
            ->where('id_question',$id_question)
            ->first();

            return $questionList;
        }

        /**
         * [This method is used to get question] 
         * @param null
         * @return Data Response
         */

        public static function getQuestion(){

            return DB::table('question')
                ->select('question.*')
                ->where('question.status','active')
                ->orderBy('id','DESC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used to save answer] 
         * @param [Array]$answerArr [Used for answer]
         * @return Boolean
         */

        public static function saveAnswer($answerArr)
        {
            DB::table('forum_answer')->insert($answerArr);
        }

        /**
         * [This method is used to update question] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Varchar]$data [Used for data]
         * @return Boolean
         */

        public static function update_question($id_question,$data)
        {
            $table_question = DB::table('forum_question');
            if(!empty($data)){
                $table_question->where('id',$id_question);
                $isUpdated = $table_question->update($data);
            }
            return (bool)$isUpdated;
        }

        /**
         * [This method is used to save question] 
         * @param [Varchar]$data [Used for data]
         * @return Boolean
         */

        public static function saveQuestion($data){
            $table_question = DB::table('forum_question');
            if(!empty($data)){
                return $table_question->insertGetId($data);
            }
            return false;
        }

        /**
         * [This method is used to getQuestionById] 
         * @param [Integer]$id_question[Used for question id]
         * @return Data Response
         */

        public static function getQuestionById($id_question){
            $questionList = DB::table('forum_question')
            ->where('id_question',$id_question)
            ->first();

            return $questionList;
        }

        /**
         * [This method is used to getAnswerById] 
         * @param [Integer]$id_answer[Used for answer id]
         * @return Data Response
         */

        public static function getAnswerById($id_answer){
            $questionList = DB::table('forum_answer')
            ->where('id_answer',$id_answer)
            ->first();

            return $questionList;
        }

        /**
         * [This method is used to getNestedAnswer] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Integer]$id_parent[Used for Parent id]
         * @param [type] $html[Used for html]
         * @return Json Response
         */

        public static function getNestedAnswer($id_question, $id_parent = 0, $html = ''){
            $prefix = DB::getTablePrefix();
            $answer = DB::table('forum_answer')
            ->select([
                'forum_answer.id_answer',
                'forum_answer.id_question',
                'forum_answer.id_user',
                'forum_answer.answer_description',
                'forum_answer.up_counter',
                'forum_answer.id_parent',
                'forum_answer.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_answer.status, 1)),SUBSTRING('.$prefix.'forum_answer.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
            ])
            ->where('forum_answer.id_question',$id_question)
            ->where('forum_answer.id_parent',$id_parent)
            ->leftJoin('users as users','users.id_user','=','forum_answer.id_user')
            ->where('forum_answer.status', '!=', 'trash')
            ->get()
            ->toArray();

            $answer = json_decode(json_encode($answer), true);

            #$html .= self::generateHtml($answer);

            foreach ($answer as &$element) {
                $children = self::getNestedAnswer($id_question, $element['id_answer']);

                if ($children) {
                    #$html .= self::generateHtml($children, $html);
                    $element['children'] = $children;
                }
            }
            #dd($html);
            return $answer;
        }

        /**
         * [This method is used for remove] 
         * @param [Varchar]$answer[Used for answer]
         * @return Data Response
         */

        public static function generateHtml($answer, $html = ''){
            if(!empty($answer) && !empty($answer['html'])){
                echo '<pre>';
                print_r($answer);
                echo '</pre>';
                foreach ($answer as $element) {
                    $html .= '<div>
                        <div>'.$element['answer_description'].'</div>
                        <div>
                        <span>'.$element['up_counter'].' ups</span>
                        <span>by '.$element['person_name'].'</span>
                        <span>reply on '.$element['created'].'</span>
                        </div>
                    </div>';
                }
            }
            return $html;
        }

        /**
         * [This method is used to getAnswerByQuesId] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Integer]$id_parent[Used for Parent id]
         * @param [Enum] $type[Used for type]
         * @return Data Response
         */

        public static function getAnswerByQuesId($id_question, $id_parent = 0, $type = 'parent', $from = 'backend'){
            $prefix = DB::getTablePrefix();
            $answer = DB::table('forum_answer');
            $answer->select([
                'forum_answer.id_answer',
                'forum_answer.id_question',
                'forum_answer.id_user',
                'forum_answer.answer_description',
                'forum_answer.up_counter',
                'forum_answer.id_parent',
                'forum_answer.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_answer.status, 1)),SUBSTRING('.$prefix.'forum_answer.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
            ]);
            $answer->where('forum_answer.id_question',$id_question);
            $answer->leftJoin('users as users','users.id_user','=','forum_answer.id_user');
            $answer->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            });

            if($from == 'backend'){
                $answer->where('forum_answer.status', '!=', 'trash');
            }
            elseif($from == 'front'){
                $answer->where('forum_answer.status', 'approve');
            }

            $answer->where('forum_answer.id_parent',$id_parent);

            if($type == 'parent'){
                $answer = $answer->paginate(10);

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->where('id_parent',$value->id_answer)
                    ->get()
                    ->toArray();

                    if(!empty($has_child)){
                        $value->has_child = 1;
                    }
                    else{
                        $value->has_child = 0;
                    }
                }
                return $answer;
            }
            elseif($type == 'child'){
                $answer = $answer->get()->toArray();

                $answer = json_decode(json_encode($answer), true);

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->where('id_parent',$value['id_answer'])
                    ->get()
                    ->toArray();

                    if(!empty($has_child)){
                        $value['has_child'] = 1;
                    }
                    else{
                        $value['has_child'] = 0;
                    }
                }

                return $answer;
            }
        }

        /**
         * [This method is used to getAnswerFrontByQuesId] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Integer]$id_parent[Used for Parent id]
         * @param [Enum] $type[Used for type]
         * @return Data Response
         */

        public static function getAnswerFrontByQuesId($id_question, $orderBy = '', $id_parent = 0, $type = 'parent'){

            $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

            $prefix = DB::getTablePrefix();
            $answer = DB::table('forum_answer');
            $answer->select([
                'forum_answer.id_answer',
                'forum_answer.id_question',
                'forum_answer.id_user',
                'forum_answer.answer_description',
                'forum_answer.up_counter',
                'forum_answer.id_parent',
                'forum_answer.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_answer.status, 1)),SUBSTRING('.$prefix.'forum_answer.status, 2)) AS status'),

                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                'forum_answer.type',
                \DB::Raw("IF(({$prefix}forum_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),


                // \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                \DB::Raw($prefix."files.filename as filename"),
                \DB::Raw($prefix."files.folder as folder"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."') AS saved_answer"),
                \DB::Raw("IFNULL((SELECT {$prefix}forum_answer_vote.vote FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."'),'none') AS saved_answer_vote"),

                \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote') AS answer_upvote_count"),
                \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='downvote') AS answer_downvote_count"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_answer.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

            ]);
            $answer->where('forum_answer.id_question',$id_question);
            $answer->leftJoin('users as users','users.id_user','=','forum_answer.id_user');
            // $answer->leftJoin('forum_question','forum_question.id_question','=','forum_answer.id_question');
            $answer->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            });

            $answer->where('forum_answer.status', '=', 'approve');
            $answer->where('forum_answer.id_parent',$id_parent);

            if($orderBy == 'Upvote'){
                $answer->orderBy(\DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote')"),'DESC');
            }

            if($orderBy == 'ASC'){
                $answer->orderBy('forum_answer.id_answer','ASC');
            }

            if($orderBy == 'DESC'){
                $answer->orderBy('forum_answer.id_answer','DESC');
            }

            if($type == 'parent'){
                $answer = $answer->get()->toArray();

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->select(['forum_answer.answer_description',
                              'forum_answer.created',
                              'forum_answer.id_answer',
                              'forum_answer.id_user',
                              \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                              // \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                              \DB::Raw("(".$prefix."files.filename) as filename"),
                              \DB::Raw("(".$prefix."files.folder) as folder"),

                              \DB::Raw("IFNULL((SELECT {$prefix}forum_answer_vote.vote FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."'),'none') AS saved_answer_vote"),
                              \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote') AS answer_upvote_count"),
                              \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='downvote') AS answer_downvote_count"),

                              \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_answer.id_user and {$prefix}network_user_save.user_id='".$user_id."'  and {$prefix}network_user_save.section='user') AS is_following"),

                              'forum_answer.type',
                              \DB::Raw("IF(({$prefix}forum_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                            ])
                    ->leftJoin('users as users','users.id_user','=','forum_answer.id_user')
                    ->leftJoin('files', function($join){
                        $join->on('files.user_id', '=', 'users.id_user')
                        ->where('files.type', 'profile');
                    })
                    ->where('forum_answer.id_parent',$value->id_answer)
                    ->where('forum_answer.status','approve')
                    ->get();

                    if(!empty($has_child[0])){
                        $value->has_child = 1;
                        $value->has_child_answer = $has_child;
                    }
                    else{
                        $value->has_child = 0;
                        $value->has_child_answer = [];
                    }
                }
                return $answer;
            }elseif($type == 'child'){
                $answer = $answer->get()->toArray();

                $answer = json_decode(json_encode($answer), true);

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->where('id_parent',$value['id_answer'])
                    ->where('status','approve')
                    ->get()
                    ->count();

                    if(!empty($has_child)){
                        $value['has_child'] = 1;
                    }
                    else{
                        $value['has_child'] = 0;
                    }
                }

                return $answer;
            }
        }

        /**
         * [This method is used to getAnswerFrontByQuesId] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Integer]$id_parent[Used for Parent id]
         * @param [Enum] $type[Used for type]
         * @return Data Response
         */

        public static function getAnswerFrontByQuesIdApi($id_question, $orderBy = '', $id_parent = 0, $type = 'parent'){

            $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $base_url       = ___image_base_url();

            $prefix = DB::getTablePrefix();
            $answer = DB::table('forum_answer');
            $answer->select([
                'forum_answer.id_answer',
                'forum_answer.id_question',
                'forum_answer.id_user',
                'forum_answer.answer_description',
                'forum_answer.up_counter',
                'forum_answer.id_parent',
                'forum_answer.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_answer.status, 1)),SUBSTRING('.$prefix.'forum_answer.status, 2)) AS status'),

                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                'forum_answer.type',
                \DB::Raw("IF(({$prefix}forum_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),


                // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                \DB::Raw("
                        {$prefix}files.filename as user_img
                    "),
                \DB::Raw("
                        {$prefix}files.folder as folder
                    "),

                \DB::Raw("(SELECT count(*) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."') AS saved_answer"),
                \DB::Raw("IFNULL((SELECT {$prefix}forum_answer_vote.vote FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."'),'none') AS saved_answer_vote"),

                \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote') AS answer_upvote_count"),
                \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='downvote') AS answer_downvote_count"),

                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_answer.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

            ]);
            $answer->where('forum_answer.id_question',$id_question);
            $answer->leftJoin('users as users','users.id_user','=','forum_answer.id_user');
            // $answer->leftJoin('forum_question','forum_question.id_question','=','forum_answer.id_question');
            $answer->leftJoin('files', function($join)
            {
                $join->on('files.user_id', '=', 'users.id_user')
                ->where('files.type', 'profile');
            });

            $answer->where('forum_answer.status', '=', 'approve');
            $answer->where('forum_answer.id_parent',$id_parent);

            if($orderBy == 'Upvote'){
                $answer->orderBy(\DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote')"),'DESC');
            }

            if($orderBy == 'ASC'){
                $answer->orderBy('forum_answer.id_answer','ASC');
            }

            if($orderBy == 'DESC'){
                $answer->orderBy('forum_answer.id_answer','DESC');
            }

            if($type == 'parent'){
                $answer = $answer->get()->toArray();

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->select(['forum_answer.answer_description',
                              'forum_answer.created',
                              'forum_answer.id_answer',
                              'forum_answer.id_user',
                              \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                              // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                              \DB::Raw("
                                    {$prefix}files.filename as user_img
                                "),
                            \DB::Raw("
                                    {$prefix}files.folder as folder
                                "),

                              \DB::Raw("IFNULL((SELECT {$prefix}forum_answer_vote.vote FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer AND {$prefix}forum_answer_vote.user_id ='".$user_id."'),'none') AS saved_answer_vote"),
                              \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='upvote') AS answer_upvote_count"),
                              \DB::Raw("(SELECT count({$prefix}forum_answer_vote.id) FROM {$prefix}forum_answer_vote WHERE {$prefix}forum_answer_vote.forum_answer_id = {$prefix}forum_answer.id_answer and {$prefix}forum_answer_vote.vote='downvote') AS answer_downvote_count"),

                              \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}forum_answer.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

                              'forum_answer.type',
                              \DB::Raw("IF(({$prefix}forum_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                            ])
                    ->leftJoin('users as users','users.id_user','=','forum_answer.id_user')
                    ->leftJoin('files', function($join){
                        $join->on('files.user_id', '=', 'users.id_user')
                        ->where('files.type', 'profile');
                    })
                    ->where('forum_answer.id_parent',$value->id_answer)
                    ->where('forum_answer.status','approve')
                    ->get();

                    if(!empty($has_child[0])){
                        $value->has_child = 1;
                        $value->has_child_answer = $has_child;
                    }
                    else{
                        $value->has_child = 0;
                        $value->has_child_answer = [];
                    }
                }
                return $answer;
            }elseif($type == 'child'){
                $answer = $answer->get()->toArray();

                $answer = json_decode(json_encode($answer), true);

                foreach ($answer as &$value) {
                    $has_child = DB::table('forum_answer')
                    ->where('id_parent',$value['id_answer'])
                    ->where('status','approve')
                    ->get()
                    ->count();

                    if(!empty($has_child)){
                        $value['has_child'] = 1;
                    }
                    else{
                        $value['has_child'] = 0;
                    }
                }

                return $answer;
            }
        }

        /**
         * [This method is used to getAnswerFrontByQuesId] 
         * @param [Integer]$id_question[Used for question id]
         * @param [Integer]$id_parent[Used for Parent id]
         * @return Json Response
         */
        
        public static function __getAnswerFrontByQuesId($id_question, $id_parent = 0){
            $prefix = DB::getTablePrefix();
            $answer = DB::table('forum_answer')
            ->select([
                'forum_answer.id_answer',
                'forum_answer.id_question',
                'forum_answer.id_user',
                'forum_answer.answer_description',
                'forum_answer.up_counter',
                'forum_answer.id_parent',
                'forum_answer.approve_date',
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.created, "%d-%m-%Y") AS created'),
                \DB::raw('DATE_FORMAT('.$prefix.'forum_answer.updated, "%d-%m-%Y") AS updated'),
                \DB::raw('CONCAT(UCASE(LEFT('.$prefix.'forum_answer.status, 1)),SUBSTRING('.$prefix.'forum_answer.status, 2)) AS status'),
                \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
            ])
            ->where('forum_answer.id_question',$id_question)
            ->leftJoin('users as users','users.id_user','=','forum_answer.id_user')
            ->where('forum_answer.status', '!=', 'trash')
            ->where('forum_answer.id_parent',$id_parent)
            ->get()
            ->toArray();

            return json_decode(json_encode($answer), true);
        }

        /**
         * [This method is used for related question] 
         * @param [Integer]$id_question[Used for question id]
         * @return Json Response
         */

        public static function relatedQuestion($id_question){
            $prefix = DB::getTablePrefix();
            $question = self::getQuestionById($id_question);
            
            if(!empty($question->question_description)){
                $answer = DB::table('forum_question')
                ->select([
                    'forum_question.*',
                    \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 100) AS question_description'),
                    \DB::raw("MATCH (".$prefix."forum_question.question_description) AGAINST ('".addslashes($question->question_description)."') AS relevance")
                ])
                ->whereRaw("MATCH (".$prefix."forum_question.question_description) AGAINST ('".addslashes($question->question_description)."' IN BOOLEAN MODE) ")
                ->where('forum_question.id_question', '!=', $id_question)
                ->where('forum_question.status', 'open')
                ->groupBy('forum_question.id_question')
                ->orderBy('relevance', 'DESC')
                ->get()
                ->toArray();

                return json_decode(json_encode($answer), true);
            }else{
                return [];
            }
        }

        /**
         * [This method is used for latest question] 
         * @param null
         * @return Json Response
         */

        public static function latestQuestion(){
            $prefix = DB::getTablePrefix();

            $answer = DB::table('forum_question')
            ->select([
                'forum_question.*',
                \DB::raw('SUBSTRING('.$prefix.'forum_question.question_description, 1, 100) AS question_description'),
            ])
            ->where('forum_question.status','open')
            ->groupBy('forum_question.id_question')
            ->take(5)
            ->get()
            ->toArray();

            return json_decode(json_encode($answer), true);

            if(!empty($question->question_description)){

            }
            else{
                return [];
            }
        }

        /**
         * [This method is used to save user's] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$talent_id [Used for talent id]
         * @return Boolean
         */

        public static function question_save_user($logged_user_id, $user_id){
            $table_saved_talent = DB::table('network_user_save');
            $table_saved_talent->where(['user_id' => $logged_user_id, 'save_user_id' => $user_id,'section' => 'user']);

            if(!empty($table_saved_talent->get()->count())){
                $isSaved = $table_saved_talent->delete();

                if(!empty($isSaved)){
                    $result = [
                        'action'    => 'deleted', /*don't change*/
                        'status'    => true,
                        'send_text' => 'Follow'
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }else{
                $data = [
                    "user_id"       => $logged_user_id,
                    "save_user_id"  => $user_id,
                    "section"       => 'user',
                    "created"       => date('Y-m-d H:i:s'),
                    "updated"       => date('Y-m-d H:i:s')
                ];
                
                $isSaved = $table_saved_talent->insertGetId($data);
                
                if(!empty($isSaved)){
                    $result = [
                        'action'    => 'saved', /*don't change*/
                        'status'    => true,
                        'send_text' => 'Following'
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }

            return $result;
        }

        /**
         * [This method is used to save user's] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$question_id [Used for question id]
         * @return Boolean
         */

        public static function save_this_question($logged_user_id, $question_id){
            $table_saved_talent = DB::table('network_user_save');
            $table_saved_talent->where(['user_id' => $logged_user_id, 'save_user_id' => $question_id,'section'=>'question']);

            if(!empty($table_saved_talent->get()->count())){
                $isSaved = $table_saved_talent->delete();

                if(!empty($isSaved)){
                    $result = [
                        'action'    => 'deleted', /*don't change*/
                        'status'    => true,
                        'send_text' => 'Follow this Question'
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }else{
                $data = [
                    "user_id"       => $logged_user_id,
                    "save_user_id"  => $question_id,
                    "section"       => 'question',
                    "created"       => date('Y-m-d H:i:s'),
                    "updated"       => date('Y-m-d H:i:s')
                ];
                
                $isSaved = $table_saved_talent->insertGetId($data);
                
                if(!empty($isSaved)){
                    $result = [
                        'action'    => 'saved', /*don't change*/
                        'status'    => true,
                        'send_text' => 'Following this Question'
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }

            return $result;
        }

        public static function getAll($search,$limit,$offset, $group_members){

            $prefix       = DB::getTablePrefix();
            $questionList = DB::table('forum_question')
            ->select([
                    'forum_question.id_question',
                    'forum_question.created',
                    DB::raw('"question" as list_type')])
            ->limit($limit)
            ->offset($offset);

            if(!empty(trim($search))){
                $search = trim($search);  
                $questionList->where('forum_question.question_description','LIKE', '%'.$search.'%');
            }

            if(!empty($group_members)){
                $questionList->whereIn('forum_question.id_user', $group_members);
            }
            $questionList->orderBy('created', 'DESC');
            $questionList = $questionList->get();

            return $questionList;
        }
    }
