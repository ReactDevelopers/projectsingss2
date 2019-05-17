<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    use \App\Lib\BulkDataQuery;
	public $incrementing = false;
	protected $primaryKey ='name';
	    
    protected $fillable = [
        'title', 
        'description',
        'section',
        'name',
    ];
}