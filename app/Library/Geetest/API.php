<?php

namespace App\Library\Geetest;

use Illuminate\Support\Facades\Session;


class API
{
    private $geetest_conf = null;

    public function __construct($config)
    {
        $this->geetest_conf = $config;
    }

    public static function get()
    {
        $id = config('services.geetest.id');
        $key = config('services.geetest.key');
        if (!strlen($id) || !strlen($key)) {
            return null;
        }
        $GtSdk = new Lib($id, $key);
        $user_id = time() . rand(1, 10000);
        $status = $GtSdk->pre_process($user_id);
        $ret = json_decode($GtSdk->get_response_str());


        Session::put('gt_server', $status);
        Session::put('gt_user_id', $user_id);
        /*session_start();
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $user_id;*/
        return $ret;
    }

    public static function verify($geetest_challenge, $geetest_validate, $geetest_seccode)
    {
        $GtSdk = new Lib(config('services.geetest.id'), config('services.geetest.key'));
        $user_id = Session::get('gt_user_id');// session_start(); $_SESSION['gt_user_id'];
        if (Session::get('gt_server') == 1) {
            $result = $GtSdk->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode, $user_id);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($GtSdk->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode)) {
                return true;
            } else {
                return false;
            }
        }
    }
}