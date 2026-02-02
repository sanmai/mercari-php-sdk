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

namespace Tests\Mercari;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JSONSerializer\Serializer;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\Mercari\Doubles\ExampleMercariClient;
use Psr\Log\LoggerInterface;
use Tests\Mercari\Doubles\ExampleResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use GuzzleHttp\Exception\ServerException;

/**
 * @covers \Mercari\AbstractMercariClient
 */
class AbstractMercariClientTest extends TestCase
{
    public function testAddLogger(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('rewind');

        $stream->expects($this->once())
            ->method('getContents')
            ->willReturn(ExampleResponse::JSON);

        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn(ExampleResponse::JSON);

        $response = new Response(HttpResponse::HTTP_OK, [], $stream);

        $client = $this->buildExampleClient([$response]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with('info', $this->logicalAnd(
                $this->stringContains(HttpResponse::HTTP_OK),
                $this->stringContains(ExampleResponse::JSON),
            ));

        $client->setLogger($logger);

        /** @var ExampleResponse $response */
        $response = $client->get(ExampleResponse::class, '/');

        $this->assertSame('OK', $response->status);
    }

    public static function provideGetMethods(): iterable
    {
        yield ['get'];
        yield ['getOptional'];
    }

    /**
     * @dataProvider provideGetMethods
     */
    public function testGet(string $method): void
    {
        $responses = [
            new Response(HttpResponse::HTTP_OK, [], ExampleResponse::JSON),
        ];

        $client = $this->buildExampleClient($responses);

        /** @var ExampleResponse $response */
        $response = $client->$method(ExampleResponse::class, '/example', ['foo' => 'bar']);
        $this->assertSame('OK', $response->status);

        $this->assertSame(1, $this->getRequestsCount());

        $request = $this->getLastRequest();

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/example?foo=bar', (string) $request->getUri());
    }

    public static function providePostMethods(): iterable
    {
        yield ['post'];
        yield ['postFallback'];
    }

    /**
     * @dataProvider providePostMethods
     */
    public function testPost(string $method): void
    {
        $responses = [
            new Response(HttpResponse::HTTP_OK, [], ExampleResponse::JSON),
        ];

        $client = $this->buildExampleClient($responses);

        /** @var ExampleResponse $response */
        $response = $client->$method(ExampleResponse::class, '/example', ['foo' => 'bar']);
        $this->assertSame('OK', $response->status);

        $this->assertSame(1, $this->getRequestsCount());

        $request = $this->getLastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/example', (string) $request->getUri());
        $this->assertSame('{"foo":"bar"}', $request->getBody()->getContents());
    }

    public function testGetFallbackHappyPath(): void
    {
        $responses = [
            new Response(HttpResponse::HTTP_NOT_FOUND),
        ];

        $client = $this->buildExampleClient($responses);

        $response = $client->getOptionalDefault(ExampleResponse::class, '/example', ['foo' => 'bar']);

        $this->assertNull($response);
    }

    public function testGetFallbackOptionalCode(): void
    {
        $responses = [
            new Response(HttpResponse::HTTP_GONE),
        ];

        $client = $this->buildExampleClient($responses);

        $response = $client->getOptional(ExampleResponse::class, '/example', ['foo' => 'bar'], [HttpResponse::HTTP_GONE]);

        $this->assertNull($response);
    }

    public function testGetFallbackThrownException(): void
    {
        $responses = [
            new Response(HttpResponse::HTTP_INTERNAL_SERVER_ERROR),
        ];

        $client = $this->buildExampleClient($responses);

        $this->expectException(ServerException::class);

        $client->getOptional(ExampleResponse::class, '/example', ['foo' => 'bar']);
    }

    public function testPostFallback(): void
    {
        $client = $this->getMockBuilder(ExampleMercariClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['post', 'handleRequestException'])
            ->getMock();

        $client->expects($this->once())
            ->method('post')
            ->with(ExampleResponse::class, '/example', ['foo' => 'bar'])
            ->willThrowException(new RequestException('Internal Server Error', $this->createMock(Request::class)));

        $client->expects($this->once())
            ->method('handleRequestException')
            ->with($this->isInstanceOf(RequestException::class), ExampleResponse::class)
            ->willReturn(null);

        /** @var ExampleMercariClient&MockObject $client */
        $response = $client->postFallback(ExampleResponse::class, '/example', ['foo' => 'bar']);

        $this->assertNull($response);
    }

    public function testHandleRequestExceptionWithoutResponse(): void
    {
        $client = $this->buildExampleClient([]);
        $request = $this->createMock(Request::class);

        $exception = new RequestException('Internal Server Error', $request);

        $this->expectExceptionObject($exception);
        $client->handleRequestException($exception, ExampleResponse::class);
    }

    public function testHandleRequestExceptionWithHtmlResponse(): void
    {
        $client = $this->buildExampleClient([]);
        $request = $this->createMock(Request::class);
        $response = new Response(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, [], '<html></html>');

        $exception = new RequestException('Internal Server Error', $request, $response);

        $this->expectExceptionObject($exception);
        $client->handleRequestException($exception, ExampleResponse::class);
    }

    public function testHandleRequestExceptionWithFailure(): void
    {
        $client = $this->buildExampleClient([]);
        $request = $this->createMock(Request::class);
        $response = new Response(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, [], '{"code": 1}');

        $exception = new RequestException('Internal Server Error', $request, $response);

        $this->expectExceptionObject($exception);
        $client->handleRequestException($exception, ExampleResponse::class);
    }

    public function testHandleRequestExceptionWithoutFailure(): void
    {
        $client = $this->buildExampleClient([]);
        $request = $this->createMock(Request::class);
        $response = new Response(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, [], ExampleResponse::JSON);

        $exception = new RequestException('Internal Server Error', $request, $response);
        $response = $client->handleRequestException($exception, ExampleResponse::class);

        /** @var ExampleResponse $response */
        $this->assertSame('OK', $response->status);
    }

    public function testHandleRequestExceptionWithInvalidResponse(): void
    {
        $client = $this->buildExampleClient([]);
        $request = $this->createMock(Request::class);
        $response = new Response(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, [], '{"status":{"foo":"bar"}}');

        $exception = new RequestException('Internal Server Error', $request, $response);

        $this->expectExceptionObject($exception);

        $client->handleRequestException($exception, ExampleResponse::class);
    }

    public function testResponseToType()
    {
        $client = $this->buildExampleClient([]);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())->method('tell')->willReturn(0);
        $body->expects($this->never())->method('rewind');
        $body->expects($this->once())->method('getContents')->willReturn(ExampleResponse::JSON);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        /** @var ExampleResponse $response */
        $response = $client->responseToType($response, ExampleResponse::class);

        $this->assertSame('OK', $response->status);
    }

    private function buildExampleClient(array $responses): ExampleMercariClient
    {
        $httpClient = $this->buildHttpClient($responses, $stack);

        return new ExampleMercariClient(
            $httpClient,
            $stack,
            Serializer::withJSONOptions(),
        );

    }
}
