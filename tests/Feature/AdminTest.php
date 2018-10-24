<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this->post('/api/admin/login',
            ['email' => '12840640+Tai7sy@users.noreply.github.com', 'password' => '123456']
        );
        $response->assertJson(['code' => 0]);

        $response = $this->post('/api/admin/login',
            ['email' => 'demo@qq.com', 'password' => '123456']
        );
        $response->assertJsonMissing(['code' => 0]);
        $response->dump();

    }

    function testDashboard()
    {
        $this->be(\App\User::whereRole('admin')->first());
        $response = $this->post('/api/admin/dashboard');
        $response->dump();
    }


    function testAction()
    {
        $this->be(\App\User::whereRole('admin')->first());
        $response = $this->post('/api/admin/order/freeze', ['ids' => 1003, 'reason' => '']);
        $response->dump();
    }
}
