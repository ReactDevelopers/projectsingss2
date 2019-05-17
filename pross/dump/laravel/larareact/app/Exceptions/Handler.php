<?php

namespace App\Exceptions;

use App;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use League\OAuth2\Server\Exception\OAuthServerException;
use GuzzleHttp\Exception\RequestException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $data = (object)[]; 

        if ($request->ajax()) {
            return response()->json(['error' => $exception->getMessage()], 200);
        }

        if($exception instanceof OAuthServerException){
            return response()->json([
               'data' => $data,
               'status' => 'failed',
               'statusCode' => 400,
               'message' => 'Auth Exceptions.',
               'error' => 'Bad Request.',
               'error_code' => 'exception_found',
               'exception' => class_basename( $exception ).' in '.basename($exception->getFile()).' line '.$exception->getLine().': ' .$exception->getMessage(),
            ],400);
        }

        if($exception instanceof RequestException){
            return response()->json([
               'data' => $data,
               'status' => 'failed',
               'statusCode' => (int)$exception->getResponse()->getStatusCode(),
               'message' => $exception->getResponse()->getReasonPhrase(),
               'error' => 'Bad Request.',
               'error_code' => 'exception_found',
               'exception' => class_basename( $exception ).' in '.basename($exception->getFile()).' line '.$exception->getLine().': ' .$exception->getMessage(),
            ],(int)$exception->getResponse()->getStatusCode());
        }

        if($this->isHttpException($exception)) {
            
            /*if($exception instanceof MethodNotAllowedHttpException){
                dd('sd');
            }*/
            switch ($exception->getStatusCode()) {
                case '403':
                    return \Response::view('errors.403');
                    break;

                case '404':
                    if ($request->is('api*')) {
                        return response()->json([
                           'data' => $data,
                           'status' => 'failed',
                           'statusCode' => 404,
                           'message' => 'Someting wrong, please try after some time.',
                           'error' => 'API endpoint not found.',
                           'error_code' => 'exception_found',
                           'exception' => class_basename( $exception ).' in '.basename($exception->getFile()).' line '.$exception->getLine().': ' .$exception->getMessage(),
                        ],404);

                    }
                    return \Response::view('errors.404');
                    break;

                case '500':
                    if ($request->is('api*')) {
                        return response()->json([
                            'data' => $data,
                            'status' => 'failed',
                            'statusCode' => 500,
                            'error' => 'Someting wrong, please try after some time.',
                            'error_code' => 'exception_found',
                            'exception' => class_basename( $exception ).' in '.basename($exception->getFile()).' line '.$exception->getLine().': ' .$exception->getMessage(),
                        ],500);
                    }
                    return \Response::view('errors.500');
                    break;

                case '405':
                    if ($request->is('api*')) {
                        return response()->json([
                            'data' => $data,
                            'status' => 'failed',
                            'statusCode' => 405,
                            'error' => 'Method nor allowed.',
                            'error_code' => 'exception_found',
                            'exception' => class_basename( $exception ).' in '.basename($exception->getFile()).' line '.$exception->getLine().': ' .$exception->getMessage(),
                        ],500);
                    }
                    return \Response::view('errors.404');
                    break;

                default:
                    return $this->renderHttpException($exception);
                    break;
            }
        }
        
        if ($exception instanceof \Illuminate\Session\TokenMismatchException){
            return redirect()->back()->withInput($request->except('_token'));
        }

        if ($exception instanceof Exception && !($exception instanceof AuthenticationException)) {
            $errorExceptionMessage = sprintf('%s in %s line %s : %s',class_basename($exception),basename($exception->getFile()),$exception->getLine(),$exception->getMessage());
            if ($request->is('api*')) {
                return response()->json([
                    'data' => $data,
                    'status' => 'failed',
                    'statusCode' => 500,
                    'error' => 'Someting wrong, please try after some time.',
                    'error_code' => 'exception_found',
                    'exception' => $errorExceptionMessage,
                ],500);
            }  
            else
            {
                $environmentsArr = ['production'];
                if (config('app.debug') && (in_array(config('app.env'),$environmentsArr)) ) {
                    // die($errorExceptionMessage);
                }
            }
        }
        
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $data = (object)[];
        $locale = App::getLocale();
        if ($request->expectsJson()) {
            return response()->json(['statusCode' => 401,'message' => trans('errorcode.401'), "data"=>$data], 401);
        }
        $guard = array_get($exception->guards(), 0);
        
        switch ($guard) {
            case 'admin':
                $login = 'admin.login';
                break;

            case 'api':
                return response()->json(['statusCode' => 401,'message' => trans('errorcode.401'), "data"=>$data], 401);
                break;

            case 'api-v1':
                return response()->json(['statusCode' => 401,'message' => trans('errorcode.401'), "data"=>$data], 401);
                break;
                
            default:
                $login = 'user.login';
                break;
        }
        
        return redirect()->guest(route($login));
    }

    /**
     * Render an exception using Whoops.
     * 
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        $environmentsArr = ['development','local'];
        if (config('app.debug') && (in_array(env('APP_ENV'),$environmentsArr)) ) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

            return response()->make(
                $whoops->handleException($e),
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
                method_exists($e, 'getHeaders') ? $e->getHeaders() : []
            );
        }

        return parent::convertExceptionToResponse($e);
    }
}
