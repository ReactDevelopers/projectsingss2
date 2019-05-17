<?php namespace Modules\Dashboard\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class APILog extends Model
{
    // use Translatable;
	
	public $timestamps = false;
    protected $table = 'api_logs';
    public $translatedAttributes = [];
    protected $fillable = [
    	'method',
        'request_url',
        'request_string',
        'response_string',
        'user_id',
        'request_ip',
        'device_type',
        'platform',
        'created_at',
        'updated_at',
        'request_header',
        'token',
        'duration',
        'agent_info',
    ];
}
