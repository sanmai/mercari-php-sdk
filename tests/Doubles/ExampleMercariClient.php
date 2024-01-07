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
