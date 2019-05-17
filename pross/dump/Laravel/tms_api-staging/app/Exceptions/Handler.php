<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Exceptions\FileHeaderNotMatchException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Debug\Exception\FlattenException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
       
        if($e instanceof ValidationException){

            
            return response()->json([
                'message'=>'Invalid Request',
                'status'=>false,
                'error_code'=> 422,
                'errors'=> $e->getResponse()->getData(),
                'data'=>[]
            ], 422);
        }
        
        else if($e instanceof FileHeaderNotMatchException) {

           return response()->json([

                'message'=> 'File Header does not match.',
                'status'=> false,
                'error_code'=> 'FHNM',
                'errors'=> [],
                'data'=> $e->getResponse(),    	
           ], 500);

        }

       else if($e instanceof FlattenException || $e instanceof FatalThrowableError){ 

            $message = class_basename( $e ).' in '.basename($e->getFile()).' line '.$e->getLine().': ' .$e->getMessage();
            //echo $message; exit;
            return response()->json([

                'message'=> $message,
                'status'=> false,
                'error_code'=> 500,
                'errors'=> [],
                'data'=> [],     
           ], 500);

        }
        return parent::render($request, $e);
    }
}
