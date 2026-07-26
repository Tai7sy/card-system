<?php

namespace Tests\Regression;

use App\Card;
use App\Coupon;
use App\Order;
use App\Pay;
use App\PayWay;
use App\Product;
use App\System;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;

class BusinessTest extends RegressionTestCase
{
    private function checkout(array $overrides = []): array
    {
        $pay = Pay::create(['name' => '离线测试支付', 'driver' => 'Demo', 'way' => 'alipay',
            'enabled' => 1, 'config' => '{}', 'fee_system' => 0.01]);
        $controller = new class extends \App\Http\Controllers\Shop\Pay {
            public function goPay($request, $order_no, $order_name, $pay, $pay_money)
            {
                return response()->json(['order_no' => $order_no]);
            }
        };
        $request = Request::create('/api/shop/buy', 'POST', array_merge([
            'customer' => str_repeat('a', 32), 'category_id' => $this->category->id,
            'product_id' => $this->product->id, 'count' => 1, 'pay_id' => $pay->id,
            'contact' => 'buyer@example.test', 'coupon' => '',
        ], $overrides));
        $response = $controller->buy($request);
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        return [$controller, $request, Order::whereOrderNo($response->getData(true)['order_no'])->firstOrFail()];
    }

    public function testManualDeliveryAndOrderFreezeLedger(): void
    {
        $this->product->update(['delivery' => \App\Product::DELIVERY_MANUAL, 'count_all' => 10]);
        [$controller, $request, $order] = $this->checkout();
        $controller->shipOrder($request, $order->order_no, $order->paid, 'manual-payment');
        $this->assertSame(Order::STATUS_PAID, $order->fresh()->status);
        $headers = $this->token($this->admin);
        $this->api('POST', '/api/admin/order/ship', ['id' => $order->id, 'card' => 'MANUAL-CARD'], $headers)->assertOk();
        $this->assertSame(Order::STATUS_SUCCESS, $order->fresh()->status);
        $this->assertSame('MANUAL-CARD', $order->fresh()->cards->first()->card);
        $this->assertSame(99, $this->admin->fresh()->m_all);
        $this->api('POST', '/api/admin/order/freeze', ['ids' => (string) $order->id, 'reason' => '测试'], $headers)->assertOk();
        $this->assertSame(99, $this->admin->fresh()->m_frozen);
        $this->api('POST', '/api/admin/order/unfreeze', ['ids' => (string) $order->id], $headers)->assertOk();
        $this->assertSame(0, $this->admin->fresh()->m_frozen);
    }

    public function testRepeatCardsCouponAndQueryPassword(): void
    {
        $this->product->update(['count_all' => 5, 'support_coupon' => 1]);
        $card = Card::create(['user_id' => $this->admin->id, 'product_id' => $this->product->id,
            'card' => 'REPEAT-CARD', 'type' => Card::TYPE_REPEAT, 'count_all' => 5, 'count_sold' => 0]);
        Coupon::create(['user_id' => $this->admin->id, 'coupon' => 'SAVE20', 'category_id' => -1,
            'product_id' => -1, 'discount_type' => Coupon::DISCOUNT_TYPE_AMOUNT, 'discount_val' => 20,
            'type' => Coupon::TYPE_ONETIME, 'count_all' => 1, 'count_used' => 0,
            'expire_at' => '2030-01-01 00:00:00']);
        System::_set('order_query_password_open', '1');
        [$controller, $request, $order] = $this->checkout(['count' => 2, 'coupon' => 'SAVE20', 'query_password' => 'BuyerSecret!']);
        $this->assertSame(180, $order->paid);
        $this->assertSame(1, Coupon::whereCoupon('SAVE20')->first()->count_used);
        $controller->shipOrder($request, $order->order_no, 180, 'repeat-payment');
        $this->assertSame(Order::STATUS_SUCCESS, $order->fresh()->status);
        $this->assertSame(2, $card->fresh()->count_sold);
        $this->api('POST', '/api/shop/record/get', ['type' => 'contact', 'contact' => 'buyer@example.test',
            'query_password' => 'wrong-secret'])->assertOk()->assertJsonCount(0, 'data.list');
        $this->api('POST', '/api/shop/record/get', ['type' => 'contact', 'contact' => 'buyer@example.test',
            'query_password' => 'BuyerSecret!'])->assertOk()->assertJsonPath('data.list.0.order_no', $order->order_no);
    }

