<?php

    namespace App\Console\Commands;
    use Illuminate\Console\Command;

    class CurrencyConversion extends Command{
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'currencyconversion';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Fetch currency conversion rate from currencylayer api every day.';

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle(){            
            $currencies         = \DB::table('currencies')->select(['id','iso_code','sign'])->get();
            $amount             = urlencode(1);
            $default_currency   = \Models\Currency::getDefaultCurrency();

            if(!empty($default_currency)){
                $base_currency  = urlencode($default_currency->iso_code);
            }else{
                $base_currency  = DEFAULT_CURRENCY;
            }

            /* We are using https://currencylayer.com/ for currency conversion */

            /* To get access key signup and goto following url to get your access key */
            /* https://currencylayer.com/quickstart */

            // Initialize 
            $endpoint = 'live';
            $access_key = '2fad19b76aa01a81e29de387322eab47';

            /*New Conversion*/
            foreach ($currencies as $item) {
                // nitialize CURL:
                $ch = curl_init('https://apilayer.net/api/convert?access_key='.$access_key.'&from='.$item->iso_code.'&to='.$base_currency.'&amount=1');   
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // get the (still encoded) JSON data:
                $json = curl_exec($ch);
                curl_close($ch);

                // Decode JSON response:
                $conversionResult = json_decode($json, true);

                if($conversionResult['success']){
                    $isUpdated = \DB::table('currencies')
                    ->where('id',$item->id)
                    ->update([
                        'conversion_rate' => @(float)___round($conversionResult['result']),
                        'updated' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $cronRecord = [];
            $cronRecord = [
                'name'       => 'Currency Conversion cron',
                'status'     => 'active',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $responce = \DB::table('cron_log')->insertGetId($cronRecord);
            /*New Conversion*/

            /*
            $from = 'SGD';
            $to = 'INR';
            $amount = 1;

            // nitialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&from='.$from.'&to='.$to.'&amount='.$amount.'');   
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // get the (still encoded) JSON data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $conversionResult = json_decode($json, true);

            if($conversionResult['success']){

                $cronRecord = [];
                $cronRecord = [
                    'name'       => 'Currency Conversion cron',
                    'status'     => 'active',
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $responce = \DB::table('cron_log')->insertGetId($cronRecord);

                foreach ($currencies as $item) {
                    
                    $isUpdated = \DB::table('currencies')
                    ->where('id',$item->id)
                    ->update([
                        'conversion_rate' => @(float)___round($conversionResult['quotes'][$from.$item->iso_code]),
                        'updated' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            */          

        }
    }