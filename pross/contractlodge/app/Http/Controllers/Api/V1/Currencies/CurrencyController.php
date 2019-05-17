<?php

namespace App\Http\Controllers\Api\V1\Currencies;

use Fixerio;
use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    /**
     * Get the exchange rate of one currency to another (by 3-letter name)
     *
     * @param  string  $from_currency
     * @param  string  $to_currency
     * @return float
     */
    public function exchange($from_currency, $to_currency)
    {
        $convertedRates = Fixerio::convert(
            [
                'from' => $from_currency,
                'to' => $to_currency,
                'amount' => 1.00, // amount static added
            ]
        );

        return $convertedRates['result'];
    }

    /**
     * Get all the supported currencies
     * @return Response JSON structure
     */
    public function index()
    {
        return response()->json(Currency::all());
    }
}
