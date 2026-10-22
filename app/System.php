<?php

namespace App;

use App\Model_ as Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class System extends Model
{
    protected $guarded = [];


    private static $systems = [];

    public static function _init()
    {
        // Log::debug('SystemSetting._init');
        static::$systems = Cache::remember('settings.all', 10 * 60, function () {
            // Log::debug('SystemSetting._init.fetch from database');
            $systems = self::query()->get()->toArray();
            $values = [];
            foreach ($systems as $system) {
                $values[$system['name']] = $system['value'];
            }
            return $values;
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
                System::query()->create([
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
        // Log::debug('SystemSetting.flushCache');
        Cache::forget('settings.all');
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

        static::deleted(function () {
            self::flushCache();
        });
    }
}
