<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Good;
use App\Library\Response;
use Illuminate\Support\Facades\DB;

class GoodController extends Controller
{
    public function getByGroup(Request $request)
    {
        $group_id = $request->post('group_id', -1);
        if ($group_id === -1) {
            return Response::Forbidden();
        }

        $list = Good::where('group_id', $group_id)->where('enabled', 1)
            ->get(['id', 'group_id', 'name', 'description', 'price', DB::raw('`all_count`-`sold_count` as available') ]);
        return Response::Ret(0, 'success', $list);
    }

    public function getInfo(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        $good = Good::where('id', $id)->select(['id', 'name', 'description', 'price'])->firstOrFail();
        return Response::Ret(0, 'success', $good);
    }

}
