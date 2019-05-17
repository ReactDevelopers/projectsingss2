<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Events_rsvp extends Model
{
    protected $table = 'events_rsvp';
    protected $primaryKey = 'id';

    public static function add_rsvp($data){

		$projects = DB::table('events_rsvp');
		$insertId = $projects->insertGetId($data);

		return $insertId;
	}

	public static function getGoingCount($event_id){

		$count = DB::table('events_rsvp')
						->select('*')
						->where('event_id','=',$event_id)
						->where('status','=','yes')
						->count();

		return $count;
	}

	public static function getEmailsById($event_id){

		$email = DB::table('events_rsvp')
						->select('email')
						->where('event_id','=',$event_id)
						->get();

		return array_column(json_decode(json_encode($email),true),'email');

	}

	public static function updateOrAddRsvp($event_id,$user_email){

		$return_val = 0;

		$count = DB::table('events_rsvp')
						->select('events_rsvp.*')
						->where('event_id','=',$event_id)
						->where('email','=',$user_email)
						->count();

		if($count == 1){

			$update_rsvp = DB::table('events_rsvp')
				 		->where('event_id','=',$event_id)
						->where('email','=',$user_email)
				 		->update(['status'=>'yes']);

			$return_val = 1;
		}else{

			$data1 = [
						'event_id'   => $event_id,
						'email'  	 =>	$user_email,
						'status' 	 => 'yes',
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					];

			$insertId = \Models\Events_rsvp::add_rsvp($data1);

			$return_val = 1;
		}

		return $return_val; 
	}

	public static function accept_event($id_event){

		$update_rsvp = DB::table((new static)->getTable())
				 		->where('id','=',$id_event)
				 		->update(['status'=>'yes']);

		return $update_rsvp;
	}

	public static function getRecordByEmail($email,$event_id){

		$get_record = DB::table((new static)->getTable())
						->select('*')
				 		->where('email','=',$email)
				 		->where('event_id','=',$event_id)
				 		->where('status','=','no')
				 		->first();

		return $get_record;
	}
}