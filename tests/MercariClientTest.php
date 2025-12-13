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

namespace Tests\Mercari;

use Mercari\CategoriesResponse;
use Mercari\CommentsResponse;
use Mercari\DTO;
use Mercari\DTO\ItemDetail;
use Mercari\DTO\Seller;
use Mercari\DTO\Transaction;
use Mercari\DTO\TransactionMessage;
use Mercari\ItemsResponse;
use Mercari\MercariClient;
use Mercari\MessagesResponse;
use Mercari\NewCommentResponse;
use Mercari\PurchaseRequest;
use Mercari\PurchaseResponse;
use Mercari\ReviewResponse;
use Mercari\SearchRequest;
use Mercari\SearchResponse;
use Mercari\TodoListResponse;
use GuzzleHttp\Client;
use PHPUnit\Framework\MockObject\MockObject;
use GuzzleHttp\HandlerStack;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use GuzzleRetry\GuzzleRetryMiddleware;

/**
 * @covers \Mercari\MercariClient
 */
class MercariClientTest extends TestCase
{
    /** @var MercariClient&MockObject */
    private $client;

    public function setUp(): void
    {
        $this->client = $this->getMockBuilder(MercariClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'get',
                'post',
                'getOptional',
                'postFallback',
            ])
            ->getMock();
    }

    public function testCreateInstance(): void
    {
        $client = MercariClient::createInstance('sandbox-api.example.com', 'token', ['Foo' => 'bar']);

        $this->assertInstanceOf(MercariClient::class, $client);

        /** @var Client $httpClient */
        $httpClient = $this->getPropertyValue($client, 'client');

        $this->assertSame('bar', $httpClient->getConfig('headers')['Foo']);
        $this->assertSame("Bearer token", $httpClient->getConfig('headers')['Authorization']);

        $this->assertNull($httpClient->getConfig('auth'));

        $this->assertSame('https://sandbox-api.example.com', (string) $httpClient->getConfig('base_uri'));

        $this->assertTrue($httpClient->getConfig('http_errors'));
        $this->assertFalse($httpClient->getConfig('allow_redirects'));

        $this->assertSame(3, $httpClient->getConfig('connect_timeout'));
        $this->assertSame(120, $httpClient->getConfig('timeout'));

        /** @var HandlerStack $handler */
        $handler = $httpClient->getConfig('handler');

        $this->assertSame($handler, $this->getPropertyValue($client, 'stack'));

        $this->assertStringContainsString('retry_on_status', (string) $handler);

        $retryMiddleware = $this->getRetryMiddleware($handler);

        $statusCodes = $this->getPropertyValue($retryMiddleware, 'defaultOptions')['retry_on_status'];

        foreach ($statusCodes as $code) {
            $this->assertIsInt($code);
        }

        $this->assertCount(5, $statusCodes);
    }

    private function getRetryMiddleware(HandlerStack $handler): GuzzleRetryMiddleware
    {
        $stack = $this->getPropertyValue($handler, 'stack');
        foreach ($stack as $item) {
            if ($item[1] === "retry_on_status") {
                return $item[0](fn() => null);
            }
        }

        $this->fail('retry_on_status middleware not found');
    }

    public function testSearch(): void
    {
        $response = new SearchResponse();

        $this->clientExpects(
            'get',
            $response,
            $this->stringContains('search'),
            $this->logicalAnd(
                $this->arrayHasKey('keyword'),
                $this->containsIdentical('foo')
            )
        );

        $request = new SearchRequest();
        $request->keyword = 'foo';

        $responseActual = $this->client->search($request);

        $this->assertSame($response, $responseActual);
    }

    public function testItems()
    {
        $response = new ItemsResponse();

        $this->clientExpects(
            'postFallback',
            $response,
            $this->stringContains('items'),
            $this->logicalAnd(
                $this->arrayHasKey('item_ids'),
                $this->containsIdentical(['foo'])
            )
        );

        $responseActual = $this->client->items(['foo']);

        $this->assertSame($response, $responseActual);
    }

    public function testItem(): void
    {
        $response = new ItemDetail();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('foo')
            ),
            $this->identicalTo([]),
            $this->identicalTo([
                HttpResponse::HTTP_NOT_FOUND,
                HttpResponse::HTTP_BAD_REQUEST,
                HttpResponse::HTTP_FORBIDDEN,
                HttpResponse::HTTP_PRECONDITION_FAILED,
            ])
        );

        $responseActual = $this->client->item('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testItemPrefecture(): void
    {
        $response = new ItemDetail();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('foo')
            ),
            $this->identicalTo(['prefecture' => 'bar']),
            $this->identicalTo([
                HttpResponse::HTTP_NOT_FOUND,
                HttpResponse::HTTP_BAD_REQUEST,
                HttpResponse::HTTP_FORBIDDEN,
                HttpResponse::HTTP_PRECONDITION_FAILED,
            ])
        );

        $responseActual = $this->client->item('foo', 'bar');

        $this->assertSame($response, $responseActual);
    }

    public function testItemComments(): void
    {
        $response = new CommentsResponse();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('foo'),
                $this->stringContains('comments')
            )
        );

        $responseActual = $this->client->itemComments('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testAddComment(): void
    {
        $response = new NewCommentResponse();

        $this->clientExpects(
            'post',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('comments'),
                $this->stringContains('foo')
            ),
            $this->logicalAnd(
                $this->containsIdentical('bar'),
            )
        );

        $responseActual = $this->client->addComment('foo', 'bar');

        $this->assertSame($response, $responseActual);
    }

    public function testUser(): void
    {
        $response = new Seller();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('user'),
                $this->stringContains('foo')
            )
        );

        $responseActual = $this->client->user('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testSimilarItems()
    {
        $response = new ItemsResponse();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('foo'),
                $this->stringContains('similar_items')
            ),
            $this->identicalTo([
                'marketplace' => MercariClient::MARKETPLACE_ALL,
            ])
        );

        $responseActual = $this->client->similarItems('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testSimilarItemsNoMarketplace()
    {
        $response = new ItemsResponse();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('item'),
                $this->stringContains('foo'),
                $this->stringContains('similar_items')
            ),
            $this->identicalTo([])
        );

        $responseActual = $this->client->similarItems('foo', 0);

        $this->assertSame($response, $responseActual);
    }

    public function testPurchase(): void
    {
        $params = ['item_id' => '1234'];

        $request = $this->getMockBuilder(PurchaseRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRequestParams'])
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getRequestParams')
            ->willReturn($params);

        $response = new PurchaseResponse();

        $this->clientExpects(
            'postFallback',
            $response,
            $this->stringContains('purchase'),
            $this->identicalTo($params)
        );

        $responseActual = $this->client->purchase($request);

        $this->assertSame($response, $responseActual);
    }

    public function testTodoList(): void
    {
        $response = new TodoListResponse();

        $this->clientExpects(
            'get',
            $response,
            $this->stringContains('todolist'),
            $this->identicalTo([
                'limit' => 10,
            ])
        );

        $responseActual = $this->client->todoList();

        $this->assertSame($response, $responseActual);
    }

    public function testTodoListCustom(): void
    {
        $response = new TodoListResponse();

        $this->clientExpects(
            'get',
            $response,
            $this->stringContains('todolist'),
            $this->identicalTo([
                'limit' => 20,
                'page_token' => 'foo',
            ])
        );

        $responseActual = $this->client->todoList(20, 'foo');

        $this->assertSame($response, $responseActual);
    }

    public function testTransaction(): void
    {
        $response = new Transaction();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
            )
        );

        $responseActual = $this->client->transaction('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testTransactionItem(): void
    {
        $response = new Transaction();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
            )
        );

        $responseActual = $this->client->itemTransaction('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testTransactionMessages(): void
    {
        $response = new MessagesResponse();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
                $this->stringContains('messages'),
            )
        );

        $responseActual = $this->client->transactionMessages('foo');

        $this->assertSame($response, $responseActual);
    }

    public function testTransactionMessage()
    {
        $response = new TransactionMessage();

        $this->clientExpects(
            'post',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
                $this->stringContains('messages'),
            ),
            $this->identicalTo([
                'message' => 'bar',
            ])
        );

        $responseActual = $this->client->transactionMessage('foo', 'bar');

        $this->assertSame($response, $responseActual);
    }

    public function testTransactionReview(): void
    {
        $response = $this->createMock(ReviewResponse::class);
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->clientExpects(
            'postFallback',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
                $this->stringContains('review'),
            ),
            $this->identicalTo([
                'fame' => 'good',
                'message' => 'bar',
                'subject' => 'seller',
            ])
        );

        $this->client->transactionReview('foo', 'bar');
    }

    public function testTransactionReviewFame(): void
    {
        $response = $this->createMock(ReviewResponse::class);
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->clientExpects(
            'postFallback',
            $response,
            $this->logicalAnd(
                $this->stringContains('transaction'),
                $this->stringContains('foo'),
                $this->stringContains('review'),
            ),
            $this->identicalTo([
                'fame' => 'neutral',
                'message' => 'bar',
                'subject' => 'seller',
            ])
        );

        $this->client->transactionReview('foo', 'bar', 'neutral');
    }


    public function testTransactionReviewException()
    {
        $response = $this->createMock(ReviewResponse::class);
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $response->failure_details = new DTO\FailureDetails();
        $response->failure_details->reasons = 'Reviews not allowed for this user';
        $response->failure_details->code = 'F1001';

        $this->clientExpects(
            'postFallback',
            $response,
        );

        $this->expectException(DTO\Exception::class);
        $this->expectExceptionMessage('Reviews not allowed for this user (F1001)');
        $this->client->transactionReview('foo', 'bar');
    }

    public function testCategories()
    {
        $response = new CategoriesResponse();

        $this->clientExpects(
            'getOptional',
            $response,
            $this->stringContains('categories'),
        );

        $responseActual = $this->client->categories();

        $this->assertSame($response, $responseActual);
    }

    public function testCategoriesOptional(): void
    {
        $this->clientExpects(
            'getOptional',
            CategoriesResponse::class,
            $this->stringContains('categories'),
        );

        $responseActual = $this->client->categories();

        $this->assertInstanceOf(CategoriesResponse::class, $responseActual);
    }

    private function clientExpects($method, $response, ...$args)
    {
        $responseClass = is_string($response) ? $response : get_class($response);

        if (strpos($responseClass, 'Mock') !== false) {
            $responseClass = get_parent_class($responseClass);
        }

        $return = is_string($response) ? null : $response;

        $this->client->expects($this->once())
            ->method($method)
            ->with(
                $responseClass,
                ...$args
            )
            ->willReturn($return);
    }
}
