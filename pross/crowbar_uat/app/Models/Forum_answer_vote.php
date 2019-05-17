<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Forum_answer_vote extends Model
{

	protected $table = 'forum_answer_vote';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = [
        'forum_answer_id',
        'user_id',
        'vote',
    ];

    /**
     * [This method is used forchange] 
     * @param [Integer]$id_question [Used for question id]
     * @param [Varchar]$data[Used for data]
     * @return Boolean
     */

    public static function count_votes($answer_id){

        $prefix = \DB::getTablePrefix();

        $forum_answer_upvote = DB::table('forum_answer_vote');
        $forum_answer_upvote->select(['vote']);
        $forum_answer_upvote->where('forum_answer_id',$answer_id);
        $forum_answer_upvote->where('vote','upvote');
        $forum_answer_upvote = $forum_answer_upvote->count();

        $forum_answer_downvote = DB::table('forum_answer_vote');
        $forum_answer_downvote->select(['vote']);
        $forum_answer_downvote->where('forum_answer_id',$answer_id);
        $forum_answer_downvote->where('vote','downvote');
        $forum_answer_downvote = $forum_answer_downvote->count();

        return ['upvote_count' => $forum_answer_upvote , 'downvote_count' => $forum_answer_downvote ];
    }
}
