<?php

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $guarded = [];

    const ID_API = -1001;


    function getUrlAttribute(){
        return config('app.url') . '/p/' . Helper::id_encode($this->id,Helper::ID_TYPE_PRODUCT);
    }

    function getCountAttribute()
    {
        // return count($this->cards) ? $this->cards[0]->count : 0;
        return $this->count_all - $this->count_sold;
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function cards()
    {
        return $this->hasMany(Card::class);
    }

    function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function refreshCount($user)
    {
        \App\Card::where('user_id', $user->id)
            ->selectRaw('`product_id`,SUM(`count_sold`) as `count_sold`,SUM(`count_all`) as `count_all`')
            ->groupBy('product_id')->orderByRaw('`product_id`')
            ->chunk(100, function ($card_groups) {
                foreach ($card_groups as $card_group) {
                    \App\Product::where('id', $card_group->product_id)->update([
                        'count_sold' => $card_group->count_sold,
                        'count_all' => $card_group->count_all,
                    ]);
                }
            });
    }

    /**
     * 设置一些属性, 为了商铺页面做准备
     */
    function setForShop(){
        $product = $this;
        $count = $product->count;
        if ($product->user->shop_inventory == User::inventory_range) {
            if ($count <= 0) {
                $count2 = '不足';
            } elseif ($count <= 10) {
                $count2 = '少量';
            } elseif ($count <= 20) {
                $count2 = '一般';
            } else {
                $count2 = '大量';
            }
            $product->setAttribute('count2', $count2);
        } else {
            $product->setAttribute('count2', $count); // 页面展示的数量
        }
        $product->setAttribute('count', $count); // 真正的数量

        $product->setVisible(['id', 'name', 'description', 'count', 'count2', 'buy_min', 'buy_max',
            'support_coupon', 'password_open', 'price', 'price_whole']);
    }
}
