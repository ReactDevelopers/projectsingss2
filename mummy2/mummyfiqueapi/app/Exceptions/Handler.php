<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
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
        if ($request->expectsJson()) {
            $message = $exception->getMessage();
            $code = $exception->getCode();

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
                $message = 'Too many requests. Slow your roll!';
                $code = $exception->getCode();
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $message = 'Not Found.';
                $code = Response::HTTP_NOT_FOUND;
            }

            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $message = 'Not Found.';
                $code = Response::HTTP_NOT_FOUND;
            }
            if ($exception instanceof ValidationException) {
                $message = 'Not Found.';
                $code = Response::HTTP_BAD_REQUEST;
            }

            return response()->json([
                'success' => false,
                'error' => [
                    'description' => $message,
                    'error_code' => $code,
                    'error_messages' => $exception->getTrace()
                ]
            ], Response::HTTP_OK);
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
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 500);
        }

        return redirect()->guest(route('login'));
    }
}
