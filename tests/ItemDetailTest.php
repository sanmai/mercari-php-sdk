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
