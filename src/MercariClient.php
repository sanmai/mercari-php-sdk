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
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use JSONSerializer\Serializer;
use Mercari\DTO\ItemDetail;
use Mercari\DTO\Seller;
use Mercari\DTO\ShopsOrder;
use Mercari\DTO\Transaction;
use Mercari\DTO\TransactionMessage;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function array_merge;
use function array_filter;
use function sprintf;

/**
 * Mercari API Client.
 */
class MercariClient extends AbstractMercariClient
{
    public const MARKETPLACE_MERCARI = 1;

    public const MARKETPLACE_SHOP = 2;

    public const MARKETPLACE_ALL = 3;

    public const DECLINE_REASON_01 = 'c2b_01';

    public const DECLINE_REASON_02 = 'c2b_02';

    public const DECLINE_REASON_03 = 'c2b_03';

    public const DECLINE_REASON_04 = 'c2b_04';

    public const SERVICE_STATUS_WAIT_PROCESSING = 'wait_processing';

    public const SERVICE_STATUS_CANCELED = 'canceled';

    public const SERVICE_STATUS_DONE = 'done';

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

    private const BRANDS = '/v1/master/item_brands';

    private const GET_PARTNER_OFFERS = '/v1/get_partner_offers';

    private const ACCEPT_TRANSACTION = '/v1/accept_transaction';

    private const DECLINE_TRANSACTION = '/v1/decline_transaction';

    private const RETURN_TRACKING_ID = '/v1/return_tracking_id';

    private const SHOPS_ORDER = '/v1/shops_order/%s';

    private const ADDITIONAL_SERVICE_STATUS = '/v1/items/%s/additional_service_status';

    private const RETRY_ON_STATUS_TRANSIENT = [
        HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
        HttpResponse::HTTP_TOO_MANY_REQUESTS,
        HttpResponse::HTTP_BAD_GATEWAY,
        HttpResponse::HTTP_SERVICE_UNAVAILABLE,
        HttpResponse::HTTP_GATEWAY_TIMEOUT,
    ];

    private const RETRY_ON_STATUS = [
        HttpResponse::HTTP_CONFLICT,
        ...self::RETRY_ON_STATUS_TRANSIENT,
    ];

    /**
     * No retries on 409 for the message board.
     */
    private const MESSAGES_RETRY_OPTIONS = [
        'retry_on_status' => self::RETRY_ON_STATUS_TRANSIENT,
    ];

    private const MESSAGES_UNAVAILABLE_ON_STATUS = [
        HttpResponse::HTTP_NOT_FOUND,
        HttpResponse::HTTP_CONFLICT,
    ];

    private const ITEM_NOT_FOUND_ON_STATUS = [
        HttpResponse::HTTP_NOT_FOUND,
        HttpResponse::HTTP_BAD_REQUEST,
        HttpResponse::HTTP_FORBIDDEN,
        HttpResponse::HTTP_PRECONDITION_FAILED,
    ];

    private const USER_NOT_FOUND_ON_STATUS = [
        HttpResponse::HTTP_NOT_FOUND,
        HttpResponse::HTTP_BAD_REQUEST,
    ];

    private const SHOPS_ORDER_NOT_FOUND_ON_STATUS = [
        HttpResponse::HTTP_NOT_FOUND,
        HttpResponse::HTTP_BAD_REQUEST,
    ];

    /**
     * Build a new client instance.
     *
     * @param string $apiHost API hostname
     * @param string $authToken Bearer token from MercariAuthClient
     * @param array<string, string> $extraHeaders Additional HTTP headers to send with every request
     * @param array<string, mixed> $retryOptions Options passed to GuzzleRetryMiddleware (retry_on_status, etc.)
     * @param array<string, mixed> $clientOptions Extra Guzzle client options (timeout, connect_timeout, etc.) merged after defaults
     */
    public static function createInstance(
        string $apiHost,
        string $authToken,
        array $extraHeaders = [],
        array $retryOptions = [],
        array $clientOptions = [],
    ): self {
        $stack = HandlerStack::create();

        $stack->push(GuzzleRetryMiddleware::factory(array_merge([
            'retry_on_timeout' => true,
            'retry_on_status' => self::RETRY_ON_STATUS,
        ], $retryOptions)), 'retry_on_status');

        $httpClient = new Client(array_merge([
            'base_uri' => sprintf('https://%s', $apiHost),
            'connect_timeout' => 3,
            'timeout' => 120,
            'http_errors' => true,
            'allow_redirects' => false,
            'headers' => array_merge([
                'Authorization' => "Bearer $authToken",
            ], $extraHeaders),
            'handler' => $stack,
        ], $clientOptions));

        return new MercariClient(
            $httpClient,
            $stack,
            Serializer::withJSONOptions(),
        );
    }

    public function search(SearchRequest $request): SearchResponse
    {
        return $this->getOptional(
            SearchResponse::class,
            self::SEARCH_ITEMS_V3,
            $request->getRequestParams(),
            error_codes: [HttpResponse::HTTP_BAD_REQUEST],
        ) ?? $this->emptySearchResponse();
    }

    private function emptySearchResponse(): SearchResponse
    {
        $response = new SearchResponse();
        $response->meta->has_next = false;
        $response->meta->num_found = 0;

        return $response;
    }

    public function items(array $items): ItemsResponse
    {
        return $this->postFallback(
            ItemsResponse::class,
            self::ITEMS,
            ['item_ids' => $items],
        );
    }

