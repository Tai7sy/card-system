<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;

class Product extends Controller
{
    function get(Request $request)
    {
        $category_id = (int)$request->post('category_id');
        if (!$category_id) return Response::forbidden('请选择商品分类');

        $category = \App\Category::where('id', $category_id)
            ->first();
        if (!$category) return Response::forbidden('商品分类未找到');

        if ($category->password_open && $request->post('password') !== $category->password) {
            return Response::fail('分类密码输入错误');
        }

        $products = \App\Product::where('category_id', $category_id)
            ->where('enabled', 1)
            ->orderBy('sort')
            ->get();
        foreach ($products as $product) {
            $count = count($product->cards) ? intval($product->cards[0]->count) : 0;
            if ($category->user->shop_inventory == \App\User::inventory_range) {
                if ($count <= 0) {
                    $count2 = '不足';
                } elseif ($count <= 10) {
                    $count2 = '少量';
                } elseif ($count <= 20) {
                    $count2 = '一般';
                } else {
                    $count2 = '大量';
                }
                $product->setAttribute('count2', $count2);
            }else{
                $product->setAttribute('count2', $count); // 页面展示的数量
            }
            $product->setAttribute('count', $count); // 真正的数量
            $product->setVisible(['id', 'name', 'description', 'count', 'count2', 'buy_min', 'buy_max',
                'support_coupon', 'password_open', 'price', 'price_whole']);
        }
        return Response::success($products);
    }

    function verifyPassword(Request $request)
    {
        $product_id = (int)$request->post('product_id');
        if (!$product_id) return Response::forbidden('请选择商品');

        $product = \App\Product::where('id', $product_id)->first();
        if (!$product) return Response::forbidden('商品未找到');

        if ($product->password_open && $request->post('password') !== $product->password) {
            return Response::fail('商品密码输入错误');
        }
        return Response::success();
    }

}
