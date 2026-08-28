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

use Mercari\DTO\MasterItemBrand;
use Tests\Mercari\TestCase;

/**
 * @covers \Mercari\DTO\MasterItemBrand
 */
class MasterItemBrandTest extends TestCase
{
    public function testNamedItem()
    {
        $brand = new MasterItemBrand();
        $brand->id = '9012';
        $brand->name_ja = 'サンプルブランド';
        $brand->name_en = 'Sample Brand';

        $this->assertSame('9012', $brand->getId());
        $this->assertSame('サンプルブランド', $brand->getName());
    }
}
