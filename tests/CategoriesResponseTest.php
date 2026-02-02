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
