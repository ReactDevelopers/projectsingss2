<?php

    namespace App\Http\Middleware;

    use Closure;
    use Auth;

    class Webservice{
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next){
            if($request->user()->status == 'trashed' || $request->user()->status == 'inactive'){
                return response()->json([
                    'status' => false,
                    'message' => 'M0048',
                    'error' => 'Unauthorized access for this request.',
                    'error_code' => 'unauthorized',
                    'status_code' => '401'
                ], 401);
            }

            return $next($request);
        }
    }
