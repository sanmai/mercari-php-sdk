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
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Exception\RuntimeException as SerializerException;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function in_array;

/**
 * Abstract Mercari API Client.
 */
abstract class AbstractMercariClient
{
    private Client $client;

    private HandlerStack $stack;

    private SerializerInterface $serializer;

    /**
     * Create a new instance.
     */
    public function __construct(Client $client, HandlerStack $stack, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->stack = $stack;
        $this->serializer = $serializer;
    }

    public function setLogger(LoggerInterface $logger, ?string $template = MessageFormatter::DEBUG): self
    {
        $this->stack->push(Middleware::mapResponse(function (Response $response) {
            $response->getBody()->rewind();
            return $response;
        }));

        $this->stack->push(Middleware::log(
            $logger,
            new MessageFormatter($template)
        ));

        return $this;
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T|null
     */
    protected function getOptional(
        string $type,
        string $uri,
        array $query = [],
        array $error_codes = [HttpResponse::HTTP_NOT_FOUND]
    ) {
        try {
            return $this->get($type, $uri, $query);
        } catch (RequestException $e) {
            if (in_array($e->getCode(), $error_codes, true)) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    protected function get(string $type, string $uri, array $query = [])
    {
        $response = $this->client->get($uri, [
            'query' => $query,
        ]);

        return $this->responseToType($response, $type);
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    protected function post(string $type, string $uri, array $json)
    {
        $response = $this->client->post(
            $uri,
            ['json' => $json]
        );

        return $this->responseToType($response, $type);
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    protected function postFallback(string $type, string $uri, array $json)
    {
        try {
            return $this->post($type, $uri, $json);
        } catch (RequestException $clientError) {
            return $this->handleRequestException($clientError, $type);
        }
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    protected function handleRequestException(RequestException $clientError, string $type)
    {
        $response = $clientError->getResponse();
        if ($response === null) {
            throw $clientError;
        }

        try {
            /** @var Failure $failure */
            $failure = $this->responseToType($response, Failure::class);
        } catch (SerializerException) {
            throw $clientError;
        }

        if ($failure->code > 0) {
            throw $clientError;
        }

        try {
            return $this->responseToType($response, $type);
        } catch (SerializerException) {
            throw $clientError;
        }
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    protected function responseToType(ResponseInterface $response, string $type)
    {
        $body = $response->getBody();

        if ($body->tell() > 0) {
            $body->rewind();
        }

        return $this->serializer->deserialize(
            $body->getContents(),
            $type,
            'json'
        );
    }
}
