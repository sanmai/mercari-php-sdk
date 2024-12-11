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

/**
 * @covers \Mercari\DTO\ItemDetail
 * @covers \Mercari\PurchaseRequest
 * @covers \Mercari\DTO\SellerLatest
 */
class ItemDetailTest extends TestCase
{
    public static function provideItems(): iterable
    {
        foreach (self::filesByPrefix('item') as $basename => $path) {
            yield $basename => [$path];
        }
    }

    /**
     * @dataProvider provideItems
     */
    public function testDeserialize(string $file)
    {
        /** @var ItemDetail $item */
        $item = $this->deserializeFile($file, ItemDetail::class);

        $this->assertDeserializedSame($file, $item);

        $this->assertSame(strpos($item->id, 'm') === 0, $item->isMercariC2C(), "C2C item with unexpected ID {$item->id}");

        $this->assertStringContainsString($item->id, $item->getUrl());

        if (!$item->isMercariC2C()) {
            $this->assertStringContainsString('shops', $item->getUrl());
        }

        $this->assertNotNull($item->seller->getAnyId());

        if (!$item->isAvailable()) {
            $this->expectExceptionMessage('Item is not available for sale');
        }

        $request = $item->getPurchaseRequest();

        if ($item->has_promotions ?? false) {
            $this->assertSame($item->item_discount->coupon_id, $request->coupon_id);
        }

        $this->assertSame($item->id, $request->item_id);
        $this->assertSame($item->checksum, $request->checksum);
    }
}
