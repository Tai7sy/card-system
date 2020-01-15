<?php

namespace App;

use App\Jobs\OrderSms;
use App\Library\LogHelper;
use App\Mail\OrderShipped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log as LogWriter;

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
     * @param bool $is_mail
     * @return string
     */
    function getSendMessage($is_mail = false)
    {
        // 发了货的
        if (count($this->cards)) {

            // 正常情况, 发货数量 == 订单购买数量
            if (count($this->cards) == $this->count) {
                $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，您购买的内容如下：';
            } else {
                // 如果数量不对
                if (
                    $this->cards[0]->type === \App\Card::TYPE_REPEAT ||  // 卡密是重复卖的, 发少了正常, 这种情况 count($this->cards) === 1
                    @$this->product->delivery === \App\Product::DELIVERY_MANUAL // 手动发货(手动发货的时候, 卡密可能只是一段话, 买多个发同一个)
                ) {
                    // 重复卖 or 手动发货
                    $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，您购买的内容如下：';
                }else{
                    // 发货了一部分 (应该不会到这里, 卡密不够不会发货)
                    $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，目前库存不足，您还有' . ($this->count - count($this->cards)) . '件未发货，请联系商家客服发货<br>已发货商品见下方：<br>';
                }
            }
        }else{
            $card_msg = '订单#' . $this->order_no . '&nbsp;已支付，目前库存不足，您购买的' . ($this->count - count($this->cards)) . '件未发货，请联系商家客服发货<br>';
        }
        $card_msg .= '<a href="http://wpa.qq.com/msgrd?v=3&uin=' . $this->user->qq . '&site=qq&menu=yes" target="_blank">商家QQ:' . $this->user->qq . '</a>';
        if ($is_mail) {
            $card_msg .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . config('app.url') . route('pay.result', [$this->order_no]) . '" target="_blank">查看订单详情</a><br>';
        }
        return $card_msg;
    }

    function sendEmail($mail = false)
    {
        if($mail === false){
            $mail = @json_decode($this->contact_ext)['_mail'];
        }
        if(!$mail || !@filter_var($mail, FILTER_VALIDATE_EMAIL)){
            return;
        }
        $cards = $this->getCardsArray();

        try {
            Mail::to($mail)->Queue(new OrderShipped($this, $this->getSendMessage(true), join('<br>', $cards)));
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_SUCCESS;
            $this->saveOrFail();
        } catch (\Throwable $e) {
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_FAILED;
            $this->saveOrFail();
            // set to mail
            LogHelper::setLogFile('mail');
            LogWriter::error('Order.sendEmail error', [
                'order_no' => $this->order_no,
                'email' => $mail,
                'cards' => $cards,
                'exception' => $e->getMessage()
            ]);
            // 还原
            LogHelper::setLogFile('card');
        }
    }


    function sendSms($mobile = false)
    {
        if($mobile === false){
            $mobile = @json_decode($this->contact_ext)['_mobile'];
        }
        if(!$mobile || strlen($mobile) !== 11){
            return;
        }
        OrderSms::dispatch($mobile, $this);
    }
}
