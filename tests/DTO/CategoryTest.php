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

use Mercari\DTO\Category;
use Tests\Mercari\TestCase;

/**
 * @covers \Mercari\DTO\Category
 */
class CategoryTest extends TestCase
{
    public function testNamedItem()
    {
        $category = new Category();
        $category->id = '5678';
        $category->name = 'サンプルカテゴリー';

        $this->assertSame('5678', $category->getId());
        $this->assertSame('サンプルカテゴリー', $category->getName());
    }

    public function testNameCanBeReplaced()
    {
        $category = new class extends Category {
            public function getName(): string
            {
                return 'Sample Category';
            }
        };

        $category->id = '5678';
        $category->name = 'サンプルカテゴリー';

        $this->assertSame('5678', $category->getId());
        $this->assertSame('Sample Category', $category->getName());
    }
}
