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
