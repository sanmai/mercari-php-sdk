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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use JMS\Serializer\SerializerInterface;
use JSONSerializer\Serializer;
use DuoClock\DuoClock;

use function sprintf;

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
            new DuoClock()
        );
    }

    private string $clientId;

    private Client $client;

    private SerializerInterface $serializer;

    private DuoClock $timekeeper;

    /**
     * Create a new instance.
     */
    public function __construct(
        string $clientId,
        Client $client,
        SerializerInterface $serializer,
        DuoClock $timekeeper
    ) {
        $this->clientId = $clientId;
        $this->client = $client;
        $this->serializer = $serializer;
        $this->timekeeper = $timekeeper;
    }

    /**
     * @throws RequestException
     */
    public function getToken(?TokenRequest $request = null): TokenResponse
    {
        $request ??= TokenRequest::clientCredentials();

        $response = $this->client->post(
            self::TOKEN,
            ['form_params' => $request->getRequestParams()]
        );

        $content = $response->getBody()->getContents();

        $token = $this->serializer->deserialize($content, TokenResponse::class, 'json');
        $token->ts = $this->timekeeper->time();

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
