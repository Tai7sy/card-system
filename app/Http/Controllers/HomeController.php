<?php

namespace App\Http\Controllers;

use App\Category;
use App\Pay;
use App\Product;
use App\System;
use App\User;
use App\UserDomain;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Library\Geetest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    /**
     * 店铺
     * 通过用户匹配店铺
     * @param \App\User $user
     * @param \App\Category|null $category
     * @param \App\Product|null $product
     * @return \Illuminate\Http\Response
     */
    private function _shop_render($user, $category = null, $product = null)
    {
        $config = [
            'url' => config('app.url'),
            'company' => System::_get('company'),
            'name' => config('app.name'),
            'logo' => System::_get('logo')
        ];
        $config['shop'] = [
            'qq' => System::_get('shop_qq'),
            'ann' => System::_get('shop_ann'),
            'ann_pop' => System::_get('shop_ann_pop'),
            'bkg' => System::_get('shop_bkg'),
            'inventory' => System::_getInt('shop_inventory')
        ];


        if ($product) {
            // 如果是产品连接
            $config['categories'] = [$category];
            $config['product'] = $product;
            $name = $product->name . ' - ' . $config['name'];
            $matches = [];
            preg_match_all('/"insert":"(.+?)"/', $product->description, $matches);
            $description = str_replace('\n', ' ', @join(' ', $matches[1]));
        } elseif ($category) {
            // 如果是分类连接
            $config['categories'] = [$category];
            $config['product'] = null;
            $name = $category->name . ' - ' . $config['name'];
            $description = $category->name;
        } else {
            // 啥也不是. 默认的店铺连接
            $categories = Category::where('user_id', $user->id)
                ->orderBy('sort')
                ->where('enabled', 1)
                ->get();

            foreach ($categories as $category) {
                $category->setVisible(['id', 'name', 'password_open']);
            }

            $config['categories'] = $categories;

            $name = $config['name'];
            $description = $config['shop']['ann'];
        }


        $config['vcode'] = [
            'driver' => System::_get('vcode_driver'),
            'buy' => (int)System::_get('vcode_shop_buy'),
            'search' => (int)System::_get('vcode_shop_search')
        ];
        if ($config['vcode']['driver'] === 'geetest') {
            $config['vcode']['geetest'] = Geetest\API::get();
        }

        // bit operate   pc & mobile = result_enabled
        $config['pays'] = Pay::whereRaw('enabled&' .
            (self::is_mobile() ? Pay::ENABLED_MOBILE : Pay::ENABLED_PC) . '!=0')
            ->orderBy('sort')
            ->get(['id', 'name', 'img']);


        $customer = Cookie::get('customer');
        $cookie = Cookie::make('customer',
            strlen($customer) !== 32 ? md5(str_random(16)) : $customer, 43200); // 30 days


        $template_id = $user->shop_template ? $user->shop_template->id : 0;

        return response()->view('shop_template.' . $template_id . '.index', [
            'name' => $name,
            'keywords' => preg_replace('/[、，；。！？]/', ', ', $name),
            'description' => $description,
            'js_tj' => System::_get('js_tj'),
            'js_kf' => System::_get('js_kf'),
            'config' => $config
        ])->cookie($cookie);
    }


    private function _shop_404()
    {
        $this->checkIsInMaintain();

        return view('message', [
            'title' => '404 NotFound',
            'message' => '该链接不存在<br>
<a style="font-size: 18px" href="/#/record">查询订单</a>'
        ]);
    }

    /**
     * 店铺
     * 通过 /shop/{en_id} 匹配店铺
     * @param $en_id
     * @return \Illuminate\Http\Response
     */
    public function shop_user($en_id)
    {
        $this->checkIsInMaintain();

        /* @var \App\User $user */
        $user = User::find(\App\UserDomain::id_decode($en_id));
        if (!$user) {
            // Warning
            // 2019-01-01 之后的用户将无法使用 id 的形式, 转变为 en_id
            $user = User::where('created_at', '<', \Carbon\Carbon::createFromDate(2019, 1, 1))
                ->find($en_id);
        }
        if (!$user) {
            return $this->_shop_404();
        }
        return $this->_shop_render($user);
    }

    public function shop_category($en_category_id)
    {
        $this->checkIsInMaintain();

        $category = Category::whereId(\App\Category::id_decode($en_category_id))->with('user')->first();
        if (!$category) {
            // Warning
            // 2019-01-01 之后的用户将无法使用 id 的形式, 转变为 en_id
            $category = Category::whereId($en_category_id)
                ->where('created_at', '<', \Carbon\Carbon::createFromDate(2019, 1, 1))
                ->with('user')->first();
        }
        if (!$category) {
            return $this->_shop_404();
        }

        $category->setVisible(['id', 'name', 'password_open']);
        return $this->_shop_render($category->user, $category);
    }

    public function shop_product(Request $request, $en_product_id)
    {
        $this->checkIsInMaintain();

        $product = Product::whereId(\App\Product::id_decode($en_product_id))
            ->with(['user', 'category', 'cards' => function (Relation $query) {
                $query->whereRaw('`count_all`>`count_sold`')
                    ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                    ->groupBy('product_id');
            }])->first();

        if (!$product) {
            // Warning
            // 2019-01-01 之后的用户将无法使用 id 的形式, 转变为 en_id
            $product = Product::whereId($en_product_id)
                ->where('created_at', '<', \Carbon\Carbon::createFromDate(2019, 1, 1))
                ->with(['user', 'category', 'cards' => function (Relation $query) {
                    $query->whereRaw('`count_all`>`count_sold`')
                        ->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count`')
                        ->groupBy('product_id');
                }])->first();
        }
        if (!$product || !$product->category) {
            return $this->_shop_404();
        }

        if ($product->password_open && $product->password !== $request->input('p')) {
            return view('message', [
                'title' => '出错',
                'message' => ($request->has('p') ? '密码错误' : '请输入密码') . '<br>
<div style="font-size: 14px">
<input id="password" type="password" style="display: block; margin: 8px 0 8px 0">
<button onclick="location.href=location.href.split(\'?\')[0]+\'?p=\'+encodeURI(document.getElementById(\'password\').value)">确认</button>
</div>
'
            ]);
        }
        $product->category->setVisible(['id', 'name', 'password_open']);
        $product->setAttribute('count', count($product->cards) ? $product->cards[0]->count : 0);
        $product->setVisible(['id', 'name', 'description', 'count', 'buy_min', 'buy_max',
            'support_coupon', 'password_open', 'price', 'price_whole']);
        return $this->_shop_render($product->user, $product->category, $product);
    }


    public function shop()
    {
        $this->checkIsInMaintain();

        $user = User::firstOrFail();
        return $this->_shop_render($user);
    }

    public function admin()
    {

        $config = [];
        $config['url'] = config('app.url');

        $config['vcode'] = [
            'driver' => System::_get('vcode_driver'),
            'login' => System::_get('vcode_login'),
        ];
        if ($config['vcode']['driver'] === 'geetest') {
            $config['vcode']['geetest'] = Geetest\API::get();
        }
        return view('admin', [
            'config' => $config
        ]);
    }
}
