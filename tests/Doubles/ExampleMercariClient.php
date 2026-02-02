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

namespace Tests\Mercari\Doubles;

use GuzzleHttp\Exception\RequestException;
use Mercari\AbstractMercariClient;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ExampleMercariClient extends AbstractMercariClient
{
    public function getOptional(
        string $type,
        string $uri,
        array $query = [],
        array $error_codes = [HttpResponse::HTTP_NOT_FOUND]
    ) {
        return parent::getOptional($type, $uri, $query, $error_codes);
    }

    public function getOptionalDefault(
        string $type,
        string $uri,
        array $query
    ) {
        return parent::getOptional($type, $uri, $query);
    }

    public function get(string $type, string $uri, array $query = [])
    {
        return parent::get($type, $uri, $query);
    }

    public function post(string $type, string $uri, array $json)
    {
        return parent::post($type, $uri, $json);
    }

    public function postFallback(string $type, string $uri, array $json)
    {
        return parent::postFallback($type, $uri, $json);
    }

    public function handleRequestException(RequestException $clientError, string $type)
    {
        return parent::handleRequestException($clientError, $type);
    }

    public function responseToType(ResponseInterface $response, string $type)
    {
        return parent::responseToType($response, $type);
    }

}
