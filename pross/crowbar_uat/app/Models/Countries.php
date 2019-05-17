<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
	protected $table = 'countries';
	protected $primaryKey = 'id_country';


	public static function getCountries(){

		$get_countries = \DB::table('countries')
						->select('iso_code','en')
						->get();

		if(!empty($get_countries)){
			$make_countries =  array();
			foreach ($get_countries as $key => $value) {
				$make_countries[$value->iso_code] = $value->en; 
			}
		}

		return json_decode(json_encode($make_countries),true);

	}

	public static function getCountryIdByCode($iso_code){

		$country_id = \DB::table('countries')
						->select('id_country')
						->where('iso_code',$iso_code)
						->first();

		$country_id = json_decode(json_encode($country_id),true);

		//Now get currency by country_id
		if(!empty($country_id)){

			$currency = \DB::table('currencies')
						->select('name')
						->where('id_country',$country_id['id_country'])
						->first();

			$currency = json_decode(json_encode($currency),true); 
		}

		if(!empty($currency)){
			return $currency['name'];
		}else{
			return 'USD';
		}

	}
}
