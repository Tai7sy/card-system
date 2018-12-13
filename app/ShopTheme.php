<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopTheme extends Model
{
    protected $guarded = [];

    public $timestamps = false;


    private static $default_theme;

    /**
     * @return \App\ShopTheme
     */
    public static function defaultTheme()
    {
        if (!static::$default_theme) {
            static::$default_theme = ShopTheme::query()->where('is_default', true)->first();
            if (!static::$default_theme) {
                static::$default_theme = ShopTheme::query()->firstOrFail();
            }
        }
        return static::$default_theme;
    }
}
