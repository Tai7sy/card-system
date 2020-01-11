<?php

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;

class PayWay extends Model
{
    protected $guarded = [];

    protected $casts = [
        'channels' => 'array',
    ];
    const ENABLED_DISABLED = 0;
    const ENABLED_PC = 1;
    const ENABLED_MOBILE = 2;
    const ENABLED_ALL = 3;

    const TYPE_SHOP = 1;
    const TYPE_API = 2;

    /**
     * 获取一个支付方式
     * @return \App\Pay|null
     */
    public function getPayByWeight()
    {
        $count = $base = 0;
        $map = [];

        $ids = [];
        foreach ($this->channels as $item) {
            $ids[] = intval($item[0]);
        }
        /** @var \App\Pay[] $pays */
        $pays = \App\Pay::gets()->filter(function ($pay) use ($ids) {
            return in_array($pay->id, $ids);
        });
        $pays_map = [];
        foreach ($pays as $pay_model) {
            $pays_map[$pay_model->id] = $pay_model;
        }

        foreach ($this->channels as $item) {
            $pay_id = intval($item[0]);
            $weight = intval($item[1]);

            // 只要 enabled 的支付方式
            if ($weight && $pays_map[$pay_id]->enabled > 0) {
                $count += $weight;
                $max = $base + $weight;
                $map[] = [
                    'min' => $base,
                    'max' => $max,
                    'pay_id' => $pay_id,
                ];
                $base = $max;
            }
        }
        if ($count <= 0) {
            return null;
        }

        // 生成一个随机数
        $rand = mt_rand(0, $count - 1);
        foreach ($map as $current) {
            if ($current['min'] <= $rand && $rand < $current['max']) {
                return $pays_map[$current['pay_id']];
            }
        }
        return null;
    }

    /**
     * 前台支付时, 拉取支付方式, 这里就开始计算
     * @param \App\User $user
     * @param \Closure|null $closure 传递名称了, 说明是API支付
     * @return \App\Pay[]
     */
    public static function gets($user, $closure = null)
    {
        /** @var \App\PayWay[] $all */
        $query = self::query();
        if ($closure !== null) $query->where($closure);
        $all = $query->orderBy('sort')->get(['name', 'img', 'channels']);

        /** @var \App\Pay[] $result_pays */
        $result_pays = [];
        foreach ($all as $pay_way) {
            // 这里随机从channel里面选一个
            $pay = $pay_way->getPayByWeight();
            if ($pay) {
                $pay->setAttribute('name', $pay_way->name);
                $pay->setAttribute('img', $pay_way->img);
                $pay->setVisible(['id', 'name', 'img', 'fee']);
                $result_pays[] = $pay;
            }
        }
        return $result_pays;
    }
}
