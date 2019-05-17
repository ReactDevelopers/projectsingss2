<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Crypt;

	class Banner extends Model
	{
	    public function __construct(){

	    }

	    /**
         * [This method is used for getting banner] 
         * @param null
         * @return Data Response
         */
        
	    public static function getBanner(){
	    	$banners = DB::table('banner')
	    	->select([
	    		DB::raw('@row_number  := @row_number  + 1 AS row_number'),
	    		'id_banner',
	    		'banner_section',
	    		'banner_title',
	    		'banner_text',
	    		'banner_variable',
	    		'banner_image',
	    		'updated'
    		])
	    	->orderBy('id_banner', 'ASC')
	    	->groupBy('banner_section')
	    	->get();

	    	$i = 1;
	    	foreach ($banners as $item) {
	    		$item->row_number = $i++;
	    	}

	    	return $banners;
	    }

	    /**
         * [This method is used for getting banner by id ] 
         * @param [Varchar]$updateArr[Used for updatesArr]         
         * @return Data Response
         */

	    public static function getBannerById($id_banner){
	    	return DB::table('banner')
	    	->where('id_banner', $id_banner)
	    	->get()
	    	->first();
	    }

	    /**
         * [This method is used for getting all banner by slug] 
         *@param [String]$slug[Used for banner identical name]
         * @return String Response
         */

	    public static function getBannerBySlug($slug){
	    	return DB::table('banner')
	    	->where('banner_variable', $slug)
	    	->get()
	    	->first();
	    }

	    /**
         * [This method is used for getting all banner by section] 
         *@param [String]$slug[Used for banner identical name]
         * @return String Response
         */

	    public static function getBannerBySection($section){
	    	return DB::table('banner')
	    	->where('banner_section', $section)
	    	->get();
	    }

	    /**
         * [This method is used for getting all banner by slug] 
         *@param [String]$slug[Used for banner identical name]
         * @return String Response
         */

	    public static function getAllBannerBySlug($slug){
	    	$banner = DB::table('banner')
	    	->where('banner_variable', $slug)
	    	->get()
	    	->toArray();

	    	return json_decode(json_encode($banner), true);
	    }

	    /**
         * [This method is used for update banner] 
         * @param [Integer]$id_banner[Used for banner id]
         * @param [Varchar]$updateArr[Used for updatesArr]
         * @return \Illuminate\Http\Response
         */

	    public static function updateBanner($id_banner, $updateArr){
	    	return DB::table('banner')
	    	->where('id_banner', $id_banner)
	    	->update($updateArr);
	    }

	    /**
         * [This method is used for inserting banner] 
         * @param [Varchar]$insertArr[Used for insertArr]
         * @return Data Response
         */

	    public static function insertBanner($insertArr){
	    	return DB::table('banner')
	    	->insertGetId($insertArr);
	    }
	}
