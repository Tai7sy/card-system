<?php

namespace App;

use App\Jobs\OrderSms;
use App\Library\LogHelper;
use App\Mail\OrderShipped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;


class Order extends Model
{
    protected $guarded = [];

    /** @var int 未支付 */
    const STATUS_UNPAY = 0;
    /** @var int 已付款未发货 */
    const STATUS_PAID = 1;
    /** @var int 已发货成功 */
    const STATUS_SUCCESS = 2;
    /** @var int 已冻结(未付款冻结/已付款冻结) */
    const STATUS_FROZEN = 3;
    /** @var int 已退款 */
    const STATUS_REFUND = 4;

    const STATUS = [
        0 => '未支付',
        1 => '未发货',
        2 => '已发货',
        3 => '已冻结',
        4 => '已退款',
    ];

    /** @var int 发货后, 通知状态, 用于 邮件/手机 通知 */
    const SEND_STATUS_UN = 0;
    const SEND_STATUS_EMAIL_SUCCESS = 1;
    const SEND_STATUS_EMAIL_FAILED = 2;
    const SEND_STATUS_MOBILE_SUCCESS = 3;
    const SEND_STATUS_MOBILE_FAILED = 4;

    /** @var int 手动发货 的备注字段 */
    const SEND_STATUS_CARD_UN = 100;
    const SEND_STATUS_CARD_PROCESSING = 101;
    const SEND_STATUS_CARD_SUCCESS = 102;
    const SEND_STATUS_CARD_FAILED = 103;

    protected $casts = [
        'api_info' => 'array',
    ];

    public static function unique_no()
    {
        $order_no = date('YmdHis') . str_random(5);
        while (\App\Order::where('order_no', $order_no)->exists()) {
            $order_no = date('YmdHis') . str_random(5);
        }
        return $order_no;
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function product()
    {
        return $this->belongsTo(Product::class);
    }

    function pay()
    {
        return $this->belongsTo(Pay::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    function cards()
    {
        /* @var \App\Card $builder */
        $builder = $this->belongsToMany(Card::class);
        return $builder->withTrashed();
    }

    function card_orders()
    {
        return $this->hasMany(CardOrder::class);
    }

    function fundRecord()
    {
        return $this->hasMany(FundRecord::class);
    }

    /**
     * 获取纯卡密的列表
     * @return array ['卡密1','卡密2']
     */
    function getCardsArray()
    {
        $cards = [];
        $this->cards->each(function ($card) use (&$cards) {
            /** @var \App\Card $card */
            $cards[] = $card->card;
        });
        return $cards;
    }

    /**
     * 获取发货的提示
     * @return string
     */
    function getSendMessage()
    {
        // 若发货的卡密少于购买的数量 且 不是手动发货(手动发货的时候, 卡密可能只是一段话, 买多个发同一个)
        if (count($this->cards) < $this->count && @$this->product->delivery !== \App\Product::DELIVERY_MANUAL) {
            // Tip:
            // 卡密若包含可以重复使用, 则商品限购一件
            // 即: $order->count === 1
            if (count($this->cards)) {
                // 应该不会到这个分支, 因为检测到卡密不够不会发货
                $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，目前库存不足，您还有' . ($this->count - count($this->cards)) . '件未发货，请联系商家客服发货<br>已发货商品见下方：<br>';
            } else {
                $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，目前库存不足，您购买的' . ($this->count - count($this->cards)) . '件未发货，请联系商家客服发货<br>';
            }
            $qq = $this->user->qq;
            if ($qq) {
                $card_msg .= '<a href="http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes" target="_blank">商家客服QQ:' . $qq . '</a><br>';
            }
        } else {
            $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，您购买的内容如下：';
        }

        return $card_msg;
    }

    function sendEmail()
    {
        $cards = $this->getCardsArray();

        try {
            Mail::to($this->contact)->Queue(new OrderShipped($this, $this->getSendMessage(), join('<br>', $cards)));
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_SUCCESS;
            $this->saveOrFail();
        } catch (\Throwable $e) {
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_FAILED;
            $this->saveOrFail();
            // set to mail
            LogHelper::setLogFile('mail');
            Log::error('Order.sendEmail error', [
                'order_no' => $this->order_no,
                'email' => $this->contact,
                'cards' => $cards,
                'exception' => $e->getMessage()
            ]);
            // 还原
            LogHelper::setLogFile('card');
        }
        return FALSE;

    }


    function sendSms()
    {
        OrderSms::dispatch($this);
    }
}
