<?php

namespace App;

use App\Model_ as Model;
use Illuminate\Support\Facades\Cache;

class Pay extends Model
{
    protected $guarded = [];

    /**
     * 获取所有支付方式, 带缓存
     * @return \Illuminate\Database\Eloquent\Collection|\App\Pay[]
     */
    public static function gets()
    {
        // Log::debug('Model.Pay.gets');
        return Cache::remember('model.pays', 10 * 60, function () {
            // Log::debug('Model.Pay.gets fetch from database');
            return self::query()->get();
        });
    }
    /**
     * Flush the cache
     */
    public static function flushCache()
    {
        // Log::debug('Model.Pay.flushCache');
        Cache::forget('model.pays');
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
