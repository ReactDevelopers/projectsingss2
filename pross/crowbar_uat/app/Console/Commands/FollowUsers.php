<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FollowUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FollowUsers:followusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email to the follower users on posting article, question & replies.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $prefix                 = \DB::getTablePrefix();
        $base_url               = ___image_base_url();
        $user_id                = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

        $article_follow = \Models\Article::leftjoin('network_user_save','network_user_save.save_user_id','article.id_user')
                        ->leftjoin('users','users.id_user','=','network_user_save.user_id')
                        ->select('article.article_id',
                                'article.title',
                                'network_user_save.user_id',
                                'article.id_user',
                                'users.name',
                                'users.email',
                                'article.type',
                                \DB::Raw("IF(({$prefix}article.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")
                        )->orderBy('article.article_id','DESC')
                        ->where('network_user_save.section','=','article')
                        ->where('article.follower_status','=','Y')->get();

        if($article_follow->count()>0){
            foreach ($article_follow as $key => $article) {
                $post_article_new = \DB::table('article')
                            ->select([
                                '*'
                            ])
                            ->where('article.id_user', '=', $article->id_user)
                            ->where('article.follower_status', '=', 'Y')
                            ->get();

                if($post_article_new->count()>0){
                    $email                  = $article->email;
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $article->name;
                    $emailData['context']   = 'A New Article has been added by the user u have Followed '.$article->title;

                    $template_name = "cron_follow_users";

                    ___mail_sender($email,'',$template_name,$emailData); 

                }
            }
            foreach ($article_follow as $key => $article) {
                \DB::table('article')->where('article_id','=',$article->article_id)->update(['follower_status'=>'N']); 
            }
        }

        $question_follow = \DB::table('forum_question')
                        ->leftjoin('network_user_save','network_user_save.save_user_id','forum_question.id_user')
                        ->leftJoin('users as users','users.id_user','=','network_user_save.user_id')
                        ->select([
                            'forum_question.id_question',
                            'forum_question.question_description',
                            'network_user_save.user_id',
                            'forum_question.id_user',
                            'users.name',
                            'users.email',
                        ])
                        ->orderBy('forum_question.id_question', 'DESC')
                        ->where('forum_question.status', '=', 'open')
                        ->where('network_user_save.section','=','forum_question')
                        ->where('forum_question.follower_status','=','Y')
                        ->get();

        if($question_follow->count()>0){

            foreach ($question_follow as $key => $question) {

                $post_question_new = \DB::table('forum_question')
                                ->select([
                                    '*'
                                ])
                                ->where('forum_question.id_user', '=', $question->id_user)
                                ->where('forum_question.follower_status', '=', 'Y')
                                ->get();

                if($post_question_new->count()>0){

                    $email                  = $question->email;
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $question->name;
                    $emailData['context']   = 'A New Question has been added by the user u have Followed '.$question->question_description;

                    $template_name = "cron_follow_users";

                    ___mail_sender($email,'',$template_name,$emailData); 

                }
            }

            foreach ($question_follow as $key => $question) {
                \DB::table('forum_question')->where('id_question','=',$question->id_question)->update(['follower_status'=>'N']); 
            }
        }

        $event_follow   = \DB::table('events')
                            ->leftjoin('network_user_save','network_user_save.save_user_id','events.posted_by')
                            ->leftJoin('users as users','users.id_user','=','network_user_save.user_id')
                            ->select([
                                'events.id_events',
                                'network_user_save.user_id',
                                'events.event_title',
                                'events.posted_by',
                                'users.name',
                                'users.email',
                            ])
                            ->where('events.status','=','active')
                            ->groupBy('network_user_save.user_id')
                            ->get();

        if($event_follow->count()>0){

            foreach ($event_follow as $key => $event) {

                $post_event_new = \DB::table('events')
                                ->select([
                                    '*'
                                ])
                                ->where('events.posted_by', '=', $event->posted_by)
                                ->where('events.follower_status', '=', 'Y')
                                ->get();

                if($post_event_new->count()>0){

                    $email                  = $event->email;
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $event->name;
                    $emailData['context']   = 'A New Question has been added by the user u have Followed '.$event->event_title;

                    $template_name = "cron_follow_users";

                    ___mail_sender($email,'',$template_name,$emailData); 

                }
            }

            foreach ($event_follow as $key => $event) {
                \DB::table('events')->where('id_events','=',$event->id_events)->update(['follower_status'=>'N']); 
            }
        }
        
        // dd($post_article_new,'1111',$article_follow);

    }
}
