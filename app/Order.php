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
     * @return string
     */
    function getSendMessage()
    {
        // 發了貨的
        if (count($this->cards)) {

            // 正常情況, 發貨數量 == 訂單購買數量
            if (count($this->cards) == $this->count) {
                $card_msg = '訂單#' . $this->order_no . '&nbsp;已支付，您購買的內容如下：';
            } else {
                // 如果數量不對
                if (
                    $this->cards[0]->type === \App\Card::TYPE_REPEAT ||  // 卡密是重復賣的, 發少了正常, 這種情況 count($this->cards) === 1
                    @$this->product->delivery === \App\Product::DELIVERY_MANUAL // 手動發貨(手動發貨的時候, 卡密可能只是一段話, 買多個發同一個)
                ) {
                    // 重復賣 or 手動發貨
                    $card_msg = '訂單#' . $this->order_no . '&nbsp;已支付，您購買的內容如下：';
                } else {
                    // 發貨了一部分 (應該不會到這里, 卡密不夠不會發貨)
                    $card_msg = '訂單#' . $this->order_no . '&nbsp;已支付，目前庫存不足，您還有' . ($this->count - count($this->cards)) . '件未發貨，請聯系商家客服發貨，';
                    $card_msg .= '商家客服：' . $this->user->qq . '<br>';
                    $card_msg .= '已發貨商品見下方：';
                }
            }
        } else {
            $card_msg = '訂單#' . $this->order_no . '&nbsp;已支付，目前庫存不足，您購買的' . ($this->count - count($this->cards)) . '件未發貨，請聯系商家客服發貨<br>';
            $card_msg .= '商家客服：' . $this->user->qq;
        }
        return $card_msg;
    }

    function sendEmail($mail = false)
    {
        if ($mail === false) {
            $mail = @json_decode($this->contact_ext)['_mail'];
        }
        if (!$mail || !@filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $cards = $this->getCardsArray();

        try {
            Mail::to($mail)->Queue(new OrderShipped($this, $this->getSendMessage(), join('<br>', $cards)));
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
        if ($mobile === false) {
            $mobile = @json_decode($this->contact_ext)['_mobile'];
        }
        if (!$mobile || strlen($mobile) !== 11) {
            return;
        }
        OrderSms::dispatch($mobile, $this);
    }
}
