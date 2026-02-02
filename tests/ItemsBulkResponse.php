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

use Mercari\ItemsResponse;

/**
 * @covers \Mercari\ItemsResponse
 */
class ItemsBulkResponse extends TestCase
{
    public function testDeserializeBulk()
    {
        $file = __DIR__ . '/data/bulk_items_min.json';

        $response = $this->deserializeFile($file, ItemsResponse::class);

        $id = array_key_first($response->items);

        /** @var ItemsResponse $response */
        $this->assertSame($id, $response->items[$id]->id);

        $this->assertSame('Nice shirt', $response->items[$id]->getDescription());

        $this->assertDeserializedSame($file, $response);
    }
}
