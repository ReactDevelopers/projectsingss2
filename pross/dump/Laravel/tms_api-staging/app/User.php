<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Arr;
use \Illuminate\Database\Eloquent\SoftDeletes;
use App\Lib\BulkDataQuery;
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens, SoftDeletes, BulkDataQuery;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email' ,'num_success_login','last_success_login_attempt',
        'role_id','department_id','designation','division'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('User:all', function () use($self) {
            return $self->select(\DB::raw( 'IF(deleted_at is NULL, name, CONCAT(name, " (inactive)")) as name'),'id','personnel_number')
                    ->withTrashed()->orderBy('name','asc')->get()->toArray();
        });
    }

    public function supervisorOf() {

        return $this->hasMany('App\User','supervisor_personnel_number' ,'personnel_number');
    }

    public function getIsSupervisorAttribute()
    {
        $is_supervisor  = $this->where('supervisor_personnel_number', $this->personnel_number)->first();
        return $is_supervisor ? true : false;
    }

    public function getAdmins() {
        
        return self::where('role_id', 1);
    }
}