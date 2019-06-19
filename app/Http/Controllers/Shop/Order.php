<?php

namespace App\Http\Controllers\Shop;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;
use App\Library\Geetest;
use Illuminate\Support\Facades\Cookie;

class Order extends Controller
{
    function get(Request $request)
    {
        if ((int)\App\System::_get('vcode_shop_search') === 1) {
            $ret = Geetest\API::verify($request->post('geetest_challenge'),
                $request->post('geetest_validate'),
                $request->post('geetest_seccode'));
            if (!$ret) {
                return Response::fail('系统无法接受您的验证结果，请刷新页面后重试。');
            }
        }

        $query = \App\Order::where('created_at', '>=', (new Carbon)->addDay(-30));


        $type = $request->post('type', '');
        if ($type === 'cookie') {
            $customer = Cookie::get('customer');
            if (strlen($customer) !== 32)
                return Response::success();
            $query->where('customer', $customer);
        } elseif ($type === 'order_no') {
            $order_no = $request->post('order_no', '');
            if (strlen($order_no) !== 19)
                return Response::success();
            $query->where('order_no', $order_no);
        } elseif ($type === 'contact') {
            $contact = $request->post('contact', '');
            if (strlen($contact) < 6)
                return Response::success();
            $query->where('contact', $contact);
        } else {
            return Response::fail('请选择查询类型');
        }


        $columns = ['id', 'created_at', 'order_no', 'contact', 'status', 'send_status', 'count', 'paid'];
        // 这里不要显示商品名称
        if (1) {
            $columns[] = 'product_name';
            $columns[] = 'contact';
            $columns[] = 'contact_ext';
        }

        $list = $query->orderBy('id', 'DESC')
            ->get($columns);

        $showMsg = '';
        return Response::success([
            'list' => $list,
            'msg' => count($list) ? $showMsg : ''
        ]);
    }
}
