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
use Mercari\DTO\ItemDiscount;
use Mercari\DTO\ItemVariant;
use Mercari\PurchaseRequest;

/**
 * @covers \Mercari\PurchaseRequest
 */
class PurchaseRequestTest extends TestCase
{
    public function testDefault()
    {
        $request = new PurchaseRequest();

        $this->assertNull($request->item_id);

        $this->assertSame([], $request->getRequestParams());
        $this->assertSame('{}', json_encode($request, JSON_FORCE_OBJECT));
    }

    public function testFromItem()
    {
        $itemId = '123123';
        $checksum = 'FFFFF';

        $item = new ItemDetail();
        $item->id = $itemId;
        $item->checksum = $checksum;

        $request = new PurchaseRequest($item);

        $this->assertSame($itemId, $request->item_id);
        $this->assertSame($checksum, $request->checksum);
    }

    public function testFromItemWithCoupon()
    {
        $itemId = '123123';
        $checksum = 'FFFFF';
        $coupon_id = 123;

        $item = new ItemDetail();
        $item->id = $itemId;
        $item->checksum = $checksum;
        $item->item_discount = new ItemDiscount();
        $item->item_discount->coupon_id = $coupon_id;

        $request = new PurchaseRequest($item);

        $this->assertSame($itemId, $request->item_id);
        $this->assertSame($checksum, $request->checksum);
        $this->assertSame($coupon_id, $request->coupon_id);
    }

    public function testFromItemWithVariant()
    {
        $itemId = '123123';
        $checksum = 'FFFFF';
        $variant_id = 'HHHHH';

        $variant = new ItemVariant();
        $variant->id = $variant_id;

        $item = new ItemDetail();
        $item->id = $itemId;
        $item->checksum = $checksum;
        $item->item_variants = [$variant];

        $request = new PurchaseRequest($item);

        $this->assertSame($itemId, $request->item_id);
        $this->assertSame($checksum, $request->checksum);
        $this->assertSame($variant_id, $request->variant_id);
    }
}
