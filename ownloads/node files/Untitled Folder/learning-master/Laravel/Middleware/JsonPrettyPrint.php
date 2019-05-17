<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;

/**
* Class PrettyPrintMiddleware
* @package App\Http\Middleware
*/
class JsonPrettyPrint
{
    /**
    * @var string the query parameter
    */
    const QUERY_PARAMETER = 'pretty';

    /**
    * Apply pretty print if designated
    * 
    * @param $request
    * @param \Closure $next
    * @return mixed
    */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            if ($request->query(self::QUERY_PARAMETER) != 'false') {
                $response->setEncodingOptions(JSON_PRETTY_PRINT);
            }
        }

        return $response;
    }
}