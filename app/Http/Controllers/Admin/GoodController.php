<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Good;
use App\Library\Response;

class GoodController extends Controller
{
    public function get(Request $request)
    {
        $pageSize = $request->post('size', 20);
        $page = $request->post('page', 1);

        $list = Good::with(['group' => function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->select(['id', 'name']);
        }])->withCount('cards')->paginate($pageSize, ['*'], 'page', $page);
        return Response::Ret(0, 'success', $list);
    }
    public function edit(Request $request)
    {
        $id = (int)$request->post('id', -1);
        $group_id = (int)$request->post('group_id');
        $name = $request->post('name');
        $description = $request->post('description');

        $all_count = (int)$request->post('all_count');
        $price = (int)$request->post('price');
        $enabled = (int)$request->post('enabled');

        $good = Good::find($id);
        if (!$good) {
            $good = new Good;
            $good->sold_count = 0;
        }
        $good->group_id = $group_id;
        $good->name = $name;
        $good->description = $description;
        $good->all_count = $all_count;
        $good->price = $price;
        $good->enabled = $enabled;
        $good->saveOrFail();
        return Response::Ret(0);
    }

    public function enabled(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        $enabled = (int)$request->post('enabled');
        $good = Good::findOrFail($id);
        $good->enabled = $enabled;
        $good->saveOrFail();
        return Response::Ret(0);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        Good::where('id', $id)->delete();
        return Response::Ret(0);
    }

}
