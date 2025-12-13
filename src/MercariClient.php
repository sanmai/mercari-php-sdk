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
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use JSONSerializer\Serializer;
use Mercari\DTO\ItemDetail;
use Mercari\DTO\Seller;
use Mercari\DTO\Transaction;
use Mercari\DTO\TransactionMessage;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function array_merge;

/**
 * Mercari API Client.
 */
class MercariClient extends AbstractMercariClient
{
    public const MARKETPLACE_MERCARI = 1;

    public const MARKETPLACE_SHOP = 2;

    public const MARKETPLACE_ALL = 3;

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

    public static function createInstance(string $apiHost, string $authToken, array $extraHeaders = [], array $retryOptions = []): self
    {
        $stack = HandlerStack::create();

        $stack->push(GuzzleRetryMiddleware::factory(array_merge([
            'retry_on_status' => [
                HttpResponse::HTTP_CONFLICT,
                HttpResponse::HTTP_TOO_MANY_REQUESTS,
                HttpResponse::HTTP_BAD_GATEWAY,
                HttpResponse::HTTP_SERVICE_UNAVAILABLE,
                HttpResponse::HTTP_GATEWAY_TIMEOUT,
            ],
        ], $retryOptions)), 'retry_on_status');

        $httpClient = new Client([
            'base_uri' => sprintf('https://%s', $apiHost),
            'connect_timeout' => 3,
            'timeout' => 120,
            'http_errors' => true,
            'allow_redirects' => false,
            'headers' => array_merge([
                'Authorization' => "Bearer $authToken",
            ], $extraHeaders),
            'handler' => $stack,
        ]);

        return new MercariClient(
            $httpClient,
            $stack,
            Serializer::withJSONOptions()
        );
    }

    public function search(SearchRequest $request): SearchResponse
    {
        return $this->get(
            SearchResponse::class,
            self::SEARCH_ITEMS_V3,
            $request->getRequestParams()
        );
    }

    public function items(array $items): ItemsResponse
    {
        return $this->postFallback(
            ItemsResponse::class,
            self::ITEMS,
            ['item_ids' => $items]
        );
    }

    public function item(string $id, ?string $prefecture = null): ?ItemDetail
    {
        return $this->getOptional(
            ItemDetail::class,
            sprintf(self::ITEM, $id),
            array_filter(['prefecture' => $prefecture]),
            [
                HttpResponse::HTTP_NOT_FOUND,
                HttpResponse::HTTP_BAD_REQUEST,
                HttpResponse::HTTP_FORBIDDEN,
                HttpResponse::HTTP_PRECONDITION_FAILED,
            ]
        );
    }

    public function itemComments(string $id): CommentsResponse
    {
        $response = $this->getOptional(
            CommentsResponse::class,
            sprintf(self::ITEM_COMMENTS, $id)
        );

        return $response ?? new CommentsResponse();
    }

    public function addComment(string $id, string $message): NewCommentResponse
    {
        return $this->post(
            NewCommentResponse::class,
            sprintf(self::ITEM_COMMENTS, $id),
            ['message' => $message]
        );
    }

    public function user(string $id): ?Seller
    {
        return $this->getOptional(
            Seller::class,
            sprintf(self::USER, $id)
        );
    }

    public function similarItems(string $id, int $marketplace = self::MARKETPLACE_ALL): ItemsResponse
    {
        $response = $this->getOptional(
            ItemsResponse::class,
            sprintf(self::SIMILAR_ITEMS, $id),
            array_filter(['marketplace' => $marketplace])
        );

        return $response ?? new ItemsResponse();
    }

    public function purchase(PurchaseRequest $request): PurchaseResponse
    {
        return $this->postFallback(
            PurchaseResponse::class,
            self::PURCHASE,
            $request->getRequestParams()
        );
    }

    public function todoList(int $limit = 10, string $page_token = ''): TodoListResponse
    {
        return $this->get(
            TodoListResponse::class,
            self::TODO_LIST,
            array_filter([
                'limit' => $limit,
                'page_token' => $page_token,
            ])
        );
    }

    public function transaction(string $transaction_id): ?Transaction
    {
        return $this->getOptional(
            Transaction::class,
            sprintf(self::TRANSACTION, $transaction_id)
        );
    }

    public function itemTransaction(string $item_id): ?Transaction
    {
        return $this->getOptional(
            Transaction::class,
            sprintf(self::TRANSACTION_ITEM, $item_id)
        );
    }

    public function transactionMessages(string $transaction_id): MessagesResponse
    {
        $response = $this->getOptional(
            MessagesResponse::class,
            sprintf(self::TRANSACTION_MESSAGES, $transaction_id)
        );

        return $response ?? new MessagesResponse();
    }

    public function transactionMessage(string $transaction_id, string $message): TransactionMessage
    {
        return $this->post(
            TransactionMessage::class,
            sprintf(self::TRANSACTION_MESSAGES, $transaction_id),
            ['message' => $message]
        );
    }

    public function transactionReview(string $transaction_id, string $message, string $fame = 'good'): void
    {
        /** @var ReviewResponse $response */
        $response = $this->postFallback(
            ReviewResponse::class,
            sprintf(self::TRANSACTION_REVIEW, $transaction_id),
            [
                'fame' => $fame,
                'message' => $message,
                'subject' => 'seller',
            ]
        );

        if ($response->isSuccess()) {
            return;
        }

        throw new DTO\Exception(sprintf('%s (%s)', $response->failure_details->reasons, $response->failure_details->code));
    }

    public function categories(): CategoriesResponse
    {
        $response = $this->getOptional(
            CategoriesResponse::class,
            self::CATEGORIES
        );

        return $response ?? new CategoriesResponse();
    }
}
