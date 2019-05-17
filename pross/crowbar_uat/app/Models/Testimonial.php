<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Testimonial extends Model
{
    protected $table = 'testimonial';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created';

    public function __construct(){}

    /**
     * [This method is for scope for default keys] 
     * @return Boolean
     */

    public static function getFrontList(){

        $prefix = DB::getTablePrefix();
        $query  = DB::table('testimonial')
                    ->select('*')
                    ->limit(2)
                    ->get();

        return $query;
    }

    public static function getTestimonialDetail($id){

        $prefix = DB::getTablePrefix();
        $query  = DB::table('testimonial')
					->select('*')
					->where('id','=',$id)
					->first();

        return $query;
    } 
}
