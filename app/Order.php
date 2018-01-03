<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Order
 *
 * @property int $id
 * @property string $order_no
 * @property int $good_id
 * @property int $count
 * @property string $email
 * @property int $email_sent
 * @property string|null $comment
 * @property int $amount
 * @property int $pay_id
 * @property string|null $pay_trade_no
 * @property int $paid
 * @property string|null $paid_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Card[] $cards
 * @property-read \App\Good $good
 * @property-read \App\Pay $pay
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereGoodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order wherePayTradeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{




    public function good()
    {
        return $this->belongsTo(\App\Good::class);
    }

    public function pay()
    {
        return $this->belongsTo(\App\Pay::class);
    }


    public function cards()
    {
        return $this->belongsToMany(\App\Card::class);
    }
}
