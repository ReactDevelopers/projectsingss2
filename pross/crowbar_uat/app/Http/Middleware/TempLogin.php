<?php

    namespace App\Http\Middleware;

    use Closure;

    class TempLogin{
        
        public function handle($request, Closure $next){
            /*if(\Cache::get('configuration')['site_environment'] == 'development' && app()->environment() !== 'local'){
                $pages = [
                    url('currency-exchange'),
                    url('activate/account'),
                    url('create/account'),
                    url('page/terms-and-conditions'),
                    url('page/privacy-policy'),
                ];

                if(!in_array(asset(\Request::path()), $pages)){
                    if(\Session::get('temp-login') != 'authenticated'){
                        return redirect('templogin?redirect='.base64_encode($request->fullUrl()));
                    }
                }
            }*/

            return $next($request);
        }
    }