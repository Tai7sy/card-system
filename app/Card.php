<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Card
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $good_id
 * @property string $card
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereGoodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereUpdatedAt($value)
 * @property int $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Card whereType($value)
 * @property \App\Order[] $orders
 */
class Card extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_SOLD = 1;
    const STATUS_USED = 2;

    const TYPE_ONETIME = 0;
    const TYPE_REPEAT = 1;


    public function orders()
    {
        return $this->hasMany(\App\Order::class);
    }
}
