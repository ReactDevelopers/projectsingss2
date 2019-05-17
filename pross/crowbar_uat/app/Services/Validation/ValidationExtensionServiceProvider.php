<?php

    namespace App\Services\Validation;

    use Illuminate\Support\ServiceProvider;

    class ValidationExtensionServiceProvider extends ServiceProvider{
        /**
         * Bootstrap the application services.
         *
         * @return void
         */
        public function boot(){
            $this->app->validator->resolver( function( $translator, $data, $rules, $messages = array(), $customAttributes = array() ) {
                return new ValidatorExtended( $translator, $data, $rules, $messages, $customAttributes );
            });
        }

        /**
         * Register the application services.
         *
         * @return void
         */
        public function register(){}
    }
