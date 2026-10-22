<?php

// Offline compatibility check for the trimmed SDK; no database or AWS account required.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require dirname(__DIR__) . '/vendor/autoload.php';

use Aws\Sqs\SqsClient;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Illuminate\Filesystem\FilesystemManager;
use Psr\Http\Message\RequestInterface;

$checks = 0;
$assert = function ($condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$responses = [
    ['PUT', '/card.txt', new Response(200, ['ETag' => '"test-etag"'])],
    ['GET', '/card.txt', new Response(200, ['Content-Length' => '4'], 'card')],
    ['HEAD', '/card.txt', new Response(200, ['Content-Length' => '4'])],
    ['GET', '/', new Response(200, ['Content-Type' => 'application/xml'],
        '<ListBucketResult xmlns="http://s3.amazonaws.com/doc/2006-03-01/"><Name>card-test</Name>'
        . '<IsTruncated>false</IsTruncated><Contents><Key>card.txt</Key><Size>4</Size>'
        . '<LastModified>2026-01-01T00:00:00Z</LastModified><StorageClass>STANDARD</StorageClass>'
        . '</Contents></ListBucketResult>')],
    ['DELETE', '/card.txt', new Response(204)],
];
$handler = function (RequestInterface $request, array $options) use (&$responses, $assert) {
    $expected = array_shift($responses);
    $assert($expected !== null, 'Unexpected HTTP request');
    $assert($request->getMethod() === $expected[0], 'Unexpected S3 method');
    $assert($request->getUri()->getPath() === $expected[1], 'Unexpected S3 path');
    $assert(str_starts_with($request->getHeaderLine('Authorization'), 'AWS4-HMAC-SHA256 '), 'Missing S3 signature');
    return Create::promiseFor($expected[2]);
};

$disk = (new FilesystemManager(new Container()))->createS3Driver([
    'driver' => 's3', 'key' => 'test-key', 'secret' => 'test-secret',
    'region' => 'us-east-1', 'bucket' => 'card-test', 'throw' => true,
    'http_handler' => $handler,
]);
$assert($disk->put('card.txt', 'card'), 'Upload failed');
$assert($disk->get('card.txt') === 'card', 'Download failed');
$assert($disk->exists('card.txt'), 'Existence check failed');
$assert($disk->files() === ['card.txt'], 'Listing failed');
$assert($disk->delete('card.txt'), 'Delete failed');
$assert($responses === [], 'Some S3 operations were not executed');
$assert(str_contains($disk->url('card.txt'), 'card-test.s3'), 'Public URL failed');
$assert(str_contains($disk->temporaryUrl('card.txt', new DateTimeImmutable('+5 minutes')), 'X-Amz-Signature='), 'Presigned URL failed');

$sqs = new SqsClient([
    'version' => 'latest', 'region' => 'us-east-1',
    'credentials' => ['key' => 'test-key', 'secret' => 'test-secret'],
    'http_handler' => function (RequestInterface $request, array $options) use ($assert) {
        $assert($request->getHeaderLine('X-Amz-Target') === 'AmazonSQS.GetQueueUrl', 'Unexpected SQS action');
        $assert($request->hasHeader('Authorization'), 'Missing SQS signature');
        return Create::promiseFor(new Response(200, ['Content-Type' => 'application/x-amz-json-1.0'],
            '{"QueueUrl":"https://sqs.us-east-1.amazonaws.com/123456789012/card-test"}'));
    },
]);
$assert(str_ends_with($sqs->getQueueUrl(['QueueName' => 'card-test'])['QueueUrl'], '/card-test'), 'SQS response failed');

// These service models support the SDK credential chain and S3 encryption.
foreach ([
    Aws\Kms\KmsClient::class => 'Encrypt',
    Aws\Sts\StsClient::class => 'AssumeRole',
    Aws\SSO\SSOClient::class => 'GetRoleCredentials',
    Aws\SSOOIDC\SSOOIDCClient::class => 'CreateToken',
    Aws\Signin\SigninClient::class => 'CreateOAuth2Token',
] as $class => $operation) {
    $client = new $class([
        'version' => 'latest', 'region' => 'us-east-1',
        'credentials' => ['key' => 'test-key', 'secret' => 'test-secret'],
        'http_handler' => function () { throw new RuntimeException('Unexpected credential service request'); },
    ]);
    $assert($client->getApi()->hasOperation($operation), 'Missing core service model: ' . $class);
}

echo "AWS offline compatibility: {$checks} checks passed.\n";
