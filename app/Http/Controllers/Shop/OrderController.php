<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Library\Response;

class OrderController extends Controller
{
    public function get(Request $request)
    {
        $email = $request->post('email', '');
        if (!strlen($email)) {
            return Response::Forbidden('请输入收货邮箱');
        }
        $list = Order::where('email', $email)
            ->with(['good' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->select(['id', 'name']);
            }, 'cards' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->select(['order_id', 'card']);
            }])
            ->get(['id', 'order_no', 'good_id', 'email', 'paid', 'count', 'amount']);

        foreach ($list as $order) {
            if ($order->cards && count($order->cards) && count($order->cards) < $order->count) {
                for ($i = count($order->cards); $i < $order->count; $i++) {
                    $order->cards->add($order->cards[0]);
                }
            }
        }


        return Response::Ret(0, 'success', $list);
    }
}
