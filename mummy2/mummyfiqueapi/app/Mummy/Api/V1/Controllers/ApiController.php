<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host=API_HOST,
 *   basePath="/api",
 *   @SWG\Info(
 *     title="Mummy API",
 *     version="1.0.0"
 *   ),
 *   @SWG\SecurityScheme(
 *   securityDefinition="api_key",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization"
 *  )
 * )
 */
class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
