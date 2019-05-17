<?php
namespace App\Http\Middleware;
use Closure;
class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next,$permission_name)
    {

        $permissions = \App\Lib\General::getRolePermissions(\Auth::user()->role_id);
        $p_name = explode(',', $permission_name);
        $has = false;
        foreach ($p_name as  $name) {

            if(in_array($name, $permissions)){
                $has = true;
                break;
            }
        }


        if(!$has) {

          //  throw new \Illuminate\Auth\Access\AuthorizationException('NO Access.');
        }

        return $next($request);
    }
}