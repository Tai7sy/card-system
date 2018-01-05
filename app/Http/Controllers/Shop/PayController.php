<?php

namespace App\Http\Controllers\Shop;

use App\Card;
use App\Mail\OrderShipped;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pay;
use App\Good;
use App\Order;
use App\Library\Response;
use App\Library\Pay\Pay as PayApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class PayController extends Controller
{

    public function __construct()
    {
        define('SYS_NAME', config('app.name'));
        define('SYS_URL', config('app.url'));
        define('SYS_URL_API', config('app.url_api'));
    }

    /**
     * @var \APP\Library\Pay\ApiInterface
     */
    private $driver = null;

    public function is_mobile()
    {
        //判断手机发送的客户端标志
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取支付方式列表
     * @return mixed
     */
    public function get()
    {
        if (self::is_mobile()) {
            $enabled = Pay::ENABLED_MOBILE;
        } else {
            $enabled = Pay::ENABLED_PC;
        }
        // bit operate   pc & mobile = result_enabled
        $list = Pay::whereRaw('enabled&' . $enabled . '!=0')->get();
        return Response::Ret(0, 'success', $list);
    }

    /**
     * 跳转到支付页面
     * @param Request $request
     * @param string $order_no
     * @param string $order_name
     * @param Pay $payway
     * @param int $pay_money
     * @return mixed
     */
    private function goPay($request, $order_no, $order_name, $payway, $pay_money)
    {

        try {
            (new PayApi)->goPay($payway, $order_no, $order_name, $order_name, $pay_money);
            return self::renderResult($request, [
                'success' => false,
                'title' => '请稍后',
                'msg' => '支付方式加载中，请稍后'
            ]);
        } catch (\Exception $e) {
            return self::renderResult($request, [
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 下单
     * @param Request $request
     * @return mixed
     */
    public function buy(Request $request)
    {
        $good_id = $request->post('good_id');
        $count = $request->post('count');
        $email = $request->post('email');
        $pay_id = $request->post('pay_id');


        $good = Good::find($good_id);
        if ($good == null || !$good->enabled) {
            return self::renderResult($request, [
                'msg' => '该商品未找到，请重新选择'
            ]);
        }

        if ($good->left_count < $count) {
            return self::renderResult($request, [
                'msg' => '该商品库存不足'
            ]);
        }
        $pay_money = $count * $good->price;


        $pay = Pay::find($pay_id);
        if ($pay == null || !$pay->enabled) {
            return self::renderResult($request, [
                'msg' => '支付方式未找到，请重新选择'
            ]);
        }

        //商户订单号，网站订单系统中唯一订单号
        $order_no = date('YmdHis') . mt_rand(10000, 99999);
        while (Order::whereOrderNo($order_no)->exists()) {
            $order_no = date('YmdHis') . mt_rand(10000, 99999);
        }

        \App\Order::insert([
            'order_no' => $order_no,
            'good_id' => $good->id,
            'count' => $count,
            'email' => $email,
            'email_sent' => false,
            'amount' => $pay_money,
            'pay_id' => $pay->id,
            'paid' => false,
            'created_at' => date('Y-m-d H:i:s', time())
        ]);

        $order_name = config('app.name') . ' - ' . $good->name;
        return $this->goPay($request, $order_no, $order_name, $pay, $pay_money);
    }

    /**
     * 传入单号, 重新支付
     * @param Request $request
     * @param string $order_no
     * @return mixed
     */
    public function pay(Request $request, $order_no)
    {
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order == null) {
            return self::renderResult($request, [
                'success' => true,
                'msg' => '订单未找到，请重试'
            ]);
        }

        $payway = $order->pay;
        $driver = $payway->driver;

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;
        $config['out_trade_no'] = $order_no;


        try {
            $this->driver = PayApi::getDriver($payway->driver);
        } catch (\Exception $e) {
            Log::error('pay: ' . $driver . ' cannot find Driver: ' . $e->getMessage());
            $this->renderResult($request, [
                'success' => false,
                'msg' => '支付驱动未找到'
            ]);
        }


        if ($this->driver->verify($config, function ($order_no, $amount, $trade_no) use ($request, $driver) {
            try {
                DB::transaction(function () use ($order_no, $amount, $trade_no, $request, $driver) {
                    $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
                });

            } catch (\Exception $e) {
                $this->renderResult($request, [
                    'success' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        })) {
            Log::notice('pay: ' . $driver . ' already success' . "\n\n");
            return redirect('/pay/result/' . $order_no);
        }

        if ($order->good->left_count < $order->count) {
            return self::renderResult($request, [
                'msg' => '该商品库存不足'
            ]);
        }

        return $this->goPay($request, $order_no, $order->good->name, $payway, $order->amount);
    }

    /**
     * 渲染二维码支付页面
     * @param Request $request
     * @param string $order_no 外部单号
     * @param string $pay 二维码模板文件名
     * @param string $qr 二维码的base64值
     * @return mixed
     */
    public function qrcode(Request $request, $order_no, $pay, $qr)
    {
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order == null) {
            return self::renderResult($request, [
                'success' => true,
                'msg' => '订单未找到，请重试'
            ]);
        }
        $qr = base64_decode($qr);
        return view('pay/' . $pay, [
            'qrcode' => $qr,
            'id' => $order_no
        ]);
    }

    /**
     * API - 二维码页面定时查询
     * @param Request $request
     * @param string $driver
     * @return mixed
     */
    public function qrQuery(Request $request, $driver)
    {
        $order_no_outer = $request->post('id', '');
        return self::payReturn($request, $driver, $order_no_outer);
    }

    /**
     * 支付回调 - 前台返回
     * @param Request $request
     * @param string $driver
     * @param string $out_trade_no 可选参数, 若设置, 会检验是否已经发货
     * @return mixed
     */
    public function payReturn(Request $request, $driver, $out_trade_no = '')
    {
        Log::notice('payReturn: ' . $driver);
        $payway = Pay::where('driver', $driver)->first();
        if (!$payway) {
            return $this->renderResult($request, [
                'success' => false,
                'msg' => '支付方式错误'
            ]);
        }

        // check if already processed
        $order = Order::whereOrderNo($out_trade_no)->first();
        if ($order && $order->paid) {
            Log::notice('payReturn: ' . $driver . ' already success' . "\n\n");
            if ($request->ajax()) {
                return self::renderResult($request, [
                    'success' => $order->paid,
                    'data' => '/pay/result/' . $out_trade_no
                ]);
            } else {
                return redirect('/pay/result/' . $out_trade_no);
            }
        }

        try {
            $this->driver = PayApi::getDriver($payway->driver);
        } catch (\Exception $e) {
            Log::error('payReturn: ' . $driver . ' cannot find Driver: ' . $e->getMessage());
            $this->renderResult($request, [
                'success' => false,
                'msg' => '支付驱动未找到'
            ]);
        }

        $config = json_decode($payway->config, true);
        $config['out_trade_no'] = $out_trade_no;
        $config['payway'] = $payway->way;
        Log::notice('payReturn: ' . $driver . ' will verify');

        if ($this->driver->verify($config, function ($order_no, $amount, $trade_no) use ($request, $driver) {
            try {
                Log::notice('payReturn: ' . $driver . ' will transaction');
                DB::transaction(function () use ($order_no, $amount, $trade_no, $request, $driver) {
                    Log::notice('payReturn: ' . $driver . ' will shipOrder');
                    $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
                    Log::notice('payReturn: ' . $driver . ' shipOrder finished');
                });

            } catch (\Exception $e) {
                $this->renderResult($request, [
                    'success' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        })) {
            Log::notice('payReturn: ' . $driver . ' verify finished: success' . "\n\n");
            if ($request->ajax()) {
                return self::renderResult($request, [
                    'success' => true,
                    'data' => '/pay/result/' . $out_trade_no
                ]);
            } else {
                return redirect('/pay/result/' . $out_trade_no);
            }
        } else {
            Log::notice('payReturn: ' . $driver . ' verify finished: fail' . "\n\n");
            return $this->renderResult($request, [
                'success' => false,
                'msg' => '支付验证失败，您可以稍后查看支付状态。'
            ]);
        }

    }

    /**
     * 支付回调 - 后台通知
     * @param Request $request
     * @param string $driver
     */
    public function payNotify(Request $request, $driver)
    {
        Log::notice('payNotify: ' . $driver);

        $payway = Pay::where('driver', $driver)->first();
        if (!$payway) {
            Log::error('payNotify: ' . $driver . ' cannot find PayWayModel');
            echo 'fail';
            exit;
        }

        try {
            $this->driver = PayApi::getDriver($payway->driver);
        } catch (\Exception $e) {
            Log::error('payNotify: ' . $driver . ' cannot find Driver: ' . $e->getMessage());
            echo 'fail';
        }

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;
        $config['isNotify'] = true;
        Log::notice('payNotify: ' . $driver . ' will verify');
        $ret = $this->driver->verify($config, function ($order_no, $amount, $trade_no) use ($request, $driver) {
            try {
                Log::notice('payNotify: ' . $driver . ' will transaction');
                DB::transaction(function () use ($order_no, $amount, $trade_no, $request, $driver) {
                    Log::notice('payNotify: ' . $driver . ' will shipOrder');
                    $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
                    Log::notice('payNotify: ' . $driver . ' shipOrder finished');
                });
            } catch (\Exception $e) {
                // do nothing
            }
        });
        Log::notice('payNotify: ' . $driver . ' notify finished: ' . $ret . "\n\n");
        exit;
    }

    /**
     * 渲染支付结果页面, 这里
     * @param Request $request
     * @param string $order_no
     * @return mixed
     * @throws \Throwable
     */
    public function result(Request $request, $order_no)
    {
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order == null) {
            return self::renderResult($request, [
                'success' => false,
                'msg' => '订单未找到，请重试'
            ]);
        }

        if ($order->paid) {
            // 说明已经 notify 过了, 直接取出card 渲染页面
            // 这里不会抛出异常($order->paid 时仅渲染页面)
            return $this->shipOrder($request, $order->order_no, $order->amount, 0, TRUE);
        }

        return self::renderResult($request, [
            'success' => false,
            'msg' => '订单支付失败，请重试'
        ]);
    }


    /**
     * 渲染输出页面
     * @param Request $request
     * @param array $status
     * @return mixed
     */
    private function renderResult(Request $request, $status)
    {
        if ($request->ajax()) {
            if ($status['success']) {
                return Response::Ret(0, 'ok', $status['data']);
            } else {
                return Response::Ret(-1, 'error', $status['msg']);
            }
        } else {
            echo view('pay/result', [
                'status' => $status
            ]);
        }

        exit;
    }


    /**
     * 处理订单并渲染页面
     * @param $request
     * @param $order_no
     * @param $amount
     * @param $trade_no
     * @param bool $need_render 是否渲染页面
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    private function shipOrder($request, $order_no, $amount, $trade_no, $need_render = true)
    {
        // 这个函数写的有点乱

        /** @var \App\Order $order */
        $order = Order:: whereOrderNo($order_no)->lockForUpdate()->firstOrFail();

        if ($order->amount > $amount) {
            throw new Exception('支付价格错误');
        }

        $cards_txt = '';
        $first_process = !$order->paid;
        $need_mail = $first_process && config('mail.send');

        if ($first_process) {
            /** @var \App\Good $good */
            $good = $order->good()->lockForUpdate()->firstOrFail();

            $good->sold_count += $order->count;
            $good->saveOrFail();

            $order->pay_trade_no = $trade_no;
            $order->paid = true;
            $order->paid_at = date('Y-m-d H:i:s', time());
            $order->saveOrFail();


            $cards = Card::where('good_id', $order->good_id)
                ->where(function ($query) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $query->where('status', Card::STATUS_NORMAL)->orWhere('type', Card::TYPE_REPEAT);
                })->take($order->count)->get();
            $order->cards()->attach($cards);

            foreach ($cards as $card) {
                $cards_txt .= $card->card . '<br/>';
                if ($card->type !== Card::TYPE_REPEAT) {
                    $card->status = Card::STATUS_SOLD;
                    $card->save();
                }
            }

        } elseif ($need_render) {
            $cards = $order->cards;
            foreach ($cards as $card) {
                $cards_txt .= $card->card . '<br/>';
            }
        }

        if ($need_render || $need_mail) {
            if (count($cards) < $order->count) {
                if (count($cards) && $cards[0]->type == Card::TYPE_REPEAT) {
                    for ($i = count($cards); $i < $order->count; $i++) {
                        $cards_txt .= $cards[0]->card . '<br/>';
                    }
                } else {
                    $cards_txt .= '目前库存不足，剩余' . ($order->count - count($cards)) . '张卡密，请联系客服补货<br/>';
                }
            }
        }


        if ($need_mail) {
            try {
                Mail::to($order->email)->send(new OrderShipped($order, $cards_txt));
                $order->email_sent = true;
                $order->saveOrFail();
            } catch (\Exception $e) {

            }
        }

        if ($need_render) {
            return self::renderResult($request, [
                'success' => true,
                'msg' => '订单已完成，卡号列表：<br>' . $cards_txt,
                'data' => $cards_txt
            ]);
        }
        return FALSE;
    }


}
