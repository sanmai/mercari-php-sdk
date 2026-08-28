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

use Mercari\BrandsResponse;
use Mercari\CategoriesResponse;
use Mercari\DTO\NamedItem;
use Mercari\NamedListResponse;

/**
 * @covers \Mercari\NamedListResponse
 */
class NamedListResponseTest extends TestCase
{
    public static function provideLookups(): iterable
    {
        yield 'categories' => [
            __DIR__ . '/data/master_categories.json',
            CategoriesResponse::class,
            '6144061',
            'ダウンロード',
        ];

        yield 'brands' => [
            __DIR__ . '/data/master_brands.json',
            BrandsResponse::class,
            '510051',
            'サンプルブランド',
        ];
    }

    /**
     * @dataProvider provideLookups
     * @param class-string<NamedListResponse> $type
     */
    public function testGet(string $file, string $type, string $id, string $name): void
    {
        $response = $this->deserializeFile($file, $type);

        $item = $response->get($id);

        $this->assertInstanceOf(NamedItem::class, $item);
        $this->assertSame($name, $item->getName());
        $this->assertSame($item, $response->get($id));

        $this->assertNull($response->get('404'));

        $this->assertDeserializedSame($file, $response, false);
    }

    public function testIndexSurvivesLaterChanges(): void
    {
        /** @var CategoriesResponse $response */
        $response = $this->deserializeFile(__DIR__ . '/data/master_categories.json', CategoriesResponse::class);

        $category = $response->get('989498');

        $response->master_categories = [];

        $this->assertSame($category, $response->get('989498'));
    }
}
