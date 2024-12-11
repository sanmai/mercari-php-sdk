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

namespace Tests\Mercari\DTO;

use Mercari\DTO\SellerLatest;
use Tests\Mercari\TestCase;

/**
 * @covers \Mercari\DTO\SellerLatest
 */
class SellerLatestTest extends TestCase
{
    public function testNoFallback()
    {
        $seller = new SellerLatest();
        $seller->id = 42;
        $seller->name = 'foo';
        $seller->shop_id = 'bar';

        $this->assertSame(42, $seller->getAnyId());
    }

    public function testFallbackShopId()
    {
        $seller = new SellerLatest();
        $seller->shop_id = 'bar';
        $seller->name = 'foo';

        $this->assertSame('bar', $seller->getAnyId());
    }

    public function testFallbackName()
    {
        $seller = new SellerLatest();
        $seller->shop_id = 'foo';

        $this->assertSame('foo', $seller->getAnyId());
    }
}
