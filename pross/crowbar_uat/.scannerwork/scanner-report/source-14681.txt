<?php

    namespace App\Http\Middleware;

    use Closure;
    use Auth;
    use Illuminate\Foundation\Auth\ThrottlesLogins;
    use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

    class TalentAuth{
        
        public function handle($request, Closure $next){
            if(Auth::check()){
                if(Auth::user()->type != TALENT_ROLE_TYPE){
                    return redirect(sprintf("/%s",'login'));
                }
                if(Auth::user()->status != 'active'){
                    return redirect(sprintf("/%s",'logout'));
                }
            }else{
                return redirect(sprintf("/%s",'login'));    
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
