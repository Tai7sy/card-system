<?php

namespace App\Http\Controllers;

use App\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DevController extends Controller
{

    private function check_readable_r($dir)
    {
        if (is_dir($dir)) {
            if (is_readable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::check_readable_r($dir . "/" . $object)) return false;
                        else continue;
                    }
                }
                echo $dir . '   ...... <span style="color: green">R</span><br>';
                return true;
            } else {
                echo $dir . '   ...... <span style="color: red">R</span><br>';
                return false;
            }

        } else if (file_exists($dir)) {
            return (is_readable($dir));
        }
        echo $dir . '   ...... 文件不存在<br>';
        return false;
    }

    private function check_writable_r($dir)
    {
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::check_writable_r($dir . "/" . $object)) return false;
                        else continue;
                    }
                }
                echo $dir . '   ...... <span style="color: green">W</span><br>';
                return true;
            } else {
                echo $dir . '   ...... <span style="color: red">W</span><br>';
                return false;
            }

        } else if (file_exists($dir)) {
            return (is_writable($dir));
        }
        echo $dir . '   ...... 文件不存在<br>';
        return false;
    }

    private function checkPathPermission($path)
    {
        self::check_readable_r($path);
        self::check_writable_r($path);
    }


    public function install()
    {
        $var = [];

        @ob_start();
        self::checkPathPermission(base_path('storage'));
        self::checkPathPermission(base_path('bootstrap/cache'));
        $var['permission'] = @ob_get_clean();


        return view('install', [
            'var' => $var
        ]);
    }


    public function test(Request $request)
    {
    }

    public function test_email()
    {
        if (!config('app.debug')) exit;

        try {
            Mail::to('12840640+Tai7sy@users.noreply.github.com')->Queue(new \App\Mail\AuthRegister());
            return 'success';
        } catch (\Exception $e) {
            return 'error-' . $e;
        }
    }

    public function test_alipay_fund_trans_config_dev()
    {
        if (!config('app.debug')) exit;

        $config = System::_get('alipay_fund_trans_config_dev');
        $config = json_decode($config, true);
        $config['gateway'] = 'https://openapi.alipaydev.com/gateway.do'; //沙箱
        $out_biz_no = date('YmdHis') . random_int(1000, 9999);
        var_dump($out_biz_no);
        $ret = (new \App\Library\AlipayFundTrans\Api())->trans(
            $config,
            $out_biz_no,
            'fygtat8407@sandbox.com',
            '沙箱环境',
            '1',
            '转账备注');


        if ($ret === true) {
            echo '转账成功';
        } else {
            echo '转账失败:' . $ret;
        }

        echo "<br>\n";

        $ret = (new \App\Library\AlipayFundTrans\Api())->verify($config, $out_biz_no);

        if ($ret === true) {
            echo '验证成功';
        } else {
            echo '验证失败:' . $ret;
        }
    }


}
