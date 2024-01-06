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
