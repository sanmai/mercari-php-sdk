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

use Mercari\Enum\Color;
use Mercari\Enum\ItemCondition;
use Mercari\Enum\ItemStatus;
use Mercari\Enum\Marketplace;
use Mercari\Enum\ShippingPayer;
use Mercari\Enum\SortBy;
use Mercari\Enum\SortOrder;
use Mercari\MercariClient;
use BackedEnum;

/**
 * @covers \Mercari\Enum\Color
 * @covers \Mercari\Enum\ItemCondition
 * @covers \Mercari\Enum\ItemStatus
 * @covers \Mercari\Enum\Marketplace
 * @covers \Mercari\Enum\ShippingPayer
 * @covers \Mercari\Enum\SortBy
 * @covers \Mercari\Enum\SortOrder
 */
class EnumTest extends TestCase
{
    public static function provideCases(): iterable
    {
        yield ItemCondition::class => [ItemCondition::class, [
            'BrandNew' => 1,
            'LikeNew' => 2,
            'NoNoticeableDamage' => 3,
            'SlightDamage' => 4,
            'Damaged' => 5,
            'PoorCondition' => 6,
        ]];

        yield ShippingPayer::class => [ShippingPayer::class, [
            'Buyer' => 1,
            'Seller' => 2,
        ]];

        yield Color::class => [Color::class, [
            'Black' => 1,
            'White' => 2,
            'Grey' => 3,
            'Brown' => 4,
            'Red' => 5,
            'Pink' => 6,
            'Purple' => 7,
            'Blue' => 8,
            'Beige' => 9,
            'Green' => 10,
            'Yellow' => 11,
            'Orange' => 12,
        ]];

        yield Marketplace::class => [Marketplace::class, [
            'Mercari' => MercariClient::MARKETPLACE_MERCARI,
            'Shops' => MercariClient::MARKETPLACE_SHOP,
            'All' => MercariClient::MARKETPLACE_ALL,
        ]];

        yield ItemStatus::class => [ItemStatus::class, [
            'OnSale' => 'on_sale',
            'Trading' => 'trading',
            'SoldOut' => 'sold_out',
            'Stop' => 'stop',
            'Cancel' => 'cancel',
            'AdminCancel' => 'admin_cancel',
        ]];

        yield SortBy::class => [SortBy::class, [
            'Score' => 'score',
            'CreatedTime' => 'created_time',
            'Price' => 'price',
            'NumLikes' => 'num_likes',
        ]];

        yield SortOrder::class => [SortOrder::class, [
            'Desc' => 'desc',
            'Asc' => 'asc',
        ]];
    }

    /**
     * @dataProvider provideCases
     * @param class-string<BackedEnum> $enum
     */
    public function testCases(string $enum, array $expected)
    {
        $actual = [];

        foreach ($enum::cases() as $case) {
            $actual[$case->name] = $case->value;
        }

        $this->assertSame($expected, $actual);
    }

    public function testUsedConditions()
    {
        $this->assertSame([
            ItemCondition::LikeNew,
            ItemCondition::NoNoticeableDamage,
            ItemCondition::SlightDamage,
            ItemCondition::Damaged,
            ItemCondition::PoorCondition,
        ], ItemCondition::used());
    }
}
