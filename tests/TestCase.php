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

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;
use JSONSerializer;

use ReflectionObject;

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
            json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
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

    protected function getPropertyValue($client, $propertyName)
    {
        $reflection = new ReflectionObject($client);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($client);
    }

    protected function buildHttpClient(array $responses): Client
    {
        $mock = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($this->requests));

        return new Client(['handler' => $handlerStack]);
    }

    protected function getLastRequest(): Request
    {
        return end($this->requests)['request'];
    }
}
