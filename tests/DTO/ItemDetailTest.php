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

use Mercari\DTO\ItemDetail;
use Mercari\DTO\SellerLatest;
use Tests\Mercari\TestCase;

/**
 * @covers \Mercari\DTO\ItemDetail
 */
class ItemDetailTest extends TestCase
{
    public function testIsAvailable()
    {
        $item = new ItemDetail();

        $item->status = ItemDetail::SOLD_OUT;
        $this->assertFalse($item->isAvailable());

        $item->status = ItemDetail::ON_SALE;
        $this->assertTrue($item->isAvailable());
    }

    public function testGetPurchaseRequest()
    {
        $item = new ItemDetail();
        $item->status = ItemDetail::ON_SALE;
        $item->id = '123';
        $item->checksum = '456';

        $request = $item->getPurchaseRequest();

        $this->assertSame('123', $request->item_id);
        $this->assertSame('456', $request->checksum);
    }

    public function testGetPurchaseRequestThrows()
    {
        $item = new ItemDetail();
        $item->status = ItemDetail::SOLD_OUT;

        $this->expectExceptionMessage('Item is not available for sale');

        $item->getPurchaseRequest();
    }

    public function testAlwaysDescription()
    {
        $item = new ItemDetail();
        $this->assertSame('', $item->getDescription());

        $item->description = '123';

        $this->assertSame('123', $item->getDescription());
    }

    public function testGetUrl()
    {
        $item = new ItemDetail();
        $item->id = '123abCD"';

        $item->item_type = ItemDetail::ITEM_TYPE_MERCARI;
        $this->assertSame('https://jp.mercari.com/item/123abCD%22', $item->getUrl());

        $item->item_type = '';
        $this->assertSame('https://jp.mercari.com/shops/product/123abCD%22', $item->getUrl());
    }

    public function testIsMercariC2C()
    {
        $item = new ItemDetail();
        $item->id = 'm1234567654321';

        $item->item_type = ItemDetail::ITEM_TYPE_MERCARI;
        $this->assertTrue($item->isMercariC2C());

        $item->item_type = '';
        $this->assertFalse($item->isMercariC2C());

        unset($item->item_type);
        $item->seller = new SellerLatest();
        $item->seller->shop_id = '123';
        $this->assertFalse($item->isMercariC2C());

        $item->seller = new SellerLatest();
        $item->id = str_repeat('a', ItemDetail::SHOPS_ID_LENGTH);
        $item->seller->shop_id = '123';
        $this->assertFalse($item->isMercariC2C());
    }

    public function testIsMercariC2CItemIdLength()
    {
        $item = new ItemDetail();
        $item->id = str_repeat('m', ItemDetail::SHOPS_ID_LENGTH);

        $this->assertFalse($item->isMercariC2C());
    }
}
