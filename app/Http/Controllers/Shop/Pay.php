<?php

namespace App\Http\Controllers\Shop;

use App\Card;
use App\Category;
use App\Library\FundHelper;
use App\Library\Helper;
use App\Product;
use App\Library\Response;
use App\Library\Pay\Pay as PayApi;
use App\Library\Geetest;
use App\Mail\OrderShipped;
use App\Mail\ProductCountWarn;
use App\System;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class Pay extends Controller
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
    private $payApi = null;

    /**
     * 跳转到支付页面
     * @param Request $request
     * @param string $order_no
     * @param string $order_name
     * @param \App\Pay $payway
     * @param int $pay_money
     * @return mixed
     */
    public function goPay($request, $order_no, $order_name, $payway, $pay_money)
    {
        try {
            // \Log::debug('Shop/Pay.goPay, order_no:' . $order_no . ', step1');
            (new PayApi)->goPay($payway, $order_no, $order_name, $order_name, $pay_money);
            // \Log::debug('Shop/Pay.goPay, order_no:' . $order_no . ', step2');
            return self::renderResult($request, [
                'success' => false,
                'title' => '请稍后',
                'msg' => '支付方式加载中，请稍后'
            ]);
        } catch (\Exception $e) {
            return self::renderResult($request, ['msg' => $e->getMessage()]);
        }
    }

    /**
     * 下单(新页面中打开的)
     * @param Request $request
     * @return mixed
     */
    function buy(Request $request)
    {
        if ((int)System::_get('vcode_shop_buy') === 1) {
            $ret = Geetest\API::verify($request->input('geetest_challenge'),
                $request->input('geetest_validate'),
                $request->input('geetest_seccode'));
            if (!$ret) {
                return self::renderResult($request, ['msg' => '系统无法接受您的验证结果，请刷新页面后重试。']);
            }
        }

        $customer = Cookie::get('customer');
        if (strlen($customer) !== 32)
            return self::renderResult($request, ['msg' => '请返回页面重新下单']);

        $category_id = (int)$request->input('category_id');
        $product_id = (int)$request->input('product_id');
        $count = (int)$request->input('count');
        $coupon = $request->input('coupon');
        $email = $request->input('email');
        $pay_id = (int)$request->input('pay_id');
        if (!$category_id || !$product_id)
            return self::renderResult($request, ['msg' => '请选择商品']);
        if (strlen($email) < 1)
            return self::renderResult($request, ['msg' => '请输入邮箱']);

        /* @var \App\Category $category */
        $category = Category::findOrFail($category_id);
        if ($category->password_open) {
            if ($category->password !== $request->input('category_password')) {
                return Response::fail('分类密码输入错误');
            }
        }

        /* @var \App\Category $category */
        $product = Product::where('id', $product_id)
            ->where('category_id', $category_id)
            ->where('enabled', 1)
            ->with(['cards' => function (Relation $query) {
                $query->whereRaw('`count_all`>`count_sold`')
                    ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                    ->groupBy('product_id');
            }])->first();
        if ($product == null || $product->user == null)
            return self::renderResult($request, ['msg' => '该商品未找到，请重新选择']);

        if ($product->password_open) {
            if ($product->password !== $request->input('product_password')) {
                return Response::fail('分类密码输入错误');
            }
        }

        if ($count < $product->buy_min)
            return self::renderResult($request, ['msg' => '该商品最少购买' . $product->buy_min . '件，请重新选择']);

        if ($count > $product->buy_max)
            return self::renderResult($request, ['msg' => '该商品限购' . $product->buy_max . '件，请重新选择']);


        $product->setAttribute('count', count($product->cards) ? $product->cards[0]->count : 0);

        if ($product->count < $count)
            return self::renderResult($request, ['msg' => '该商品库存不足']);

        $pay = \App\Pay::find($pay_id);
        if ($pay == null || !$pay->enabled) {
            return self::renderResult($request, ['msg' => '支付方式未找到，请重新选择']);
        }

        // 计算商品应付价格
        $per_price = $product->price;
        if ($product->price_whole) {
            $price_whole = json_decode($product->price_whole, true);
            for ($i = count($price_whole) - 1; $i >= 0; $i--) {
                if ($count >= (int)$price_whole[$i][0]) {
                    $per_price = (int)$price_whole[$i][1]; // 批发优惠
                    break;
                }
            }
        }
        // 应付价格
        $price = $count * $per_price;
        // 计算商品 使用优惠券后的价格 ( 实际支付 )
        $pay_money = $price;
        $used_coupon = null;
        if ($product->support_coupon && strlen($coupon) > 0) {
            $coupons = \App\Coupon::where('user_id', $product->user_id)
                ->where('coupon', $coupon)
                ->where('expire_at', '>', Carbon::now())
                ->whereRaw('`count_used`<`count_all`')->get();
            foreach ($coupons as $c) {
                if ($c->category_id === -1 ||
                    ($c->category_id === $category_id &&
                        ($c->product_id === -1 || $c->product_id === $product_id))) {

                    if ($c->discount_type === \App\Coupon::DISCOUNT_TYPE_AMOUNT && $pay_money > $c->discount_val) {
                        $used_coupon = $c;
                        $pay_money = $pay_money - $c->discount_val;
                        break;
                    }
                    if ($c->discount_type === \App\Coupon::DISCOUNT_TYPE_PERCENT) {
                        $used_coupon = $c;
                        $pay_money = $pay_money - intval($pay_money * $c->discount_val / 100);
                        break;
                    }
                }
            }
        }

        if ($used_coupon) {
            $used_coupon->status = \App\Coupon::STATUS_USED;
            $used_coupon->count_used++;
            $used_coupon->save();
        }

        $system_fee = (int)round($pay_money * $pay->fee_system);  // 系统通道手续费  ( 四舍五入 )
        $merchant_income = $pay_money - $system_fee; // 用户实际支付 - 手续费
        //商户订单号，网站订单系统中唯一订单号

        $order_no = date('YmdHis') . str_random(5);
        while (\App\Order::whereOrderNo($order_no)->exists()) {
            $order_no = date('YmdHis') . str_random(5);
        }
        \App\Order::insert([
            'user_id' => $product->user_id,
            'order_no' => $order_no,
            'product_id' => $product_id,
            'count' => $count,
            'email' => $email,
            'ip' => Helper::getIP(),
            'customer' => $customer,
            'email_sent' => false,
            'cost' => $count * $product->cost, // 商户成本
            'price' => $price, // 应售价格
            'discount' => $price - $pay_money, // 优惠金额
            'paid' => $pay_money, //实际支付金额
            'pay_id' => $pay->id,
            'fee' => $system_fee,
            'system_fee' => $system_fee,
            'income' => $merchant_income,
            'status' => \App\Order::STATUS_UNPAY,
            'created_at' => Carbon::now()
        ]);

        $order_name = $order_no;  // 商品名改成单号
        return $this->goPay($request, $order_no, $order_name, $pay, $pay_money);
        // return Response::success(['order_no' => $order_no]);
    }

    /**
     * 传入单号, 重新支付
     * @param Request $request
     * @param string $order_no
     * @return mixed
     */
    function pay(Request $request, $order_no)
    {
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order == null) {
            return self::renderResult($request, ['msg' => '订单未找到，请重试']);
        }

        if ($order->status !== \App\Order::STATUS_UNPAY) {
            return redirect('/pay/result/' . $order_no);
        }

        $logTag = 'pay: ' . $order->pay_id;
        $payway = $order->pay;
        if (!$payway) {
            \Log::error($logTag . ' cannot find Pay');
            return $this->renderResult($request, ['msg' => '支付方式未找到']);
        }
        $logTag .= ',' . $payway->driver;

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;
        $config['out_trade_no'] = $order_no;

        try {
            $this->payApi = PayApi::getDriver($payway->id, $payway->driver);
        } catch (\Exception $e) {
            \Log::error($logTag . ' cannot find Driver: ' . $e->getMessage());
            return $this->renderResult($request, ['msg' => '支付驱动未找到']);
        }
        // 如果已经支付成功, 直接返回
        if ($this->payApi->verify($config, function ($order_no, $amount, $trade_no) use ($request) {
            try {
                $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
            } catch (\Exception $e) {
                $this->renderResult($request, [
                    'success' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        })) {
            \Log::notice($logTag . ' already success' . "\n\n");
            return redirect('/pay/result/' . $order_no);
        }

        $product = Product::where('id', $order->product_id)
            ->where('enabled', 1)
            ->with(['cards' => function (Relation $query) {
                $query->whereRaw('`count_all`>`count_sold`')
                    ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                    ->groupBy('product_id');
            }])->first();
        if ($product == null) {
            return self::renderResult($request, ['msg' => '该商品已下架']);
        }

        $product->setAttribute('count', count($product->cards) ? $product->cards[0]->count : 0);

        if ($product->count < $order->count) {
            return self::renderResult($request, ['msg' => '该商品库存不足']);
        }

        $order_name = $order_no;  // 商品名改成单号
        return $this->goPay($request, $order_no, $order_name, $payway, $order->paid);
    }

    /**
     * 渲染二维码支付页面
     * /qrcode/pay/2018021318341115991/wechat/d2VpeGluOi8vd3hwYXkvYml6cGF5dXJsP3ByPVVqSU03alY=
     * @param Request $request
     * @param string $order_no 外部单号
     * @param string $pay_file 二维码模板文件名
     * @return mixed
     */
    function qrcode(Request $request, $order_no, $pay_file)
    {
        $order = \App\Order::whereOrderNo($order_no)->with('product')->first();
        if ($order == null) {
            return self::renderResult($request, ['msg' => '订单未找到，请重试']);
        }
        if ($order->product_id !== \App\Product::ID_API && $order->product == null) {
            return self::renderResult($request, ['msg' => '商品未找到，请重试']);
        }
        return view('pay/' . $pay_file, [
            'pay_id' => $order->pay_id,
            'name' => $order->product_id === \App\Product::ID_API ? $order->api_out_no : $order->product->name,
            'qrcode' => $request->get('url'),
            'id' => $order_no
        ]);
    }

    /**
     * API - 二维码页面定时查询
     * @param Request $request
     * @param string $pay_id
     * @return mixed
     */
    function qrQuery(Request $request, $pay_id)
    {
        $order_no_outer = $request->input('id', '');
        return self::payReturn($request, $pay_id, $order_no_outer);
    }

    /**
     * 支付回调 - 前台返回 (二维码查询也是这个接口)
     * @param Request $request
     * @param string $pay_id
     * @param string $out_trade_no 可选参数, <br>
     * 若设置(二维码查询/Aliwap/Alipay), 会检验是否已经发货(若发货, 直接返回, 不进行verify了, 效率高一些)
     * @return mixed
     */
    function payReturn(Request $request, $pay_id, $out_trade_no = '')
    {
        $logTag = 'payReturn: ' . $pay_id;
        \Log::debug($logTag);
        $payway = \App\Pay::where('id', $pay_id)->first();
        if (!$payway) {
            return $this->renderResult($request, [
                'success' => 0,
                'msg' => '支付方式错误'
            ]);
        }
        $logTag .= ',' . $payway->driver;

        // check if already processed
        if (strlen($out_trade_no) > 0) {
            $order = \App\Order::whereOrderNo($out_trade_no)->first();
            if ($order && ($order->status === \App\Order::STATUS_PAID || $order->status === \App\Order::STATUS_SUCCESS)) {
                \Log::notice($logTag . ' already success' . "\n\n");
                if ($request->ajax()) {
                    return self::renderResult($request, [
                        'success' => 1,
                        'data' => '/pay/result/' . $out_trade_no
                    ], [
                        'order' => $order
                    ]);
                } else {
                    return redirect('/pay/result/' . $out_trade_no);
                }
            }
        }


        try {
            $this->payApi = PayApi::getDriver($payway->id, $payway->driver);
        } catch (\Exception $e) {
            \Log::error($logTag . ' cannot find Driver: ' . $e->getMessage());
            return $this->renderResult($request, [
                'success' => 0,
                'msg' => '支付驱动未找到'
            ]);
        }

        $config = json_decode($payway->config, true);
        $config['out_trade_no'] = $out_trade_no; // (order_no)可选参数
        $config['payway'] = $payway->way;
        \Log::debug($logTag . ' will verify');

        if ($this->payApi->verify($config, function ($order_no, $amount, $trade_no) use ($request, $logTag, &$out_trade_no) {
            /**
             * @var string $order_no 本系统内订单号
             * @var int $amount 实际支付金额 单价(分)
             * @var string $trade_no 外部系统订单号
             */
            $out_trade_no = $order_no;
            try {
                \Log::debug($logTag . " shipOrder start, order_no: $order_no, amount: $amount, trade_no: $trade_no");
                $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
                \Log::debug($logTag . ' shipOrder end, order_no: ' . $order_no);
            } catch (\Exception $e) {
                \Log::error($logTag . ' shipOrder Exception: ' . $e->getMessage());
            }
        })) {
            \Log::debug($logTag . ' verify finished: 1' . "\n\n");
            if ($request->ajax()) {
                return self::renderResult($request, [
                    'success' => 1,
                    'data' => '/pay/result/' . $out_trade_no  // (order_no)
                ]);
            } else {
                return redirect('/pay/result/' . $out_trade_no);  // (order_no)
            }
        } else {
            \Log::debug($logTag . ' verify finished: 0' . "\n\n");
            return $this->renderResult($request, [
                'success' => 0,
                'msg' => '支付验证失败，您可以稍后查看支付状态。'
            ]);
        }

    }

    /**
     * 支付回调 - 后台通知
     * @param Request $request
     * @param string $pay_id
     */
    function payNotify(Request $request, $pay_id)
    {
        $logTag = 'payNotify pay_id: ' . $pay_id;
        \Log::debug($logTag);

        $payway = \App\Pay::where('id', $pay_id)->first();
        if (!$payway) {
            \Log::error($logTag . ' cannot find PayModel');
            echo 'fail';
            exit;
        }
        $logTag .= ',' . $payway->driver;
        try {
            $this->payApi = PayApi::getDriver($payway->id, $payway->driver);
        } catch (\Exception $e) {
            \Log::error($logTag . ' cannot find Driver: ' . $e->getMessage());
            echo 'fail';
            exit;
        }

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;
        $config['isNotify'] = true;
        \Log::debug($logTag . ' will verify');
        $ret = $this->payApi->verify($config, function ($order_no, $amount, $trade_no) use ($request, $logTag) {
            try {
                \Log::debug($logTag . " shipOrder start, order_no: $order_no, amount: $amount, trade_no: $trade_no");
                $this->shipOrder($request, $order_no, $amount, $trade_no, FALSE);
                \Log::debug($logTag . ' shipOrder end, order_no: ' . $order_no);
            } catch (\Exception $e) {
                \Log::error($logTag . ' shipOrder Exception: ' . $e->getMessage());
            }
        });
        \Log::debug($logTag . ' notify finished: ' . (int)$ret . "\n\n");
        exit;
    }

    /**
     * 渲染支付结果页面, 这里
     * @param Request $request
     * @param string $order_no
     * @return mixed
     * @throws \Throwable
     */
    function result(Request $request, $order_no)
    {
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order == null) {
            return self::renderResult($request, ['msg' => '订单未找到，请重试']);
        }

        if ($order->status === \App\Order::STATUS_PAID) {
            // 处于已支付 (不是 SUCCESS) , 说明没货了
            $qq = $order->user->qq;
            $msg = '商家库存不足，因此卡密没有自动发货，请联系商家客服发货';
            if ($qq) {
                $msg .= '<br><a href="http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes" target="_blank">商家客服QQ:' . $qq . '</a>';
            }
            return self::renderResult($request, [
                'success' => false,
                'title' => '订单已支付',
                'msg' => $msg
            ], [
                'order' => $order
            ]);
        } elseif ($order->status === \App\Order::STATUS_SUCCESS) {
            // 说明已经 notify 过了, 订单已经成功, 直接取出card 渲染页面
            // 这里不会抛出异常($order->paid 时仅渲染页面)
            return $this->shipOrder($request, $order->order_no, $order->paid, 0, TRUE);
        }

        return self::renderResult($request, [
            'success' => false,
            'msg' => $order->remark ? '失败原因:<br>' . $order->remark : '订单支付失败，请重试'
        ], [
            'order' => $order
        ]);
    }


    /**
     * 渲染页面(包括二维码轮训页面) 用于提示结果 之类的
     * @param Request $request
     * @param array $result
     * @param array $append
     * @return mixed
     */
    function renderResult(Request $request, $result, $append = [])
    {
        if ($request->ajax()) {
            if (@$result['success']) {
                return Response::success($result['data']);
            } else {
                return Response::fail('error', $result['msg']);
            }
        } else {
            return view('pay.result', array_merge([
                'result' => $result,
                'data' => $append
            ], $append));
        }
    }


    /**
     * 处理订单并渲染页面
     * @param $request
     * @param string $order_no 本站订单号
     * @param int $amount 单位/分. 支付金额
     * @param string $trade_no 支付通道内订单号
     * @param bool $need_render 是否渲染页面
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function shipOrder($request, $order_no, $amount, $trade_no, $need_render = true)
    {


        /** @var \App\Order $order */
        $order = \App\Order::whereOrderNo($order_no)->first();
        if ($order === null) {

            \Log::error('shipOrder: No query results for model [App\Order:' . $order_no . ',trade_no:' . $trade_no . ',amount:' . $amount . ']. die(\'success\');');
            die('success');
        }

        // 注意 一些支付方式 实际到账金额 可能略大于 订单金额, 所以这里只验证少的, 少了就禁止掉
        if ($order->paid > $amount) {
            \Log::alert('shipOrder, price may error, order_no:' . $order_no .
                ', paid:' . $order->paid . ', $amount get:' . $amount);
            $order->remark = '支付金额(' . sprintf('%0.2f', $amount / 100) .
                ') 小于 订单金额(' . sprintf('%0.2f', $order->paid / 100) . ')';
            $order->save();
            throw new \Exception($order->remark);
        }

        $cards = [];
        $card_msg = '订单#' . $order_no . '&nbsp;已支付，卡号列表：';
        $cards_txt = '';
        $product = null;
        $first_process = $order->status === \App\Order::STATUS_UNPAY; //未处理过此订单
        $need_mail = $first_process && System::_getInt('mail_send_order') === 1 && filter_var($order->email, FILTER_VALIDATE_EMAIL);

        if ($first_process) {
            \Log::debug('shipOrder.first_process:' . $order_no);
            $order_id = $order->id;
            if (FundHelper::orderSuccess($order,
                /**
                 * 这个函数做了以下工作
                 * Order -> STATUS_PAID or STATUS_SUCCESS
                 * Product 和 Card -> sold + 1
                 * 返回 STATUS_PAID or STATUS_SUCCESS
                 */
                function () use ($order_id, $trade_no, &$cards, &$cards_txt) {
                    /** @var \App\Order $order */
                    $order = \App\Order::where('id', $order_id)->lockForUpdate()->firstOrFail();
                    if ($order->status !== \App\Order::STATUS_UNPAY) {
                        \Log::debug('shipOrder.first_process:' . $order->order_no . ' already processed!');
                        return -999;
                    }
                    /** @var \App\Product $product */
                    $product = $order->product()->lockForUpdate()->firstOrFail();
                    $product->count_sold += $order->count;
                    $product->saveOrFail();

                    $order->pay_trade_no = $trade_no; // 支付通道订单号
                    $order->paid_at = Carbon::now();

                    /** @var \App\Card[] $cards */
                    $cards = Card::where('product_id', $order->product_id)
                        ->whereRaw('`count_sold`<`count_all`')
                        ->take($order->count)->lockForUpdate()->get();
                    // 这里注意 如果商品包含重复卡密, 则商品,每次限购1件


                    if (count($cards) !== $order->count) {
                        \Log::alert('订单:' . $order->order_no .
                            ', 购买数量:' . $order->count .
                            ', 卡数量:' . count($cards) . ' 卡密不足(已支付 未发货)');

                        // 库存不足了, GG
                        // 已支付 未发货
                        $order->status = \App\Order::STATUS_PAID;
                        $order->saveOrFail();
                        return \App\Order::STATUS_PAID;
                        // 这里需要商户到后台手动发货
                    } else {
                        $order->status = \App\Order::STATUS_SUCCESS;
                        $order->saveOrFail();
                        $card_ids = [];
                        foreach ($cards as $card) {
                            $cards_txt .= $card->card . '<br>';
                            $card_ids[] = $card->id;
                        }
                        $order->cards()->attach($card_ids); // 使用了 card_order 这个中间表
                        Card::whereIn('id', $card_ids)->update([
                            'status' => Card::STATUS_SOLD,
                            'count_sold' => \DB::raw('`count_sold`+1')
                        ]);

                        return \App\Order::STATUS_SUCCESS;
                    }
                })
            ) {
                // ------------------------------------------------------------------------ 提醒库存 start
                $product = Product::where('id', $order->product_id)
                    ->with(['cards' => function (Relation $query) {
                        $query->whereRaw('`count_all`>`count_sold`')
                            ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                            ->groupBy('product_id');
                    }])->first();
                if ($product) {
                    // \Log::debug('shipOrder.count_warn step1');
                    $count = count($product->cards) ? $product->cards[0]->count : 0;
                    // \Log::debug('shipOrder.count_warn step2, count:' . $count . ', warn:' . $product->count_warn);
                    $product->setAttribute('count', $count);
                    if ($product->count_warn > 0 && $count < $product->count_warn) {
                        try {
                            // \Log::debug('shipOrder.count_warn step3');
                            \Mail::to($order->user->email)->Queue(new ProductCountWarn($product, $count));
                            // \Log::debug('shipOrder.count_warn step4, email:' . $order->user->email);
                        } catch (\Exception $e) {
                            // set to mail
                            \App\Library\LogHelper::setLogFile('mail');
                            \Log::error('shipOrder.count_warn error, product_id:' . $order->product_id .
                                ', email:' . $order->user->email . ', Exception:' . $e);
                            // 还原
                            \App\Library\LogHelper::setLogFile('card');
                        }
                    }
                }
                // ------------------------------------------------------------------------ 提醒库存 end
            } else {
                \Log::error('shipOrder.first_process error, order_no:' . $order_no . ',trade_no:' . $trade_no);
                // orderSuccess 里面写过详细log了
                throw new \Exception('merchant operate exception!');
            }
        } elseif ($need_render) {
            // result/{order_no} 渲染支付结果时进入此分支, 已经notify过(订单已经处理过, 直接渲染页面)
            $cards = $order->cards;
            $product = $order->product;
            foreach ($cards as $card) {
                $cards_txt .= $card->card . "\r\n";
            }
        }

        if ($need_render || $need_mail) {

            if (count($cards) < $order->count) {
                // Tip:
                // 卡密若包含可以重复使用, 则商品限购一件
                // 即: $order->count === 1
                if (count($cards)) {
                    $card_msg = '目前库存不足，您还有' . ($order->count - count($cards)) . '张卡密未发货，请联系商家客服发货<br>已发货卡密见下方：<br>';
                } else {
                    $card_msg = '目前库存不足，您购买的' . ($order->count - count($cards)) . '张卡密未发货，请联系商家客服发货<br>';
                }
                $qq = $order->user->qq;
                if ($qq) {
                    $card_msg .= '<a href="http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes" target="_blank">商家客服QQ:' . $qq . '</a><br>';
                }
            }
        }


        // $need_mail = $first_process && System::_getInt('mail_send_order') === 1 && filter_var($order->email, FILTER_VALIDATE_EMAIL);
        if ($need_mail) {
            $cards_txt_br = str_replace("\r\n", '<br>', $cards_txt);
            try {
                \Mail::to($order->email)->Queue(new OrderShipped($order, $card_msg, $cards_txt_br));
                $order->email_sent = true;
                $order->saveOrFail();
            } catch (\Exception $e) {
                // set to mail
                \App\Library\LogHelper::setLogFile('mail');
                \Log::error('shipOrder.need_mail error, order_no:' . $order_no .
                    ', email:' . $order->email . ', cards:' . $cards_txt_br .
                    ', Exception:' . $e->getMessage());
                // 还原
                \App\Library\LogHelper::setLogFile('card');
            }
        }

        if ($need_render) {
            return self::renderResult($request, [
                'success' => true,
                'msg' => $card_msg,
            ], [
                'card_txt' => $cards_txt,
                'order' => $order,
                'product' => $product
            ]);
        }
        return FALSE;
    }

}
