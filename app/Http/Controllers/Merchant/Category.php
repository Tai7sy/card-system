<?php

namespace App\Http\Controllers\Merchant;

use App\Library\Helper;
use App\Library\Response;
use App\System;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Category extends Controller
{
    function get(Request $request)
    {
        $page = $request->post('current_page', 1);
        $pageSize = $request->post('per_page', 20);

        $query = $this->authQuery($request, \App\Category::class);

        $searchType = $request->post('search', false);
        $searchVal = $request->post('val', false);
        if ($searchType && $searchVal) {
            if ($searchType == 'simple') {
                return Response::success($query->get(['id', 'name']));
            } elseif ($searchType == 'id') {
                $query->where('id', $searchVal);
            } else {
                $query->where($searchType, 'like', '%' . $searchVal . '%');
            }
        }

        $enabled = $request->post('enabled');
        if (strlen($enabled))
            $query->whereIn('enabled', explode(',', $enabled));

        $list = $query->withCount('products')->orderBy('sort')->paginate($pageSize, ['*'], 'page', $page);

        foreach ($list->items() as $category){
            /* @var \App\Category $category*/
            $category->setAppends(['url']);
        }
        return Response::success($list);
    }

    function sort(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if (!$id) return Response::forbidden();
        /* @var \App\Category $category */
        $category = $this->authQuery($request, \App\Category::class)->findOrFail($id);
        $category->sort = (int)$request->post('sort', 1000);
        $category->save();
        return Response::success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $name = $request->post('name');
        $enabled = (int)$request->post('enabled');
        $sort = $request->post('sort');
        $sort = $sort === NULL ? 1000 : (int)$sort;


        if (System::_getInt('filter_words_open') === 1) {
            $words = explode('|', System::_get('filter_words'));
            if (($ret = Helper::filterWords($name, $words)) !== false)
                return Response::fail('提交失败! 分类名称包含敏感词: ' . $ret);
        }

        if ($sort < 0 || $sort > 1000000)
            return Response::fail('排序需要在0-1000000之间');

        $password = $request->post('password');
        $password_open = $request->post('password_open') === 'true';

        $category = $this->authQuery($request, \App\Category::class)->find($id);
        if (!$category) {
            $category = new \App\Category;
            $category->user_id = $this->getUserIdOrFail($request);
        }
        $category->name = $name;
        $category->sort = $sort;
        $category->password = $password;
        $category->password_open = $password_open;
        $category->enabled = $enabled;
        $category->saveOrFail();
        return Response::success();
    }

    function enable(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();
        $enabled = (int)$request->post('enabled');

        $this->authQuery($request, \App\Category::class)
            ->whereIn('id', explode(',', $ids))
            ->update(['enabled' => $enabled]);

        return Response::success();
    }

    function delete(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $this->authQuery($request, \App\Category::class)
            ->whereIn('id', explode(',', $ids))->delete();
        return Response::success();
    }

}
