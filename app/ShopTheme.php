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
            static::$default_theme = ShopTheme::query()->where('name', \App\System::_get('shop_theme_default', 'Material'))->first();
            if (!static::$default_theme) {
                static::$default_theme = ShopTheme::query()->firstOrFail();
            }
        }
        return static::$default_theme;
    }

    public static function freshList()
    {
        $path = realpath(app_path(
            '..' . DIRECTORY_SEPARATOR .
            'resources' . DIRECTORY_SEPARATOR .
            'views' . DIRECTORY_SEPARATOR .
            'shop_theme'));
        foreach (scandir($path) as $theme_name) {
            if ($theme_name === '.' || $theme_name === '..') continue;
            if (!\App\ShopTheme::query()->where('name', $theme_name)->exists()) {
                @$theme = include($path . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'config.php');
                if ($theme && isset($theme['description'])) {
                    \App\ShopTheme::query()->insert([
                        'name' => $theme_name,
                        'description' => $theme['description'],
                        'config' => json_encode(@$theme['config'])
                    ]);
                }

            }
        }
    }
}
