<?php

namespace App\Http\Controllers\Merchant;

use App\Library\Response;
use App\System;
use function GuzzleHttp\Psr7\mimetype_from_filename;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class File extends Controller
{
    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $user_id
     * @param string $folder
     * @param bool $return_file
     * @return \Illuminate\Http\JsonResponse | array
     */
    public static function uploadImg($file, $user_id, $folder, $return_file = false)
    {
        try {
            $ext = $file->extension();
        } catch (\Throwable $e) {
            return Response::fail($e->getMessage()); // 这里可能扩展没安装, 给出提示
        }

        if (!$file || !in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
            return Response::fail('图片错误, 系统支持jpg/png/gif格式');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return Response::fail('图片不能大于5MB');
        }

        try {
            $path = $file->store($folder, [
                'disk' => System::_get('storage_driver')
            ]);
        } catch (\Exception $e) {
            \Log::error('File.uploadImg folder:' . $folder . ', error:' . $e->getMessage(), ['exception' => $e]);
            if (config('app.debug')) {
                return Response::fail($e->getMessage());
            } else {
                return Response::fail('上传文件失败, 内部错误, 请联系客服');
            }
        }
        if (!$path) {
            return Response::fail('系统保存文件出错, 请稍后再试');
        }

        $driver = System::_get('storage_driver');
        $url = Storage::disk($driver)->url($path);

        $file_id = \App\File::insertGetId([
            'user_id' => $user_id,
            'driver' => $driver,
            'path' => $path,
            'url' => $url
        ]);

        if ($file_id < 1) {
            Storage::disk($driver)->delete($path);
            return Response::fail('数据库繁忙，请稍后再试');
        }

        $ret = [
            'id' => $file_id,
            'url' => $url,
            'name' => pathinfo($path, PATHINFO_BASENAME)
        ];

        if ($return_file) {
            return $ret;
        }

        return Response::success($ret);
    }

    /**
     * 商户商铺公告 or 商户商品介绍
     * 用户上传 or 管理员后台代为上传, 因此有记录 user_id
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    function upload_merchant(Request $request)
    {
        $user = $this->getUser($request);
        if ($user === null)
            return Response::forbidden('无效的用户');

        $file = $request->file('file');
        return $this->uploadImg($file, $user->id, \App\File::getProductFolder());
    }

    /**
     * 这个接口应该只有 driver 是 local 的时候会用到
     * @param Request $request
     * @param $relative_path
     * @return \Illuminate\Http\Response
     */
    public function renderImage(Request $request, $relative_path)
    {
        if (str_contains($relative_path, '..') ||
            str_contains($relative_path, './') ||
            str_contains($relative_path, '.\\') ||
            !starts_with($relative_path, 'images/')) {
            $fileBin = file_get_contents(public_path('images/illegal.jpg'));
        } else {
            $relative_path = str_replace('\\', '/', $relative_path);
            $file = \App\File::wherePath($relative_path)->first();
            if ($file) {
                $driver = $file->driver;
            } else {
                $driver = System::_get('storage_driver');
            }
            if (!in_array($driver, ['local', 's3', 'oss', 'qiniu'])) {
                return response()->view('message', ['title' => '404', 'message' => '404 Driver NotFound'], 404);
            }
            try {
                $fileBin = Storage::disk($driver)->get($relative_path);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                \Log::error('File.renderImage error: ' . $e->getMessage(), ['exception' => $e]);
                return response()->view('message', ['title' => '404', 'message' => '404 NotFound'], 404);
            }
        }
        ob_end_clean();
        header('Content-Type: ' . mimetype_from_filename($relative_path));
        die($fileBin);
    }
}
