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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use Mercari\MercariAuthClient;
use Mercari\TokenResponse;
use ReflectionObject;
use GuzzleHttp\Client;
use Mercari\TokenRequest;
use Tumblr\Chorus\FakeTimeKeeper;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

/**
 * @covers \Mercari\MercariAuthClient
 */
class MercariAuthClientTest extends TestCase
{
    private array $requests = [];

    public function setUp(): void
    {
        $this->requests = [];
    }

    public function testCreateInstance()
    {
        $client = MercariAuthClient::createInstance('sandbox.example.com', 'client_id', 'secret', ['Foo' => 'bar']);

        $this->assertInstanceOf(MercariAuthClient::class, $client);

        $reflection = new ReflectionObject($client);
        $property = $reflection->getProperty('clientId');
        $property->setAccessible(true);

        $this->assertSame('client_id', $property->getValue($client));


        $property = $reflection->getProperty('client');
        $property->setAccessible(true);

        /** @var Client $httpClient */
        $httpClient = $property->getValue($client);

        $this->assertSame('bar', $httpClient->getConfig('headers')['Foo']);

        $this->assertSame([
            'client_id',
            'secret',
        ], $httpClient->getConfig('auth'));

        $this->assertSame('https://sandbox.example.com', (string) $httpClient->getConfig('base_uri'));

        $this->assertTrue($httpClient->getConfig('http_errors'));
        $this->assertFalse($httpClient->getConfig('allow_redirects'));
    }

    private function buildHttpClient(array $responses): Client
    {
        $mock = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($this->requests));

        return new Client(['handler' => $handlerStack]);
    }

    public function testLoginUrlRequest()
    {
        $redirectUrl = 'https://www.example.com/mercari/return';
        $csrfToken = 'foo';
        $state = 'bar';

        $tokenRequest = TokenRequest::loginUrl($redirectUrl, $csrfToken, $state);

        $responses = [
            new Response(200, ['Location' => 'https://sandbox.example.com/login']),
        ];

        $client = new MercariAuthClient('xclient_id', $this->buildHttpClient($responses));
        $url = $client->getAuthUrl($tokenRequest);

        $this->assertSame('https://sandbox.example.com/login', $url);

        $this->assertCount(1, $this->requests);

        /** @var Request $request */
        $request = $this->requests[0]['request'];

        $this->assertSame('GET', $request->getMethod());

        $this->assertSame(MercariAuthClient::REDIRECT, $request->getUri()->getPath());

        $query = $request->getUri()->getQuery();

        $this->assertStringContainsString('response_type=code', $query);
        $this->assertStringContainsString('redirect_uri=https', $query);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirectUrl), $query);

        $this->assertStringContainsString('state=' . $csrfToken, $query);
        $this->assertStringContainsString('nonce=' . $state, $query);
        $this->assertStringContainsString('client_id=xclient_id', $query);
    }

    public function testAuthorizationCodeRequest()
    {
        $redirectUrl = 'https://www.example.com/mercari/return';
        $code = 'foo123123';

        $timekeeper = new FakeTimeKeeper(10000);

        $tokenResponse = new TokenResponse();
        $tokenResponse->access_token = 'access_token123';

        $responses = [
            new Response(200, [], json_encode($tokenResponse)),
        ];

        $client = new MercariAuthClient('client_id', $this->buildHttpClient($responses), null, $timekeeper);

        $tokenRequest = TokenRequest::authorizationCode($redirectUrl, $code);
        $tokenActual = $client->getToken($tokenRequest);

        $this->assertSame($tokenResponse->access_token, $tokenActual->access_token);
        $this->assertSame(10000, $tokenActual->ts);

        $this->assertCount(1, $this->requests);

        /** @var Request $request */
        $request = $this->requests[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(MercariAuthClient::TOKEN, $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        $this->assertStringContainsString('grant_type=authorization_code', $body);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirectUrl), $body);
        $this->assertStringContainsString('code=' . $code, $body);
    }
}
