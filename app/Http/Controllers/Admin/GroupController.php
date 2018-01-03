<?php

namespace App\Http\Controllers\Admin;

use App\Library\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Group;

class GroupController extends Controller
{
    public function get()
    {
        $list = Group::withCount('goods')->get();
        return Response::Ret(0, 'success', $list);
    }

    public function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $name = $request->post('name');
        $enabled = (int)$request->post('enabled');


        $group = Group::find($id);
        if (!$group) {
            $group = new Group;
        }
        $group->name = $name;
        $group->enabled = $enabled;
        $group->saveOrFail();
        return Response::Ret(0);
    }

    public function enabled(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        $enabled = (int)$request->post('enabled');
        $group = Group::findOrFail($id);
        $group->enabled = $enabled;
        $group->saveOrFail();
        return Response::Ret(0);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        Group::where('id', $id)->delete();
        return Response::Ret(0);
    }

}
