<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Library\QQWry\QQWry;
use App\Library\WeChat;
use App\System;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    use ThrottlesLogins;

    // 在 ThrottlesLogin 里面用到作为限制的
    protected function username()
    {
        return 'email';
    }

    /**
     * @param Request $request
     * @param string $role
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function login(Request $request, $role)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($role === 'merchant') {
            if (System::_getInt('vcode_login') === 1) {
                $this->validateCaptcha($request);
            }
        } else {
            if (System::_getInt('vcode_login_admin') === 1) {
                $this->validateCaptcha($request);
            }
        }


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );
            return response(['message' => trans('auth.throttle', ['seconds' => $seconds])], Response::HTTP_BAD_REQUEST);
        }

        if (($token = Auth::attempt($request->only('email', 'password')))) {
            /** @var \App\User $user */
            $user = Auth::getUser();

            if ($user->status === \App\User::STATUS_FROZEN) {
                Auth::logout();
                return response(['message' => strlen($user->frozen_reason) ? $user->frozen_reason : '您的账户已被冻结'], Response::HTTP_BAD_REQUEST);
            }

            if ($user->role !== $role) {
                goto LOGIN_FAILED;
            }

            $ip = $request->ip();
            $user->logs()->create([
                'action' => \App\Log::ACTION_LOGIN,
                'ip' => $ip,
                'address' => (new QQWry)->getLocation($ip)
            ]);

            return response($this->getUserInfo()->getContent(), Response::HTTP_CREATED, [
                'Authorization' => 'Bearer ' . $token
            ]);
        }

        LOGIN_FAILED:
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return response(['message' => trans('auth.failed')], Response::HTTP_BAD_REQUEST);
    }

    public function loginMerchant(Request $request)
    {
        return $this->login($request, 'merchant');
    }

    public function loginAdmin(Request $request)
    {
        return $this->login($request, 'admin');
    }

    public function getUserInfo()
    {
        /** @var \App\User $user */
        $user = Auth::getUser();
        $user->addHidden(['created_at', 'updated_at']);
        $user->append(['last_login_at']);

        $response = [];
        if ($user->isAdmin()) {
            $response['permissions'] = empty(@$user->api_config['permissions']) ? [] : $user->api_config['permissions'];
        } else {
            $user->setAttribute('verify', $user->verify);
        }
        $response['user'] = $user;

        return response($response);
    }

    public function logout()
    {
        try {
            @Auth::logout();
        } catch (\Throwable $e) {
        }
        return response([]);
    }
}
