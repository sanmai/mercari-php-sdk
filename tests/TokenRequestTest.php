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
