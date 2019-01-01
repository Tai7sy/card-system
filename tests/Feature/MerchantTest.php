<?php

namespace Tests\Feature;

use App\UserDomain;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MerchantTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->post('/api/merchant/login',
            ['email' => 'demo@qq.com', 'password' => 'error pass']
        );
        $response->assertJsonMissing(['code' => 0]);

        $response = $this->post('/api/merchant/login',
            ['email' => 'demo@qq.com', 'password' => '123456']
        );
        $response->assertJson(['code' => 0]);
        $response->dump();
    }

    function testDashboard()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/dashboard');
        $response->dump();
    }

    function testDashboard2()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/order/stat');
        $response->dump();
    }


    function testMessage()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/message');
        $response->dump();
    }

    function testEditInfo()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/info/edit', ['name' => '']);
        $response->dump();
    }

    function testIncomeStat()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/income/stat');
        $response->dump();
    }

    function testPayEnable()
    {
        $this->be(\App\User::whereEmail('demo@qq.com')->first());
        $response = $this->post('/api/merchant/pay/enable', ['pay_ids' => 1, 'enabled' => 1]);
        $response->dump();
    }

    function testEmail()
    {
        $response = $this->get('/test');
        $response->dump();
    }

    function testList()
    {
        $order = \App\Order::whereOrderNo('2018030321070543648')->first();
        var_dump($order->cards()->exists());
    }
}
