<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $guarded = [];


    private static $systems = [];

    public static function _init()
    {
        \Log::debug('SystemSetting._init');
        static::$systems = \Cache::rememberForever('settings.all', function () {
            \Log::debug('SystemSetting._init.fetch from database');
            $systems = System::query()->get()->toArray();
            foreach ($systems as $system) {
                static::$systems[$system['name']] = $system['value'];
            }
            return static::$systems;
        });
        static::$systems['_initialized'] = true;
    }

    /**
     * @param $name
     * @param null $default
     * @return string
     */
    public static function _get($name, $default = NULL)
    {
        if (!isset(static::$systems['_initialized'])) {
            static::_init();
        }
        if (isset(static::$systems[$name]))
            return static::$systems[$name];
        return $default;
    }

    public static function _getInt($name, $default = NULL)
    {
        return (int)static::_get($name, $default);
    }

    /**
     * @param $name
     * @param $value
     */
    public static function _set($name, $value)
    {
        static::$systems[$name] = $value;
        $v = System::query()->where('name', $name)->first();
        if ($v) {
            $v->value = $value;
            $v->save();
        } else {
            try {
                System::query()->insert([
                    'name' => $name,
                    'value' => $value
                ]);
            } catch (\Exception $e) {

            }
        }
        self::flushCache();
    }

    /**
     * Flush the cache
     */
    public static function flushCache()
    {
        \Log::debug('SystemSetting.flushCache');
        \Cache::forget('settings.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });
    }
}
