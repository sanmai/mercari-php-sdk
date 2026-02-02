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

use Mercari\DTO\ItemDetail;
use Mercari\ItemsResponse;

/**
 * @covers \Mercari\ItemsResponse
 */
class ItemsResponseTest extends TestCase
{
    public function provideItems(): iterable
    {
        yield 'similar_items_null.json' => [__DIR__ . '/data/similar_items_null.json'];
    }

    /**
     * @dataProvider provideItems
     */
    public function testDeserialize(string $file)
    {
        $response = $this->deserializeFile($file, ItemsResponse::class);

        $this->assertCount(0, $response->items);
    }

    public function testItemsNull()
    {
        $response = new ItemsResponse();
        $response->items = null;

        $this->assertCount(0, $response);
    }

    public function testItemsOne()
    {
        $response = new ItemsResponse();
        $response->items = [new ItemDetail()];

        $this->assertCount(1, $response);
    }
}
