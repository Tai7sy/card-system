<?php

namespace App\Http\Controllers\Admin;

use App\Library\Response;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $incomeToday = (int)Order::whereDate('paid_at', date('Y-m-d'))
            ->where('paid', true)
            ->where('amount', '>', 0)->sum('amount');
        $incomeMonth = (int)Order::whereYear('paid_at', date('Y'))->whereMonth('paid_at', date('m'))
            ->where('paid', true)
            ->where('amount', '>', 0)->sum('amount');
        $incomeYear = (int)Order::whereYear('paid_at', date('Y'))
            ->where('paid', true)
            ->where('amount', '>', 0)->sum('amount');


        $day = (int)$request->post('day', 30);

        $list = Order::where('paid_at', '>=', date('Y-m-d', time() - $day * 24 * 3600))
            ->where('paid', true)
            ->where('amount', '>', 0)
            ->groupBy('date')->orderBy('date', 'DESC')
            ->get(array(
                DB::raw('Date(`paid_at`) as "date"'),
                DB::raw('SUM(`amount`) as "sum"')
            ));

        $list = $list->toArray();
        $order = [];
        foreach ($list as $item) {
            $order[$item['date']] = (int)$item['sum'];
        }


        $list = Order::where('paid_at', '>=', date('Y-m-d', time() - $day * 24 * 3600))
            ->where('paid', true)
            ->where('amount', '>', 0)
            ->groupBy('pay_id')->orderBy('pay_id', 'DESC')
            ->with('pay')
            ->get(array(
                'pay_id',
                DB::raw('COUNT(*) as "count"'),
                DB::raw('SUM(`amount`) as "sum"')
            ));
        $pay = [];
        foreach ($list as $item) {
            $pay[$item['pay']['name']] = [(int)$item['count'], (int)$item['sum']];
        }


        return Response::Ret(0, 'success', [
            'income' => [
                'today' => $incomeToday,
                'month' => $incomeMonth,
                'year' => $incomeYear,
            ],
            'order' => [
                'day' => $day,
                'data' => $order
            ],
            'pay' => [
                'day' => $day,
                'data' => $pay
            ]
        ]);
    }


}
