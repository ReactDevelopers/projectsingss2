<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Foundation\Auth\ThrottlesLogins;
    use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

    class AdminAuth{
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next){
            if(\Auth::guard('admin')->check()){
                if(\Auth::guard('admin')->user()->type != 'administrator' && \Auth::guard('admin')->user()->type != 'superadmin' && \Auth::guard('admin')->user()->type != 'sub-admin'){
                    return redirect(sprintf("%s/%s",ADMIN_FOLDER,'login'));
                }

                \Config::set('constants.URI_PLACEHOLDER',\Auth::guard('admin')->user()->type);
            }else{
                return redirect(sprintf("%s/%s",ADMIN_FOLDER,'login'));
            }

            return $next($request);
        }

        protected function validator(array $data) {
            $validator = Validator::make($data, [
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);
            
            return $validator;
        }
    }
