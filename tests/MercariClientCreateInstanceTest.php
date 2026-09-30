<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko <alexey@kopytko.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Tests\Mercari;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Mercari\MercariClient;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MercariClient::class)]
class MercariClientCreateInstanceTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $client = MercariClient::createInstance('sandbox-api.example.com', 'token', ['Foo' => 'bar']);

        $this->assertInstanceOf(MercariClient::class, $client);

        /** @var Client $httpClient */
        $httpClient = $this->getPropertyValue($client, 'client');

        $this->assertSame('bar', $httpClient->getConfig('headers')['Foo']);
        $this->assertSame("Bearer token", $httpClient->getConfig('headers')['Authorization']);

        $this->assertNull($httpClient->getConfig('auth'));

        $this->assertSame('https://sandbox-api.example.com', (string) $httpClient->getConfig('base_uri'));

        $this->assertTrue($httpClient->getConfig('http_errors'));
        $this->assertFalse($httpClient->getConfig('allow_redirects'));

        $this->assertSame(3, $httpClient->getConfig('connect_timeout'));
        $this->assertSame(120, $httpClient->getConfig('timeout'));

        /** @var HandlerStack $handler */
        $handler = $httpClient->getConfig('handler');

        $this->assertSame($handler, $this->getPropertyValue($client, 'stack'));

        $this->assertStringContainsString('retry_on_status', (string) $handler);

        $retryMiddleware = $this->getRetryMiddleware($handler);

        $statusCodes = $this->getPropertyValue($retryMiddleware, 'defaultOptions')['retry_on_status'];

        foreach ($statusCodes as $code) {
            $this->assertIsInt($code);
        }

        $this->assertCount(6, $statusCodes);
    }

    private function getRetryMiddleware(HandlerStack $handler): GuzzleRetryMiddleware
    {
        $stack = $this->getPropertyValue($handler, 'stack');
        foreach ($stack as $item) {
            if ($item[1] === "retry_on_status") {
                return $item[0](fn() => null);
            }
        }

        $this->fail('retry_on_status middleware not found');
    }

    public function testCreateInstanceWithClientOptions(): void
    {
        $client = MercariClient::createInstance(
            'sandbox-api.example.com',
            'token',
            clientOptions: ['timeout' => 42, 'connect_timeout' => 67],
        );

        $this->assertInstanceOf(MercariClient::class, $client);

        /** @var Client $httpClient */
        $httpClient = $this->getPropertyValue($client, 'client');

        $this->assertSame(42, $httpClient->getConfig('timeout'));
        $this->assertSame(67, $httpClient->getConfig('connect_timeout'));

        // defaults preserved when not overridden
        $this->assertSame("Bearer token", $httpClient->getConfig('headers')['Authorization']);
        $this->assertTrue($httpClient->getConfig('http_errors'));
        $this->assertFalse($httpClient->getConfig('allow_redirects'));
    }
}
