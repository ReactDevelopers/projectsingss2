<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class Faqs extends Model{
	   	protected $table = 'faq';	
        protected $primaryKey = 'id_faq';

        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = ['id_faq','type','parent','status','created','updated'];

		public function categories(){
            return $this->hasMany('Models\Faqs','parent','id_faq');
        } 

		public function posts(){
            return $this->hasMany('Models\Faqs','parent','id_faq');
        }

		public function scopeCategoryTopic($query){
            $query->leftJoin('faq_language as topic','topic.faq_id','=','faq.parent')->addSelect([
            	'topic.title as parent_title'
        	]);

            return $query;
        } 

        public function postcategory(){
            return $this->hasOne(self::class,'id_faq','parent');
        } 

        public function topicCategory(){
            return $this->hasMany(self::class,'parent','id_faq');
        }

        public function categoryPost(){
            return $this->hasMany(self::class,'parent','id_faq');
        }
        
        public function description(){
            return $this->hasOne(Faq_language::class,'faq_id','id_faq');
        }

        public function faqResponse(){
            return $this->hasMany(Faq_response::class,'faq_id','id_faq');
        }

        public function faqResponseCount(){
            return $this->hasMany(Faq_response::class,'faq_id','id_faq');
        }

        public function faqResponseByIp(){
            return $this->hasOne(Faq_response::class,'faq_id','id_faq');
        }
	}