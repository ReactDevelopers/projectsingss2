<?php

use App\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                'iso_code' => 'ABW',
                'name' => 'Aruba',
            ],
            [
                'iso_code' => 'ALB',
                'name' => 'Albania',
            ],
            [
                'iso_code' => 'AND',
                'name' => 'Andorra',
            ],
            [
                'iso_code' => 'ARE',
                'name' => 'United Arab Emirates',
            ],
            [
                'iso_code' => 'ARG',
                'name' => 'Argentina',
            ],
            [
                'iso_code' => 'ARM',
                'name' => 'Armenia',
            ],
            [
                'iso_code' => 'AUS',
                'name' => 'Australia',
            ],
            [
                'iso_code' => 'AUT',
                'name' => 'Austria',
            ],
            [
                'iso_code' => 'AZE',
                'name' => 'Azerbaijan',
            ],
            [
                'iso_code' => 'BEL',
                'name' => 'Belgium',
            ],
            [
                'iso_code' => 'BGR',
                'name' => 'Bulgaria',
            ],
            [
                'iso_code' => 'BHR',
                'name' => 'Bahrain',
            ],
            [
                'iso_code' => 'BIH',
                'name' => 'Bosnia and Herzegovina',
            ],
            [
                'iso_code' => 'BLZ',
                'name' => 'Belize',
            ],
            [
                'iso_code' => 'BMU',
                'name' => 'Burmuda',
            ],
            [
                'iso_code' => 'BRA',
                'name' => 'Brazil',
            ],
            [
                'iso_code' => 'BTN',
                'name' => 'Bhutan',
            ],
            [
                'iso_code' => 'CAN',
                'name' => 'Canada',
            ],
            [
                'iso_code' => 'CHE',
                'name' => 'Switzerland',
            ],
            [
                'iso_code' => 'CHL',
                'name' => 'Chile',
            ],
            [
                'iso_code' => 'CHN',
                'name' => 'China',
            ],
            [
                'iso_code' => 'COL',
                'name' => 'Colombia',
            ],
            [
                'iso_code' => 'CYM',
                'name' => 'Cayman Islands',
            ],
            [
                'iso_code' => 'CYP',
                'name' => 'Cyprus',
            ],
            [
                'iso_code' => 'CZE',
                'name' => 'Czech Republic',
            ],
            [
                'iso_code' => 'DEU',
                'name' => 'Germany',
            ],
            [
                'iso_code' => 'DMA',
                'name' => 'Dominica',
            ],
            [
                'iso_code' => 'DNK',
                'name' => 'Denmark',
            ],
            [
                'iso_code' => 'EGY',
                'name' => 'Egypt',
            ],
            [
                'iso_code' => 'ESP',
                'name' => 'Spain',
            ],
            [
                'iso_code' => 'EST',
                'name' => 'Estonia',
            ],
            [
                'iso_code' => 'FIN',
                'name' => 'Finland',
            ],
            [
                'iso_code' => 'FRA',
                'name' => 'France',
            ],
            [
                'iso_code' => 'GBR',
                'name' => 'United Kingdom',
            ],
            [
                'iso_code' => 'GGY',
                'name' => 'Guernsey',
            ],
            [
                'iso_code' => 'GRC',
                'name' => 'Greece',
            ],
            [
                'iso_code' => 'GRD',
                'name' => 'Grenada',
            ],
            [
                'iso_code' => 'HKG',
                'name' => 'Hong Kong',
            ],
            [
                'iso_code' => 'HRV',
                'name' => 'Croatia',
            ],
            [
                'iso_code' => 'HUN',
                'name' => 'Hungary',
            ],
            [
                'iso_code' => 'IDN',
                'name' => 'Indonesia',
            ],
            [
                'iso_code' => 'IMN',
                'name' => 'Isle of Man',
            ],
            [
                'iso_code' => 'IND',
                'name' => 'India',
            ],
            [
                'iso_code' => 'IRL',
                'name' => 'Ireland',
            ],
            [
                'iso_code' => 'IRN',
                'name' => 'Iran',
            ],
            [
                'iso_code' => 'ISL',
                'name' => 'Iceland',
            ],
            [
                'iso_code' => 'ISR',
                'name' => 'Israel',
            ],
            [
                'iso_code' => 'ITA',
                'name' => 'Italy',
            ],
            [
                'iso_code' => 'JEY',
                'name' => 'Jersey',
            ],
            [
                'iso_code' => 'JOR',
                'name' => 'Jordan',
            ],
            [
                'iso_code' => 'JPN',
                'name' => 'Japan',
            ],
            [
                'iso_code' => 'KEN',
                'name' => 'Kenya',
            ],
            [
                'iso_code' => 'KOR',
                'name' => 'Republic of Korea',
            ],
            [
                'iso_code' => 'LBN',
                'name' => 'Lebanon',
            ],
            [
                'iso_code' => 'LBR',
                'name' => 'Republic of Liberia',
            ],
            [
                'iso_code' => 'LIE',
                'name' => 'Liechtenstein',
            ],
            [
                'iso_code' => 'LKA',
                'name' => 'Sri Lanka',
            ],
            [
                'iso_code' => 'LTU',
                'name' => 'Lithuania',
            ],
            [
                'iso_code' => 'LUX',
                'name' => 'Luxembourg',
            ],
            [
                'iso_code' => 'LVA',
                'name' => 'Latvia',
            ],
            [
                'iso_code' => 'MAR',
                'name' => 'Morocco',
            ],
            [
                'iso_code' => 'MCO',
                'name' => 'Monaco',
            ],
            [
                'iso_code' => 'MEX',
                'name' => 'Mexico',
            ],
            [
                'iso_code' => 'MKD',
                'name' => 'The former Yugoslav Republic of Macedoni',
            ],
            [
                'iso_code' => 'MLT',
                'name' => 'Malta',
            ],
            [
                'iso_code' => 'MNE',
                'name' => 'Montenegro',
            ],
            [
                'iso_code' => 'MNG',
                'name' => 'Mongolia',
            ],
            [
                'iso_code' => 'MUS',
                'name' => 'Mauritius',
            ],
            [
                'iso_code' => 'MYS',
                'name' => 'Malaysia',
            ],
            [
                'iso_code' => 'NGA',
                'name' => 'Nigeria',
            ],
            [
                'iso_code' => 'NLD',
                'name' => 'Netherlands',
            ],
            [
                'iso_code' => 'NOR',
                'name' => 'Norway',
            ],
            [
                'iso_code' => 'NPL',
                'name' => 'Nepal',
            ],
            [
                'iso_code' => 'NZL',
                'name' => 'New Zealand',
            ],
            [
                'iso_code' => 'OMN',
                'name' => 'Oman',
            ],
            [
                'iso_code' => 'PAK',
                'name' => 'Pakistan',
            ],
            [
                'iso_code' => 'PAN',
                'name' => 'Panama',
            ],
            [
                'iso_code' => 'PER',
                'name' => 'Peru',
            ],
            [
                'iso_code' => 'PHL',
                'name' => 'Philippines',
            ],
            [
                'iso_code' => 'POL',
                'name' => 'Poland',
            ],
            [
                'iso_code' => 'PRK',
                'name' => 'Democratic People\'s Republic of Korea',
            ],
            [
                'iso_code' => 'PRT',
                'name' => 'Portugal',
            ],
            [
                'iso_code' => 'QAT',
                'name' => 'Qatar',
            ],
            [
                'iso_code' => 'ROU',
                'name' => 'Romania',
            ],
            [
                'iso_code' => 'RUS',
                'name' => 'Russian Federation',
            ],
            [
                'iso_code' => 'SAU',
                'name' => 'Saudi Arabia',
            ],
            [
                'iso_code' => 'SGP',
                'name' => 'Singapore',
            ],
            [
                'iso_code' => 'SRB',
                'name' => 'Serbia',
            ],
            [
                'iso_code' => 'SVK',
                'name' => 'Slovakia',
            ],
            [
                'iso_code' => 'SVN',
                'name' => 'Slovenia',
            ],
            [
                'iso_code' => 'SWE',
                'name' => 'Sweden',
            ],
            [
                'iso_code' => 'SYC',
                'name' => 'Seychelles',
            ],
            [
                'iso_code' => 'THA',
                'name' => 'Thailand',
            ],
            [
                'iso_code' => 'TUR',
                'name' => 'Turkey',
            ],
            [
                'iso_code' => 'UKR',
                'name' => 'Ukraine',
            ],
            [
                'iso_code' => 'URY',
                'name' => 'Uruguay',
            ],
            [
                'iso_code' => 'USA',
                'name' => 'United States of America',
            ],
            [
                'iso_code' => 'VEN',
                'name' => 'Venezuela',
            ],
            [
                'iso_code' => 'VGB',
                'name' => 'British Virgin Islands',
            ],
            [
                'iso_code' => 'VNM',
                'name' => 'Viet Nam',
            ],
            [
                'iso_code' => 'ZAF',
                'name' => 'South Africa',
            ],
            [
                'iso_code' => 'FIN',
                'name' => 'Finland',
            ],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate($country);
        }
    }
}
