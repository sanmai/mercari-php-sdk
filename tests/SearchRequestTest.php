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
