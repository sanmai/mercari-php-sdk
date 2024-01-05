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
