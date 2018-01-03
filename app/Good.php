<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Good
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $sold_count
 * @property int $all_count
 * @property int $price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Group $group
 * @property \App\Card[] $cards
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereAllCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereSoldCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereUpdatedAt($value)
 * @property int $group_id
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereStatus($value)
 * @property int $enabled
 * @property int $left_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Good whereEnabled($value)
 */
class Good extends Model
{
    public function group(){
        return $this->belongsTo(\App\Group::class);
    }

    public function cards()
    {
        return $this->hasMany(\App\Card::class);
    }

    public function getLeftCountAttribute(){
        return $this->all_count - $this->sold_count;
    }

}
