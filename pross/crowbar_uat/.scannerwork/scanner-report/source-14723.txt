<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id_file';

	const CREATED_AT = 'created';
	const UPDATED_AT = 'updated';

    protected $fillable = [
        'user_id', 'record_id', 'reference', 'filename', 'extension', 'folder', 'type', 'caption','size', 'is_default', 'status', 'created','updated',
    ];

	public function __construct(){
	}

    /**
     * [This method is for scoping default keys] 
     * @return Boolean
     */

    public function scopeDefaultKeys($query){
        $base_url       = ___image_base_url();
        
        $query->addSelect([
            'id_file',
            'record_id',
            'extension',
            'filename',
            'folder',
            'filename',
            \DB::Raw("CONCAT('{$base_url}','uploads/proposals/',filename) as file_url")
        ]);

        return $query;
    }

	/**
         * [This method is used to upload file] 
         * @param [Integer]$file_ids[Used for file id]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */

	public static function update_file($file_ids,$data){
		$table_files = DB::table('files');
		
		$table_files->whereIn('id_file',$file_ids)->update($data);
		return true;
	}

    public static function delete_file($record_id){
        DB::table('files')->where('record_id', $record_id)->where('type','=','events')->delete();
        return true;
    }
}
