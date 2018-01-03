<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pay;
use App\Library\Response;

class PayController extends Controller
{
    public function get()
    {
        $list = Pay::get();
        return Response::Ret(0, 'success', $list);
    }

    public function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $name = $request->post('name');
        $img = $request->post('img');
        $comment = $request->post('comment');
        $driver = $request->post('driver');
        $way = $request->post('way');
        $config = $request->post('config');
        $enabled = (int)$request->post('enabled');


        $pay = Pay::find($id);
        if (!$pay) {
            $pay = new Pay;
        }
        $pay->name = $name;
        $pay->img = $img;
        $pay->comment = $comment;
        $pay->driver = $driver;
        $pay->way = $way;
        $pay->config = $config;
        $pay->enabled = $enabled;
        $pay->saveOrFail();
        return Response::Ret(0);
    }

    public function enabled(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        $enabled = (int)$request->post('enabled');

        /** @var \App\Pay $pay */
        $pay = Pay::findOrFail($id);
        $pay->enabled = $enabled;
        $pay->saveOrFail();
        return Response::Ret(0);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        Pay::where('id', $id)->delete();
        return Response::Ret(0);
    }
}