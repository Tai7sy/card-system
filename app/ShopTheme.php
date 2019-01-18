<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopTheme extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'config' => 'array'
    ];

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
        \App\ShopTheme::query()->get()->each(function ($theme) use ($path) {
            /**
             * @var \App\ShopTheme $theme
             */
            if (!file_exists($path . DIRECTORY_SEPARATOR . $theme->name . DIRECTORY_SEPARATOR . 'config.php')) {
                $theme->delete();
            }
        });
        foreach (scandir($path) as $theme_name) {
            if ($theme_name === '.' || $theme_name === '..') continue;
            if (!\App\ShopTheme::query()->where('name', $theme_name)->exists()) {
                try {
                    @$theme = include($path . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'config.php');
                } catch (\Exception $e) {
                    continue;
                }
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
