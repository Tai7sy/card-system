<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Pay
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $driver
 * @property string $config
 * @property int $enabled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereUpdatedAt($value)
 * @property string $img
 * @property string $way
 * @property string|null $comment
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pay whereWay($value)
 */
class Pay extends Model
{
    const ENABLED_DISABLED = 0;
    const ENABLED_PC = 1;
    const ENABLED_MOBILE = 2;
    const ENABLED_ALL = 3;
}
