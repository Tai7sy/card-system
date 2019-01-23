<?php

namespace App\Http\Controllers\Merchant;

use App\Library\Helper;
use App\Library\Response;
use App\System;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use voku\helper\AntiXSS;

class Product extends Controller
{

    function get(Request $request)
    {
        $query = $this->authQuery($request, \App\Product::class)
            ->with(['category' => function (Relation $query) {
                $query->select(['id', 'name', 'password_open']);
            }])->with(['cards' => function (Relation $query) {
                $query->whereRaw('`count_all`>`count_sold`')
                    ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                    ->groupBy('product_id');
            }]);

        $searchType = $request->post('search', false);
        $searchVal = $request->post('val', false);
        if ($searchType && $searchVal) {
            if ($searchType == 'simple') {
                return Response::success($query->where('category_id', $searchVal)->get(['id', 'name']));
            } elseif ($searchType == 'id') {
                $query->where('id', $searchVal);
            } else {
                $query->where($searchType, 'like', '%' . $searchVal . '%');
            }
        }

        $category_id = (int)$request->post('category_id');
        if ($category_id > 0) $query->where('category_id', $category_id);

        $enabled = $request->post('enabled');
        if (strlen($enabled))
            $query->whereIn('enabled', explode(',', $enabled));

        $page = $request->post('current_page', 1);
        $pageSize = $request->post('per_page', 20);
        $list = $query->orderBy('sort')->paginate($pageSize, ['*'], 'page', $page);

        foreach ($list->items() as $product) {
            /* @var \App\Product $product */
            $product->setAppends(['count', 'url']);
        }
        return Response::success($list);
    }

    function sort(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if (!$id) return Response::forbidden();
        /* @var \App\Product $product */
        $product = $this->authQuery($request, \App\Product::class)->findOrFail($id);
        $product->sort = (int)$request->post('sort', 1000);
        $product->save();
        return Response::success();
    }

    function category_edit(Request $request)
    {
        $id = (int)$request->post('id');
        $category_id = (int)$request->post('category_id');

        if (!$id || !$category_id) return Response::forbidden();
        /* @var \App\Product $product */
        $product = $this->authQuery($request, \App\Product::class)->findOrFail($id);
        $product->category_id = $category_id;
        $product->save();
        return Response::success();
    }

    function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $category_id = (int)$request->post('category_id');
        $name = $request->post('name');
        $description = $request->post('description');
        $instructions = $request->post('instructions');
        $sort = $request->post('sort');
        $sort = $sort === NULL ? 1000 : (int)$sort;

        $count_warn = (int)$request->post('count_warn', 0);
        $buy_min = (int)$request->post('buy_min', 0);
        $buy_max = (int)$request->post('buy_max', 0);

        $support_coupon = $request->post('support_coupon', 0) === 'true';
        $password = $request->post('password');
        $password_open = $request->post('password_open', 0) === 'true';

        $cost = (int)round($request->post('cost') * 100);
        $price = (int)round($request->post('price') * 100);
        $price_whole = $request->post('price_whole'); // [1:]
        $enabled = (int)$request->post('enabled');


        if (System::_getInt('filter_words_open') === 1) {
            $words = explode('|', System::_get('filter_words'));
            if (($ret = Helper::filterWords($name, $words)) !== false)
                return Response::fail('提交失败! 商品名称包含敏感词: ' . $ret);
            if (($ret = Helper::filterWords($description, $words)) !== false)
                return Response::fail('提交失败! 商品描述包含敏感词: ' . $ret);
            if (($ret = Helper::filterWords($instructions, $words)) !== false)
                return Response::fail('提交失败! 商品使用说明包含敏感词: ' . $ret);
        }


        if ($buy_min < 1 || $buy_min > 10000)
            return Response::fail('最小购买量不能超过10000');
        if ($buy_max < 1 || $buy_max > 10000)
            return Response::fail('最大购买量不能超过10000');

        if ($count_warn < 0 || $count_warn > 10000000)
            return Response::fail('库存预警需要在0-10000000之间');

        if ($sort < 0 || $sort > 10000000)
            return Response::fail('排序需要在0-10000000之间');

        if ($cost > 1000000000 || $price > 1000000000) {
            return Response::fail('商品价格不能超过10000000, 请重新输入');
        }
        if ($cost < 0 || $price < 0) {
            return Response::fail('价格不能为负数');
        }

        $product = $this->authQuery($request, \App\Product::class)->find($id);
        if (!$product) {
            $product = new \App\Product;
            $product->count_sold = 0;
            $product->user_id = $this->getUserIdOrFail($request);
        } else {
            if (\App\Card::whereProductId($product->id)
                ->where('type', \App\Card::TYPE_REPEAT)
                ->whereRaw('`count_all`>`count_sold`')
                ->exists()) {
                if ($buy_min !== 1) return Response::fail('该商品含有重复售卖的卡密, 最小购买量必须为1件');
                if ($buy_max !== 1) return Response::fail('该商品含有重复售卖的卡密, 最大购买量必须为1件');
            }
        }
        $product->category_id = $category_id;
        $product->name = $name;

        $antiXss = new AntiXSS();
        $product->description = $antiXss->xss_clean($description);
        $product->instructions = $antiXss->xss_clean($instructions);
        $product->sort = $sort;

        $product->buy_min = $buy_min;
        $product->buy_max = $buy_max;
        $product->count_warn = $count_warn;

        $product->support_coupon = $support_coupon;
        $product->password = $password;
        $product->password_open = $password_open;

        $product->cost = $cost;
        $product->price = $price;
        $product->price_whole = $price_whole;
        $product->enabled = $enabled;
        $product->saveOrFail();
        return Response::success();
    }

    function enable(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();
        $enabled = (int)$request->post('enabled');

        $this->authQuery($request, \App\Product::class)
            ->whereIn('id', explode(',', $ids))
            ->update(['enabled' => $enabled]);

        return Response::success();
    }

    function delete(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $this->authQuery($request, \App\Product::class)
            ->whereIn('id', explode(',', $ids))->delete();
        return Response::success();
    }
}
