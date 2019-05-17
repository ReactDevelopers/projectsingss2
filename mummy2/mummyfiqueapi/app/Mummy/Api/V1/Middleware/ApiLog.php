<?php

namespace App\Mummy\Api\V1\Middleware;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;
use Closure;
use Jenssegers\Agent\Facades\Agent;
use Carbon\Carbon;

class ApiLog extends BaseEncrypter
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        //
    ];
    protected $startTime;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->startTime = microtime(true);

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $endTime = microtime(true);
        $duration = $endTime - $this->startTime;
        $userId = isset($request->user()->id) ? $request->user()->id: 0;
        $userToken = $userId ? $request->user()->token()->id: null;

        $agent = new \Jenssegers\Agent\Agent();
        $agentInfo = [
            'device_name' => $agent->device(),
            'platform' => $agent->platform(),
            'version' => $agent->version($agent->platform())
        ];
        $agentInfo = json_encode($agentInfo);

        if($agent->isMobile()){
            $device = 'MOBILE';
        }else{
            $device = 'WEB';
        }
        $platform = $agent->platform();

        if(env('APP_DEBUG')){
            DB::insert('INSERT INTO `api_logs` (method, request_url, request_header,request_string,response_string,user_id,token,request_ip,device_type,duration,platform,agent_info,created_at) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$request->getMethod(), $request->path(),json_encode($request->header()),json_encode($request->all()),$response->getContent(),$userId,$userToken,$request->getClientIp(),$device,$duration,$platform,$agentInfo,Carbon::today()]);
        }
    }
}
