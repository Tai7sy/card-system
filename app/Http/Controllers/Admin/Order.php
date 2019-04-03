<?php

namespace App\Http\Controllers\Admin;

use App\Library\FundHelper;
use App\Library\Helper;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Controller
{
    function stat(Request $request)
    {
        $day = (int)$request->input('day', 7);
        $profit = $request->post('profit') === 'true';
        $query = \App\Order::where(function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->where('status', \App\Order::STATUS_PAID)
                ->orWhere('status', \App\Order::STATUS_SUCCESS);
        })->where('paid_at', '>=', Helper::getMysqlDate(-$day + 1))
            ->groupBy('date')->orderBy('date', 'DESC');

        if ($profit) {
            $query->selectRaw('DATE(`paid_at`) as "date",COUNT(*) as "count",SUM(`fee`-`system_fee`) as "sum"');
        } else {
            $query->selectRaw('DATE(`paid_at`) as "date",COUNT(*) as "count",SUM(`paid`) as "sum"');
        }

        $list = $query->get()->toArray();
        $ret = [];
        foreach ($list as $item) {
            $ret[$item['date']] = [(int)$item['count'], (int)$item['sum']];
        }

        return Response::success($ret);
    }

    public function delete(Request $request)
    {
        $ids = $request->post('ids', '');
        $delete_income = (int)$request->post('income');
        $delete_balance = (int)$request->post('balance');

        if (strlen($ids) < 1) return Response::forbidden();

        \App\Order::whereIn('id', explode(',', $ids))
            ->chunk(100, function ($orders) use ($delete_income, $delete_balance) {
                /** @var \App\Order[] $orders */
                foreach ($orders as $order) {
                    $order->cards()->detach();
                    try {
                        if ($delete_income) $order->fundRecord()->delete();
                        if ($delete_balance) {
                            /* @var \App\User $user */
                            $user = \App\User::lockForUpdate()->firstOrFail();
                            $user->m_all -= $order->income;
                            $user->saveOrFail();
                        }
                        $order->delete();
                    } catch (\Exception $e) {
                    }
                }
            });
        return Response::success();
    }

    function freeze(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $frozen_reason = $request->post('reason');

        $orders = \App\Order::whereIn('id', explode(',', $ids))
            ->where('status', \App\Order::STATUS_SUCCESS)->get();

        $count = 0;
        $success = 0;
        foreach ($orders as $order) {
            if (FundHelper::orderFreeze($order, $frozen_reason)) {
                $success++;
            }
            $count++;
        }
        return Response::success([$count, $success]);
    }

    function unfreeze(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $orders = \App\Order::whereIn('id', explode(',', $ids))
            ->where('status', \App\Order::STATUS_FROZEN)->get();

        $count = 0;
        $success = 0;
        $outerStatus = \App\Order::STATUS_FROZEN;
        foreach ($orders as $order) {
            if (FundHelper::orderUnfreeze($order, '后台操作', null, $outerStatus)) {
                $success++;
            }
            $count++;
        }
        return Response::success([$count, $success, $outerStatus]);
    }

    function set_paid(Request $request)
    {
        $id = (int)$request->post('id', '');
        if ($id < 1) return Response::forbidden();

        $trade_no = $request->post('trade_no', '');
        if (strlen($trade_no) < 1) return Response::forbidden('请输入支付系统内单号');

        /**
         * @var \App\Order $order
         */
        $order = \App\Order::findOrFail($id);
        if ($order->status !== \App\Order::STATUS_UNPAY) {
            return Response::forbidden('只能操作未支付订单');
        }

        $logTag = 'Admin.SetPaid';
        $order_no = $order->order_no;
        $amount = $order->paid;
        try {
            Log::debug($logTag . " shipOrder start, order_no: $order_no, amount: $amount, trade_no: $trade_no");
            (new \App\Http\Controllers\Shop\Pay)->shipOrder($request, $order_no, $amount, $trade_no);
            Log::debug($logTag . ' shipOrder end, order_no: ' . $order_no);
            $success = true;
            $msg = '发货成功';
        } catch (\Exception $e) {
            $success = false;
            $msg = $e->getMessage();
            Log::error($logTag . ' shipOrder Exception: ' . $e->getMessage());
        }

        $order = \App\Order::with(['card_orders.card' => function (Relation $query) {
            $query->select(['id', 'card']);
        }])->findOrFail($id);

        if ($order->status === \App\Order::STATUS_PAID) {
            // 已付款未发货
            $success = false;
            $msg = '已标记为付款成功, 但是买家库存不足, 发货失败, 请稍后尝试手动发货';
        }

        return Response::success([
            'code' => $success ? 0 : -1,
            'msg' => $msg,
            'order' => $order
        ]);
    }
}
