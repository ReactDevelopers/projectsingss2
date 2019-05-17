<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class connectedTalent extends Model
{
    protected $table = 'connected_talent';
	protected $primaryKey = 'id_connect';

	protected $fillable = ['send_by','send_to_name','send_to_email','created_at','updated_at'];

	public function getConnectedTalentList()
	{
		$this->hasOne('\App\Models\Users','email','send_to_email');
	}
}
