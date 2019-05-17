<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;

class VendorProfile extends Model
{
    // use Translatable;
	use MediaRelation;
	
	public $timestamps = false;
    protected $table = 'mm__vendors_profile';
    public $translatedAttributes = [];
    protected $fillable = [
    	'user_id',
		'business_name',
		'business_phone',
		'business_phone2',
		'business_phone3',
		'business_address',
		'zip_code',
		'website',
		'how_know_mummy',
		'created_by',
		'photo',
		'photo_resize',
		'photo_thumb',
		'about',
		'contact_email',
		'social_media_link',
		'instagram_id',
		'instagram_showfeed',
		'others_social_data',
		'information',
		'rating_points',
		'lat',
		'lng',
		'dimension',
    ];

    public function getVendorSocial($type = 'facebook'){
        $social = $this->social_media_link;
        if($social){
            $arr = json_decode($social);
            if($arr){
                return isset($arr->$type) && !empty($arr->$type) ? $arr->$type : "";
            }
        }
        return "";
    }
}
