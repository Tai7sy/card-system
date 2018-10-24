<?php

namespace App\Library;

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
    public static function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return response()
            ->json($data, $status, $headers, $options);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [])
    {
        return self::json(['message' => 'success', 'data' => $data]);
    }

    public static function fail($msg = 'fail', $data = [])
    {
        return self::json(['message' => $msg, 'data' => $data], 500);
    }

    public static function forbidden($msg = 'forbidden', $data = [])
    {
        return self::json(['message' => $msg, 'data' => $data], 403);
    }
}