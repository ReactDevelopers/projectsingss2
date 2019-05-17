<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FollowQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FollowQuestion:followquestion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email to the follower question on answer posting.';

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
        $question_follow = \DB::table('forum_question')
                        ->leftjoin('network_user_save','network_user_save.save_user_id','forum_question.id_question')
                        ->leftJoin('users as users','users.id_user','=','network_user_save.user_id')
                        ->leftJoin('forum_answer','forum_answer.id_question','=','forum_question.id_question')
                        ->select([
                            'forum_question.id_question',
                            'forum_question.question_description',
                            'network_user_save.user_id',
                            'forum_question.id_user',
                            'users.name',
                            'users.email',
                        ])
                        ->groupBy('network_user_save.user_id')
                        ->orderBy('forum_question.id_question', 'DESC')
                        ->where('forum_question.status', '=', 'open')
                        ->where('forum_question.follower_status','=','Y')
                        ->get();
                        
        if($question_follow->count()>0){
            foreach ($question_follow as $key => $value) {
                $post_forum_answer_new = \DB::table('forum_answer')
                                ->select([
                                    '*'
                                ])
                                ->where('forum_answer.id_question','=',$value->id_question)
                                ->where('status','=','approve')
                                ->where('forum_answer.is_question_follow', '=','Y')
                                ->get();

                if($post_forum_answer_new->count()>0){
                    $email                  = $value->email;
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $value->name;
                    $emailData['context']   = 'User have replied on your followed question '.$value->question_description;

                    $template_name = "cron_follow_users";

                    ___mail_sender($email,'',$template_name,$emailData); 
                }
            }
            foreach ($question_follow as $key => $value) {
                \DB::table('forum_answer')->where('id_question','=',$value->id_question)->where('forum_answer.is_question_follow', '=','Y')->update(['is_question_follow'=>'N']);
            } 
        }

    }
}
