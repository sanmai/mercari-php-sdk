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

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;
use JSONSerializer;
use ReflectionObject;
use ReflectionException;

use function Pipeline\take;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected SerializerInterface $serializer;
    protected array $requests = [];

    protected function setUp(): void
    {
        $this->serializer = JSONSerializer\Serializer::withJSONOptions(JSON_PRETTY_PRINT);
        $this->requests = [];
    }

    protected static function krsort(&$array)
    {
        if (!is_array($array)) {
            return;
        }

        array_walk($array, fn(&$array) => self::krsort($array));
        ksort($array);
    }

    protected static function filesByPrefix(string $prefix): iterable
    {
        return take(glob(__DIR__ . '/data/*.json'))
            ->filter(fn($file) => strpos(basename($file), $prefix) === 0)
            ->map(fn($file) => yield basename($file) => $file);
    }

    /**
     * @param string $file
     * @param object $response
     * @param bool $normalize_id Whenever to normalize IDs from strings to integers (Mercari sometimes provides strings)
     */
    protected function assertDeserializedSame(string $file, $response, bool $normalize_id = true): void
    {
        $contents = file_get_contents($file);

        if ($normalize_id) {
            $contents = preg_replace('/"id":(\s*)"(\d+)"/', '"id":\1\2', $contents);
        }

        $expected = json_decode($contents, true);
        self::krsort($expected);

        $actual = json_decode($this->serializer->serialize($response, 'json'), true);
        self::krsort($actual);

        $this->assertSame(
            json_encode($expected, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            sprintf(
                "Failed to deserialize %s to %s",
                str_replace(dirname(__DIR__), '.', $file),
                get_class($response)
            )
        );
    }

    protected function deserializeFile(string $file, string $type)
    {
        if (!is_file($file) && isset($_SERVER['CI'])) {
            $this->markTestIncomplete(sprintf('File not found: %s', $file));
        }

        $this->assertFileExists($file);

        return $this->serializer->deserialize(file_get_contents($file), $type, 'json');
    }

    protected function getPropertyValue($client, string $propertyName)
    {
        $reflection = new ReflectionObject($client);

        try {
            $property = $reflection->getProperty($propertyName);
        } catch (ReflectionException $e) {
            $property = $reflection->getParentClass()->getProperty($propertyName);
        }

        $property->setAccessible(true);

        return $property->getValue($client);
    }

    protected function buildHttpClient(array $responses, &$handlerStack = null): Client
    {
        $mock = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($this->requests));

        return new Client(['handler' => $handlerStack]);
    }

    protected function getRequestsCount(): int
    {
        return count($this->requests);
    }

    protected function getLastRequest(): Request
    {
        return end($this->requests)['request'];
    }
}
