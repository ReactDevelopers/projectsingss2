<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Pages extends Model{

        /**
         * [This method is used for all] 
         * @param [type]$fetch [Used for fetching]
         * @param [Varchar]$keys [Used for keys]
         * @param [type]$where[Used for where clause]
         * @param [type]$order_by[Used for sorting]
         * @return Json Response
         */ 
        
        public static function  all($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'title'){
            $table_pages = DB::table('pages');

            if(!empty($keys)){
                $table_pages->select($keys); 
            }

            if(!empty($where)){
                $table_pages->whereRaw($where); 
            }
            
            $table_pages->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_pages->get()
                    ),
                    true
                );
            }else{
                return $table_pages->get();
            }
        }

        /**
         * [This method is used for single] 
         * @param [type]$page_slug[Page Slug]
         * @param [type]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function  single($page_slug,$keys = ['*']){
            $table_pages = DB::table('pages');

            if(!empty($keys)){
                $table_pages->select($keys); 
            }

            $table_pages->where('slug','=',$page_slug); 

            return json_decode(
                json_encode(
                    $table_pages->get()->first()
                ),
                true
            );
        }
    }