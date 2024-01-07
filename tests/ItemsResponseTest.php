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