    public function item(string $id, ?string $prefecture = null): ?ItemDetail
    {
        return $this->getOptional(
            ItemDetail::class,
            sprintf(self::ITEM, $id),
            array_filter(['prefecture' => $prefecture]),
            error_codes: self::ITEM_NOT_FOUND_ON_STATUS,
        );
    }

    public function itemComments(string $id): CommentsResponse
    {
        $response = $this->getOptional(
            CommentsResponse::class,
            sprintf(self::ITEM_COMMENTS, $id),
        );

        return $response ?? new CommentsResponse();
    }

    public function addComment(string $id, string $message): NewCommentResponse
    {
        return $this->post(
            NewCommentResponse::class,
            sprintf(self::ITEM_COMMENTS, $id),
            ['message' => $message],
        );
    }

    public function user(string $id): ?Seller
    {
        return $this->getOptional(
            Seller::class,
            sprintf(self::USER, $id),
            error_codes: self::USER_NOT_FOUND_ON_STATUS,
        );
    }

    public function similarItems(string $id, int $marketplace = self::MARKETPLACE_ALL): ItemsResponse
    {
        $response = $this->getOptional(
            ItemsResponse::class,
            sprintf(self::SIMILAR_ITEMS, $id),
            array_filter(['marketplace' => $marketplace]),
        );

        return $response ?? new ItemsResponse();
    }

    public function purchase(PurchaseRequest $request): PurchaseResponse
    {
        return $this->postFallback(
            PurchaseResponse::class,
            self::PURCHASE,
            $request->getRequestParams(),
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
            ]),
        );
    }

    public function transaction(string $transaction_id): ?Transaction
    {
        return $this->getOptional(
            Transaction::class,
            sprintf(self::TRANSACTION, $transaction_id),
        );
    }

    public function itemTransaction(string $item_id): ?Transaction
    {
        return $this->getOptional(
            Transaction::class,
            sprintf(self::TRANSACTION_ITEM, $item_id),
        );
    }

    public function transactionMessages(string $transaction_id): MessagesResponse
    {
        $response = $this->getOptional(
            MessagesResponse::class,
            sprintf(self::TRANSACTION_MESSAGES, $transaction_id),
            error_codes: self::MESSAGES_UNAVAILABLE_ON_STATUS,
            options: self::MESSAGES_RETRY_OPTIONS,
        );

        return $response ?? new MessagesResponse();
    }

    public function transactionMessage(string $transaction_id, string $message): TransactionMessage
    {
        return $this->post(
            TransactionMessage::class,
            sprintf(self::TRANSACTION_MESSAGES, $transaction_id),
            ['message' => $message],
            self::MESSAGES_RETRY_OPTIONS,
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
            ],
        );

        if ($response->isSuccess()) {
            return;
        }

        throw new DTO\Exception(sprintf('%s (%s)', $response->failure_details->reasons, $response->failure_details->code));
    }

    public function categories(array $headers = []): CategoriesResponse
    {
        return $this->get(
            CategoriesResponse::class,
            self::CATEGORIES,
            headers: $headers,
        );
    }

    /**
     * @throws NotModifiedException When the list did not change since the last call.
     */
    public function brands(array $headers = []): BrandsResponse
    {
        return $this->get(
            BrandsResponse::class,
            self::BRANDS,
            headers: $headers,
        );
    }

    public function partnerOffers(int $page = 0, int $limit = 50, ?string $item_id = null): PartnerOffersResponse
    {
        $response = $this->getOptional(
            PartnerOffersResponse::class,
            self::GET_PARTNER_OFFERS,
            ['page' => $page, 'limit' => $limit] + array_filter(['item_id' => $item_id]),
        );

        return $response ?? new PartnerOffersResponse();
    }

    public function acceptTransaction(AcceptTransactionRequest $request): PurchaseResponse
    {
        return $this->postFallback(
            PurchaseResponse::class,
            self::ACCEPT_TRANSACTION,
            $request->getRequestParams(),
        );
    }

    public function declineTransaction(string $transaction_id, string $cancellation_reason): PurchaseResponse
    {
        return $this->postFallback(
            PurchaseResponse::class,
            self::DECLINE_TRANSACTION,
            [
                'transaction_id' => $transaction_id,
                'cancellation_reason' => $cancellation_reason,
            ],
        );
    }

    public function returnTransaction(string $transaction_id, string $tracking_id, string $shipping_carrier_name): ReturnResponse
    {
        return $this->postFallback(
            ReturnResponse::class,
            self::RETURN_TRACKING_ID,
            [
                'transaction_id' => $transaction_id,
                'tracking_id' => $tracking_id,
                'shipping_carrier_name' => $shipping_carrier_name,
            ],
        );
    }

    public function shopsOrder(string $order_id): ?ShopsOrder
    {
        return $this->getOptional(
            ShopsOrder::class,
            sprintf(self::SHOPS_ORDER, $order_id),
            error_codes: self::SHOPS_ORDER_NOT_FOUND_ON_STATUS,
        );
    }

    /**
     * @param array<array{code: string, note?: string}> $reasons
     */
    public function updateAdditionalServiceStatus(
        string $item_id,
        string $status,
        ?string $tracking_number = null,
        array $reasons = [],
    ): void {
        $this->put(
            sprintf(self::ADDITIONAL_SERVICE_STATUS, $item_id),
            ['status' => $status] + array_filter([
                'tracking_number' => $tracking_number,
                'reasons' => $reasons,
            ]),
        );
    }
}
