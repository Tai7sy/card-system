<?php

namespace App\Http\Controllers\Shop;

use App\Category;
use App\Product;
use App\Library\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Coupon extends Controller
{
    function info(Request $request)
    {
        $category_id = (int)$request->post('category_id', -1);
        $product_id = (int)$request->post('product_id', -1);
        $coupon = $request->post('coupon');
        if (!$coupon) return Response::fail('请输入优惠券');


        if ($category_id > 0) {
            /* @var \App\Category $category */
            $category = Category::findOrFail($category_id);
            $user_id = $category->user_id;
        } elseif ($product_id > 0) {
            /* @var \App\Product $product */
            $product = Product::findOrFail($product_id);
            $user_id = $product->user_id;
        } else {
            return Response::fail('请先选择分类或商品');
        }


        $coupons = \App\Coupon::where('user_id', $user_id)
            ->where('coupon', $coupon)
            ->where('expire_at', '>', Carbon::now())
            ->whereRaw('`count_used`<`count_all`')->get();
        foreach ($coupons as $coupon) {
            if ($coupon->category_id === -1 ||
                ($coupon->category_id === $category_id &&
                    ($coupon->product_id === -1 || $coupon->product_id === $product_id))) {

                $coupon->setVisible(['discount_type', 'discount_val']);
                return Response::success($coupon);
            }
        }
        return Response::fail('您输入的优惠券信息无效<br>如果没有优惠券请不要填写');
    }
}
