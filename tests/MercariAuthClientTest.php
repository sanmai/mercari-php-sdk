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

use Mercari\MercariAuthClient;
use Mercari\TokenResponse;
use ReflectionObject;
use GuzzleHttp\Client;
use Mercari\TokenRequest;
use DuoClock\DuoClock;
use DuoClock\TimeSpy;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \Mercari\MercariAuthClient
 */
class MercariAuthClientTest extends TestCase
{
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

        $this->assertSame('https://sandbox.example.com', (string) $httpClient->getConfig('base_uri'));
        $this->assertSame(['client_id', 'secret'], $httpClient->getConfig('auth'));
        $this->assertSame('bar', $httpClient->getConfig('headers')['Foo']);

        $this->assertTrue($httpClient->getConfig('http_errors'));
        $this->assertFalse($httpClient->getConfig('allow_redirects'));

        $this->assertSame(3, $httpClient->getConfig('connect_timeout'));
        $this->assertSame(10, $httpClient->getConfig('timeout'));
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

        $client = new MercariAuthClient(
            'xclient_id',
            $this->buildHttpClient($responses),
            $this->serializer,
            new DuoClock()
        );
        $url = $client->getAuthUrl($tokenRequest);

        $this->assertSame('https://sandbox.example.com/login', $url);

        $this->assertCount(1, $this->requests);

        $request = $this->getLastRequest();

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

        $timekeeper = new TimeSpy(10000);

        $tokenResponse = new TokenResponse();
        $tokenResponse->access_token = 'access_token123';

        $responses = [
            new Response(200, [], json_encode($tokenResponse)),
        ];

        $client = new MercariAuthClient(
            'xclient_id',
            $this->buildHttpClient($responses),
            $this->serializer,
            $timekeeper
        );

        $tokenRequest = TokenRequest::authorizationCode($redirectUrl, $code);
        $tokenActual = $client->getToken($tokenRequest);

        $this->assertSame($tokenResponse->access_token, $tokenActual->access_token);
        $this->assertSame(10000, $tokenActual->ts);

        $this->assertCount(1, $this->requests);

        $request = $this->getLastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(MercariAuthClient::TOKEN, $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        $this->assertStringContainsString('grant_type=authorization_code', $body);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirectUrl), $body);
        $this->assertStringContainsString('code=' . $code, $body);
    }
}
