<?php

namespace App\Http\Controllers;

use App\System;
use App\Library\Geetest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCaptcha()
    {
        $driver = System::_get('vcode_driver');
        if ($driver === 'code') {
            return response([]);
        } elseif ($driver === 'geetest') {
            return response(Geetest\API::get());
        } elseif ($driver === 'recaptcha') {
            // 未实现
        }
        return response([], Response::HTTP_NOT_IMPLEMENTED);
    }

    function validateCaptcha(Request $request)
    {
        $driver = System::_get('vcode_driver');
        if ($driver === 'code') {
            $this->validate($request, [
                'captcha.key' => 'required|string',
                'captcha.code' => 'required|captcha_api:' . $request->input('captcha.key')
            ]);
        } elseif ($driver === 'geetest') {
            $this->validate($request, [
                'captcha.a' => 'required|string',
                'captcha.b' => 'required|string',
                'captcha.c' => 'required|string',
                'captcha.d' => 'required|string'
            ]);
            if (!Geetest\API::verify(
                $request->input('captcha.a'),
                $request->input('captcha.b'),
                $request->input('captcha.c'),
                $request->input('captcha.d'))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'captcha' => [trans('validation.captcha')],
                ]);
            }
        } elseif ($driver === 'recaptcha') {
            // 未实现
            $this->validate($request, [
                'captcha.t' => 'required|string'
            ]);
        }
    }

    /**
     * 商户: 设置当前登录用户user_id <br>管理员: 设置user_id or 所有
     * @param Request $request
     * @param $model
     * @param string $user_id_key
     * @param string $post_key
     * @return \App\Model_|\Illuminate\Database\Eloquent\Builder|$model
     */
    function authQuery(Request $request, $model, $user_id_key = 'user_id', $post_key = 'user_id')
    {
        /* @var \App\Model_ $model */
        return $model::where($user_id_key, \Auth::id());
    }

    /**
     * 商户: 获取当前登录用户<br>管理员: 获取user_id 且一定存在
     * @param Request $request
     * @param string $post_key
     * @return int
     */
    protected function getUserId(Request $request, $post_key = 'user_id')
    {
        return \Auth::id();
    }

    /**
     * @param Request $request
     * @param string $post_key
     * @return int
     * @throws \Exception
     */
    protected function getUserIdOrFail(Request $request, $post_key = 'user_id')
    {
        $user_id = self::getUserId($request, $post_key);
        if ($user_id) {
            return $user_id;
        } else {
            throw new \Exception('参数缺少 ' . $post_key);
        }
    }


    /**
     * 商户: 获取当前登录用户<br>
     * 管理员: 获取user_id 对应用户 or null
     * @param Request $request
     * @return \App\User|null
     */
    protected function getUser(Request $request)
    {
        return \Auth::getUser();
    }


    protected function checkIsInMaintain()
    {
        if (System::_getInt('maintain') === 1) {
            $maintain_info = System::_get('maintain_info');
            echo view('message', [
                'title' => '维护中',
                'message' => $maintain_info
            ]);
            exit;
        }
    }


    protected function msg($message, $title = null, $exception = null)
    {
        return view('message', ['message' => $message, 'title' => $title, 'exception' => $exception]);
    }
}
