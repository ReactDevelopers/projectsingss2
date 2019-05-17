<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Events extends Model
{
	protected $table = 'events';
	protected $primaryKey = 'id_events';

	public function emails(){
		return $this->hasMany('\Models\Events_rsvp','event_id','id_events');
	}
	public function country_name(){
		return $this->hasOne('\Models\Countries','id_country','country');
	}
	public function state_name(){
		return $this->hasOne('\Models\State','id_state','state');
	}
	public function file(){
		return $this->hasOne('\Models\File','record_id','id_events')->where('type','=','event');
	}
	public function city_name(){
		return $this->hasOne('\Models\City','id_city','city');
	}

	public static function addevent($data){

		$projects = DB::table('events');
		$insertId = $projects->insertGetId($data);

		return $insertId;
	}

	public static function getEventListById($id_user){

		$get_date   = date('Y-m-d');
		$events = DB::table('events')
						->select('*')
						->where('posted_by','=',$id_user)
						->where('status','!=', 'deleted')
						->where('status','!=', 'draft')
						->orderBy('id_events','DESC')
						->where('event_date', '>=', $get_date)
						->get();

		return json_decode(json_encode($events),true);

	}

	public static function getEventList($inCheck,$inEvent_date=''){

		$user_id = \Auth::user()->id_user;


		$base_url       = ___image_base_url();
		$prefix 		= DB::getTablePrefix();
		$language 		= \App::getLocale();

		$events 		= DB::table('events');
		DB::statement(\DB::raw('set @row_number=0'));
		$events->select([
			'events.*',
			DB::raw('@row_number  := @row_number  + 1 AS row_number'),
			DB::Raw("CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename) as image"),
			DB::Raw("IFNULL((SELECT {$prefix}events_rsvp.status FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.email ='".\Auth::user()->email."' LIMIT 1),'no') AS rsvp_response_status"),
			DB::Raw("(SELECT count(*) FROM {$prefix}saved_event WHERE {$prefix}saved_event.event_id = {$prefix}events.id_events AND {$prefix}saved_event.user_id ='".\Auth::user()->id_user."' LIMIT 1) AS saved_bookmark"),
			DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country"),
			DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state"),
			DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city"),
			DB::Raw("(SELECT count(*) FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.status ='yes' LIMIT 1) AS total_attending"),
			DB::Raw(" COUNT( distinct rsvp_id) AS in_circle_attending"),

		]);
		$events->leftJoin('files',function($leftjoin){
			$leftjoin->on('files.record_id','=','events.id_events');
			$leftjoin->on('files.type','=',\DB::Raw("'event'"));
		});

		$events->leftjoin(\DB::raw("(SELECT 
							event_id ,
							{$prefix}events_rsvp.id as rsvp_id
						FROM 
						{$prefix}events_rsvp 
						 JOIN {$prefix}users ON {$prefix}users.email = {$prefix}events_rsvp.email 
						 JOIN {$prefix}members ON {$prefix}members.member_id = {$prefix}users.id_user 
						and {$prefix}members.user_id = '$user_id' where {$prefix}events_rsvp.status ='yes' ) as rsvp"),\DB::raw('rsvp.event_id'),'=','events.id_events');

		if(!empty($inCheck) && in_array('circle', $inCheck)){
			$events->whereRaw('rsvp.event_id is not null');
		}

		if(!empty($inCheck) && in_array('bookmark', $inCheck)){
			$events->join('saved_event', function($leftjoin) use($user_id){
				$leftjoin->on('saved_event.event_id','=','events.id_events')
				->on('saved_event.user_id','=',\DB::Raw("$user_id"));			
			});
		}

		if(!empty($inEvent_date)){

			$date_event = explode('-',$inEvent_date);
			$start_date = explode('/',str_replace(' ','',$date_event[0]));
			$end_date = explode('/',str_replace(' ','',$date_event[1]));

			$events->where('event_date','>=',$start_date[2].'/'.$start_date[0].'/'.$start_date[1]);
			$events->where('event_date','<=',$end_date[2].'/'.$end_date[0].'/'.$end_date[1]);
		}


		// $events->leftjoin('members','members.user_id','=',\DB::Raw("$user_id"));
		$events->leftJoin('members', function($join) use($user_id) {
  			$join->on('members.user_id','=',\DB::Raw("$user_id"));
  			$join->on('members.member_id','=','events.posted_by');
  		});

		$events->leftjoin('users','users.id_user','=','members.member_id');
		$events->leftjoin('countries','countries.id_country','=','events.country');
		$events->leftjoin('state','state.id_state','=','events.state');
		$events->leftjoin('city','city.id_city','=','events.city');
		
		$events->whereRaw("({$prefix}events.visibility = 'public' OR ({$prefix}events.visibility = 'circle' and {$prefix}members.id is NOT NULL) OR {$prefix}events.posted_by = '$user_id') ");

		$events->whereNotIn('events.status',['deleted','draft']);
		$events->orderBy('events.id_events','DESC');
		$events->groupBy('events.id_events');
		$events = $events->get();

		return $events;
	}

	public static function getEventListApi($inCheck,$inEvent_date=''){

		$user_id = \Auth::user()->id_user;


		$base_url       = ___image_base_url();
		$prefix 		= DB::getTablePrefix();
		$language 		= \App::getLocale();

		$events 		= DB::table('events');
		$events->select([
			'events.*',
			DB::Raw("CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename) as image"),
			DB::Raw("IFNULL((SELECT {$prefix}events_rsvp.status FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.email ='".\Auth::user()->email."' LIMIT 1),'no') AS rsvp_response_status"),
			DB::Raw("(SELECT count(*) FROM {$prefix}saved_event WHERE {$prefix}saved_event.event_id = {$prefix}events.id_events AND {$prefix}saved_event.user_id ='".\Auth::user()->id_user."') AS saved_bookmark"),
			DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country"),
			DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state"),
			DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city"),
			DB::Raw("(SELECT count(*) FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.status ='yes' LIMIT 1) AS total_attending"),
			DB::Raw(" COUNT( distinct rsvp_id) AS in_circle_attending"),

		]);
		$events->leftJoin('files',function($leftjoin){
			$leftjoin->on('files.record_id','=','events.id_events');
			$leftjoin->on('files.type','=',\DB::Raw("'event'"));
		});

		$events->leftjoin(\DB::raw("(SELECT 
							event_id ,
							{$prefix}events_rsvp.id as rsvp_id
						FROM 
						{$prefix}events_rsvp 
						 JOIN {$prefix}users ON {$prefix}users.email = {$prefix}events_rsvp.email 
						 JOIN {$prefix}members ON {$prefix}members.member_id = {$prefix}users.id_user 
						and {$prefix}members.user_id = '$user_id' where {$prefix}events_rsvp.status ='yes' ) as rsvp"),\DB::raw('rsvp.event_id'),'=','events.id_events');

		if(!empty($inCheck) && in_array('circle', $inCheck)){
			$events->whereRaw('rsvp.event_id is not null');
		}

		if(!empty($inCheck) && in_array('bookmark', $inCheck)){
			$events->join('saved_event', function($leftjoin) use($user_id){
				$leftjoin->on('saved_event.event_id','=','events.id_events')
				->on('saved_event.user_id','=',\DB::Raw("$user_id"));			
			});
		}

		if(!empty($inEvent_date)){

			$date_event = explode('-',$inEvent_date);
			$start_date = explode('/',str_replace(' ','',$date_event[0]));
			$end_date = explode('/',str_replace(' ','',$date_event[1]));

			$events->where('event_date','>=',$start_date[2].'/'.$start_date[0].'/'.$start_date[1]);
			$events->where('event_date','<=',$end_date[2].'/'.$end_date[0].'/'.$end_date[1]);
		}


		// $events->leftjoin('members','members.user_id','=',\DB::Raw("$user_id"));
		$events->leftJoin('members', function($join) use($user_id) {
  			$join->on('members.user_id','=',\DB::Raw("$user_id"));
  			$join->on('members.member_id','=','events.posted_by');
  		});

		$events->leftjoin('users','users.id_user','=','members.member_id');
		$events->leftjoin('countries','countries.id_country','=','events.country');
		$events->leftjoin('state','state.id_state','=','events.state');
		$events->leftjoin('city','city.id_city','=','events.city');
		
		$events->whereRaw("({$prefix}events.visibility = 'public' OR ({$prefix}events.visibility = 'circle' and {$prefix}members.id is NOT NULL) OR {$prefix}events.posted_by = '$user_id') ");

		$events->whereNotIn('events.status',['deleted','draft']);
		$events->orderBy('events.id_events','DESC');
		$events->groupBy('events.id_events');

		return $events;
	}

	public static function updateEventStatusById($id_events){

		$events = DB::table('events')
		->where('id_events','=',$id_events)
		->update(['status'=>'deleted']);

		return $events;

	}

	public static function getEventById($id_events){

		$prefix = DB::getTablePrefix();
		$language = \App::getLocale();

		// $events = DB::table('events')
		// ->select([
		// 	'events.*',
		// 	DB::raw("(SELECT GROUP_CONCAT({$prefix}events_rsvp.email) FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events) AS emails"),
		// 	DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country_name"),
		// 	DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state_name"),
		// 	DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city_name")
		// ])
		// ->leftjoin('countries','countries.id_country','=','events.country')
		// ->leftjoin('state','state.id_state','=','events.state')
		// ->leftjoin('city','city.id_city','=','events.city')
		// ->where('events.id_events','=',$id_events)
		// ->first();

		$events = self::select(['*'])
		->with([
			'emails' => function($q) use($language){
				$q->select('event_id','email');
			},'country_name' => function($q) use($prefix,$language){
				$q->select('id_country',DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country_name"));
			},'state_name' => function($q) use($prefix,$language){
				$q->select('id_state',DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state_name"));
			}, 'city_name' => function($q) use($prefix,$language){
				$q->select('id_city',DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city_name"));
			}, 'file' => function($q) use($prefix,$language,$id_events){
				$q->select('*');
			}
		])->where('id_events','=',$id_events)->first();

		return json_decode(json_encode($events),true);

	}

	public static function getEventDetailById($id_events){

		$prefix = DB::getTablePrefix();
		$language = \App::getLocale();
		$events = self::select(['*'])
		->with([
			'emails' => function($q) use($language){
				$q->select('event_id','email');
			},'country_name' => function($q) use($prefix,$language){
				$q->select('id_country',DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country_name"));
			},'state_name' => function($q) use($prefix,$language){
				$q->select('id_state',DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state_name"));
			}, 'city_name' => function($q) use($prefix,$language){
				$q->select('id_city',DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city_name"));
			}, 'file' => function($q) use($prefix,$language,$id_events){
				$q->select('*');
			}
		])->where('id_events','=',$id_events)->first();

		return json_decode(json_encode($events),true);

	}

	public static function getDraftEvent(){

		$prefix = DB::getTablePrefix();
		$language = \App::getLocale();
		$events = self::select(['*'])
		->with([
			'emails' => function($q) use($language){
				$q->select('event_id','email');
			},'country_name' => function($q) use($prefix,$language){
				$q->select('id_country',DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country_name"));
			},'state_name' => function($q) use($prefix,$language){
				$q->select('id_state',DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state_name"));
			}, 'city_name' => function($q) use($prefix,$language){
				$q->select('id_city',DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city_name"));
			}, 'file' => function($q) use($prefix,$language){
				$q->select('*');
			}
		])->where('posted_by','=',\Auth::user()->id_user)
		->where('status','=','draft')
		->orderBy('id_events','DESC')->first();

		return json_decode(json_encode($events),true);

	}

	/**
	 * [This method is used to save user's] 
	 * @param [Integer]$user_id [Used for user id]
	 * @param [Integer]$event_id [Used for event id]
	 * @return Boolean
	 */

	public static function save_fav_event($user_id, $event_id){
		$table_saved_event = DB::table('saved_event');

		$table_saved_event->where(['user_id' => $user_id, 'event_id' => $event_id]);

		if(!empty($table_saved_event->get()->count())){
			$isSaved = $table_saved_event->delete();

			if(!empty($isSaved)){
				$result = [
					'action' => 'deleted_saved_event',
					'status' => true
				];
			}else{
				$result = [
					'action' => 'failed',
					'status' => false
				];
			} 
		}else{
			$data = [
				"user_id"   => $user_id,
				"event_id"  => $event_id,
				"created"   => date('Y-m-d H:i:s'),
				"updated"   => date('Y-m-d H:i:s')
			]; 
			
			$isSaved = $table_saved_event->insertGetId($data);


			if(!empty($isSaved)){
				$result = [
					'action' => 'saved_event',
					'status' => true
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

	public static function updateEventById($id_events,$data){

		$events = DB::table('events')
		->where('id_events','=',$id_events)
		->update($data);

		return $events;

	}

	public static function deleteEventDraft(){

		$table_saved_event = DB::table('events');
		$table_saved_event->where(['posted_by' => \Auth::user()->id_user, 'status' => 'draft']);
		$isSaved = $table_saved_event->delete();

		return  $isSaved;
	} 

	public static function getHomeEventDetail($event_id,$api = NULL){

		$user_id = \Auth::user()->id_user;

		$base_url       = ___image_base_url();
		$prefix 		= DB::getTablePrefix();
		$language 		= \App::getLocale();

		$events 		= DB::table('events');
		$events->select([
			'events.*',
			DB::Raw("CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename) as image"),
			DB::Raw("IFNULL((SELECT {$prefix}events_rsvp.status FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.email ='".\Auth::user()->email."' LIMIT 1),'no') AS rsvp_response_status"),
			DB::Raw("(SELECT count(*) FROM {$prefix}saved_event WHERE {$prefix}saved_event.event_id = {$prefix}events.id_events AND {$prefix}saved_event.user_id ='".\Auth::user()->id_user."' LIMIT 1) AS saved_bookmark"),
			DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country"),
			DB::Raw("IF((`{$prefix}state`.`{$language}` != ''),`{$prefix}state`.`{$language}`, `{$prefix}state`.`en`) as state"),
			DB::Raw("IF((`{$prefix}city`.`{$language}` != ''),`{$prefix}city`.`{$language}`, `{$prefix}city`.`en`) as city"),
			DB::Raw("(SELECT count(*) FROM {$prefix}events_rsvp WHERE {$prefix}events_rsvp.event_id = {$prefix}events.id_events AND {$prefix}events_rsvp.status ='yes' LIMIT 1) AS total_attending"),
			DB::Raw(" COUNT( distinct rsvp_id) AS in_circle_attending"),

			// \DB::Raw("
   //              IF(
   //                  {$prefix}user_file.filename IS NOT NULL,
   //                  CONCAT('{$base_url}',{$prefix}user_file.folder,{$prefix}user_file.filename),
   //                  CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
   //              ) as user_profile_img
   //          ")
		]);

		$events->leftJoin('files',function($leftjoin){
			$leftjoin->on('files.record_id','=','events.id_events');
			$leftjoin->on('files.type','=',\DB::Raw("'event'"));
		});

		// $events->leftJoin('files as user_file',function($leftjoin){
		// 	$leftjoin->on('user_file.record_id','=','users2.id_user');
		// 	$leftjoin->on('user_file.type','=',\DB::Raw("'profile'"));
		// });

		$events->leftjoin(\DB::raw("(SELECT 
							event_id ,
							{$prefix}events_rsvp.id as rsvp_id
						FROM 
						{$prefix}events_rsvp 
						 JOIN {$prefix}users ON {$prefix}users.email = {$prefix}events_rsvp.email 
						 JOIN {$prefix}members ON {$prefix}members.member_id = {$prefix}users.id_user 
						and {$prefix}members.user_id = '$user_id' where {$prefix}events_rsvp.status ='yes' ) as rsvp"),\DB::raw('rsvp.event_id'),'=','events.id_events');


		// $events->leftjoin('members','members.user_id','=',\DB::Raw("$user_id"));
		$events->leftJoin('members', function($join) use($user_id) {
  			$join->on('members.user_id','=',\DB::Raw("$user_id"));
  			$join->on('members.member_id','=','events.posted_by');
  		});

		$events->leftjoin('users','users.id_user','=','members.member_id');

		// $events->leftjoin('users as users2','users2.id_user','=','events.posted_by');

		$events->leftjoin('countries','countries.id_country','=','events.country');
		$events->leftjoin('state','state.id_state','=','events.state');
		$events->leftjoin('city','city.id_city','=','events.city');
		
		$events->whereRaw("({$prefix}events.visibility = 'public' OR ({$prefix}events.visibility = 'circle' and {$prefix}members.id is NOT NULL) OR {$prefix}events.posted_by = '$user_id') ");

		$events->whereNotIn('events.status',['deleted','draft']);
		$events->where('events.id_events',$event_id);
		$events->orderBy('events.id_events','DESC');
		$events->groupBy('events.id_events');
		$events = $events->get();

		if($event_id > 0){
            $events = $events->first();
            $events = json_decode(json_encode($events), true);

            if($api=='apidata' && !empty($events)){
	            $events['created'] = ___ago($events['created']);
	            $events['share_link'] = url('/mynetworks/eventsdetail/'.$events['id_events']);
	        }
        }

		return $events;
	}

	public static function userDetailsForEvent($event_id){
        
        $prefix     = DB::getTablePrefix();
		$base_url       = ___image_base_url();

        $user_id = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

		$event_post_by = \DB::table('events')
							->select([
										'events.posted_by',
										'users.name as user_name',
										// \DB::Raw("
		        //                             IF(
		        //                                 {$prefix}files.filename IS NOT NULL,
		        //                                 CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
		        //                                 CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
		        //                             ) as user_img
		        //                         "),
		                                \DB::Raw(" {$prefix}files.filename as filename "),
		                                \DB::Raw(" {$prefix}files.folder as folder "),
		                                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}events.posted_by and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user' LIMIT 1) AS is_evt_following"),
										'events.created'
									])
							->leftJoin('users','users.id_user','=','events.posted_by')
							->leftJoin('files',function($leftjoin){
	                            $leftjoin->on('files.user_id','=','events.posted_by');
	                            $leftjoin->on('files.type','=',\DB::Raw('"profile"'));
	                        })
							->where('id_events','=',$event_id)
							->first();

		if(!empty($event_post_by->created)){
			$event_post_by->created = ___ago($event_post_by->created);
        }

		return json_decode(json_encode($event_post_by),true);
	}

	public static function article_save_user($logged_user_id, $user_id){

        $table_saved_talent = DB::table('network_user_save');
        $table_saved_talent->where(['user_id' => $logged_user_id, 'save_user_id' => $user_id, 'section' => 'events']);

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
                "section"       => 'events',
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

	public static function getAll($search,$limit,$offset,$group_members){

        $prefix     = DB::getTablePrefix();
        $events 	= DB::table('events')
                        ->select([
                        	'events.id_events',
                        	'events.created',
                        	DB::raw('"event" as list_type')])
                        ->where('events.status','active')
                        ->limit($limit)
                        ->offset($offset);

                        if(!empty(trim($search))){
                            $search = trim($search);
                            $events->where('events.event_title','LIKE', '%'.$search.'%');
                        }

                        if(!empty($group_members)){
			                $events->whereIn('events.posted_by', $group_members);
			            }

                        $events->orderBy('created', 'DESC');
                        $events = $events->get();
        return $events;
    }

}
