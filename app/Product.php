<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log as LogWriter;


class Product extends Model
{
    protected $guarded = [];
    protected $hidden = [];

    const ID_API = -1001;
    /** @var int 自动发货 */
    const DELIVERY_AUTO = 0;
    /** @var int 手动发货 */
    const DELIVERY_MANUAL = 1;
    /** @var int API接口发货 */
    const DELIVERY_API = 2;

    function getUrlAttribute()
    {
        return config('app.url') . '/p/' . Helper::id_encode($this->id, Helper::ID_TYPE_PRODUCT);
    }

    function getCountAttribute()
    {
        // return count($this->cards) ? $this->cards[0]->count : 0;
        return $this->count_all - $this->count_sold;
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function cards()
    {
        return $this->hasMany(Card::class);
    }

    function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function refreshCount($user)
    {
        \App\Card::where('user_id', $user->id)
            ->selectRaw('`product_id`,SUM(`count_sold`) as `count_sold`,SUM(`count_all`) as `count_all`')
            ->groupBy('product_id')->orderByRaw('`product_id`')
            ->chunk(1000, function ($card_groups) {
                foreach ($card_groups as $card_group) {
                    $product = \App\Product::where('id', $card_group->product_id)->first();
                    if($product){
                        if ($product->delivery === \App\Product::DELIVERY_MANUAL) {
                            // 手动发货, count_all 手动修改, 这里不同步
                            $product->update([
                                'count_sold' => $card_group->count_sold
                            ]);
                        } else {
                            $product->update([
                                'count_sold' => $card_group->count_sold,
                                'count_all' => $card_group->count_all,
                            ]);
                        }
                    }else{
                     // 可能卡密对应的商品已经删除
                    }
                }
            });
    }

    /**
     * 商品发货类型: API发货, 通过请求API接口生成卡密
     * @param \App\Order $order
     * @return \App\Card[]
     */
    function createApiCards($order)
    {


        $cards_txt = [];
        $cards_sql = [];
        $cards_attributes = [];
        for ($i = 0; $i < $order->count; $i++) {
            $cards_txt[] = strtoupper(str_random(16));
            $card_addtime = date('Y-m-d H:i:s');

            switch ($this->id) {
                case 6:
                    $card_task = 1; // 正式码
                    break;
                case 11:
                    $card_task = 2; // 更新码
                    break;
                case 37:
                    $card_task = 3; // 重签码
                    break;
                default:
                    die('App.Products fatal error#1');
            }

            $cards_attributes[] = [
                'user_id' => $this->user_id,
                'product_id' => $this->id,
                'card' => $cards_txt[$i],
                'type' => \App\Card::TYPE_ONETIME,
                'status' => \App\Card::STATUS_NORMAL,
                'count_sold' => 0,
                'count_all' => 1
            ];
            $cards_sql[] = "(NULL, '{$cards_txt[$i]}', '1', '{$card_task}', NULL, NULL, NULL, NULL, NULL, '0', '{$card_addtime}', '0000-00-00 00:00:00')";
        }
        $s = mysqli_connect('localhost', 'udiddz', 'tRihPm3sh6yKedtX', 'udiddz', '3306');
        $sql = "INSERT INTO `udiddz`.`ac_kms` (`id`, `km`, `value`, `task`, `udid`, `diz`, `task_id`, `install_url`, `plist_url`, `jh`, `addtime`, `tjtime`) VALUES " . join(',', $cards_sql);
        $res = mysqli_query($s, $sql);
        if (!$res) {
            LogWriter::error('App.Products, connect udid database failed', ['sql' => $sql, 'error' => mysqli_error($s)]);
            return [];
        }

        $this->count_all += $order->count;
        return $this->cards()->createMany($cards_attributes);
    }

    /**
     * 设置一些属性, 为了商铺页面做准备
     * @param User $user 传递一个user 复用, 不然每次都要获取
     */
    function setForShop($user = null)
    {
        $product = $this;
        $count = $product->count;

        $inventory = $product->inventory;
        if ($inventory == User::INVENTORY_AUTO) {
            $inventory = System::_getInt('shop_inventory');
        }

        if ($inventory == User::INVENTORY_RANGE) {
            if ($count <= 0) {
                $count2 = '不足';
            } elseif ($count <= 10) {
                $count2 = '少量';
            } elseif ($count <= 20) {
                $count2 = '一般';
            } else {
                $count2 = '大量';
            }
            $product->setAttribute('count2', $count2);
        } else {
            $product->setAttribute('count2', $count); // 页面展示的数量
        }
        $product->setAttribute('count', $count); // 真正的数量

        $product->setVisible(['id', 'name', 'description', 'fields', 'delivery', 'count', 'count2', 'buy_min', 'buy_max',
            'support_coupon', 'password_open', 'price', 'price_whole']);
    }
}
