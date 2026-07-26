<?php

namespace Tests\Regression;

use App\Category;
use App\Product;
use App\System;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

abstract class RegressionTestCase extends \Tests\TestCase
{
    use DatabaseTransactions;

    protected User $merchant;
    protected User $admin;
    protected Category $category;
    protected Product $product;
    protected \Illuminate\Queue\QueueManager $realQueue;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Mail::fake();
        $this->realQueue = app('queue');
        Queue::fake();
        Cache::flush();
        System::_init();
        foreach (['storage_driver' => 'local', 'mail_driver' => 'array', 'app_url' => 'http://localhost',
            'app_url_api' => 'http://localhost', 'app_url_admin' => 'http://localhost',
            'maintain' => '0', 'vcode_login' => '0', 'vcode_login_admin' => '0', 'vcode_shop_buy' => '0',
            'vcode_register' => '0', 'vcode_forget' => '0', 'reg' => '1', 'filter_words_open' => '0',
            'wechat_oa_open' => '0', 'withdraw_manual_auto_alipay' => '0'] as $key => $value) {
            System::_set($key, $value);
        }
        config(['app.url' => 'http://localhost', 'app.url_api' => 'http://localhost',
            'app.url_admin' => 'http://localhost', 'filesystems.default' => 'local']);
        $this->merchant = User::create(['id' => 1, 'email' => 'admin@example.test',
            'password' => Hash::make('password123'), 'remember_token' => '0']);
        $this->admin = $this->merchant;
        $this->category = Category::create(['user_id' => $this->merchant->id, 'name' => '测试分类', 'enabled' => 1]);
        $this->product = Product::create(['user_id' => $this->merchant->id, 'category_id' => $this->category->id,
            'name' => '测试商品', 'description' => '商品说明', 'price' => 100, 'enabled' => 1,
            'instructions' => '使用说明', 'fields' => '[]', 'count_all' => 0, 'count_sold' => 0]);
    }

    protected function token(User $user): array
    {
        $token = Auth::login($user);
        Auth::forgetGuards();
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function api(string $method, string $url, array $data = [], ?array $headers = null)
    {
        Auth::forgetGuards();
        app('tymon.jwt.auth')->unsetToken();
        return $this->json($method, $url, $data, $headers ?? []);
    }
}