    public function testInsufficientStockAfterPaymentCanBeFulfilledLater(): void
    {
        $this->product->update(['count_all' => 1]);
        [$controller, $request, $order] = $this->checkout();
        $controller->shipOrder($request, $order->order_no, 100, 'late-stock-payment');
        $this->assertSame(Order::STATUS_PAID, $order->fresh()->status);
        Card::create(['user_id' => $this->admin->id, 'product_id' => $this->product->id,
            'card' => 'RESTOCKED', 'type' => Card::TYPE_ONETIME, 'count_all' => 1]);
        $this->api('POST', '/api/admin/order/ship', ['id' => $order->id], $this->token($this->admin))->assertOk();
        $this->assertSame(Order::STATUS_SUCCESS, $order->fresh()->status);
        $this->assertSame(99, $this->admin->fresh()->m_all);
    }

    public function testProductAndCategoryPasswords(): void
    {
        $this->category->update(['password_open' => 1, 'password' => 'CategorySecret']);
        // The existing free-edition API reports business rejections with status 500.
        $this->api('POST', '/api/shop/product', ['category_id' => $this->category->id])
            ->assertStatus(500)->assertJsonPath('message', trans('shop.category.password_error'));
        $this->api('POST', '/api/shop/product', ['category_id' => $this->category->id, 'password' => 'CategorySecret'])->assertOk();
        $this->product->update(['password_open' => 1, 'password' => 'ProductSecret']);
        $this->api('POST', '/api/shop/product/password', ['product_id' => $this->product->id, 'password' => 'wrong'])
            ->assertStatus(500)->assertJsonPath('message', trans('shop.product.password_error'));
        $this->api('POST', '/api/shop/product/password', ['product_id' => $this->product->id, 'password' => 'ProductSecret'])->assertOk();
        $this->get('/p/' . \App\Library\Helper::id_encode($this->product->id, \App\Library\Helper::ID_TYPE_PRODUCT))->assertOk();
        $this->get('/c/' . \App\Library\Helper::id_encode($this->category->id, \App\Library\Helper::ID_TYPE_CATEGORY))->assertOk();
    }

    public function testImageUploadThroughAuthenticatedEndpoint(): void
    {
        Storage::fake('local');
        $this->post('/api/admin/file/upload', ['file' => UploadedFile::fake()->image('product.png')],
            $this->token($this->admin))->assertOk();
        $this->assertSame(1, \App\File::count());
    }

    public static function listingEndpoints(): array
    {
        return array_map(fn ($path) => [$path], ['/api/admin/dashboard', '/api/admin/category',
            '/api/admin/product', '/api/admin/card', '/api/admin/coupon', '/api/admin/order',
            '/api/admin/order/stat', '/api/admin/log', '/api/admin/pay', '/api/admin/pay/stat',
            '/api/admin/payway', '/api/admin/system/info', '/api/admin/system/theme',
            '/api/admin/system/order', '/api/admin/system/vcode', '/api/admin/system/email',
            '/api/admin/system/sms', '/api/admin/system/storage']);
    }

    #[DataProvider('listingEndpoints')]
    public function testListingEndpoints(string $path): void
    {
        $this->api('GET', $path, ['day' => 7], $this->token($this->admin))->assertOk();
    }

    public function testCategoryProductAndCardManagement(): void
    {
        $headers = $this->token($this->merchant);
        $this->api('POST', '/api/admin/category/edit', ['name' => '新分类', 'sort' => 1, 'enabled' => 1], $headers)->assertOk();
        $this->assertDatabaseHas('categories', ['name' => '新分类', 'user_id' => $this->merchant->id]);
        $response = $this->api('POST', '/api/admin/product/edit', [
            'id' => $this->product->id, 'category_id' => $this->category->id, 'name' => '更新商品',
            'description' => '新商品说明', 'instructions' => '使用说明', 'fields' => '[]', 'sort' => 10,
            'inventory' => 1, 'buy_min' => 1, 'buy_max' => 10, 'cost' => 0, 'price' => 2.5,
            'price_whole' => '[]', 'enabled' => 1, 'retail_open' => 0, 'count_warn' => 0,
        ], $headers);
        $response->assertOk();
        $this->assertSame(250, $this->product->fresh()->price);
        $this->api('POST', '/api/admin/card/add', ['product_id' => $this->product->id,
            'card' => "CARD-A\nCARD-B\nCARD-A", 'type' => Card::TYPE_ONETIME, 'is_check' => 'true'], $headers)->assertOk();
        $this->assertSame(2, Card::where('product_id', $this->product->id)->count());
        $this->assertSame(2, $this->product->fresh()->count_all);
        $this->api('POST', '/api/shop/product', ['category_id' => $this->category->id])->assertOk();
        $card = Card::where('product_id', $this->product->id)->firstOrFail();
        $this->api('POST', '/api/admin/card/delete', ['ids' => (string) $card->id], $headers)->assertOk();
        $this->assertSoftDeleted('cards', ['id' => $card->id]);
        $this->api('POST', '/api/admin/card/trash/restore', ['ids' => (string) $card->id], $headers)->assertOk();
        $this->assertNotNull(Card::find($card->id));
    }

