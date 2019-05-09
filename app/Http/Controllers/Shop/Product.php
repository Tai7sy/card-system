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
            /** @var \App\Product $product */
            $product->setForShop();
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
