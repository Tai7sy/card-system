<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $api_pay = \App\ApiPay::where('code', 'wechat')->first();
        $api_pay->options = [
            '10' => 3,
            '11' => 0,
            '12' => 0
        ];
        //' {"10":3,"11":0,"12":0} '
        $this->assertTrue(true);
    }
}
