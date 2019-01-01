<?php

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    function getUrlAttribute()
    {
        return config('app.url') . '/c/' . Helper::id_encode($this->id, Helper::ID_TYPE_CATEGORY);
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }


}
