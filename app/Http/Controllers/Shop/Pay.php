<?php

namespace App\Http\Controllers\Shop;

use App\Card;
use App\Category;
use App\Library\FundHelper;
use App\Library\Helper;
use App\Library\LogHelper;
use App\Product;
use App\Library\Response;
use Gateway\Pay\Pay as GatewayPay;
use App\Library\Geetest;
use App\Mail\ProductCountWarn;
use App\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Pay extends Controller
{

    public function __construct()
    {
        define('SYS_NAME', config('app.name'));
        define('SYS_URL', config('app.url'));
        define('SYS_URL_API', config('app.url_api'));
    }

    /**
     * @var \Gateway\Pay\ApiInterface
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
            $config = json_decode($payway->config, true);
            $config['payway'] = $payway->way;
            // \Log::debug('Shop/Pay.goPay, order_no:' . $order_no . ', step1');
            GatewayPay::getDriver($payway)->goPay($config, $order_no, $order_name, $order_name, $pay_money);
            // \Log::debug('Shop/Pay.goPay, order_no:' . $order_no . ', step2');
            return self::renderResultPage($request, [
                'success' => false,
                'title' => '请稍后',
                'msg' => '支付方式加载中，请稍后'
            ]);
        } catch (\Exception $e) {
            return self::renderResultPage($request, ['msg' => $e->getMessage()]);
        }
    }

    /**
     * 下单(新页面中打开的)
     * @param Request $request
     * @return mixed
     */
    function buy(Request $request)
    {
        $customer = $request->input('customer');
        if (strlen($customer) !== 32) {
            return self::renderResultPage($request, [
                'msg' => '提交超时，请刷新购买页面并重新提交<br><br>
当前网址: ' . $request->getQueryString() . '
提交内容: ' . var_export($customer) . ', 提交长度:' . strlen($customer) . '<br>
若您刷新后仍然出现此问题. 请加网站客服反馈'
            ]);
        }

        if ((int)System::_get('vcode_shop_buy') === 1) {
            $ret = Geetest\API::verify($request->input('geetest_challenge'),
                $request->input('geetest_validate'),
                $request->input('geetest_seccode'));
            if (!$ret) {
                return self::renderResultPage($request, ['msg' => '滑动验证超时，请返回页面重试。']);
            }
        }

        $category_id = (int)$request->input('category_id');
        $product_id = (int)$request->input('product_id');
        $count = (int)$request->input('count');
        $coupon = $request->input('coupon');
        $contact = $request->input('contact');
        $contact_ext = $request->input('contact_ext') ?? null;
        $send_sms = !empty(@json_decode($contact_ext, true)['_mobile']);
        $pay_id = (int)$request->input('pay_id');
        if (!$category_id || !$product_id)
            return self::renderResultPage($request, ['msg' => '请选择商品']);
        if (strlen($contact) < 1)
            return self::renderResultPage($request, ['msg' => '请输入联系方式']);

        /* @var \App\Category $category */
        $category = Category::findOrFail($category_id);

        /* @var \App\Product $product */
        $product = Product::where('id', $product_id)
            ->where('category_id', $category_id)
            ->where('enabled', 1)
            ->with(['user'])->first();
        if ($product == null || $product->user == null)
            return self::renderResultPage($request, ['msg' => '该商品未找到，请重新选择']);

        // 如果商品设置了密码, 那么不校验分类密码
        if ($product->password_open) {
            if ($product->password !== $request->input('product_password')) {
                return self::renderResultPage($request, ['msg' => '商品密码输入错误']);
            }
        } else {
            if ($category->password_open) {
                if ($category->password !== $request->input('category_password')) {
                    // 检查下是否从商品链接过来的(商品链接会把分类临时密码写进去)
                    if ($category->getTmpPassword() !== $request->input('category_password')) {
                        return self::renderResultPage($request, ['msg' => '分类密码输入错误']);
                    }
                }
            }
        }

        if ($count < $product->buy_min)
            return self::renderResultPage($request, ['msg' => '该商品最少购买' . $product->buy_min . '件，请重新选择']);

        if ($count > $product->buy_max)
            return self::renderResultPage($request, ['msg' => '该商品限购' . $product->buy_max . '件，请重新选择']);

        if ($product->count < $count)
            return self::renderResultPage($request, ['msg' => '该商品库存不足']);

        $pay = \App\Pay::find($pay_id);
        if ($pay == null || !$pay->enabled) {
            return self::renderResultPage($request, ['msg' => '支付方式未找到，请重新选择']);
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
        $discount_fee = 0;
        $used_coupon = null;
        if ($product->support_coupon && strlen($coupon) > 0) {
            /** @var \App\Coupon[] $coupons */
            $coupons = \App\Coupon::where('user_id', $product->user_id)
                ->where('coupon', $coupon)
                ->where('expire_at', '>', Carbon::now())
                ->whereRaw('`count_used`<`count_all`')->get();
            foreach ($coupons as $c) {
                if ($c->category_id === -1 ||
                    ($c->category_id === $category_id &&
                        ($c->product_id === -1 || $c->product_id === $product_id))) {

                    if ($c->discount_type === \App\Coupon::DISCOUNT_TYPE_AMOUNT && $pay_money >= $c->discount_val) {
                        // (可以大于等于)支付金额
                        $used_coupon = $c;
                        $discount_fee = $c->discount_val;
                        break;
                    }
                    if ($c->discount_type === \App\Coupon::DISCOUNT_TYPE_PERCENT) {
                        $used_coupon = $c;
                        $discount_fee = (int)round($pay_money * $c->discount_val / 100);
                        break;
                    }
                }
            }
            if ($used_coupon === null) {
                return self::renderResultPage($request, ['msg' => '优惠券信息错误，请重新输入']);
            }
            $pay_money -= $discount_fee;
        }

        $system_fee = (int)round($pay_money * $pay->fee_system);  // 系统通道手续费  ( 四舍五入 )
        $merchant_income = $pay_money - $system_fee; // 用户实际支付 - 手续费

        // 短信费用
        $sms_price = $send_sms ? System::_getInt('sms_price', 10) : 0;
        $pay_money += $sms_price;

        // 商品成本价
        $cost = $count * $product->cost;

        $order_no = \App\Order::unique_no();  //商户订单号，网站订单系统中唯一订单号

        try{
            DB::transaction(function () use (
                $product, $order_no, $used_coupon, $contact, $contact_ext, $customer,
                $count, $cost, $price, $sms_price, $discount_fee, $pay_money, $pay, $system_fee, $merchant_income
            ) {

                if ($used_coupon) {
                    $used_coupon->status = \App\Coupon::STATUS_USED;
                    $used_coupon->count_used++;
                    $used_coupon->save();
                    $remark = '使用优惠券: ' . $used_coupon->coupon;
                } else {
                    $remark = null;
                }

                $order = \App\Order::create([
                    'user_id' => $product->user_id,
                    'order_no' => $order_no,
                    'product_id' => $product->id,
                    'product_name' => $product->name,   // 缓存一个商品名称
                    'count' => $count,
                    'ip' => Helper::getIP(),
                    'customer' => $customer,
                    'contact' => $contact,
                    'contact_ext' => $contact_ext,
                    'cost' => $cost,                    // 商户商品成本
                    'price' => $price,                  // 应付款总额(单价*数量)
                    'sms_price' => $sms_price,
                    'discount' => $discount_fee,        // 优惠金额
                    'paid' => $pay_money,               // 实际支付金额 (price - discount + 短信0.1)
                    'pay_id' => $pay->id,
                    'fee' => $system_fee,
                    'system_fee' => $system_fee,
                    'income' => $merchant_income,
                    'status' => \App\Order::STATUS_UNPAY,
                    'remark' => $remark,
                    'created_at' => Carbon::now(),
                ]);

                assert($order !== null);

            });
        }catch (\Throwable $e){
            Log::error('Shop.Pay.buy 下单失败', ['Exception' => $e]);
            return self::renderResultPage($request, ['msg' => '发生错误，下单失败，请稍后重试']);
        }

        if ($pay_money === 0) {
            // 支付金额为0, 直接成功
            $this->shipOrder($request, $order_no, $pay_money, null);
            return redirect('/pay/result/' . $order_no);
        }

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
            return self::renderResultPage($request, ['msg' => '订单未找到，请重试']);
        }

        if ($order->status !== \App\Order::STATUS_UNPAY) {
            return redirect('/pay/result/' . $order_no);
        }

        $logTag = 'pay: ' . $order->pay_id;
        $payway = $order->pay;
        if (!$payway) {
            \Log::error($logTag . ' cannot find Pay');
            return $this->renderResultPage($request, ['msg' => '支付方式未找到']);
        }
        $logTag .= ',' . $payway->driver;

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;
        $config['out_trade_no'] = $order_no;

        try {
            $this->payApi = GatewayPay::getDriver($payway);
        } catch (\Exception $e) {
            \Log::error($logTag . ' cannot find Driver: ' . $e->getMessage());
            return $this->renderResultPage($request, ['msg' => '支付驱动未找到']);
        }
        // 如果已经支付成功, 直接返回
        if ($this->payApi->verify($config, function ($order_no, $amount, $trade_no) use ($request) {
            try {
                $this->shipOrder($request, $order_no, $amount, $trade_no);
            } catch (\Exception $e) {
                $this->renderResultPage($request, [
                    'success' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        })) {
            \Log::notice($logTag . ' already success' . "\n\n");
            return redirect('/pay/result/' . $order_no);
        }

        // 超过 5 分钟就不准重新进入页面了, 但是已经在页面的是不影响支付的
        if ($order->created_at < Carbon::now()->addMinutes(-5)) {
            return $this->renderResultPage($request, ['msg' => '当前订单长时间未支付已作废, 请重新下单']);
        }

        $product = Product::where('id', $order->product_id)
            ->where('enabled', 1)
            ->first();
        if ($product == null) {
            return self::renderResultPage($request, ['msg' => '该商品已下架']);
        }

        $product->setAttribute('count', count($product->cards) ? $product->cards[0]->count : 0);

        if ($product->count < $order->count) {
            return self::renderResultPage($request, ['msg' => '该商品库存不足']);
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
            return self::renderResultPage($request, ['msg' => '订单未找到，请重试']);
        }
        if ($order->product_id !== \App\Product::ID_API && $order->product == null) {
            return self::renderResultPage($request, ['msg' => '商品未找到，请重试']);
        }
        return view('pay/' . $pay_file, [
            'pay_id' => $order->pay_id,
            'name' => $order->product->name . ' x ' . $order->count . '件',
            'amount' => $order->paid,
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
        /** @var \App\Pay $payway */
        $payway = \App\Pay::where('id', $pay_id)->first();
        if (!$payway) {
            return $this->renderResultPage($request, [
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
                    return self::renderResultPage($request, [
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
            $this->payApi = GatewayPay::getDriver($payway);
        } catch (\Exception $e) {
            \Log::error($logTag . ' cannot find Driver: ' . $e->getMessage());
            return $this->renderResultPage($request, [
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
                $this->shipOrder($request, $order_no, $amount, $trade_no);
                \Log::debug($logTag . ' shipOrder end, order_no: ' . $order_no);
            } catch (\Exception $e) {
                \Log::error($logTag . ' shipOrder Exception: ' . $e->getMessage());
            }
        })) {
            \Log::debug($logTag . ' verify finished: 1' . "\n\n");
            if ($request->ajax()) {
                return self::renderResultPage($request, [
                    'success' => 1,
                    'data' => '/pay/result/' . $out_trade_no  // (order_no)
                ]);
            } else {
                return redirect('/pay/result/' . $out_trade_no);  // (order_no)
            }
        } else {
            \Log::debug($logTag . ' verify finished: 0' . "\n\n");
            return $this->renderResultPage($request, [
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

        /** @var \App\Pay $payway */
        $payway = \App\Pay::where('id', $pay_id)->first();
        if (!$payway) {
            \Log::error($logTag . ' cannot find PayModel');
            echo 'fail';
            exit;
        }
        $logTag .= ',' . $payway->driver;
        try {
            $this->payApi = GatewayPay::getDriver($payway);
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
                $this->shipOrder($request, $order_no, $amount, $trade_no);
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
        /** @var \App\Order $order */
        $order = \App\Order::where('order_no', $order_no)->first();
        if ($order == null) {
            return self::renderResultPage($request, ['msg' => '订单未找到，请重试']);
        }

        if ($order->status === \App\Order::STATUS_PAID) {
            // 处于已支付 (不是 SUCCESS) , 说明没货了
            $qq = $order->user->qq;
            if ($order->product->delivery === \App\Product::DELIVERY_MANUAL) {
                $msg = '您购买的为手动充值商品，请耐心等待处理';
            } else {
                $msg = '商家库存不足，因此没有自动发货，请联系商家客服发货';
            }
            if ($qq) {
                $msg .= '<br><a href="http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes" target="_blank">客服QQ:' . $qq . '</a>';
            }
            return self::renderResultPage($request, [
                'success' => false,
                'title' => '订单已支付',
                'msg' => $msg
            ], [
                'order' => $order
            ]);
        } elseif ($order->status === \App\Order::STATUS_SUCCESS) {
            // 说明已经 notify 过了, 订单已经成功, 直接取出card 渲染页面
            return self::showOrderResult($request, $order);
        }

        return self::renderResultPage($request, [
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
    function renderResultPage(Request $request, $result, $append = [])
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
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function shipOrder($request, $order_no, $amount, $trade_no)
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

        /** @var \App\Product $product */
        $product = null;

        if ($order->status === \App\Order::STATUS_UNPAY) {
            //未处理过此订单
            Log::debug('shipOrder.first_process:' . $order_no);
            if (FundHelper::orderSuccess($order->id,
                /**
                 * 此函数在 transition里工作
                 * 这个函数做了以下工作
                 * Order -> STATUS_PAID or STATUS_SUCCESS
                 * Product 和 Card -> sold + 1
                 * 返回 STATUS_PAID or STATUS_SUCCESS
                 * @param \App\Order $locked_order
                 * @return int 返回 CONTINUE 会加余额等等操作
                 */
                function ($locked_order) use ($trade_no, &$order, &$product) {
                    $order = $locked_order;
                    if ($order->status !== \App\Order::STATUS_UNPAY) {
                        \Log::debug('Shop.Pay.shipOrder: .first_process:' . $order->order_no . ' already processed! #2');
                        return false;
                    }

                    /** @var \App\Product $product */
                    $product = $order->product()->lockForUpdate()->firstOrFail();

                    $order->pay_trade_no = $trade_no; // 支付通道订单号
                    $order->paid_at = Carbon::now();

                    if ($product->delivery === \App\Product::DELIVERY_MANUAL) {
                        // 手动发货的商品, 这里标记为已支付待发货, 然后返回
                        $order->status = \App\Order::STATUS_PAID;
                        $order->send_status = \App\Order::SEND_STATUS_CARD_UN;
                        $order->saveOrFail();
                        return true;
                    }

                    // API接口发货
                    if ($product->delivery === \App\Product::DELIVERY_API) {
                        // 这里通过API接口生成卡密
                        $cards = $product->createApiCards($order);
                    }else{
                        /** @var \App\Card[] $cards */
                        $cards = Card::where('product_id', $order->product_id)
                            ->whereRaw('`count_sold`<`count_all`')
                            ->take($order->count)->lockForUpdate()->get();
                    }
                    /** @var bool 卡密是否足够 */
                    $cards_enough = false;
                    if (count($cards) === $order->count) {
                        $cards_enough = true;
                    } else {
                        $cards_enough = false;
                        if (count($cards)) {
                            foreach ($cards as $card) {
                                if ($card->type === \App\Card::TYPE_REPEAT && $card->count >= $order->count) {
                                    // 重复售卖的卡密, 单次只找一张满足数量的卡密
                                    $cards = [$card];
                                    $cards_enough = true;
                                    break;
                                }
                            }
                        }
                    }
                    if ($cards_enough === false) {
                        Log::alert('Shop.Pay.shipOrder: 订单:' . $order->order_no .
                            ', 购买数量:' . $order->count .
                            ', 卡数量:' . count($cards) . ' 卡密不足(已支付 未发货)');

                        // 库存不足了, GG 已支付 未发货
                        $order->status = \App\Order::STATUS_PAID;
                        $order->saveOrFail();
                        return true;
                        // 这里需要商户到后台手动发货
                    } else {
                        $card_ids = [];
                        foreach ($cards as $card) {
                            $card_ids[] = $card->id;
                        }
                        $order->cards()->attach($card_ids); // 使用了 card_order 这个中间表
                        if (count($cards) === 1 && $cards[0]->type === \App\Card::TYPE_REPEAT) {
                            \App\Card::where('id', $card_ids[0])->update([
                                'status' => \App\Card::STATUS_SOLD,
                                'count_sold' => DB::raw('`count_sold`+' . $order->count)
                            ]);
                        } else {
                            \App\Card::whereIn('id', $card_ids)->update([
                                'status' => \App\Card::STATUS_SOLD,
                                'count_sold' => DB::raw('`count_sold`+1')
                            ]);
                        }

                        $order->status = \App\Order::STATUS_SUCCESS;
                        $order->saveOrFail();

                        $product->count_sold += $order->count;
                        $product->saveOrFail();

                        return FundHelper::ACTION_CONTINUE;
                    }
                })
            ) {
                // ------------------------------------------------------------------------ 提醒库存 start
                // \Log::debug('shipOrder.count_warn step1, count:' . $count . ', warn:' . $product->count_warn);
                if ($product->count_warn > 0 && $product->count < $product->count_warn) {
                    try {
                        // \Log::debug('shipOrder.count_warn step2');
                        Mail::to($order->user->email)->Queue(new ProductCountWarn($product, $product->count));
                        // \Log::debug('shipOrder.count_warn step3, email:' . $order->user->email);
                    } catch (\Throwable $e) {
                        // set to mail
                        LogHelper::setLogFile('mail');
                        Log::error('shipOrder.count_warn error', [
                            'product_id' => $order->product_id,
                            'email' => $order->user->email,
                            'exception' => $e->getMessage()
                        ]);
                        // 还原
                        LogHelper::setLogFile('card');
                    }
                }
                // ------------------------------------------------------------------------ 提醒库存 end

                // 需要发货到邮箱
                if (System::_getInt('mail_send_order')) {
                    $mail = @json_decode($order->contact_ext, true)['_mail'];
                    if($mail){
                        $order->sendEmail($mail);
                    }
                }

                // 需要发送到手机
                if ($order->status === \App\Order::STATUS_SUCCESS && System::_getInt('sms_send_order')) {
                    $mobile = @json_decode($order->contact_ext, true)['_mobile'];
                    if($mobile){
                        $order->sendSms($mobile);
                    }
                }
            } else {
                // 数据库报错 or 已经处理过(status !== UN_PAY)
                // 数据库报错的话 orderSuccess 里面写过详细log了
                // throw new \Exception('merchant operate exception!');
            }

        } else {
            Log::debug('Shop.Pay.shipOrder: .order_no:' . $order->order_no . ' already processed! #1');
        }
        return FALSE;
    }

    /**
     * 渲染已经支付的订单结果页面
     * @param Request $request
     * @param \App\Order $order
     * @return mixed
     * @throws \Throwable
     */
    private function showOrderResult($request, $order)
    {
        return self::renderResultPage($request, [
            'success' => true,
            'msg' => $order->getSendMessage(),
        ], [
            'card_txt' => join('&#013;&#010;', $order->getCardsArray()),
            'order' => $order,
            'product' => $order->product
        ]);
    }
}
