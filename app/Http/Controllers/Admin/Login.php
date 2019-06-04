<?php

namespace App\Http\Controllers\Admin;

use App\Library\Helper;
use App\Library\Geetest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{
    function getVerify()
    {
        $verify = [
            'driver' => 'geetest',
            'geetest' => Geetest\API::get()
        ];
        return Response::success($verify);
    }

    public function login(Request $request)
    {
        if ((int)\App\System::_get('vcode_login') === 1) {
            $ret = Geetest\API::verify($request->post('gt_c'),
                $request->post('gt_v'),
                $request->post('gt_s'));
            if (!$ret) {
                return Response::fail('滑动验证超时，请返回页面重试。');
            }
        }

        $remember = $request->input('remember') == 'true';
        if (!Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')], $remember)
        ) {
            return Response::fail('邮箱或密码错误, 请检查');
        } else {
            $log = new \App\Log;
            $log->user_id = Auth::Id();
            $log->ip = Helper::getIP();
            $log->action = \App\Log::ACTION_LOGIN;
            $log->save();
            $user = Auth::getUser();
            $user->addHidden(['created_at', 'updated_at']);
            return Response::success([
                'token' => 'login',
                'user' => $user->toArray()
            ]);
        }
    }

    function info()
    {
        $user = Auth::getUser();
        $user->addHidden(['created_at', 'updated_at']);
        $user->setAttribute('shop_name', \App\System::_get('app_name'));
        return Response::success([
            'token' => 'login',
            'user' => $user->toArray()
        ]);
    }

    function logout(Request $request)
    {
        //$request->session()->flush();
        //$request->session()->regenerate();
        @Auth::logout();
        return Response::success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function changePassword(Request $request)
    {
        $user = Auth::getUser();
        $old = $request->post('old', null);
        $password = $request->post('password', null);
        if (strlen($password) < 6 || strlen($password) > 32)
            return Response::fail('请输入6-32位密码');

        if (!Hash::check($old, $user->password))
            return Response::fail('旧密码错误，请检查');


        $user->password = Hash::make($password);
        $user->setRememberToken(str_random(60));
        $user->saveOrFail();

        event(new PasswordReset($user));

        Auth::guard()->login($user);

        return Response::success();
    }
}
