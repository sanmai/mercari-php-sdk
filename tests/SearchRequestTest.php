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

use Mercari\MercariClient;
use Mercari\SearchRequest;

/**
 * @covers \Mercari\SearchRequest
 */
class SearchRequestTest extends TestCase
{
    public function testDefault()
    {
        $request = SearchRequest::build();

        $this->assertNull($request->marketplace);
    }

    public function testShops()
    {
        $request = new SearchRequest();
        $request->searchShopsOnly();

        $this->assertSame(MercariClient::MARKETPLACE_SHOP, $request->marketplace);
    }

    public function testMercari()
    {
        $request = new SearchRequest();
        $request->searchMercariOnly();

        $this->assertSame(MercariClient::MARKETPLACE_MERCARI, $request->marketplace);
    }

    public function testEverywhere()
    {
        $request = new SearchRequest();
        $request->searchBothMarketplaces();

        $this->assertSame(MercariClient::MARKETPLACE_ALL, $request->marketplace);
    }
}
