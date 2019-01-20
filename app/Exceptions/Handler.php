<?php

namespace App\Exceptions;

use App\Library\Response;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;

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
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $request->expectsJson() ?
                response()->json(['message' => '记录未找到'], 404) :
                response()->view('errors._', ['code' => 404, 'message' => '记录未找到'], 404);
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return $request->expectsJson() ?
                response()->json(['message' => '您需要登录您的账户再进行此操作'], 401) :
                response()->view('errors._', ['code' => 401, 'message' => '您需要登录您的账户再进行此操作'], 401);
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return $request->expectsJson() ?
                response()->json(['message' => '未授权的操作'], 403) :
                response()->view('errors._', ['code' => 403, 'message' => '未授权的操作'], 403);
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            return parent::render($request, $e);
        } elseif ($e instanceof \Illuminate\Session\TokenMismatchException) { // CSRF
            return $request->expectsJson() ?
                response()->json(['message' => '请刷新页面后重试'], 403) :
                response()->view('errors._', ['code' => 403, 'message' => '请刷新页面后重试'], 403);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $request->expectsJson() ?
                response()->json(['message' => '页面未找到'], 404) :
                response()->view('errors._', ['code' => 404, 'message' => '页面未找到'], 404);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return $request->expectsJson() ?
                response()->json(['message' => '请求方法不允许'], 405) :
                response()->view('errors._', ['code' => 405, 'message' => '请求方法不允许'], 405);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
            Log::error('Caught a ServiceUnavailableHttpException', ['Exception' => $e]);
            return $request->expectsJson() ?
                response()->json(['message' => '当前服务不可用，请稍后重试'], 503) :
                response()->view('errors._', ['code' => 503, 'message' => '当前服务不可用，请稍后重试'], 503);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $statusCode = $request->expectsJson();
            switch ($statusCode){
                case 429:
                    return $request->expectsJson() ?
                        response()->json(['message' => '您的请求过于频繁，请稍后重试'], 429) :
                        response()->view('errors._', ['code' => 429, 'message' => '您的请求过于频繁，请稍后重试'], 429);
                    break;
                default:
                    Log::error('Caught a UnknownHttpException', ['Exception' => $e]);
                    return $request->expectsJson() ?
                        response()->json(['message' => '当前服务不可用，请稍后重试'], $statusCode) :
                        response()->view('errors._', ['code' => $statusCode, 'message' => '当前服务不可用，请稍后重试'], $statusCode);
            }
        }

        Log::error('Uncaught Exception', ['Exception' => $e]);

        if (config('app.debug')) {
            return parent::render($request, $e);
        } else {
            return $request->expectsJson() ?
                response()->json(['message' => '未知错误，请联系客服'], 500) :
                response()->view('errors._', ['code' => 500, 'message' => '未知错误，请联系客服'], 500);
        }
    }
}
