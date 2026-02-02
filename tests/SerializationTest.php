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

use Mercari\CategoriesResponse;
use Mercari\CommentsResponse;
use Mercari\Failure;
use Mercari\ItemsResponse;
use Mercari\MessagesResponse;
use Mercari\NewCommentResponse;
use Mercari\PurchaseResponse;
use Mercari\ReviewResponse;
use Mercari\SearchResponse;
use Mercari\DTO\ItemDetail;
use Mercari\DTO\Transaction;
use Mercari\DTO\Webhook;
use Mercari\DTO\Seller;
use Mercari\TodoListResponse;
use PHPUnit\Framework\AssertionFailedError;
use IteratorAggregate;
use Countable;
use Iterator;

class SerializationTest extends TestCase
{
    private const PREFIX_CLASS_MAP = [
        'master_categories' => CategoriesResponse::class,
        'comments' => CommentsResponse::class,
        'failure' => Failure::class,
        'similar_items' => ItemsResponse::class,
        'txn_messages' => MessagesResponse::class,
        'new_comment' => NewCommentResponse::class,
        'purchase' => PurchaseResponse::class,
        'review' => ReviewResponse::class,
        'search' => SearchResponse::class,
        'item' => ItemDetail::class,
        'transaction' => Transaction::class,
        'webhook' => Webhook::class,
        'seller' => Seller::class,
        'todo' => TodoListResponse::class,
        'bulk' => ItemsResponse::class,
    ];

    private const NORMALIZE_IDS = [
        CategoriesResponse::class => false,
        Transaction::class => false,
        MessagesResponse::class => false,
        CommentsResponse::class => false,
    ];

    private const SKIP_FILES = [
        'todolist_null.json',
        'similar_items_null.json',
    ];

    public static function provideFiles(): iterable
    {
        $files = glob(__DIR__ . '/data/*.json');

        foreach ($files as $file) {
            $basename = basename($file);

            foreach (self::PREFIX_CLASS_MAP as $prefix => $className) {
                if (strpos($basename, $prefix) !== 0) {
                    continue;
                }

                yield $basename => [
                    $basename,
                    $file,
                    $className,
                    self::NORMALIZE_IDS[$className] ?? true,
                ];
                continue 2;
            }

            yield $basename => [$basename, $file];
        }
    }

    /**
     * @dataProvider provideFiles
     */
    public function testDeserialize(
        string $basename,
        string $file,
        ?string $className = null,
        bool $normalize_id = true
    ): void {
        if ($className === null) {
            $this->markTestIncomplete("No matching class for $basename");
        }

        $response = $this->deserializeFile($file, $className);

        if ($response instanceof IteratorAggregate) {
            $this->assertInstanceOf(Iterator::class, $response->getIterator());
            $this->assertGreaterThanOrEqual(0, iterator_count($response));
        }

        if ($response instanceof Countable) {
            $this->assertIsInt($response->count());
            $this->assertGreaterThanOrEqual(0, count($response));
        }

        try {
            $this->assertDeserializedSame($file, $response, $normalize_id);
        } catch (AssertionFailedError $e) {
            if (array_search($basename, self::SKIP_FILES) !== false) {
                return;
            }

            throw $e;
        }
    }

}
