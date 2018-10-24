<?php

namespace App;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $guarded = [];

    const ID_API = -1001;


    public static function id_encode($id)
    {
        $hashids = new Hashids(config('app.key'));
        return @$hashids->encode($id, 2);
    }

    public static function id_decode($en_id)
    {
        $hashids = new Hashids(config('app.key'));
        return @$hashids->decode($en_id)[0];
    }


    function getUrlAttribute(){
        return config('app.url') . '/p/' . self::id_encode($this->id);
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
