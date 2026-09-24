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

declare(strict_types=1);

namespace Tests\Mercari\Enum;

use BackedEnum;
use PHPUnit\Framework\TestCase;

/**
 * Base for the per-enum tests: pins the exact set of cases and their values.
 */
abstract class BackedEnumTestCase extends TestCase
{
    /**
     * @return class-string<BackedEnum>
     */
    abstract public function enumClass(): string;

    /**
     * @return array<string, string|int> Case names mapped to their values.
     */
    abstract public function expectedValues(): array;

    public function testCases(): void
    {
        $actual = [];

        foreach ($this->enumClass()::cases() as $case) {
            $actual[$case->name] = $case->value;
        }

        $this->assertSame($this->expectedValues(), $actual);
    }
}
