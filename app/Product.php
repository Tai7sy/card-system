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

    function getCountAttribute(){
        return count($this->cards) ? $this->cards[0]->count : 0;
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

}
