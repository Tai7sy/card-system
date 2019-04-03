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
}
