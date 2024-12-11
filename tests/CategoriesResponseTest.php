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

use Mercari\CategoriesResponse;

/**
 * @covers \Mercari\DTO\Category
 * @covers \Mercari\CategoriesResponse
 */
class CategoriesResponseTest extends TestCase
{
    public function provideCategories(): iterable
    {
        yield 'master_categories.json' => [__DIR__ . '/data/master_categories.json', 4];

        if (is_file(__DIR__ . '/data/master_categories_all.json')) {
            yield 'master_categories_all.json' => [__DIR__ . '/data/master_categories_all.json'];
        }
    }

    /**
     * @dataProvider provideCategories
     */
    public function testDeserialize(string $file, ?int $count = null)
    {
        $response = $this->deserializeFile($file, CategoriesResponse::class);

        /** @var CategoriesResponse $response */
        if ($count === null) {
            $count = count($response->master_categories);
            $this->assertGreaterThan(0, $count);
        }

        $this->assertCount($count, $response->master_categories);

        $this->assertDeserializedSame($file, $response, false);
    }
}
