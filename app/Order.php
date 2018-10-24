<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $guarded = [];

    const STATUS_UNPAY = 0;
    const STATUS_PAID = 1;    // 已付款未发货
    const STATUS_SUCCESS = 2; // 已发货成功
    const STATUS_FROZEN = 3;
    const STATUS_REFUND = 4;

    protected $casts = [
        'api_info' => 'array',
    ];


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
}
