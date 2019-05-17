<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class DisputeConcern extends Model{
		protected $table = 'dispute_concern';	
	    protected $primaryKey = 'id_concern';

	    const CREATED_AT = 'created';
	    const UPDATED_AT = 'updated';

	    protected $fillable = ['id_concern','en','id','cz','ta','hi','status','created','updated'];

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = \DB::getTablePrefix();
            $language       = \App::getLocale();
            $query->addSelect([
                'dispute_concern.id_concern',
                \DB::Raw("IF(({$prefix}dispute_concern.{$language} != ''),{$prefix}dispute_concern.`{$language}`, {$prefix}dispute_concern.`en`) as reason")
            ]);

            return $query;
        } 
	}
