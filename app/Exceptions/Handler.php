<?php

namespace App\Exceptions;

use App\Library\Response;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return mixed
     */
    public function render($request, Exception $e)
    {
        if ($request->expectsJson()) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return Response::json(['message' => '记录未找到'], 404);
            } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return Response::json(['message' => '请登录'], 401);
            } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return Response::json(['message' => '未授权的操作'], 403);
            } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                return parent::render($request, $e);
            }
        } else {
            if ($e instanceof \Illuminate\Session\TokenMismatchException) { // CSRF
                return response()->view('errors.419', ['exception' => null], 419);
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->view('errors.404', ['exception' => null], 404);
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return response()->view('errors.404', ['exception' => null], 404);
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
                return response()->view('errors.503', ['exception' => null], 503);
            }
        }
        \Log::error('Uncaught Exception', ['Exception' => $e]);

        if (config('app.debug')) {
            return parent::render($request, $e);
        } else {
            return $request->expectsJson() ?
                response()->json(['message' => '未知错误，请联系客服'], 500) :
                response()->view('errors.500', [], 500);
        }
    }
}
