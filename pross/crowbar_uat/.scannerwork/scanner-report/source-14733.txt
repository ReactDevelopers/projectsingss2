<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Article extends Model
{
	protected $table = 'article';
	protected $primaryKey = 'article_id';

	/**
     * [This method is used to save article] 
     * @param [Array]$article [Used for answer]
     * @return Boolean
     */

    public static function saveArticle($article)
    {
        $article_id = \DB::table('article')->insertGetId($article);
        return $article_id;
    }

    public static function related_article(){

        $base_url       = ___image_base_url();
        $prefix         = \DB::getTablePrefix();

        /*Get 1 week previous date*/
        $from_date  = date("Y-m-d", strtotime("-1 week"));

        /*Get Admin set value from Config table*/
        $article_prevoius_days = !empty(\Cache::get('configuration')['article_prevoius_daysdvsv']) ? \Cache::get('configuration')['article_prevoius_days']: 7;
        $configure_days = "- ".$article_prevoius_days." days";

        $start_date = date("Y-m-d",strtotime($configure_days));
        $end_date   = date("Y-m-d");

        $viewed_article_id = self::getMostViewedArticle($start_date,$end_date);

        $article_list = \DB::table('article')
                        ->select(['article.*',
                            \DB::Raw("
                                    IF(
                                        {$prefix}files.filename IS NOT NULL,
                                        CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
                                        CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                                    ) as article_img
                                "),
                            ])
                        ->leftJoin('files',function($leftjoin){
                            $leftjoin->on('files.record_id','=','article.article_id');
                            $leftjoin->on('files.type','=',\DB::Raw('"article"'));
                        });

                        if(empty($viewed_article_id)){
                            $article_list->where('article.created', '>=', $from_date);
                        }else{
                            $article_list->whereIn('article.article_id', $viewed_article_id);
                        }

    
                        $article_list->limit(5);
                        $article_list = $article_list->get();


        return json_decode(json_encode($article_list),true);
    }

    public static function related_article_api(){

        $base_url       = ___image_base_url();
        $prefix         = \DB::getTablePrefix();

        /*Get 1 week previous date*/
        $from_date  = date("Y-m-d", strtotime("-1 week"));

        /*Get Admin set value from Config table*/
        $article_prevoius_days = !empty(\Cache::get('configuration')['article_prevoius_daysdvsv']) ? \Cache::get('configuration')['article_prevoius_days']: 7;
        $configure_days = "- ".$article_prevoius_days." days";

        $start_date = date("Y-m-d",strtotime($configure_days));
        $end_date   = date("Y-m-d");

        $viewed_article_id = self::getMostViewedArticle($start_date,$end_date);

        $article_list = \DB::table('article')
                        ->select(['article.*',
                            \DB::Raw("
                                    IF(
                                        {$prefix}files.filename IS NOT NULL,
                                        CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
                                        CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                                    ) as article_img
                                "),
                            ])
                        ->leftJoin('files',function($leftjoin){
                            $leftjoin->on('files.record_id','=','article.article_id');
                            $leftjoin->on('files.type','=',\DB::Raw('"article"'));
                        });

        return $article_list;
                        // if(empty($viewed_article_id)){
                        //     $article_list->where('article.created', '>=', $from_date);
                        // }else{
                        //     $article_list->whereIn('article.article_id', $viewed_article_id);
                        // }
    }

    public static function getArticleDetail($id_article){

        $base_url       = ___image_base_url();
        $prefix         = \DB::getTablePrefix();
        $user_id        = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

        $article_detail = \DB::table('article')
                        ->select(['article.*',
                            'users.name as user_name',
                            \DB::Raw("
                                    IF(
                                        {$prefix}article_file.filename IS NOT NULL,
                                        CONCAT('{$base_url}',{$prefix}article_file.folder,{$prefix}article_file.filename),
                                        'none'
                                    ) as article_img
                                "),
                            // \DB::Raw("
                            //         IF(
                            //             {$prefix}user_file.filename IS NOT NULL,
                            //             CONCAT('{$base_url}',{$prefix}user_file.folder,{$prefix}user_file.filename),
                            //             CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                            //         ) as user_img
                            //     "),
                            \DB::Raw("
                                    {$prefix}user_file.filename as user_img
                                "),
                            \DB::Raw("
                                    {$prefix}user_file.folder as folder
                                "),
                            \DB::raw('(SELECT COUNT(id_article_answer) FROM '.$prefix.'article_answer WHERE '.$prefix.'article_answer.article_id = '.$prefix.'article.article_id) AS total_reply'),

                            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='article') AS is_following"),

                            'article.type',
                            \DB::Raw("IF(({$prefix}article.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = '".$id_article."' and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='article') AS is_article_following")

                            ])
                        ->leftJoin('files as article_file',function($leftjoin){
                            $leftjoin->on('article_file.record_id','=','article.article_id');
                            $leftjoin->on('article_file.type','=',\DB::Raw('"article"'));
                        })
                        ->leftJoin('files as user_file',function($leftjoin){
                            $leftjoin->on('user_file.user_id','=','article.id_user');
                            $leftjoin->on('user_file.type','=',\DB::Raw('"profile"'));
                        })
                        ->leftJoin('users','users.id_user','=','article.id_user')
                        ->where('article.article_id', $id_article)
                        ->first();
// dd($article_detail,'zz');
        $filedata['filename'] = $article_detail->user_img;
        $filedata['folder'] = $article_detail->folder;
        $article_detail->user_img = get_file_url($filedata);

        return json_decode(json_encode($article_detail),true);
    }

    public static function getHomeArticleDetail($id_article,$api = NULL){

        $base_url       = ___image_base_url();
        $prefix         = \DB::getTablePrefix();
        $user_id        = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

        $article_detail = \DB::table('article')
                        ->select(['article.*',
                            'users.name as user_name',
                            \DB::Raw("
                                    IF(
                                        {$prefix}article_file.filename IS NOT NULL,
                                        CONCAT('{$base_url}',{$prefix}article_file.folder,{$prefix}article_file.filename),
                                        'none'
                                    ) as article_img
                                "),
                            /*\DB::Raw("
                                    IF(
                                        {$prefix}user_file.filename IS NOT NULL,
                                        CONCAT('{$base_url}',{$prefix}user_file.folder,{$prefix}user_file.filename),
                                        CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                                    ) as user_img
                                "),*/
                            \DB::Raw("
                                    {$prefix}user_file.filename as user_img
                                "),
                            \DB::Raw("
                                    {$prefix}user_file.folder as folder
                                "),
                            \DB::raw('(SELECT COUNT(id_article_answer) FROM '.$prefix.'article_answer WHERE '.$prefix.'article_answer.article_id = '.$prefix.'article.article_id LIMIT 1) AS total_reply'),

                            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user' LIMIT 1) AS is_following"),

                            'article.type',
                            \DB::Raw("IF(({$prefix}article.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name"),

                            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = '".$id_article."' and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='article' LIMIT 1) AS is_article_following")

                            ])
                        ->leftJoin('files as article_file',function($leftjoin){
                            $leftjoin->on('article_file.record_id','=','article.article_id');
                            $leftjoin->on('article_file.type','=',\DB::Raw('"article"'));
                        })
                        ->leftJoin('files as user_file',function($leftjoin){
                            $leftjoin->on('user_file.user_id','=','article.id_user');
                            $leftjoin->on('user_file.type','=',\DB::Raw('"profile"'));
                        })
                        ->leftJoin('users','users.id_user','=','article.id_user')
                        ->where('article.article_id', $id_article)
                        ->first();

        if($api=='apidata'){
            $article_detail->created = ___ago($article_detail->created);
            $article_detail->share_link = url('/network/article/detail/'.$article_detail->article_id);
        }

        return json_decode(json_encode($article_detail),true);
    }

    public static function getLastComment($id_article){

        $answer = DB::table('article_answer');
        $answer->select([
                        'article_answer.id_article_answer',
                        'article_answer.article_id',
                        'article_answer.user_id',
                        'article_answer.answer_desp',
                        ]);
        $answer->where('article_answer.article_id',$id_article);
        $answer->orderBy('article_answer.id_article_answer','DESC');
        $answer = $answer->first();
        
        return json_decode(json_encode($answer),true);
    }

    public static function getLastCommentApi($id_article){
        $prefix                 = DB::getTablePrefix();
        $base_url               = ___image_base_url();
        $answer = DB::table('article_answer');
        $answer->leftjoin('users','users.id_user','=','article_answer.user_id')
                ->leftJoin('files as user_profile',function($leftjoin){
                    $leftjoin->on('user_profile.user_id','=','users.id_user');
                    $leftjoin->on('user_profile.type','=',\DB::Raw('"profile"'));
                })
                ->select([
                    'article_answer.id_article_answer',
                    'article_answer.article_id',
                    'article_answer.user_id',
                    'article_answer.answer_desp',
                    'article_answer.created',
                    'users.name',
                    \DB::Raw("
                        IF(
                            {$prefix}user_profile.filename IS NOT NULL,
                            CONCAT('{$base_url}',{$prefix}user_profile.folder,{$prefix}user_profile.filename),

                            CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                        ) as user_img
                    ")
                ]);
        $answer->where('article_answer.article_id',$id_article);
        $answer->orderBy('article_answer.id_article_answer','DESC');
        $answer = $answer->first();

        if(!empty($answer->created)){
            $answer->created = ___ago($answer->created);
        }
        
        return json_decode(json_encode($answer),true);
    }

    /**
     * [This method is used to getAnswerFrontByQuesId] 
     * @param [Integer]$id_question[Used for question id]
     * @param [Integer]$id_parent[Used for Parent id]
     * @param [Enum] $type[Used for type]
     * @return Data Response
     */

    public static function getAnswerByQuesId($id_article, $type = 'parent'){

        $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

        $prefix = DB::getTablePrefix();
        $answer = DB::table('article_answer');
        $answer->select([
            'article_answer.id_article_answer',
            'article_answer.article_id',
            'article_answer.user_id',
            'article_answer.answer_desp',
            'article_answer.id_parent',

            'article_answer.created',
            'article_answer.updated',
            // \DB::raw('DATE_FORMAT('.$prefix.'article_answer.created, "%d-%m-%Y") AS created'),
            // \DB::raw('DATE_FORMAT('.$prefix.'article_answer.updated, "%d-%m-%Y") AS updated'),


            \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
            // \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
            \DB::Raw($prefix."files.folder as folder"),
            \DB::Raw($prefix."files.filename as filename"),


            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article_answer.user_id and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

            'article_answer.type',
            \DB::Raw("IF(({$prefix}article_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")

        ]);
        $answer->where('article_answer.article_id',$id_article);
        $answer->where('article_answer.id_parent','0');
        $answer->leftJoin('users as users','users.id_user','=','article_answer.user_id');
        $answer->leftJoin('files', function($join){
            $join->on('files.user_id', '=', 'users.id_user')
            ->where('files.type', 'profile');
        });

        if($type == 'parent'){
            $answer = $answer->get()->toArray();

            foreach ($answer as &$value) {
                $has_child = DB::table('article_answer')
                ->select(['article_answer.answer_desp',
                          'article_answer.created',
                          'article_answer.id_article_answer',
                          'article_answer.user_id',
                          \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                          // \DB::Raw("TRIM(CONCAT(".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                          \DB::Raw($prefix."files.folder as folder"),
                            \DB::Raw($prefix."files.filename as filename"),
                          \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article_answer.user_id and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

                          'article_answer.type',
                          \DB::Raw("IF(({$prefix}article_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")

                        ])
                ->leftJoin('users as users','users.id_user','=','article_answer.user_id')
                ->leftJoin('files', function($join){
                    $join->on('files.user_id', '=', 'users.id_user')
                    ->where('files.type', 'profile');
                })
                ->where('article_answer.id_parent',$value->id_article_answer)
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
        }
    }

    /**
     * [This method is used to getAnswerFrontByQuesId] 
     * @param [Integer]$id_question[Used for question id]
     * @param [Integer]$id_parent[Used for Parent id]
     * @param [Enum] $type[Used for type]
     * @return Data Response
     */

    public static function getAnswerByQuesIdApi($id_article, $type = 'parent'){

        $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
        $base_url       = ___image_base_url();

        $prefix = DB::getTablePrefix();
        $answer = DB::table('article_answer');
        $answer->select([
            'article_answer.id_article_answer',
            'article_answer.article_id',
            'article_answer.user_id',
            'article_answer.answer_desp',
            'article_answer.id_parent',

            'article_answer.created',
            'article_answer.updated',
            // \DB::raw('DATE_FORMAT('.$prefix.'article_answer.created, "%d-%m-%Y") AS created'),
            // \DB::raw('DATE_FORMAT('.$prefix.'article_answer.updated, "%d-%m-%Y") AS updated'),


            \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
            // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
            \DB::Raw("
                    {$prefix}files.filename as user_img
                "),
            \DB::Raw("
                    {$prefix}files.folder as folder
                "),


            \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article_answer.user_id and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

            'article_answer.type',
            \DB::Raw("IF(({$prefix}article_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")

        ]);
        $answer->where('article_answer.article_id',$id_article);
        $answer->where('article_answer.id_parent','0');
        $answer->leftJoin('users as users','users.id_user','=','article_answer.user_id');
        $answer->leftJoin('files', function($join){
            $join->on('files.user_id', '=', 'users.id_user')
            ->where('files.type', 'profile');
        });

        if($type == 'parent'){
            $answer = $answer->get()->toArray();

            foreach ($answer as &$value) {
                $has_child = DB::table('article_answer')
                ->select(['article_answer.answer_desp',
                          'article_answer.created',
                          'article_answer.id_article_answer',
                          'article_answer.user_id',
                          \DB::Raw("TRIM(CONCAT(".$prefix."users.first_name,' ',".$prefix."users.last_name)) as person_name"),
                          // \DB::Raw("TRIM(CONCAT('{$base_url}',".$prefix."files.folder,'',".$prefix."files.filename)) as filename"),
                          \DB::Raw("
                                    {$prefix}files.filename as user_img
                                "),
                            \DB::Raw("
                                    {$prefix}files.folder as folder
                                "),

                          \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article_answer.user_id and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

                          'article_answer.type',
                          \DB::Raw("IF(({$prefix}article_answer.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")

                        ])
                ->leftJoin('users as users','users.id_user','=','article_answer.user_id')
                ->leftJoin('files', function($join){
                    $join->on('files.user_id', '=', 'users.id_user')
                    ->where('files.type', 'profile');
                })
                ->where('article_answer.id_parent',$value->id_article_answer)
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
        }
    }

    public static function saveComment($answerArr){
        DB::table('article_answer')->insert($answerArr);
    }

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

    public static function follow_this_article($logged_user_id, $data){

        $table_saved_talent = DB::table('network_user_save');
        $table_saved_talent->where(['user_id' => $logged_user_id, 'save_user_id' => $data['post_id'],'section'=>$data['section']]);

        if(!empty($table_saved_talent->get()->count())){
            $isSaved = $table_saved_talent->delete();

            if(!empty($isSaved)){
                $result = [
                    'action'    => 'deleted', /*don't change*/
                    'status'    => true,
                    'send_text' => 'Follow this '.ucfirst($data['section'])
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
                "save_user_id"  => $data['post_id'],
                "section"       => $data['section'],
                "created"       => date('Y-m-d H:i:s'),
                "updated"       => date('Y-m-d H:i:s')
            ];
            
            $isSaved = $table_saved_talent->insertGetId($data);
            
            if(!empty($isSaved)){
                $result = [
                    'action'    => 'saved', /*don't change*/
                    'status'    => true,
                    'send_text' => 'Following this '.ucfirst($data['section'])
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
     * @param [Integer]$talent_id [Used for talent id]
     * @return Boolean
     */

    public static function countView($article_id){

        $today_date = date('Y-m-d');

        $article_views = DB::table('article_views');
        $article_views->select('article_views.*');
        $article_views->where(['article_id' => $article_id, 'date' => $today_date]);
        $article_views = $article_views->first();

        if(empty($article_views)){
                $insertArr = [
                    'article_id'    => $article_id,
                    'views_count'   => 1,
                    'date'          => date('Y-m-d'),
                    'created'       => date('Y-m-d H:i:s'),
                    'updated'       => date('Y-m-d H:i:s')
                ];

            $articles = DB::table('article_views')->insert($insertArr);
        }else{
            $count = $article_views->views_count + 1;

            $updateArr = [
                'views_count'   => $count,
                'updated'       => date('Y-m-d H:i:s')
            ];
            
            $article_views = DB::table('article_views')
                                    ->where('id',$article_views->id)
                                    ->update($updateArr);
        }
    }

    public static function getMostViewedArticle($start_date, $end_date){

        $article_views = array();

        $article_views = DB::table('article_views');
        $article_views->select('article_views.*');
        $article_views->where('article_views.date','>=',$start_date);
        $article_views->where('article_views.date','<=',$end_date);
        $article_views->orderBy('article_views.views_count', 'DESC');
        $article_views = $article_views->get();

        if(!empty($article_views)){
            $article_views = json_decode(json_encode($article_views),true);
            $article_views = array_column($article_views, 'article_id');
        }

        return $article_views; 
    }

    public static function getAll($search,$limit,$offset,$group_members){

        $prefix     = DB::getTablePrefix();
        $article_id = DB::table('article')
                        ->select(['*',
                            'article.article_id',
                            'article.created',
                            DB::raw('"article" as list_type')])
                        ->limit($limit)
                        ->offset($offset);

                        if(!empty(trim($search))){
                            $search = trim($search);
                            $article_id->where('article.title','LIKE', '%'.$search.'%');
                        }

                        if(!empty($group_members)){
                            $article_id->whereIn('article.id_user', $group_members);
                        }

                        $article_id->orderBy('created', 'DESC');
                        $article_id = $article_id->get();

        return $article_id;
    }
    public  function getArticleUser(){
        return $this->hasMAny('\Models\Users','id_user','id_user');
    }

     public  function getArticleAnwser(){
        return $this->hasMAny('\Models\ArticleAnswer','article_id','article_id');
    }

}