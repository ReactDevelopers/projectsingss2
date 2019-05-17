<?php namespace Modules\Customer\Composers;

use Illuminate\Contracts\View\View;
use Modules\Customer\Entities\Country;

class CountryComposer
{

    public function compose(View $view)
    {
    	$data = Country::all();
    	$countries = [];
    	if(count($data)){
    		foreach ($data as $key => $item) {
    			$countries = array_merge($countries, [$item->id, $item->name]);
    		}
    	}
        $view->with('countries', $countries);
    }
}
