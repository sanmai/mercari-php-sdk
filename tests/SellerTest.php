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

use Mercari\DTO\Seller;

/**
 * @covers \Mercari\DTO\Seller
 */
class SellerTest extends TestCase
{
    public function provideItems(): iterable
    {
        yield 'seller.json' => [__DIR__ . '/data/seller.json', 773746477];
        yield 'seller_actual.json' => [__DIR__ . '/data/seller_actual.json', 810795681];
        yield 'seller_badges.json' => [__DIR__ . '/data/seller_badges.json', 712229671];
        yield 'seller_badge_actual.json' => [__DIR__ . '/data/seller_badge_actual.json', 646991664];
    }

    /**
     * @dataProvider provideItems
     */
    public function testDeserialize(string $file, int $id)
    {
        $response = $this->deserializeFile($file, Seller::class);

        /** @var Seller $response */
        $this->assertSame($id, $response->id);

        $this->assertDeserializedSame($file, $response);
    }
}
