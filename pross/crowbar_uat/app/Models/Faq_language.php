<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class Faq_language extends Model{
	   	protected $table = 'faq_language';	
        protected $primaryKey = 'id_faq_language';

        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = ['id_faq_language','faq_id','title','description','language','created','updated'];        

	}