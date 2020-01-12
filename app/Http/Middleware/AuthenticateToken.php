<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


/**
 * 虽然jwt-auth可以配置provide实现全自动刷新token, 但是我们自己实现一个,
 * 因为需要根据remember_token(token生效时间)来决定token是否已经失效
 * Class AuthenticateToken
 * @package App\Http\Middleware
 */
// 注意，我们要继承的是 jwt 的 BaseMiddleware
class AuthenticateToken extends BaseMiddleware
{

    private function checkTokenTime()
    {
        /** @var \App\User $user */
        $user = $this->auth->user();
        $issuedAtTime = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->get('iat')->getValue();
        // $issuedAtTime = $this->auth->getClaim('iat'); // 会校验是否认证通过, (现在是有可能刷新过token, 旧的已经是blacklist了)
        // Log::error($request->url() . ' $issuedAtTime: ' . $issuedAtTime . ', remember_token: ' . $user->remember_token);
        if ($issuedAtTime >= $user->remember_token) {
            // remember_token 储存上一次 修改密码/退出 的时间, 用于校验是否token已经失效
            return true;
        }
        return false;
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if (is_string($request->query('token'))) {
            $request->headers->set('Authorization', $request->query('token'));
        }

        $this->checkForToken($request);

        // 使用 try 包裹，以捕捉 token 过期所抛出的 TokenExpiredException  异常
        try {
            // https://github.com/tymondesigns/jwt-auth/issues/1263#issuecomment-318414278
            // 检测用户的登录状态，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                if ($this->checkTokenTime()) {
                    return $next($request);
                } else {
                    throw new UnauthorizedHttpException('jwt-auth', 'Token invalid');
                }
            } else {
                throw new  UnauthorizedHttpException('jwt-auth', 'User not found');
            }
        } catch (TokenExpiredException $exception) {
            // Log::debug('AuthenticateToken.TokenExpiredException: try refresh', ['exception' => $exception]);
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
            try {
                // 刷新用户的 token
                $token = $this->auth->refresh(); // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9xxx
                // 使用一次性登录以保证此次请求的成功
                Auth::onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->get('sub')->getValue());
                // Auth::onceUsingId($this->auth->payload()->get('sub')); // payload() 会校验是否认证通过, 没法用, 现在是不通过的状态(TokenExpiredException)

                if ($this->checkTokenTime()) {
                    return $this->setAuthenticationHeader($next($request), $token);
                } else {
                    throw new UnauthorizedHttpException('jwt-auth', 'Token invalid');
                }
            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                // Log::debug('AuthenticateToken.TokenExpiredException.JWTException: refresh failed', ['exception' => $exception]);
                throw new  UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        } catch (JWTException $exception) {
            // 在请求头里面没找到 token 之类的异常
            throw new  UnauthorizedHttpException('jwt-auth', $exception->getMessage());
        }
    }
}