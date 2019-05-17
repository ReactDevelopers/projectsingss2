<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;

    class Industries extends Model{
        protected $table = 'industries';
        protected $primaryKey = 'id_industry';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';


        public function projects(){
            return $this->belongsTo('App\Models\ProjectsIndustries','industry_id','id_industry');
        }
        /**
         * [This method is used for children] 
         * @param null
         * @return Data Response
         */

        public function children(){
            return $this->hasMany('Models\Industries', 'parent');
        }

        /**
         * [This method is used for ChildrenResursive] 
         * @param null
         * @return Data Response
         */

        public function childrenRecursive(){
            return $this->children()->with('childrenRecursive');
        }

        /**
         * [This method is used for parent] 
         * @param null
         * @return Data Response
         */

        public function parent(){
            return $this->belongsTo('Models\Industries','parent');
        }

        /**
         * [This method is used for parentRecursive] 
         * @param null
         * @return Data Response
         */

        public function parentRecursive(){
            return $this->parent()->with('parentRecursive');
        }

        /**
         * [This method is used for lists] 
         * @param [String]$where[Used for where clause]
         * @param [Varchar]$keys[Used for keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */

        public static function lists($where = "1", $keys = ['*'],$limit = 8){
            return \DB::table((new static)->getTable())
            ->select($keys)
            ->whereRaw($where)
            ->offset(0)
            ->limit($limit)
            ->get();
        }

        /**
         * [This method is used for all industries] 
         * @param [Fetch]$fetch [Used for fetching]
         * @param [String]$where[Used for where clause]
         * @param [Varchar]$keys[Used for keys]
         * @param [Varchar]$order_by[Used for sorting]
         * @param [Varchar]$withSubindustries[Used for Subindustries]
         * @return Data Response
         */

        public static function allindustries($fetch = "array", $where = "", $keys = [], $order_by = "name",$withSubindustries = NULL){
            $prefix             = \DB::getTablePrefix();
            $table_industries   = \DB::table((new static)->getTable());
            \DB::statement(\DB::raw('set @row_number=0'));


            if(!empty($keys)){
                $table_industries->select($keys);
            }

            if(!empty($withSubindustries)){
                $table_industries->addSelect([
                    \DB::raw("(SELECT COUNT(*) FROM {$prefix}industries as subindustries WHERE `subindustries`.`parent` = `{$prefix}industries`.`id_industry` )as subindustries_count")
                ]);
            }

            if(!empty($where)){
                $table_industries->whereRaw($where); 
            }

            if($fetch === 'array'){
                $table_industries->orderBy('industries_order'); 
                if(!empty($withSubindustries)){
                    $table_industries->having('subindustries_count','>',1);
                }

                $industries_list = $table_industries->get();
                return json_decode(
                    json_encode(
                        $industries_list
                    ),
                    true
                );
            }else if($fetch === 'obj'){
                $table_industries->orderBy('parent.industries_order'); 
                $table_industries->leftJoin('industries as parent','parent.id_industry','=','industries.parent');
                return $table_industries->get();                
            }else if($fetch === 'single'){
                $table_industries->orderBy('industries_order'); 
                return $table_industries->get()->first();
            }else{
                $table_industries->orderBy('industries_order'); 
                return $table_industries->get();
            }
        }

        /**
         * [This method is used for Updating industry] 
         * @param [Integer]$id_industry [Used forindustry id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function update_industry($id_industry,$data){
            $table_industries = \DB::table('industries');
            if(!empty($data)){
                $table_industries->where('id_industry',$id_industry);
                $isUpdated = $table_industries->update($data);           
                $cache_key = ['industries_name','subindustries_name'];
                forget_cache($cache_key);
            }
            return (bool)$isUpdated;
        }

        /**
         * [This method is used for adding industry] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function  add_industry($data){
            $table_industries = \DB::table('industries');

            if(!empty($data)){
                $isInserted = $table_industries->insert($data);
                $cache_key = ['industries_name','subindustries_name'];
                forget_cache($cache_key);
            }
            return (bool)$isInserted;
        }   

        /**
         * [This method is used for adding industry] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function getIndustryIdByName($name){
            $table_industries = \DB::table('industries');
            $table_industries->select('id_industry');
            $table_industries->where('industries.en','LIKE', '%'.$name.'%');
            $table_industries = $table_industries->first();

            if(!empty($table_industries)){
                return $table_industries->id_industry;
            }else{
                return 0;
            }
        }
        
    }