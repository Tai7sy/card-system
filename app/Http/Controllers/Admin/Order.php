<?php

namespace App\Http\Controllers\Admin;

use App\Library\FundHelper;
use App\Library\Helper;
use Carbon\Carbon;
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
        $this->validate($request, [
            'day' => 'required|integer|between:7,30'
        ]);
        $day = (int)$request->input('day');
        if ($day === 30) {
            $before = Carbon::now()->addMonths(-1);
        } else {
            $before = Carbon::now()->addDays(-$day);
        }

        $query = \App\Order::where(function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->where('status', \App\Order::STATUS_PAID)
                ->orWhere('status', \App\Order::STATUS_SUCCESS);
        })->where('paid_at', '>=', Helper::getMysqlDate(-$day + 1))
            ->groupBy('date')->orderBy('date', 'DESC');

        $query->selectRaw('DATE(`paid_at`) as "date",COUNT(*) as "count",SUM(`paid`) as "paid",SUM(`fee`-`system_fee`) as "profit"');

        $list = $query->get()->toArray();
        $ret = [];
        foreach ($list as $item) {
            $ret[$item['date']] = [(int)$item['count'], (int)$item['paid'], (int)$item['profit']];
        }

        return Response::success($ret);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|string',
            'income' => 'required|integer',
            'balance' => 'required|integer',
        ]);
        $ids = $request->post('ids');
        $delete_income = (int)$request->post('income');
        $delete_balance = (int)$request->post('balance');

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
        $this->validate($request, [
            'ids' => 'required|string'
        ]);

        $ids = explode(',', $request->post('ids'));
        $frozen_reason = $request->post('reason');

        $count = 0;
        $success = 0;
        foreach ($ids as $order_id) {
            $count++;

            if (FundHelper::orderFreeze($order_id, $frozen_reason)) {
                $success++;
            }
        }
        return Response::success([$count, $success]);
    }

    function unfreeze(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|string'
        ]);

        $ids = explode(',', $request->post('ids'));

        $count = 0;
        $success = 0;
        $outerStatus = \App\Order::STATUS_FROZEN;
        foreach ($ids as $order_id) {
            $count++;

            if (FundHelper::orderUnfreeze($order_id, '后台操作', null, $outerStatus)) {
                $success++;
            }
        }
        return Response::success([$count, $success, $outerStatus]);
    }

    function set_paid(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = $request->post('id', '');

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

        // 发货OK了. 重新获取一下订单状态
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
