<?php

namespace Tests\Regression;

use App\Mail\Transport\SendCloudTransport;
use App\System;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;

class IntegrationTest extends RegressionTestCase
{
    public function testDatabaseQueueProcessesSerializedMail(): void
    {
        \Illuminate\Support\Facades\Queue::swap($this->realQueue);
        $manager = \Illuminate\Support\Facades\Mail::getFacadeRoot()->manager;
        \Illuminate\Support\Facades\Mail::swap($manager);
        config(['queue.default' => 'database', 'mail.driver' => 'array',
            'mail.from.address' => 'sender@example.test', 'mail.from.name' => '测试站点']);
        $manager->forgetMailers();
        $manager->to('recipient@example.test')->queue(new \App\Mail\Test());
        $connection = $this->realQueue->connection('database');
        $this->assertSame(1, $connection->size());
        $job = $connection->pop();
        $this->assertNotNull($job);
        $job->fire();
        $this->assertSame(0, $connection->size());
        $messages = $manager->mailer()->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $this->assertSame('recipient@example.test', $messages->first()->getOriginalMessage()->getTo()[0]->getAddress());
    }

    public function testFailedJobsKeepTheExistingDatabaseSchema(): void
    {
        $provider = app('queue.failer');
        $id = $provider->log('database', 'default', '{"test":true}', new \RuntimeException('regression probe'));
        $this->assertNotNull($provider->find($id));
        $this->assertTrue($provider->forget($id));
        $this->assertNull($provider->find($id));
    }

    public function testSendCloudMessageAttachmentsAndFailure(): void
    {
        Http::fake(['api.sendcloud.net/*' => Http::sequence()
            ->push(['result' => true, 'statusCode' => 200])
            ->push(['result' => false, 'statusCode' => 400])]);
        $transport = new SendCloudTransport('test-user', 'test-key');
        $email = (new Email())->from('sender@example.test')->to('to@example.test')->cc('cc@example.test')
            ->bcc('bcc@example.test')->replyTo('reply@example.test')->subject('测试邮件')->html('<p>正文</p>')
            ->attach("raw\x00attachment", 'data.bin', 'application/octet-stream');
        $transport->send($email);
        Http::assertSent(function ($request) {
            $data = array_column($request->data(), 'contents', 'name');
            return $request->url() === 'https://api.sendcloud.net/apiv2/mail/send'
                && $data['apiUser'] === 'test-user' && $data['to'] === 'to@example.test'
                && $data['html'] === '<p>正文</p>' && $data['bcc'] === 'bcc@example.test';
        });
        Http::assertSentCount(1);
        $multipart = Http::recorded()[0][0]->body();
        $this->assertStringContainsString("raw\x00attachment", $multipart);
        $this->expectException(TransportException::class);
        $transport->send($email);
    }

    public function testLegacySmtpConfigurationBuildsTransport(): void
    {
        config(['mail.driver' => 'smtp', 'mail.host' => 'smtp.example.test', 'mail.port' => 465,
            'mail.scheme' => 'smtps', 'mail.username' => 'test', 'mail.password' => 'test']);
        $manager = app('mail.manager');
        $manager->forgetMailers();
        $this->assertStringContainsString('smtps://smtp.example.test', (string) $manager->mailer()->getSymfonyTransport());
    }

    public function testMailViewsRender(): void
    {
        $this->assertNotEmpty((new \App\Mail\Test())->render());
        $this->assertStringContainsString('测试商品', (new \App\Mail\ProductCountWarn($this->product, 1))->render());
    }

    public function testCaptchaImageAndValidation(): void
    {
        $captcha = app('captcha')->create('default', true);
        $this->assertArrayHasKey('key', $captcha);
        $this->assertStringStartsWith('data:image/', $captcha['img']);
        $this->assertFalse(captcha_api_check('wrong-value', $captcha['key']));
    }

    public function testVendorWarningsAreSuppressedButApplicationErrorsStillThrow(): void
    {
        $handler = app(\Illuminate\Foundation\Bootstrap\HandleExceptions::class);
        $handler->handleError(E_WARNING, 'vendor warning', base_path('vendor/legacy/example.php'), 1);
        $this->addToAssertionCount(1);
        $this->expectException(\ErrorException::class);
        $handler->handleError(E_WARNING, 'application warning', app_path('Example.php'), 1);
    }

    public function testAllPaymentDriverClassesLoad(): void
    {
        $count = 0;
        foreach (glob(app_path('Library/Gateway/Pay/*/Api.php')) as $path) {
            $driver = basename(dirname($path));
            $class = 'Gateway\\Pay\\' . $driver . '\\Api';
            $this->assertTrue(class_exists($class), $class);
            $this->assertTrue(is_subclass_of($class, \Gateway\Pay\ApiInterface::class), $class);
            $count++;
        }
        $this->assertGreaterThan(20, $count);
    }
}
