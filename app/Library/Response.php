<?php

namespace App\Library;
/**
 * Created by PhpStorm.
 * User: Wind
 * Date: 2017/7/18
 * Time: 下午10:54
 */
class Response
{
    /**
     * Return a new JSON response from the application.
     *
     * @param  string|array $data
     * @param  int $status
     * @param  array $headers
     * @param  int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function Json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return response()
            ->json($data, $status, $headers, $options);
    }

    public static function Ret($code = 0, $msg = 'undefined', $data = [])
    {
        return self::Json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public static function UnAuthorized($msg = 'notLogin', $data = [])
    {
        return self::Json(['code' => 401, 'msg' => $msg, 'data' => $data]);
    }

    public static function Forbidden($msg = 'Forbidden', $data = [])
    {
        return self::Json(['code' => 403, 'msg' => $msg, 'data' => $data]);
    }
}