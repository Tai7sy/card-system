<?php

namespace Tests\Feature;

use App\Http\Controllers\Shop\ApiPay;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPayTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {


        $apipay = \App\ApiPay::whereName('wx')->first();

        // $apipay->options = [['1', 1], ['2', 2]];

        // $apipay->save();
        var_dump($apipay->getPayByWeight()->name);


        $this->assertTrue(true);
    }
}
