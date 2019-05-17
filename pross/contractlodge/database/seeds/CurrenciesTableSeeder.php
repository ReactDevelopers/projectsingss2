<?php

use App\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'name' => 'AED',
                'symbol' => 'د.إ',
                'iso_code' => 'AED'
            ],
            [
                'name' => 'AUD',
                'symbol' => '$',
                'iso_code' => 'AUD'
            ],
            [
                'name' => 'AZN',
                'symbol' => 'ман',
                'iso_code' => 'AZN'
            ],
            [
                'name' => 'BHD',
                'symbol' => 'ب.د',
                'iso_code' => 'BHD'
            ],
            [
                'name' => 'BRL',
                'symbol' => 'R$',
                'iso_code' => 'BRL'
            ],
            [
                'name' => 'CAD',
                'symbol' => '$',
                'iso_code' => 'CAD'
            ],
            [
                'name' => 'CNY',
                'symbol' => '¥',
                'iso_code' => 'CNY'
            ],
            [
                'name' => 'EUR',
                'symbol' => '€',
                'iso_code' => 'EUR'
            ],
            [
                'name' => 'GBP',
                'symbol' => '£',
                'iso_code' => 'GBP'
            ],
            [
                'name' => 'HUF',
                'symbol' => 'Ft',
                'iso_code' => 'HUF'
            ],
            [
                'name' => 'JPY',
                'symbol' => '¥',
                'iso_code' => 'JPY'
            ],
            [
                'name' => 'MXN',
                'symbol' => '$',
                'iso_code' => 'MXN'
            ],
            [
                'name' => 'RUB',
                'symbol' => 'р.',
                'iso_code' => 'RUB'
            ],
            [
                'name' => 'SGD',
                'symbol' => '$',
                'iso_code' => 'SGD'
            ],
            [
                'name' => 'USD',
                'symbol' => '$',
                'iso_code' => 'USD'
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate($currency);
        }
    }
}

