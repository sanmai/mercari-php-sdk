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

use Mercari\DTO\ItemCategory;
use Mercari\DTO\NamedDetail;
use Mercari\DTO\NamedItem;
use Tests\Mercari\TestCase;

/**
 * @covers \Mercari\DTO\NamedDetail
 */
class NamedDetailTest extends TestCase
{
    public static function provideNamedDetails(): iterable
    {
        $detail = new NamedDetail();
        $detail->id = 42;
        $detail->name = 'Sample Detail';

        yield 'named detail' => [$detail, 42, 'Sample Detail'];

        $itemCategory = new ItemCategory();
        $itemCategory->id = 1234;
        $itemCategory->name = 'サンプル小カテゴリー';

        yield 'item category' => [$itemCategory, 1234, 'サンプル小カテゴリー'];
    }

    /**
     * @dataProvider provideNamedDetails
     */
    public function testNamedItem(NamedItem $item, int $id, string $name)
    {
        $this->assertSame($id, $item->getId());
        $this->assertSame($name, $item->getName());
    }
}
