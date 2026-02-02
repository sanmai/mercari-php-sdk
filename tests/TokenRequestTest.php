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

use Mercari\TokenRequest;
use Mercari\TokenResponse;

/**
 * @covers \Mercari\TokenRequest
 */
class TokenRequestTest extends TestCase
{
    public function testClientCredentials()
    {
        $token = TokenRequest::clientCredentials();

        $this->assertSame([
            'grant_type' => 'client_credentials',
            'scope' => 'openapi:buy',
        ], $token->getRequestParams());
    }

    public function testLoginUrl()
    {
        $url = 'https://www.example.com/redirect';
        $state = '12345';
        $nonce = '67890';

        $token = TokenRequest::loginUrl($url, $state, $nonce);

        $this->assertSame([
            'response_type' => 'code',
            'redirect_uri' => $url,
            'state' => $state,
            'scope' => 'openapi:buy openid offline_access',
            'nonce' => $nonce,
        ], $token->getRequestParams());
    }

    public function testAuthorizationCode()
    {
        $url = 'https://www.example.com/redirect';
        $code = 'X12345';

        $token = TokenRequest::authorizationCode($url, $code);

        $this->assertSame([
            'grant_type' => 'authorization_code',
            'scope' => 'openapi:buy openid offline_access',
            'redirect_uri' => $url,
            'code' => $code,
        ], $token->getRequestParams());
    }

    public function testRefreshToken()
    {
        $response = new TokenResponse();
        $response->refresh_token = '123678';

        $token = TokenRequest::refreshToken($response);

        $this->assertSame([
            'grant_type' => 'refresh_token',
            'scope' => 'openapi:buy openid offline_access',
            'refresh_token' => $response->refresh_token,
        ], $token->getRequestParams());
    }
}
