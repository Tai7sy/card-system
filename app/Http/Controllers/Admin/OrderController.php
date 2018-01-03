<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Library\Response;

class OrderController extends Controller
{
    public function get(Request $request)
    {
        $pageSize = $request->post('size', 20);
        $page = $request->post('page', 1);

        $list = Order::with(['pay', 'good', 'cards' => function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->select(['order_id', 'card']);
        }])->paginate($pageSize, ['*'], 'page', $page);


        foreach ($list->items() as $order) {
            if ($order->cards && count($order->cards) && count($order->cards) < $order->count) {
                for ($i = count($order->cards); $i < $order->count; $i++) {
                    $order->cards->add($order->cards[0]);
                }
            }
        }

        return Response::Ret(0, 'success', $list);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        $order = Order::find($id);
        if ($order) {
            $order->cards()->detach();
            $order->delete();
        }
        return Response::Ret(0);
    }
}
