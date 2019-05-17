<?php
    
    namespace Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Currency extends Model{
        protected $table = 'currencies';

        /**
         * [This method is used to getCurrencyList]
         * @param  null
         * @return Data Response
         */

        public static function getCurrencyList($keys = NULL){
            $language = \App::getLocale();


            $currencies = \DB::table('currencies');
            
            if(empty($keys)){
                $currencies->select(
                'currencies.*',
                 \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name")
                );    
            }else{
                $currencies->select($keys);
            }

            $currencies->leftjoin("countries","countries.id_country", "=", "currencies.id_country");
            $currencies->where('currencies.status','!=','deleted');
            return $currencies->get();
        }

        /**
         * [This method is used to save currency] 
         * @param [Array] $answerArr [Used for answer]
         * @return Boolean 
         */

        public static function saveCurrency($answerArr)
        {
            DB::table('currencies')->insert($answerArr);
        }

         /**
         * [This method is used for changes] 
         *@param [Integer] $id_curr,$data[Used for curriculum id]
         *@param [Varchar] $data[Used for data]
         * @return Boolean
         */

        public static function change($id_curr,$data){
            $isUpdated = false;
            $table_users = DB::table('currencies');

            if(!empty($data)){
                $table_users->where('id','=',$id_curr);
                $isUpdated = $table_users->update($data);
            }
            $cache_key  = ['currencies'];
            forget_cache($cache_key);
            return (bool)$isUpdated;
        }

         /**
         * [This method is used to get getCurrencyByCountryId description] 
         * @param [Integer] $id_country [Used for country id]
         * @return Data Response
         */

        public static function getCurrencyByCountryId($id_country){
            $id_country = ($id_country ? $id_country : 208);
            $currency = Currency::select(['id','sign','iso_code','conversion_rate'])->where('id_country',$id_country)->first();
            if(empty($currency)){
                $currency = self::getDefaultCurrency();
            }
            return $currency;
        }

        /**
         * [This method is used to getCurrencyByCountryCode description]
         * @param  [Varchar] $country_code [Used for country code]
         * @return Data Response
         */

        public static function getCurrencyByCountryCode($country_code){
        	$currency = Currency::select(['currencies.id','sign','countries.iso_code','conversion_rate'])
            ->join('countries','countries.id_country','=','currencies.id_country')
            ->where('countries.iso_code',$country_code)
            ->first();

            if(empty($currency)){
                $currency = self::getDefaultCurrency();
            }
            
            return $currency;
        }

        /**
         * [This method is used to getCurrencyById description]
         * @param  [Varchar] $iso_code [Used for iso code]
         * @return Data Response
         */

        public static function getCurrencyById($id_currency){
        	$currency = Currency::select(['id','sign','iso_code','id_country','conversion_rate','status'])->where('id',$id_currency)->first();
            if(empty($currency)){
                $currency = self::getDefaultCurrency();
            }
            return $currency;
        }
        
        /**
         * [This method is used to getCurrencyByISOCode description]
         * @param  [Varchar] $iso_code [Used for iso code]
         * @return Data Response
         */
        
        public static function getCurrencyByISOCode($iso_code){
            $currency = Currency::select(['id','sign','iso_code','conversion_rate'])->where('iso_code',$iso_code)->first();
            if(empty($currency)){
                $currency = self::getDefaultCurrency();
            }
            return $currency;
        }

        /**
         * [This method is used to getShopCurrency description]
         * @param [Integer] $user_id [Used for user id]
         * @return Data Response
         */
        
        public static function getShopCurrency($id_user){
        	$currency = Currency::select(['currency.id','currency.iso_code','currency.sign','currency.conversion_rate'])->join('shop','currency.id_country','=','shop.id_country')->where(['shop.id_user'=>$id_user])->first();
        	if(empty($currency)){
        		$currency = self::getDefaultCurrency();
        	}
        	return $currency;
        }

        /**
         * [This method is used to getDefaultCurrency description]
         * @param null 
         * @return Data Response
         */
        
        public static function getDefaultCurrency(){
            return Currency::select(['id','sign','iso_code','conversion_rate'])->where('default_currency','Y')->first();
        }

        /**
        * [This method is used for Return price converted]
        * @param [Float ]$price[Used for Product price]
        * @param [Object|array] $currency [Current currency object]
        * @param [Boolean]$to_currency[convert to currency or from currency to default currency]
        * @return float Price
        */

        public static function convertPrice($price, $currency = null, $to_currency = true){
            $currency  = self::getCurrencyByCountryCode($currency);
            
            $defaultCurrency = self::getDefaultCurrency();
            $default_currency = $defaultCurrency->id;
            
            $c_id = $currency->id;
            $c_rate = $currency->conversion_rate;

            if ($c_id != $default_currency) {
                if ($to_currency) {
                    $price *= $c_rate;
                } else {
                    $price /= $c_rate;
                }
            }

            return $price;
        }
    }
