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

    /** @var int 正常可用状态 */
    const STATUS_NORMAL = 0;
    /** @var int 标记一下, 已经卖过 */
    const STATUS_SOLD = 1;
    /** @var int 暂未用到此状态 */
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

    function getCountAttribute()
    {
        return $this->count_all - $this->count_sold;
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

    /**
     * 删除卡密
     * @param \Illuminate\Database\Eloquent\Builder|\App\Card $query
     * @throws \Throwable
     */
    public static function _trash($query)
    {
        DB::transaction(function () use ($query) {
            $tmp = clone $query;
            $tmp->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count_left`')
                ->groupBy('product_id')->orderByRaw('`product_id`')
                ->chunk(100, function ($card_groups) {
                    foreach ($card_groups as $card_group) {
                        /** @var \App\Product $product */
                        $product = \App\Product::where('id', $card_group->product_id)->lockForUpdate()->first();
                        if ($product) {
                            $product->count_all -= $card_group->count_left;
                            $product->saveOrFail();
                        }
                    }
                });
            $query->delete();
            return true;
        });
    }

    /**
     * 恢复卡密
     * @param \Illuminate\Database\Eloquent\Builder|\App\Card $query
     * @throws \Throwable
     */
    public static function _restore($query)
    {
        DB::transaction(function () use ($query) {
            $tmp = clone $query;
            $tmp->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count_left`')
                ->groupBy('product_id')->orderByRaw('`product_id`')
                ->chunk(100, function ($card_groups) {
                    foreach ($card_groups as $card_group) {
                        /** @var \App\Product $product */
                        $product = \App\Product::where('id', $card_group->product_id)->lockForUpdate()->first();
                        if ($product) {
                            $product->count_all += $card_group->count_left;
                            $product->saveOrFail();
                        }
                    }
                });
            $query->restore();
            return true;
        });
    }
}
