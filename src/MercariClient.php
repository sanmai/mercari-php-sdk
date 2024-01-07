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
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleRetry\GuzzleRetryMiddleware;
use JMS\Serializer\Exception\RuntimeException as SerializerException;
use JMS\Serializer\SerializerInterface;
use JSONSerializer\Serializer;
use Mercari\DTO\ItemDetail;
use Mercari\DTO\Seller;
use Mercari\DTO\Transaction;
use Mercari\DTO\TransactionMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Mercari API Client.
 */
class MercariClient
{
    private const SEARCH_ITEMS_V3 = '/v3/items/search';

    private const ITEMS = '/v1/items/fetch';

    private const ITEM = '/v1/items/%s';

    private const ITEM_COMMENTS = '/v1/items/%s/comments';

    private const USER = '/v1/users/%s';

    private const SIMILAR_ITEMS = '/v1/similar_items/%s';

    private const TODO_LIST = '/v1/todolist';

    private const PURCHASE = '/v1/items/purchase';

    private const TRANSACTION = '/v1/transactions/%s';

    private const TRANSACTION_ITEM = '/v2/transactions/%s';

    private const TRANSACTION_MESSAGES = '/v2/transactions/%s/messages';

    private const TRANSACTION_REVIEW = '/v1/transactions/%s/post_review';

    private const CATEGORIES = '/v1/master/item_categories';

    public static function createInstance(string $apiHost, string $authToken, array $extraHeaders = []): self
    {
        $stack = HandlerStack::create();

        $stack->push(GuzzleRetryMiddleware::factory([
            'retry_on_status' => [409, 429, ...range(500, 505)],
        ]), 'retry_on_status');

        return new MercariClient(
            new Client([
                'debug' => defined('MERCARI_DEBUG_CURL'),
                'base_uri' => sprintf('https://%s', $apiHost),
                'connect_timeout' => 3,
                'timeout' => 120,
                'http_errors' => true,
                'allow_redirects' => false,
                'headers' => array_merge([
                    'Authorization' => "Bearer $authToken",
                ], $extraHeaders),
                'handler' => $stack,
            ]),
            $stack,
            Serializer::withJSONOptions()
        );
    }

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

    public function setLogger(LoggerInterface $logger, ?string $template = MessageFormatter::DEBUG)
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

    public const MARKETPLACE_MERCARI = 1;

    public const MARKETPLACE_SHOP = 2;

    public const MARKETPLACE_ALL = 3;
}
