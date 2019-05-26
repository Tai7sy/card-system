<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Shop\ApiPay;
use App\Library\CurlRequest;
use App\Library\FundHelper;
use App\Library\Helper;
use App\Library\Response;
use App\Mail\OrderShipped;
use App\System;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Order extends Controller
{
    function get(Request $request)
    {
        $query = $this->authQuery($request, \App\Order::class);

        $category_id = (int)$request->post('category_id');
        $product_id = (int)$request->post('product_id');

        $only_profit = (int)$request->post('profit');
        $is_api = $category_id === \App\Product::ID_API || $product_id === \App\Product::ID_API;


        $searchType = $request->post('search', false);
        $searchVal = $request->post('val', false);
        if ($searchType && $searchVal) {
            if ($searchType == 'id' || $searchType == 'order_no' || $searchType === 'pay_trade_no' || $searchType === 'api_out_no') {
                $query->where($searchType, $searchVal);
            } else {
                $query->where($searchType, 'like', '%' . $searchVal . '%');
            }
        }


        if ($category_id > 0) {
            if ($product_id > 0) $query->where('product_id', $product_id);
            else {
                $query->whereHas('product', function ($query) use ($category_id) {
                    /* @var \Illuminate\Database\Eloquent\Builder $query */
                    $query->where('category_id', $category_id);
                });
            }
        }


        $recent = (int)$request->post('recent', 0);
        if ($recent) {
            // 最近订单, 限制开始时间(paid_at>=XXX)
            $start_at = (new Carbon)->addDay(-$recent);
            $query->where('paid_at', '>=', $start_at);
        } else {
            $start_at = $request->post('start_at', false);
            if (strlen($start_at))
                $query->where('paid_at', '>=', $start_at . ' 00:00:00');

            $end_at = $request->post('end_at', false);
            if (strlen($end_at))
                $query->where('paid_at', '<=', $end_at . ' 23:59:59');
        }

        if ($only_profit) {
            // 利润窗口 只查询成功的订单
            $query->where('status', \App\Order::STATUS_SUCCESS);

            // 利润总和统计
            $query2 = clone $query;
            $profit_sum = $query2->selectRaw('SUM(`income`) as income, SUM(`income`-`cost`) as profit')->first();
        } else {

            $status = $request->post('status');
            if (strlen($status)) {
                $query->whereIn('status', explode(',', $status));
            } else {
                $query->where('status', '!=', \App\Order::STATUS_UNPAY);
            }

            if ($is_api) {
                // 只获取API订单
                $query->where('product_id', \App\Product::ID_API);
            } else {
                $query->where('product_id', '>', 0);
                // 这时候确认加入订单详情
                $query->with(['product' => function (Relation $query) {
                    $query->select(['id', 'name']);
                }, 'card_orders.card' => function (Relation $query) {
                    $query->select(['id', 'card']);
                }]);
            }
        }

        $query->with(['pay' => function (Relation $query) {
            $query->select(['id', 'name']);
        }]);


        $page = $request->post('current_page', 1);
        $pageSize = $request->post('per_page', 20);

        $list = $query->orderBy('id', 'DESC')->paginate($pageSize, ['*'], 'page', $page);

        if ($only_profit) {
            $list = $list->toArray();
            $list['profit_sum'] = $profit_sum;
        }
        return Response::success($list);
    }


    function stat(Request $request)
    {
        $day = (int)$request->input('day', 7);
        $list = $this->authQuery($request, \App\Order::class)
            ->where(function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('status', \App\Order::STATUS_PAID)
                    ->orWhere('status', \App\Order::STATUS_SUCCESS);
            })
            ->where('paid_at', '>=', Helper::getMysqlDate(-$day + 1))
            ->groupBy('date')->orderBy('date', 'DESC')
            ->selectRaw('DATE(`paid_at`) as "date",COUNT(*) as "count",SUM(`income`) as "sum"')
            ->get()->toArray();

        $ret = [];
        foreach ($list as $item) {
            $ret[$item['date']] = [(int)$item['count'], (int)$item['sum']];
        }

        return Response::success($ret);
    }

    function info(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = $request->post('id');

        /* @var \App\Order $order */
        $order = $this->authQuery($request, \App\Order::class)
            ->with(['pay' => function (Relation $query) {
                $query->select(['id', 'name']);
            }, 'card_orders.card' => function (Relation $query) {
                $query->select(['id', 'card']);
            }])
            ->findOrFail($id);

        $order->addHidden(['system_fee']);
        return Response::success($order);
    }

    function remark(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'remark' => 'required|string'
        ]);
        $id = $request->post('id');

        /* @var \App\Order $order */
        $order = $this->authQuery($request, \App\Order::class)->findOrFail($id);

        $order->remark = $request->post('remark');
        $order->save();
        return Response::success();
    }


    /**
     * 后台重新发货(适合未发货订单)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function ship(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        /* @var \App\Order $order */
        $order = $this->authQuery($request, \App\Order::class)->findOrFail($request->post('id'));
        if ($order->status !== \App\Order::STATUS_PAID) {
            return Response::fail('订单不是待发货状态, 无法发货');
        }
        $errMsg = null;
        $card_arr = [];
        if (FundHelper::orderSuccess($order->id,
            function () use (&$order, &$errMsg, &$card_arr) {
                /** @var \App\Order $order */
                $order = \App\Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

                if ($order->cards && count($order->cards)) {
                    $errMsg = '该订单已经发货，无需再次发货';
                    return false;
                }
                /** @var \App\Card[] $cards */
                $cards = \App\Card::where('product_id', $order->product_id)
                    ->whereRaw('`count_sold`<`count_all`')
                    ->take($order->count)->lockForUpdate()->get();

                if (count($cards) !== $order->count) {
                    $errMsg = '商品卡密不足, 请添加卡密后再发货';
                    return false;
                } else {
                    $order->status = \App\Order::STATUS_SUCCESS;
                    $order->saveOrFail();

                    $card_ids = [];
                    foreach ($cards as $card) {
                        $card_ids[] = $card->id;
                        $card_arr[] = $card->card;
                    }
                    $order->cards()->attach($card_ids); // 使用了 card_order 这个中间表
                    \App\Card::whereIn('id', $card_ids)->update([
                        'status' => \App\Card::STATUS_SOLD,
                        'count_sold' => DB::raw('`count_sold`+1')
                    ]);

                    return FundHelper::ACTION_CONTINUE;
                }
            }
        )) {
            if (filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
                $cards_br = join('<br>', $card_arr);
                try {
                    Mail::to($order->email)->Queue(
                        new OrderShipped($order,
                            '订单#' . $order->order_no . '&nbsp;已支付，卡号列表：',
                            $cards_br));
                    $order->email_sent = true;
                    $order->saveOrFail();
                } catch (\Throwable $e) {
                    // set to mail
                    \App\Library\LogHelper::setLogFile('mail');
                    \Log::error('Order.ship error, order_no:' . $order->order_no .
                        ', email:' . $order->email . ', cards:' . $cards_br .
                        ', Exception:' . $e->getMessage());

                    // 还原
                    \App\Library\LogHelper::setLogFile('card');
                }
            }
            $order->addHidden(['system_fee']);
            $order = $order->toArray();
            $order['cards'] = array_map(function ($item) {
                return ['card' => $item];
            }, $card_arr);
            return Response::success($order);
        } else {
            // orderSuccess 里面写过详细log了
            return Response::fail($errMsg ?? '数据库繁忙, 请联系客服');
        }
    }
}
