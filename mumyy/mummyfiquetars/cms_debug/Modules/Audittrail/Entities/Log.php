<?php namespace Modules\Audittrail\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'audittrail__logs';
    protected $fillable = ['entity_id','entity_type','event_name','title','description','old_data','performed_user_id'];


    public function performedUser()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User','performed_user_id');
    }

}
