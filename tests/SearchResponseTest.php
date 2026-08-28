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
use Mercari\SearchResponse;

/**
 * @covers \Mercari\SearchResponse
 */
class SearchResponseTest extends TestCase
{
    public function testDeserialize()
    {
        /** @var SearchResponse $response */
        $response = $this->deserializeFile(__DIR__ . '/data/search_response_min.json', SearchResponse::class);

        $this->assertCount(1, $response);
        $this->assertContainsOnlyInstancesOf(ItemDetail::class, $response);

        $this->assertTrue($response->meta->has_next);
        $this->assertSame(15000, $response->meta->num_found);
    }

    public function testEmpty()
    {
        $this->assertCount(0, new SearchResponse());
    }
}
