<?php

use Illuminate\Database\Seeder;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Entities\City;
use Modules\Portfolio\Entities\Portfolio;
use Modules\Portfolio\Entities\PortfolioMedia;

class PorfoliosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service = app(VendorService::class);

        $vendors = $service->all();
        $faker = \Faker\Factory::create();
        if(count($vendors)){
        	foreach ($vendors as $key => $item) {
	            $portfolio = new Portfolio;
	            $portfolio->category_id = $item->vendorCategory[0]->category_id;
				$portfolio->sub_category_id = $item->vendorCategory[0]->sub_category_id;
				
				if($item->vendorLocation[0]->city_id){
					$city = City::find($item->vendorLocation[0]->city_id);
					$cityName = $city->name;
				}else{
					$cityName = 'Singapore';
				}
							
				$portfolio->city = $cityName;
				$portfolio->title = $faker->name;
				$portfolio->description = $faker->text;
				$portfolio->status = 1;
				$portfolio->vendor_id = $item->id;
				$portfolio->save();

				$i = 0;
				$medias = [
					'/assets/media/vendor.png',
					'/assets/media/vendor1.png',
					'/assets/media/vendor2.png',
				];
				
				while($i < ){
					$portfolioMedia = new PortfolioMedia;

					$portfolioMedia->portfolio_id		= $portfolio->id;
					$portfolioMedia->media_url 			= $medias[$i];
					$portfolioMedia->media_url_thumb		= $medias[$i];
					$portfolioMedia->media_type			= 'IMAGE';
					$portfolioMedia->media_source 		= 'local';
					$portfolioMedia->sorts 				= $i;
					$portfolioMedia->status 				= 1;
					$portfolioMedia->save();

					$i++;
				}
	        }
        }
    }
}
