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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use JMS\Serializer\SerializerInterface;
use JSONSerializer\Serializer;
use Tumblr\Chorus\TimeKeeper;

/**
 * Authentication client for Mercari API.
 */
class MercariAuthClient
{
    public const TOKEN = '/jp/v1/token';

    public const REDIRECT = '/jp/v1/authorize';

    public static function createInstance(string $authHost, string $clientId, string $clientSecret, array $extraHeaders = []): self
    {
        $httpClient = new Client([
            'debug' => defined('MERCARI_DEBUG_CURL'),
            'base_uri' => sprintf('https://%s', $authHost),
            'auth' => [$clientId, $clientSecret],
            'connect_timeout' => 3,
            'timeout' => 10,
            'http_errors' => true,
            'allow_redirects' => false,
            'headers' => $extraHeaders,
        ]);

        return new MercariAuthClient(
            $clientId,
            $httpClient,
            Serializer::withJSONOptions(),
            new TimeKeeper()
        );
    }

    private string $clientId;

    private Client $client;

    private SerializerInterface $serializer;

    private TimeKeeper $timekeeper;

    /**
     * Create a new instance.
     */
    public function __construct(
        string $clientId,
        Client $client,
        SerializerInterface $serializer,
        TimeKeeper $timekeeper
    ) {
        $this->clientId = $clientId;
        $this->client = $client;
        $this->serializer = $serializer;
        $this->timekeeper = $timekeeper;
    }

    /**
     * @throws RequestException
     */
    public function getToken(TokenRequest $request = null): TokenResponse
    {
        $request ??= TokenRequest::clientCredentials();

        $response = $this->client->post(
            self::TOKEN,
            ['form_params' => $request->getRequestParams()]
        );

        $content = $response->getBody()->getContents();

        $token = $this->serializer->deserialize($content, TokenResponse::class, 'json');
        $token->ts = $this->timekeeper->getCurrentUnixTime();

        return $token;
    }

    /**
     * @throws RequestException
     */
    public function getAuthUrl(TokenRequest $request): ?string
    {
        $request->client_id = $this->clientId;

        return $this->client->get(
            self::REDIRECT,
            [
                'query' => $request->getRequestParams(),
                'auth' => null,
            ]
        )->getHeader('Location')[0];
    }
}
