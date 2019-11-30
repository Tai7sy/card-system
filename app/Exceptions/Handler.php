<?php

namespace App\Exceptions;

use App\Library\Helper;
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
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    private function msg($request, $message, $status)
    {
        return $request->isXmlHttpRequest() ?
            response()->json(['message' => $message], $status) :
            response()->view('errors._', ['code' => $status, 'message' => $message], $status);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->msg($request, '记录未找到', 404);
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->msg($request, '您需要登录您的账户再进行此操作', 401);
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return $this->msg($request, '未授权的操作', 403);
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            // 会显示详细的错误内容
            return parent::render($request, $e);
        } elseif ($e instanceof \Illuminate\Session\TokenMismatchException) { // CSRF
            return $this->msg($request, '请刷新页面后重试', 403);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->msg($request, '页面未找到', 404);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return $this->msg($request, '请求方法不允许', 405);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
            Log::error('Caught a ServiceUnavailableHttpException', ['Exception' => $e]);
            return $this->msg($request, '当前服务不可用，请稍后重试', 503);
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $statusCode = $e->getStatusCode();
            switch ($statusCode) {
                case 429:
                    return $this->msg($request, '您的请求过于频繁，请稍后重试', 429);
                    break;
                default:
                    Log::error('Caught a UnknownHttpException', ['Exception' => $e]);
                    return $this->msg($request, '当前服务不可用，请稍后重试', 500);
            }
        }

        Log::error('Uncaught error', [
            'exception' => $e,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'data' => file_get_contents('php://input'),
            'ip' => Helper::getIP(),
            'client_ip' => $request->getClientIp(),
            'headers' => $request->header()
        ]);

        if (config('app.debug')) {
            return parent::render($request, $e);
        } else {
            return $this->msg($request, '未知错误，请联系客服', 500);
        }
    }
}