    public function testCouponCreation(): void
    {
        $this->api('POST', '/api/admin/coupon/create', ['count' => 2, 'discount_val' => 20,
            'discount_type' => Coupon::DISCOUNT_TYPE_AMOUNT, 'expire_at' => '2030-01-01 00:00:00',
            'remark' => '测试'], $this->token($this->merchant))->assertOk()->assertJsonCount(2, 'data');
        $this->assertSame(2, Coupon::where('user_id', $this->merchant->id)->count());
    }

    public function testLocalImageUploadReadAndDelete(): void
    {
        Storage::fake('local');
        $result = \App\Http\Controllers\Merchant\File::uploadImg(UploadedFile::fake()->image('example.png'),
            $this->merchant->id, 'images', true);
        $this->assertIsArray($result);
        $file = \App\File::findOrFail($result['id']);
        Storage::disk('local')->assertExists($file->path);
        $this->get('/storage/' . $file->path)->assertOk()->assertHeader('Content-Type', 'image/png');
        Storage::disk('local')->delete($file->path);
        Storage::disk('local')->assertMissing($file->path);
    }

    public function testPaymentSelectionCheckoutAndIdempotentDelivery(): void
    {
        $pay = Pay::create(['name' => '测试支付', 'driver' => 'Demo', 'way' => 'alipay', 'enabled' => 3,
            'config' => '{}', 'fee_system' => 0.01]);
        $way = PayWay::create(['name' => '测试方式', 'enabled' => 3, 'type' => PayWay::TYPE_SHOP,
            'channels' => [[$pay->id, 100]]]);
        $this->assertSame($pay->id, $way->getPayByWeight()->id);
        $this->assertNotEmpty(PayWay::gets($this->merchant));
        System::_set('order_query_password_open', '0');
        System::_set('order_pay_amount_min', '1');
        System::_set('order_pay_amount_max', '1000000');
        $card = Card::create(['user_id' => $this->merchant->id, 'product_id' => $this->product->id,
            'card' => 'DELIVERY-SECRET', 'type' => Card::TYPE_ONETIME, 'count_all' => 1, 'count_sold' => 0]);
        $this->product->update(['count_all' => 1, 'count_sold' => 0]);
        // Stub only the external gateway handoff; all pricing/order/delivery code is real.
        $controller = new class extends \App\Http\Controllers\Shop\Pay {
            public function goPay($request, $order, $order_name, $pay, $pay_money)
            {
                return response()->json(['order_no' => $order]);
            }
        };
        $request = Request::create('/api/shop/buy', 'POST', ['customer' => str_repeat('a', 32),
            'category_id' => $this->category->id, 'product_id' => $this->product->id, 'count' => 1,
            'pay_id' => $pay->id, 'contact' => 'buyer@example.test']);
        $result = $controller->buy($request);
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $order = Order::where('order_no', $result->getData(true)['order_no'])->firstOrFail();
        $this->assertSame(100, $order->paid);
        $this->assertSame(99, $order->income);
        $controller->shipOrder($request, $order->order_no, 100, 'mock-trade-1');
        $this->assertSame(Order::STATUS_SUCCESS, $order->fresh()->status);
        $this->assertSame(1, $order->fresh()->cards()->count());
        $this->assertSame(1, $card->fresh()->count_sold);
        $this->assertSame(99, $this->merchant->fresh()->m_all);
        $controller->shipOrder($request, $order->order_no, 100, 'mock-trade-1');
        $this->assertSame(99, $this->merchant->fresh()->m_all);
        $this->assertSame(1, $card->fresh()->count_sold);
        System::_set('vcode_shop_search', '0');
        $this->api('POST', '/api/shop/record/get', ['type' => 'order_no', 'order_no' => $order->order_no])
            ->assertOk()->assertJsonPath('data.list.0.order_no', $order->order_no);

    }
}
