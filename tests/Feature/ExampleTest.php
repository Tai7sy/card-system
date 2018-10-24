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
        $app = UserDomain::shorten1('http://www.baidu.com');
        var_dump($app);
    }
}
