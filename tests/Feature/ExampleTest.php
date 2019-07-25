<?php

namespace Tests\Feature;

use App\Http\Controllers\Merchant\UserDomain;
use App\Library\Helper;
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
        $app = strtoupper(str_random(16));
        var_dump($app);
    }
}
