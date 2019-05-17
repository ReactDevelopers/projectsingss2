<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use App\Providers\Request;
use App\Providers\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        if(!request()->is('api/*')){
            $site_currency = \Session::get('site_currency');
            if(empty($site_currency)){
                $defaultCurrency = \Models\Currency::getDefaultCurrency();
                if(!empty($defaultCurrency)){
                    \Session::set('site_currency',$defaultCurrency->iso_code);
                }else{
                    \Session::set('site_currency',DEFAULT_CURRENCY);
                }
            }
        }
        
        $current_url        = explode("/", \URL::current());
        $language           = (!empty($current_url[array_search(DEFAULT_LANGUAGE, $current_url)+1]))?$current_url[array_search(DEFAULT_LANGUAGE, $current_url)+1]:DEFAULT_LANGUAGE;

        \Braintree_Configuration::environment(config('services.braintree.environment'));
        \Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
        \Braintree_Configuration::publicKey(config('services.braintree.public_key'));
        \Braintree_Configuration::privateKey(config('services.braintree.private_key'));
        
        Cashier::useCurrency('HKD', 'HK$');

        /*** CACHING KEYS USED IN APPLICATION ***/
        $configuration_key                  = "configuration";
        $currencies_key                     = "currencies";
        $default_currency_key               = "default_currency";
        $default_language_key               = "default_language";
        $languages_key                      = "languages";
        $phone_codes_key                    = "phone_codes";
        $country_name_key                   = "country_name";
        $state_name_key                     = "state_name";
        $industries_name_key                = "industries_name";
        $subindustries_name_key             = "subindustries_name";
        $degree_name_key                    = "degree_name";
        $countries_key                      = "countries";
        $states_key                         = "states";
        $cities_key                         = "cities";
        $country_phone_codes_key            = "country_phone_codes";
        $work_fields_key                    = "work_fields";
        $phone_codes_key                    = "phone_codes";
        $abusive_words_key                  = "abusive_words";
        $all_industries_name_key            = "all_industries_name";
        $job_titles_key                     = "job_titles";
        $colleges_key                       = "colleges";
        $college_images_key                 = "college_images";
        $companies_key                      = "companies";
        $company_images_key                 = "company_images";
        $skills_key                         = "skills";
        $skills_filter_key                  = "skills_filter";
        $certificates_key                   = "certificates";
        $dispute_concern_key                = "dispute_concern";
        $card_type_key                      = "card_type";


        if(empty(\Cache::get($languages_key))){
            $languages = (array)\App\Lib\Dash::combine(\Models\Listings::languages(),'{n}.language_code','{n}.language_name');
            \Cache::forever($languages_key,$languages);
        }

        if(!in_array($language, array_keys(language()))){
            $language = DEFAULT_LANGUAGE;
        }

        if(empty(\Cache::get($configuration_key))){
            $configuration = (array)\App\Lib\Dash::combine((array)\DB::table('config')->get()->toArray(),'{n}.key','{n}.value');
            \Cache::forever($configuration_key,$configuration);
        }

        if(empty(\Cache::get($currencies_key))){
            $currencies = (array)\App\Lib\Dash::combine((array)\DB::table('currencies')->get()->toArray(),'{n}.iso_code','{n}.sign');
            \Cache::forever($currencies_key,$currencies);
        }

        if(empty(\Cache::get($default_currency_key))){
            $default_currency = \DB::table('currencies')->select(['iso_code'])->where('default_currency','Y')->get()->first();
            \Cache::forever($default_currency_key,$default_currency->iso_code);
        }

        if(empty(\Cache::get($default_language_key))){
            $default_language = \DB::table('languages')->select(['language_code'])->where('is_default','Y')->get()->first();
            \Cache::forever($default_language_key,$default_language->language_code);
        }

        if(empty(\Cache::get($phone_codes_key))){
            $phone_codes = \Models\Listings::countries('array',['phone_country_code'],'status = "active"');
            \Cache::forever($phone_codes_key,$phone_codes);
        }
        
        if(empty(\Cache::get($country_name_key))){
            $country_name = \Models\Listings::countries('array',[\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name")], 'status = "active"');
            \Cache::forever($country_name_key,$country_name);
        }
        
        if(empty(\Cache::get($state_name_key))){
            $state_name = \Models\Listings::states('array',[\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as state_name")], 'status = "active"');
            \Cache::forever($state_name_key,$state_name);
        }
        
        if(empty(\Cache::get($industries_name_key))){
            $industries_name = (array)\App\Lib\Dash::combine(
                \Models\Industries::allindustries(
                    "array",
                    " parent = '0' AND status != 'trashed' ",[
                        'id_industry',
                        \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
                        'parent'
                    ],''
                ),
                '{n}.id_industry',
                '{n}.name'
            );
            \Cache::forever($industries_name_key,$industries_name);
        }
        
        if(empty(\Cache::get($subindustries_name_key))){
            $subindustries_name = (array)\App\Lib\Dash::combine(
                \Models\Industries::allindustries(
                    "array",
                    " parent != '0' ",
                    ['id_industry',\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),'parent']
                ),
                '{n}.name',
                '{n}.name'
            );
            \Cache::forever($subindustries_name_key,$subindustries_name);
        }

        if(empty(\Cache::get($degree_name_key))){
            $degree = (array)\App\Lib\Dash::combine(
                \Models\Listings::degrees(
                    'array',
                    ['id_degree','degree_name']
                ),
                '{n}.id_degree',
                '{n}.degree_name'
            );
            \Cache::forever($degree_name_key,$degree);
        }

        if(empty(\Cache::get($countries_key))){
            $countries = (array)\App\Lib\Dash::combine(
                \Models\Listings::countries(
                    'array',
                    [\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name"),'id_country'],
                    'status = "active"'
                ),
                '{n}.id_country',
                '{n}.country_name'
            );
            
            \Cache::forever($countries_key,$countries);
        }
        
        if(empty(\Cache::get($job_titles_key))){
            $job_titles = (array)\App\Lib\Dash::combine(
                \Models\Listings::job_titles(
                    'array',
                    ['id_job_title','job_title_name']
                ),
                '{n}.job_title_name',
                '{n}.job_title_name'
            );
            
            \Cache::forever($job_titles_key,$job_titles);
        }

        if(empty(\Cache::get($colleges_key))){
            $colleges = (array)\App\Lib\Dash::combine(
                \Models\Listings::colleges(
                    'array',
                    ['id_college','college_name']
                ),
                '{n}.college_name',
                '{n}.college_name'
            );
            
            \Cache::forever($colleges_key,$colleges);

            $college_images = (array)\App\Lib\Dash::combine(
                \Models\Listings::colleges(
                    'array',
                    ['id_college','college_name']
                ),
                '{n}.id_college',
                '{n}.image'
            );
            
            \Cache::forever($college_images_key,$college_images);
        }

        if(empty(\Cache::get($companies_key))){
            $companies = (array)\App\Lib\Dash::combine(
                \Models\Listings::companies(
                    'array',
                    ['id_company','company_name']
                ),
                '{n}.company_name',
                '{n}.company_name'
            );
            
            \Cache::forever($companies_key,$companies);

            $company_images = (array)\App\Lib\Dash::combine(
                \Models\Listings::companies(
                    'array',
                    ['id_company','company_name']
                ),
                '{n}.id_company',
                '{n}.image'
            );
            
            \Cache::forever($company_images_key,$company_images);
        }

        if(empty(\Cache::get($states_key))){
            $states = (array)\App\Lib\Dash::combine(
                \Models\Listings::states(
                    'array',
                    [\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as state_name"),'id_state'],
                    'status = "active"',
                    "state_name ASC"
                ),
                '{n}.id_state',
                '{n}.state_name'
            );
            
            \Cache::forever($states_key,$states);
        }

        if(empty(\Cache::get($card_type_key))){
            $card_type  = (array)\App\Lib\Dash::combine(
                \Models\Listings::card_type(
                    "array",
                    "status='active'",
                    ['id','type', 'name','image']
                ),
                '{n}.type',
                '{n}.name'
            );
            \Cache::forever($card_type_key,$card_type);
        }

        if(empty(\Cache::get($cities_key))){
            $cities = (array)\App\Lib\Dash::combine(
                \Models\Listings::cities(
                    'array',
                    [\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as city_name"),'id_city'],'','',
                    'status = "active"'
                ),
                '{n}.id_city',
                '{n}.city_name'
            );
            
            \Cache::forever($cities_key,$cities);
        }

        if(empty(\Cache::get($country_phone_codes_key))){
            $codes = (array)\App\Lib\Dash::combine(
                \Models\Listings::countries(
                    'array',
                    ['phone_country_code'],
                    'status = "active"'
                ),
                '{n}.phone_country_code',
                '{n}.phone_country_code'
            );
            
            \Cache::forever($country_phone_codes_key,$codes);
        }

        if(empty(\Cache::get($work_fields_key))){
            $work_fields = (array)\App\Lib\Dash::combine(
                \Models\Listings::workfields(
                    'array',
                    ['id_workfield','field_name']
                ),
                '{n}.id_workfield',
                '{n}.field_name'
            );
            
            \Cache::forever($work_fields_key,$work_fields);
        }

        if(empty(\Cache::get($skills_key))){
            $skills = (array)\App\Lib\Dash::combine(
                \Models\Listings::skills(
                    'array',
                    ['id_skill','skill_name']
                ),
                '{n}.skill_name',
                '{n}.skill_name'
            );
            \Cache::forever($skills_key,$skills);
        }

        if(empty(\Cache::get($skills_filter_key))){
            $skill_filter = (array)\App\Lib\Dash::combine(
                \Models\Listings::skills(
                    'array',
                    ['id_skill','skill_name']
                ),
                '{n}.id_skill',
                '{n}.skill_name'
            );
            \Cache::forever($skills_filter_key,$skill_filter);
        }

        if(empty(\Cache::get($certificates_key))){
            $certificates = (array)\App\Lib\Dash::combine(
                \Models\Listings::certificates(
                    'array',
                    ['certificate_name']
                ),
                '{n}.certificate_name',
                '{n}.certificate_name'
            );
            \Cache::forever($certificates_key,$certificates);
        }        

        if(empty(\Cache::get($phone_codes_key))){
            $phone_codes = \Models\Listings::countries(
                'array',
                ['phone_country_code']
            );
            \Cache::forever($phone_codes_key,$phone_codes);
        }

        if(empty(\Cache::get($abusive_words_key))){
            $abusive_words = \Models\Listings::abusive_words('array'," status != 'trashed' ",['abusive_word']);
            
            $all_words = [];
            array_walk($abusive_words, function($item) use(&$all_words){
                $all_words[$item['abusive_word']] = str_repeat("*", strlen($item['abusive_word']));
            });

            \Cache::forever($abusive_words_key,$all_words);
        }
        
        if(empty(\Cache::get($all_industries_name_key))){
            $all_industries_name = (array)\App\Lib\Dash::combine(\Models\Industries::allindustries("array",
                    " parent = '0' AND status != 'trashed' ",[
                        'id_industry',
                        'en as name',
                        'parent'
                    ]
                ),
                '{n}.id_industry',
                '{n}.name'
            );
            \Cache::forever($all_industries_name_key,$all_industries_name);
        }
        
        if(empty(\Cache::get($dispute_concern_key))){
            $dispute_concern    = json_decode(json_encode(
                \Models\DisputeConcern::select([
                    'id_concern',
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as concern")
                ])
                ->whereNotIn('status',['trashed'])
                ->get()
            ),true);
            $dispute_concern = (array)\App\Lib\Dash::combine($dispute_concern,
                '{n}.id_concern',
                '{n}.concern'
            );

            \Cache::forever($dispute_concern_key,$dispute_concern);
        }



        \Validator::extend('validate_date_type', function($attribute, $value, $parameters){
            if(!empty(date_create($parameters['0'])) && !empty($value)){
                $startdate  = date_create($parameters['0']);
                $enddate    = date_create($value);
                $datediff   = date_diff($startdate,$enddate);
                $daycount   = $datediff->format("%a");
                if($parameters['1'] == 'weekly' && $daycount < 7){
                    return false;
                }else if($parameters['1'] == 'monthly' && $daycount < 29){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        });



        \Validator::extend('match_old_password', function($attribute, $value, $parameters){

            if(\Hash::check($value, $parameters[0])){
                return false;
            }else{
                return true;
            }
        });



        \Validator::extend('validate_start_date', function($attribute, $value, $parameters){
            if(!empty($parameters['0']) && !empty($value)){
                if(strtotime($value) < strtotime(date('Y-m-d')) || strtotime($parameters['0']) < strtotime(date('Y-m-d'))){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        });

        \Validator::extend('validate_date', function($attribute, $value, $parameters, $validator){
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            return $d && $d->format('Y-m-d') === $value;
        });        

        \Validator::extend('validate_future_date', function($attribute, $date, $parameters, $validator){
            $date = \DateTime::createFromFormat('Y-m-d', $date);
            
            if(!empty($date->format('Y-m-d'))){
                if(strtotime($date->format('Y-m-d')) <= strtotime(date('Y-m-d'))){
                    return false; 
                }else{
                    return true; 
                }
            }else{
                return true;
            }
        });        

        \Validator::extend('one_hour_difference', function($attribute, $value, $parameters, $validator){

            $start_time = array_get($validator->getData(), $parameters[0], null);
            $end_time = $value;

            if(strtotime($start_time) !== false && strtotime($end_time) !== false){
                if(time_difference_in_hours($start_time,$end_time) >= 1){
                    return true;
                }
            }
        });

        \Validator::extend('invalid_time_range', function($attribute, $value, $parameters, $validator){

            $start_time = array_get($validator->getData(), $parameters[0], null);
            $end_time = $value;

            if(strtotime($start_time) !== false && strtotime($end_time) !== false){
                if(strtotime($start_time) < strtotime($end_time)){
                    return true;
                }
            }
        });

        \Validator::extend('validate_file_type', function($attribute, $file){
            $allowed_documents = explode(",",ALLOWED_DOCUMENTS);
            
            if(in_array($file->getClientOriginalExtension(), $allowed_documents)){
                return true;
            }
        });

        \Validator::extend('validate_image_type', function($attribute, $file){
            $allowed_documents = explode(",",ALLOWED_BANNER_IMAGE);
            
            if(in_array($file->getClientOriginalExtension(), $allowed_documents)){
                return true;
            }
        });

        \Validator::extend('validate_banner_type', function($attribute, $file){
            $allowed_documents = explode(",",ALLOWED_BANNER_IMAGE);
            
            if(in_array($file->getClientOriginalExtension(), $allowed_documents)){
                return true;
            }
        });

        \Validator::extend('min_age', function($attribute, $value, $parameters){
            $min_age = ( ! empty($parameters)) ? (int) $parameters[0] : MIN_AGE;
            $difference = get_time_difference($value,'y');

            return $difference >= $min_age;
        });

        \Validator::extend('numeric_range', function($attribute, $value, $parameters, $validator){
            
            $min    = array_get($validator->getData(), $parameters[0], null);
            $max    = array_get($validator->getData(), $parameters[1], null);
            
            if(!empty($min) && !empty($max)){
                return $min < $max;
            }else{
                return true;
            }
        });

        \Validator::extendImplicit('required_range', function($attribute, $value, $parameters, $validator){
            $field      = (array)array_get($validator->getData(), $parameters[0], null);
            $selected   = (array)explode("-", $parameters[1]);
            
            if(!empty($field)){
                if(array_intersect($selected, $field)){
                    if($value == DEFAULT_NO_VALUE){
                        $value = NULL;
                    }
                    return (!empty($value));
                }else{
                    return true;
                }
            }else{    
                return true;
            }
        });

        \Validator::extend('required_having', function($attribute, $value, $parameters, $validator){

            $required   = array_get($validator->getData(), $parameters[0], null);
            $field      = array_get($validator->getData(), $parameters[1], null);
            $selected   = (array)explode("-", $parameters[1]);
            
            if(!empty($field)){
                if(array_intersect($selected, $field)){
                    return ($required);
                }else{
                    return true;
                }
            }else{    
                return true;
            }

        });

        \Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            return \Hash::check($value, current($parameters));
        });

        \Validator::extend('validate_expiry_month', function($attribute, $expiry_month, $parameters, $validator){
            $expiry_year = array_get($validator->getData(), $parameters[0], null);

            if(!empty($expiry_year) && !empty($expiry_month)){
                if($expiry_year == date('Y') && (int)$expiry_month < (int)date('m')){
                    return false;
                }else{
                    return true;
                }
            }else{
                return true;    
            }
        });

        \Validator::extend('validate_paypal_email', function($attribute, $email){
            $response = validatePayPalEmail2($email);
            if(!empty($response)){
                if($response == 'Failure'){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        });

        \Validator::extend('validate_card_type', function($attribute,$value){
            $card_type = \Models\Listings::card_type("array", "status='active'", ['type'] );
            $card_type = array_column($card_type, 'type');

            if(!in_array($value,$card_type)){
                return false;
            }else{
                return true;
            }
        });     

        \Validator::extend('required_time', function($attribute,$value,$parameters){
            if($parameters[0] == 'hourly' && (empty($value) || $value == '00:00')){
                return false;
            }else{
                return true;
            }
        });        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerUrlGenerator();
    }

    /**
     *  Overwriting laravel urlGenerator
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app) {
            $routes = $app['router']->getRoutes();
            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);
            $url = new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                )
            );
            $url->setSessionResolver(function () {
                return $this->app['session'];
            });
            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });
            return $url;
        });
    }
    /**
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'url',
        ];
    }
}
