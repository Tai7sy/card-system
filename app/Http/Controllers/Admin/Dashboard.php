<?php

namespace App\Http\Controllers\Admin;

use App\Library\CurlRequest;
use App\Library\Response;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Dashboard extends Controller
{
    // 显示平台当日实时收入（已完成订单金额扣除商户平台费率在减去支付通道费率）
    function index(Request $request)
    {
        $dashboard = [
            'today' => [
                'count' => 0,
                'paid' => 0,
                'profit' => 0
            ],
            'yesterday' => [
                'count' => 0,
                'paid' => 0,
                'profit' => 0
            ]
        ];

        $ordersToday = Order::whereUserId(\Auth::Id())
            ->whereDate('paid_at', \Carbon\Carbon::now()->toDateString())
            ->where(function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('status', Order::STATUS_PAID)
                    ->orWhere('status', Order::STATUS_SUCCESS);
            })
            ->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')
            ->get()->toArray();

        $ordersYesterday = Order::whereUserId(\Auth::Id())
            ->whereDate('paid_at', \Carbon\Carbon::yesterday()->toDateString())
            ->where(function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('status', Order::STATUS_PAID)
                    ->orWhere('status', Order::STATUS_SUCCESS);
            })
            ->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')
            ->get()->toArray();


        if (isset($ordersToday[0]) && isset($ordersToday[0]['count'])) {
            $dashboard['today'] = [
                'count' => (int)$ordersToday[0]['count'],
                'paid' => (int)$ordersToday[0]['paid'],
                'profit' => (int)$ordersToday[0]['profit']
            ];
        }
        if (isset($ordersYesterday[0]) && isset($ordersYesterday[0]['count'])) {
            $dashboard['yesterday'] = [
                'count' => (int)$ordersYesterday[0]['count'],
                'paid' => (int)$ordersYesterday[0]['paid'],
                'profit' => (int)$ordersYesterday[0]['profit']
            ];
        }

        $dashboard['need_ship_count'] = Order::whereUserId(\Auth::Id())
            ->where('status', Order::STATUS_PAID)->count();
        // 订单状态为 已支付 = 已支付未发货

        $dashboard['login'] = \App\Log::where('action', \App\Log::ACTION_LOGIN)->latest()->firstOrFail();

        return Response::success($dashboard);
    }


    function clearCache()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('route:cache');
            \Artisan::call('config:cache');
        } catch (\Throwable $e) {
            return Response::fail($e->getMessage());
        }
        return Response::success();
    }

    function version()
    {
        return Response::success([
            'version' => config('app.version')
        ]);
    }

    function checkUpdate()
    {
        return Response::success(
            @json_decode(
                CurlRequest::get('https://raw.githubusercontent.com/Tai7sy/card-system/master/.version'
                ), true)
        );
    }
}
