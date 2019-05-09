<?php

namespace App\Http\Controllers;

use App\Category;
use App\Library\Helper;
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
            'url' => config('app.url'),//支付域名
            'company' => config('app.company'),
            'name' => config('app.name'),
            'logo' => config('app.logo')
        ];
        $config['shop'] = [
            'name' => config('app.name'),
            'qq' => System::_get('shop_qq'),
            'ann' => System::_get('shop_ann'),
            'ann_pop' => System::_get('shop_ann_pop'),
            'inventory' => System::_getInt('shop_inventory')
        ];


        if ($product) {
            // 如果是产品连接
            $config['categories'] = [$category];
            $config['product'] = $product;
            $name = $product->name . ' - ' . $config['name'];
            $description = $product->description;
            if (@$description[0] === '{') {
                $matches = [];
                preg_match_all('/"insert":"(.+?)"/', $description, $matches);
                $description = str_replace('\n', ' ', @join(' ', $matches[1]));
            }
        } elseif ($category) {
            // 如果是分类连接
            $config['categories'] = [$category];
            $config['product'] = null;
            $products = Product::where('category_id', $category->id)
                ->where('enabled', 1)
                ->orderBy('sort')
                ->get();
            foreach ($products as $product) {
                $product->setForShop();
            };
            $config['products'] = $products->toArray();
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
            if (@$description[0] === '{') {
                $matches = [];
                preg_match_all('/"insert":"(.+?)"/', $description, $matches);
                $description = str_replace('\n', ' ', @join(' ', $matches[1]));
            }
        }


        $config['vcode'] = [
            'driver' => System::_get('vcode_driver'),
            'buy' => (int)System::_get('vcode_shop_buy'),
            'search' => (int)System::_get('vcode_shop_search')
        ];
        if ($config['vcode']['driver'] === 'geetest' &&
            (
                $config['vcode'] ||
                $config['search']
            )
        ) {
            $config['vcode']['geetest'] = Geetest\API::get();
        }

        // bit operate   pc & mobile = result_enabled
        $config['pays'] = Pay::whereRaw('enabled&' .
            (Helper::is_mobile() ? Pay::ENABLED_MOBILE : Pay::ENABLED_PC) . '!=0')
            ->orderBy('sort')
            ->get(['id', 'name', 'img']);


        $customer = Cookie::get('customer');
        $cookie = Cookie::make('customer',
            strlen($customer) !== 32 ? md5(str_random(16)) : $customer,
            43200, null, null, false, false); // 30 days


        /* @var $shop_theme \App\ShopTheme */
        $shop_theme = null;
        if (isset($_GET['theme'])) {
            $shop_theme = \App\ShopTheme::whereName($_GET['theme'])->first();
        }
        if (!$shop_theme) {
            $shop_theme = \App\ShopTheme::defaultTheme();
        }

        $config['theme'] = $user->theme_config && isset($user->theme_config[$shop_theme->name]) ?
            $user->theme_config[$shop_theme->name] :
            $shop_theme->config;

        if(isset($config['theme']['background']) && $config['theme']['background'] === '内置1'){
            $config['theme']['background'] = Helper::b1_rand_background();
        }

        return response()->view('shop_theme.' . $shop_theme->name . '.index', [
            'name' => $name,
            'title' => config('app.title'),
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

        // TODO 这里订单查询有问题, 需要重写
        return view('message', [
            'title' => '404 NotFound',
            'message' => '该链接不存在<br>
<a style="font-size: 18px" href="/s/#/record">查询订单</a>'
        ]);
    }

    public function shop_default(Request $request)
    {
        $tab = $request->get('tab', '');
        return response()->redirectTo('/?theme=Material#/record?tab=' . $tab);
    }


    public function shop_category($en_category_id)
    {
        $this->checkIsInMaintain();

        $category = Category::whereId(Helper::id_decode($en_category_id, Helper::ID_TYPE_CATEGORY))->with('user')->first();
        if (!$category && is_numeric($category)) {
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

        $product = Product::whereId(Helper::id_decode($en_product_id, Helper::ID_TYPE_PRODUCT))
            ->with(['user', 'category'])->first();

        if (!$product && is_numeric($en_product_id)) {
            // Warning
            // 2019-01-01 之后的用户将无法使用 id 的形式, 转变为 en_id
            $product = Product::whereId($en_product_id)
                ->where('created_at', '<', \Carbon\Carbon::createFromDate(2019, 1, 1))
                ->with(['user', 'category'])->first();
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
        $product->setForShop();
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
            'login' => (int)System::_get('vcode_login'),
        ];
        if ($config['vcode']['driver'] === 'geetest') {
            $config['vcode']['geetest'] = Geetest\API::get();
        }
        return view('admin', [
            'config' => $config
        ]);
    }
}
