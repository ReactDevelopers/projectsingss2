<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataCountriesStatesCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // country_id
        // mm__categories
        // mm__packages
        // mm__vendors_location
        $categories = \DB::table('mm__categories')->whereNotNull('country_id')->groupBy('country_id')->get();
        $packages = \DB::table('mm__packages')->whereNotNull('country_id')->groupBy('country_id')->get();
        $vendors_location = \DB::table('mm__vendors_location')->whereNotNull('country_id')->groupBy('country_id')->get();

        if(count($categories)){
            foreach ($categories as $item) {
                $country = \DB::table('mm__countries')->where('id', $item->country_id)->first();
                if($country){
                    $new_country = \DB::table('mm__new_countries')->where('name', $country->name)->first();
                    if(count($new_country)){
                        \DB::table('mm__categories')->where('country_id', $item->country_id)->update(['country_id' => $new_country->id]);
                    }
                }
            }
        }
        if(count($packages)){
            foreach ($packages as $item) {
                $country = \DB::table('mm__countries')->where('id', $item->country_id)->first();
                if($country){
                    $new_country = \DB::table('mm__new_countries')->where('name', $country->name)->first();
                    if(count($new_country)){
                        \DB::table('mm__packages')->where('country_id', $item->country_id)->update(['country_id' => $new_country->id]);
                    }
                }
            }
        }
        if(count($vendors_location)){
            foreach ($vendors_location as $item) {
                $country = \DB::table('mm__countries')->where('id', $item->country_id)->first();
                if($country){
                    $new_country = \DB::table('mm__new_countries')->where('name', $country->name)->first();
                    if(count($new_country)){
                        \DB::table('mm__vendors_location')->where('country_id', $item->country_id)->update(['country_id' => $new_country->id, 'states_id' => $new_country->id]);
                    }
                }
            }
        }
        //
        // city_id
        // mm__plan_subscriptions
        // mm__vendors_location
        // mm__vendors_package
        // mm_vendors_transactions
        $plan_subscriptions = \DB::table('mm__plan_subscriptions')->whereNotNull('city_id')->groupBy('city_id')->get();
        $vendors_location = \DB::table('mm__vendors_location')->whereNotNull('city_id')->groupBy('city_id')->get();
        $vendors_package = \DB::table('mm__vendors_package')->whereNotNull('city_id')->groupBy('city_id')->get();
        $vendors_transactions = \DB::table('mm_vendors_transactions')->whereNotNull('city_id')->groupBy('city_id')->get();
        if(count($plan_subscriptions)){
            foreach ($plan_subscriptions as $item) {
                $city = \DB::table('mm__cities')->where('id', $item->city_id)->first();
                if($city){
                    $new_city = \DB::table('mm__new_countries_cities')->where('name', $city->name)->first();
                    if(count($new_city)){
                        \DB::table('mm__plan_subscriptions')->where('city_id', $item->city_id)->update(['city_id' => $new_city->id]);
                    }
                }
            }
        }
        if(count($vendors_location)){
            foreach ($vendors_location as $item) {
                $city = \DB::table('mm__cities')->where('id', $item->city_id)->first();
                if($city){
                    $new_city = \DB::table('mm__new_countries_cities')->where('name', $city->name)->first();
                    if(count($new_city)){
                        \DB::table('mm__vendors_location')->where('city_id', $item->city_id)->update(['city_id' => $new_city->id]);
                    }
                }elseif($item->country_id == 236){
                    \DB::table('mm__vendors_location')->where('city_id', $item->city_id)->update(['city_id' => 1]);
                }elseif($item->country_id == 44){
                    \DB::table('mm__vendors_location')->where('city_id', $item->city_id)->update(['city_id' => 2309]);
                }
            }
        }
        if(count($vendors_package)){
            foreach ($vendors_package as $item) {
                $city = \DB::table('mm__cities')->where('id', $item->city_id)->first();
                if($city){
                    $new_city = \DB::table('mm__new_countries_cities')->where('name', $city->name)->first();
                    if(count($new_city)){
                        \DB::table('mm__vendors_package')->where('city_id', $item->city_id)->update(['city_id' => $new_city->id]);
                    }
                }
            }
        }
        if(count($vendors_transactions)){
            foreach ($vendors_transactions as $item) {
                $city = \DB::table('mm__cities')->where('id', $item->city_id)->first();
                if($city){
                    $new_city = \DB::table('mm__new_countries_cities')->where('name', $city->name)->first();
                    if(count($new_city)){
                        \DB::table('mm_vendors_transactions')->where('city_id', $item->city_id)->update(['city_id' => $new_city->id]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
