<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Card extends Model
{
    protected $guarded = [];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    const STATUS_NORMAL = 0;
    const STATUS_SOLD = 1;
    const STATUS_USED = 2;

    const TYPE_ONETIME = 0;
    const TYPE_REPEAT = 1;


    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function product()
    {
        return $this->belongsTo(Product::class);
    }


    /**
     * @param int $user_id
     * @param int $product_id
     * @param int $type
     * @param int $status
     * @param string $card_arr
     * @param boolean $is_check 新增前检查是否存在
     */
    public static function add_cards($user_id, $product_id, $type, $status, $card_arr, $is_check)
    {
        DB::statement('call add_cards(?,?,?,?,?,?)', [$user_id, $product_id, $type, $status, $card_arr, (int)$is_check]);
    }
}
