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
            $this->assertGreaterThanOrEqual(0, iterator_count($response));
        }

        if ($response instanceof Countable) {
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
