<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('future_date', function ($attribute, $value, $parameters) {
            $date = Carbon::createFromFormat('d/m/Y', $value)->format('m/d/Y');
            return strtotime($date) >= strtotime('now');
        });

        Validator::extend('after_date', function ($attribute, $value, $parameters, $validator) {
            $end_date = Carbon::createFromFormat('d/m/Y', $value)->format('m/d/Y');
            $start_date_param = array_get($validator->getData(), $parameters[0], null);
            $start_date = Carbon::createFromFormat('d/m/Y', $start_date_param)->format('m/d/Y');

            return strtotime($start_date) < strtotime($end_date);
        });

        Validator::extend('currency', function($attribute, $value, $parameters) {
            return preg_match("/^\d+(\.\d{1,2})?$/", $value);
        });
    }
}
