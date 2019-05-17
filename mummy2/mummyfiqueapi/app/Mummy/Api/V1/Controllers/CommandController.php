<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Helper;
use DB;
use App\Mummy\Api\V1\Entities\UserReview;
use App\Mummy\Api\V1\Entities\Vendors\VendorProfile;
use App\Mummy\Api\V1\Entities\Vendor;

class CommandController extends ApiController
{
    public function getUpdateUserReview(){
        $reviews = UserReview::whereNotNull('user_id')
        					->where('status', 1)
        					->get();
        $arr = [];
        foreach ($reviews as $key => $item) {
        	$user = $item->user;
        	$vendor_id = $item->vendor_id;
			$user_id = $item->user_id;
        	if(!$user){
        		$arr[] = $item;
        		$item->delete();

        		// update rating point
        		$vendorProfile = VendorProfile::where('user_id',$vendor_id)->first();
        		$items = UserReview::where('vendor_id',$vendor_id)->where('status',1)->whereNull('is_deleted')->get();
	            if(isset($vendorProfile) && !empty($vendorProfile))
	            {
	                 $this->updateRatingPoint($vendorProfile,$items);
	            }
        	}
        }
        return response([
                        'data'   =>  $arr
                        ]
                    ,Response::HTTP_OK);
    } 

    public function getUpdateUserRating(){
        $vendors = Vendor::join('role_users', 'users.id', '=', 'role_users.user_id')
                        ->where('role_users.role_id', 3)
                        ->where('users.status', 1)
                        ->whereNull('users.is_deleted')
                        ->get();
        if(count($vendors)){
            foreach($vendors as $key => $item){
                $vendorProfile = VendorProfile::where('user_id', $item->id)->first();
                $reviews = UserReview::where('vendor_id', $item->id)->where('status',1)->whereNull('is_deleted')->get();
                if(isset($vendorProfile) && !empty($vendorProfile))
                {
                     $this->updateRatingPoint($vendorProfile,$reviews);
                }
                $this->updateRatingPoint($vendorProfile,$reviews);
            }
        }
        return response([
                        'message'   =>  'Successfully'
                        ]
                    ,Response::HTTP_OK);
    } 

    protected function updateRatingPoint($vendorProfile,$reviews)
    {
        $total = 0;
        $point = 0;
        $countReview = count($reviews);
        if($countReview > 0)
        {
            foreach ($reviews as $key => $review) {
                if($review->rating)
                {
                     $total += $review->rating;
                }
            }
            $point = ROUND(($total / $countReview) * 2);
        }
        
        if(isset($vendorProfile) && !empty($vendorProfile))
        {
            $vendorProfile->rating_points = $point;
            $vendorProfile->save();
        }
        
        return $vendorProfile;
    }

    public function updateVendorPhotoThumb(Request $request){
        $take = $request->get('take') ? $request->get('take') : 10;
        $vendors = Vendor::join('role_users', 'users.id', '=', 'role_users.user_id')
                        ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                        ->where('role_users.role_id', 3)
                        ->where('users.status', 1)
                        ->whereNull('users.is_deleted')
                        ->whereNull('mm__vendors_profile.photo_thumb')
                        ->whereNotNull('mm__vendors_profile.photo')
                        ->limit($take)
                        ->orderBy('users.id', 'desc')
                        ->get();

        $allVendors = Vendor::join('role_users', 'users.id', '=', 'role_users.user_id')
                        ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                        ->where('role_users.role_id', 3)
                        ->where('users.status', 1)
                        ->whereNull('users.is_deleted')
                        ->whereNull('mm__vendors_profile.photo_thumb')
                        ->whereNotNull('mm__vendors_profile.photo')
                        ->orderBy('users.id', 'desc')
                        ->get();
        $defaultWidth = 50;
        $dataResponse = [];
        $arrImg = ['.jpg', '.png'];
        $photo_thumb = '';
        if(count($vendors)){
            foreach ($vendors as $key => $value) {
                if(!empty($value->photo))
                {
                    $url = Helper::getImage($value->photo);

                    if($this->is_url_exist($url)){
                        $basename = basename($url);
                   
                        $abc = explode('.', $basename);
                        $imageThumb = $abc[0].'_smallThumb.'.$abc[1];

                        $img = \Image::make($url);
                        $ratioImage = $img->height() / $img->width();
                        $heightResize = $defaultWidth * $ratioImage;

                        $urlImageThumb = str_replace($basename, $imageThumb, $url);
                        if(!$this->is_url_exist($urlImageThumb)){
                            $urlImage = Helper::uploadImage($img,$imageThumb,$defaultWidth,round($heightResize,2));
                            if($urlImage)
                            {
                                $photo_thumb =  $urlImage;
                                $dataUpdate = [
                                    'photo_thumb' => $urlImage,
                                    // 'dimension' => json_encode(array('width' => $defaultWidth, 'height' => round($heightResize,2))),
                                ];
                                VendorProfile::where('id', $value->id)->update($dataUpdate);
                            }
                        }else{
                            $urlImageThumb = \Image::make($urlImageThumb);
                            $abc = explode('.', $value->photo);
                            $photo_thumb = $abc[0].'_smallThumb.'.$abc[1];
                            $photo_thumb = str_replace( $abc[0].'.'.$abc[1], $photo_thumb, $value->photo);
                            if($urlImageThumb->width() > $defaultWidth){
                                if(in_array(substr($photo_thumb, -4), $arrImg)){
                                    // remove old image
                                    Helper::removeImage($photo_thumb);
                                    $urlImage = Helper::uploadImage($img,$imageThumb,$defaultWidth,round($heightResize,2));
                                    if($urlImage)
                                    {
                                        $photo_thumb =  $urlImage;
                                        $dataUpdate = [
                                            'photo_thumb' => $urlImage,
                                            // 'dimension' => json_encode(array('width' => $defaultWidth, 'height' => round($heightResize,2))),
                                        ];
                                        VendorProfile::where('id', $value->id)->update($dataUpdate);
                                    }
                                }else{
                                    VendorProfile::where('id', $value->id)->update(['photo' => null]);
                                }
                            }else{
                                $dataUpdate = [
                                    'photo_thumb' => $photo_thumb,
                                    // 'dimension' => json_encode(array('width' => $defaultWidth, 'height' => round($heightResize,2))),
                                ];
                                VendorProfile::where('id', $value->id)->update($dataUpdate);
                            }
                        }
                        
                    }else{
                        VendorProfile::where('id', $value->id)->update(['photo' => null]);
                    }
                    
                    $dataResponse[] = [
                        'id' => $value->id,
                        'email' => $value->email,
                        'photo' => $value->photo,
                        'photo_thumb' => $photo_thumb,
                        'url_photo_thumb' => 'https://proj-mummy-fique.s3.ap-southeast-1.amazonaws.com' . $photo_thumb,
                    ];
                    
                }
            }
        }
        return response([
            'data' => [
                'vendors'   =>  $dataResponse,
                'count_left' => count($allVendors) - count($vendors),
                ],
        ],Response::HTTP_OK);      
    }

    protected function is_url_exist($url){
        $ch = curl_init($url);    
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
           $status = true;
        }else{
          $status = false;
        }
        curl_close($ch);
       return $status;
    }

}
