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

use PHPUnit\Framework\TestCase;
use Mercari\DTO\TodoItem;
use ReflectionClass;

use function count;
use function dirname;
use function glob;

class StaticAnalysisTest extends TestCase
{
    public function testInclude()
    {
        $reflectionClass = new ReflectionClass(TodoItem::class);
        $files = glob(dirname($reflectionClass->getFileName()) . '/*.php');

        $this->assertGreaterThan(10, count($files));

        foreach ($files as $file) {
            require_once $file;
        }

        $this->addToAssertionCount(1);
    }
}
