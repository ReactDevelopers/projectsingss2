<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;

    class Skills extends Model{
        protected $table = 'skill';
        protected $primaryKey = 'id_skill';

        public static function getSkillIdByName($name){
            $table_skill = \DB::table('skill');
            $table_skill->select('id_skill');
            $table_skill->where('skill_name','LIKE', '%'.$name.'%');
            $table_skill = $table_skill->first();

            if(!empty($table_skill)){
                return $table_skill->id_skill;
            }else{
                return 0;
            }
        }
        
    }