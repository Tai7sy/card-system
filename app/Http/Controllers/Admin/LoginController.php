<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        if (\App\User::count() == 0) {
            return Response::Ret(-1, '您还未安装本程序，请输入 域名/install 进行安装');
        }
        $remember = $request->input('remember') == 'true';
        if (Auth::attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password')], $remember)
        ) {
            return Response::Ret(0, 'ok');
        } else {
            return Response::Ret(-1, '用户名或密码错误, 请检查');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        Auth::logout();
        return Response::ret(0, 'ok');
    }


    public function change(Request $request)
    {
        $newPassword = $request->post('password');
        $user = Auth::getUser();
        $user->password = bcrypt($newPassword);
        $user->saveOrFail();
        Response::Ret(0);
    }
}
