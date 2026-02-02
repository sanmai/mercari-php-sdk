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
