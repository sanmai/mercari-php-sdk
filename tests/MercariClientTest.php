<?php
/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko
 *
 * Mercari PHP SDK is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * Mercari PHP SDK is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Mercari PHP SDK. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Tests\Mercari;

use JSONSerializer\Serializer;
use Mercari\MercariClient;
use Mercari\TokenResponse;
use GuzzleHttp\Client;
use Mercari\TokenRequest;
use Tumblr\Chorus\FakeTimeKeeper;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Tumblr\Chorus\TimeKeeper;

/**
 * @covers \Mercari\MercariClient
 */
class MercariClientTest extends TestCase
{
    public function testCreateInstance()
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
        $this->assertFalse($httpClient->getConfig('debug'));

        $this->assertSame(3, $httpClient->getConfig('connect_timeout'));
        $this->assertSame(120, $httpClient->getConfig('timeout'));

        /** @var HandlerStack $handler */
        $handler = $httpClient->getConfig('handler');

        $this->assertSame($handler, $this->getPropertyValue($client, 'stack'));

        $this->assertStringContainsString('retry_on_status', (string) $handler);
    }

}
