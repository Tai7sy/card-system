<?php

namespace Tests\Regression;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthTest extends RegressionTestCase
{
    public function testLogViewerTokenAndInvalidToken(): void
    {
        $response = $this->api('GET', '/api/admin/web/logs/token', [], $this->token($this->admin))->assertOk();
        $token = $response->json('token');
        $this->get('/api/admin/web/logs/' . $token)->assertOk();
        $this->get('/api/admin/web/logs/' . str_repeat('0', 32))->assertUnauthorized();
    }

    public function testAdministratorLoginLogoutAndInvalidPassword(): void
    {
        $this->api('POST', '/api/admin/auth/login', ['email' => $this->admin->email, 'password' => 'wrong'])->assertStatus(400);
        $response = $this->api('POST', '/api/admin/auth/login', ['email' => $this->admin->email, 'password' => 'password123']);
        $response->assertCreated()->assertHeader('Authorization')->assertJsonPath('user.role', 'admin');
        $headers = ['Authorization' => $response->headers->get('Authorization')];
        $this->api('GET', '/api/admin/info', [], $headers)->assertOk();
        $this->api('DELETE', '/api/admin/auth/token', [], $headers)->assertOk();
        $this->api('GET', '/api/admin/info', [], $headers)->assertUnauthorized();
    }

    public function testChangingPasswordInvalidatesPreviouslyIssuedTokens(): void
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(5));
        $old = $this->token($this->merchant);
        Carbon::setTestNow();
        $response = $this->api('POST', '/api/admin/info/password', ['old' => 'password123',
            'password' => 'newPassword123', 'password_confirmation' => 'newPassword123'], $old);
        $response->assertOk()->assertHeader('Authorization');
        $this->api('GET', '/api/admin/info', [], $old)->assertUnauthorized();
        $this->api('GET', '/api/admin/info', [], ['Authorization' => $response->headers->get('Authorization')])->assertOk();
    }

    public function testExpiredTokenRefreshAndRefreshWindow(): void
    {
        config(['jwt.ttl' => 1, 'jwt.refresh_ttl' => 10]);
        $this->travel(-2)->minutes();
        $old = $this->token($this->merchant);
        $this->travelBack();
        $response = $this->api('GET', '/api/admin/info', [], $old);
        $response->assertOk()->assertHeader('Authorization');
        $this->travel(20)->minutes();
        $this->api('GET', '/api/admin/info', [], ['Authorization' => $response->headers->get('Authorization')])->assertUnauthorized();
        $this->travelBack();
    }

}
