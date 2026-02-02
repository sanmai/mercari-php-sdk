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

declare(strict_types=1);

namespace Mercari;

/**
 * @property string $grant_type
 * @property string $scope
 * @property string $redirect_uri
 * @property string $code
 * @property string $client_id
 * @property string $response_type
 * @property string $state
 * @property string $nonce
 * @property string $refresh_token
 */
class TokenRequest extends GenericRequest
{
    private const DEFAULT_SCOPE = ['openapi:buy', 'openid', 'offline_access'];

    public static function clientCredentials(): TokenRequest
    {
        $request = new self();
        $request->grant_type = 'client_credentials';
        $request->scope = 'openapi:buy';

        return $request;
    }

    public static function loginUrl(string $redirect_uri, string $state, string $nonce): TokenRequest
    {
        $request = new self();
        $request->response_type = 'code';
        $request->redirect_uri = $redirect_uri;
        $request->state = $state;
        $request->scope = join(' ', self::DEFAULT_SCOPE);
        $request->nonce = $nonce;

        return $request;
    }

    public static function authorizationCode(string $redirect_uri, string $code): TokenRequest
    {
        $request = new self();
        $request->grant_type = 'authorization_code';
        $request->scope = join(' ', self::DEFAULT_SCOPE);
        $request->redirect_uri = $redirect_uri;
        $request->code = $code;

        return $request;
    }

    public static function refreshToken(TokenResponse $token): TokenRequest
    {
        $request = new self();
        $request->grant_type = 'refresh_token';
        $request->scope = join(' ', self::DEFAULT_SCOPE);
        $request->refresh_token = $token->refresh_token;

        return $request;
    }
}
